<?php
require 'vendor/autoload.php';
use Firebase\JWT\JWT;
use Firebase\JWT\Key;

header("Content-Type: application/json");

function verifyToken() {
    $headers = getallheaders();
    
    // Debugging: Log received headers
    error_log("Received Headers: " . json_encode($headers));

    // Check for Authorization in different formats
    $token = null;
    if (isset($headers['Authorization'])) {
        $token = str_replace("Bearer ", "", $headers['Authorization']);
    } elseif (isset($headers['HTTP_AUTHORIZATION'])) {
        $token = str_replace("Bearer ", "", $headers['HTTP_AUTHORIZATION']);
    } elseif (function_exists('apache_request_headers')) {
        $apacheHeaders = apache_request_headers();
        if (isset($apacheHeaders['Authorization'])) {
            $token = str_replace("Bearer ", "", $apacheHeaders['Authorization']);
        }
    }

    // If no token is found, return an error
    if (!$token) {
        http_response_code(401);
        echo json_encode(["error" => "Token missing", "headers" => $headers]);
        exit;
    }

    // Validate token
    $jwt_secret = "MF7JCcuB+5UtsZy887byx3BQcuSAAEQvwmR5fsWaAgU="; // Use your actual JWT secret
    try {
        $decoded = JWT::decode($token, new Key($jwt_secret, 'HS256'));
        return (array) $decoded;
    } catch (Exception $e) {
        http_response_code(401);
        echo json_encode(["error" => "Invalid token"]);
        exit;
    }
}
?>
