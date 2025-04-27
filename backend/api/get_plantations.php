<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';
require_once '../middleware/auth_middleware.php';

// Check authentication
$auth = checkAuth();
if (!$auth['success']) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

try {
    $db = Database::getInstance();
    $conn = $db->connect();

    // Get query parameters
    $page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 10;
    $offset = ($page - 1) * $limit;

    // Get total count
    $stmt = $conn->prepare("
        SELECT COUNT(*) as total 
        FROM plantations 
        WHERE user_id = ?
    ");
    $stmt->execute([$auth['user_id']]);
    $total = $stmt->fetch(PDO::FETCH_ASSOC)['total'];

    // Get plantations
    $stmt = $conn->prepare("
        SELECT * FROM plantations 
        WHERE user_id = ? 
        ORDER BY created_at DESC 
        LIMIT ? OFFSET ?
    ");
    $stmt->execute([$auth['user_id'], $limit, $offset]);
    $plantations = $stmt->fetchAll(PDO::FETCH_ASSOC);

    echo json_encode([
        'success' => true,
        'data' => [
            'plantations' => $plantations,
            'pagination' => [
                'total' => $total,
                'page' => $page,
                'limit' => $limit,
                'total_pages' => ceil($total / $limit)
            ]
        ]
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}