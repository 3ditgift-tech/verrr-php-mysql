<?php
/**
 * Application Configuration for Frontend
 */

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Error reporting (set to 0 in production, 1 for debugging)
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
error_reporting(0);

// Timezone
date_default_timezone_set('UTC');

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 1);  // 1 for HTTPS

// Application constants
define('APP_NAME', 'VERCUL Business Onboarding');
define('APP_VERSION', '1.0.0');
define('ADMIN_EMAIL', 'admin@vercul.com');

// Base URL configuration
define('BASE_URL', 'https://vercul.com');

// Include database configuration
require_once __DIR__ . '/../config/database.php';

// Include helper functions
require_once __DIR__ . '/functions.php';

// Include database checker
require_once __DIR__ . '/db-check.php';
