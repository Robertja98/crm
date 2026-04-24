<?php
/**
 * CSRF Protection Helper Functions
 * Prevents Cross-Site Request Forgery attacks
 */

/**
 * Ensure session is available for CSRF checks, matching auth session settings when possible.
 */
function ensureCSRFSessionStarted() {
    if (session_status() === PHP_SESSION_ACTIVE) {
        return;
    }

    $authConfigPath = __DIR__ . '/simple_auth/config.php';
    if (file_exists($authConfigPath)) {
        $config = require $authConfigPath;
        $security = $config['security'] ?? [];

        // Match Auth::initSession() so CSRF reads/writes the same session store.
        $localSessionDir = __DIR__ . '/simple_auth/sessions';
        if (!is_dir($localSessionDir)) {
            mkdir($localSessionDir, 0755, true);
        }
        session_save_path($localSessionDir);
        ini_set('session.use_strict_mode', '1');

        if (!empty($security['session_name'])) {
            session_name($security['session_name']);
        }

        if (PHP_VERSION_ID >= 70300) {
            session_set_cookie_params([
                'lifetime' => $security['session_lifetime'] ?? 86400,
                'path' => '/',
                'domain' => '',
                'secure' => $security['session_cookie_secure'] ?? false,
                'httponly' => $security['session_cookie_httponly'] ?? true,
                'samesite' => $security['session_cookie_samesite'] ?? 'Lax',
            ]);
        }
    }

    if (!headers_sent()) {
        session_start();
    }
}


/**
 * Initialize CSRF token in session
 */
function initializeCSRFToken() {
    ensureCSRFSessionStarted();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/**
 * Get the current CSRF token
 */
function getCSRFToken() {
    ensureCSRFSessionStarted();
    initializeCSRFToken();
    return $_SESSION['csrf_token'];
}

/**
 * Verify CSRF token from POST/REQUEST
 */
function verifyCSRFToken($token) {
    ensureCSRFSessionStarted();
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
