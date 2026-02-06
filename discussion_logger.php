<?php
// discussion_logger.php

// Set plain text response headers
header('Content-Type: text/plain; charset=utf-8');
header('X-Content-Type-Options: nosniff');

// Log raw POST data for debugging
file_put_contents('debug_log.txt', print_r($_POST, true) . PHP_EOL, FILE_APPEND);

// Use $_POST directly
$data = $_POST;

// Define the logging function
function logDiscussionEntry(array $data): bool {
    $file = 'discussion_log.csv';
    $logFile = 'error_log.txt';
    $schema = require __DIR__ . '/discussion_schema.php';

    // Build row using schema order
    $row = [];
    foreach ($schema as $col) {
        switch ($col) {
            case 'timestamp':
                $row[] = date('Y-m-d H:i');
                break;
            case 'entry_text':
                $row[] = isset($data[$col]) ? str_replace(["\r", "\n"], ' ', $data[$col]) : '';
                break;
            case 'author':
                $row[] = $data[$col] ?? 'System';
                break;
            case 'linked_opportunity_id':
                $row[] = $data[$col] ?? '';
                break;
            case 'visibility':
                $row[] = $data[$col] ?? 'private';
                break;
            default:
                $row[] = $data[$col] ?? '';
        }
    }

    // Try to append to CSV
    if (($fp = fopen($file, 'a')) !== false) {
        fputcsv($fp, $row);
        fclose($fp);
        return true;
    }

    // Log error if write fails
    $errorMessage = "[" . date('Y-m-d H:i:s') . "] Failed to write to $file for contact_id: {$data['contact_id']}\n";
    file_put_contents($logFile, $errorMessage, FILE_APPEND);

    return false;
}

// Output plain success or error message
if (!empty($data) && logDiscussionEntry($data)) {
    echo "Logged successfully";
} else {
    echo "Logging failed or invalid data";
}
?>
