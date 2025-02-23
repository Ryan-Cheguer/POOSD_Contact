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

    function getRequestInfo(){
        return json_decode(file_get_contents('php://input'), true);
    }

    function sendResultInfoAsJson($obj){
        header('Content-type: application/json');
        echo json_encode($obj);
    }

    function validatePageNumber($inData){
        $pageNum = 1;
        if(isset($inData['page']) && is_numeric($inData['page'])){
            if($inData['page'] < 1){
                sendError('Error: Invalid page number');
                exit();
            }
            $pageNum = $inData['page'];
            $pageNum = intval($pageNum);
        }
        return $pageNum;
    }

    function validatePageSize($inData){
        $contactsPerPage = 10;
        if(isset($inData['pageSize']) && is_numeric($inData['pageSize'])){
            if($inData['pageSize'] < 1){
                sendError('Error: Invalid page size');
                exit();
            }
            $contactsPerPage = $inData['pageSize'];
            $contactsPerPage = intval($contactsPerPage);
        }
        return $contactsPerPage;
    }

    function sendError($msg, $code = 400){
        http_response_code($code);
        echo json_encode(["error" => $msg]);
        http_response_code(400);
        exit();
    }
?>