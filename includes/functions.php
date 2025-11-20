<?php
/**
 * Helper Functions
 */

/**
 * Require admin authentication
 */
function require_auth() {
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    
    if (!isset($_SESSION['admin_authenticated']) || $_SESSION['admin_authenticated'] !== true) {
        header('Location: /admin/login.php');
        exit;
    }
    
    // Check session timeout (2 hours)
    $timeout = 2 * 60 * 60;
    if (isset($_SESSION['admin_login_time']) && (time() - $_SESSION['admin_login_time'] > $timeout)) {
        unset($_SESSION['admin_authenticated']);
        session_destroy();
        header('Location: /admin/login.php?timeout=1');
        exit;
    }
}

/**
 * Sanitize output
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Format date for display
 */
function format_date($date, $format = 'F j, Y') {
    return date($format, strtotime($date));
}

/**
 * Get status badge class
 */
function get_status_class($status) {
    return 'status-' . strtolower(str_replace(' ', '-', $status));
}
