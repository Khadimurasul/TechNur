<?php
// mmda-revenue/includes/functions.php

/**
 * Validate input and sanitize
 */
function sanitize_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    return $data;
}

/**
 * Generate a unique transaction reference
 */
function generate_transaction_ref() {
    return 'MMDA-' . strtoupper(uniqid());
}

/**
 * Generate a unique receipt number
 */
function generate_receipt_number() {
    return 'REC-' . time() . '-' . rand(1000, 9999);
}

/**
 * Redirect with a message
 */
function redirect($url, $message = null, $type = 'success') {
    if ($message) {
        $_SESSION['flash_message'] = $message;
        $_SESSION['flash_type'] = $type;
    }
    header("Location: $url");
    exit();
}

/**
 * Display flash messages
 */
function display_flash() {
    if (isset($_SESSION['flash_message'])) {
        $message = $_SESSION['flash_message'];
        $type = $_SESSION['flash_type'] ?? 'success';
        echo "<div class='alert alert-$type alert-dismissible fade show' role='alert'>
                $message
                <button type='button' class='btn-close' data-bs-dismiss='alert' aria-label='Close'></button>
              </div>";
        unset($_SESSION['flash_message']);
        unset($_SESSION['flash_type']);
    }
}

/**
 * Format currency
 */
function format_currency($amount) {
    return 'GH₵ ' . number_format($amount, 2);
}
?>
