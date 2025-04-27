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
    if (!preg_match('/[!@#$%^&*()\-_=+{};:,<.>]/', $password)) {
        throw new Exception('Password must contain at least one special character');
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

function generateOTP() {
    return str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
}

try {
    // Get database connection
    $db = Database::getInstance();
    $conn = $db->connect();

    // Get POST data
    $data = json_decode(file_get_contents('php://input'), true);
    if (!$data) {
        $data = $_POST;
    }

    // Validate required fields
    $requiredFields = ['email', 'username', 'password', 'user_role'];
    foreach ($requiredFields as $field) {
        if (!isset($data[$field]) || empty(trim($data[$field]))) {
            throw new Exception("$field is required");
        }
    }

    // Sanitize and validate input
    $email = filter_var(trim($data['email']), FILTER_SANITIZE_EMAIL);
    $username = trim($data['username']);
    $password = $data['password'];
    $userRole = trim($data['user_role']);

    // Validate input
    validateEmail($email);
    validateUsername($username);
    validatePassword($password);

    // Check if email or username already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? OR username = ?");
    $stmt->execute([$email, $username]);
    if ($stmt->fetch()) {
        throw new Exception('Email or username already exists');
    }

    // Start transaction
    $conn->beginTransaction();

    try {
        // Hash password using Argon2id
        $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);

        // Generate OTP
        $otp = generateOTP();
        $otpExpiry = date('Y-m-d H:i:s', strtotime('+15 minutes'));

        // Insert into users table
        $stmt = $conn->prepare("
            INSERT INTO users (email, username, password, role, status, created_at) 
            VALUES (?, ?, ?, ?, 'pending', NOW())
        ");
        $stmt->execute([$email, $username, $hashedPassword, $userRole]);
        $userId = $conn->lastInsertId();

        // Insert OTP
        $stmt = $conn->prepare("
            INSERT INTO otp_verifications (user_id, otp, expires_at) 
            VALUES (?, ?, ?)
        ");
        $stmt->execute([$userId, $otp, $otpExpiry]);

        // Insert role-specific data
        if ($userRole === 'planter') {
            // Additional planter fields
            $planterFields = [
                'full_name' => $data['full_name'] ?? null,
                'phone' => $data['phone'] ?? null,
                'address' => $data['address'] ?? null
            ];

            $stmt = $conn->prepare("
                INSERT INTO planters (user_id, full_name, phone, address) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                $userId,
                $planterFields['full_name'],
                $planterFields['phone'],
                $planterFields['address']
            ]);
        } elseif ($userRole === 'admin') {
            // Additional admin fields
            $adminFields = [
                'company_name' => $data['company_name'] ?? null,
                'license_number' => $data['license_number'] ?? null,
                'contact_number' => $data['contact_number'] ?? null
            ];

            $stmt = $conn->prepare("
                INSERT INTO mine_admins (user_id, company_name, license_number, contact_number) 
                VALUES (?, ?, ?, ?)
            ");
            $stmt->execute([
                $userId,
                $adminFields['company_name'],
                $adminFields['license_number'],
                $adminFields['contact_number']
            ]);
        }

        // Commit transaction
        $conn->commit();

        // TODO: Send OTP via email
        // For now, we'll return it in the response (only for development)
        echo json_encode([
            'success' => true,
            'message' => 'Registration successful. Please verify your email.',
            'debug_otp' => $otp, // Remove this in production
            'user_id' => $userId
        ]);

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
}
