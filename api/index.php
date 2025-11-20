<?php
/**
 * API Router
 * Routes all API requests to appropriate controllers
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../utils/Response.php';

// Get request method and path
$method = $_SERVER['REQUEST_METHOD'];
$path = $_SERVER['REQUEST_URI'];

// Remove base path and query string
$path = parse_url($path, PHP_URL_PATH);
$path = str_replace('/api', '', $path);
$path = trim($path, '/');

// Split path into segments
$segments = explode('/', $path);
$endpoint = $segments[0] ?? '';
$action = $segments[1] ?? '';
$param = $segments[2] ?? '';

try {
    // Route to appropriate controller
    switch ($endpoint) {
        
        // Application endpoints
        case 'applications':
            require_once __DIR__ . '/controllers/ApplicationController.php';
            $controller = new ApplicationController();
            
            switch ($action) {
                case 'submit':
                    if ($method !== 'POST') Response::error('Method not allowed', 405);
                    $controller->submit();
                    break;
                    
                case 'all':
                    if ($method !== 'GET') Response::error('Method not allowed', 405);
                    $controller->getAll();
                    break;
                    
                case 'get':
                    if ($method !== 'GET') Response::error('Method not allowed', 405);
                    if (empty($param)) Response::error('Application ID required');
                    $controller->get($param);
                    break;
                    
                case 'update-status':
                    if ($method !== 'POST') Response::error('Method not allowed', 405);
                    $controller->updateStatus();
                    break;
                    
                case 'update-notes':
                    if ($method !== 'POST') Response::error('Method not allowed', 405);
                    $controller->updateNotes();
                    break;
                    
                case 'stats':
                    if ($method !== 'GET') Response::error('Method not allowed', 405);
                    $controller->getStats();
                    break;
                    
                case 'pending-count':
                    if ($method !== 'GET') Response::error('Method not allowed', 405);
                    $controller->getPendingCount();
                    break;
                    
                default:
                    Response::notFound('Endpoint not found');
            }
            break;
            
        // Authentication endpoints
        case 'auth':
            require_once __DIR__ . '/controllers/AuthController.php';
            $controller = new AuthController();
            
            switch ($action) {
                case 'verify':
                    if ($method !== 'POST') Response::error('Method not allowed', 405);
                    $controller->verifyPassword();
                    break;
                    
                case 'update-password':
                    if ($method !== 'POST') Response::error('Method not allowed', 405);
                    $controller->updatePassword();
                    break;
                    
                case 'check':
                    if ($method !== 'GET') Response::error('Method not allowed', 405);
                    $controller->checkAuth();
                    break;
                    
                case 'logout':
                    if ($method !== 'POST') Response::error('Method not allowed', 405);
                    $controller->logout();
                    break;
                    
                default:
                    Response::notFound('Endpoint not found');
            }
            break;
            
        // Settings endpoints
        case 'settings':
            require_once __DIR__ . '/controllers/SettingsController.php';
            $controller = new SettingsController();
            
            switch ($action) {
                case 'frontend':
                    if ($method === 'GET') {
                        $controller->getFrontendSettings();
                    } elseif ($method === 'POST') {
                        $controller->saveFrontendSettings();
                    } else {
                        Response::error('Method not allowed', 405);
                    }
                    break;
                    
                case 'email-templates':
                    if ($method === 'GET') {
                        $controller->getEmailTemplates();
                    } elseif ($method === 'POST') {
                        $controller->updateEmailTemplate();
                    } else {
                        Response::error('Method not allowed', 405);
                    }
                    break;
                    
                case 'smtp':
                    if ($method === 'GET') {
                        $controller->getSmtpSettings();
                    } elseif ($method === 'POST') {
                        $controller->saveSmtpSettings();
                    } else {
                        Response::error('Method not allowed', 405);
                    }
                    break;
                    
                case 'test-email':
                    if ($method !== 'POST') Response::error('Method not allowed', 405);
                    $controller->sendTestEmail();
                    break;
                    
                default:
                    Response::notFound('Endpoint not found');
            }
            break;
            
        // Health check
        case 'health':
            Response::success(['status' => 'ok', 'timestamp' => time()], 'API is running');
            break;
            
        default:
            Response::notFound('Endpoint not found');
    }
    
} catch (Exception $e) {
    error_log("API Error: " . $e->getMessage());
    Response::serverError('An unexpected error occurred');
}
