
<?php
require_once 'tasks_mysql.php';

$timestamp = $_GET['timestamp'] ?? '';
$tasks = fetch_tasks_mysql(['timestamp' => $timestamp]);
$taskToEdit = $tasks ? $tasks[0] : null;
if (!$taskToEdit) {
    echo "Task not found.";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fields = [
        'title' => $_POST['title'],
        'due_date' => $_POST['due_date'],
        'status' => $_POST['status'],
        'timestamp' => $_POST['timestamp']
    ];
    update_task_mysql($taskToEdit['id'], $fields);
    header('Location: index.php');
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
