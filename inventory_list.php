
<?php
include_once(__DIR__ . '/layout_start.php');
include_once(__DIR__ . '/navbar.php');
require_once 'csv_handler.php';

$schema = require __DIR__ . '/inventory_schema.php';
$inventoryFile = __DIR__ . '/inventory.csv';
$items = readCSV($inventoryFile, $schema);

// Build filter array from GET
$filters = [];
foreach ($schema as $f) {
    $filters[$f] = isset($_GET[$f]) ? trim($_GET[$f]) : '';
}

// Filter items by all non-empty fields
$filtered = $items;
foreach ($filters as $field => $val) {
    if ($val !== '') {
        $filtered = array_filter($filtered, function($item) use ($field, $val) {
            return stripos($item[$field] ?? '', $val) !== false;
        });
    }
}
?>
<div class="container">
  <h2>Inventory List</h2>
  <form method="get" style="margin-bottom:20px;">
    <div style="display: flex; flex-wrap: wrap; gap: 18px; align-items: flex-end;">
      <?php foreach ($schema as $f): ?>
        <div style="display: flex; flex-direction: column; min-width: 140px;">
          <label for="filter_<?= $f ?>" style="font-size:0.95em; font-weight:600; margin-bottom:2px;">
            <?= htmlspecialchars(ucwords(str_replace('_', ' ', $f))) ?>
          </label>
          <input type="text" name="<?= $f ?>" id="filter_<?= $f ?>" value="<?= htmlspecialchars($filters[$f]) ?>" style="padding:3px 6px; font-size:0.95em; border-radius:4px; border:1px solid #bbb;">
        </div>
      <?php endforeach; ?>
      <div style="display:flex; flex-direction:column; justify-content:flex-end;">
        <button type="submit" style="margin-bottom:4px;">Filter</button>
        <a href="inventory_add.php" class="btn-outline" style="margin-top:4px;">➕ Add Item</a>
      </div>
    </div>
  </form>
  <div style="overflow-x:auto;">
    <table border="1" cellpadding="6" style="width:100%; font-size:0.97em; border-collapse:collapse;">
      <thead style="background:#f5f5f5;">
        <tr>
          <?php foreach ($schema as $f): ?>
            <th style="padding:8px 6px;"> <?= htmlspecialchars(ucwords(str_replace('_', ' ', $f))) ?> </th>
          <?php endforeach; ?>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($filtered as $row): ?>
          <tr>
            <?php foreach ($schema as $f): ?>
              <td style="padding:6px 4px;"> <?= htmlspecialchars($row[$f] ?? '') ?> </td>
            <?php endforeach; ?>
          </tr>
        <?php endforeach; ?>
        <?php if (empty($filtered)): ?>
          <tr><td colspan="<?= count($schema) ?>" style="text-align:center; color:#888;">No items found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include_once(__DIR__ . '/layout_end.php'); ?>
