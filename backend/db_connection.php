<?php
    $localhost = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'voting_system';
    $charset = 'utf8mb4';

    try {
        $con = new PDO(
            "mysql:host=$localhost;dbname=$database;charset=$charset",
            $username,
            $password,
            [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_EMULATE_PREPARES => false,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
            ]
        );
    } catch (PDOException $e) {
        header('Content-Type: application/json');
        die(json_encode([
            'success' => false,
            'message' => 'Database connection failed',
            'error' => $e->getMessage()
        ]));
    }
?>