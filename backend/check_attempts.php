<?php
require_once './db_connection.php';

header('Content-Type: application/json');

try {
    $ip = $_SERVER['REMOTE_ADDR'];
    $response = [
        'blocked' => false,
        'attempts' => 0,
        'next_attempt' => 0
    ];

    // Check if IP is blocked
    $stmt = $con->prepare("SELECT COUNT(*) FROM blocked_ips bi JOIN ip_addresses ia ON bi.ip_id = ia.ip_id WHERE ia.ip_address = ?");
    $stmt->execute([$ip]);
    if ($stmt->fetchColumn() > 0) {
        $response['blocked'] = true;
        echo json_encode($response);
        exit;
    }

    // Get attempt count
    $stmt = $con->prepare("SELECT attempts, attempted_at FROM invalid_attempts ia JOIN ip_addresses ip ON ia.ip_id = ip.ip_id WHERE ip.ip_address = ? ORDER BY attempted_at DESC LIMIT 1");
    $stmt->execute([$ip]);
    $attempts = $stmt->fetch();

    if ($attempts) {
        $lastAttempt = strtotime($attempts['attempted_at']);
        $nextAttempt = $lastAttempt + (50 * (3 - $attempts['attempts']));
        $response['attempts'] = $attempts['attempts'];
        $response['next_attempt'] = $nextAttempt * 1000; // Convert to milliseconds
    }

    echo json_encode($response);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>