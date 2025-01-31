<?php
    session_start();
    require_once '../backend/db_connection.php';
    header('Content-Type: application/json');
    try {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            http_response_code(405);
            echo json_encode(['error' => 'Method_not_allowed']);
            exit();
        }
        $input = json_decode(file_get_contents('php://input'), true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(['error' => 'Invalid_JSON_input']);
            exit();
        }
        $nid = $input['nid'] ?? '';
        $ip = $input['ip'];
        if (!filter_var($nid, FILTER_VALIDATE_INT) || !preg_match('/^\d{8,10}$/', $nid)) {
            http_response_code(400);
            echo json_encode(['niderror' => true]);
            exit();
        }
        $stmt = $con->prepare("SELECT user_id FROM users WHERE national_id = ?");
        $stmt->execute([$nid]);
        $user = $stmt->fetch();
        if ($user) {
            $stmt = $con->prepare("SELECT 1 FROM votes WHERE user_id = ?");
            $stmt->execute([$user['user_id']]);
            if ($stmt->fetch()) {
                echo json_encode(['casted' => true]);
            } else {
                session_regenerate_id(true);
                $_SESSION['authenticated'] = true;
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['ip_address'] = $ip;
                $_SESSION['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
                $_SESSION['last_activity'] = time();
                echo json_encode(['success' => true]);
            }
        } else {
            echo json_encode(['niderror' => true]);
        }
    } catch (PDOException $e) {
        http_response_code(500);
        error_log("Database error: " . $e->getMessage());
        echo json_encode(['error' => 'Database operation failed']);
    } catch (Exception $e) {
        http_response_code(500);
        error_log("System error: " . $e->getMessage());
        echo json_encode(['error' => 'System error occurred']);
    }
?>