<?php

    session_start();

    require './db_con.php';

    header('Content-Type: application/json');
    $input = json_decode(file_get_contents('php://input'), true);
    $ip = $input['ip'];

    $sql = "SELECT ip_id FROM ip_address WHERE ip = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $ip);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $ip_id = $row['ip_id'];
    }

    $sql = "INSERT INTO blocked_devices (ip_id) VALUES (?)";
    $stmt2 = $conn->prepare($sql);
    $stmt2->bind_param('i', $ip_id);
    $stmt2->execute();

    if ($stmt2->affected_rows > 0) {
        $audit_sql = "INSERT INTO audit_log (table_name, operation, new_data, changed_by) VALUES (?, ?, ?, ?)";
        $audit_stmt = $conn->prepare($audit_sql);
        $table_name = 'blocked_devices';
        $operation = 'INSERT';
        $new_data = "ip=$ip";
        $changed_by = $ip;
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