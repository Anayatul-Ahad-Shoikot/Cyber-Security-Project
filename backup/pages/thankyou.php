<?php
    session_start();
        if (!isset($_SESSION['nid'])) {
            header('Location: varification.html');
            exit();
        }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assests/css/style.css">
    <title>Voting App</title>
</head>
<body>
    <div id="loading-screen">Loading...</div>
    <div class="container">
    <div class="welcome-text">Thank you for your <span>Vote</span> !</div>
        <button class="btn btn-secondary" onclick="exit()">Exit</button>
    </div>
    <script src="../assests/js/thankyou.js"></script>
</body>
</html>