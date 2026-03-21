<?php
/**
 * Activity Controller
 */

class ActivityController {
    private $activityModel;
    private $customerModel;
    private $contactModel;
    
    public function __construct() {
        require_once __DIR__ . '/../models/Activity.php';
        require_once __DIR__ . '/../models/Customer.php';
        require_once __DIR__ . '/../models/Contact.php';
        require_once __DIR__ . '/../models/Attachment.php';
        
        $this->activityModel = new Activity();
        $this->customerModel = new Customer();
        $this->contactModel = new Contact();
        $this->attachmentModel = new Attachment();
    }
    
    /**
     * List activities for a customer
     */
    public function list($customerId = null) {
        if (!$customerId) {
            $customerId = $_GET['customer_id'] ?? null;
        }
        
        if (!$customerId) {
            setFlashMessage('error', 'Kunde ID mangler');
            redirect(APP_URL . '/customers');
        }
        
        $customer = $this->customerModel->getById($customerId);
        $activities = $this->activityModel->getByCustomer($customerId);
        
        $pageTitle = 'Aktiviteter - ' . $customer['company_name'];
        require __DIR__ . '/../views/activities/list.php';
    }
    
    /**
     * View activity
     */
    public function view($id) {
        if (!$id) {
            setFlashMessage('error', 'Aktivitet ID mangler');
            redirect(APP_URL . '/customers');
        }
        
        $activity = $this->activityModel->getById($id);
        
        if (!$activity) {
            setFlashMessage('error', 'Aktivitet ikke funnet');
            redirect(APP_URL . '/customers');
        }
        
        // Get attachments
        $attachments = $this->attachmentModel->getByActivity($id);
        
        $pageTitle = $activity['title'];
        require __DIR__ . '/../views/activities/view.php';
    }
    
    /**
     * Create activity
     */
    public function create() {
        $customerId = $_GET['customer_id'] ?? $_POST['customer_id'] ?? null;
        
        if (!$customerId) {
            setFlashMessage('error', 'Kunde ID mangler');
            redirect(APP_URL . '/customers');
        }
        
        $customer = $this->customerModel->getById($customerId);
        $contacts = $this->contactModel->getByCustomer($customerId);
        
        if (!$customer) {
            setFlashMessage('error', 'Kunde ikke funnet');
            redirect(APP_URL . '/customers');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                validateCsrfToken($_POST['csrf_token'] ?? '');
                
                $data = [
                    'customer_id' => $customerId,
                    'contact_id' => !empty($_POST['contact_id']) ? $_POST['contact_id'] : null,
                    'activity_type' => sanitizeInput($_POST['activity_type'] ?? 'note'),
                    'title' => sanitizeInput($_POST['title'] ?? ''),
                    'description' => sanitizeInput($_POST['description'] ?? ''),
                    'activity_date' => sanitizeInput($_POST['activity_date'] ?? date('Y-m-d H:i:s')),
                    'created_by' => 'System'
                ];
                
                if (empty($data['title'])) {
                    throw new Exception('Tittel er påkrevd');
                }
                
                $activityId = $this->activityModel->create($data);
                
                // Handle file uploads
                if (!empty($_FILES['attachments']['name'][0])) {
                    $fileCount = count($_FILES['attachments']['name']);
                    
                    for ($i = 0; $i < $fileCount; $i++) {
                        $file = [
                            'name' => $_FILES['attachments']['name'][$i],
                            'type' => $_FILES['attachments']['type'][$i],
                            'tmp_name' => $_FILES['attachments']['tmp_name'][$i],
                            'error' => $_FILES['attachments']['error'][$i],
                            'size' => $_FILES['attachments']['size'][$i]
                        ];
                        
                        // Skip if no file
                        if ($file['error'] === UPLOAD_ERR_NO_FILE) {
                            continue;
                        }
                        
                        // Validate file
                        $errors = $this->attachmentModel->validateFile($file);
                        if (!empty($errors)) {
                            setFlashMessage('warning', 'Fil "' . $file['name'] . '": ' . implode(', ', $errors));
                            continue;
                        }
                        
                        // Upload file
                        try {
                            $this->attachmentModel->uploadFile($file, $activityId);
                        } catch (Exception $e) {
                            setFlashMessage('warning', 'Kunne ikke laste opp "' . $file['name'] . '": ' . $e->getMessage());
                        }
                    }
                }
                
                setFlashMessage('success', 'Aktivitet registrert');
                redirect(APP_URL . '/customers/view/' . $customerId);
                
            } catch (Exception $e) {
                setFlashMessage('error', $e->getMessage());
            }
        }
        
        $pageTitle = 'Ny aktivitet';
        require __DIR__ . '/../views/activities/form.php';
    }
    
    /**
     * Edit activity
     */
    public function edit($id) {
        if (!$id) {
            setFlashMessage('error', 'Aktivitet ID mangler');
            redirect(APP_URL . '/customers');
        }
        
        $activity = $this->activityModel->getById($id);
        
        if (!$activity) {
            setFlashMessage('error', 'Aktivitet ikke funnet');
            redirect(APP_URL . '/customers');
        }
        
        $customer = $this->customerModel->getById($activity['customer_id']);
        $contacts = $this->contactModel->getByCustomer($activity['customer_id']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                validateCsrfToken($_POST['csrf_token'] ?? '');
                
                $data = [
                    'activity_type' => sanitizeInput($_POST['activity_type'] ?? 'note'),
                    'title' => sanitizeInput($_POST['title'] ?? ''),
                    'description' => sanitizeInput($_POST['description'] ?? ''),
                    'activity_date' => sanitizeInput($_POST['activity_date'] ?? date('Y-m-d H:i:s'))
                ];
                
                if (empty($data['title'])) {
                    throw new Exception('Tittel er påkrevd');
                }
                
                $this->activityModel->update($id, $data);
                
                setFlashMessage('success', 'Aktivitet oppdatert');
                redirect(APP_URL . '/customers/view/' . $activity['customer_id']);
                
            } catch (Exception $e) {
                setFlashMessage('error', $e->getMessage());
            }
        }
        
        $pageTitle = 'Rediger aktivitet';
        $formData = $activity;
        require __DIR__ . '/../views/activities/form.php';
    }
    
    /**
     * Delete activity
     */
    public function delete($id) {
        if (!$id) {
            setFlashMessage('error', 'Aktivitet ID mangler');
            redirect(APP_URL . '/customers');
        }
        
        $activity = $this->activityModel->getById($id);
        
        if (!$activity) {
            setFlashMessage('error', 'Aktivitet ikke funnet');
            redirect(APP_URL . '/customers');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                validateCsrfToken($_POST['csrf_token'] ?? '');
                
                $this->activityModel->delete($id);
                setFlashMessage('success', 'Aktivitet slettet');
                
            } catch (Exception $e) {
                setFlashMessage('error', 'Kunne ikke slette: ' . $e->getMessage());
            }
        }
        
        redirect(APP_URL . '/customers/view/' . $activity['customer_id']);
    }
    
    /**
     * Download attachment
     */
    public function downloadAttachment($id) {
        if (!$id) {
            setFlashMessage('error', 'Vedlegg ID mangler');
            redirect(APP_URL . '/customers');
        }
        
        $attachment = $this->attachmentModel->getById($id);
        
        if (!$attachment) {
            setFlashMessage('error', 'Vedlegg ikke funnet');
            redirect(APP_URL . '/customers');
        }
        
        $filePath = UPLOAD_DIR . $attachment['file_path'];
        
        if (!file_exists($filePath)) {
            setFlashMessage('error', 'Filen finnes ikke');
            redirect(APP_URL . '/activities/view/' . $attachment['activity_id']);
        }
        
        // Set headers for download
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . $attachment['original_name'] . '"');
        header('Content-Length: ' . $attachment['file_size']);
        header('Cache-Control: no-cache, must-revalidate');
        
        readfile($filePath);
        exit;
    }
    
    /**
     * Delete attachment
     */
    public function deleteAttachment($id) {
        if (!$id) {
            setFlashMessage('error', 'Vedlegg ID mangler');
            redirect(APP_URL . '/customers');
        }
        
        $attachment = $this->attachmentModel->getById($id);
        
        if (!$attachment) {
            setFlashMessage('error', 'Vedlegg ikke funnet');
            redirect(APP_URL . '/customers');
        }
        
        $activityId = $attachment['activity_id'];
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                validateCsrfToken($_POST['csrf_token'] ?? '');
                
                $this->attachmentModel->delete($id);
                setFlashMessage('success', 'Vedlegg slettet');
                
            } catch (Exception $e) {
                setFlashMessage('error', 'Kunne ikke slette: ' . $e->getMessage());
            }
        }
        
        redirect(APP_URL . '/activities/view/' . $activityId);
    }
}

function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}
