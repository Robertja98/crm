<?php

function readCSV($filename, $schema) {
    // If given path does not exist, try relative to this library's directory
    if (!file_exists($filename)) {
        $alt = __DIR__ . '/' . ltrim($filename, '/');
        if (file_exists($alt)) {
            $filename = $alt;
        } else {
            error_log("csv_handler: File not found: $filename");
            return [];
        }
    }

    $rows = [];
    if (($handle = fopen($filename, 'r')) !== false) {
        $headers = fgetcsv($handle);
        if ($headers === false) {
            echo "Failed to read headers.";
            return [];
        }

        while (($data = fgetcsv($handle)) !== false) {
            if (count($data) !== count($headers)) {
                echo "Row length mismatch: " . implode(',', $data);
                continue;
            }
            $row = array_combine($headers, $data);
            $rows[] = $row;
        }
        fclose($handle);
    }
    return $rows;
}

function writeCSV($filename, $data, $schema) {
    // If filename is a bare name (no directory), write into this library's directory
    if (basename($filename) === $filename) {
        $filename = __DIR__ . '/' . $filename;
    }

    $file = fopen($filename, 'w');
    if ($file === false) throw new Exception("Unable to open file for writing: $filename");

    // ✅ Write header row first
    fputcsv($file, $schema);

    foreach ($data as $row) {
        $csvRow = [];
        foreach ($schema as $field) {
            $value = $row[$field] ?? '';
            // Prevent CSV injection
            if (preg_match('/^[=+\-@]/', $value)) {
                $value = "'" . $value;
            }
            $csvRow[] = $value;
        }
        fputcsv($file, $csvRow);
    }

    fclose($file);
}


function appendCSV($filename, $row) {
    // If filename is a bare name (no directory), write into this library's directory
    if (basename($filename) === $filename) {
        $filename = __DIR__ . '/' . $filename;
    }

    $file = fopen($filename, 'a');
    if ($file === false) throw new Exception("Unable to open file for writing: $filename");
    fputcsv($file, $row);
    fclose($file);
}
?>