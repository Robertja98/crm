<?php
require_once 'db_mysql.php';
require_once 'csrf_helper.php';
<<<<<<< HEAD
$schema = require __DIR__ . '/inventory_schema.php';
=======
>>>>>>> e8fc044 (WIP: Commit all local changes before rebase/pull)

$pageTitle = 'Edit Inventory Item';
include_once __DIR__ . '/layout_start.php';

$schema = require __DIR__ . '/inventory_schema.php';
$readOnlyFields = ['item_id', 'created_at', 'created_by'];
$dateFields = ['purchase_date', 'last_service_date', 'next_service_date', 'warranty_expiry'];
$numberFields = ['supplier_id', 'cost_price', 'margin', 'selling_price', 'quantity_in_stock', 'reorder_level', 'reorder_quantity', 'updated_by'];
$textareaFields = ['description', 'notes'];

$item_id = trim((string) ($_GET['item_id'] ?? ''));
if ($item_id === '') {
        echo '<div class="alert alert-danger m-3">No item ID specified.</div>';
        include_once __DIR__ . '/layout_end.php';
        exit;
}

$conn = get_mysql_connection();
$fields = implode(',', array_map(function ($field) {
        return '`' . $field . '`';
}, $schema));

$stmt = $conn->prepare("SELECT $fields FROM inventory WHERE item_id = ? LIMIT 1");
$stmt->bind_param('s', $item_id);
$stmt->execute();
$result = $stmt->get_result();
$item = $result->fetch_assoc();
$stmt->close();

if (!$item) {
        echo '<div class="alert alert-warning m-3">Item not found.</div>';
        include_once __DIR__ . '/layout_end.php';
        $conn->close();
        exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
<<<<<<< HEAD
    if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
        die('CSRF validation failed');
    }
    $updates = [];
    $params = [];
    $types = '';
    foreach ($schema as $field) {
        if (isset($_POST[$field])) {
            $updates[] = "$field = ?";
            $params[] = $_POST[$field];
            $types .= 's';
=======
        if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
                echo '<div class="alert alert-danger m-3">Security validation failed. Please refresh and try again.</div>';
        } else {
                $updates = [];
                $params = [];
                $types = '';

                foreach ($schema as $field) {
                        if (in_array($field, $readOnlyFields, true) || $field === 'updated_at') {
                                continue;
                        }
                        if (array_key_exists($field, $_POST)) {
                                $updates[] = "`$field` = ?";
                                $params[] = trim((string) $_POST[$field]);
                                $types .= 's';
                        }
                }

                if (in_array('updated_at', $schema, true)) {
                        $updates[] = "`updated_at` = NOW()";
                }

                if ($updates) {
                        $sql = 'UPDATE inventory SET ' . implode(', ', $updates) . ' WHERE item_id = ?';
                        $params[] = $item_id;
                        $types .= 's';

                        $stmt = $conn->prepare($sql);
                        $stmt->bind_param($types, ...$params);
                        $stmt->execute();
                        $stmt->close();

                        header('Location: inventory_list.php?updated=1');
                        $conn->close();
                        exit;
                }
>>>>>>> e8fc044 (WIP: Commit all local changes before rebase/pull)
        }
}

$conn->close();
?>
<<<<<<< HEAD
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Inventory Item</title>
</head>
<body>
    <h2>Edit Inventory Item: <?= htmlspecialchars($item['item_id']) ?></h2>
    <form method="post">
        <?php renderCSRFInput(); ?>
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
=======

<div class="container" style="max-width: 960px; margin-top: 24px; margin-bottom: 24px;">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2 class="m-0">Edit Inventory Item</h2>
        <a href="inventory_list.php" class="btn btn-outline-secondary btn-sm">Back to Inventory</a>
    </div>

    <div class="card shadow-sm">
        <div class="card-body">
            <p class="text-muted mb-3">Editing item: <strong><?= htmlspecialchars($item['item_id'] ?? '') ?></strong></p>
            <form method="post">
                <?php renderCSRFInput(); ?>
                <div class="row g-3">
                    <?php foreach ($schema as $field): ?>
                        <?php
                            $label = ucwords(str_replace('_', ' ', $field));
                            $value = (string) ($item[$field] ?? '');
                            $isReadOnly = in_array($field, $readOnlyFields, true) || $field === 'updated_at';
                            $inputType = in_array($field, $dateFields, true) ? 'date' : (in_array($field, $numberFields, true) ? 'number' : 'text');
                        ?>
                        <div class="col-12 col-md-6">
                            <label for="<?= htmlspecialchars($field) ?>" class="form-label"><?= htmlspecialchars($label) ?></label>
                            <?php if (in_array($field, $textareaFields, true)): ?>
                                <textarea id="<?= htmlspecialchars($field) ?>" name="<?= htmlspecialchars($field) ?>" class="form-control" rows="3" <?= $isReadOnly ? 'readonly' : '' ?>><?= htmlspecialchars($value) ?></textarea>
                            <?php else: ?>
                                <input
                                    id="<?= htmlspecialchars($field) ?>"
                                    type="<?= $inputType ?>"
                                    name="<?= htmlspecialchars($field) ?>"
                                    class="form-control"
                                    value="<?= htmlspecialchars($value) ?>"
                                    <?= $isReadOnly ? 'readonly' : '' ?>
                                    <?= $inputType === 'number' ? 'step="any"' : '' ?>
                                >
                            <?php endif; ?>
                            <?php if ($field === 'supplier_id' || $field === 'supplier_name'): ?>
                                <div class="form-text">
                                    <a href="supplier_directory.php" target="_blank" rel="noopener">Open Supplier Directory</a>
                                </div>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="d-flex gap-2 mt-4">
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                    <a href="inventory_list.php" class="btn btn-outline-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</div>

<?php include_once __DIR__ . '/layout_end.php'; ?>
>>>>>>> e8fc044 (WIP: Commit all local changes before rebase/pull)
