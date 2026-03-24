<?php
// mmda-revenue/includes/auth.php
session_start();

/**
 * Check if a user is logged in
 */
function is_logged_in() {
    return isset($_SESSION['user_id']);
}

/**
 * Require login to access a page
 */
function require_login() {
    if (!is_logged_in()) {
        header("Location: ../public/index.php");
        exit();
    }
}

/**
 * Check for specific roles
 */
function has_role($role_name) {
    if (!is_logged_in()) {
        return false;
    }
    return $_SESSION['role_name'] === $role_name;
}

/**
 * Require a specific role to access a page
 */
function require_role($role_name) {
    require_login();
    if ($_SESSION['role_name'] !== $role_name) {
        // Forbidden or redirect to unauthorized
        $_SESSION['flash_message'] = "Unauthorized access.";
        $_SESSION['flash_type'] = "danger";
        header("Location: ../public/index.php");
        exit();
    }
}

/**
 * Get dashboard URL based on role
 */
function get_dashboard_url($role_name) {
    switch ($role_name) {
        case 'Admin':
            return '../admin/dashboard.php';
        case 'Collector':
            return '../collector/dashboard.php';
        case 'Citizen':
            return '../user/dashboard.php';
        default:
            return '../public/index.php';
    }
}
?>
