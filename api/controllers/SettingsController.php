<?php
/**
 * Settings Controller
 * Handles frontend settings, email templates, and SMTP configuration
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../utils/Response.php';
require_once __DIR__ . '/../../utils/EmailService.php';

class SettingsController {
    
    private $db;
    private $emailService;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->emailService = new EmailService();
    }
    
    /**
     * Get frontend settings
     */
    public function getFrontendSettings() {
        try {
            $stmt = $this->db->prepare("
                SELECT setting_value FROM settings WHERE setting_key = 'frontend_settings'
            ");
            $stmt->execute();
            $result = $stmt->fetch();
            
            if ($result) {
                $settings = json_decode($result['setting_value'], true);
                Response::success($settings);
            } else {
                // Return default settings
                $defaultSettings = $this->getDefaultFrontendSettings();
                Response::success($defaultSettings);
            }
            
        } catch (Exception $e) {
            error_log("Get frontend settings error: " . $e->getMessage());
            Response::serverError('Failed to fetch settings');
        }
    }
    
    /**
     * Save frontend settings
     */
    public function saveFrontendSettings() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!$data) {
            Response::error('Invalid settings data');
        }
        
        try {
            $stmt = $this->db->prepare("
                INSERT INTO settings (setting_key, setting_value) 
                VALUES ('frontend_settings', ?)
                ON DUPLICATE KEY UPDATE setting_value = ?
            ");
            
            $jsonSettings = json_encode($data);
            $stmt->execute([$jsonSettings, $jsonSettings]);
            
            Response::success($data, 'Settings saved successfully');
            
        } catch (Exception $e) {
            error_log("Save frontend settings error: " . $e->getMessage());
            Response::serverError('Failed to save settings');
        }
    }
    
    /**
     * Get email templates
     */
    public function getEmailTemplates() {
        try {
            $stmt = $this->db->query("SELECT * FROM email_templates");
            $templates = $stmt->fetchAll();
            
            Response::success($templates);
            
        } catch (Exception $e) {
            error_log("Get email templates error: " . $e->getMessage());
            Response::serverError('Failed to fetch email templates');
        }
    }
    
    /**
     * Update email template
     */
    public function updateEmailTemplate() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['id']) || !isset($data['subject']) || !isset($data['body'])) {
            Response::error('Missing required fields: id, subject, body');
        }
        
        try {
            $stmt = $this->db->prepare("
                UPDATE email_templates 
                SET subject = ?, body = ?, name = ?
                WHERE id = ?
            ");
            
            $stmt->execute([
                $data['subject'],
                $data['body'],
                $data['name'] ?? '',
                $data['id']
            ]);
            
            Response::success($data, 'Email template updated successfully');
            
        } catch (Exception $e) {
            error_log("Update email template error: " . $e->getMessage());
            Response::serverError('Failed to update email template');
        }
    }
    
    /**
     * Get SMTP settings
     */
    public function getSmtpSettings() {
        try {
            $stmt = $this->db->query("SELECT * FROM smtp_settings LIMIT 1");
            $settings = $stmt->fetch();
            
            if ($settings) {
                // Remove sensitive password from response
                unset($settings['id']);
                $settings['password'] = $settings['password'] ? '********' : '';
                Response::success($settings);
            } else {
                Response::success($this->getDefaultSmtpSettings());
            }
            
        } catch (Exception $e) {
            error_log("Get SMTP settings error: " . $e->getMessage());
            Response::serverError('Failed to fetch SMTP settings');
        }
    }
    
    /**
     * Save SMTP settings
     */
    public function saveSmtpSettings() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['host']) || !isset($data['port']) || !isset($data['username'])) {
            Response::error('Missing required fields: host, port, username');
        }
        
        try {
            // Check if settings exist
            $stmt = $this->db->query("SELECT id FROM smtp_settings LIMIT 1");
            $exists = $stmt->fetch();
            
            if ($exists) {
                // Update existing
                $sql = "UPDATE smtp_settings SET host = ?, port = ?, username = ?, security = ?, from_name = ?, from_address = ?";
                $params = [
                    $data['host'],
                    $data['port'],
                    $data['username'],
                    $data['security'] ?? 'starttls',
                    $data['fromName'] ?? '',
                    $data['fromAddress'] ?? ''
                ];
                
                // Only update password if provided and not masked
                if (isset($data['password']) && $data['password'] !== '********' && !empty($data['password'])) {
                    $sql .= ", password = ?";
                    $params[] = $data['password'];
                }
                
                $stmt = $this->db->prepare($sql);
                $stmt->execute($params);
            } else {
                // Insert new
                $stmt = $this->db->prepare("
                    INSERT INTO smtp_settings (host, port, username, password, security, from_name, from_address)
                    VALUES (?, ?, ?, ?, ?, ?, ?)
                ");
                $stmt->execute([
                    $data['host'],
                    $data['port'],
                    $data['username'],
                    $data['password'] ?? '',
                    $data['security'] ?? 'starttls',
                    $data['fromName'] ?? '',
                    $data['fromAddress'] ?? ''
                ]);
            }
            
            Response::success($data, 'SMTP settings saved successfully');
            
        } catch (Exception $e) {
            error_log("Save SMTP settings error: " . $e->getMessage());
            Response::serverError('Failed to save SMTP settings');
        }
    }
    
    /**
     * Send test email
     */
    public function sendTestEmail() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        $testEmail = $data['email'] ?? ADMIN_EMAIL;
        
        try {
            $result = $this->emailService->sendTest($testEmail);
            
            if ($result) {
                Response::success(
                    ['success' => true], 
                    "Test email sent successfully to {$testEmail}"
                );
            } else {
                Response::error('Failed to send test email. Check SMTP configuration.');
            }
            
        } catch (Exception $e) {
            error_log("Send test email error: " . $e->getMessage());
            Response::error('Failed to send test email: ' . $e->getMessage());
        }
    }
    
    /**
     * Helper: Get default frontend settings
     */
    private function getDefaultFrontendSettings() {
        return [
            'logoUrl' => '',
            'faviconUrl' => '',
            'seoTitle' => 'VERCUL | €500 Bonus',
            'seoMetaDescription' => 'An expertly designed landing page to onboard European businesses to VERCUL Business',
            'copyrightText' => '© {YEAR} VERCUL HOLDINGS LTD. All rights reserved.',
            'contactEmail' => 'contact@vercul.com',
            'contactPhone' => '+44 20 8275 6432',
            'contactAddress' => 'VER-CUL HOLDINGS LTD\n41 Somerset Gardens, Creighton Road\nLondon, United Kingdom N17 8JX',
            'primaryColor' => '#2563eb',
            'secondaryColor' => '#1d4ed8',
            'baseFontSize' => 16,
            'fontFamily' => 'Inter',
            'borderRadius' => '0.75rem',
            'enableGradients' => true,
            'showFeaturesSection' => true,
            'showWhyUsSection' => true,
            'showProcessSection' => true,
            'showCountriesSection' => true,
            'showTestimonialsSection' => true,
            'showTrustpilotSection' => true,
            'showFaqSection' => true,
            'showSecuritySection' => true
        ];
    }
    
    /**
     * Helper: Get default SMTP settings
     */
    private function getDefaultSmtpSettings() {
        return [
            'host' => '',
            'port' => 587,
            'username' => '',
            'password' => '',
            'security' => 'starttls',
            'fromName' => 'VERCUL Support',
            'fromAddress' => 'no-reply@vercul.com'
        ];
    }
}
