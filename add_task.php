
<?php
require_once 'tasks_mysql.php';
require_once 'csrf_helper.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        echo "<div style='color:red;'><strong>Error:</strong> CSRF validation failed</div><a href='index.php'>Go back</a>";
        exit;
    }
    $title = trim($_POST['title']);
    $due_date = $_POST['due_date'];
    $status = $_POST['status'];
    $contact_id = isset($_POST['contact_id']) ? intval($_POST['contact_id']) : null;
    $opportunity_id = isset($_POST['opportunity_id']) ? intval($_POST['opportunity_id']) : null;
    $project_id = isset($_POST['project_id']) ? intval($_POST['project_id']) : null;
    $errors = [];

    if ($title === '') $errors[] = 'Task title is required.';
    if ($due_date === '') $errors[] = 'Due date is required.';
    elseif (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $due_date)) $errors[] = 'Invalid date format.';
    $valid_statuses = ['not_started', 'in_progress', 'waiting', 'review', 'completed', 'archived'];
    if (!in_array($status, $valid_statuses, true)) $errors[] = 'Invalid status selected.';

    if (!empty($errors)) {
        echo "<div style='color:red;'><strong>Error:</strong><ul>";
        foreach ($errors as $error) echo "<li>" . htmlspecialchars($error) . "</li>";
        echo "</ul></div><a href='index.php'>Go back</a>";
        exit;
    }

    $id = uniqid('task_', true);
    $priority = '';
    $assigned_to = '';
    $timestamp = date('Y-m-d H:i:s');
    $task = [
        'id' => $id,
        'title' => $title,
        'status' => $status,
        'priority' => $priority,
        'assigned_to' => $assigned_to,
        'due_date' => $due_date,
        'timestamp' => $timestamp,
        'contact_id' => $contact_id,
        'opportunity_id' => $opportunity_id,
        'project_id' => $project_id
    ];
    insert_task_mysql($task);
    header('Location: index.php');
    exit;
}
?>

<form method="POST" action="">
    <div style="background:#ff0;color:#000;padding:8px 12px;margin-bottom:10px;font-weight:bold;">DEBUG: add_task.php form loaded (2026-04-27)</div>
    <label>Title: <input type="text" name="title" required></label><br>
    <label>Due Date: <input type="date" name="due_date" required></label><br>
    <label for="task_status">Status:
        <select name="status" id="task_status" required>
            <option value="not_started">Not Started</option>
            <option value="in_progress">In Progress</option>
            <option value="waiting">Waiting/Blocked</option>
            <option value="review">Review</option>
            <option value="completed">Completed</option>
            <option value="archived">Archived</option>
        </select>
    </label><br>
    <?php if (isset($_GET['contact_id'])): ?>
        <input type="hidden" name="contact_id" value="<?= htmlspecialchars($_GET['contact_id']) ?>">
        <div>Linked to Contact ID: <?= htmlspecialchars($_GET['contact_id']) ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['opportunity_id'])): ?>
        <input type="hidden" name="opportunity_id" value="<?= htmlspecialchars($_GET['opportunity_id']) ?>">
        <div>Linked to Opportunity ID: <?= htmlspecialchars($_GET['opportunity_id']) ?></div>
    <?php endif; ?>
    <?php if (isset($_GET['project_id'])): ?>
        <input type="hidden" name="project_id" value="<?= htmlspecialchars($_GET['project_id']) ?>">
        <div>Linked to Project ID: <?= htmlspecialchars($_GET['project_id']) ?></div>
    <?php endif; ?>
    <button type="submit">Add Task</button>
</form>
