<?php
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Content-Type');

require_once '../config/database.php';

function sendEmailOTP($email, $otp) {
    // TODO: Replace with your email service
    // For testing, we'll log it and return true
    error_log("Email OTP for $email: $otp");
    return true;
}

function sendSMSOTP($phone, $otp) {
    // TODO: Replace with your SMS service
    // For testing, we'll log it and return true
    error_log("SMS OTP for $phone: $otp");
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
    if (!isset($data['type']) || !in_array($data['type'], ['email', 'phone'])) {
        throw new Exception('Invalid OTP type');
    }
    if (!isset($data['user_id']) || !is_numeric($data['user_id'])) {
        throw new Exception('Invalid user ID');
    }

    $type = $data['type'];
    $userId = $data['user_id'];

    // Get user details
    $stmt = $conn->prepare("SELECT email, phone FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$user) {
        throw new Exception('User not found');
    }

    // Check if the verification type is already completed
    $verificationField = $type . '_verified';
    if ($user[$verificationField]) {
        throw new Exception("Your $type is already verified");
    }

    // Generate OTP
    $otp = generateOTP();
    $expiresAt = date('Y-m-d H:i:s', strtotime('+15 minutes'));

    // Delete any existing OTPs for this user and type
    $stmt = $conn->prepare("DELETE FROM otps WHERE user_id = ? AND type = ?");
    $stmt->execute([$userId, $type]);

    // Insert new OTP
    $stmt = $conn->prepare("INSERT INTO otps (user_id, type, code, expires_at) VALUES (?, ?, ?, ?)");
    $stmt->execute([$userId, $type, $otp, $expiresAt]);

    // Send OTP
    $sent = false;
    if ($type === 'email' && $user['email']) {
        $sent = sendEmailOTP($user['email'], $otp);
    } elseif ($type === 'phone' && $user['phone']) {
        $sent = sendSMSOTP($user['phone'], $otp);
    }

    if (!$sent) {
        throw new Exception("Failed to send OTP to your $type");
    }

    echo json_encode([
        'success' => true,
        'message' => "OTP sent successfully to your $type",
        'expires_at' => $expiresAt
    ]);

} catch (Exception $e) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => $e->getMessage()
    ]);
}
