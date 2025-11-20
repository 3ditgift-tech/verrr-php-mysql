<?php
/**
 * Database Connection Checker
 * This file checks if database is accessible before allowing site access
 */

require_once __DIR__ . '/../config/database.php';

/**
 * Check if database connection is working
 */
function checkDatabaseConnection() {
    try {
        $db = Database::getInstance()->getConnection();
        
        // Test if we can query the database
        $stmt = $db->query("SELECT 1");
        
        return true;
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Show database setup page if connection fails
 */
function showDatabaseSetupPage() {
    $pageTitle = 'Database Setup Required';
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title><?php echo $pageTitle; ?></title>
        <style>
            * { margin: 0; padding: 0; box-sizing: border-box; }
            body {
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 100vh;
                display: flex;
                align-items: center;
                justify-content: center;
                padding: 20px;
            }
            .setup-container {
                background: white;
                border-radius: 12px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.3);
                max-width: 600px;
                width: 100%;
                padding: 40px;
            }
            .icon {
                font-size: 60px;
                text-align: center;
                margin-bottom: 20px;
            }
            h1 {
                text-align: center;
                color: #333;
                margin-bottom: 20px;
                font-size: 28px;
            }
            .error-message {
                background: #fee;
                border: 1px solid #fcc;
                color: #c33;
                padding: 15px;
                border-radius: 8px;
                margin-bottom: 20px;
            }
            .info-box {
                background: #e3f2fd;
                border: 1px solid #90caf9;
                color: #1565c0;
                padding: 15px;
                border-radius: 8px;
                margin-bottom: 20px;
            }
            .steps {
                margin: 20px 0;
            }
            .step {
                margin-bottom: 20px;
                padding-left: 30px;
                position: relative;
            }
            .step::before {
                content: attr(data-step);
                position: absolute;
                left: 0;
                top: 0;
                width: 24px;
                height: 24px;
                background: #667eea;
                color: white;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-weight: bold;
                font-size: 14px;
            }
            .step h3 {
                margin-bottom: 8px;
                color: #333;
            }
            .step p {
                color: #666;
                line-height: 1.6;
            }
            .code {
                background: #f5f5f5;
                padding: 10px;
                border-radius: 4px;
                font-family: monospace;
                font-size: 13px;
                margin: 10px 0;
                overflow-x: auto;
            }
            .refresh-btn {
                display: block;
                width: 100%;
                padding: 15px;
                background: #667eea;
                color: white;
                border: none;
                border-radius: 8px;
                font-size: 16px;
                font-weight: 600;
                cursor: pointer;
                margin-top: 20px;
            }
            .refresh-btn:hover {
                background: #5568d3;
            }
        </style>
    </head>
    <body>
        <div class="setup-container">
            <div class="icon">⚠️</div>
            <h1>Database Setup Required</h1>
            
            <div class="error-message">
                <strong>Cannot connect to database!</strong><br>
                Please configure your database connection before accessing the website.
            </div>
            
            <div class="info-box">
                <strong>Your Database Credentials:</strong><br>
                Database: <code>u342028963_vercul_busines</code><br>
                Username: <code>u342028963_vercul_admin</code>
            </div>
            
            <div class="steps">
                <div class="step" data-step="1">
                    <h3>Create Database</h3>
                    <p>In Hostinger panel, go to <strong>MySQL Databases</strong> and ensure your database exists.</p>
                </div>
                
                <div class="step" data-step="2">
                    <h3>Import Schema</h3>
                    <p>Import the database schema using phpMyAdmin:</p>
                    <div class="code">database/schema.sql</div>
                </div>
                
                <div class="step" data-step="3">
                    <h3>Configure Connection</h3>
                    <p>Edit <code>config/database.php</code> and add your database password:</p>
                    <div class="code">
private $host = 'localhost';<br>
private $database = 'u342028963_vercul_busines';<br>
private $username = 'u342028963_vercul_admin';<br>
private $password = 'YOUR_PASSWORD_HERE'; // Add password
                    </div>
                </div>
                
                <div class="step" data-step="4">
                    <h3>Refresh Page</h3>
                    <p>After completing the setup, click the button below to test the connection.</p>
                </div>
            </div>
            
            <button class="refresh-btn" onclick="location.reload()">Test Connection Again</button>
        </div>
    </body>
    </html>
    <?php
    exit;
}

/**
 * Require database connection for page access
 * Call this at the top of any page that needs database access
 */
function requireDatabase() {
    if (!checkDatabaseConnection()) {
        showDatabaseSetupPage();
    }
}
