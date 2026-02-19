<?php
/**
 * CSRF Protection Helper Functions
 * Prevents Cross-Site Request Forgery attacks
 */

// Ensure session is started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

/**
 * Initialize CSRF token in session
 */
function initializeCSRFToken() {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Get the current CSRF token
 */
function getCSRFToken() {
    initializeCSRFToken();
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token from POST/REQUEST
 */
function verifyCSRFToken($token) {
    if (empty($_SESSION['csrf_token'])) {
        return false;
    }
    return hash_equals($_SESSION['csrf_token'], $token ?? '');
}

/**
 * Render hidden CSRF input field for HTML forms
 */
function renderCSRFInput() {
    $token = htmlspecialchars(getCSRFToken(), ENT_QUOTES, 'UTF-8');
    echo '<input type="hidden" name="csrf_token" value="' . $token . '">';
}

/**
 * Get CSRF token as escaped attribute value (for data attributes, etc)
 */
function getCSRFTokenAttribute() {
    return htmlspecialchars(getCSRFToken(), ENT_QUOTES, 'UTF-8');
}
?>
