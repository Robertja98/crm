<?php
include_once(__DIR__ . '/layout_start.php');
include_once(__DIR__ . '/navbar.php');
require_once 'csv_handler.php';


$schema = require __DIR__ . '/purchase_order_schema.php';
$poFile = __DIR__ . '/purchase_orders.csv';
$errors = [];
$inventorySchema = require __DIR__ . '/inventory_schema.php';
$inventoryFile = __DIR__ . '/inventory.csv';
$inventory = readCSV($inventoryFile, $inventorySchema);

function generate_po_number() {
  $prefix = 'EWTPO' . date('Ymd');
  $poFile = __DIR__ . '/purchase_orders.csv';
  $count = 1;
  if (file_exists($poFile)) {
    $lines = file($poFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    $today = date('Ymd');
    $matches = array_filter($lines, function($line) use ($today) {
      return strpos($line, 'EWTPO' . $today) === 0;
    });
    $count = count($matches) + 1;
  }
  return $prefix . str_pad($count, 3, '0', STR_PAD_LEFT);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $orders = readCSV($poFile, $schema);
  $po_number = generate_po_number();
  $date = date('Y-m-d');
  $created_at = date('Y-m-d H:i:s');
  $updated_at = $created_at;
  $item_count = isset($_POST['item_id']) ? count($_POST['item_id']) : 0;
  for ($i = 0; $i < $item_count; $i++) {
    $newPO = [];
    foreach ($schema as $f) {
      if (in_array($f, ['item_id','item_name','quantity','unit','unit_price','discount','tax_rate','tax_amount','total'])) {
        $newPO[$f] = trim($_POST[$f][$i] ?? '');
      } else {
        $newPO[$f] = trim($_POST[$f] ?? '');
      }
    }
    $newPO['po_number'] = $po_number;
    $newPO['date'] = $date;
    $newPO['created_at'] = $created_at;
    $newPO['updated_at'] = $updated_at;
    $orders[] = $newPO;
  }
  writeCSV($poFile, $orders, $schema);
  header('Location: purchase_orders_list.php');
  exit;
}
?>
<div class="container">
  <h2>Add Purchase Order</h2>
  <form method="post" style="max-width:900px; margin:auto; background:#fafbfc; border-radius:8px; padding:24px 28px 18px 28px; box-shadow:0 2px 8px #0001;">
    <div style="display:grid; grid-template-columns:repeat(2,1fr); gap:12px 24px; margin-bottom:18px; align-items:end;">
      <?php foreach ($schema as $f):
        if (in_array($f, ['created_at','updated_at','po_number','date','item_id','item_name','quantity','unit','unit_price','discount','tax_rate','tax_amount','total'])) continue;
        $readonly = '';
        $value = htmlspecialchars($_POST[$f] ?? '');
      ?>
        <div style="display:flex; flex-direction:column; min-width:160px;">
          <label for="<?= $f ?>" style="font-weight:600; margin-bottom:2px; font-size:0.97em; color:#222;"> <?= htmlspecialchars(ucwords(str_replace('_',' ',$f))) ?> </label>
          <input type="text" name="<?= $f ?>" id="<?= $f ?>" value="<?= $value ?>" style="padding:4px 7px; border-radius:4px; border:1px solid #bbb; font-size:0.97em;" <?= $readonly ?> >
        </div>
      <?php endforeach; ?>
      <div style="display:flex; flex-direction:column; min-width:120px;">
        <label for="po_number" style="font-weight:600; margin-bottom:2px; font-size:0.97em; color:#222;">PO Number</label>
        <input type="text" name="po_number" id="po_number" value="<?= generate_po_number() ?>" readonly style="padding:4px 7px; border-radius:4px; border:1px solid #bbb; background:#f5f5f5; font-size:0.97em;">
      </div>
      <div style="display:flex; flex-direction:column; min-width:120px;">
        <label for="date" style="font-weight:600; margin-bottom:2px; font-size:0.97em; color:#222;">Date</label>
        <input type="text" name="date" id="date" value="<?= date('Y-m-d') ?>" readonly style="padding:4px 7px; border-radius:4px; border:1px solid #bbb; background:#f5f5f5; font-size:0.97em;">
      </div>
    </div>
    <h3 style="margin:18px 0 8px 0; font-size:1.13em; color:#222;">Order Items</h3>
    <table id="po-items-table" style="width:100%; border-collapse:collapse; margin-bottom:12px; background:#fff;">
      <thead>
        <tr style="background:#f5f5f5; font-size:0.97em;">
          <th style="padding:6px 4px;">Item</th>
          <th style="padding:6px 4px;">Item Name</th>
          <th style="padding:6px 4px;">Qty</th>
          <th style="padding:6px 4px;">Unit</th>
          <th style="padding:6px 4px;">Unit Price</th>
          <th style="padding:6px 4px;">Discount</th>
          <th style="padding:6px 4px;">Tax %</th>
          <th style="padding:6px 4px;">Tax Amt</th>
          <th style="padding:6px 4px;">Total</th>
          <th style="padding:6px 4px;"></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>
            <select name="item_id[]" style="width:120px; padding:3px 5px; font-size:0.97em;">
              <option value="">-- Select --</option>
              <?php foreach ($inventory as $item): ?>
                <option value="<?= htmlspecialchars($item['item_id']) ?>">
                  <?= htmlspecialchars($item['item_id']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </td>
          <td><input type="text" name="item_name[]" style="width:110px; padding:3px 5px; font-size:0.97em;"></td>
          <td><input type="number" name="quantity[]" min="1" style="width:55px; padding:3px 5px; font-size:0.97em;"></td>
          <td><input type="text" name="unit[]" style="width:50px; padding:3px 5px; font-size:0.97em;"></td>
          <td><input type="number" name="unit_price[]" step="0.01" style="width:75px; padding:3px 5px; font-size:0.97em;"></td>
          <td><input type="number" name="discount[]" step="0.01" style="width:60px; padding:3px 5px; font-size:0.97em;"></td>
          <td><input type="number" name="tax_rate[]" step="0.01" style="width:55px; padding:3px 5px; font-size:0.97em;"></td>
          <td><input type="number" name="tax_amount[]" step="0.01" style="width:75px; padding:3px 5px; font-size:0.97em;"></td>
          <td><input type="number" name="total[]" step="0.01" style="width:85px; padding:3px 5px; font-size:0.97em;"></td>
          <td><button type="button" onclick="removeRow(this)" style="padding:2px 7px; font-size:1.1em;">🗑</button></td>
        </tr>
      </tbody>
    </table>
    <button type="button" onclick="addRow()" style="margin-bottom:18px;">➕ Add Item</button>
    <div style="margin-top:18px; text-align:center;">
      <button type="submit" class="btn-primary" style="padding:7px 22px; font-size:1.08em;">💾 Save Purchase Order</button>
      <a href="purchase_orders_list.php" class="btn-outline" style="margin-left:18px;">Cancel</a>
    </div>
  </form>
  <script>
    function addRow() {
      const table = document.getElementById('po-items-table').getElementsByTagName('tbody')[0];
      const row = table.rows[0].cloneNode(true);
      // Clear all input values in the new row
      Array.from(row.querySelectorAll('input,select')).forEach(el => el.value = '');
      table.appendChild(row);
    }
    function removeRow(btn) {
      const table = document.getElementById('po-items-table').getElementsByTagName('tbody')[0];
      if (table.rows.length > 1) {
        btn.closest('tr').remove();
      }
    }
  </script>
  </form>
</div>
<?php include_once(__DIR__ . '/layout_end.php'); ?>
