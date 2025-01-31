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
            <div class="welcome-text">Upload <span>NID</span> numbers</div>
            <input type="file" name="nid_file" accept=".txt" required>
            <div class="button-group">
                <button type="submit" class="btn btn-secondary">Upload</button>
            </div>
        </div>
    </div>

    <a href="./view_database.php" class="back-btn">
        <i class='bx bx-download bx-rotate-90' ></i>
    </a>

    <div id="notification" ></div>

    <script src="../assests/js/upload_nids.js"></script>
</body>
</html>