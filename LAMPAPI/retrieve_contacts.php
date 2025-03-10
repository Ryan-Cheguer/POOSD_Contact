<?php
    require 'db.php';
    require 'auth.php';

    // Get JSON from client
    $inData = getRequestInfo();

    $user = verifyToken();

    // Make sure input is valid. Use default values if optional values not provided
    $pageNum = validatePageNumber($inData);
    $contactsPerPage = validatePageSize($inData);
    
    $limit = $contactsPerPage + 1;

    // Calculate offset
    $offset = ($pageNum - 1) * $contactsPerPage;

    // Get total number of contacts
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM contacts WHERE UserID = ?");
    $stmt->bind_param("i", $user['id']);
    if(!$stmt->execute()){
        sendError('Failed to retrieve contact count', 500);
        exit();
    }
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $totalContacts = $row['total'];
    $stmt->close();

    // Get contacts from database
    $stmt = $conn->prepare("SELECT * FROM contacts WHERE UserID = ? LIMIT $limit OFFSET $offset");
    $stmt->bind_param("i", $user['id']);
    if(!$stmt->execute()){
        sendError('Failed to retrieve contacts', 500);
        exit();
    }
    $result = $stmt->get_result();

    // Return contacts
    $contacts = [];
    $hasMore = false;

    while($row = $result->fetch_assoc()){
        if(count($contacts) < $contactsPerPage){
            $contacts[] = $row;
        } else {
            $hasMore = true;
            break;
        }
    }

    sendResultInfoAsJson([
        "contacts" => $contacts,
        "hasMore" => $hasMore,
        "currentPage" => $pageNum,
        "totalContacts" => $totalContacts
    ]);

    $stmt->close();
?>