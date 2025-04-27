<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');
header('Access-Control-Allow-Credentials: true');

session_start();

try {
    // Check if user is logged in
    if (!isset($_SESSION['user_id'])) {
        throw new Exception('Not authenticated');
    }

    // Get database connection
    require_once '../config/database.php';
    $db = Database::getInstance();
    $conn = $db->connect();

    // Get user data
    $stmt = $conn->prepare("
        SELECT id, username, email, role, status 
        FROM users 
        WHERE id = ? AND status = 'active'
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception('User not found or inactive');
    }

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

    // Return user data
    echo json_encode([
        'success' => true,
        'user' => [
            'id' => $user['id'],
            'username' => $user['username'],
            'email' => $user['email'],
            'role' => $user['role'],
            'role_data' => $roleData
        ]
    ]);

} catch (Exception $e) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
