<?php

session_start();

require './db_con.php';

header('Content-Type: application/json');

try {
    $input = json_decode(file_get_contents('php://input'), true);
    $nid = $input['nid'];

    if (!filter_var($nid, FILTER_VALIDATE_INT) || !preg_match('/^\d{8,10}$/', $nid)) {
        echo json_encode(['success' => false, 'message' => 'Invalid NID format']);
        exit();
    }

    // Fetch all NIDs from the database
    $sql = "SELECT nid, nid_id FROM users";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    $nidFound1 = false;
    while ($row = $result->fetch_assoc()) {
        if (password_verify($nid, $row['nid'])) {
            $nidFound1 = true;
            $nid_id = $row['nid_id'];
            break;
        }
    }

    if ($nidFound1) {
        $sql = "SELECT v.* 
                FROM votes v
                JOIN users u ON v.nid_id = u.id
                WHERE u.id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $nid_id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows > 0) {
            echo json_encode(['casted' => true]);
        } else {
            echo json_encode(['casted' => false]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'NID not found']);
    }

    $stmt->close();
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'An error occurred', 'error' => $e->getMessage()]);
}
?>