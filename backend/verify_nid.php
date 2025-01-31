<?php
require_once '../config/db_connection.php';

header('Content-Type: application/json');
$data = json_decode(file_get_contents('php://input'), true);

try {
    $ip = $_SERVER['REMOTE_ADDR'];
    $response = ['status' => 'invalid'];

    // Check users table
    $stmt = $con->prepare("SELECT user_id FROM users WHERE national_id = ?");
    $stmt->execute([$data['nid']]);
    $user = $stmt->fetch();

    if ($user) {
        // Check votes table
        $stmt = $conn->prepare("SELECT vote_id FROM votes WHERE user_id = ?");
        $stmt->execute([$user['user_id']]);
        $response['status'] = $stmt->fetch() ? 'voted' : 'valid';
    } else {
        // Log invalid attempt
        $stmt = $con->prepare("INSERT INTO ip_addresses (ip_address) VALUES (?) ON DUPLICATE KEY UPDATE ip_id=LAST_INSERT_ID(ip_id)");
        $stmt->execute([$ip]);
        $ipId = $con->lastInsertId();

        $stmt = $con->prepare("
            INSERT INTO invalid_attempts (ip_id, attempts, attempted_at)
            VALUES (?, 1, NOW())
            ON DUPLICATE KEY UPDATE 
            attempts = attempts + 1,
            attempted_at = NOW()
        ");
        $stmt->execute([$ipId]);

        // Check attempt count
        $stmt = $con->prepare("SELECT attempts FROM invalid_attempts WHERE ip_id = ?");
        $stmt->execute([$ipId]);
        $attempts = $stmt->fetchColumn();

        if ($attempts >= 3) {
            $conn->prepare("INSERT INTO blocked_ips (ip_id) VALUES (?)")->execute([$ipId]);
        }

        $response['attempts_remaining'] = 3 - $attempts;
        $response['next_attempt'] = time() + (50 * (3 - $attempts));
    }

    echo json_encode($response);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>