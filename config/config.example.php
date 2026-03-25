<?php
/**
 * Simple CRM Configuration
 * 
 * IMPORTANT: Copy this file to config.php and update with your settings.
 * Never commit config.php with real credentials to version control.
 */

// Database configuration
define('DB_HOST', 'localhost');
define('DB_NAME', 'simple_crm');
define('DB_USER', 'crm_user');
define('DB_PASS', 'your_secure_password_here');
define('DB_CHARSET', 'utf8mb4');

// Application settings
define('APP_NAME', 'Simple CRM');
define('APP_VERSION', '1.0.0');
define('APP_URL', 'http://localhost/simple-crm'); // Update to your domain

// File upload settings
define('UPLOAD_DIR', __DIR__ . '/../uploads/attachments/');
define('MAX_FILE_SIZE', 10 * 1024 * 1024); // 10MB
define('ALLOWED_EXTENSIONS', ['pdf', 'docx', 'xlsx', 'png', 'jpg', 'jpeg', 'txt']);
define('ALLOWED_MIME_TYPES', [
    'application/pdf',
    'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'image/png',
    'image/jpeg',
    'text/plain'
]);

// BRREG API settings
define('BRREG_API_URL', 'https://data.brreg.no/enhetsregisteret/api/enheter/');

// Session settings are set in index.php BEFORE session_start()
// Do NOT set session ini settings here

// Timezone
date_default_timezone_set('Europe/Oslo');

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', '1');
