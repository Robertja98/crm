<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$idToArchive = $_POST['id'] ?? '';
if (!$idToArchive) {
    echo 'error';
    exit;
}

$filename = 'tasks.csv';
$rows = [];
$found = false;

if (($handle = fopen($filename, 'r')) !== false) {
    $headers = fgetcsv($handle); // Read and store header
    $rows[] = $headers;

    while (($data = fgetcsv($handle)) !== false) {
        if ($data[0] === $idToArchive) {
            $data[2] = 'archived'; // Update status
            $found = true;
        }
        $rows[] = $data;
    }
    fclose($handle);
}

// Rewrite the CSV only if the task was found
if ($found && ($handle = fopen($filename, 'w')) !== false) {
    foreach ($rows as $row) {
        fputcsv($handle, $row);
    }
    fclose($handle);
    echo 'success';
} else {
    echo 'error';
}
?>
