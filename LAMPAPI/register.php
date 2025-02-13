<?php
    require 'db.php'
    $inData = getRequestInfo();

    $stmt = $conn->prepare("INSERT INTO users (FirstName, LastName, Username, Pass) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $firstName, $lastName, $username, $password)\
    $stmt->execute();

    if($stmt->affected_rows > 0){
        echo "Registered successfully";
    } else {
        echo "Error registering user."
    }

    $stmt->close();
?>