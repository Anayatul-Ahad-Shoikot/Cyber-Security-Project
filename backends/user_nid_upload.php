<?php

    require '../backend/db_connection.php';
    require '../backend/crypto.php';

    header('Content-Type: application/json');

    try {

        $data = json_decode(file_get_contents('php://input'), true);

        if (empty($data['ip'])) {
            throw new Exception('Invalid input data');
        }

        $con->beginTransaction();

        $encrypted_ip = encrypt($data['ip'], ENCRYPTION_KEY);

        $stmt = $con->prepare("SELECT ip_id FROM ip_addresses WHERE ip_address = ?");
        $stmt->execute([$encrypted_ip]);
        $result = $stmt->fetch();
        if ($result) {
            $ip_id = $result['ip_id'];
        } else {
            $stmt = $con->prepare('INSERT INTO ip_addresses (ip_address) VALUES (?)');
            $stmt->execute([$encrypted_ip]);
            $ip_id = $con->lastInsertId();
        }

        if (!empty($data['nids'])) {
            $content = $data['nids'];
            $numbers = array_map('trim', explode(',', $content));
            foreach ($numbers as $nid) {
                $encrypted_nid = encrypt(trim(json_encode($nid)), ENCRYPTION_KEY);
                $stmt = $con->prepare("INSERT INTO users (national_id, ip_id) VALUES (?, ?)");
                $stmt->execute([$encrypted_nid, $ip_id]);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'nid not uploaded']);
            exit();
        }

        $con->commit();
        echo json_encode(['success' => true]);

    } catch (Exception $e) {
        if (isset($con) && $con->inTransaction()) {
            $con->rollBack();
        }
        http_response_code(500);
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
    }

?>