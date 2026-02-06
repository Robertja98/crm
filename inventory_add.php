<?php
include_once(__DIR__ . '/layout_start.php');
include_once(__DIR__ . '/navbar.php');
require_once 'csv_handler.php';

$schema = require __DIR__ . '/inventory_schema.php';
$inventoryFile = __DIR__ . '/inventory.csv';
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $newItem = [];
  foreach ($schema as $f) {
    $newItem[$f] = trim($_POST[$f] ?? '');
  }
  // Always auto-generate item_id
  $newItem['item_id'] = uniqid('ITM_');
  $newItem['created_at'] = date('Y-m-d H:i:s');
  $newItem['updated_at'] = date('Y-m-d H:i:s');
  // Load and append
  $items = readCSV($inventoryFile, $schema);
  $items[] = $newItem;
  writeCSV($inventoryFile, $items, $schema);
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
