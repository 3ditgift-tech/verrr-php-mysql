<?php
/**
 * Database Configuration
 * 
 * IMPORTANT: Update these credentials with your Hostinger database details
 */

class Database {
    private static $instance = null;
    private $connection;
    
    // ========================================
    // UPDATE THESE WITH YOUR CREDENTIALS
    // ========================================
    private $host = 'localhost';
    private $database = 'u342028963_vercul_busines';  // Your Hostinger database name
    private $username = 'u342028963_vercul_admin';    // Your Hostinger database username
    private $password = '';  // ADD YOUR DATABASE PASSWORD HERE!
    // ========================================
    
    private $charset = 'utf8mb4';
    
    private function __construct() {
        try {
            $dsn = "mysql:host={$this->host};dbname={$this->database};charset={$this->charset}";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            
            $this->connection = new PDO($dsn, $this->username, $this->password, $options);
        } catch (PDOException $e) {
            // Log error but don't expose details
            error_log("Database connection failed: " . $e->getMessage());
            throw new Exception("Database connection failed. Please check configuration.");
        }
    }
    
    public static function getInstance() {
        if (self::$instance === null) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    public function getConnection() {
        return $this->connection;
    }
    
    private function __clone() {}
    
    public function __wakeup() {
        throw new Exception("Cannot unserialize singleton");
    }
}
