<?php
session_start();
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_auth();

$pageTitle = 'Admin Settings';
$success = '';
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $db = Database::getInstance()->getConnection();
        
        if (isset($_POST['action'])) {
            if ($_POST['action'] === 'change_password') {
                if (!empty($_POST['new_password']) && strlen($_POST['new_password']) >= 4) {
                    $passwordHash = password_hash($_POST['new_password'], PASSWORD_BCRYPT);
                    $stmt = $db->prepare("UPDATE admin_settings SET password_hash = ?");
                    $stmt->execute([$passwordHash]);
                    $success = 'Password updated successfully';
                } else {
                    $error = 'Password must be at least 4 characters';
                }
            } elseif ($_POST['action'] === 'update_smtp') {
                $stmt = $db->prepare("
                    UPDATE smtp_settings SET 
                    host = ?, port = ?, username = ?, security = ?, 
                    from_name = ?, from_address = ?
                ");
                $stmt->execute([
                    $_POST['smtp_host'],
                    $_POST['smtp_port'],
                    $_POST['smtp_username'],
                    $_POST['smtp_security'],
                    $_POST['from_name'],
                    $_POST['from_address']
                ]);
                
                // Update password only if provided
                if (!empty($_POST['smtp_password'])) {
                    $stmt = $db->prepare("UPDATE smtp_settings SET password = ?");
                    $stmt->execute([$_POST['smtp_password']]);
                }
                
                $success = 'SMTP settings updated successfully';
            }
        }
    } catch (Exception $e) {
        $error = 'Error updating settings';
        error_log('Settings error: ' . $e->getMessage());
    }
}

// Get current SMTP settings
try {
    $db = Database::getInstance()->getConnection();
    $stmt = $db->query("SELECT * FROM smtp_settings LIMIT 1");
    $smtp = $stmt->fetch();
} catch (Exception $e) {
    $smtp = null;
}

include __DIR__ . '/../../templates/admin_header.php';
?>

<div class="admin-dashboard">
    <div class="dashboard-header">
        <h1>Settings</h1>
        <div class="header-actions">
            <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
        </div>
    </div>
    
    <?php if ($success): ?>
        <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
    <?php endif; ?>
    <?php if ($error): ?>
        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
    <?php endif; ?>
    
    <div class="settings-sections">
        <div class="settings-section">
            <h2>Change Admin Password</h2>
            <form method="POST" class="settings-form">
                <input type="hidden" name="action" value="change_password">
                <div class="form-group">
                    <label for="new_password">New Password (min 4 characters)</label>
                    <input type="password" id="new_password" name="new_password" required minlength="4">
                </div>
                <button type="submit" class="btn btn-primary">Update Password</button>
            </form>
        </div>
        
        <div class="settings-section">
            <h2>SMTP Email Configuration</h2>
            <form method="POST" class="settings-form">
                <input type="hidden" name="action" value="update_smtp">
                
                <div class="form-group">
                    <label for="smtp_host">SMTP Host</label>
                    <input type="text" id="smtp_host" name="smtp_host" 
                           value="<?php echo htmlspecialchars($smtp['host'] ?? ''); ?>" 
                           placeholder="smtp.gmail.com">
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="smtp_port">Port</label>
                        <input type="number" id="smtp_port" name="smtp_port" 
                               value="<?php echo htmlspecialchars($smtp['port'] ?? 587); ?>">
                    </div>
                    
                    <div class="form-group">
                        <label for="smtp_security">Security</label>
                        <select id="smtp_security" name="smtp_security">
                            <option value="none" <?php echo ($smtp['security'] ?? '') === 'none' ? 'selected' : ''; ?>>None</option>
                            <option value="ssl" <?php echo ($smtp['security'] ?? '') === 'ssl' ? 'selected' : ''; ?>>SSL</option>
                            <option value="starttls" <?php echo ($smtp['security'] ?? 'starttls') === 'starttls' ? 'selected' : ''; ?>>STARTTLS</option>
                        </select>
                    </div>
                </div>
                
                <div class="form-group">
                    <label for="smtp_username">Username</label>
                    <input type="text" id="smtp_username" name="smtp_username" 
                           value="<?php echo htmlspecialchars($smtp['username'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="smtp_password">Password (leave blank to keep current)</label>
                    <input type="password" id="smtp_password" name="smtp_password" 
                           placeholder="Enter new password or leave blank">
                </div>
                
                <div class="form-group">
                    <label for="from_name">From Name</label>
                    <input type="text" id="from_name" name="from_name" 
                           value="<?php echo htmlspecialchars($smtp['from_name'] ?? 'VERCUL Support'); ?>">
                </div>
                
                <div class="form-group">
                    <label for="from_address">From Email Address</label>
                    <input type="email" id="from_address" name="from_address" 
                           value="<?php echo htmlspecialchars($smtp['from_address'] ?? 'no-reply@vercul.com'); ?>">
                </div>
                
                <button type="submit" class="btn btn-primary">Save SMTP Settings</button>
            </form>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../../templates/admin_footer.php'; ?>