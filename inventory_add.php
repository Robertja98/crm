<?php
require_once 'db_mysql.php';

$errors = [];

// Fields shown in the add form (user-facing only)
$formFields = [
    'item_name'         => 'Item Name',
    'category'          => 'Category',
    'description'       => 'Description',
    'brand'             => 'Brand',
    'model'             => 'Model / Part #',
    'supplier_name'     => 'Supplier Name',
    'unit'              => 'Unit (e.g. cuft, ea)',
    'quantity_in_stock' => 'Qty in Stock',
    'reorder_level'     => 'Reorder Level',
    'cost_price'        => 'Cost Price',
    'status'            => 'Status',
    'notes'             => 'Notes',
];

$intFields     = ['quantity_in_stock', 'reorder_level'];
$decimalFields = ['cost_price', 'margin', 'selling_price'];
$statusOptions = ['Stock', 'Production', 'Maintenance', 'Retired'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newItem = [];
    foreach ($formFields as $f => $label) {
        $val = trim($_POST[$f] ?? '');
        if (in_array($f, $intFields, true)) {
            $newItem[$f] = ($val === '' ? null : (int)$val);
        } elseif (in_array($f, $decimalFields, true)) {
            $newItem[$f] = ($val === '' ? null : (float)$val);
        } else {
            $newItem[$f] = ($val === '') ? null : $val;
        }
    }

    $newItem['item_id']    = uniqid('ITM_');
    $newItem['created_at'] = date('Y-m-d H:i:s');
    $newItem['updated_at'] = date('Y-m-d H:i:s');

    $fields       = array_keys($newItem);
    $placeholders = implode(',', array_fill(0, count($fields), '?'));
    $types        = '';
    foreach ($fields as $f) {
        if (in_array($f, $intFields, true)) {
            $types .= 'i';
        } elseif (in_array($f, $decimalFields, true)) {
            $types .= 'd';
        } else {
            $types .= 's';
        }
    }

    $conn = get_mysql_connection();
    $sql  = 'INSERT INTO inventory (' . implode(',', $fields) . ') VALUES (' . $placeholders . ')';
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...array_values($newItem));
    $stmt->execute();
    $stmt->close();
    $conn->close();

    header('Location: inventory_list.php');
    exit;
}

include_once(__DIR__ . '/layout_start.php');
?>
<div class="container" style="max-width:700px; margin:32px auto;">
    <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:24px;">
        <h2 style="margin:0;">Add Inventory Item</h2>
        <a href="inventory_list.php" class="btn btn-outline-secondary">← Back</a>
    </div>
    <form method="post">
    <?php renderCSRFInput(); ?>
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
            <?php foreach ($formFields as $f => $label): ?>
                <div style="display:flex; flex-direction:column; <?= in_array($f, ['description','notes'], true) ? 'grid-column:1/3;' : '' ?>">
                    <label for="<?= $f ?>" style="font-weight:600; margin-bottom:4px;"><?= htmlspecialchars($label) ?></label>
                    <?php if (in_array($f, ['description','notes'], true)): ?>
                        <textarea name="<?= $f ?>" id="<?= $f ?>" rows="3" class="form-control"><?= htmlspecialchars($_POST[$f] ?? '') ?></textarea>
                      <?php elseif ($f === 'status'): ?>
                        <select name="status" id="status" class="form-control">
                          <option value="">-- Select Status --</option>
                          <?php foreach ($statusOptions as $opt): ?>
                            <option value="<?= htmlspecialchars($opt) ?>" <?= ($_POST['status'] ?? '') === $opt ? 'selected' : '' ?>><?= htmlspecialchars($opt) ?></option>
                          <?php endforeach; ?>
                        </select>
                      <?php else: ?>
                        <input type="<?= in_array($f, $intFields, true) ? 'number' : 'text' ?>" name="<?= $f ?>" id="<?= $f ?>" value="<?= htmlspecialchars($_POST[$f] ?? '') ?>" class="form-control">
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
        <div style="margin-top:24px; display:flex; gap:12px;">
            <button type="submit" class="btn btn-success">💾 Save Item</button>
            <a href="inventory_list.php" class="btn btn-outline-secondary">Cancel</a>
        </div>
    </form>
</div>
<?php include_once(__DIR__ . '/layout_end.php'); ?>
