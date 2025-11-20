<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle ?? 'Admin Dashboard'); ?> - VERCUL Admin</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="stylesheet" href="/assets/css/admin.css">
</head>
<body class="admin-body">
    <div class="admin-layout">
        <aside class="admin-sidebar">
            <div class="sidebar-header">
                <h2>VERCUL Admin</h2>
            </div>
            <nav class="sidebar-nav">
                <ul>
                    <li><a href="/admin/dashboard.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'dashboard.php' ? 'active' : ''; ?>">
                        📊 Dashboard
                    </a></li>
                    <li><a href="/admin/settings.php" class="<?php echo basename($_SERVER['PHP_SELF']) === 'settings.php' ? 'active' : ''; ?>">
                        ⚙️ Settings
                    </a></li>
                    <li><a href="/admin/logout.php">
                        🚪 Logout
                    </a></li>
                </ul>
            </nav>
        </aside>
        
        <div class="admin-main">