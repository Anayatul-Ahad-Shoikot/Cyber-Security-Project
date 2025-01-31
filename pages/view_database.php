<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;900&display=swap" rel="stylesheet">
    <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="../assests/css/style.css">
    <title>Voting App</title>
</head>

<body>
    <div class="container">
        <div class="content-box">
            <div class="welcome-text"><span>NID</span> list</div>
            <?php
            require '../backend/db_connection.php';
            require '../backend/crypto.php';
            $stmt = $con->query("SELECT national_id FROM users");
            $nids = $stmt->fetchAll();
            if ($nids) {

                echo '<ul>';
                foreach ($nids as $nid) {
                    echo '<li>' . decrypt($nid['national_id'], ENCRYPTION_KEY) . '</li>';
                }
                echo '</ul>';
            } else {
                echo '<div>No NID uploaded yet</div>';
            }
            ?>
            <div class="welcome-text"><span>IP</span> list</div>
            <?php
            $stmt = $con->query("SELECT ip_address FROM ip_addresses");
            $ips = $stmt->fetchAll();
            if ($ips) {
                echo '<ul>';
                foreach ($ips as $ip) {
                    echo '<li>' . decrypt($ip['ip_address'], ENCRYPTION_KEY) . '</li>';
                }
                echo '</ul>';
            } else {
                echo '<div>No IP recorded yet</div>';
            }
            ?>
        </div>
    </div>

    <a href="./upload_user_data.php" class="back-btn">
        <i class='bx bx-download bx-rotate-90' ></i>
    </a>

    <script src="../assests/js/upload_nids.js"></script>

    <div id="notification"></div>
</body>

</html>