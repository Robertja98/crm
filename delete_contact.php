<?php
// Start output buffering to prevent accidental output before header() calls
ob_start();

require_once 'layout_start.php';
require_once 'contact_validator.php';
require_once 'db_mysql.php';
require_once 'error_handler.php';

// ✅ CSRF Protection: Verify token on POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        showError('CSRF validation failed', 'The CSRF token is missing or invalid. Please try again.');
        logWarning('CSRF token validation failed on delete_contact', ['ip' => $_SERVER['REMOTE_ADDR']]);
        exit;
    }
}

// Use contact_id for consistency
$idToDelete = $_POST['contact_id'] ?? null;
if (!$idToDelete) {
    showError('No contact ID provided', 'A contact ID must be specified to delete a contact.');
    exit;
}


$schema = require __DIR__ . '/contact_schema.php';
$deleted_contact = null;


try {
    $conn = get_mysql_connection();
    // Find and capture contact before deletion (for audit log)
    $stmt = $conn->prepare("SELECT * FROM contacts WHERE contact_id = ? LIMIT 1");
    $stmt->bind_param('s', $idToDelete);
    $stmt->execute();
    $result = $stmt->get_result();
    $deleted_contact = $result->fetch_assoc();
    $stmt->close();

    if ($deleted_contact) {
        // Delete the contact
        $delStmt = $conn->prepare("DELETE FROM contacts WHERE contact_id = ?");
        $delStmt->bind_param('s', $idToDelete);
        $delStmt->execute();
        $delStmt->close();

        auditDeleteContact($deleted_contact);
        logInfo('Contact deleted successfully', [
            'email' => $deleted_contact['email'],
            'name' => ($deleted_contact['first_name'] ?? '') . ' ' . ($deleted_contact['last_name'] ?? ''),
        ]);
    }
    $conn->close();

    // Redirect to contacts_list.php (PHP header, JS, and HTML fallback)
    if (!headers_sent()) {
        header('Location: contacts_list.php');
        exit;
    } else {
        echo '<meta http-equiv="refresh" content="0;url=contacts_list.php">';
        echo '<script>window.location.href="contacts_list.php";</script>';
        echo '<p>If you are not redirected, <a href="contacts_list.php">click here</a>.</p>';
        exit;
    }
} catch (Exception $e) {
    showError('Error deleting contact', htmlspecialchars($e->getMessage()));
    logError('Contact deletion failed', [
        'id' => $idToDelete,
        'exception' => $e->getMessage(),
    ]);
    // ✅ AUDIT: Log failed deletion
    if ($deleted_contact) {
        auditDeleteContact($deleted_contact);
    }
    echo "<p><a href='contacts_list.php'>Go back</a></p>";
    exit;
}
?>

