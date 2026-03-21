<?php
/**
 * Activity Model
 */

class Activity {
    private $db;
    
    public function __construct() {
        $this->db = db();
    }
    
    /**
     * Get activities for a customer
     */
    public function getByCustomer($customerId, $limit = null) {
        $sql = "SELECT a.*, c.first_name, c.last_name 
            FROM activities a 
            LEFT JOIN contacts c ON a.contact_id = c.contact_id 
            WHERE a.customer_id = ? 
            ORDER BY a.activity_date DESC";
        
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$customerId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get activity by ID
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT a.*, c.first_name, c.last_name, cu.company_name 
            FROM activities a 
            LEFT JOIN contacts c ON a.contact_id = c.contact_id 
            JOIN customers cu ON a.customer_id = cu.customer_id 
            WHERE a.activity_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Get recent activities across all customers
     */
    public function getRecent($limit = 10) {
        $sql = "SELECT a.*, c.first_name, c.last_name, cu.company_name, cu.customer_id 
            FROM activities a 
            LEFT JOIN contacts c ON a.contact_id = c.contact_id 
            JOIN customers cu ON a.customer_id = cu.customer_id 
            WHERE cu.deleted_at IS NULL
            ORDER BY a.created_at DESC 
            LIMIT ?";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Create activity
     */
    public function create($data) {
        $sql = "INSERT INTO activities (
            customer_id, contact_id, activity_type, title, description, activity_date, created_by
        ) VALUES (?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['customer_id'],
            $data['contact_id'] ?? null,
            $data['activity_type'],
            $data['title'],
            $data['description'] ?? null,
            $data['activity_date'] ?? date('Y-m-d H:i:s'),
            $data['created_by'] ?? 'System'
        ]);
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Update activity
     */
    public function update($id, $data) {
        $sql = "UPDATE activities SET 
            activity_type = ?, title = ?, description = ?, activity_date = ?, updated_at = NOW()
        WHERE activity_id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['activity_type'],
            $data['title'],
            $data['description'] ?? null,
            $data['activity_date'],
            $id
        ]);
    }
    
    /**
     * Delete activity
     */
    public function delete($id) {
        // Also delete attachments
        $stmt = $this->db->prepare("DELETE FROM attachments WHERE activity_id = ?");
        $stmt->execute([$id]);
        
        $stmt = $this->db->prepare("DELETE FROM activities WHERE activity_id = ?");
        return $stmt->execute([$id]);
    }
}
