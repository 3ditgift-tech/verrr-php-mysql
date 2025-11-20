<?php
/**
 * Application Controller
 * Handles all application-related operations
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../utils/Response.php';
require_once __DIR__ . '/../../utils/Validator.php';
require_once __DIR__ . '/../../utils/IdGenerator.php';
require_once __DIR__ . '/../../utils/EmailService.php';

class ApplicationController {
    
    private $db;
    private $validator;
    private $emailService;
    
    public function __construct() {
        $this->db = Database::getInstance()->getConnection();
        $this->validator = new Validator();
        $this->emailService = new EmailService();
    }
    
    /**
     * Submit a new application
     */
    public function submit() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        // Validate required fields
        $this->validateApplicationData($data);
        
        if ($this->validator->hasErrors()) {
            Response::validationError($this->validator->getErrors());
        }
        
        try {
            $this->db->beginTransaction();
            
            // Generate unique ID
            $applicationId = IdGenerator::generateApplicationId();
            
            // Insert application
            $stmt = $this->db->prepare("
                INSERT INTO applications (
                    id, company_name, registration_number, country, 
                    business_address, city, postal_code, applicant_name, 
                    applicant_role, applicant_dob, applicant_email, applicant_phone,
                    status, submitted_at
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'Submitted', NOW())
            ");
            
            $stmt->execute([
                $applicationId,
                $data['companyName'],
                $data['registrationNumber'],
                $data['country'],
                $data['businessAddress'],
                $data['city'],
                $data['postalCode'],
                $data['applicantName'],
                $data['applicantRole'],
                $data['applicantDob'],
                $data['applicantEmail'],
                $data['applicantPhone']
            ]);
            
            // Handle uploaded documents separately
            if (isset($data['uploadedDocuments']) && !empty($data['uploadedDocuments'])) {
                $stmt = $this->db->prepare("
                    INSERT INTO application_files (application_id, uploaded_documents)
                    VALUES (?, ?)
                ");
                $stmt->execute([
                    $applicationId,
                    json_encode($data['uploadedDocuments'])
                ]);
            }
            
            $this->db->commit();
            
            // Get the complete application
            $application = $this->getById($applicationId, false);
            
            // Send notification emails
            $this->emailService->sendNotification('application-submitted', $application, $application['applicant_email']);
            $this->emailService->sendNotification('admin-new-application', $application, ADMIN_EMAIL);
            
            Response::success($application, 'Application submitted successfully', 201);
            
        } catch (Exception $e) {
            $this->db->rollBack();
            error_log("Application submission error: " . $e->getMessage());
            Response::serverError('Failed to submit application');
        }
    }
    
    /**
     * Get all applications (admin)
     */
    public function getAll() {
        try {
            $stmt = $this->db->prepare("
                SELECT 
                    id, status, submitted_at, company_name, registration_number,
                    country, business_address, city, postal_code, applicant_name,
                    applicant_role, applicant_dob, applicant_email, applicant_phone,
                    admin_notes, action_required_message, action_required_link,
                    action_required_image_url
                FROM applications 
                ORDER BY submitted_at DESC
            ");
            $stmt->execute();
            $applications = $stmt->fetchAll();
            
            // Format the response
            $formatted = array_map(function($app) {
                return $this->formatApplication($app);
            }, $applications);
            
            Response::success($formatted);
            
        } catch (Exception $e) {
            error_log("Get applications error: " . $e->getMessage());
            Response::serverError('Failed to fetch applications');
        }
    }
    
    /**
     * Get application by ID
     */
    public function get($id) {
        $application = $this->getById($id);
        
        if (!$application) {
            Response::notFound('Application not found');
        }
        
        Response::success($application);
    }
    
    /**
     * Update application status (admin)
     */
    public function updateStatus() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['id']) || !isset($data['status'])) {
            Response::error('Missing required fields: id and status');
        }
        
        $validStatuses = ['Submitted', 'In Review', 'Action Required', 'Approved', 'Declined'];
        if (!in_array($data['status'], $validStatuses)) {
            Response::error('Invalid status value');
        }
        
        try {
            $sql = "UPDATE applications SET status = ?";
            $params = [$data['status']];
            
            // Handle Action Required details
            if ($data['status'] === 'Action Required' && isset($data['details'])) {
                $sql .= ", action_required_message = ?, action_required_link = ?, action_required_image_url = ?";
                $params[] = $data['details']['message'] ?? null;
                $params[] = $data['details']['link'] ?? null;
                $params[] = $data['details']['imageUrl'] ?? null;
            } else {
                $sql .= ", action_required_message = NULL, action_required_link = NULL, action_required_image_url = NULL";
            }
            
            $sql .= " WHERE id = ?";
            $params[] = $data['id'];
            
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            
            // Get updated application
            $application = $this->getById($data['id'], false);
            
            // Send notification email based on status
            $statusTemplateMap = [
                'In Review' => 'application-in-review',
                'Action Required' => 'application-action-required',
                'Approved' => 'application-approved',
                'Declined' => 'application-declined'
            ];
            
            if (isset($statusTemplateMap[$data['status']])) {
                $this->emailService->sendNotification(
                    $statusTemplateMap[$data['status']],
                    $application,
                    $application['applicant_email']
                );
            }
            
            Response::success($application, 'Application status updated successfully');
            
        } catch (Exception $e) {
            error_log("Update status error: " . $e->getMessage());
            Response::serverError('Failed to update application status');
        }
    }
    
    /**
     * Update application notes (admin)
     */
    public function updateNotes() {
        $data = json_decode(file_get_contents('php://input'), true);
        
        if (!isset($data['id']) || !isset($data['notes'])) {
            Response::error('Missing required fields: id and notes');
        }
        
        try {
            $stmt = $this->db->prepare("
                UPDATE applications SET admin_notes = ? WHERE id = ?
            ");
            $stmt->execute([$data['notes'], $data['id']]);
            
            $application = $this->getById($data['id'], false);
            Response::success($application, 'Notes updated successfully');
            
        } catch (Exception $e) {
            error_log("Update notes error: " . $e->getMessage());
            Response::serverError('Failed to update notes');
        }
    }
    
    /**
     * Get dashboard statistics (admin)
     */
    public function getStats() {
        try {
            $stmt = $this->db->query("
                SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN status = 'Submitted' THEN 1 ELSE 0 END) as submitted,
                    SUM(CASE WHEN status = 'In Review' THEN 1 ELSE 0 END) as inReview,
                    SUM(CASE WHEN status = 'Action Required' THEN 1 ELSE 0 END) as actionRequired,
                    SUM(CASE WHEN status = 'Approved' THEN 1 ELSE 0 END) as approved
                FROM applications
            ");
            
            $stats = $stmt->fetch();
            Response::success($stats);
            
        } catch (Exception $e) {
            error_log("Get stats error: " . $e->getMessage());
            Response::serverError('Failed to fetch statistics');
        }
    }
    
    /**
     * Get count of pending applications (for notifications)
     */
    public function getPendingCount() {
        try {
            $stmt = $this->db->query("
                SELECT COUNT(*) as count FROM applications WHERE status = 'Submitted'
            ");
            
            $result = $stmt->fetch();
            Response::success(['count' => (int)$result['count']]);
            
        } catch (Exception $e) {
            error_log("Get pending count error: " . $e->getMessage());
            Response::serverError('Failed to fetch pending count');
        }
    }
    
    /**
     * Helper: Get application by ID
     */
    private function getById($id, $includeFiles = true) {
        try {
            $stmt = $this->db->prepare("
                SELECT * FROM applications WHERE id = ?
            ");
            $stmt->execute([$id]);
            $application = $stmt->fetch();
            
            if (!$application) {
                return null;
            }
            
            // Get uploaded documents if needed
            if ($includeFiles) {
                $stmt = $this->db->prepare("
                    SELECT uploaded_documents FROM application_files WHERE application_id = ?
                ");
                $stmt->execute([$id]);
                $files = $stmt->fetch();
                
                if ($files && $files['uploaded_documents']) {
                    $application['uploaded_documents'] = json_decode($files['uploaded_documents'], true);
                }
            }
            
            return $this->formatApplication($application);
            
        } catch (Exception $e) {
            error_log("Get by ID error: " . $e->getMessage());
            return null;
        }
    }
    
    /**
     * Helper: Format application for API response
     */
    private function formatApplication($app) {
        $formatted = [
            'id' => $app['id'],
            'status' => $app['status'],
            'submittedAt' => $app['submitted_at'],
            'companyName' => $app['company_name'],
            'registrationNumber' => $app['registration_number'],
            'country' => $app['country'],
            'businessAddress' => $app['business_address'],
            'city' => $app['city'],
            'postalCode' => $app['postal_code'],
            'applicantName' => $app['applicant_name'],
            'applicantRole' => $app['applicant_role'],
            'applicantDob' => $app['applicant_dob'],
            'applicantEmail' => $app['applicant_email'],
            'applicantPhone' => $app['applicant_phone'],
            'adminNotes' => $app['admin_notes'] ?? ''
        ];
        
        // Add action required details if present
        if ($app['status'] === 'Action Required' && !empty($app['action_required_message'])) {
            $formatted['actionRequiredDetails'] = [
                'message' => $app['action_required_message'],
                'link' => $app['action_required_link'],
                'imageUrl' => $app['action_required_image_url']
            ];
        }
        
        // Add uploaded documents if present
        if (isset($app['uploaded_documents'])) {
            $formatted['uploadedDocuments'] = $app['uploaded_documents'];
        }
        
        return $formatted;
    }
    
    /**
     * Helper: Validate application data
     */
    private function validateApplicationData($data) {
        // Required fields
        $requiredFields = [
            'companyName', 'registrationNumber', 'country', 'businessAddress',
            'city', 'postalCode', 'applicantName', 'applicantRole',
            'applicantDob', 'applicantEmail', 'applicantPhone'
        ];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $this->validator->required('', $field);
            }
        }
        
        // Email validation
        if (isset($data['applicantEmail'])) {
            $this->validator->email($data['applicantEmail'], 'applicantEmail');
        }
        
        // Date validation
        if (isset($data['applicantDob'])) {
            $this->validator->date($data['applicantDob'], 'applicantDob');
        }
        
        // Phone validation
        if (isset($data['applicantPhone'])) {
            $this->validator->phone($data['applicantPhone'], 'applicantPhone');
        }
    }
}
