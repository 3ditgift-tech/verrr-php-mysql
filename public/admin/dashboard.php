<?php
session_start();
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_auth();

$pageTitle = 'Admin Dashboard';

// Get statistics
try {
    $db = Database::getInstance()->getConnection();
    
    $statsQuery = $db->query("
        SELECT 
            COUNT(*) as total,
            SUM(CASE WHEN status = 'Submitted' THEN 1 ELSE 0 END) as submitted,
            SUM(CASE WHEN status = 'In Review' THEN 1 ELSE 0 END) as inReview,
            SUM(CASE WHEN status = 'Action Required' THEN 1 ELSE 0 END) as actionRequired,
            SUM(CASE WHEN status = 'Approved' THEN 1 ELSE 0 END) as approved,
            SUM(CASE WHEN status = 'Declined' THEN 1 ELSE 0 END) as declined
        FROM applications
    ");
    $stats = $statsQuery->fetch();
    
    // Get recent applications
    $applicationsQuery = $db->query("
        SELECT * FROM applications 
        ORDER BY submitted_at DESC 
        LIMIT 50
    ");
    $applications = $applicationsQuery->fetchAll();
    
} catch (Exception $e) {
    error_log('Dashboard error: ' . $e->getMessage());
    $stats = ['total' => 0, 'submitted' => 0, 'inReview' => 0, 'actionRequired' => 0, 'approved' => 0, 'declined' => 0];
    $applications = [];
}

include __DIR__ . '/../../templates/admin_header.php';
?>

<div class="admin-dashboard">
    <div class="dashboard-header">
        <h1>Dashboard</h1>
        <div class="header-actions">
            <span class="admin-user">👤 Admin</span>
            <a href="logout.php" class="btn btn-secondary btn-sm">Logout</a>
        </div>
    </div>
    
    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-icon">📊</div>
            <div class="stat-content">
                <h3>Total Applications</h3>
                <div class="stat-value"><?php echo $stats['total']; ?></div>
            </div>
        </div>
        
        <div class="stat-card pending">
            <div class="stat-icon">📨</div>
            <div class="stat-content">
                <h3>Pending Review</h3>
                <div class="stat-value"><?php echo $stats['submitted']; ?></div>
            </div>
        </div>
        
        <div class="stat-card in-review">
            <div class="stat-icon">🔍</div>
            <div class="stat-content">
                <h3>In Review</h3>
                <div class="stat-value"><?php echo $stats['inReview']; ?></div>
            </div>
        </div>
        
        <div class="stat-card action-required">
            <div class="stat-icon">⚠️</div>
            <div class="stat-content">
                <h3>Action Required</h3>
                <div class="stat-value"><?php echo $stats['actionRequired']; ?></div>
            </div>
        </div>
        
        <div class="stat-card approved">
            <div class="stat-icon">✅</div>
            <div class="stat-content">
                <h3>Approved</h3>
                <div class="stat-value"><?php echo $stats['approved']; ?></div>
            </div>
        </div>
        
        <div class="stat-card declined">
            <div class="stat-icon">❌</div>
            <div class="stat-content">
                <h3>Declined</h3>
                <div class="stat-value"><?php echo $stats['declined']; ?></div>
            </div>
        </div>
    </div>
    
    <div class="applications-section">
        <h2>Recent Applications</h2>
        
        <?php if (empty($applications)): ?>
            <div class="empty-state">
                <p>No applications yet.</p>
            </div>
        <?php else: ?>
            <div class="table-responsive">
                <table class="applications-table">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Company</th>
                            <th>Applicant</th>
                            <th>Country</th>
                            <th>Status</th>
                            <th>Submitted</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($applications as $app): ?>
                            <tr>
                                <td><code><?php echo htmlspecialchars($app['id']); ?></code></td>
                                <td><?php echo htmlspecialchars($app['company_name']); ?></td>
                                <td>
                                    <?php echo htmlspecialchars($app['applicant_name']); ?><br>
                                    <small><?php echo htmlspecialchars($app['applicant_email']); ?></small>
                                </td>
                                <td><?php echo htmlspecialchars($app['country']); ?></td>
                                <td>
                                    <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $app['status'])); ?>">
                                        <?php echo htmlspecialchars($app['status']); ?>
                                    </span>
                                </td>
                                <td><?php echo date('M j, Y', strtotime($app['submitted_at'])); ?></td>
                                <td>
                                    <a href="view.php?id=<?php echo urlencode($app['id']); ?>" class="btn btn-sm btn-primary">View</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../../templates/admin_footer.php'; ?>