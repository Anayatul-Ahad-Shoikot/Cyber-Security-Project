<?php

session_start();
require './db_con.php';

header('Content-Type: application/json');

// Decode JSON input
$input = json_decode(file_get_contents('php://input'), true);

// Validate NID input
if (!isset($input['nid']) || !filter_var($input['nid'], FILTER_VALIDATE_INT) || !preg_match('/^\d{8,10}$/', $input['nid'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid NID format']);
    exit();
}

$nid = $input['nid'];

try {
    // Check if the user exists in the users table
    $sql = "SELECT id FROM users WHERE nid = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $nid);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        // If user not found in the users table
        echo json_encode(['record' => false]);
        exit();
    }

    $user = $result->fetch_assoc();
    $userId = $user['id'];

    // Check if the user ID exists in the votes table
    $sql = "SELECT 1 FROM votes WHERE nid_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $userId);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        // If user ID is found in the votes table, voting already done
        echo json_encode(['casted' => true]);
    } else {
        // If user ID is not found in the votes table
        echo json_encode(['success' => true, 'casted' => false]);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred: ' . $e->getMessage()]);
} finally {
    $stmt->close();
    $conn->close();
}
