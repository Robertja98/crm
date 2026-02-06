<?php
require_once 'csv_handler.php';
require_once 'csv_export.php';

$filename = 'contacts.csv';
$schema = require __DIR__ . '/contact_schema.php';
$contacts = readCSV($filename, $schema);

// Build filters from query string using schema
$filters = [];
foreach ($schema as $field) {
    if (!empty($_GET[$field])) {
        $filters[$field] = $_GET[$field];
    }
}

// Schema-safe export function
function exportCSVFiltered($filename, $rows, $filters = [], $schema = []) {
    $filtered = array_filter($rows, function($row) use ($filters) {
        foreach ($filters as $key => $value) {
            if ($row[$key] !== $value) return false;
        }
        return true;
    });
    return writeCSV($filename, array_values($filtered), $schema);
}

// Export filtered contacts to a new file
$exported = exportCSVFiltered('contacts_export.csv', $contacts, $filters, $schema);
echo $exported ? "Export complete." : "No matching contacts.";
?>
