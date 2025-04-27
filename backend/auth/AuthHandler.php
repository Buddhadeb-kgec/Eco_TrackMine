<?php
require_once '../config/database.php';

class AuthHandler {
    private $db;
    private $conn;

    public function __construct() {
        $this->db = Database::getInstance();
        $this->conn = $this->db->connect();
    }

    private function validateEmail($email) {
        return filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    private function validatePassword($password) {
        // At least 8 characters, 1 uppercase, 1 lowercase, 1 number
        return strlen($password) >= 8 && 
               preg_match('/[A-Z]/', $password) && 
               preg_match('/[a-z]/', $password) && 
               preg_match('/[0-9]/', $password);
    }

    public function register($data) {
        try {
            // Validate input
            if (!$this->validateEmail($data['email'])) {
                throw new Exception('Invalid email format');
            }
            if (!$this->validatePassword($data['password'])) {
                throw new Exception('Password must be at least 8 characters long and contain uppercase, lowercase, and numbers');
            }
            if (!in_array($data['user_role'], ['admin', 'planter'])) {
                throw new Exception('Invalid user role');
            }

            // Check for existing email/username
            $stmt = $this->conn->prepare("SELECT COUNT(*) FROM users WHERE email = ? OR username = ?");
            $stmt->execute([$data['email'], $data['username']]);
            if ($stmt->fetchColumn() > 0) {
                throw new Exception('Email or username already exists');
            }

            // Start transaction
            $this->conn->beginTransaction();

            // Hash password with strong algorithm
            $password_hash = password_hash($data['password'], PASSWORD_ARGON2ID);

            // Insert into users table
            $stmt = $this->conn->prepare("INSERT INTO users (username, email, password_hash, user_role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$data['username'], $data['email'], $password_hash, $data['user_role']]);
            $user_id = $this->conn->lastInsertId();

            // Insert role-specific data
            if ($data['user_role'] === 'admin') {
                if (empty($data['mine_name']) || empty($data['registration_number']) || empty($data['mine_address'])) {
                    throw new Exception('Missing required admin fields');
                }
                $stmt = $this->conn->prepare("INSERT INTO mine_admins (user_id, mine_name, registration_number, mine_address) VALUES (?, ?, ?, ?)");
                $stmt->execute([$user_id, $data['mine_name'], $data['registration_number'], $data['mine_address']]);
            } else {
                if (empty($data['full_name']) || empty($data['phone_number'])) {
                    throw new Exception('Missing required planter fields');
                }
                $stmt = $this->conn->prepare("INSERT INTO planters (user_id, full_name, phone_number) VALUES (?, ?, ?)");
                $stmt->execute([$user_id, $data['full_name'], $data['phone_number']]);
            }

            $this->conn->commit();
            return ['success' => true, 'message' => 'Registration successful'];
        } catch (Exception $e) {
            $this->conn->rollBack();
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function login($username, $password, $role) {
        try {
            if (empty($username) || empty($password) || empty($role)) {
                throw new Exception('All fields are required');
            }

            $stmt = $this->conn->prepare("SELECT id, password_hash, user_role FROM users WHERE username = ? AND user_role = ?");
            $stmt->execute([$username, $role]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($user && password_verify($password, $user['password_hash'])) {
                // Start secure session
                if (session_status() === PHP_SESSION_NONE) {
                    ini_set('session.use_strict_mode', 1);
                    ini_set('session.use_only_cookies', 1);
                    ini_set('session.cookie_httponly', 1);
                    ini_set('session.cookie_secure', 1);
                    ini_set('session.cookie_samesite', 'Strict');
                    session_start();
                }

                // Regenerate session ID to prevent session fixation
                session_regenerate_id(true);
                
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['user_role'] = $user['user_role'];
                $_SESSION['last_activity'] = time();
                $_SESSION['ip_address'] = $_SERVER['REMOTE_ADDR'];

                return ['success' => true, 'message' => 'Login successful'];
            }
            return ['success' => false, 'message' => 'Invalid credentials'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function sendOTP($user_id, $type) {
        if (!in_array($type, ['email', 'phone'])) {
            return ['success' => false, 'message' => 'Invalid OTP type'];
        }

        $otp = sprintf("%06d", mt_rand(1, 999999));
        $expires = date('Y-m-d H:i:s', strtotime('+10 minutes'));

        try {
            // Delete any existing unverified OTPs
            $stmt = $this->conn->prepare("DELETE FROM otp_verifications WHERE user_id = ? AND otp_type = ? AND verified = FALSE");
            $stmt->execute([$user_id, $type]);

            // Insert new OTP
            $stmt = $this->conn->prepare("INSERT INTO otp_verifications (user_id, otp_type, otp_code, expires_at) VALUES (?, ?, ?, ?)");
            $stmt->execute([$user_id, $type, $otp, $expires]);

            // In production, implement actual email/SMS sending here
            return ['success' => true, 'otp' => $otp];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }

    public function verifyOTP($user_id, $otp, $type) {
        try {
            if (!preg_match('/^\d{6}$/', $otp)) {
                throw new Exception('Invalid OTP format');
            }

            $stmt = $this->conn->prepare("SELECT * FROM otp_verifications WHERE user_id = ? AND otp_type = ? AND otp_code = ? AND expires_at > NOW() AND verified = FALSE");
            $stmt->execute([$user_id, $type, $otp]);
            
            if ($stmt->rowCount() > 0) {
                $stmt = $this->conn->prepare("UPDATE otp_verifications SET verified = TRUE WHERE user_id = ? AND otp_type = ? AND otp_code = ?");
                $stmt->execute([$user_id, $type, $otp]);
                return ['success' => true, 'message' => 'OTP verified successfully'];
            }
            return ['success' => false, 'message' => 'Invalid or expired OTP'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}
?>
