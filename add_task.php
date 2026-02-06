<?php
require_once 'csv_handler.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $due_date = $_POST['due_date'];
    $status = $_POST['status'];
    $errors = [];

    if ($title === '') $errors[] = 'Task title is required.';
    if ($due_date === '') $errors[] = 'Due date is required.';
    elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $due_date)) $errors[] = 'Invalid date format.';
    if ($status !== 'pending' && $status !== 'completed') $errors[] = 'Invalid status selected.';

    if (!empty($errors)) {
        echo "<div style='color:red;'><strong>Error:</strong><ul>";
        foreach ($errors as $error) echo "<li>" . htmlspecialchars($error) . "</li>";
        echo "</ul></div><a href='index.php'>Go back</a>";
        exit;
    }

    $filename = 'tasks.csv';
    $newTask = [$title, $due_date, $status, date('Y-m-d H:i:s')];
    appendCSV($filename, $newTask);
    header('Location: index.php');
    exit;
}
?>
