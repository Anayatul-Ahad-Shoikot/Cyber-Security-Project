<?php
require_once './db_connection.php';

header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

try {
    $stmt = $con->prepare("INSERT INTO ip_addresses (ip_address) VALUES (?)");
    $stmt->execute([$data['ip']]);
    $ipId = $con->lastInsertId();
    $stmt = $con->prepare("INSERT INTO invalid_attempts (ip_id, attempts) VALUES (?, 1)");
    $stmt->execute([$ipId]);

    echo json_encode(['success' => true]);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>