<?php
require_once 'db_mysql.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $itemId = trim($_POST['item_id'] ?? '');
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
