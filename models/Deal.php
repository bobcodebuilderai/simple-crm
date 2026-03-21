<?php
/**
 * Deal Model
 */

class Deal {
    private $db;
    
    public function __construct() {
        $this->db = db();
    }
    
    /**
     * Get all deals for a customer
     */
    public function getByCustomer($customerId) {
        $stmt = $this->db->prepare("SELECT * FROM deals WHERE customer_id = ? ORDER BY created_at DESC");
        $stmt->execute([$customerId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get deal by ID
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT d.*, c.company_name FROM deals d JOIN customers c ON d.customer_id = c.customer_id WHERE d.deal_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Get all open deals
     */
    public function getOpenDeals() {
        $stmt = $this->db->query("SELECT d.*, c.company_name FROM deals d JOIN customers c ON d.customer_id = c.customer_id WHERE d.status IN ('new', 'ongoing') ORDER BY d.expected_close_date ASC");
        return $stmt->fetchAll();
    }
    
    /**
     * Create deal
     */
    public function create($data) {
        $sql = "INSERT INTO deals (customer_id, title, value, status, description, expected_close_date) 
            VALUES (?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['customer_id'],
            $data['title'],
            $data['value'] ?? null,
            $data['status'] ?? 'new',
            $data['description'] ?? null,
            $data['expected_close_date'] ?? null
        ]);
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Update deal
     */
    public function update($id, $data) {
        $sql = "UPDATE deals SET 
            title = ?, value = ?, status = ?, description = ?, expected_close_date = ?, updated_at = NOW()
        WHERE deal_id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['title'],
            $data['value'] ?? null,
            $data['status'],
            $data['description'] ?? null,
            $data['expected_close_date'] ?? null,
            $id
        ]);
    }
    
    /**
     * Delete deal
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM deals WHERE deal_id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Get deal statistics
     */
    public function getStats() {
        $stats = [];
        
        // Count by status
        $stmt = $this->db->query("SELECT status, COUNT(*) as count, SUM(value) as total FROM deals GROUP BY status");
        $stats['by_status'] = $stmt->fetchAll();
        
        // Total open deals
        $stmt = $this->db->query("SELECT COUNT(*) as count, SUM(value) as total FROM deals WHERE status IN ('new', 'ongoing')");
        $open = $stmt->fetch();
        $stats['open_count'] = $open['count'];
        $stats['open_value'] = $open['total'] ?? 0;
        
        return $stats;
    }
}
