<?php
/**
 * Email Service using PHPMailer
 * Install via: composer require phpmailer/phpmailer
 */

require_once __DIR__ . '/../vendor/autoload.php';

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService {
    
    private $db;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
    }
    
    /**
     * Get SMTP settings from database
     */
    private function getSmtpSettings() {
        $stmt = $this->db->prepare("SELECT * FROM smtp_settings LIMIT 1");
        $stmt->execute();
        return $stmt->fetch();
    }
    
    /**
     * Get email template by ID
     */
    private function getTemplate($templateId) {
        $stmt = $this->db->prepare("SELECT * FROM email_templates WHERE id = ?");
        $stmt->execute([$templateId]);
        return $stmt->fetch();
    }
    
    /**
     * Replace placeholders in template
     */
    private function replacePlaceholders($text, $application) {
        $trackingLink = BASE_URL . "/#/track/" . $application['id'];
        
        $replacements = [
            '{{applicantName}}' => $application['applicant_name'],
            '{{applicationId}}' => $application['id'],
            '{{companyName}}' => $application['company_name'],
            '{{applicantEmail}}' => $application['applicant_email'],
            '{{country}}' => $application['country'],
            '{{trackingLink}}' => $trackingLink
        ];
        
        return str_replace(array_keys($replacements), array_values($replacements), $text);
    }
    
    /**
     * Send email using PHPMailer
     */
    public function send($to, $subject, $body, $toName = '') {
        try {
            $settings = $this->getSmtpSettings();
            
            $mail = new PHPMailer(true);
            
            // Server settings
            if (!empty($settings['host'])) {
                $mail->isSMTP();
                $mail->Host = $settings['host'];
                $mail->SMTPAuth = true;
                $mail->Username = $settings['username'];
                $mail->Password = $settings['password'];
                
                if ($settings['security'] === 'ssl') {
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
                } elseif ($settings['security'] === 'starttls') {
                    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
                }
                
                $mail->Port = $settings['port'];
            }
            
            // Recipients
            $fromAddress = $settings['from_address'] ?? 'no-reply@vercul.com';
            $fromName = $settings['from_name'] ?? 'VERCUL Support';
            
            $mail->setFrom($fromAddress, $fromName);
            $mail->addAddress($to, $toName);
            
            // Content
            $mail->isHTML(false);
            $mail->Subject = $subject;
            $mail->Body = $body;
            
            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Email sending failed: {$mail->ErrorInfo}");
            return false;
        }
    }
    
    /**
     * Send notification email using template
     */
    public function sendNotification($templateId, $application, $recipient) {
        $template = $this->getTemplate($templateId);
        
        if (!$template) {
            error_log("Email template not found: {$templateId}");
            return false;
        }
        
        $subject = $this->replacePlaceholders($template['subject'], $application);
        $body = $this->replacePlaceholders($template['body'], $application);
        
        return $this->send($recipient, $subject, $body, $application['applicant_name']);
    }
    
    /**
     * Send test email
     */
    public function sendTest($testEmail) {
        $subject = 'VERCUL Test Email';
        $body = 'This is a test email from VERCUL Business Onboarding system. If you received this, your SMTP configuration is working correctly!';
        
        return $this->send($testEmail, $subject, $body);
    }
}
