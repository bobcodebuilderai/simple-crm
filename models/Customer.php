<?php
/**
 * Customer Model
 * Handles all database operations for customers
 */

class Customer {
    private $db;
    
    public function __construct() {
        $this->db = db();
    }
    
    /**
     * Get all active customers
     */
    public function getAll($search = '', $status = '') {
        $sql = "SELECT * FROM customers WHERE deleted_at IS NULL";
        $params = [];
        
        if (!empty($search)) {
            $sql .= " AND (company_name LIKE ? OR customer_number LIKE ? OR org_number LIKE ?)";
            $searchTerm = "%$search%";
            $params = [$searchTerm, $searchTerm, $searchTerm];
        }
        
        if (!empty($status)) {
            $sql .= " AND status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY company_name ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Get customer by ID
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM customers WHERE customer_id = ? AND deleted_at IS NULL");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Get customer by number
     */
    public function getByNumber($number) {
        $stmt = $this->db->prepare("SELECT * FROM customers WHERE customer_number = ? AND deleted_at IS NULL");
        $stmt->execute([$number]);
        return $stmt->fetch();
    }
    
    /**
     * Create new customer
     */
    public function create($data) {
        $sql = "INSERT INTO customers (
            customer_number, company_name, org_number, address, postal_code, 
            city, country, phone, email, website, status, notes
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([
            generateCustomerNumber(),
            $data['company_name'],
            $data['org_number'] ?? null,
            $data['address'] ?? null,
            $data['postal_code'] ?? null,
            $data['city'] ?? null,
            $data['country'] ?? 'Norge',
            $data['phone'] ?? null,
            $data['email'] ?? null,
            $data['website'] ?? null,
            $data['status'] ?? 'active',
            $data['notes'] ?? null
        ]);
        
        return $this->db->lastInsertId();
    }
    
    /**
     * Update customer
     */
    public function update($id, $data) {
        $sql = "UPDATE customers SET 
            company_name = ?, org_number = ?, address = ?, postal_code = ?, 
            city = ?, country = ?, phone = ?, email = ?, website = ?, 
            status = ?, notes = ?, updated_at = NOW()
        WHERE customer_id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute([
            $data['company_name'],
            $data['org_number'] ?? null,
            $data['address'] ?? null,
            $data['postal_code'] ?? null,
            $data['city'] ?? null,
            $data['country'] ?? 'Norge',
            $data['phone'] ?? null,
            $data['email'] ?? null,
            $data['website'] ?? null,
            $data['status'] ?? 'active',
            $data['notes'] ?? null,
            $id
        ]);
    }
    
    /**
     * Soft delete customer
     */
    public function delete($id) {
        $stmt = $this->db->prepare("UPDATE customers SET deleted_at = NOW() WHERE customer_id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Get customer statistics
     */
    public function getStats() {
        $stats = [];
        
        // Total active customers
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM customers WHERE deleted_at IS NULL AND status = 'active'");
        $stats['active'] = $stmt->fetch()['count'];
        
        // Total inactive customers
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM customers WHERE deleted_at IS NULL AND status = 'inactive'");
        $stats['inactive'] = $stmt->fetch()['count'];
        
        // Recent customers (last 30 days)
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM customers WHERE deleted_at IS NULL AND created_at >= DATE_SUB(NOW(), INTERVAL 30 DAY)");
        $stats['recent'] = $stmt->fetch()['count'];
        
        return $stats;
    }
    
    /**
     * Search customers
     */
    public function search($query) {
        $sql = "SELECT * FROM customers WHERE deleted_at IS NULL 
            AND (company_name LIKE ? OR customer_number LIKE ? OR org_number LIKE ? 
            OR phone LIKE ? OR email LIKE ?)
            ORDER BY company_name ASC";
        
        $searchTerm = "%$query%";
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        return $stmt->fetchAll();
    }
}
