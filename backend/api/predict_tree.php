<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../middleware/auth_middleware.php';

// Check authentication
$auth = checkAuth();

function validateImage($file) {
    $allowedTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    $maxSize = 5 * 1024 * 1024; // 5MB

    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        throw new Exception('No file uploaded');
    }

    if (!in_array($file['type'], $allowedTypes)) {
        throw new Exception('Invalid file type. Only JPG, JPEG, and PNG are allowed');
    }

    if ($file['size'] > $maxSize) {
        throw new Exception('File size too large. Maximum size is 5MB');
    }

    return true;
}

try {
    // Validate uploaded files
    $images = ['treeImage1', 'treeImage2', 'treeImage3'];
    $predictions = [];
    
    foreach ($images as $image) {
        if (!isset($_FILES[$image])) {
            throw new Exception("Missing $image upload");
        }
        validateImage($_FILES[$image]);
        
        // Read image and convert to base64
        $imageData = base64_encode(file_get_contents($_FILES[$image]['tmp_name']));
        
        // Call Python script for prediction
        $pythonScript = realpath(__DIR__ . '/../ml/tree_classifier.py');
        if (!file_exists($pythonScript)) {
            throw new Exception("ML model script not found");
        }
        
        $command = escapeshellcmd("python \"$pythonScript\" " . escapeshellarg($imageData));
        $output = shell_exec($command);
        
        if ($output === null) {
            throw new Exception("Error executing ML model. Command: $command");
        }
        
        $result = json_decode($output, true);
        if ($result === null) {
            throw new Exception("Invalid JSON response from ML model: $output");
        }
        if (!$result['success']) {
            throw new Exception($result['error']);
        }
        
        $predictions[$image] = $result;
    }
    
    // Check if all images are tropical trees
    $allTropical = true;
    $avgConfidence = 0;
    
    foreach ($predictions as $prediction) {
        if (!$prediction['is_tropical_tree']) {
            $allTropical = false;
        }
        $avgConfidence += $prediction['confidence'];
    }
    $avgConfidence /= count($predictions);
    
    // Save images if they're all tropical trees
    if ($allTropical) {
        $uploadDir = '../uploads/trees/' . $auth['user_id'] . '/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        foreach ($images as $image) {
            $extension = pathinfo($_FILES[$image]['name'], PATHINFO_EXTENSION);
            $newFilename = uniqid() . '.' . $extension;
            move_uploaded_file($_FILES[$image]['tmp_name'], $uploadDir . $newFilename);
        }
        
        // Save to database
        require_once '../config/database.php';
        $db = Database::getInstance();
        $conn = $db->connect();
        
        $stmt = $conn->prepare("INSERT INTO plantations (user_id, image1, image2, image3, description, confidence_score, status) VALUES (?, ?, ?, ?, ?, ?, 'pending')");
        $stmt->execute([
            $auth['user_id'],
            $uploadDir . $images[0],
            $uploadDir . $images[1],
            $uploadDir . $images[2],
            $_POST['description'],
            $avgConfidence
        ]);
        
        echo json_encode([
            'success' => true,
            'message' => 'Images uploaded successfully and verified as tropical trees',
            'predictions' => $predictions,
            'average_confidence' => $avgConfidence
        ]);
    } else {
        echo json_encode([
            'success' => false,
            'message' => 'One or more images were not identified as tropical trees',
            'predictions' => $predictions
        ]);
    }
    
} catch (Exception $e) {
    error_log("Plantation API Error: " . $e->getMessage());
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage(),
        'debug_info' => [
            'php_version' => PHP_VERSION,
            'python_version' => shell_exec('python --version'),
            'working_dir' => getcwd(),
            'script_path' => __FILE__,
            'error_time' => date('Y-m-d H:i:s')
        ]
    ]);
}
