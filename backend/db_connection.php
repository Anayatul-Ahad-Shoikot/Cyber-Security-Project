<?php
    $host = 'localhost';
    $port = "5432"; 
    $user = 'postgres';
    $pass = 'RX6600xt';
    $db = 'cyber_security_project';
try {
    $dsn = "pgsql:host=$host;port=$port;dbname=$db;";
    $pdo = new PDO($dsn, $user, $pass, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false
    ]);
    echo "PostgreSQL connection successful.";
} catch (PDOException $e) {
    die("PostgreSQL connection failed: " . $e->getMessage());
}
?>