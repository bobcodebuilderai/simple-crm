<?php
/**
 * Common Helper Functions
 */

/**
 * Sanitize user input
 * Prevents XSS attacks
 */
function e($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Format date for display
 */
function formatDate($date, $format = 'd.m.Y') {
    if (empty($date)) return '';
    $timestamp = strtotime($date);
    return date($format, $timestamp);
}

/**
 * Format datetime for display
 */
function formatDateTime($datetime, $format = 'd.m.Y H:i') {
    if (empty($datetime)) return '';
    $timestamp = strtotime($datetime);
    return date($format, $timestamp);
}

/**
 * Format number as currency
 */
function formatCurrency($amount, $currency = 'kr') {
    if (empty($amount)) return '';
    return number_format($amount, 2, ',', ' ') . ' ' . $currency;
}

/**
 * Generate CSRF token
 */
function generateCsrfToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Validate CSRF token
 */
function validateCsrfToken($token) {
    if (empty($_SESSION['csrf_token']) || $token !== $_SESSION['csrf_token']) {
        throw new Exception('Invalid CSRF token');
    }
    return true;
}

/**
 * Get CSRF token input field
 */
function csrfField() {
    $token = generateCsrfToken();
    return '<input type="hidden" name="csrf_token" value="' . e($token) . '">';
}

/**
 * Generate unique customer number
 * Format: CUST-YYYY-XXXX where XXXX is auto-increment
 */
function generateCustomerNumber() {
    $db = db();
    $year = date('Y');
    $prefix = "CUST-{$year}-";
    
    // Find highest number for this year
    $stmt = $db->prepare("SELECT customer_number FROM customers WHERE customer_number LIKE ? ORDER BY customer_number DESC LIMIT 1");
    $stmt->execute([$prefix . '%']);
    $last = $stmt->fetch();
    
    if ($last) {
        $parts = explode('-', $last['customer_number']);
        $number = intval(end($parts)) + 1;
    } else {
        $number = 1;
    }
    
    return $prefix . str_pad($number, 4, '0', STR_PAD_LEFT);
}

/**
 * Validate email address
 */
function isValidEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate URL
 */
function isValidUrl($url) {
    return filter_var($url, FILTER_VALIDATE_URL) !== false;
}

/**
 * Validate Norwegian organization number
 * Uses modulo 11 check
 */
function isValidOrgNumber($orgNumber) {
    // Remove spaces
    $orgNumber = preg_replace('/\s+/', '', $orgNumber);
    
    // Must be 9 digits
    if (!preg_match('/^\d{9}$/', $orgNumber)) {
        return false;
    }
    
    // Modulo 11 check
    $weights = [3, 2, 7, 6, 5, 4, 3, 2];
    $sum = 0;
    
    for ($i = 0; $i < 8; $i++) {
        $sum += intval($orgNumber[$i]) * $weights[$i];
    }
    
    $remainder = $sum % 11;
    $controlDigit = $remainder === 0 ? 0 : 11 - $remainder;
    
    return $controlDigit == intval($orgNumber[8]);
}

/**
 * Format phone number for display
 */
function formatPhone($phone) {
    if (empty($phone)) return '';
    // Simple formatting, can be improved
    return preg_replace('/(\d{2})(?=\d)/', '$1 ', $phone);
}

/**
 * Truncate text with ellipsis
 */
function truncate($text, $length = 100) {
    if (strlen($text) <= $length) return $text;
    return substr($text, 0, $length) . '...';
}

/**
 * Flash message helper
 */
function setFlashMessage($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type, // success, error, warning, info
        'message' => $message
    ];
}

/**
 * Get and clear flash message
 */
function getFlashMessage() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

/**
 * Display flash message if exists
 */
function displayFlashMessage() {
    $flash = getFlashMessage();
    if ($flash) {
        $colors = [
            'success' => 'bg-green-100 text-green-800 border-green-200',
            'error' => 'bg-red-100 text-red-800 border-red-200',
            'warning' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
            'info' => 'bg-blue-100 text-blue-800 border-blue-200'
        ];
        $color = $colors[$flash['type']] ?? $colors['info'];
        
        echo '<div class="p-4 mb-4 rounded border ' . $color . '">';
        echo e($flash['message']);
        echo '</div>';
    }
}

/**
 * Get activity type label in Norwegian
 */
function getActivityTypeLabel($type) {
    $labels = [
        'customer_service' => 'Kundeservice',
        'meeting' => 'Møte',
        'phone_call' => 'Telefonsamtale',
        'email' => 'E-post',
        'contract' => 'Kontrakt',
        'follow_up' => 'Oppfølging',
        'note' => 'Notat',
        'other' => 'Annet'
    ];
    return $labels[$type] ?? $type;
}

/**
 * Get deal status label in Norwegian
 */
function getDealStatusLabel($status) {
    $labels = [
        'new' => 'Ny',
        'ongoing' => 'Pågående',
        'won' => 'Vunnet',
        'lost' => 'Tapt',
        'on_hold' => 'Satt på vent'
    ];
    return $labels[$status] ?? $status;
}

/**
 * Get deal status color class
 */
function getDealStatusColor($status) {
    $colors = [
        'new' => 'bg-blue-100 text-blue-800',
        'ongoing' => 'bg-yellow-100 text-yellow-800',
        'won' => 'bg-green-100 text-green-800',
        'lost' => 'bg-red-100 text-red-800',
        'on_hold' => 'bg-gray-100 text-gray-800'
    ];
    return $colors[$status] ?? 'bg-gray-100 text-gray-800';
}

/**
 * Get task status label
 */
function getTaskStatusLabel($status) {
    $labels = [
        'open' => 'Åpen',
        'completed' => 'Fullført',
        'postponed' => 'Utsatt'
    ];
    return $labels[$status] ?? $status;
}

/**
 * Get task priority label
 */
function getTaskPriorityLabel($priority) {
    $labels = [
        'low' => 'Lav',
        'medium' => 'Middels',
        'high' => 'Høy'
    ];
    return $labels[$priority] ?? $priority;
}

/**
 * Get task priority color
 */
function getTaskPriorityColor($priority) {
    $colors = [
        'low' => 'bg-gray-100 text-gray-800',
        'medium' => 'bg-yellow-100 text-yellow-800',
        'high' => 'bg-red-100 text-red-800'
    ];
    return $colors[$priority] ?? 'bg-gray-100 text-gray-800';
}

/**
 * Check if date is in the past
 */
function isOverdue($date) {
    return strtotime($date) < strtotime('today');
}

/**
 * Get file icon based on extension
 */
function getFileIcon($extension) {
    $icons = [
        'pdf' => '📄',
        'docx' => '📝',
        'xlsx' => '📊',
        'png' => '🖼️',
        'jpg' => '🖼️',
        'jpeg' => '🖼️',
        'txt' => '📃'
    ];
    return $icons[strtolower($extension)] ?? '📎';
}

/**
 * Format file size
 */
function formatFileSize($bytes) {
    $units = ['B', 'KB', 'MB', 'GB'];
    $unitIndex = 0;
    
    while ($bytes >= 1024 && $unitIndex < count($units) - 1) {
        $bytes /= 1024;
        $unitIndex++;
    }
    
    return round($bytes, 2) . ' ' . $units[$unitIndex];
}

/**
 * Redirect to URL
 */
function redirect($url) {
    header("Location: $url");
    exit;
}

/**
 * Get current URL path
 */
function currentPath() {
    return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
}

/**
 * Check if current path matches
 */
function isCurrentPath($path) {
    return strpos(currentPath(), $path) !== false;
}
