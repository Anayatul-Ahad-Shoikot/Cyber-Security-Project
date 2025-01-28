<?php

    session_start();

    require './db_con.php';
    require './crypto.php';

    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);
    $ip = $input['ip'];
    $key = 'RX6600xt';

    $encrypted_ip = encrypt(trim($ip), $key);
    $sql = "INSERT INTO ip_address (ip) VALUES (?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $encrypted_ip);
    $stmt->execute();

    $ip_id = $conn->insert_id;

    $sql = "INSERT INTO blocked_devices (ip_id) VALUES (?)";
    $stmt2 = $conn->prepare($sql);
    $stmt2->bind_param('i', $ip_id);
    $stmt2->execute();

    if ($stmt2->affected_rows > 0) {
        $audit_sql = "INSERT INTO audit_log (table_name, operation, new_data, changed_by) VALUES (?, ?, ?, ?)";
        $audit_stmt = $conn->prepare($audit_sql);
        $table_name = 'blocked_devices';
        $operation = 'INSERT';
        $new_data = "ip=$encrypted_ip";
        $changed_by = $encrypted_ip;
        $audit_stmt->bind_param('ssss', $table_name, $operation, $new_data, $changed_by);
        $audit_stmt->execute();
        $audit_stmt->close();
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false]);
    }
    
    $stmt->close();
    $stmt2->close();
    $conn->close();
    
?>