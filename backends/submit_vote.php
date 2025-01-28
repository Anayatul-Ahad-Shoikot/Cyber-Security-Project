<?php
    session_start();
    if (!isset($_SESSION['nid'])) {
        header('Location: ../pages/varification.php');
        exit();
    }

    require './db_con.php';
    
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        $option = filter_input(INPUT_POST, 'option', FILTER_SANITIZE_FULL_SPECIAL_CHARS);
        $nid = $_SESSION['nid'];
        $ip = $_SESSION['ip'];
        if ($option && $nid && $ip) {
            $sql = "INSERT INTO votes (nid, ip, team) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('iss', $nid, $ip, $option);
            if ($stmt->execute()) {
                $audit_sql = "INSERT INTO audit_log (table_name, operation, new_data, changed_by) VALUES (?, ?, ?, ?)";
                $audit_stmt = $conn->prepare($audit_sql);
                $table_name = 'votes';
                $operation = 'INSERT';
                $new_data = "nid=$nid, ip=$ip, team=$option";
                $changed_by = $nid . '-' . $ip;
                $audit_stmt->bind_param('ssss', $table_name, $operation, $new_data, $changed_by);
                $audit_stmt->execute();
                $audit_stmt->close();

                header('Location: ../pages/thankyou.php');
            } else {
                echo "Error submitting vote.";
            }
            $stmt->close();
        } else {
            echo "Invalid input.";
        }
    }
    $conn->close();
?>