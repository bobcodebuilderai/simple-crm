<?php
/**
 * Customer Controller
 */

class CustomerController {
    private $customerModel;
    private $contactModel;
    private $activityModel;
    private $dealModel;
    private $taskModel;
    
    public function __construct() {
        require_once __DIR__ . '/../models/Customer.php';
        require_once __DIR__ . '/../models/Contact.php';
        require_once __DIR__ . '/../models/Activity.php';
        require_once __DIR__ . '/../models/Deal.php';
        require_once __DIR__ . '/../models/Task.php';
        
        $this->customerModel = new Customer();
        $this->contactModel = new Contact();
        $this->activityModel = new Activity();
        $this->dealModel = new Deal();
        $this->taskModel = new Task();
    }
    
    /**
     * List all customers
     */
    public function list() {
        $search = $_GET['search'] ?? '';
        $status = $_GET['status'] ?? '';
        
        $customers = $this->customerModel->getAll($search, $status);
        
        $pageTitle = 'Kunder';
        require __DIR__ . '/../views/customers/list.php';
    }
    
    /**
     * View customer details
     */
    public function view($id) {
        if (!$id) {
            setFlashMessage('error', 'Kunde ID mangler');
            redirect(APP_URL . '/customers');
        }
        
        $customer = $this->customerModel->getById($id);
        
        if (!$customer) {
            setFlashMessage('error', 'Kunde ikke funnet');
            redirect(APP_URL . '/customers');
        }
        
        // Get related data
        $contacts = $this->contactModel->getByCustomer($id);
        $activities = $this->activityModel->getByCustomer($id, 10);
        $deals = $this->dealModel->getByCustomer($id);
        $tasks = $this->taskModel->getByCustomer($id, 'open');
        
        $pageTitle = $customer['company_name'];
        require __DIR__ . '/../views/customers/detail.php';
    }
    
    /**
     * Create customer form and handling
     */
    public function create() {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                validateCsrfToken($_POST['csrf_token'] ?? '');
                
                $data = [
                    'company_name' => sanitizeInput($_POST['company_name'] ?? ''),
                    'org_number' => sanitizeInput($_POST['org_number'] ?? ''),
                    'address' => sanitizeInput($_POST['address'] ?? ''),
                    'postal_code' => sanitizeInput($_POST['postal_code'] ?? ''),
                    'city' => sanitizeInput($_POST['city'] ?? ''),
                    'country' => sanitizeInput($_POST['country'] ?? 'Norge'),
                    'phone' => sanitizeInput($_POST['phone'] ?? ''),
                    'email' => sanitizeInput($_POST['email'] ?? ''),
                    'website' => sanitizeInput($_POST['website'] ?? ''),
                    'status' => sanitizeInput($_POST['status'] ?? 'active'),
                    'notes' => sanitizeInput($_POST['notes'] ?? '')
                ];
                
                // Validation
                if (empty($data['company_name'])) {
                    throw new Exception('Firmanavn er påkrevd');
                }
                
                if (!empty($data['email']) && !isValidEmail($data['email'])) {
                    throw new Exception('Ugyldig e-postadresse');
                }
                
                $customerId = $this->customerModel->create($data);
                
                setFlashMessage('success', 'Kunde opprettet');
                redirect(APP_URL . '/customers/view/' . $customerId);
                
            } catch (Exception $e) {
                setFlashMessage('error', $e->getMessage());
                $formData = $_POST;
                $pageTitle = 'Ny kunde';
                require __DIR__ . '/../views/customers/form.php';
                return;
            }
        }
        
        $pageTitle = 'Ny kunde';
        $formData = [];
        require __DIR__ . '/../views/customers/form.php';
    }
    
    /**
     * Edit customer
     */
    public function edit($id) {
        if (!$id) {
            setFlashMessage('error', 'Kunde ID mangler');
            redirect(APP_URL . '/customers');
        }
        
        $customer = $this->customerModel->getById($id);
        
        if (!$customer) {
            setFlashMessage('error', 'Kunde ikke funnet');
            redirect(APP_URL . '/customers');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                validateCsrfToken($_POST['csrf_token'] ?? '');
                
                $data = [
                    'company_name' => sanitizeInput($_POST['company_name'] ?? ''),
                    'org_number' => sanitizeInput($_POST['org_number'] ?? ''),
                    'address' => sanitizeInput($_POST['address'] ?? ''),
                    'postal_code' => sanitizeInput($_POST['postal_code'] ?? ''),
                    'city' => sanitizeInput($_POST['city'] ?? ''),
                    'country' => sanitizeInput($_POST['country'] ?? 'Norge'),
                    'phone' => sanitizeInput($_POST['phone'] ?? ''),
                    'email' => sanitizeInput($_POST['email'] ?? ''),
                    'website' => sanitizeInput($_POST['website'] ?? ''),
                    'status' => sanitizeInput($_POST['status'] ?? 'active'),
                    'notes' => sanitizeInput($_POST['notes'] ?? '')
                ];
                
                if (empty($data['company_name'])) {
                    throw new Exception('Firmanavn er påkrevd');
                }
                
                $this->customerModel->update($id, $data);
                
                setFlashMessage('success', 'Kunde oppdatert');
                redirect(APP_URL . '/customers/view/' . $id);
                
            } catch (Exception $e) {
                setFlashMessage('error', $e->getMessage());
            }
        }
        
        $pageTitle = 'Rediger kunde';
        $formData = $customer;
        require __DIR__ . '/../views/customers/form.php';
    }
    
    /**
     * Delete customer (soft delete)
     */
    public function delete($id) {
        if (!$id) {
            setFlashMessage('error', 'Kunde ID mangler');
            redirect(APP_URL . '/customers');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                validateCsrfToken($_POST['csrf_token'] ?? '');
                
                $this->customerModel->delete($id);
                setFlashMessage('success', 'Kunde slettet');
                
            } catch (Exception $e) {
                setFlashMessage('error', 'Kunne ikke slette kunde: ' . $e->getMessage());
            }
        }
        
        redirect(APP_URL . '/customers');
    }
    
    /**
     * BRReg lookup
     */
    public function brregLookup() {
        header('Content-Type: application/json');
        
        $orgNumber = $_GET['org_number'] ?? '';
        
        if (empty($orgNumber)) {
            echo json_encode(['error' => 'Organisasjonsnummer mangler']);
            return;
        }
        
        // Clean org number
        $orgNumber = preg_replace('/[^0-9]/', '', $orgNumber);
        
        if (strlen($orgNumber) !== 9) {
            echo json_encode(['error' => 'Ugyldig organisasjonsnummer']);
            return;
        }
        
        // Call BRReg API
        $url = BRREG_API_URL . $orgNumber;
        
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Accept: application/json'
        ]);
        
        $response = curl_exec($ch);
        $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);
        
        if ($httpCode === 200) {
            $data = json_decode($response, true);
            
            if ($data) {
                // Extract relevant fields
                $result = [
                    'company_name' => $data['navn'] ?? '',
                    'org_number' => $data['organisasjonsnummer'] ?? '',
                    'address' => ($data['forretningsadresse']['adresse'][0] ?? '') . ' ' . ($data['forretningsadresse']['adresse'][1] ?? ''),
                    'postal_code' => $data['forretningsadresse']['postnummer'] ?? '',
                    'city' => $data['forretningsadresse']['poststed'] ?? ''
                ];
                
                echo json_encode($result);
            } else {
                echo json_encode(['error' => 'Kunne ikke parse respons']);
            }
        } else {
            echo json_encode(['error' => 'Fant ikke organisasjon']);
        }
    }
}

/**
 * Sanitize input
 */
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}
