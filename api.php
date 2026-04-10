<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Set headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Database connection
require_once 'config/database.php';

$database = new Database();
$db = $database->getConnection();

// Start session
session_start();

// Parse request
$method = $_SERVER['REQUEST_METHOD'];
$request_uri = $_SERVER['REQUEST_URI'];
$path = parse_url($request_uri, PHP_URL_PATH);
$path = trim($path, '/');
$segments = explode('/', $path);

// Find the endpoint (after api.php)
$endpoint_index = array_search('api.php', $segments);
if ($endpoint_index !== false) {
    $endpoint = isset($segments[$endpoint_index + 1]) ? $segments[$endpoint_index + 1] : '';
    $id = isset($segments[$endpoint_index + 2]) ? $segments[$endpoint_index + 2] : null;
} else {
    $endpoint = isset($segments[0]) ? $segments[0] : '';
    $id = isset($segments[1]) ? $segments[1] : null;
}

// Get input data
$input = json_decode(file_get_contents('php://input'), true);

// Create response array
$response = ['success' => false, 'error' => 'Unknown endpoint'];

// Handle different endpoints
if ($endpoint == 'login' && $method == 'POST') {
    // Check if we have input
    if (!$input) {
        $response = ['success' => false, 'error' => 'No data received. Input: ' . file_get_contents('php://input')];
    } elseif (!isset($input['role']) || !isset($input['password'])) {
        $response = ['success' => false, 'error' => 'Missing role or password. Received: ' . json_encode($input)];
    } else {
        $role = $input['role'];
        $password = $input['password'];
        $md5_password = md5($password);
        
        // Try MD5 password
        $query = "SELECT id, name, email, phone, role FROM users WHERE role = :role AND password = :password";
        $stmt = $db->prepare($query);
        $stmt->execute([':role' => $role, ':password' => $md5_password]);
        
        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            $response = ['success' => true, 'user' => $user];
        } else {
            // Try plain text password
            $stmt2 = $db->prepare($query);
            $stmt2->execute([':role' => $role, ':password' => $password]);
            
            if ($stmt2->rowCount() > 0) {
                $user = $stmt2->fetch(PDO::FETCH_ASSOC);
                // Update to MD5
                $update = $db->prepare("UPDATE users SET password = MD5(:password) WHERE id = :id");
                $update->execute([':password' => $password, ':id' => $user['id']]);
                $response = ['success' => true, 'user' => $user];
            } else {
                $response = ['success' => false, 'error' => 'Invalid credentials for role: ' . $role];
            }
        }
    }
} 
elseif ($endpoint == 'animals' && $method == 'GET') {
    $stmt = $db->query("SELECT * FROM animals ORDER BY created_at DESC");
    $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
elseif ($endpoint == 'dashboard' && $method == 'GET') {
    $totalAnimals = $db->query("SELECT COUNT(*) FROM animals")->fetchColumn();
    $totalHealth = $db->query("SELECT COUNT(*) FROM health_records")->fetchColumn();
    $sickCount = $db->query("SELECT COUNT(*) FROM animals WHERE is_sick = 1")->fetchColumn();
    $totalUsers = $db->query("SELECT COUNT(*) FROM users")->fetchColumn();
    
    $recentStmt = $db->query("SELECT action, details, user_name, created_at FROM scan_logs ORDER BY created_at DESC LIMIT 5");
    $recentActivity = $recentStmt->fetchAll(PDO::FETCH_ASSOC);
    
    $response = [
        'totalAnimals' => (int)$totalAnimals,
        'totalHealth' => (int)$totalHealth,
        'sickCount' => (int)$sickCount,
        'totalUsers' => (int)$totalUsers,
        'recentActivity' => $recentActivity
    ];
}
elseif ($endpoint == 'users' && $method == 'GET') {
    $stmt = $db->query("SELECT id, name, email, phone, role FROM users ORDER BY created_at DESC");
    $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
elseif ($endpoint == 'health' && $method == 'GET') {
    $stmt = $db->query("SELECT * FROM health_records ORDER BY created_at DESC");
    $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
elseif ($endpoint == 'logs' && $method == 'GET') {
    $stmt = $db->query("SELECT * FROM scan_logs ORDER BY created_at DESC LIMIT 100");
    $response = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
elseif ($endpoint == 'register_mode' && $method == 'POST') {
    if ($input && isset($input['tagId'])) {
        $_SESSION['pending_tag_id'] = $input['tagId'];
        $response = ['success' => true, 'tagId' => $input['tagId']];
    } else {
        $response = ['success' => false, 'error' => 'No tag ID provided'];
    }
}
elseif ($endpoint == 'get_pending_tag' && $method == 'GET') {
    if (isset($_SESSION['pending_tag_id'])) {
        $response = ['tagId' => $_SESSION['pending_tag_id']];
        unset($_SESSION['pending_tag_id']);
    } else {
        $response = ['tagId' => null];
    }
}
elseif ($endpoint == 'send_to_dashboard' && $method == 'POST') {
    if ($input && isset($input['rfid_tag'])) {
        $rfidTag = $input['rfid_tag'];
        $stmt = $db->prepare("SELECT * FROM animals WHERE rfid_tag = :rfid_tag");
        $stmt->execute([':rfid_tag' => $rfidTag]);
        
        if ($stmt->rowCount() > 0) {
            $animal = $stmt->fetch(PDO::FETCH_ASSOC);
            $response = ['success' => true, 'message' => 'Animal details sent to dashboard', 'animal' => $animal];
        } else {
            $response = ['success' => false, 'error' => 'Animal not found with RFID: ' . $rfidTag];
        }
    } else {
        $response = ['success' => false, 'error' => 'No RFID tag provided'];
    }
}
elseif ($endpoint == 'recover' && $method == 'POST') {
    $stmt = $db->query("SELECT * FROM deleted_items ORDER BY deleted_at DESC LIMIT 1");
    if ($stmt->rowCount() > 0) {
        $deleted = $stmt->fetch(PDO::FETCH_ASSOC);
        $itemData = json_decode($deleted['item_data'], true);
        
        if ($deleted['item_type'] == 'animal') {
            $insert = $db->prepare("INSERT INTO animals (id, rfid_tag, name, animal_type, breed, sex, birthdate, is_pregnant, is_sick, owner_contact) VALUES (:id, :rfid_tag, :name, :animal_type, :breed, :sex, :birthdate, :is_pregnant, :is_sick, :owner_contact)");
            $insert->execute([
                ':id' => $itemData['id'],
                ':rfid_tag' => $itemData['rfid_tag'],
                ':name' => $itemData['name'],
                ':animal_type' => $itemData['animal_type'],
                ':breed' => $itemData['breed'],
                ':sex' => $itemData['sex'],
                ':birthdate' => $itemData['birthdate'],
                ':is_pregnant' => $itemData['is_pregnant'],
                ':is_sick' => $itemData['is_sick'],
                ':owner_contact' => $itemData['owner_contact']
            ]);
            $db->prepare("DELETE FROM deleted_items WHERE id = :id")->execute([':id' => $deleted['id']]);
            $response = ['success' => true, 'type' => 'Animal', 'message' => 'Animal recovered'];
        } else {
            $response = ['success' => false, 'error' => 'Recovery not implemented for this type'];
        }
    } else {
        $response = ['success' => false, 'error' => 'No items to recover'];
    }
}

// Output response
echo json_encode($response);
?>