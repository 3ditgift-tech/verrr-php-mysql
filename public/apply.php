<?php
require_once __DIR__ . '/../includes/config.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../utils/Validator.php';
require_once __DIR__ . '/../utils/IdGenerator.php';
require_once __DIR__ . '/../utils/EmailService.php';

$pageTitle = 'Apply for Business Account';
$errors = [];
$success = false;
$applicationId = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $validator = new Validator();
    
    // Validate all fields
    $validator->required($_POST['companyName'] ?? '', 'companyName');
    $validator->required($_POST['registrationNumber'] ?? '', 'registrationNumber');
    $validator->required($_POST['country'] ?? '', 'country');
    $validator->required($_POST['businessAddress'] ?? '', 'businessAddress');
    $validator->required($_POST['city'] ?? '', 'city');
    $validator->required($_POST['postalCode'] ?? '', 'postalCode');
    $validator->required($_POST['applicantName'] ?? '', 'applicantName');
    $validator->required($_POST['applicantRole'] ?? '', 'applicantRole');
    $validator->date($_POST['applicantDob'] ?? '', 'applicantDob');
    $validator->email($_POST['applicantEmail'] ?? '', 'applicantEmail');
    $validator->phone($_POST['applicantPhone'] ?? '', 'applicantPhone');
    
    if ($validator->hasErrors()) {
        $errors = $validator->getErrors();
    } else {
        try {
            $db = Database::getInstance()->getConnection();
            $applicationId = IdGenerator::generateApplicationId();
            
            $stmt = $db->prepare("
                INSERT INTO applications (
                    id, company_name, registration_number, country, 
                    business_address, city, postal_code, applicant_name, 
                    applicant_role, applicant_dob, applicant_email, applicant_phone,
                    status, submitted_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Submitted', NOW())
            ");
            
            $stmt->execute([
                $applicationId,
                $_POST['companyName'],
                $_POST['registrationNumber'],
                $_POST['country'],
                $_POST['businessAddress'],
                $_POST['city'],
                $_POST['postalCode'],
                $_POST['applicantName'],
                $_POST['applicantRole'],
                $_POST['applicantDob'],
                $_POST['applicantEmail'],
                $_POST['applicantPhone']
            ]);
            
            // Send notification emails
            $emailService = new EmailService();
            $application = [
                'id' => $applicationId,
                'applicant_name' => $_POST['applicantName'],
                'applicant_email' => $_POST['applicantEmail'],
                'company_name' => $_POST['companyName'],
                'country' => $_POST['country']
            ];
            
            $emailService->sendNotification('application-submitted', $application, $_POST['applicantEmail']);
            $emailService->sendNotification('admin-new-application', $application, ADMIN_EMAIL);
            
            $success = true;
            
        } catch (Exception $e) {
            $errors['general'] = 'Failed to submit application. Please try again.';
            error_log('Application submission error: ' . $e->getMessage());
        }
    }
}

include __DIR__ . '/../templates/header.php';
?>

<div class="container">
    <div class="application-form-wrapper">
        <h1>Business Account Application</h1>
        
        <?php if ($success): ?>
            <div class="success-message">
                <h2>✓ Application Submitted Successfully!</h2>
                <p>Your application ID is: <strong><?php echo htmlspecialchars($applicationId); ?></strong></p>
                <p>We've sent a confirmation email to <?php echo htmlspecialchars($_POST['applicantEmail']); ?></p>
                <p>You can track your application status using the link below:</p>
                <a href="track.php?id=<?php echo htmlspecialchars($applicationId); ?>" class="btn btn-primary">Track Application</a>
            </div>
        <?php else: ?>
            <?php if (!empty($errors['general'])): ?>
                <div class="error-message">
                    <?php echo htmlspecialchars($errors['general']); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="" class="application-form">
                <h2>Company Information</h2>
                
                <div class="form-group">
                    <label for="companyName">Company Name *</label>
                    <input type="text" id="companyName" name="companyName" 
                           value="<?php echo htmlspecialchars($_POST['companyName'] ?? ''); ?>" required>
                    <?php if (isset($errors['companyName'])): ?>
                        <span class="error"><?php echo $errors['companyName']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="registrationNumber">Registration Number *</label>
                    <input type="text" id="registrationNumber" name="registrationNumber" 
                           value="<?php echo htmlspecialchars($_POST['registrationNumber'] ?? ''); ?>" required>
                    <?php if (isset($errors['registrationNumber'])): ?>
                        <span class="error"><?php echo $errors['registrationNumber']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="country">Country *</label>
                    <select id="country" name="country" required>
                        <option value="">Select Country</option>
                        <option value="United Kingdom" <?php echo ($_POST['country'] ?? '') === 'United Kingdom' ? 'selected' : ''; ?>>United Kingdom</option>
                        <option value="Ireland" <?php echo ($_POST['country'] ?? '') === 'Ireland' ? 'selected' : ''; ?>>Ireland</option>
                        <option value="France" <?php echo ($_POST['country'] ?? '') === 'France' ? 'selected' : ''; ?>>France</option>
                        <option value="Germany" <?php echo ($_POST['country'] ?? '') === 'Germany' ? 'selected' : ''; ?>>Germany</option>
                        <option value="Spain" <?php echo ($_POST['country'] ?? '') === 'Spain' ? 'selected' : ''; ?>>Spain</option>
                        <option value="Italy" <?php echo ($_POST['country'] ?? '') === 'Italy' ? 'selected' : ''; ?>>Italy</option>
                        <option value="Netherlands" <?php echo ($_POST['country'] ?? '') === 'Netherlands' ? 'selected' : ''; ?>>Netherlands</option>
                        <option value="Belgium" <?php echo ($_POST['country'] ?? '') === 'Belgium' ? 'selected' : ''; ?>>Belgium</option>
                    </select>
                    <?php if (isset($errors['country'])): ?>
                        <span class="error"><?php echo $errors['country']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="businessAddress">Business Address *</label>
                    <textarea id="businessAddress" name="businessAddress" required><?php echo htmlspecialchars($_POST['businessAddress'] ?? ''); ?></textarea>
                    <?php if (isset($errors['businessAddress'])): ?>
                        <span class="error"><?php echo $errors['businessAddress']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-row">
                    <div class="form-group">
                        <label for="city">City *</label>
                        <input type="text" id="city" name="city" 
                               value="<?php echo htmlspecialchars($_POST['city'] ?? ''); ?>" required>
                        <?php if (isset($errors['city'])): ?>
                            <span class="error"><?php echo $errors['city']; ?></span>
                        <?php endif; ?>
                    </div>
                    
                    <div class="form-group">
                        <label for="postalCode">Postal Code *</label>
                        <input type="text" id="postalCode" name="postalCode" 
                               value="<?php echo htmlspecialchars($_POST['postalCode'] ?? ''); ?>" required>
                        <?php if (isset($errors['postalCode'])): ?>
                            <span class="error"><?php echo $errors['postalCode']; ?></span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <h2>Applicant Information</h2>
                
                <div class="form-group">
                    <label for="applicantName">Full Name *</label>
                    <input type="text" id="applicantName" name="applicantName" 
                           value="<?php echo htmlspecialchars($_POST['applicantName'] ?? ''); ?>" required>
                    <?php if (isset($errors['applicantName'])): ?>
                        <span class="error"><?php echo $errors['applicantName']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="applicantRole">Role in Company *</label>
                    <input type="text" id="applicantRole" name="applicantRole" 
                           value="<?php echo htmlspecialchars($_POST['applicantRole'] ?? ''); ?>" 
                           placeholder="e.g., Director, CEO, Owner" required>
                    <?php if (isset($errors['applicantRole'])): ?>
                        <span class="error"><?php echo $errors['applicantRole']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="applicantDob">Date of Birth *</label>
                    <input type="date" id="applicantDob" name="applicantDob" 
                           value="<?php echo htmlspecialchars($_POST['applicantDob'] ?? ''); ?>" required>
                    <?php if (isset($errors['applicantDob'])): ?>
                        <span class="error"><?php echo $errors['applicantDob']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="applicantEmail">Email Address *</label>
                    <input type="email" id="applicantEmail" name="applicantEmail" 
                           value="<?php echo htmlspecialchars($_POST['applicantEmail'] ?? ''); ?>" required>
                    <?php if (isset($errors['applicantEmail'])): ?>
                        <span class="error"><?php echo $errors['applicantEmail']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="applicantPhone">Phone Number *</label>
                    <input type="tel" id="applicantPhone" name="applicantPhone" 
                           value="<?php echo htmlspecialchars($_POST['applicantPhone'] ?? ''); ?>" 
                           placeholder="+44 20 1234 5678" required>
                    <?php if (isset($errors['applicantPhone'])): ?>
                        <span class="error"><?php echo $errors['applicantPhone']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary btn-lg">Submit Application</button>
                    <a href="index.php" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../templates/footer.php'; ?>