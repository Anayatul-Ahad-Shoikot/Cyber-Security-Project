<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../assests/css/style.css">
    <title>Voting App</title>
</head>
<body>
    <div class="container">
        <div class="welcome-text">Upload <span>NID</span> numbers</div>
        <form action="../backends/user_nid_upload.php" method="post" enctype="multipart/form-data">
            <input type="file" name="nid_file" accept=".txt" required>
            <button type="submit" class="btn btn-secondary">Upload</button>
        </form>
    </div>
</body>
</html>