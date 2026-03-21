<?php
/**
 * Simple CRM - Front Controller
 * All requests go through this file
 */

// Start session
session_start();

// Load configuration
require_once __DIR__ . '/../config/config.php';

// Load helpers
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/database.php';

// Simple routing
$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$path = trim($path, '/');
$parts = explode('/', $path);

// Get controller and action
$controller = $parts[0] ?? 'dashboard';
$action = $parts[1] ?? 'index';
$id = $parts[2] ?? null;

// Route mapping
$routes = [
    // Auth
    'auth/login' => ['controller' => 'AuthController', 'action' => 'login'],
    'auth/logout' => ['controller' => 'AuthController', 'action' => 'logout'],
    'auth/changePassword' => ['controller' => 'AuthController', 'action' => 'changePassword'],
    // Dashboard
    'dashboard' => ['controller' => 'DashboardController', 'action' => 'index'],
    
    // Customers
    'customers' => ['controller' => 'CustomerController', 'action' => 'list'],
    'customers/view' => ['controller' => 'CustomerController', 'action' => 'view'],
    'customers/create' => ['controller' => 'CustomerController', 'action' => 'create'],
    'customers/edit' => ['controller' => 'CustomerController', 'action' => 'edit'],
    'customers/delete' => ['controller' => 'CustomerController', 'action' => 'delete'],
    'customers/brreg' => ['controller' => 'CustomerController', 'action' => 'brregLookup'],
    
    // Contacts
    'contacts' => ['controller' => 'ContactController', 'action' => 'list'],
    'contacts/view' => ['controller' => 'ContactController', 'action' => 'view'],
    'contacts/create' => ['controller' => 'ContactController', 'action' => 'create'],
    'contacts/edit' => ['controller' => 'ContactController', 'action' => 'edit'],
    'contacts/delete' => ['controller' => 'ContactController', 'action' => 'delete'],
    
    // Activities
    'activities' => ['controller' => 'ActivityController', 'action' => 'list'],
    'activities/view' => ['controller' => 'ActivityController', 'action' => 'view'],
    'activities/create' => ['controller' => 'ActivityController', 'action' => 'create'],
    'activities/edit' => ['controller' => 'ActivityController', 'action' => 'edit'],
    'activities/delete' => ['controller' => 'ActivityController', 'action' => 'delete'],
    'activities/downloadAttachment' => ['controller' => 'ActivityController', 'action' => 'downloadAttachment'],
    'activities/deleteAttachment' => ['controller' => 'ActivityController', 'action' => 'deleteAttachment'],
    
    // Projects
    'projects' => ['controller' => 'ProjectController', 'action' => 'list'],
    'projects/create' => ['controller' => 'ProjectController', 'action' => 'create'],
    'projects/delete' => ['controller' => 'ProjectController', 'action' => 'delete'],
    
    // Deals
    'deals' => ['controller' => 'DealController', 'action' => 'list'],
    'deals/view' => ['controller' => 'DealController', 'action' => 'view'],
    'deals/create' => ['controller' => 'DealController', 'action' => 'create'],
    'deals/edit' => ['controller' => 'DealController', 'action' => 'edit'],
    'deals/delete' => ['controller' => 'DealController', 'action' => 'delete'],
    
    // Tasks
    'tasks' => ['controller' => 'TaskController', 'action' => 'list'],
    'tasks/view' => ['controller' => 'TaskController', 'action' => 'view'],
    'tasks/create' => ['controller' => 'TaskController', 'action' => 'create'],
    'tasks/edit' => ['controller' => 'TaskController', 'action' => 'edit'],
    'tasks/delete' => ['controller' => 'TaskController', 'action' => 'delete'],
    'tasks/complete' => ['controller' => 'TaskController', 'action' => 'complete'],
    
    // Search
    'search' => ['controller' => 'SearchController', 'action' => 'search'],
];

// Build route key
$routeKey = $controller;
if ($action !== 'index' || $id) {
    $routeKey .= '/' . $action;
}

// Find route
if (isset($routes[$routeKey])) {
    $route = $routes[$routeKey];
    $controllerName = $route['controller'];
    $actionName = $route['action'];
} else {
    // Default to dashboard if route not found
    $controllerName = 'DashboardController';
    $actionName = 'index';
}

// Check authentication (skip for login/logout)
$publicRoutes = ['AuthController'];
if (!in_array($controllerName, $publicRoutes) && !isset($_SESSION['user_id'])) {
    setFlashMessage('error', 'Du må logge inn for å se denne siden');
    redirect(APP_URL . '/auth/login');
}

// Create default user if no users exist (first time setup)
require_once __DIR__ . '/../models/User.php';
$userModel = new User();
$userModel->createDefaultUser();

// Load controller file
$controllerFile = __DIR__ . '/../controllers/' . $controllerName . '.php';
if (file_exists($controllerFile)) {
    require_once $controllerFile;
} else {
    die("Controller not found: $controllerName");
}

// Instantiate controller and call action
if (class_exists($controllerName)) {
    $controller = new $controllerName();
    
    if (method_exists($controller, $actionName)) {
        try {
            $controller->$actionName($id);
        } catch (Exception $e) {
            error_log("Action error: " . $e->getMessage());
            setFlashMessage('error', 'En feil oppstod: ' . $e->getMessage());
            redirect(APP_URL);
        }
    } else {
        die("Action not found: $actionName");
    }
} else {
    die("Controller class not found: $controllerName");
}
