<?php
    require 'db.php';
    require 'auth.php';
    use Firebase\JWT\JWT;

    $user = verifyToken();

    // Get JSON from client
    $inData = getRequestInfo();
    if(!$inData){
        echo 'Error: Invalid JSON data received'
    }

    // Make sure input is valid
    if($inData['search'] == '' ){
        echo 'Error: Invalid search term';
        exit();
    } else if($inData['page'] < 1){
        echo 'Error: Invalid page number';
        exit();
    }

    // Get contacts from database
    $stmt = $conn->prepare("SELECT * FROM contacts WHERE UserID = ? AND (FirstName LIKE ? OR LastName LIKE ? OR Phone LIKE ? OR Email LIKE ?) LIMIT 10 OFFSET ?");
    $search = '%' . $inData['search'] . '%';
    $stmt->bind_param("issss", $user['id'], $search, $search, $search, $search, ($inData['page'] - 1) * 10);
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