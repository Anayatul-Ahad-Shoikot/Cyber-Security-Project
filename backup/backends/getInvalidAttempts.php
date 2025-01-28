<?php
    session_start();

    require './db_con.php';

    $ip = $_GET['ip'];

    $sql = "SELECT attempts FROM invalid_attempts WHERE ip = '$ip'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $invalidAttempts = $row['attempts'];
    } else {
        $invalidAttempts = 0;
    }

    echo $invalidAttempts;
    $conn->close();
?>