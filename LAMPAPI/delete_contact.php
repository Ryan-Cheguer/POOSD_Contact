<?php
    require 'db.php';
    require 'auth.php';

    $inData = getRequestInfo();
    if(!$inData || !isset($inData["ID"])){
        sendError("A contact ID is required");
        exit();
    }

    $user = verifyToken();

    // Ensure the contact belongs to the logged-in user
    $checkStmt = $conn->prepare("SELECT * FROM contacts WHERE ID = ? AND UserID = ?");
    $checkStmt->bind_param("ii", $inData["ID"], $user['id']);
    $checkStmt->execute();
    $result = $checkStmt->get_result();

    if ($result->num_rows === 0) {
        sendError("Contact not found or unauthorized", 403);
        exit();
    }

    // Delete contact
    $deleteStmt = $conn->prepare("DELETE FROM contacts WHERE ID = ?");
    $deleteStmt->bind_param("i", $inData["ID"]);
    $deleteStmt->execute();

    if ($deleteStmt->affected_rows > 0) {
        http_response_code(200);
        sendResultInfoAsJson(["success" => "Contact deleted successfully"]);
        exit();
    } else {
        sendError("Failed to delete contact: " . $conn->error, 500);
    }

    $deleteStmt->close();
    $checkStmt->close();
?>
