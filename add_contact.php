<?php
require_once 'contact_validator.php';
require_once 'csv_handler.php';
require_once 'forecast_calc.php';

$filename = 'contacts.csv';
$logFile = 'error_log.txt';
$schema = require __DIR__ . '/contact_schema.php';

$timestamp = date('Y-m-d H:i:s');
$newContact = [
    'id' => uniqid(),
    'first_name' => $_POST['first_name'] ?? '',
    'last_name' => $_POST['last_name'] ?? '',
    'company' => $_POST['company'] ?? '',
    'address_1' => $_POST['address_1'] ?? '',
    'address_2' => $_POST['address_2'] ?? '', 
    'city' => $_POST['city'] ?? '',
    'postal_code' => $_POST['postal_code'] ?? '',
    'province' => $_POST['province'] ?? '',
    'country' => $_POST['country'] ?? '',
    'phone' => $_POST['phone'] ?? '',
    'email' => $_POST['email'] ?? '',
    'created_at' => $timestamp,
    'last_modified' => $timestamp,
    'source' => 'manual_entry'
];

// Validate contact
$errors = validateContact($newContact);
if (!empty($errors)) {
    echo "<p style='color:red;'>Invalid contact data:</p>";
    foreach ($errors as $field => $msg) {
        echo "<p>Error in $field: $msg</p>";
    }

    // Log errors
    $errorMessage = "[$timestamp] Contact validation failed for email: {$newContact['email']}\n";
    $errorMessage .= print_r($errors, true) . "\n";
    file_put_contents($logFile, $errorMessage, FILE_APPEND);

    echo "<p><a href='contact_view.php'>Go back</a></p>";
    exit;
}

// Load existing contacts
$contacts = readCSV($filename, $schema);

// Check for duplicates
foreach ($contacts as $contact) {
    if ($contact['email'] === $newContact['email']) {
        echo "<p style='color:red;'>Duplicate email detected. Contact not saved.</p>";
        $duplicateMessage = "[$timestamp] Duplicate email detected: {$newContact['email']}\n";
        file_put_contents($logFile, $duplicateMessage, FILE_APPEND);
        echo "<p><a href='contact_view.php'>Go back</a></p>";
        exit;
    }
}

// Save contact
$contacts[] = $newContact;
writeCSV($filename, $contacts, $schema);

// Log success
$successMessage = "[$timestamp] Contact saved: {$newContact['email']}\n";
file_put_contents($logFile, $successMessage, FILE_APPEND);

// Redirect to confirmation
header('Location: contact_success.php');
exit;
?>
