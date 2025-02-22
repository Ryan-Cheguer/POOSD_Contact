<?php
require 'db.php';
require 'auth.php';

header("Content-Type: application/json");

// Verify JWT Token & Get User ID
$user = verifyToken();  // Extracts user ID from the token

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);
$contactID = $data["ID"] ?? null;
$firstName = $data["FirstName"] ?? null;
$lastName = $data["LastName"] ?? null;
$contactEmail = $data["Email"] ?? null;
$contactPhone = $data["Phone"] ?? null;

// Validate input
if (!$contactID || !$firstName || !$lastName || !$contactEmail || !$contactPhone) {
    echo json_encode(["error" => "All fields (ID, FirstName, LastName, Email, Phone) are required"]);
    http_response_code(400);
    exit;
}

// Ensure the contact belongs to the logged-in user
$checkStmt = $conn->prepare("SELECT * FROM contacts WHERE ID = ? AND UserID = ?");
$checkStmt->bind_param("ii", $contactID, $user['id']);
$checkStmt->execute();
$result = $checkStmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(["error" => "Contact not found or unauthorized"]);
    http_response_code(403);
    exit;
}

// Update contact information
$updateStmt = $conn->prepare("UPDATE contacts SET FirstName = ?, LastName = ?, Email = ?, Phone = ? WHERE ID = ?");
$updateStmt->bind_param("ssssi", $firstName, $lastName, $contactEmail, $contactPhone, $contactID);
$updateStmt->execute();

if ($updateStmt->affected_rows > 0) {
    echo json_encode(["success" => "Contact updated successfully"]);
    http_response_code(200);
} else {
    echo json_encode(["error" => "No changes made"]);
    http_response_code(500);
}

$updateStmt->close();
$checkStmt->close();
?>
