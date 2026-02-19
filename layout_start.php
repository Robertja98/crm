<?php
// Force correct session name before any output or session_start
if (session_status() === PHP_SESSION_NONE) {
    session_name('CRM_SESSION');
}
// Layout start code here
require_once __DIR__ . '/simple_auth/middleware.php';
require_once __DIR__ . '/backup_handler.php';
require_once __DIR__ . '/error_handler.php';
require_once __DIR__ . '/csrf_helper.php';
require_once __DIR__ . '/sanitize_helper.php';
require_once __DIR__ . '/audit_handler.php';

// Initialize CSRF token for this session
initializeCSRFToken();

// Security headers
header('Content-Type: text/html; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
header('X-Frame-Options: DENY');
header('X-XSS-Protection: 1; mode=block');
header('Referrer-Policy: strict-origin-when-cross-origin');
header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net; img-src 'self' data:; font-src 'self' https://cdn.jsdelivr.net; connect-src 'self' https://cdn.jsdelivr.net;");

// Disable caching for pages with sensitive data
if (strpos($_SERVER['REQUEST_URI'], 'contact') !== false) {
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
    header('Pragma: no-cache');
}

$pageTitle = $pageTitle ?? 'CRM';
$currentPage = basename($_SERVER['SCRIPT_NAME']);
?>
<!DOCTYPE html>
<html lang="en-CA">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="CRM system for managing contacts and customer interactions.">
  <meta name="author" content="Eclipse Water Technologies">
  <meta name="robots" content="index, follow">
  <meta property="og:title" content="<?= htmlspecialchars($pageTitle) ?>">
  <meta property="og:type" content="website">
  <meta property="og:url" content="https://yourdomain.com/<?= htmlspecialchars($currentPage) ?>">
  <meta property="og:image" content="https://yourdomain.com/images/preview.png">
  <link rel="icon" href="/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <link rel="stylesheet" href="crm_bootstrap.css?v=20260215">
    <link rel="stylesheet" href="styles.css?v=20251002">
    <link rel="stylesheet" href="css/modern-sidebar.css?v=20260213-2">
    <link rel="stylesheet" href="css/modern-components.css?v=20260213">
  <script src="https://cdn.jsdelivr.net/npm/dompurify@latest/dist/purify.min.js"></script>
  <title><?= htmlspecialchars($pageTitle) ?></title>
</head>
<body>
<?php include_once 'navbar-sidebar.php'; ?>

<?php
// Display error messages if present
if (isset($_GET['error'])) {
    $errorMessages = [
        // 'access_denied' admin message removed
        'not_found' => 'The requested page was not found.',
        'invalid_request' => 'Invalid request received.',
    ];
    $errorType = $_GET['error'];
    if (isset($errorMessages[$errorType])) {
        echo '<div style="background:#fee;border:1px solid #fcc;padding:15px;margin:20px;border-radius:4px;color:#c33;">';
        echo htmlspecialchars($errorMessages[$errorType]);
        echo '</div>';
    }
}

// Display success messages if present
if (isset($_GET['success'])) {
    $successMessages = [
        'updated' => '✓ Successfully updated.',
        'created' => '✓ Successfully created.',
        'deleted' => '✓ Successfully deleted.',
    ];
    $successType = $_GET['success'];
    if (isset($successMessages[$successType])) {
        echo '<div style="background:#efe;border:1px solid #cfc;padding:15px;margin:20px;border-radius:4px;color:#363;">';
        echo htmlspecialchars($successMessages[$successType]);
        echo '</div>';
    }
}
?>
