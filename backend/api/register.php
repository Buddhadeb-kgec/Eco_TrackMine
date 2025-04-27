<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

// Validation functions
function validateEmail($email) {
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        throw new Exception('Invalid email format');
    }
    return true;
}

function validatePassword($password) {
    if (strlen($password) < 8) {
        throw new Exception('Password must be at least 8 characters long');
    }
    if (!preg_match('/[A-Z]/', $password)) {
        throw new Exception('Password must contain at least one uppercase letter');
    }
    if (!preg_match('/[a-z]/', $password)) {
        throw new Exception('Password must contain at least one lowercase letter');
    }
    if (!preg_match('/[0-9]/', $password)) {
        throw new Exception('Password must contain at least one number');
    }
    return true;
}

function validateUsername($username) {
    if (strlen($username) < 3 || strlen($username) > 50) {
        throw new Exception('Username must be between 3 and 50 characters');
    }
    if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
        throw new Exception('Username can only contain letters, numbers, and underscores');
    }
    return true;
}

function validatePhone($phone) {
    // Remove any non-digit characters
    $phone = preg_replace('/[^0-9]/', '', $phone);
    if (strlen($phone) < 10 || strlen($phone) > 15) {
        throw new Exception('Invalid phone number format');
    }
    return $phone;
}

try {
    // Get database connection
    $db = Database::getInstance();
    $conn = $db->connect();

    // Get POST data
    $postData = file_get_contents('php://input');
    if (empty($postData)) {
        $data = $_POST;
    } else {
        $data = json_decode($postData, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new Exception('Invalid JSON data');
        }
    }

    if (empty($data)) {
        throw new Exception('No data received');
    }

    // Validate required fields
    $requiredFields = ['email', 'username', 'password', 'user_role'];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            throw new Exception("$field is required");
        }
    }

    // Additional required fields based on role
    if ($data['user_role'] === 'planter') {
        if (!isset($data['full_name']) || empty(trim($data['full_name']))) {
            throw new Exception("Full name is required for planters");
        }
        if (!isset($data['phone']) || empty(trim($data['phone']))) {
            throw new Exception("Phone number is required for planters");
        }
    } else if ($data['user_role'] === 'admin') {
        if (!isset($data['company_name']) || empty(trim($data['company_name']))) {
            throw new Exception("Company name is required for admins");
        }
        if (!isset($data['license_number']) || empty(trim($data['license_number']))) {
            throw new Exception("License number is required for admins");
        }
        if (!isset($data['phone']) || empty(trim($data['phone']))) {
            throw new Exception("Phone number is required for admins");
        }
    } else {
        throw new Exception("Invalid user role");
    }

    // Sanitize and validate input
    $email = filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL);
    $username = trim($data['username']);
    $password = $data['password'];
    $userRole = trim($data['user_role']);
    $phone = validatePhone($data['phone']);

    // Validate input
    validateEmail($email);
    validateUsername($username);
    validatePassword($password);

    // Check if email, username, or phone already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ? OR phone = ?");
    $stmt->execute([$email, $username, $phone]);
    if ($stmt->fetch()) {
        throw new Exception('Email, username, or phone number already exists');
    }

    // Start transaction
    $conn->beginTransaction();

    try {
        // Hash password
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);

        // Insert into users table
        $stmt = $conn->prepare("
            INSERT INTO users (email, username, password, role, phone, status, created_at) 
            VALUES (?, ?, ?, ?, ?, 'pending', NOW())
        ");
        $stmt->execute([$email, $username, $hashedPassword, $userRole, $phone]);
        $userId = $conn->lastInsertId();

        // Insert role-specific data
        if ($userRole === 'planter') {
            $stmt = $conn->prepare("
                INSERT INTO planters (user_id, full_name, address) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([
                $userId,
                trim($data['full_name']),
                isset($data['address']) ? trim($data['address']) : null
            ]);
        } else {
            $stmt = $conn->prepare("
                INSERT INTO mine_admins (user_id, company_name, license_number) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([
                $userId,
                trim($data['company_name']),
                trim($data['license_number'])
            ]);
        }

        // Commit transaction
        $conn->commit();

        // Return success response
        http_response_code(200);
        echo json_encode([
            'success' => true,
            'message' => 'Registration successful! Please verify your email and phone number.',
            'user_id' => $userId
        ]);
        exit;

    } catch (Exception $e) {
        $conn->rollBack();
        throw $e;
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
    exit;
}
