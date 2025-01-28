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
            <div class="welcome-text">Verify <span>yourself</span></div>
            <input type="number" id="nid-input" placeholder="Your NID number" oninput="validateInput()">
            <div class="button-group">
                <button id="next-btn" class="btn disabled" onclick="verifyNID()" disabled>Next</button>
            </div>
        </div>
    </div>
    <a href="../index.php" class="back-btn">
        <i class='bx bx-undo'></i>
    </a>
    <div id="loading-screen" style="display: none;">Loading...</div>
    <div id="notification" >Invalid NID. Please try Again after <h2 id="timer">50</h2> seconds.</div>
    <script src="../assests/js/varification.js"></script>
</body>

</html>