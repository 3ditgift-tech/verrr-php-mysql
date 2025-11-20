-- VERCUL Business Onboarding Database Schema
-- MySQL Database Schema

CREATE DATABASE IF NOT EXISTS vercul_business;
USE vercul_business;

-- Applications table (lightweight metadata)
CREATE TABLE applications (
    id VARCHAR(50) PRIMARY KEY,
    status ENUM('Submitted', 'In Review', 'Action Required', 'Approved', 'Declined') NOT NULL DEFAULT 'Submitted',
    submitted_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
    company_name VARCHAR(255) NOT NULL,
    registration_number VARCHAR(100) NOT NULL,
    country VARCHAR(100) NOT NULL,
    business_address TEXT NOT NULL,
    city VARCHAR(100) NOT NULL,
    postal_code VARCHAR(20) NOT NULL,
    applicant_name VARCHAR(255) NOT NULL,
    applicant_role VARCHAR(100) NOT NULL,
    applicant_dob DATE NOT NULL,
    applicant_email VARCHAR(255) NOT NULL,
    applicant_phone VARCHAR(50) NOT NULL,
    admin_notes TEXT,
    action_required_message TEXT,
    action_required_link VARCHAR(500),
    action_required_image_url VARCHAR(500),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_status (status),
    INDEX idx_email (applicant_email),
    INDEX idx_submitted_at (submitted_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Application files table (heavy file data)
CREATE TABLE application_files (
    application_id VARCHAR(50) PRIMARY KEY,
    uploaded_documents JSON,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Settings table
CREATE TABLE settings (
    setting_key VARCHAR(50) PRIMARY KEY,
    setting_value LONGTEXT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Admin settings
CREATE TABLE admin_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    password_hash VARCHAR(255) NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Email templates
CREATE TABLE email_templates (
    id VARCHAR(100) PRIMARY KEY,
    name VARCHAR(255) NOT NULL,
    subject TEXT NOT NULL,
    body LONGTEXT NOT NULL,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SMTP settings
CREATE TABLE smtp_settings (
    id INT PRIMARY KEY AUTO_INCREMENT,
    host VARCHAR(255) NOT NULL,
    port INT NOT NULL DEFAULT 587,
    username VARCHAR(255) NOT NULL,
    password VARCHAR(255),
    security ENUM('none', 'ssl', 'starttls') DEFAULT 'starttls',
    from_name VARCHAR(255),
    from_address VARCHAR(255),
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insert default admin password (1234, hashed)
INSERT INTO admin_settings (password_hash) 
VALUES ('$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi');

-- Insert default email templates
INSERT INTO email_templates (id, name, subject, body) VALUES
('application-submitted', 
 'Application Submitted (to user)',
 'Your VERCUL Business Application is Submitted (ID: {{applicationId}})',
 'Hi {{applicantName}},\n\nThank you for submitting your application for a VERCUL Business account. We\'ve received your details and your application is now in the queue for review.\n\nYour Application ID is: {{applicationId}}\n\nYou can track the status of your application here: {{trackingLink}}\n\nWe\'ll notify you of any updates.\n\nBest regards,\nThe VERCUL Onboarding Team'),

('admin-new-application',
 'New Application Notification (to admin)',
 'New Business Application Received: {{companyName}} ({{applicationId}})',
 'A new application has been submitted by {{companyName}}.\n\nApplicant: {{applicantName}}\nEmail: {{applicantEmail}}\nCountry: {{country}}\n\nPlease review it in the admin dashboard.'),

('application-in-review',
 'Application In Review (to user)',
 'Your Application is Under Review (ID: {{applicationId}})',
 'Hi {{applicantName}},\n\nGood news! Your application (ID: {{applicationId}}) has moved to the \'In Review\' stage. Our team is now carefully reviewing the documents and information you provided.\n\nThis process typically takes 1-2 business days. We appreciate your patience.\n\nYou can track the status here: {{trackingLink}}\n\nBest regards,\nThe VERCUL Onboarding Team'),

('application-action-required',
 'Action Required (to user)',
 'Action Required for Your Application (ID: {{applicationId}})',
 'Hi {{applicantName}},\n\nWe need some additional information to proceed with your application (ID: {{applicationId}}).\n\nPlease check your application dashboard for specific details on what is required from you.\n\nTracking Link: {{trackingLink}}\n\nPromptly providing the required information will help us speed up the process.\n\nBest regards,\nThe VERCUL Onboarding Team'),

('application-approved',
 'Application Approved (to user)',
 'Congratulations! Your VERCUL Business Account is Approved!',
 'Hi {{applicantName}},\n\nWe are thrilled to inform you that your VERCUL Business account application (ID: {{applicationId}}) has been approved!\n\nYour account is now being set up, and you will receive a separate email with your account details and instructions on how to get started. The €500 bonus will be credited to your account according to the offer terms.\n\nWelcome to the future of business banking!\n\nBest regards,\nThe VERCUL Onboarding Team'),

('application-declined',
 'Application Declined (to user)',
 'Update on Your VERCUL Business Application (ID: {{applicationId}})',
 'Hi {{applicantName}},\n\nFollowing a review of your application (ID: {{applicationId}}), we regret to inform you that we are unable to offer you a VERCUL Business account at this time.\n\nWe understand this is not the news you were hoping for. If you believe there has been a mistake or have questions, please contact our support team.\n\nWe wish you the best in your business endeavors.\n\nBest regards,\nThe VERCUL Onboarding Team');

-- Insert default SMTP settings
INSERT INTO smtp_settings (host, port, username, security, from_name, from_address)
VALUES ('', 587, '', 'starttls', 'VERCUL Support', 'no-reply@vercul.com');

-- Insert default frontend settings
INSERT INTO settings (setting_key, setting_value) VALUES
('frontend_settings', '{"logoUrl":"","faviconUrl":"","seoTitle":"VERCUL | €500 Bonus","seoMetaDescription":"An expertly designed landing page to onboard European businesses to VERCUL Business, highlighting a special €500 bonus offer.","copyrightText":"© {YEAR} VERCUL HOLDINGS LTD. All rights reserved.","contactEmail":"contact@vercul.com","contactPhone":"+44 20 8275 6432","contactAddress":"VER-CUL HOLDINGS LTD\\n41 Somerset Gardens, Creighton Road\\nLondon, United Kingdom N17 8JX","primaryColor":"#2563eb","secondaryColor":"#1d4ed8","baseFontSize":16,"fontFamily":"Inter","borderRadius":"0.75rem","enableGradients":true,"showFeaturesSection":true,"showWhyUsSection":true,"showProcessSection":true,"showCountriesSection":true,"showTestimonialsSection":true,"showTrustpilotSection":true,"showFaqSection":true,"showSecuritySection":true}');