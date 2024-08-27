<?php
// Enable error reporting for debugging
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Allow cross-origin requests from your S3 website domain
header("Access-Control-Allow-Origin: http://hostingbucket1.s3-website.ap-south-1.amazonaws.com");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

// Handle OPTIONS preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit();
}

// Database credentials
$servername = "database-1.ctwcqkmykzvj.ap-south-1.rds.amazonaws.com";
$username = "admin";
$password = "project123";
$dbname = "rds_db_name";

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    echo json_encode(['status' => 'error', 'message' => 'Database connection failed: ' . $conn->connect_error]);
    exit();
}

// Handle POST request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the raw POST data
    $data = json_decode(file_get_contents('php://input'), true);

    // Check if the POST data contains username and password
    if (isset($data['username']) && isset($data['password'])) {
        $username = $data['username'];
        $password = $data['password'];

        // Example query to check credentials (adjust column names if needed)
        $stmt = $conn->prepare("SELECT * FROM users WHERE username = ? AND password = ?");
        $stmt->bind_param('ss', $username, $password);
        $stmt->execute();

        if ($stmt->get_result()->num_rows > 0) {
            // Credentials are valid
            echo json_encode(['status' => 'success', 'message' => 'Login successful']);
        } else {
            // Invalid credentials
            echo json_encode(['status' => 'error', 'message' => 'Invalid username or password']);
        }

        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Username and password required']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid request method']);
}

$conn->close();
?>