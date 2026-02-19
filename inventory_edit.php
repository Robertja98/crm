<?php
// inventory_edit.php - Edit inventory item
require_once 'db_mysql.php';
$schema = require __DIR__ . '/inventory_schema.php';

$item_id = $_GET['item_id'] ?? '';
if (!$item_id) {
    die('No item_id specified.');
}

$conn = get_mysql_connection();
$fields = implode(',', array_map(function($f) { return '`' . $f . '`'; }, $schema));
$stmt = $conn->prepare("SELECT $fields FROM inventory WHERE item_id = ? LIMIT 1");
$stmt->bind_param('s', $item_id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();
$stmt->close();

if (!$item) {
    die('Item not found.');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $updates = [];
    $params = [];
    $types = '';
    foreach ($schema as $field) {
        if (isset($_POST[$field])) {
            $updates[] = "$field = ?";
            $params[] = $_POST[$field];
            $types .= 's';
        }
    }
    if ($updates) {
        $sql = "UPDATE inventory SET " . implode(',', $updates) . " WHERE item_id = ?";
        $params[] = $item_id;
        $types .= 's';
        $stmt = $conn->prepare($sql);
        $stmt->bind_param($types, ...$params);
        $stmt->execute();
        $stmt->close();
        header('Location: inventory_list.php');
        exit;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Inventory Item</title>
</head>
<body>
    <h2>Edit Inventory Item: <?= htmlspecialchars($item['item_id']) ?></h2>
    <form method="post">
        <?php foreach ($schema as $field): ?>
            <div>
                <label><?= htmlspecialchars($field) ?>:</label>
                <input type="text" name="<?= htmlspecialchars($field) ?>" value="<?= htmlspecialchars($item[$field] ?? '') ?>">
            </div>
        <?php endforeach; ?>
        <button type="submit">Save</button>
        <a href="inventory_list.php">Cancel</a>
    </form>
</body>
</html>
