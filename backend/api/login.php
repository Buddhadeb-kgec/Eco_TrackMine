<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

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
    if (!isset($data['username']) || !isset($data['password'])) {
        throw new Exception('Username and password are required');
    }

    $username = trim($data['username']);
    $password = $data['password'];

    // Get user from database
    $stmt = $conn->prepare("
        SELECT id, username, email, password, role, status 
        FROM users 
        WHERE username = ? OR email = ?
    ");
    $stmt->execute([$username, $username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception('Invalid username or password');
    }

    // Verify password
    if (!password_verify($password, $user['password'])) {
        throw new Exception('Invalid username or password');
    }

    // Check if account is active
    if ($user['status'] !== 'active') {
        throw new Exception('Account is not active. Please verify your email.');
    }

    // Start session
    session_start();
    $_SESSION['user_id'] = $user['id'];
    $_SESSION['username'] = $user['username'];
    $_SESSION['role'] = $user['role'];
    $_SESSION['last_activity'] = time();

    // Update last login
    $stmt = $conn->prepare("UPDATE users SET last_login = NOW() WHERE id = ?");
    $stmt->execute([$user['id']]);

    // Get role-specific data
    $roleData = [];
    if ($user['role'] === 'planter') {
        $stmt = $conn->prepare("SELECT * FROM planters WHERE user_id = ?");
        $stmt->execute([$user['id']]);
        $roleData = $stmt->fetch(PDO::FETCH_ASSOC);
    } elseif ($user['role'] === 'admin') {
        $stmt = $conn->prepare("SELECT * FROM mine_admins WHERE user_id = ?");
        $stmt->execute([$user['id']]);
        $roleData = $stmt->fetch(PDO::FETCH_ASSOC);
    }

    echo json_encode([
        'success' => true,
        'message' => 'Login successful',
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role'],
            'role_data' => $roleData
        ]
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
