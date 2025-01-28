<?php
    session_start();
    require '../backends/db_con.php';
    if (!isset($_SESSION['nid'])) {
        header('Location: ./varification.php');
        exit();
    }
?>

<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link href='https://unpkg.com/boxicons@2.1.4/css/boxicons.min.css' rel='stylesheet'>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;900&display=swap" rel="stylesheet">
        <link rel="stylesheet" href="../assests/css/style.css">
        <title>Voting App</title>
    </head>

<body>
    <div class="container">
        <div class="welcome-text">Choose Your <span>Voting</span> team</div>
        <form action="../backends/submit_vote.php" method="POST">
            <div class="options">
                <div class="left">
                    <div class="card">
                        <img src="../assests/image/jamat.jpg">
                        <div class="option left" onclick="selectOption('Jamaat-e-islami')">
                            <span>Jamaat-e-islami</span>
                            <input type="radio" name="option" value="Jamaat-e-islami" style="display: none;">
                        </div>
                    </div>
                    <div class="card">
                        <img src="../assests/image/BNP.png">
                        <div class="option left" onclick="selectOption('BNP')">
                            <span>BNP</span>
                            <input type="radio" name="option" value="BNP" style="display: none;">
                        </div>
                    </div>
                </div>
                <div class="right">
                    <div class="card">
                        <div class="option right" onclick="selectOption('Awami League')">
                            <span>Awami League</span>
                            <input type="radio" name="option" value="Awami League" style="display: none;">
                        </div>
                        <img src="../assests/image/BAL.png">
                    </div>
                    <div class="card">
                        <div class="option right" onclick="selectOption('Jatio Party')">
                            <span>Jatio Party</span>
                            <input type="radio" name="option" value="Jatio Party" style="display: none;">
                        </div>
                        <img src="../assests/image/JP.png">
                    </div>
                </div>
            </div>
            <button class="btn disabled" disabled id="next-btn">Submit</button>
        </form>
    </div>

    <a href="../pages/varification.php" class="back-btn">
        <i class='bx bx-undo'></i>
    </a>

    <script src="../assests/js/casting.js"></script>
</body>

</html>