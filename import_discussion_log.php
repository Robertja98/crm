<?php
// import_discussion_log.php
// Import discussion_log.csv into MySQL and update customer records

require_once 'db_mysql.php';
$discussionSchema = require __DIR__ . '/discussion_schema.php';
$customerSchema = require __DIR__ . '/customer_schema.php';

$csvFile = __DIR__ . '/discussion_log.csv';
if (!file_exists($csvFile)) {
    die('discussion_log.csv not found');
}

$conn = get_mysql_connection();
$handle = fopen($csvFile, 'r');
$header = fgetcsv($handle);
$count = 0;
while (($row = fgetcsv($handle)) !== false) {
    $data = array_combine($header, $row);
    // Insert into discussion_log table
    $fields = [];
    $placeholders = [];
    $values = [];
    foreach ($discussionSchema as $col) {
        $fields[] = "`$col`";
        $placeholders[] = '?';
        $values[] = $data[$col] ?? null;
    }
    $sql = "INSERT INTO discussion_log (" . implode(",", $fields) . ") VALUES (" . implode(",", $placeholders) . ")";
    $stmt = $conn->prepare($sql);
    $types = str_repeat('s', count($values));
    $stmt->bind_param($types, ...$values);
    $stmt->execute();
    $stmt->close();
    $count++;
    // Optionally update customer record (example: update last_modified)
    if (!empty($data['contact_id'])) {
        $update = $conn->prepare("UPDATE customers SET last_modified = ? WHERE customer_id = ?");
        $update->bind_param('ss', $data['timestamp'], $data['contact_id']);
        $update->execute();
        $update->close();
    }
}
fclose($handle);
$conn->close();
echo "Imported $count discussion log entries and updated customers.";
