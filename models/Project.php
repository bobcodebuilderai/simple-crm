<?php
/**
 * Project Model
 */

class Project {
    private $db;
    
    public function __construct() {
        $this->db = db();
    }
    
    /**
     * Get projects for a customer
     */
    public function getByCustomer($customerId) {
        $stmt = $this->db->prepare("SELECT * FROM projects WHERE customer_id = ? ORDER BY created_at DESC");
        $stmt->execute([$customerId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get project by ID
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM projects WHERE project_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Create project
     */
    public function create($data) {
        $sql = "INSERT INTO projects (customer_id, project_name, project_url, description) 
            VALUES (?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['customer_id'],
            $data['project_name'],
            $data['project_url'],
            $data['description'] ?? null
        ]);
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Delete project
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM projects WHERE project_id = ?");
        return $stmt->execute([$id]);
    }
}
