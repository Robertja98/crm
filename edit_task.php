<?php
require_once 'csv_handler.php';

$filename = 'tasks.csv';
$schema = ['title', 'due_date', 'status', 'timestamp'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updatedTask = [
        $_POST['title'],
        $_POST['due_date'],
        $_POST['status'],
        $_POST['timestamp']
    ];
    updateTask($filename, $schema, $_POST['timestamp'], $updatedTask);
    header('Location: index.php');
    exit;
}

$timestamp = $_GET['timestamp'] ?? '';
$tasks = readCSV($filename, $schema);
$taskToEdit = null;
foreach ($tasks as $task) {
    if ($task['timestamp'] === $timestamp) {
        $taskToEdit = $task;
        break;
    }
}
if (!$taskToEdit) {
    echo "Task not found.";
    exit;
}
?>

<h3>Edit Task</h3>
<form method="POST">
  <input type="hidden" name="timestamp" value="<?= htmlspecialchars($taskToEdit['timestamp']) ?>">
  <input type="text" name="title" value="<?= htmlspecialchars($taskToEdit['title']) ?>" required>
  <input type="date" name="due_date" value="<?= htmlspecialchars($taskToEdit['due_date']) ?>" required>
  <select name="status" required>
    <option value="pending" <?= $taskToEdit['status'] === 'pending' ? 'selected' : '' ?>>Pending</option>
    <option value="completed" <?= $taskToEdit['status'] === 'completed' ? 'selected' : '' ?>>Completed</option>
  </select>
  <button type="submit">Update Task</button>
</form>
