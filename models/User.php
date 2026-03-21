<?php
/**
 * User Model
 */

class User {
    private $db;
    
    public function __construct() {
        $this->db = db();
    }
    
    /**
     * Authenticate user
     */
    public function authenticate($username, $password) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE username = ? AND is_active = 1");
        $stmt->execute([$username]);
        $user = $stmt->fetch();
        
        if ($user && password_verify($password, $user['password_hash'])) {
            return $user;
        }
        
        return false;
    }
    
    /**
     * Get user by ID
     */
    public function getById($id) {
        $stmt = $this->db->prepare("SELECT * FROM users WHERE user_id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Update last login
     */
    public function updateLastLogin($id) {
        $stmt = $this->db->prepare("UPDATE users SET last_login = NOW() WHERE user_id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Update password
     */
    public function updatePassword($id, $newPassword) {
        $hash = password_hash($newPassword, PASSWORD_DEFAULT);
        $stmt = $this->db->prepare("UPDATE users SET password_hash = ? WHERE user_id = ?");
        return $stmt->execute([$hash, $id]);
    }
    
    /**
     * Create default admin user (for setup)
     */
    public function createDefaultUser() {
        // Check if any users exist
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM users");
        $result = $stmt->fetch();
        
        if ($result['count'] == 0) {
            // Create default admin user
            $stmt = $this->db->prepare("INSERT INTO users (username, email, password_hash, full_name) VALUES (?, ?, ?, ?)");
            $stmt->execute([
                'admin',
                'admin@example.com',
                password_hash('admin123', PASSWORD_DEFAULT),
                'Administrator'
            ]);
            return true;
        }
        
        return false;
    }
}
