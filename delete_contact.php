<?php
require_once 'layout_start.php';
require_once 'contact_validator.php';
// CSV handler removed for production
require_once 'error_handler.php';

// ✅ CSRF Protection: Verify token on POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        showError('CSRF validation failed');
        logWarning('CSRF token validation failed on delete_contact', ['ip' => $_SERVER['REMOTE_ADDR']]);
        exit;
    }
}

$idToDelete = $_POST['id'] ?? null;
if (!$idToDelete) {
    showError('No contact ID provided');
    exit;
}

$schema = require __DIR__ . '/contact_schema.php';
$deleted_contact = null;

try {
    // ✅ LOCKED READ-MODIFY-WRITE: Prevents race condition data loss
    // readModifyWriteCSV('contacts.csv', $schema, function($contacts) use ($idToDelete) { // CSV logic removed
        global $deleted_contact;
        
        // Find and capture contact before deletion (for audit log)
        $deleted_contact = null;
        foreach ($contacts as $contact) {
            if ($contact['id'] === $idToDelete) {
                $deleted_contact = $contact;
                break;
            }
        }
        
        // Filter out the contact to delete
        $filtered = array_filter($contacts, function($c) use ($idToDelete) {
            return $c['id'] !== $idToDelete;
        });
        
        // Reindex array to maintain clean structure
        return array_values($filtered);
    });
    
    // ✅ AUDIT: Log contact deletion
    if ($deleted_contact) {
        auditDeleteContact($deleted_contact);
        logInfo('Contact deleted successfully', [
            'email' => $deleted_contact['email'],
            'name' => ($deleted_contact['first_name'] ?? '') . ' ' . ($deleted_contact['last_name'] ?? ''),
        ]);
    }
    
    header('Location: contacts_list.php');
    exit;
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

