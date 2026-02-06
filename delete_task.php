<?php
require_once 'csv_handler.php';

$filename = 'tasks.csv';
$schema = ['title', 'due_date', 'status', 'timestamp'];

if (isset($_GET['timestamp'])) {
    deleteTask($filename, $schema, $_GET['timestamp']);
}
header('Location: index.php');
exit;
