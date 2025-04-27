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
    if (!isset($data['type']) || !in_array($data['type'], ['email', 'phone'])) {
        throw new Exception('Invalid OTP type');
    }
    if (!isset($data['user_id']) || !is_numeric($data['user_id'])) {
        throw new Exception('Invalid user ID');
    }
    if (!isset($data['otp']) || !preg_match('/^\d{6}$/', $data['otp'])) {
        throw new Exception('Invalid OTP format');
    }

    $type = $data['type'];
    $userId = $data['user_id'];
    $otp = $data['otp'];

    // Start transaction
    $conn->beginTransaction();

    try {
        // Check if user exists and verification status
        $stmt = $conn->prepare("SELECT email_verified, phone_verified FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            throw new Exception('User not found');
        }

        // Check if already verified
        $verificationField = $type . '_verified';
        if ($user[$verificationField]) {
            throw new Exception("Your $type is already verified");
        }

        // Verify OTP
        $stmt = $conn->prepare("
            SELECT id 
            FROM otps 
            WHERE user_id = ? 
            AND type = ? 
            AND code = ? 
            AND expires_at > NOW()
        ");
        $stmt->execute([$userId, $type, $otp]);

        if (!$stmt->fetch()) {
            throw new Exception('Invalid or expired OTP');
        }

        // Mark the verification as complete
        $stmt = $conn->prepare("UPDATE users SET {$verificationField} = TRUE WHERE id = ?");
        $stmt->execute([$userId]);

        // Delete used OTP
        $stmt = $conn->prepare("DELETE FROM otps WHERE user_id = ? AND type = ?");
        $stmt->execute([$userId, $type]);

        // Check if both email and phone are verified
        $stmt = $conn->prepare("SELECT email_verified, phone_verified FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $verificationStatus = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($verificationStatus['email_verified'] && $verificationStatus['phone_verified']) {
            // Activate the account
            $stmt = $conn->prepare("UPDATE users SET status = 'active' WHERE id = ?");
            $stmt->execute([$userId]);
        }

        // Commit transaction
        $conn->commit();

        echo json_encode([
            'success' => true,
            'message' => "Your $type has been verified successfully",
            'account_activated' => ($verificationStatus['email_verified'] && $verificationStatus['phone_verified'])
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
