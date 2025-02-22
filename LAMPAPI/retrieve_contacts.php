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
    if($inData['page'] < 1){
        echo 'Error: Invalid page number';
        exit();
    }

    // Get contacts from database
    $stmt = $conn->prepare("SELECT * FROM contacts WHERE UserID = ? LIMIT 10 OFFSET ?");
    $stmt->bind_param("ii", $user['id'], ($inData['page'] - 1) * 10);
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