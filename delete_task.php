<?php
require_once 'tasks_mysql.php';
require_once __DIR__ . '/csrf_helper.php';
require_once __DIR__ . '/simple_auth/middleware.php';

$requestMethod = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
if ($requestMethod !== 'POST') {
    header('Location: index.php?error=invalid_request');
    exit;
}

if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    header('Location: index.php?error=invalid_request');
    exit;
}

$id = trim((string) ($_POST['id'] ?? ''));
if ($id !== '') {
    // Use update to set status to archived (soft delete)
    archive_task_mysql($id);
}

header('Location: index.php');
exit;
