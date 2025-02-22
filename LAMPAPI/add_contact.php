<?php
require 'db.php';
require 'auth.php';  // JWT Authentication

header("Content-Type: application/json");

// Verify JWT Token & Get User ID
$user = verifyToken();  // Extracts user ID from the token

// Get JSON input
$data = json_decode(file_get_contents("php://input"), true);
$firstName = $data["firstName"] ?? '';
$lastName = $data["lastName"] ?? '';
$contactPhone = $data["phone"] ?? '';
$contactEmail = $data["email"] ?? '';

// Validate input
if (!$firstName || !$lastName || !$contactPhone || !$contactEmail) {
    echo json_encode(["error" => "All fields (firstName, lastName, phone, email) are required"]);
    http_response_code(400);
    exit;
}

// Insert contact into database
$stmt = $conn->prepare("INSERT INTO contacts (FirstName, LastName, Phone, Email, UserID) VALUES (?, ?, ?, ?, ?)");
$stmt->bind_param("ssssi", $firstName, $lastName, $contactPhone, $contactEmail, $user['id']);
$stmt->execute();

// Check if the contact was added successfully
if ($stmt->affected_rows > 0) {
    echo json_encode(["success" => "Contact added successfully"]);
    http_response_code(201);
} else {
    echo json_encode(["error" => "Failed to add contact"]);
    http_response_code(500);
}

$stmt->close();
?>
