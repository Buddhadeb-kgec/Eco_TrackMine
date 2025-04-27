<?php
class Database {
    private static $instance = null;
    private $conn;

    private $host = 'localhost';
    private $db_name = 'skmd_db';
    private $username = 'root';  // default XAMPP username
    private $password = '';      // default XAMPP password is empty

    private function __construct() {
        try {
            $this->conn = new PDO(
                "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                $this->username,
                $this->password
            );
            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            $this->conn->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
            $this->conn->exec("SET NAMES utf8mb4");
        } catch(PDOException $e) {
            error_log("Connection Error: " . $e->getMessage());
            throw new Exception("Database connection failed");
        }
    }

    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function connect() {
        return $this->conn;
    }

    // Prevent cloning of the instance
    private function __clone() {}

    // Prevent unserializing of the instance
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }

    // Test the connection
    public static function testConnection() {
        try {
            $instance = self::getInstance();
            $conn = $instance->connect();
            
            // Try a simple query
            $stmt = $conn->query("SELECT 1");
            if ($stmt) {
                return [
                    'success' => true,
                    'message' => 'Database connection successful',
                    'details' => [
                        'server_info' => $conn->getAttribute(PDO::ATTR_SERVER_VERSION),
                        'client_info' => $conn->getAttribute(PDO::ATTR_CLIENT_VERSION)
                    ]
                ];
            }
        } catch (Exception $e) {
            return [
                'success' => false,
                'message' => 'Database connection failed',
                'error' => $e->getMessage()
            ];
        }
    }
}
?>
