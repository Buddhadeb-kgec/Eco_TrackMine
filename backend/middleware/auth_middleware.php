<?php
function checkAuth() {
    session_start();
    
    if (!isset($_SESSION['user_id'])) {
        header('Content-Type: application/json');
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
    
    return [
        'user_id' => $_SESSION['user_id'],
        'user_role' => $_SESSION['user_role']
    ];
}

function requireRole($required_role) {
    $auth = checkAuth();
    
    if ($auth['user_role'] !== $required_role) {
        header('Content-Type: application/json');
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Forbidden']);
        exit;
    }
    
    return $auth;
}
?>
