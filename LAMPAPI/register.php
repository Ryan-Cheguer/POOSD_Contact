<?php
    require 'db.php';
    $inData = getRequestInfo();
    if(!$inData){
        echo "Error: Invalid JSON data received.";
    }

    // Check if input is valid
    if($inData["firstName"] == '' || $inData["lastName"] == '' || $inData["username"] == '' || $inData["password"] == ''){
        echo "Error: Invalid input.";
        exit();
    }

    // Check if username is already taken
    $stmt = $conn->prepare("SELECT * FROM users WHERE Username = ?");
    $stmt->bind_param("s", $inData["username"]);
    $stmt->execute();
    $result = $stmt->get_result();
    if($result->fetch_assoc()){
        echo "Error: Username already taken.";
        exit();
    }

    // Insert into database
    $stmt = $conn->prepare("INSERT INTO users (FirstName, LastName, Username, Pass) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $inData["firstName"], $inData["lastName"], $inData["username"], $inData["password"]);
    $stmt->execute();

    if($stmt->affected_rows > 0){
        echo "Registered successfully";
    } else {
        echo "Error registering user.";
    }

    $stmt->close();
?>