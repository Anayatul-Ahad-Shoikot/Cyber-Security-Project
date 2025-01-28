<?php
require './db_con.php';

$data = json_decode(file_get_contents('php://input'), true);
$ip = $data['ip'];
$invalidAttempts = $data['invalidAttempts'];

$sql = "SELECT * FROM ip_address WHERE ip = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $ip);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $ip_id = $row['ip_id'];
} else {
    $sql = "INSERT INTO ip_address (ip) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $ip);
    $stmt->execute();
    $ip_id = $conn->insert_id;
}

// Check if the ip_id already exists in the invalid_attempts table
$sql = "SELECT attempts FROM invalid_attempts WHERE ip_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $ip_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // ip_id found, update the attempts count
    $row = $result->fetch_assoc();
    $new_attempts = $invalidAttempts;
    $sql = "UPDATE invalid_attempts SET attempts = ? WHERE ip_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $new_attempts, $ip_id);
} else {
    // ip_id not found, insert a new record
    $sql = "INSERT INTO invalid_attempts (ip_id, attempts) VALUES (?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ii', $ip_id, $invalidAttempts);
}
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Record updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error updating record: ' . $stmt->error]);
}

$conn->close();
?>