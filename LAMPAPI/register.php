<?php
    require 'db.php';
    $inData = getRequestInfo();
    if(!$inData){
        sendError('Error: No input provided');
    }

    // Check if input is valid
    if($inData["firstName"] == '' || $inData["lastName"] == '' || $inData["username"] == '' || $inData["password"] == ''){
        sendError('Error: All fields are required');
        exit();
    }

    // Check if username is already taken
    $stmt = $conn->prepare("SELECT * FROM users WHERE Username = ?");
    $stmt->bind_param("s", $inData["username"]);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->fetch_assoc()){
        sendError('Error: Username already taken');
        exit();
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO users (FirstName, LastName, Username, Pass) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $inData["firstName"], $inData["lastName"], $inData["username"], $inData["password"]);
    $stmt->execute();

    if($stmt->affected_rows > 0){
        sendResultInfoAsJson(["success" => "Registered successfully"]);
    } else {
        sendError('Error: Failed to register user');
    }

    $stmt->close();
?>