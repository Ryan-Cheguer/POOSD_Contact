<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require 'db.php';  // Includes the database connection from db.php
require 'vendor/autoload.php';
use Firebase\JWT\JWT;

header("Content-Type: application/json");

// Get the input JSON request
$data = json_decode(file_get_contents("php://input"), true);
$username = $data['Username'] ?? '';
$password = $data['Password'] ?? '';

// Validate input
if (!$username || !$password) {
    echo json_encode(["error" => "Username and Password are required"]);
    http_response_code(400);
    exit;
}

// Prepare SQL statement to prevent SQL injection
$stmt = $conn->prepare("SELECT * FROM users WHERE Username = ?");
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();
$user = $result->fetch_assoc();

// Check if user exists and password is correct
if (!$user || !password_verify($password, $user['Pass'])) {
    echo json_encode(["error" => "Invalid username or password"]);
    http_response_code(401);
    exit;
}

// Generate JWT token
$payload = [
    "id" => $user['ID'],
    "username" => $user['Username'],
    "exp" => time() + 3600  // Token expires in 1 hour
];
$jwt = JWT::encode($payload, $_ENV['JWT_SECRET'], 'HS256');

echo json_encode(["token" => $jwt]);
http_response_code(200);
?>
