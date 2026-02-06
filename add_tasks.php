<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$title = $_POST['title'] ?? '';
if (trim($title) === '') {
    echo 'error: empty title';
    exit;
}

$filename = 'tasks.csv';
$headers = ['id','title','status','priority','assigned_to','due_date','timestamp'];

if (!file_exists($filename)) {
    $fp = fopen($filename, 'w');
    if ($fp === false) {
        echo 'error: cannot create file';
        exit;
    }
    fputcsv($fp, $headers);
    fclose($fp);
}

$id = uniqid('task_', true);
$status = 'incomplete';
$priority = '';
$assigned_to = '';
$due_date = '';
$timestamp = date('Y-m-d H:i:s');

$fp = fopen($filename, 'a');
if ($fp !== false) {
    fputcsv($fp, [$id, $title, $status, $priority, $assigned_to, $due_date, $timestamp]);
    fclose($fp);
    echo 'success';
} else {
    echo 'error: cannot open file for appending';
}
?>