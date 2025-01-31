<?php
    header('Content-Type: application/json');
    require_once './db_connection.php';

    try {
        
        $data = json_decode(file_get_contents('php://input'), true);
        $ip = $data['ip'] ?? '';

        $stmt = $con->prepare("
            SELECT COUNT(*) AS blocked 
            FROM blocked_ips AS bi
            JOIN ip_addresses AS ia ON bi.ip_id = ia.ip_id
            WHERE ia.ip_address = ?
        ");

        $stmt->execute([$ip]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        echo json_encode(['blocked' => $result['blocked'] > 0]);

    } catch (PDOException $e) {
        echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
    } catch (Exception $e) {
        echo json_encode(['error' => 'General error: ' . $e->getMessage()]);
    }
?>