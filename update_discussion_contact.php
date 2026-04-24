<?php
// update_discussion_contact.php - Update manual_contact_id for a discussion log entry
require_once __DIR__ . '/simple_auth/middleware.php';
require_once 'db_mysql.php';
require_once 'csrf_helper.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'], $_POST['manual_contact_id'])) {
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        http_response_code(403);
        echo 'CSRF validation failed';
        exit;
    }
    $id = intval($_POST['id']);
    $manual_contact_id = trim($_POST['manual_contact_id']);
    $conn = get_mysql_connection();
    $stmt = $conn->prepare('UPDATE discussion_log SET manual_contact_id = ? WHERE id = ?');
    $stmt->bind_param('si', $manual_contact_id, $id);
    $stmt->execute();
    $stmt->close();
    $conn->close();
    header('Location: discussion.php');
    exit;
}
http_response_code(400);
echo 'Invalid request.';
