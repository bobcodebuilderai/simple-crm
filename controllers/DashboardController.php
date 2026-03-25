<?php
/**
 * Dashboard Controller
 */
class DashboardController
{
    /**
     * Dashboard overview
     */
    public function index()
    {
        // Get statistics
        $customerCount = $this->getCustomerCount();
        $dealCount = $this->getDealCount();
        $taskCount = $this->getTaskCount();
        $recentActivities = $this->getRecentActivities();
        
        // Include view
        require_once __DIR__ . '/../views/dashboard.php';
    }
    
    /**
     * Get total customer count
     */
    private function getCustomerCount()
    {
        try {
            $db = db();
            $stmt = $db->query("SELECT COUNT(*) FROM customers");
            return $stmt->fetchColumn();
        } catch (Exception $e) {
            return 0;
        }
    }
    
    /**
     * Get total deal count
     */
    private function getDealCount()
    {
        try {
            $db = db();
            $stmt = $db->query("SELECT COUNT(*) FROM deals");
            return $stmt->fetchColumn();
        } catch (Exception $e) {
            return 0;
        }
    }
    
    /**
     * Get open task count
     */
    private function getTaskCount()
    {
        try {
            $db = db();
            $stmt = $db->query("SELECT COUNT(*) FROM tasks WHERE status != 'completed'");
            return $stmt->fetchColumn();
        } catch (Exception $e) {
            return 0;
        }
    }
    
    /**
     * Get recent activities
     */
    private function getRecentActivities($limit = 10)
    {
        try {
            $db = db();
            $stmt = $db->prepare("
                SELECT a.*, c.company_name as customer_name, u.name as user_name
                FROM activities a
                LEFT JOIN customers c ON a.customer_id = c.id
                LEFT JOIN users u ON a.created_by = u.id
                ORDER BY a.created_at DESC
                LIMIT ?
            ");
            $stmt->execute([$limit]);
            return $stmt->fetchAll();
        } catch (Exception $e) {
            return [];
        }
    }
}
