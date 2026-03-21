<?php
/**
 * Contact Controller
 */

class ContactController {
    private $contactModel;
    private $customerModel;
    
    public function __construct() {
        require_once __DIR__ . '/../models/Contact.php';
        require_once __DIR__ . '/../models/Customer.php';
        
        $this->contactModel = new Contact();
        $this->customerModel = new Customer();
    }
    
    /**
     * List contacts for a customer
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
        $contacts = $this->contactModel->getByCustomer($customerId);
        
        $pageTitle = 'Kontaktpersoner - ' . $customer['company_name'];
        require __DIR__ . '/../views/contacts/list.php';
    }
    
    /**
     * Create contact
     */
    public function create() {
        $customerId = $_GET['customer_id'] ?? $_POST['customer_id'] ?? null;
        
        if (!$customerId) {
            setFlashMessage('error', 'Kunde ID mangler');
            redirect(APP_URL . '/customers');
        }
        
        $customer = $this->customerModel->getById($customerId);
        
        if (!$customer) {
            setFlashMessage('error', 'Kunde ikke funnet');
            redirect(APP_URL . '/customers');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                validateCsrfToken($_POST['csrf_token'] ?? '');
                
                $data = [
                    'customer_id' => $customerId,
                    'first_name' => sanitizeInput($_POST['first_name'] ?? ''),
                    'last_name' => sanitizeInput($_POST['last_name'] ?? ''),
                    'title' => sanitizeInput($_POST['title'] ?? ''),
                    'email' => sanitizeInput($_POST['email'] ?? ''),
                    'phone' => sanitizeInput($_POST['phone'] ?? ''),
                    'mobile' => sanitizeInput($_POST['mobile'] ?? ''),
                    'is_primary' => !empty($_POST['is_primary']),
                    'notes' => sanitizeInput($_POST['notes'] ?? '')
                ];
                
                if (empty($data['first_name']) || empty($data['last_name'])) {
                    throw new Exception('Fornavn og etternavn er påkrevd');
                }
                
                $contactId = $this->contactModel->create($data);
                
                setFlashMessage('success', 'Kontaktperson opprettet');
                redirect(APP_URL . '/customers/view/' . $customerId);
                
            } catch (Exception $e) {
                setFlashMessage('error', $e->getMessage());
            }
        }
        
        $pageTitle = 'Ny kontaktperson';
        require __DIR__ . '/../views/contacts/form.php';
    }
    
    /**
     * Edit contact
     */
    public function edit($id) {
        if (!$id) {
            setFlashMessage('error', 'Kontakt ID mangler');
            redirect(APP_URL . '/customers');
        }
        
        $contact = $this->contactModel->getById($id);
        
        if (!$contact) {
            setFlashMessage('error', 'Kontaktperson ikke funnet');
            redirect(APP_URL . '/customers');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                validateCsrfToken($_POST['csrf_token'] ?? '');
                
                $data = [
                    'first_name' => sanitizeInput($_POST['first_name'] ?? ''),
                    'last_name' => sanitizeInput($_POST['last_name'] ?? ''),
                    'title' => sanitizeInput($_POST['title'] ?? ''),
                    'email' => sanitizeInput($_POST['email'] ?? ''),
                    'phone' => sanitizeInput($_POST['phone'] ?? ''),
                    'mobile' => sanitizeInput($_POST['mobile'] ?? ''),
                    'is_primary' => !empty($_POST['is_primary']),
                    'notes' => sanitizeInput($_POST['notes'] ?? '')
                ];
                
                if (empty($data['first_name']) || empty($data['last_name'])) {
                    throw new Exception('Fornavn og etternavn er påkrevd');
                }
                
                $this->contactModel->update($id, $data);
                
                setFlashMessage('success', 'Kontaktperson oppdatert');
                redirect(APP_URL . '/customers/view/' . $contact['customer_id']);
                
            } catch (Exception $e) {
                setFlashMessage('error', $e->getMessage());
            }
        }
        
        $pageTitle = 'Rediger kontaktperson';
        $formData = $contact;
        require __DIR__ . '/../views/contacts/form.php';
    }
    
    /**
     * Delete contact
     */
    public function delete($id) {
        if (!$id) {
            setFlashMessage('error', 'Kontakt ID mangler');
            redirect(APP_URL . '/customers');
        }
        
        $contact = $this->contactModel->getById($id);
        
        if (!$contact) {
            setFlashMessage('error', 'Kontaktperson ikke funnet');
            redirect(APP_URL . '/customers');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                validateCsrfToken($_POST['csrf_token'] ?? '');
                
                $this->contactModel->delete($id);
                setFlashMessage('success', 'Kontaktperson slettet');
                
            } catch (Exception $e) {
                setFlashMessage('error', 'Kunne ikke slette: ' . $e->getMessage());
            }
        }
        
        redirect(APP_URL . '/customers/view/' . $contact['customer_id']);
    }
}

function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}
