<?php
/**
 * Application Configuration
 */

// Error reporting (set to 0 in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Timezone
date_default_timezone_set('UTC');

// CORS headers for API
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
header('Content-Type: application/json; charset=UTF-8');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Application constants
define('APP_NAME', 'VERCUL Business Onboarding');
define('APP_VERSION', '1.0.0');
define('ADMIN_EMAIL', 'admin@vercul.com');

// Base URL configuration
define('BASE_URL', 'http://localhost/verrr-php-mysql');
define('API_URL', BASE_URL . '/api');

// File upload settings
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_FILE_TYPES', ['pdf', 'jpg', 'jpeg', 'png']);

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 in production with HTTPS

// Include database configuration
require_once __DIR__ . '/database.php';
