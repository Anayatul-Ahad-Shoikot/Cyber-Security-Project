<?php

    require './db_con.php';

    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['nid_file'])) {
        $file = $_FILES['nid_file']['tmp_name'];
        $content = file_get_contents($file);
        $numbers = explode(',', $content);
    
        foreach ($numbers as $number) {
            $encrypted_nid = trim($number);
            $stmt = $conn->prepare("INSERT INTO users (nid) VALUES (?)");
            $stmt->bind_param("s", $encrypted_nid);
            $stmt->execute();
        }
    } else {
        echo "Invalid request.";
    }

?>