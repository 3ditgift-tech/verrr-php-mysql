<?php
/**
 * Authentication Controller
 * Handles admin authentication
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../utils/Response.php';

class AuthController {
    
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Verify admin password
     */
    public function verifyPassword() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['password'])) {
            Response::error('Password is required');
        }
        
        try {
            $stmt = $this->db->prepare("SELECT password_hash FROM admin_settings LIMIT 1");
            $stmt->execute();
            $result = $stmt->fetch();
            
            if (!$result) {
                Response::serverError('Admin settings not configured');
            }
            
            // Verify password
            $isValid = password_verify($data['password'], $result['password_hash']);
            
            if ($isValid) {
                // Start session and set admin flag
                if (session_status() === PHP_SESSION_NONE) {
                    session_start();
                }
                $_SESSION['admin_authenticated'] = true;
                $_SESSION['admin_login_time'] = time();
                
                Response::success(['authenticated' => true], 'Authentication successful');
            } else {
                Response::unauthorized('Invalid password');
            }
            
        } catch (Exception $e) {
            error_log("Auth error: " . $e->getMessage());
            Response::serverError('Authentication failed');
        }
    }
    
    /**
     * Update admin password
     */
    public function updatePassword() {
        // Require admin authentication
        $this->requireAuth();
        
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['newPassword'])) {
            Response::error('New password is required');
        }
        
        if (strlen($data['newPassword']) < 4) {
            Response::error('Password must be at least 4 characters');
        }
        
        try {
            $passwordHash = password_hash($data['newPassword'], PASSWORD_BCRYPT);
            
            $stmt = $this->db->prepare("
                UPDATE admin_settings SET password_hash = ?, updated_at = NOW()
            ");
            $stmt->execute([$passwordHash]);
            
            Response::success(null, 'Password updated successfully');
            
        } catch (Exception $e) {
            error_log("Password update error: " . $e->getMessage());
            Response::serverError('Failed to update password');
        }
    }
    
    /**
     * Check if admin is authenticated
     */
    public function checkAuth() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        $isAuthenticated = isset($_SESSION['admin_authenticated']) && $_SESSION['admin_authenticated'] === true;
        
        Response::success(['authenticated' => $isAuthenticated]);
    }
    
    /**
     * Logout admin
     */
    public function logout() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        unset($_SESSION['admin_authenticated']);
        unset($_SESSION['admin_login_time']);
        session_destroy();
        
        Response::success(null, 'Logged out successfully');
    }
    
    /**
     * Helper: Require admin authentication
     */
    public function requireAuth() {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
        
        if (!isset($_SESSION['admin_authenticated']) || $_SESSION['admin_authenticated'] !== true) {
            Response::unauthorized('Authentication required');
        }
        
        // Optional: Check session timeout (e.g., 2 hours)
        $timeout = 2 * 60 * 60; // 2 hours in seconds
        if (isset($_SESSION['admin_login_time']) && (time() - $_SESSION['admin_login_time'] > $timeout)) {
            unset($_SESSION['admin_authenticated']);
            session_destroy();
            Response::unauthorized('Session expired');
        }
    }
}
