<?php
session_start();
require_once 'csv_handler.php';
require_once 'error_handler.php';
require_once 'contact_validator.php';

$schema = require __DIR__ . '/contact_schema.php';
$targetFile = 'contacts.csv';

// CSRF token verification
if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    showError('CSRF token validation failed. Import cancelled.');
    logError('CSRF token validation failed during import', ['ip' => $_SERVER['REMOTE_ADDR']]);
    exit;
}

// Validate session data
$contacts = $_SESSION['import_preview'] ?? [];

if (!is_array($contacts) || empty($contacts)) {
    echo "<div class='container'>";
    showError('No import data received or session expired. <a href="import_contacts.php">Try again</a>');
    logWarning('Import failed: no data in session', ['ip' => $_SERVER['REMOTE_ADDR']]);
    echo "</div>";
    exit;
}

echo "<div class='container'>";
echo "<h2>Commit Import</h2>";

// Re-validate each contact before import (safety check)
$validContacts = [];
$importErrors = [];

foreach ($contacts as $index => $contact) {
    $contact_errors = validateContact($contact);
    if (empty($contact_errors)) {
        $validContacts[] = $contact;
    } else {
        $importErrors[$index] = $contact_errors;
    }
}

if (!empty($importErrors)) {
    showError('Some contacts failed final validation. Import aborted.');
    echo "<p><strong>Failed contacts:</strong></p>";
    echo "<ul>";
    foreach ($importErrors as $idx => $errors) {
        echo "<li>Contact " . ($idx + 1) . ": " . implode(", ", $errors) . "</li>";
    }
    echo "</ul>";
    logError('Import failed during validation', ['count' => count($importErrors), 'total' => count($contacts)]);
    echo "<p><a href='import_contacts.php'>← Back to Import</a></p>";
    echo "</div>";
    exit;
}

// Final check: Re-validate schema alignment
if (!empty($validContacts)) {
    $schema_valid = true;
    foreach ($validContacts as $contact) {
        foreach ($schema as $col) {
            if (!array_key_exists($col, $contact)) {
                $schema_valid = false;
                break;
            }
        }
        if (!$schema_valid) break;
    }
    
    if (!$schema_valid) {
        showError('Contact schema mismatch. Import aborted.');
        logError('Schema validation failed during import', ['schemas' => json_encode($schema)]);
        echo "<p><a href='import_contacts.php'>← Back to Import</a></p>";
        echo "</div>";
        exit;
    }
}

// Create backup before import
if (file_exists($targetFile) && function_exists('createBackup')) {
    $backup_result = createBackup($targetFile);
    if (!$backup_result) {
        showError('Failed to create backup. Import cancelled for safety.');
        logError('Backup creation failed during import', ['file' => $targetFile]);
        echo "<p><a href='import_contacts.php'>← Back to Import</a></p>";
        echo "</div>";
        exit;
    }
}

// Attempt to write/append contacts
try {
    $success = writeCSV($targetFile, $validContacts, $schema);
    
    if ($success) {
        showSuccess('Import complete. ' . count($validContacts) . ' contacts added successfully.');
        logInfo('Import successful', ['count' => count($validContacts), 'user_id' => $_SESSION['user_id'] ?? 'unknown']);
        
        // ✅ AUDIT: Log bulk import
        auditImport(count($validContacts), 'success');
        
        echo "<p><a href='contacts_list.php' class='btn'>← Back to Contacts</a></p>";
        unset($_SESSION['import_preview']);
    } else {
        showError('Failed to write contacts to file. Import may have failed.');
        logError('Write operation failed during import', ['file' => $targetFile, 'count' => count($validContacts)]);
        
        // ✅ AUDIT: Log failed import
        auditImport(count($validContacts), 'failed', 'Write operation failed');
        
        echo "<p><a href='import_contacts.php'>← Back to Import</a></p>";
    }
} catch (Exception $e) {
    showError('Unexpected error during import: ' . htmlspecialchars($e->getMessage()));
    logError('Exception during import', ['error' => $e->getMessage(), 'code' => $e->getCode()]);
    
    // ✅ AUDIT: Log failed import with exception
    auditImport(count($validContacts), 'failed', $e->getMessage());
    
    echo "<p><a href='import_contacts.php'>← Back to Import</a></p>";
}

echo "</div>";
?>

