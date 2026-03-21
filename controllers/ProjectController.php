<?php
/**
 * Project Controller
 * Handles external project links
 */

class ProjectController {
    private $projectModel;
    private $customerModel;
    
    public function __construct() {
        require_once __DIR__ . '/../models/Project.php';
        require_once __DIR__ . '/../models/Customer.php';
        
        $this->projectModel = new Project();
        $this->customerModel = new Customer();
    }
    
    /**
     * List projects for a customer
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
        $projects = $this->projectModel->getByCustomer($customerId);
        
        $pageTitle = 'Prosjekter - ' . $customer['company_name'];
        require __DIR__ . '/../views/projects/list.php';
    }
    
    /**
     * Create project link
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
                    'project_name' => sanitizeInput($_POST['project_name'] ?? ''),
                    'project_url' => sanitizeInput($_POST['project_url'] ?? ''),
                    'description' => sanitizeInput($_POST['description'] ?? '')
                ];
                
                if (empty($data['project_name'])) {
                    throw new Exception('Prosjektnavn er påkrevd');
                }
                
                if (empty($data['project_url'])) {
                    throw new Exception('URL er påkrevd');
                }
                
                // Validate URL
                if (!filter_var($data['project_url'], FILTER_VALIDATE_URL)) {
                    throw new Exception('Ugyldig URL');
                }
                
                $this->projectModel->create($data);
                
                setFlashMessage('success', 'Prosjektlenke lagt til');
                redirect(APP_URL . '/customers/view/' . $customerId);
                
            } catch (Exception $e) {
                setFlashMessage('error', $e->getMessage());
            }
        }
        
        $pageTitle = 'Ny prosjektlenke';
        require __DIR__ . '/../views/projects/form.php';
    }
    
    /**
     * Delete project link
     */
    public function delete($id) {
        if (!$id) {
            setFlashMessage('error', 'Prosjekt ID mangler');
            redirect(APP_URL . '/customers');
        }
        
        $project = $this->projectModel->getById($id);
        
        if (!$project) {
            setFlashMessage('error', 'Prosjekt ikke funnet');
            redirect(APP_URL . '/customers');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                validateCsrfToken($_POST['csrf_token'] ?? '');
                
                $this->projectModel->delete($id);
                setFlashMessage('success', 'Prosjektlenke slettet');
                
            } catch (Exception $e) {
                setFlashMessage('error', 'Kunne ikke slette: ' . $e->getMessage());
            }
        }
        
        redirect(APP_URL . '/customers/view/' . $project['customer_id']);
    }
}

function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}
