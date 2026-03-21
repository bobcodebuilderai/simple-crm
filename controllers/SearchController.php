<?php
/**
 * Search Controller
 */

class SearchController {
    private $customerModel;
    private $contactModel;
    
    public function __construct() {
        require_once __DIR__ . '/../models/Customer.php';
        require_once __DIR__ . '/../models/Contact.php';
        
        $this->customerModel = new Customer();
        $this->contactModel = new Contact();
    }
    
    /**
     * Search customers and contacts
     */
    public function search() {
        $query = $_GET['q'] ?? '';
        
        $customers = [];
        $contacts = [];
        
        if (!empty($query)) {
            $customers = $this->customerModel->search($query);
            $contacts = $this->contactModel->search($query);
        }
        
        $pageTitle = 'Søkeresultater';
        require __DIR__ . '/../views/search/results.php';
    }
}
