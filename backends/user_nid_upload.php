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

        $raw_ip = $data['ip'];

        $stmt = $con->prepare("SELECT ip_id, ip_address FROM ip_addresses");
        $stmt->execute();
        $result = $stmt->fetchAll();
        $ip_id = null;
        foreach($result as $fetched_ip) {
            if (decrypt($fetched_ip['ip_address'], ENCRYPTION_KEY) == $raw_ip) {
                $ip_id = $fetched_ip['ip_id'];
                break;
            }
        }
        if ($ip_id === null) {
            $stmt = $con->prepare('INSERT INTO ip_addresses (ip_address) VALUES (?)');
            $stmt->execute([encrypt($raw_ip, ENCRYPTION_KEY)]);
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