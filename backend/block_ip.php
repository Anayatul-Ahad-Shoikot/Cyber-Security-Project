<?php
require_once './db_connection.php';

header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

try {
    $stmt = $con->prepare("SELECT ip_id FROM ip_addresses WHERE ip_address = ?");
    $stmt->execute([$data['ip']]);
    $ipId = $stmt->fetchColumn();

    // Block IP
    $stmt = $con->prepare("INSERT INTO blocked_ips (ip_id) VALUES (?)");
    $stmt->execute([$ipId]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>