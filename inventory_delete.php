<?php
require_once 'db_mysql.php';
require_once __DIR__ . '/csrf_helper.php';
require_once __DIR__ . '/simple_auth/middleware.php';

$requestMethod = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
if ($requestMethod === 'POST') {
    $csrfToken = $_POST['csrf_token'] ?? '';
    if (!verifyCSRFToken($csrfToken)) {
        header('Location: inventory_list.php?error=invalid_request');
        exit;
    }

    $itemId = trim((string) ($_POST['item_id'] ?? ''));
    if ($itemId !== '') {
        $conn = get_mysql_connection();
        $stmt = $conn->prepare('DELETE FROM inventory WHERE item_id = ?');
        $stmt->bind_param('s', $itemId);
        $stmt->execute();
        $stmt->close();
        $conn->close();
    }
}

header('Location: inventory_list.php');
exit;
