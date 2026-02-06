<?php
require_once 'csv_handler.php';

$schemaFile = __DIR__ . '/contact_schema.php';
$schema = file_exists($schemaFile) ? require $schemaFile : [];
if (!is_array($schema)) {
    die('Error: contact_schema.php must return an array.');
}

$contacts = readCSV('contacts.csv', $schema);
if (!is_array($contacts)) {
    $contacts = [];
}

$selectedIds = $_POST['selected_ids'] ?? [];
$action = $_POST['action'] ?? '';
$tagToAssign = $_POST['assign_tag'] ?? '';
$statusToAssign = $_POST['assign_status'] ?? '';

if (!is_array($selectedIds) || empty($action)) {
    die('Error: No contacts selected or no action specified.');
}

$updatedContacts = [];
$exportRows = [];

foreach ($contacts as $contact) {
    $id = $contact['id'] ?? '';
    $isSelected = in_array($id, $selectedIds);

    if ($action === 'delete' && $isSelected) {
        continue; // Skip this contact (delete)
    }

    if ($action === 'export' && $isSelected) {
        $row = [];
        foreach ($schema as $field) {
            $row[] = $contact[$field] ?? '';
        }
        $exportRows[] = $row;
    }

    if ($action === 'assign_tag' && $isSelected && $tagToAssign !== '') {
        $existingTags = array_map('trim', explode(',', $contact['tags'] ?? ''));
        if (!in_array($tagToAssign, $existingTags)) {
            $existingTags[] = $tagToAssign;
        }
        $contact['tags'] = implode(', ', $existingTags);
    }

    if ($action === 'assign_status' && $isSelected && $statusToAssign !== '') {
        $contact['status'] = $statusToAssign;
    }

    $updatedContacts[] = $contact;
}

if ($action === 'export') {
    $filename = 'contacts_bulk_export_' . date('Ymd_His') . '.csv';
    header('Content-Type: text/csv');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    $output = fopen('php://output', 'w');
    fputcsv($output, $schema);
    foreach ($exportRows as $row) {
        fputcsv($output, $row);
    }
    fclose($output);
    exit;
} else {
    writeCSV('contacts.csv', $updatedContacts, $schema);
    header('Location: contacts_list.php');
    exit;
}
?>