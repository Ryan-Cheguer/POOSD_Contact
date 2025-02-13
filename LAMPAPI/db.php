<?php
    $host = "localhost";
    $dbname = "contactsDB";
    $username = "db_user";
    $password = "DHJLRR";

    $conn = new mysqli($host, $dbname, $username, $password);
    
    if($conn->connect_error){
        die("Connection failed: " . $conn->connect_error);
    }

    $conn->set_charset("utf8mb4");

    echo "Connected to database";
?>