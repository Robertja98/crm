<?php
require_once 'contact_validator.php';
require_once 'csv_handler.php';

$idToDelete = $_POST['id'] ?? null;
if (!$idToDelete) {
    echo "No contact ID provided.";
    exit;
}

$schema = require __DIR__ . '/contact_schema.php';
$contacts = readCSV('contacts.csv', $schema);

// Filter out the contact to delete
$filtered = array_filter($contacts, function($c) use ($idToDelete) {
    return $c['id'] !== $idToDelete;
});

// Defensive write: ensure header matches schema
$fp = fopen('contacts.csv', 'w');
fputcsv($fp, $schema);
foreach ($filtered as $row) {
    $line = [];
    foreach ($schema as $col) {
        $line[] = array_key_exists($col, $row) ? $row[$col] : '';
    }
    fputcsv($fp, $line);
}
fclose($fp);

header('Location: contacts_list.php');
exit;
?>
