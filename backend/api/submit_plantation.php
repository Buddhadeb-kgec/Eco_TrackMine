<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

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
    // Get database connection
    $db = Database::getInstance();
    $conn = $db->connect();

    // Validate primary image upload
    if (!isset($_FILES['image1'])) {
        throw new Exception("Primary image is required");
    }

    // Validate file uploads
    $uploadDir = '../uploads/plantations/' . $auth['user_id'] . '/';
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
    }

    // Handle file uploads
    $imagePaths = ['image1_path' => null, 'image2_path' => null, 'image3_path' => null];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    // Process each possible image upload
    foreach (['image1', 'image2', 'image3'] as $index => $fieldName) {
        if (isset($_FILES[$fieldName]) && $_FILES[$fieldName]['error'] === UPLOAD_ERR_OK) {
            $file = $_FILES[$fieldName];
            
            // Validate file type
            if (!in_array($file['type'], $allowedTypes)) {
                throw new Exception("Invalid file type for $fieldName. Allowed types: JPG, JPEG, PNG");
            }

            // Validate file size
            if ($file['size'] > $maxSize) {
                throw new Exception("File size too large for $fieldName. Maximum size: 5MB");
            }

            // Generate unique filename
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $newFilename = uniqid() . '_' . ($index + 1) . '.' . $extension;
            $targetPath = $uploadDir . $newFilename;

            // Move uploaded file
            if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
                throw new Exception("Failed to upload $fieldName");
            }

            $imagePaths[$fieldName . '_path'] = $targetPath;
        }
    }

    // Get other form data
    $description = $_POST['description'] ?? '';
    $treeType = $_POST['tree_type'] ?? 'unknown';
    $latitude = $_POST['latitude'] ?? null;
    $longitude = $_POST['longitude'] ?? null;
    $confidenceScore = $_POST['confidence_score'] ?? 0;

    // Start transaction
    $conn->beginTransaction();

    try {
        // Build dynamic query based on available images
        $fields = ['user_id', 'description', 'location_lat', 'location_lng', 'tree_type', 'confidence_score', 'status'];
        $values = ['?', '?', '?', '?', '?', '?', 'pending'];
        $params = [$auth['user_id'], $description, $latitude, $longitude, $treeType, $confidenceScore];

        // Add available image paths to query
        foreach ($imagePaths as $field => $path) {
            if ($path !== null) {
                $fields[] = $field;
                $values[] = '?';
                $params[] = $path;
            }
        }

        // Construct and execute query
        $query = "INSERT INTO plantations (" . implode(', ', $fields) . ") VALUES (" . implode(', ', $values) . ")";
        $stmt = $conn->prepare($query);
        $stmt->execute($params);

        $plantationId = $conn->lastInsertId();

        // Commit transaction
        $conn->commit();

        echo json_encode([
            'success' => true,
            'message' => 'Plantation submitted successfully',
            'plantation_id' => $plantationId
        ]);

    } catch (Exception $e) {
        $conn->rollBack();
        // Delete uploaded files if database insertion fails
        foreach ($imagePaths as $path) {
            if ($path !== null && file_exists($path)) {
                unlink($path);
            }
        }
        throw $e;
    }

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}