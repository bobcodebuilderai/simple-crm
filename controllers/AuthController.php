<?php
/**
 * Auth Controller
 * Handles user authentication
 */

class AuthController {
    
    public function __construct() {
        require_once __DIR__ . '/../models/User.php';
        $this->userModel = new User();
    }
    
    /**
     * Login form and handling
     */
    public function login() {
        // If already logged in, redirect to dashboard
        if (isset($_SESSION['user_id'])) {
            redirect(APP_URL);
        }
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                $username = trim($_POST['username'] ?? '');
                $password = $_POST['password'] ?? '';
                
                if (empty($username) || empty($password)) {
                    throw new Exception('Brukernavn og passord er påkrevd');
                }
                
                $user = $this->userModel->authenticate($username, $password);
                
                if (!$user) {
                    throw new Exception('Ugyldig brukernavn eller passord');
                }
                
                // Set session
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                
                // Update last login
                $this->userModel->updateLastLogin($user['user_id']);
                
                setFlashMessage('success', 'Velkommen, ' . $user['full_name'] . '!');
                redirect(APP_URL);
                
            } catch (Exception $e) {
                $error = $e->getMessage();
            }
        }
        
        // Show login view without header/footer
        require __DIR__ . '/../views/auth/login.php';
    }
    
    /**
     * Logout
     */
    public function logout() {
        // Clear session
        $_SESSION = [];
        
        // Destroy session cookie
        if (isset($_COOKIE[session_name()])) {
            setcookie(session_name(), '', time() - 3600, '/');
        }
        
        session_destroy();
        
        setFlashMessage('info', 'Du er nå logget ut');
        redirect(APP_URL . '/auth/login');
    }
    
    /**
     * Change password
     */
    public function changePassword() {
        $this->requireAuth();
        
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            try {
                validateCsrfToken($_POST['csrf_token'] ?? '');
                
                $currentPassword = $_POST['current_password'] ?? '';
                $newPassword = $_POST['new_password'] ?? '';
                $confirmPassword = $_POST['confirm_password'] ?? '';
                
                if (empty($currentPassword) || empty($newPassword)) {
                    throw new Exception('Alle felter er påkrevd');
                }
                
                if ($newPassword !== $confirmPassword) {
                    throw new Exception('Nytt passord og bekreftelse må være like');
                }
                
                if (strlen($newPassword) < 8) {
                    throw new Exception('Passordet må være minst 8 tegn');
                }
                
                // Verify current password
                $user = $this->userModel->getById($_SESSION['user_id']);
                if (!password_verify($currentPassword, $user['password_hash'])) {
                    throw new Exception('Nåværende passord er feil');
                }
                
                // Update password
                $this->userModel->updatePassword($_SESSION['user_id'], $newPassword);
                
                setFlashMessage('success', 'Passordet er endret');
                redirect(APP_URL);
                
            } catch (Exception $e) {
                setFlashMessage('error', $e->getMessage());
            }
        }
        
        $pageTitle = 'Endre passord';
        require __DIR__ . '/../views/auth/change-password.php';
    }
    
    /**
     * Require authentication
     */
    private function requireAuth() {
        if (!isset($_SESSION['user_id'])) {
            setFlashMessage('error', 'Du må logge inn for å se denne siden');
            redirect(APP_URL . '/auth/login');
        }
    }
}
