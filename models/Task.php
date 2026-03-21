<?php
/**
 * Task Model
 */

class Task {
    private $db;
    
    public function __construct() {
        $this->db = db();
    }
    
    /**
     * Get tasks for a customer
     */
    public function getByCustomer($customerId, $status = null) {
        $sql = "SELECT t.*, d.title as deal_title FROM tasks t 
            LEFT JOIN deals d ON t.deal_id = d.deal_id 
            WHERE t.customer_id = ?";
        $params = [$customerId];
        
        if ($status) {
            $sql .= " AND t.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY t.due_date ASC, t.priority DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Get tasks for a deal
     */
    public function getByDeal($dealId) {
        $stmt = $this->db->prepare("SELECT * FROM tasks WHERE deal_id = ? ORDER BY due_date ASC, priority DESC");
        $stmt->execute([$dealId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get task by ID
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT t.*, c.company_name, d.title as deal_title 
            FROM tasks t 
            JOIN customers c ON t.customer_id = c.customer_id 
            LEFT JOIN deals d ON t.deal_id = d.deal_id 
            WHERE t.task_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Get all open tasks
     */
    public function getOpenTasks($limit = null) {
        $sql = "SELECT t.*, c.company_name, d.title as deal_title 
            FROM tasks t 
            JOIN customers c ON t.customer_id = c.customer_id 
            LEFT JOIN deals d ON t.deal_id = d.deal_id 
            WHERE t.status = 'open' 
            ORDER BY t.due_date ASC, t.priority DESC";
        
        if ($limit) {
            $sql .= " LIMIT " . (int)$limit;
        }
        
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Get overdue tasks
     */
    public function getOverdueTasks() {
        $stmt = $this->db->query("SELECT t.*, c.company_name, d.title as deal_title 
            FROM tasks t 
            JOIN customers c ON t.customer_id = c.customer_id 
            LEFT JOIN deals d ON t.deal_id = d.deal_id 
            WHERE t.status = 'open' AND t.due_date < CURDATE()
            ORDER BY t.due_date ASC");
        return $stmt->fetchAll();
    }
    
    /**
     * Create task
     */
    public function create($data) {
        $sql = "INSERT INTO tasks (deal_id, customer_id, title, description, due_date, reminder_date, priority, status) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            $data['deal_id'] ?? null,
            $data['customer_id'],
            $data['title'],
            $data['description'] ?? null,
            $data['due_date'],
            $data['reminder_date'] ?? null,
            $data['priority'] ?? 'medium',
            $data['status'] ?? 'open'
        ]);
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Update task
     */
    public function update($id, $data) {
        $sql = "UPDATE tasks SET 
            title = ?, description = ?, due_date = ?, reminder_date = ?, 
            priority = ?, status = ?, updated_at = NOW()
        WHERE task_id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['title'],
            $data['description'] ?? null,
            $data['due_date'],
            $data['reminder_date'] ?? null,
            $data['priority'],
            $data['status'],
            $id
        ]);
    }
    
    /**
     * Complete task
     */
    public function complete($id) {
        $stmt = $this->db->prepare("UPDATE tasks SET status = 'completed', updated_at = NOW() WHERE task_id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Delete task
     */
    public function delete($id) {
        $stmt = $this->db->prepare("DELETE FROM tasks WHERE task_id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Get task statistics
     */
    public function getStats() {
        $stats = [];
        
        // Open tasks
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM tasks WHERE status = 'open'");
        $stats['open'] = $stmt->fetch()['count'];
        
        // Overdue tasks
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM tasks WHERE status = 'open' AND due_date < CURDATE()");
        $stats['overdue'] = $stmt->fetch()['count'];
        
        // Completed today
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM tasks WHERE status = 'completed' AND DATE(updated_at) = CURDATE()");
        $stats['completed_today'] = $stmt->fetch()['count'];
        
        return $stats;
    }
}
