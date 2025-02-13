<?php
    $host = "localhost";
    $dbname = "contactsDB";
    $username = "db_user";
    $password = "DHJLRR";

    $conn = new mysqli($host, $username, $password, $dbname);
    
    if($conn->connect_error){
        die("Connection failed: " . $conn->connect_error);
    }

    $conn->set_charset("utf8mb4");

    echo "Connected to database";

    function getRequestInfo(){
        return json_decode(file_get_contents('php://input'), true);
    }

    function sendResultInfoAsJson($obj){
        header('Content-type: application/json');
        echo json_encode($obj);
    }
?>