<?php
/**
 * Contact Model
 */

class Contact {
    private $db;
    
    public function __construct() {
        $this->db = db();
    }
    
    /**
     * Get all contacts for a customer
     */
    public function getByCustomer($customerId) {
        $stmt = $this->db->prepare("SELECT * FROM contacts WHERE customer_id = ? AND deleted_at IS NULL ORDER BY is_primary DESC, last_name ASC");
        $stmt->execute([$customerId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get contact by ID
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT c.*, cu.company_name FROM contacts c JOIN customers cu ON c.customer_id = cu.customer_id WHERE c.contact_id = ? AND c.deleted_at IS NULL");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Create new contact
     */
    public function create($data) {
        // If this is primary, unset other primary contacts
        if (!empty($data['is_primary'])) {
            $this->unsetPrimaryForCustomer($data['customer_id']);
        }
        
        $sql = "INSERT INTO contacts (
            customer_id, first_name, last_name, title, email, phone, mobile, is_primary, notes
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['customer_id'],
            $data['first_name'],
            $data['last_name'],
            $data['title'] ?? null,
            $data['email'] ?? null,
            $data['phone'] ?? null,
            $data['mobile'] ?? null,
            !empty($data['is_primary']) ? 1 : 0,
            $data['notes'] ?? null
        ]);
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Update contact
     */
    public function update($id, $data) {
        // If setting as primary, unset others
        if (!empty($data['is_primary'])) {
            $contact = $this->getById($id);
            if ($contact) {
                $this->unsetPrimaryForCustomer($contact['customer_id']);
            }
        }
        
        $sql = "UPDATE contacts SET 
            first_name = ?, last_name = ?, title = ?, email = ?, 
            phone = ?, mobile = ?, is_primary = ?, notes = ?, updated_at = NOW()
        WHERE contact_id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['first_name'],
            $data['last_name'],
            $data['title'] ?? null,
            $data['email'] ?? null,
            $data['phone'] ?? null,
            $data['mobile'] ?? null,
            !empty($data['is_primary']) ? 1 : 0,
            $data['notes'] ?? null,
            $id
        ]);
    }
    
    /**
     * Soft delete contact
     */
    public function delete($id) {
        $stmt = $this->db->prepare("UPDATE contacts SET deleted_at = NOW() WHERE contact_id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Unset primary flag for all contacts of a customer
     */
    private function unsetPrimaryForCustomer($customerId) {
        $stmt = $this->db->prepare("UPDATE contacts SET is_primary = 0 WHERE customer_id = ?");
        $stmt->execute([$customerId]);
    }
    
    /**
     * Search contacts
     */
    public function search($query) {
        $sql = "SELECT c.*, cu.company_name FROM contacts c 
            JOIN customers cu ON c.customer_id = cu.customer_id 
            WHERE c.deleted_at IS NULL 
            AND (c.first_name LIKE ? OR c.last_name LIKE ? OR c.email LIKE ? OR c.phone LIKE ?)
            ORDER BY c.last_name ASC";
        
        $searchTerm = "%$query%";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        return $stmt->fetchAll();
    }
}
