<?php
    require 'db.php';
    require 'auth.php';

    // Get JSON from client
    $inData = getRequestInfo();
    if(!$inData){
        sendError('No input provided');
        exit();
    }

    $user = verifyToken();

    // Make sure input is valid. Use default values if optional values not provided
    $pageNum = validatePageNumber($inData);
    $contactsPerPage = validatePageSize($inData);
    
    $limit = $contactsPerPage + 1;

    if(!isset($inData['search']) || $inData['search'] == '' ){
        sendError('Search term is required');
        exit();
    }

    // Calculate offset
    $offset = ($pageNum - 1) * $contactsPerPage;

    // Prepare search term
    $search = '%' . $inData['search'] . '%';

    // Get total number of contacts that match search criteria
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM contacts WHERE UserID = ? AND (FirstName LIKE ? OR LastName LIKE ? OR Phone LIKE ? OR Email LIKE ?)");
    $stmt->bind_param("issss", $user['id'], $search, $search, $search, $search);
    if(!$stmt->execute()){
        sendError('Failed to retrieve contact count', 500);
        exit();
    }
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $totalContacts = $row['total'];
    $stmt->close();
    
    // Get contacts from database
    $stmt = $conn->prepare("SELECT * FROM contacts WHERE UserID = ? AND (FirstName LIKE ? OR LastName LIKE ? OR Phone LIKE ? OR Email LIKE ?) LIMIT $limit OFFSET $offset");
    $stmt->bind_param("issss", $user['id'], $search, $search, $search, $search);
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