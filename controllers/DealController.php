<?php
/**
 * Deal Controller
 */

class DealController {
    private $dealModel;
    private $customerModel;
    
    public function __construct() {
        require_once __DIR__ . '/../models/Deal.php';
        require_once __DIR__ . '/../models/Customer.php';
        
        $this->dealModel = new Deal();
        $this->customerModel = new Customer();
    }
    
    /**
     * List all open deals
     */
    public function list() {
        $deals = $this->dealModel->getOpenDeals();
        $pageTitle = 'Deals';
        require __DIR__ . '/../views/deals/list.php';
    }
    
    /**
     * View deal
     */
    public function view($id) {
        if (!$id) {
            setFlashMessage('error', 'Deal ID mangler');
            redirect(APP_URL . '/deals');
        }
        
        $deal = $this->dealModel->getById($id);
        
        if (!$deal) {
            setFlashMessage('error', 'Deal ikke funnet');
            redirect(APP_URL . '/deals');
        }
        
        require_once __DIR__ . '/../models/Task.php';
        $taskModel = new Task();
        $tasks = $taskModel->getByDeal($id);
        
        $pageTitle = $deal['title'];
        require __DIR__ . '/../views/deals/view.php';
    }
    
    /**
     * Create deal
     */
    public function create() {
        $customerId = $_GET['customer_id'] ?? $_POST['customer_id'] ?? null;
        
        if ($customerId) {
            $customer = $this->customerModel->getById($customerId);
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                validateCsrfToken($_POST['csrf_token'] ?? '');
                
                $data = [
                    'customer_id' => sanitizeInput($_POST['customer_id'] ?? ''),
                    'title' => sanitizeInput($_POST['title'] ?? ''),
                    'value' => !empty($_POST['value']) ? floatval($_POST['value']) : null,
                    'status' => sanitizeInput($_POST['status'] ?? 'new'),
                    'description' => sanitizeInput($_POST['description'] ?? ''),
                    'expected_close_date' => !empty($_POST['expected_close_date']) ? sanitizeInput($_POST['expected_close_date']) : null
                ];
                
                if (empty($data['customer_id'])) {
                    throw new Exception('Kunde er påkrevd');
                }
                
                if (empty($data['title'])) {
                    throw new Exception('Tittel er påkrevd');
                }
                
                $dealId = $this->dealModel->create($data);
                
                setFlashMessage('success', 'Deal opprettet');
                redirect(APP_URL . '/deals/view/' . $dealId);
                
            } catch (Exception $e) {
                setFlashMessage('error', $e->getMessage());
            }
        }
        
        $customers = $this->customerModel->getAll();
        $pageTitle = 'Ny deal';
        require __DIR__ . '/../views/deals/form.php';
    }
    
    /**
     * Edit deal
     */
    public function edit($id) {
        if (!$id) {
            setFlashMessage('error', 'Deal ID mangler');
            redirect(APP_URL . '/deals');
        }
        
        $deal = $this->dealModel->getById($id);
        
        if (!$deal) {
            setFlashMessage('error', 'Deal ikke funnet');
            redirect(APP_URL . '/deals');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                validateCsrfToken($_POST['csrf_token'] ?? '');
                
                $data = [
                    'title' => sanitizeInput($_POST['title'] ?? ''),
                    'value' => !empty($_POST['value']) ? floatval($_POST['value']) : null,
                    'status' => sanitizeInput($_POST['status'] ?? 'new'),
                    'description' => sanitizeInput($_POST['description'] ?? ''),
                    'expected_close_date' => !empty($_POST['expected_close_date']) ? sanitizeInput($_POST['expected_close_date']) : null
                ];
                
                if (empty($data['title'])) {
                    throw new Exception('Tittel er påkrevd');
                }
                
                $this->dealModel->update($id, $data);
                
                setFlashMessage('success', 'Deal oppdatert');
                redirect(APP_URL . '/deals/view/' . $id);
                
            } catch (Exception $e) {
                setFlashMessage('error', $e->getMessage());
            }
        }
        
        $pageTitle = 'Rediger deal';
        $formData = $deal;
        require __DIR__ . '/../views/deals/form.php';
    }
    
    /**
     * Delete deal
     */
    public function delete($id) {
        if (!$id) {
            setFlashMessage('error', 'Deal ID mangler');
            redirect(APP_URL . '/deals');
        }
        
        $deal = $this->dealModel->getById($id);
        
        if (!$deal) {
            setFlashMessage('error', 'Deal ikke funnet');
            redirect(APP_URL . '/deals');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                validateCsrfToken($_POST['csrf_token'] ?? '');
                
                $this->dealModel->delete($id);
                setFlashMessage('success', 'Deal slettet');
                redirect(APP_URL . '/customers/view/' . $deal['customer_id']);
                return;
                
            } catch (Exception $e) {
                setFlashMessage('error', 'Kunne ikke slette: ' . $e->getMessage());
            }
        }
        
        redirect(APP_URL . '/deals');
    }
}

function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}
