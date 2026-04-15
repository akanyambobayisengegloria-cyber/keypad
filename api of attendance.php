<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Handle preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// ==================== DATABASE CONFIGURATION ====================
$host = 'localhost';
$user = 'root';
$password = '';
$database = 'attendly_db';

// Create connection
$conn = new mysqli($host, $user, $password);

if ($conn->connect_error) {
    die(json_encode(['error' => 'Database connection failed: ' . $conn->connect_error]));
}

// Create database if not exists
$conn->query("CREATE DATABASE IF NOT EXISTS $database");
$conn->select_db($database);

// ==================== CREATE TABLES IF NOT EXISTS ====================
// Students table
$conn->query("CREATE TABLE IF NOT EXISTS students (
    id VARCHAR(50) PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    rfidCard VARCHAR(50) UNIQUE NOT NULL,
    trade VARCHAR(100),
    level VARCHAR(10),
    gender VARCHAR(10),
    fingerprintId INT,
    email VARCHAR(100),
    createdAt DATETIME
)");

// Sessions table
$conn->query("CREATE TABLE IF NOT EXISTS sessions (
    id VARCHAR(50) PRIMARY KEY,
    title VARCHAR(100) NOT NULL,
    date DATE,
    startTime TIME,
    endTime TIME,
    status VARCHAR(20),
    participants TEXT,
    createdAt DATETIME
)");

// Attendance table
$conn->query("CREATE TABLE IF NOT EXISTS attendance (
    id VARCHAR(50) PRIMARY KEY,
    sessionId VARCHAR(50),
    studentId VARCHAR(50),
    status VARCHAR(20),
    timestamp DATETIME,
    createdAt DATETIME
)");

// ==================== GET RESOURCE FROM URL ====================
$request_uri = $_SERVER['REQUEST_URI'];
$script_name = $_SERVER['SCRIPT_NAME'];

// Remove query string
$request_uri = strtok($request_uri, '?');

// Get the path after the script name
$path = str_replace($script_name, '', $request_uri);
$path = ltrim($path, '/');

// Get the resource (first segment)
$segments = explode('/', $path);
$resource = $segments[0] ?? '';
$id = $segments[1] ?? '';

$method = $_SERVER['REQUEST_METHOD'];

// If no resource is specified, return available endpoints
if (empty($resource)) {
    echo json_encode([
        'message' => 'API is working',
        'endpoints' => [
            'GET /students' => 'Get all students',
            'POST /students' => 'Add new student',
            'PUT /students/{id}' => 'Update student',
            'DELETE /students/{id}' => 'Delete student',
            'GET /sessions' => 'Get all sessions',
            'POST /sessions' => 'Create session',
            'PUT /sessions/{id}' => 'Update session',
            'DELETE /sessions/{id}' => 'Delete session',
            'GET /attendance' => 'Get attendance records',
            'POST /attendance' => 'Record attendance',
            'GET /data' => 'Get dashboard statistics',
            'GET /health' => 'Health check'
        ]
    ]);
    $conn->close();
    exit();
}

// ==================== HEALTH CHECK ====================
if ($resource === 'health') {
    $studentsCount = $conn->query("SELECT COUNT(*) as count FROM students")->fetch_assoc()['count'] ?? 0;
    $sessionsCount = $conn->query("SELECT COUNT(*) as count FROM sessions")->fetch_assoc()['count'] ?? 0;
    $attendanceCount = $conn->query("SELECT COUNT(*) as count FROM attendance")->fetch_assoc()['count'] ?? 0;
    
    echo json_encode([
        'status' => 'connected',
        'timestamp' => date('Y-m-d H:i:s'),
        'database' => $database,
        'stats' => [
            'students' => (int)$studentsCount,
            'sessions' => (int)$sessionsCount,
            'attendance' => (int)$attendanceCount
        ]
    ]);
    $conn->close();
    exit();
}

// ==================== STUDENTS CRUD ====================
if ($resource === 'students') {
    // GET all students
    if ($method === 'GET') {
        $result = $conn->query("SELECT * FROM students ORDER BY createdAt DESC");
        $students = [];
        while ($row = $result->fetch_assoc()) {
            $students[] = $row;
        }
        echo json_encode($students);
    }
    
    // CREATE student
    elseif ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        $id = $input['id'] ?? ('student_' . time());
        $name = $input['name'] ?? '';
        $rfidCard = $input['rfidCard'] ?? '';
        $trade = $input['trade'] ?? '';
        $level = $input['level'] ?? '';
        $gender = $input['gender'] ?? '';
        $fingerprintId = $input['fingerprintId'] ?? 0;
        $email = $input['email'] ?? '';
        $createdAt = $input['createdAt'] ?? date('Y-m-d H:i:s');
        
        // Check if RFID already exists
        $check = $conn->query("SELECT id FROM students WHERE rfidCard = '$rfidCard'");
        if ($check && $check->num_rows > 0) {
            echo json_encode(['error' => 'RFID Card already exists!']);
            $conn->close();
            exit();
        }
        
        $stmt = $conn->prepare("INSERT INTO students (id, name, rfidCard, trade, level, gender, fingerprintId, email, createdAt) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssiss", $id, $name, $rfidCard, $trade, $level, $gender, $fingerprintId, $email, $createdAt);
        
        if ($stmt->execute()) {
            echo json_encode(['message' => 'Student created successfully', 'student' => $input]);
        } else {
            echo json_encode(['error' => $stmt->error]);
        }
        $stmt->close();
    }
    
    // UPDATE student
    elseif ($method === 'PUT' && $id) {
        $input = json_decode(file_get_contents('php://input'), true);
        
        $name = $input['name'] ?? '';
        $rfidCard = $input['rfidCard'] ?? '';
        $trade = $input['trade'] ?? '';
        $level = $input['level'] ?? '';
        $email = $input['email'] ?? '';
        
        $stmt = $conn->prepare("UPDATE students SET name=?, rfidCard=?, trade=?, level=?, email=? WHERE id=?");
        $stmt->bind_param("ssssss", $name, $rfidCard, $trade, $level, $email, $id);
        
        if ($stmt->execute()) {
            echo json_encode(['message' => 'Student updated']);
        } else {
            echo json_encode(['error' => $stmt->error]);
        }
        $stmt->close();
    }
    
    // DELETE student
    elseif ($method === 'DELETE' && $id) {
        $conn->query("DELETE FROM attendance WHERE studentId='$id'");
        $stmt = $conn->prepare("DELETE FROM students WHERE id=?");
        $stmt->bind_param("s", $id);
        
        if ($stmt->execute()) {
            echo json_encode(['message' => 'Student deleted']);
        } else {
            echo json_encode(['error' => $stmt->error]);
        }
        $stmt->close();
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed for students']);
    }
}

// ==================== SESSIONS CRUD ====================
elseif ($resource === 'sessions') {
    // GET all sessions
    if ($method === 'GET') {
        $result = $conn->query("SELECT * FROM sessions ORDER BY createdAt DESC");
        $sessions = [];
        while ($row = $result->fetch_assoc()) {
            $row['participants'] = json_decode($row['participants'] ?? '[]', true);
            $sessions[] = $row;
        }
        echo json_encode($sessions);
    }
    
    // CREATE session
    elseif ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        $id = $input['id'] ?? ('sess_' . time());
        $title = $input['title'] ?? '';
        $date = $input['date'] ?? '';
        $startTime = $input['startTime'] ?? '';
        $endTime = $input['endTime'] ?? '';
        $participants = json_encode($input['participants'] ?? []);
        $status = $input['status'] ?? 'scheduled';
        $createdAt = $input['createdAt'] ?? date('Y-m-d H:i:s');
        
        $stmt = $conn->prepare("INSERT INTO sessions (id, title, date, startTime, endTime, status, participants, createdAt) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssss", $id, $title, $date, $startTime, $endTime, $status, $participants, $createdAt);
        
        if ($stmt->execute()) {
            echo json_encode(['message' => 'Session created']);
        } else {
            echo json_encode(['error' => $stmt->error]);
        }
        $stmt->close();
    }
    
    // UPDATE session
    elseif ($method === 'PUT' && $id) {
        $input = json_decode(file_get_contents('php://input'), true);
        
        $title = $input['title'] ?? '';
        $date = $input['date'] ?? '';
        $startTime = $input['startTime'] ?? '';
        $endTime = $input['endTime'] ?? '';
        $status = $input['status'] ?? '';
        
        $stmt = $conn->prepare("UPDATE sessions SET title=?, date=?, startTime=?, endTime=?, status=? WHERE id=?");
        $stmt->bind_param("ssssss", $title, $date, $startTime, $endTime, $status, $id);
        
        if ($stmt->execute()) {
            echo json_encode(['message' => 'Session updated']);
        } else {
            echo json_encode(['error' => $stmt->error]);
        }
        $stmt->close();
    }
    
    // DELETE session
    elseif ($method === 'DELETE' && $id) {
        $stmt = $conn->prepare("DELETE FROM sessions WHERE id=?");
        $stmt->bind_param("s", $id);
        
        if ($stmt->execute()) {
            echo json_encode(['message' => 'Session deleted']);
        } else {
            echo json_encode(['error' => $stmt->error]);
        }
        $stmt->close();
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed for sessions']);
    }
}

// ==================== ATTENDANCE CRUD ====================
elseif ($resource === 'attendance') {
    // GET all attendance
    if ($method === 'GET') {
        $sessionId = $_GET['sessionId'] ?? '';
        $query = "SELECT * FROM attendance ORDER BY timestamp DESC";
        if ($sessionId) {
            $query = "SELECT * FROM attendance WHERE sessionId = '$sessionId' ORDER BY timestamp DESC";
        }
        $result = $conn->query($query);
        $records = [];
        while ($row = $result->fetch_assoc()) {
            $records[] = $row;
        }
        echo json_encode($records);
    }
    
    // CREATE attendance
    elseif ($method === 'POST') {
        $input = json_decode(file_get_contents('php://input'), true);
        
        $id = $input['id'] ?? ('att_' . time());
        $sessionId = $input['sessionId'] ?? '';
        $studentId = $input['studentId'] ?? '';
        $userId = $input['userId'] ?? '';
        $status = $input['status'] ?? 'present';
        
        // If userId is provided (from ESP32), find student by RFID
        if ($userId && !$studentId) {
            $result = $conn->query("SELECT id FROM students WHERE rfidCard = '$userId'");
            if ($result && $result->num_rows > 0) {
                $row = $result->fetch_assoc();
                $studentId = $row['id'];
            }
        }
        
        if (!$studentId) {
            echo json_encode(['error' => 'Student not found']);
            $conn->close();
            exit();
        }
        
        // Check if attendance already exists
        $check = $conn->query("SELECT id FROM attendance WHERE sessionId = '$sessionId' AND studentId = '$studentId'");
        
        if ($check && $check->num_rows > 0) {
            $conn->query("UPDATE attendance SET status='$status', timestamp=NOW() WHERE sessionId='$sessionId' AND studentId='$studentId'");
            echo json_encode(['message' => 'Attendance updated']);
        } else {
            $stmt = $conn->prepare("INSERT INTO attendance (id, sessionId, studentId, status, timestamp, createdAt) VALUES (?, ?, ?, ?, NOW(), NOW())");
            $stmt->bind_param("ssss", $id, $sessionId, $studentId, $status);
            
            if ($stmt->execute()) {
                echo json_encode(['message' => 'Attendance recorded']);
            } else {
                echo json_encode(['error' => $stmt->error]);
            }
            $stmt->close();
        }
    }
    
    // UPDATE attendance
    elseif ($method === 'PUT' && $id) {
        $input = json_decode(file_get_contents('php://input'), true);
        $status = $input['status'] ?? '';
        
        $stmt = $conn->prepare("UPDATE attendance SET status=? WHERE id=?");
        $stmt->bind_param("ss", $status, $id);
        
        if ($stmt->execute()) {
            echo json_encode(['message' => 'Attendance updated']);
        } else {
            echo json_encode(['error' => $stmt->error]);
        }
        $stmt->close();
    }
    
    // DELETE attendance
    elseif ($method === 'DELETE' && $id) {
        $stmt = $conn->prepare("DELETE FROM attendance WHERE id=?");
        $stmt->bind_param("s", $id);
        
        if ($stmt->execute()) {
            echo json_encode(['message' => 'Attendance deleted']);
        } else {
            echo json_encode(['error' => $stmt->error]);
        }
        $stmt->close();
    } else {
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed for attendance']);
    }
}

// ==================== DASHBOARD STATS ====================
elseif ($resource === 'data') {
    $students = $conn->query("SELECT COUNT(*) as count FROM students")->fetch_assoc();
    $sessions = $conn->query("SELECT COUNT(*) as count FROM sessions")->fetch_assoc();
    $attendance = $conn->query("SELECT COUNT(*) as count FROM attendance")->fetch_assoc();
    
    $today = date('Y-m-d');
    $todayAttendance = $conn->query("SELECT COUNT(*) as count FROM attendance WHERE DATE(timestamp) = '$today'")->fetch_assoc();
    
    $statusStats = [];
    $statusResult = $conn->query("SELECT status, COUNT(*) as count FROM attendance GROUP BY status");
    if ($statusResult) {
        while ($row = $statusResult->fetch_assoc()) {
            $statusStats[] = $row;
        }
    }
    
    echo json_encode([
        'totalStudents' => (int)($students['count'] ?? 0),
        'totalSessions' => (int)($sessions['count'] ?? 0),
        'totalAttendance' => (int)($attendance['count'] ?? 0),
        'todayAttendance' => (int)($todayAttendance['count'] ?? 0),
        'statusStats' => $statusStats
    ]);
}

// ==================== UNKNOWN ENDPOINT ====================
else {
    http_response_code(404);
    echo json_encode(['error' => 'Endpoint not found: ' . $resource]);
}

$conn->close();
?>