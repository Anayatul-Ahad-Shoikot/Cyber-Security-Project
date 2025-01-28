<?php
    header('Content-Type: application/json');
    require './db_con.php';

    $input = json_decode(file_get_contents('php://input'), true);
    $ip = $input['ip'];

    $sql = "SELECT * FROM ip_address";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    $ipFound = false;
    while ($row = $result->fetch_assoc()) {
        if (password_verify($ip, $row['ip'])) {
            $ipFound = true;
            $ip_id = $row['ip_id'];
            break;
        }
    }

    $sql = "SELECT * FROM blocked_devices WHERE ip_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $ip_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        echo json_encode(['blocked' => true]);
    } else {
        echo json_encode(['blocked' => false]);
    }

    $stmt->close();
    $conn->close();
?>