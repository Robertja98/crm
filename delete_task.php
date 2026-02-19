<?php
require_once 'tasks_mysql.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    // Use update to set status to archived (soft delete)
    archive_task_mysql($id);
}
header('Location: index.php');
exit;
