<?php

    $localhost = 'localhost';
    $username = 'root';
    $password = '';
    $database = 'voting_app';

    $conn = new mysqli($localhost, $username, $password, $database);


    if ($conn->connect_error) {
        die(json_encode(['success' => false, 'message' => 'Database connection failed']));
    }
