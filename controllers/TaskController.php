<?php
/**
 * Task Controller
 */

class TaskController {
    private $taskModel;
    private $customerModel;
    private $dealModel;
    
    public function __construct() {
        require_once __DIR__ . '/../models/Task.php';
        require_once __DIR__ . '/../models/Customer.php';
        require_once __DIR__ . '/../models/Deal.php';
        
        $this->taskModel = new Task();
        $this->customerModel = new Customer();
        $this->dealModel = new Deal();
    }
    
    /**
     * List all open tasks
     */
    public function list() {
        $status = $_GET['status'] ?? 'open';
        
        if ($status === 'all') {
            $tasks = $this->taskModel->getOpenTasks(100);
        } else {
            $tasks = $this->taskModel->getOpenTasks();
        }
        
        $overdueTasks = $this->taskModel->getOverdueTasks();
        
        $pageTitle = 'Oppgaver';
        require __DIR__ . '/../views/tasks/list.php';
    }
    
    /**
     * View task
     */
    public function view($id) {
        if (!$id) {
            setFlashMessage('error', 'Oppgave ID mangler');
            redirect(APP_URL . '/tasks');
        }
        
        $task = $this->taskModel->getById($id);
        
        if (!$task) {
            setFlashMessage('error', 'Oppgave ikke funnet');
            redirect(APP_URL . '/tasks');
        }
        
        $pageTitle = $task['title'];
        require __DIR__ . '/../views/tasks/view.php';
    }
    
    /**
     * Create task
     */
    public function create() {
        $customerId = $_GET['customer_id'] ?? $_POST['customer_id'] ?? null;
        $dealId = $_GET['deal_id'] ?? $_POST['deal_id'] ?? null;
        
        if ($customerId) {
            $customer = $this->customerModel->getById($customerId);
            $deals = $this->dealModel->getByCustomer($customerId);
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                validateCsrfToken($_POST['csrf_token'] ?? '');
                
                $data = [
                    'deal_id' => !empty($_POST['deal_id']) ? $_POST['deal_id'] : null,
                    'customer_id' => sanitizeInput($_POST['customer_id'] ?? ''),
                    'title' => sanitizeInput($_POST['title'] ?? ''),
                    'description' => sanitizeInput($_POST['description'] ?? ''),
                    'due_date' => sanitizeInput($_POST['due_date'] ?? ''),
                    'reminder_date' => !empty($_POST['reminder_date']) ? sanitizeInput($_POST['reminder_date']) : null,
                    'priority' => sanitizeInput($_POST['priority'] ?? 'medium'),
                    'status' => 'open'
                ];
                
                if (empty($data['customer_id'])) {
                    throw new Exception('Kunde er påkrevd');
                }
                
                if (empty($data['title'])) {
                    throw new Exception('Tittel er påkrevd');
                }
                
                if (empty($data['due_date'])) {
                    throw new Exception('Forfallsdato er påkrevd');
                }
                
                $taskId = $this->taskModel->create($data);
                
                setFlashMessage('success', 'Oppgave opprettet');
                
                if ($data['deal_id']) {
                    redirect(APP_URL . '/deals/view/' . $data['deal_id']);
                } else {
                    redirect(APP_URL . '/customers/view/' . $data['customer_id']);
                }
                
            } catch (Exception $e) {
                setFlashMessage('error', $e->getMessage());
            }
        }
        
        $customers = $this->customerModel->getAll();
        $pageTitle = 'Ny oppgave';
        require __DIR__ . '/../views/tasks/form.php';
    }
    
    /**
     * Edit task
     */
    public function edit($id) {
        if (!$id) {
            setFlashMessage('error', 'Oppgave ID mangler');
            redirect(APP_URL . '/tasks');
        }
        
        $task = $this->taskModel->getById($id);
        
        if (!$task) {
            setFlashMessage('error', 'Oppgave ikke funnet');
            redirect(APP_URL . '/tasks');
        }
        
        $deals = $this->dealModel->getByCustomer($task['customer_id']);
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                validateCsrfToken($_POST['csrf_token'] ?? '');
                
                $data = [
                    'title' => sanitizeInput($_POST['title'] ?? ''),
                    'description' => sanitizeInput($_POST['description'] ?? ''),
                    'due_date' => sanitizeInput($_POST['due_date'] ?? ''),
                    'reminder_date' => !empty($_POST['reminder_date']) ? sanitizeInput($_POST['reminder_date']) : null,
                    'priority' => sanitizeInput($_POST['priority'] ?? 'medium'),
                    'status' => sanitizeInput($_POST['status'] ?? 'open')
                ];
                
                if (empty($data['title'])) {
                    throw new Exception('Tittel er påkrevd');
                }
                
                if (empty($data['due_date'])) {
                    throw new Exception('Forfallsdato er påkrevd');
                }
                
                $this->taskModel->update($id, $data);
                
                setFlashMessage('success', 'Oppgave oppdatert');
                redirect(APP_URL . '/tasks/view/' . $id);
                
            } catch (Exception $e) {
                setFlashMessage('error', $e->getMessage());
            }
        }
        
        $pageTitle = 'Rediger oppgave';
        $formData = $task;
        require __DIR__ . '/../views/tasks/form.php';
    }
    
    /**
     * Complete task
     */
    public function complete($id) {
        if (!$id) {
            setFlashMessage('error', 'Oppgave ID mangler');
            redirect(APP_URL . '/tasks');
        }
        
        try {
            $this->taskModel->complete($id);
            setFlashMessage('success', 'Oppgave fullført');
        } catch (Exception $e) {
            setFlashMessage('error', 'Kunne ikke fullføre: ' . $e->getMessage());
        }
        
        redirect(APP_URL . '/tasks');
    }
    
    /**
     * Delete task
     */
    public function delete($id) {
        if (!$id) {
            setFlashMessage('error', 'Oppgave ID mangler');
            redirect(APP_URL . '/tasks');
        }
        
        $task = $this->taskModel->getById($id);
        
        if (!$task) {
            setFlashMessage('error', 'Oppgave ikke funnet');
            redirect(APP_URL . '/tasks');
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                validateCsrfToken($_POST['csrf_token'] ?? '');
                
                $this->taskModel->delete($id);
                setFlashMessage('success', 'Oppgave slettet');
                
            } catch (Exception $e) {
                setFlashMessage('error', 'Kunne ikke slette: ' . $e->getMessage());
            }
        }
        
        if ($task['deal_id']) {
            redirect(APP_URL . '/deals/view/' . $task['deal_id']);
        } else {
            redirect(APP_URL . '/customers/view/' . $task['customer_id']);
        }
    }
}

function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}
