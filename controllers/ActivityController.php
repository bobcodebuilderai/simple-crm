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
        
        $this->activityModel = new Activity();
        $this->customerModel = new Customer();
        $this->contactModel = new Contact();
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
}

function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}
