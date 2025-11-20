<?php
session_start();
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';

// Redirect if already logged in
if (isset($_SESSION['admin_authenticated']) && $_SESSION['admin_authenticated'] === true) {
    header('Location: dashboard.php');
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT password_hash FROM admin_settings LIMIT 1");
        $stmt->execute();
        $result = $stmt->fetch();
        
        if ($result && password_verify($_POST['password'], $result['password_hash'])) {
            $_SESSION['admin_authenticated'] = true;
            $_SESSION['admin_login_time'] = time();
            header('Location: dashboard.php');
            exit;
        } else {
            $error = 'Invalid password';
        }
    } catch (Exception $e) {
        $error = 'Authentication error. Please try again.';
        error_log('Admin login error: ' . $e->getMessage());
    }
}

$pageTitle = 'Admin Login';
include __DIR__ . '/../../templates/header.php';
?>

<div class="container">
    <div class="admin-login-wrapper">
        <div class="admin-login-box">
            <h1>🔒 Admin Login</h1>
            <p>Enter your admin password to access the dashboard</p>
            
            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <form method="POST" action="" class="admin-login-form">
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required autofocus>
                </div>
                
                <button type="submit" class="btn btn-primary btn-block">Login</button>
            </form>
            
            <div class="login-footer">
                <a href="../index.php">← Back to Home</a>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../templates/footer.php'; ?>