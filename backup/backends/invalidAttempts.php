<?php
require './db_con.php';
require './crypto.php';

$data = json_decode(file_get_contents('php://input'), true);
$ip = $data['ip'];
$invalidAttempts = $data['invalidAttempts'];
$key = 'RX6600xt';

$encrypted_ip = encrypt(trim($ip), $key);


$sql = "SELECT id FROM ip_address WHERE ip = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $encrypted_ip);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    // IP address found, get the id
    $row = $result->fetch_assoc();
    $ip_id = $row['id'];
} else {
    // IP address not found, insert it
    $sql = "INSERT INTO ip_address (ip) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $encrypted_ip);
    $stmt->execute();
    $ip_id = $conn->insert_id;
}

// Insert or update the invalid attempts for the IP address
$sql = "INSERT INTO invalid_attempts (ip_id, attempts) VALUES (?, ?) ON DUPLICATE KEY UPDATE attempts = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('iii', $ip_id, $invalidAttempts, $invalidAttempts);
if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Record updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Error updating record: ' . $stmt->error]);
}

$conn->close();
?>