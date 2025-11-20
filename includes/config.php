<?php
/**
 * Application Configuration for Frontend
 */

// Error reporting (set to 0 in production)
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Timezone
date_default_timezone_set('UTC');

// Session configuration
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', 0); // Set to 1 in production with HTTPS

// Application constants
define('APP_NAME', 'VERCUL Business Onboarding');
define('APP_VERSION', '1.0.0');
define('ADMIN_EMAIL', 'admin@vercul.com');

// Base URL configuration - adjust for your environment
define('BASE_URL', 'http://localhost/verrr-php-mysql/public');

// Include database configuration
require_once __DIR__ . '/../config/database.php';
