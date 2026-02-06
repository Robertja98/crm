<?php
session_start();
require_once 'csv_handler.php';

$schema = require __DIR__ . '/contact_schema.php';
$logFile = 'error_log.txt';
$targetFile = 'contacts.csv';

// Validate session data
$contacts = $_SESSION['import_preview'] ?? [];

if (!is_array($contacts) || empty($contacts)) {
    echo "<p style='color:red;'>No import data received or session expired.</p>";
    exit;
}

// Re-validate schema alignment
$validContacts = array_filter($contacts, function($row) use ($schema) {
    foreach ($schema as $col) {
        if (!array_key_exists($col, $row)) return false;
    }
    return true;
});

// Append to contacts.csv
$success = writeCSV($targetFile, $validContacts, $schema);

if ($success) {
    echo "<p style='color:green;'>Import complete. " . count($validContacts) . " contacts added.</p>";
    echo "<p><a href='contacts_list.php' class='btn'>← Back to Contacts</a></p>";
    unset($_SESSION['import_preview']); // Clear session after successful import
} else {
    $errorMessage = "[" . date('Y-m-d H:i:s') . "] Failed to write imported contacts.\n";
    file_put_contents($logFile, $errorMessage, FILE_APPEND);
    echo "<p style='color:red;'>Import failed. See error log.</p>";
}
?>
