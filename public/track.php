<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';

$pageTitle = 'Track Your Application';
$application = null;
$error = '';

if (isset($_GET['id']) && !empty($_GET['id'])) {
    try {
        $db = Database::getInstance()->getConnection();
        $stmt = $db->prepare("SELECT * FROM applications WHERE id = ?");
        $stmt->execute([$_GET['id']]);
        $application = $stmt->fetch();
        
        if (!$application) {
            $error = 'Application not found. Please check your Application ID.';
        }
    } catch (Exception $e) {
        $error = 'Error retrieving application. Please try again.';
        error_log('Track error: ' . $e->getMessage());
    }
}

include __DIR__ . '/../templates/header.php';
?>

<div class="container">
    <div class="track-wrapper">
        <h1>Track Your Application</h1>
        
        <?php if (!$application): ?>
            <form method="GET" action="" class="track-form">
                <div class="form-group">
                    <label for="id">Enter your Application ID</label>
                    <input type="text" id="id" name="id" 
                           value="<?php echo htmlspecialchars($_GET['id'] ?? ''); ?>" 
                           placeholder="VC-BIZ-XXXXXX" required>
                </div>
                <button type="submit" class="btn btn-primary">Track Application</button>
            </form>
            
            <?php if ($error): ?>
                <div class="error-message"><?php echo htmlspecialchars($error); ?></div>
            <?php endif; ?>
            
            <div class="help-text">
                <p>Don't have your Application ID? Check your confirmation email.</p>
            </div>
        <?php else: ?>
            <div class="application-details">
                <div class="status-badge status-<?php echo strtolower(str_replace(' ', '-', $application['status'])); ?>">
                    <?php echo htmlspecialchars($application['status']); ?>
                </div>
                
                <div class="detail-section">
                    <h2>Application Information</h2>
                    <div class="detail-grid">
                        <div class="detail-item">
                            <span class="label">Application ID:</span>
                            <span class="value"><?php echo htmlspecialchars($application['id']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Company Name:</span>
                            <span class="value"><?php echo htmlspecialchars($application['company_name']); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Submitted On:</span>
                            <span class="value"><?php echo date('F j, Y, g:i a', strtotime($application['submitted_at'])); ?></span>
                        </div>
                        <div class="detail-item">
                            <span class="label">Applicant:</span>
                            <span class="value"><?php echo htmlspecialchars($application['applicant_name']); ?></span>
                        </div>
                    </div>
                </div>
                
                <?php if ($application['status'] === 'Action Required' && !empty($application['action_required_message'])): ?>
                    <div class="action-required-section">
                        <h2>⚠️ Action Required</h2>
                        <p><?php echo nl2br(htmlspecialchars($application['action_required_message'])); ?></p>
                        <?php if (!empty($application['action_required_link'])): ?>
                            <a href="<?php echo htmlspecialchars($application['action_required_link']); ?>" 
                               class="btn btn-primary" target="_blank">Take Action</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
                
                <div class="status-timeline">
                    <h2>Application Progress</h2>
                    <div class="timeline">
                        <div class="timeline-item completed">
                            <div class="timeline-marker">✓</div>
                            <div class="timeline-content">
                                <h3>Submitted</h3>
                                <p>Application received and logged in our system</p>
                            </div>
                        </div>
                        
                        <div class="timeline-item <?php echo in_array($application['status'], ['In Review', 'Action Required', 'Approved', 'Declined']) ? 'completed' : ''; ?>">
                            <div class="timeline-marker"><?php echo in_array($application['status'], ['In Review', 'Action Required', 'Approved', 'Declined']) ? '✓' : ''; ?></div>
                            <div class="timeline-content">
                                <h3>In Review</h3>
                                <p>Our team is reviewing your application and documents</p>
                            </div>
                        </div>
                        
                        <?php if ($application['status'] === 'Action Required'): ?>
                        <div class="timeline-item active">
                            <div class="timeline-marker">!</div>
                            <div class="timeline-content">
                                <h3>Action Required</h3>
                                <p>We need additional information from you</p>
                            </div>
                        </div>
                        <?php endif; ?>
                        
                        <div class="timeline-item <?php echo $application['status'] === 'Approved' ? 'completed' : ''; ?>">
                            <div class="timeline-marker"><?php echo $application['status'] === 'Approved' ? '✓' : ''; ?></div>
                            <div class="timeline-content">
                                <h3>Approved</h3>
                                <p>Account setup and welcome email sent</p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <?php if ($application['status'] === 'Declined'): ?>
                    <div class="declined-message">
                        <h2>Application Status: Declined</h2>
                        <p>Unfortunately, we are unable to approve your application at this time.</p>
                        <p>If you have questions, please contact our support team.</p>
                    </div>
                <?php endif; ?>
                
                <div class="actions">
                    <a href="track.php" class="btn btn-secondary">Track Another Application</a>
                    <a href="index.php" class="btn btn-secondary">Back to Home</a>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>