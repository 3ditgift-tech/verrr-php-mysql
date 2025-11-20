<?php
session_start();
require_once __DIR__ . '/../../includes/config.php';
require_once __DIR__ . '/../../includes/functions.php';
require_once __DIR__ . '/../../utils/EmailService.php';
require_auth();

$pageTitle = 'View Application';
$application = null;
$error = '';
$success = '';

if (!isset($_GET['id'])) {
    header('Location: dashboard.php');
    exit;
}

try {
    $db = Database::getInstance()->getConnection();
    
    // Handle status update
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
        if ($_POST['action'] === 'update_status') {
            $stmt = $db->prepare("UPDATE applications SET status = ? WHERE id = ?");
            $stmt->execute([$_POST['status'], $_GET['id']]);
            
            // Send email notification
            $emailService = new EmailService();
            $stmt = $db->prepare("SELECT * FROM applications WHERE id = ?");
            $stmt->execute([$_GET['id']]);
            $app = $stmt->fetch();
            
            $statusTemplateMap = [
                'In Review' => 'application-in-review',
                'Action Required' => 'application-action-required',
                'Approved' => 'application-approved',
                'Declined' => 'application-declined'
            ];
            
            if (isset($statusTemplateMap[$_POST['status']])) {
                $emailService->sendNotification($statusTemplateMap[$_POST['status']], $app, $app['applicant_email']);
            }
            
            $success = 'Status updated successfully';
        } elseif ($_POST['action'] === 'update_notes') {
            $stmt = $db->prepare("UPDATE applications SET admin_notes = ? WHERE id = ?");
            $stmt->execute([$_POST['notes'], $_GET['id']]);
            $success = 'Notes updated successfully';
        }
    }
    
    // Get application
    $stmt = $db->prepare("SELECT * FROM applications WHERE id = ?");
    $stmt->execute([$_GET['id']]);
    $application = $stmt->fetch();
    
    if (!$application) {
        $error = 'Application not found';
    }
    
} catch (Exception $e) {
    $error = 'Error loading application';
    error_log('View application error: ' . $e->getMessage());
}

include __DIR__ . '/../../templates/admin_header.php';
?>

<div class="admin-dashboard">
    <div class="dashboard-header">
        <h1>Application Details</h1>
        <div class="header-actions">
            <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
        </div>
    </div>
    
    <?php if ($error): ?>
        <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
    <?php elseif ($application): ?>
        <?php if ($success): ?>
            <div class="success-message"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <div class="application-view">
            <div class="view-section">
                <h2>Application Information</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Application ID:</label>
                        <span><?php echo htmlspecialchars($application['id']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Status:</label>
                        <span class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $application['status'])); ?>">
                            <?php echo htmlspecialchars($application['status']); ?>
                        </span>
                    </div>
                    <div class="info-item">
                        <label>Submitted:</label>
                        <span><?php echo date('F j, Y g:i A', strtotime($application['submitted_at'])); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="view-section">
                <h2>Company Details</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Company Name:</label>
                        <span><?php echo htmlspecialchars($application['company_name']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Registration Number:</label>
                        <span><?php echo htmlspecialchars($application['registration_number']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Country:</label>
                        <span><?php echo htmlspecialchars($application['country']); ?></span>
                    </div>
                    <div class="info-item full-width">
                        <label>Business Address:</label>
                        <span><?php echo nl2br(htmlspecialchars($application['business_address'])); ?></span>
                    </div>
                    <div class="info-item">
                        <label>City:</label>
                        <span><?php echo htmlspecialchars($application['city']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Postal Code:</label>
                        <span><?php echo htmlspecialchars($application['postal_code']); ?></span>
                    </div>
                </div>
            </div>
            
            <div class="view-section">
                <h2>Applicant Details</h2>
                <div class="info-grid">
                    <div class="info-item">
                        <label>Full Name:</label>
                        <span><?php echo htmlspecialchars($application['applicant_name']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Role:</label>
                        <span><?php echo htmlspecialchars($application['applicant_role']); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Date of Birth:</label>
                        <span><?php echo date('F j, Y', strtotime($application['applicant_dob'])); ?></span>
                    </div>
                    <div class="info-item">
                        <label>Email:</label>
                        <span><a href="mailto:<?php echo htmlspecialchars($application['applicant_email']); ?>"><?php echo htmlspecialchars($application['applicant_email']); ?></a></span>
                    </div>
                    <div class="info-item">
                        <label>Phone:</label>
                        <span><a href="tel:<?php echo htmlspecialchars($application['applicant_phone']); ?>"><?php echo htmlspecialchars($application['applicant_phone']); ?></a></span>
                    </div>
                </div>
            </div>
            
            <div class="view-section">
                <h2>Update Status</h2>
                <form method="POST" class="status-form">
                    <input type="hidden" name="action" value="update_status">
                    <div class="form-group">
                        <label for="status">Change Status:</label>
                        <select name="status" id="status" class="form-control">
                            <option value="Submitted" <?php echo $application['status'] === 'Submitted' ? 'selected' : ''; ?>>Submitted</option>
                            <option value="In Review" <?php echo $application['status'] === 'In Review' ? 'selected' : ''; ?>>In Review</option>
                            <option value="Action Required" <?php echo $application['status'] === 'Action Required' ? 'selected' : ''; ?>>Action Required</option>
                            <option value="Approved" <?php echo $application['status'] === 'Approved' ? 'selected' : ''; ?>>Approved</option>
                            <option value="Declined" <?php echo $application['status'] === 'Declined' ? 'selected' : ''; ?>>Declined</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </form>
            </div>
            
            <div class="view-section">
                <h2>Admin Notes</h2>
                <form method="POST" class="notes-form">
                    <input type="hidden" name="action" value="update_notes">
                    <div class="form-group">
                        <textarea name="notes" rows="5" class="form-control" placeholder="Add internal notes about this application..."><?php echo htmlspecialchars($application['admin_notes'] ?? ''); ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Save Notes</button>
                </form>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/../../templates/admin_footer.php'; ?>