<?php
    require 'db.php';
    require 'auth.php';

    // Default values
    $contactsPerPage = 10;
    $pageNum = 1;

    // Get JSON from client
    $inData = getRequestInfo();
    if(!$inData){
        echo 'Error: Invalid JSON data received';
        exit();
    }

    $user = verifyToken();

    // Make sure input is valid. Use default values if optional values not provided
    if(isset($inData['page']) && is_numeric($inData['page'])){
        if($inData['page'] < 1){
            echo 'Error: Invalid page number';
            exit();
        }
        $pageNum = $inData['page'];
        $pageNum = intval($pageNum);
    }

    if(isset($inData['pageSize']) && is_numeric($inData['pageSize'])){
        if($inData['pageSize'] < 1){
            echo 'Error: Invalid page size';
            exit();
        }
        $contactsPerPage = $inData['pageSize'];
        $contactsPerPage = intval($contactsPerPage);
    }

    if(!isset($inData['search']) || $inData['search'] == '' ){
        echo 'Error: A search term is required';
        exit();
    }

    // Calculate offset
    $offset = ($pageNum - 1) * $contactsPerPage;

    // Prepare search term
    $search = '%' . $inData['search'] . '%';
    
    // Get contacts from database
    $stmt = $conn->prepare("SELECT * FROM contacts WHERE UserID = ? AND (FirstName LIKE ? OR LastName LIKE ? OR Phone LIKE ? OR Email LIKE ?) LIMIT $contactsPerPage OFFSET $offset");
    $stmt->bind_param("issss", $user['id'], $search, $search, $search, $search);
    $stmt->execute();
    $result = $stmt->get_result();

    // Return contacts
    $contacts = [];
    while($row = $result->fetch_assoc()){
        $contacts[] = $row;
    }
    sendResultInfoAsJson($contacts);

    $stmt->close();
?>