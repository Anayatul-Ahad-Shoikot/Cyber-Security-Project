<?php
    header('Content-Type: application/json');
    require './db_con.php';

    $input = json_decode(file_get_contents('php://input'), true);
    $ip = $input['ip'];

    $sql = "SELECT * FROM ip_address WHERE ip = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $ip);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $ip_id = $row['ip_id'];
    }

    $sql1 = "SELECT * FROM blocked_devices WHERE ip_id = ?";
    $stmt1 = $conn->prepare($sql1);
    $stmt1->bind_param('i', $ip_id);
    $stmt1->execute();
    $result2 = $stmt1->get_result();

    if ($result2->num_rows > 0) {
        echo json_encode(['blocked' => true]);
    } else {
        echo json_encode(['blocked' => false]);
    }

    $conn->close();
?>