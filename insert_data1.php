<?php
// Set headers
header("Content-Type: application/json");

// Database configuration
$host = "localhost";
$db_name = "smart_home";
$username = "root";
$password = "";

// Create connection
$conn = new mysqli($host, $username, $password, $db_name);

// Check connection
if ($conn->connect_error) {
    echo json_encode([
        "status" => "error",
         "message" => "Database connection failed: " . $conn->connect_error
    ]);
    exit();
}

// Get raw POST data
$input = file_get_contents("php://input");

// Decode JSON
$data = json_decode($input, true);

// Validate input
if (!isset($data['temperature']) || !isset($data['humidity'])) {
    echo json_encode([
        "status" => "error",
        "message" => "Missing temperature or humidity"
    ]);
    exit();
}

$temperature = $data['temperature'];
$humidity = $data['humidity'];

// Prepare SQL statement (prevents SQL injection)
$stmt = $conn->prepare("INSERT INTO sensor_data (temperature, humidity) VALUES (?, ?)");

if (!$stmt) {
    echo json_encode([
        "status" => "error",
        "message" => "Prepare failed: " . $conn->error
    ]);
    exit();
}

// Bind parameters
$stmt->bind_param("dd", $temperature, $humidity);

// Execute query
if ($stmt->execute()) {
   echo json_encode([
        "status" => "success",
        "message" => "Data inserted successfully"
    ]);
} else {
     echo json_encode([
        "status" => "error",
        "message" => "Insert failed: " . $stmt->error
    ]);
}

// Close connections
$stmt->close();
$conn->close();
?>
