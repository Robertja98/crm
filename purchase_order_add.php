<?php
include_once(__DIR__ . '/layout_start.php');
require_once 'db_mysql.php';
require_once 'inventory_mysql.php';


$schema = require __DIR__ . '/purchase_order_schema.php';
$poFile = __DIR__ . '/purchase_orders.csv';
$errors = [];
$inventorySchema = require __DIR__ . '/inventory_schema.php';
$inventory = fetch_inventory_mysql($inventorySchema);

function generate_po_number() {
  $prefix = 'EWTPO' . date('Ymd');
  $conn = get_mysql_connection();
  $count = 1;
  while (true) {
    $po_number = $prefix . str_pad($count, 3, '0', STR_PAD_LEFT);
    $stmt = $conn->prepare("SELECT 1 FROM purchase_orders WHERE po_number = ? LIMIT 1");
    $stmt->bind_param('s', $po_number);
    $stmt->execute();
    $stmt->store_result();
    if ($stmt->num_rows === 0) {
      $stmt->close();
      break;
    }
    $stmt->close();
    $count++;
  }
  $conn->close();
  return $po_number;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  ini_set('display_errors', 1);
  ini_set('display_startup_errors', 1);
  error_reporting(E_ALL);
  $po_number = generate_po_number();
  $date = date('Y-m-d');
  $created_at = date('Y-m-d H:i:s');
  $updated_at = $created_at;
  $item_count = isset($_POST['item_id']) ? count($_POST['item_id']) : 0;
  $conn = get_mysql_connection();
  // Insert into purchase_orders (header)
  $orderFields = [
    'po_number','date','status','supplier_id','supplier_name','supplier_contact','supplier_address','billing_address','shipping_address',
    'subtotal','total_discount','total_tax','shipping_cost','other_fees','grand_total','currency','expected_delivery','payment_terms','notes','created_by','created_at','updated_at'
  ];
  $orderData = [];
  $decimalFields = ['subtotal','total_discount','total_tax','shipping_cost','other_fees','grand_total'];
  $dateFields = ['date','expected_delivery','created_at','updated_at'];
  foreach ($orderFields as $f) {
    $val = trim($_POST[$f] ?? '');
    if ($f === 'po_number') $val = $po_number;
    if ($f === 'date') $val = $date;
    if ($f === 'created_at' || $f === 'updated_at') $val = $created_at;
    if (in_array($f, $decimalFields)) {
      $orderData[] = ($val === '' ? null : (float)$val);
    } elseif (in_array($f, $dateFields)) {
      $orderData[] = ($val === '' ? null : $val);
    } else {
      $orderData[] = $val;
    }
  }
  $orderPlaceholders = implode(',', array_fill(0, count($orderFields), '?'));
  $orderTypes = '';
  foreach ($orderFields as $f) {
    $orderTypes .= in_array($f, $decimalFields) ? 'd' : 's';
  }
  $orderStmt = $conn->prepare('INSERT INTO purchase_orders (' . implode(',', $orderFields) . ') VALUES (' . $orderPlaceholders . ')');
  if (!$orderStmt) {
    echo '<div style="color:red;">MySQL Prepare Error (orders): ' . htmlspecialchars($conn->error) . '</div>';
    exit;
  }
  $orderStmt->bind_param($orderTypes, ...$orderData);
  if (!$orderStmt->execute()) {
    echo '<div style="color:red;">MySQL Execute Error (orders): ' . htmlspecialchars($orderStmt->error) . '</div>';
    $orderStmt->close();
    $conn->close();
    exit;
  }
  $orderStmt->close();
  // Insert items into purchase_order_items
  $itemFields = ['po_number','item_id','item_name','quantity','unit','unit_price','discount','tax_rate','tax_amount','total'];
  $itemDecimalFields = ['quantity','unit_price','discount','tax_rate','tax_amount','total'];
  for ($i = 0; $i < $item_count; $i++) {
    $itemData = [$po_number];
    foreach (array_slice($itemFields, 1) as $f) {
      $val = trim($_POST[$f][$i] ?? '');
      if (in_array($f, $itemDecimalFields)) {
        $itemData[] = ($val === '' ? null : (float)$val);
      } else {
        $itemData[] = $val;
      }
    }
    $itemPlaceholders = implode(',', array_fill(0, count($itemFields), '?'));
    $itemTypes = 's';
    foreach (array_slice($itemFields, 1) as $f) {
      $itemTypes .= in_array($f, $itemDecimalFields) ? 'd' : 's';
    }
    $itemStmt = $conn->prepare('INSERT INTO purchase_order_items (' . implode(',', $itemFields) . ') VALUES (' . $itemPlaceholders . ')');
    if (!$itemStmt) {
      echo '<div style="color:red;">MySQL Prepare Error (items): ' . htmlspecialchars($conn->error) . '</div>';
      $conn->close();
      exit;
    }
    $itemStmt->bind_param($itemTypes, ...$itemData);
    if (!$itemStmt->execute()) {
      echo '<div style="color:red;">MySQL Execute Error (items): ' . htmlspecialchars($itemStmt->error) . '</div>';
      $itemStmt->close();
      $conn->close();
      exit;
    }
    $itemStmt->close();
  }
  $conn->close();
  echo '<div style="color:green; font-weight:bold;">[DEBUG] About to redirect to purchase_orders_list.php</div>';
  if (function_exists('ob_clean')) { ob_clean(); }
  header('Location: purchase_orders_list.php');
  exit;
}
// Debug: If script reaches here, POST handler did not exit as expected
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  echo '<div style="color:red; font-weight:bold;">[DEBUG] Script reached end of POST handler without exit. Check for logic errors above.</div>';
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
    <style>
      #po-items-table {
        width: 100%;
        border-collapse: separate;
        border-spacing: 0 6px;
        margin-bottom: 16px;
        background: #fff;
        box-shadow: 0 1px 4px #0001;
        border-radius: 8px;
        overflow: hidden;
      }
      #po-items-table th, #po-items-table td {
        padding: 8px 6px;
        text-align: left;
        font-size: 1em;
      }
      #po-items-table th {
        background: #f5f5f5;
        font-weight: 600;
        color: #234;
        border-bottom: 1px solid #e0e0e0;
      }
      #po-items-table td {
        background: #fcfcfd;
        border-bottom: 1px solid #f0f0f0;
        vertical-align: middle;
      }
      #po-items-table input, #po-items-table select {
        width: 100%;
        padding: 5px 7px;
        border-radius: 4px;
        border: 1px solid #bbb;
        font-size: 1em;
        box-sizing: border-box;
        background: #fff;
      }
      #po-items-table input[type="number"] {
        text-align: right;
      }
      #po-items-table button {
        padding: 2px 9px;
        font-size: 1.1em;
        border-radius: 4px;
        border: none;
        background: #f8d7da;
        color: #a71d2a;
        cursor: pointer;
        transition: background 0.2s;
      }
      #po-items-table button:hover {
        background: #f1b0b7;
      }
    </style>
    <table id="po-items-table">
      <thead>
        <tr>
          <th style="min-width:120px;">Item</th>
          <th style="min-width:140px;">Item Name</th>
          <th style="min-width:60px;">Qty</th>
          <th style="min-width:60px;">Unit</th>
          <th style="min-width:90px;">Unit Price</th>
          <th style="min-width:70px;">Discount</th>
          <th style="min-width:60px;">Tax %</th>
          <th style="min-width:90px;">Tax Amt</th>
          <th style="min-width:100px;">Total</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <td>
            <select name="item_id[]">
              <option value="">-- Select --</option>
              <?php foreach ($inventory as $item): ?>
                <option value="<?= htmlspecialchars($item['item_id']) ?>">
                  <?= htmlspecialchars($item['item_id']) ?>
                </option>
              <?php endforeach; ?>
            </select>
          </td>
          <td><input type="text" name="item_name[]"></td>
          <td><input type="number" name="quantity[]" min="1"></td>
          <td><input type="text" name="unit[]"></td>
          <td><input type="number" name="unit_price[]" step="0.01"></td>
          <td><input type="number" name="discount[]" step="0.01"></td>
          <td><input type="number" name="tax_rate[]" step="0.01"></td>
          <td><input type="number" name="tax_amount[]" step="0.01"></td>
          <td><input type="number" name="total[]" step="0.01"></td>
          <td><button type="button" onclick="removeRow(this)">🗑</button></td>
        </tr>
      </tbody>
    </table>
    <button type="button" onclick="addRow()" style="margin-bottom:18px; background:#e7f3e7; color:#1a5d1a; border:1px solid #b6e2b6; border-radius:4px; padding:6px 16px; font-size:1em; cursor:pointer;">➕ Add Item</button>
    <div style="margin-top:18px; text-align:center;">
      <button type="submit" class="btn-primary" style="padding:7px 22px; font-size:1.08em;">💾 Save Purchase Order</button>
      <a href="purchase_orders_list.php" class="btn-outline" style="margin-left:18px;">Cancel</a>
    </div>
  </form>
  <script>
    // Inventory data for JS (item_id -> {item_name, unit, unit_price})
    const inventoryData = {};
    <?php foreach ($inventory as $item): ?>
      inventoryData["<?= addslashes($item['item_id']) ?>"] = {
        item_name: "<?= addslashes($item['item_name'] ?? '') ?>",
        unit: "<?= addslashes($item['unit'] ?? '') ?>",
        unit_price: "<?= addslashes($item['unit_price'] ?? '') ?>"
      };
    <?php endforeach; ?>

    function addRow() {
      const table = document.getElementById('po-items-table').getElementsByTagName('tbody')[0];
      const row = table.rows[0].cloneNode(true);
      // Clear all input values in the new row
      Array.from(row.querySelectorAll('input,select')).forEach(el => el.value = '');
      attachItemSelectListener(row);
      table.appendChild(row);
    }

    function removeRow(btn) {
      const table = document.getElementById('po-items-table').getElementsByTagName('tbody')[0];
      if (table.rows.length > 1) {
        btn.closest('tr').remove();
      }
    }

    function attachItemSelectListener(row) {
      const select = row.querySelector('select[name="item_id[]"]');
      if (!select) return;
      select.addEventListener('change', function() {
        const val = this.value;
        const data = inventoryData[val] || {};
        // Find sibling inputs in the same row
        const inputs = row.querySelectorAll('input');
        inputs.forEach(input => {
          if (input.name === 'item_name[]') input.value = data.item_name || '';
          if (input.name === 'unit[]') input.value = data.unit || '';
          if (input.name === 'unit_price[]') input.value = data.unit_price || '';
        });
      });
    }

    // Attach listeners to all existing rows on page load
    document.addEventListener('DOMContentLoaded', function() {
      document.querySelectorAll('#po-items-table tbody tr').forEach(attachItemSelectListener);
    });
  </script>
  </form>
</div>
<?php include_once(__DIR__ . '/layout_end.php'); ?>
