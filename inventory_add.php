<?php
include_once(__DIR__ . '/layout_start.php');
require_once 'db_mysql.php';

$schema = require __DIR__ . '/inventory_schema.php';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $newItem = [];
  // List of integer fields in inventory
  $intFields = ['supplier_id', 'quantity_in_stock', 'reorder_level', 'reorder_quantity', 'created_by', 'updated_by'];
  // List of date/datetime fields in inventory
  $dateFields = ['purchase_date', 'last_service_date', 'next_service_date', 'warranty_expiry', 'created_at', 'updated_at'];
  // List of decimal fields in inventory
  $decimalFields = ['cost_price', 'margin', 'selling_price'];
  foreach ($schema as $f) {
    $val = trim($_POST[$f] ?? '');
    if (in_array($f, $intFields)) {
      $newItem[$f] = ($val === '' ? null : (int)$val);
    } elseif (in_array($f, $decimalFields)) {
      $newItem[$f] = ($val === '' ? null : (float)$val);
    } elseif (in_array($f, $dateFields)) {
      $newItem[$f] = ($val === '' ? null : $val);
    } else {
      $newItem[$f] = $val;
    }
  }
  // Always auto-generate item_id
  $newItem['item_id'] = uniqid('ITM_');
  $newItem['created_at'] = date('Y-m-d H:i:s');
  $newItem['updated_at'] = date('Y-m-d H:i:s');

  // Build insert
  $fields = array_keys($newItem);
  $placeholders = implode(',', array_fill(0, count($fields), '?'));
  // Set types: i for int fields, s for others
  $types = '';
  foreach ($fields as $f) {
    $types .= in_array($f, $intFields) ? 'i' : 's';
  }
  $values = array_values($newItem);

  $conn = get_mysql_connection();
  $sql = 'INSERT INTO inventory (' . implode(',', $fields) . ') VALUES (' . $placeholders . ')';
  $stmt = $conn->prepare($sql);
  $stmt->bind_param($types, ...$values);
  $stmt->execute();
  $stmt->close();
  $conn->close();
  header('Location: inventory_list.php');
  exit;
}
?>
<div class="container">
  <h2>Add Inventory Item</h2>
  <form method="post" style="max-width:900px; margin:auto;">
    <div style="display:grid; grid-template-columns:repeat(2,1fr); gap:18px;">
      <?php foreach ($schema as $f):
        if (in_array($f, ['created_at','updated_at'])) continue;
        $readonly = $f === 'item_id' ? 'readonly' : '';
        $value = '';
        if ($f === 'item_id') {
          $value = uniqid('ITM_');
        } else {
          $value = htmlspecialchars($_POST[$f] ?? '');
        }
      ?>
        <div style="display:flex; flex-direction:column;">
          <label for="<?= $f ?>" style="font-weight:600; margin-bottom:2px;"> <?= htmlspecialchars(ucwords(str_replace('_',' ',$f))) ?> </label>
          <input type="text" name="<?= $f ?>" id="<?= $f ?>" value="<?= $value ?>" style="padding:5px; border-radius:4px; border:1px solid #bbb;" <?= $readonly ?> >
        </div>
      <?php endforeach; ?>
    </div>
    <div style="margin-top:24px; text-align:center;">
      <button type="submit" class="btn-primary">💾 Save Item</button>
      <a href="inventory_list.php" class="btn-outline">Cancel</a>
    </div>
  </form>
</div>
<?php include_once(__DIR__ . '/layout_end.php'); ?>
