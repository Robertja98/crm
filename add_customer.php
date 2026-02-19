<?php
include_once(__DIR__ . '/layout_start.php');
require_once 'db_mysql.php';

$schema = require __DIR__ . '/customer_schema.php';
$itemFields = require __DIR__ . '/customer_item_config.php';
$errors = [];
$success = false;
$newCustomer = [];

// Get next customer ID from MySQL
$conn = get_mysql_connection();
$result = $conn->query('SELECT MAX(customer_id) AS max_id FROM customers');
$row = $result ? $result->fetch_assoc() : null;
$lastId = $row && $row['max_id'] ? intval($row['max_id']) : 0;
$nextId = str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);

// Handle POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  foreach ($schema as $field) {
    if ($field === 'customer_id') {
      $newCustomer[$field] = $nextId;
    } elseif ($field === 'tank_count') {
      $val = trim($_POST[$field] ?? '');
      $newCustomer[$field] = ($val === '' || !is_numeric($val)) ? null : (int)$val;
    } elseif ($field === 'last_delivery') {
      $val = trim($_POST[$field] ?? '');
      // Accept only valid YYYY-MM-DD or set to null
      if ($val === '' || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $val)) {
        $newCustomer[$field] = null;
      } else {
        $newCustomer[$field] = $val;
      }
    } elseif ($field === 'last_modified') {
      $val = trim($_POST[$field] ?? '');
      // Accept only valid YYYY-MM-DD HH:MM:SS or set to null
      if ($val === '' || !preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $val)) {
        $newCustomer[$field] = null;
      } else {
        $newCustomer[$field] = $val;
      }
    } else {
      $newCustomer[$field] = trim($_POST[$field] ?? '');
    }
  }

  // Validate main fields
  if ($newCustomer['contact_name'] === '') $errors[] = 'Contact name is required.';
  if ($newCustomer['address'] === '') $errors[] = 'Address is required.';

  // Parse line items
  $items = [];
  if (isset($_POST['items']) && is_array($_POST['items'])) {
    foreach ($_POST['items'] as $item) {
      $clean = ['customer_id' => $nextId];
      foreach ($itemFields as $f) {
        $clean[$f] = trim($item[$f] ?? '');
      }
      $items[] = $clean;
    }
  }

  if (empty($errors)) {
    // Insert customer
    $fieldsStr = implode(", ", $schema);
    $placeholders = implode(", ", array_fill(0, count($schema), '?'));
    // Set types: s for string, i for int, d for double
    $types = '';
    foreach ($schema as $field) {
      if ($field === 'tank_count') {
        $types .= 'i';
      } else {
        $types .= 's';
      }
    }
    $stmt = $conn->prepare("INSERT INTO customers ($fieldsStr) VALUES ($placeholders)");
    $params = array_values($newCustomer);
    // Convert nulls for tank_count
    foreach ($schema as $i => $field) {
      if ($field === 'tank_count' && $params[$i] === null) {
        $params[$i] = null;
      }
    }
    $stmt->bind_param($types, ...$params);
    if (!$stmt->execute()) {
      $errors[] = 'Failed to add customer: ' . $stmt->error;
    } else {
      // Insert items
      foreach ($items as $item) {
        $itemFieldsFull = array_merge(['customer_id'], $itemFields);
        $fieldsStr2 = implode(", ", $itemFieldsFull);
        $placeholders2 = implode(", ", array_fill(0, count($itemFieldsFull), '?'));
        $types2 = str_repeat('s', count($itemFieldsFull));
        $stmt2 = $conn->prepare("INSERT INTO customer_items ($fieldsStr2) VALUES ($placeholders2)");
        $stmt2->bind_param($types2, ...array_values($item));
        if (!$stmt2->execute()) {
          $errors[] = 'Failed to add item: ' . $stmt2->error;
        }
        $stmt2->close();
      }
      if (empty($errors)) {
        $success = true;
      }
    }
    $stmt->close();
  }
}
$conn->close();
?>

<div class="container">
  <h2>Add New Customer</h2>

  <?php if ($success): ?>
    <p style="color:green;">✅ Customer added successfully. ID: <?= $nextId ?></p>
  <?php endif; ?>

  <?php if (!empty($errors)): ?>
    <ul style="color:red;">
      <?php foreach ($errors as $e): ?>
        <li><?= htmlspecialchars($e) ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <form method="POST">
    <fieldset style="margin-bottom:20px;">
      <legend><strong>Main Customer Info</strong></legend>
      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap:20px;">
        <div>
          <label><strong>Customer ID:</strong></label><br>
          <input type="text" value="<?= $nextId ?>" readonly>
        </div>
        <?php foreach ($schema as $field): ?>
          <?php if ($field === 'customer_id') continue; ?>
          <div>
            <label for="<?= $field ?>"><strong><?= ucfirst(str_replace('_', ' ', $field)) ?>:</strong></label><br>
            <input type="text" name="<?= $field ?>" id="<?= $field ?>" value="<?= htmlspecialchars($_POST[$field] ?? '') ?>">
          </div>
        <?php endforeach; ?>
      </div>
    </fieldset>

    <fieldset>
      <legend><strong>Tank & Delivery Line Items</strong></legend>
      <div id="lineItems"></div>
      <button type="button" onclick="addLineItem()" class="btn-outline">➕ Add Line Item</button>
    </fieldset>

    <div style="margin-top:20px;">
      <button type="submit" class="btn-outline">💾 Save Customer</button>
    </div>
  </form>
</div>

<script>
function addLineItem() {
  const container = document.getElementById('lineItems');
  const index = container.children.length;
  const fields = <?= json_encode($itemFields) ?>;
  const row = document.createElement('div');
  row.style.marginBottom = '12px';
  row.style.border = '1px solid #ccc';
  row.style.padding = '10px';
  row.style.borderRadius = '6px';
  row.style.background = '#f9f9f9';

  let html = '<div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap:12px;">';
  fields.forEach(f => {
    html += `
      <div>
        <label><strong>${f.replace(/_/g, ' ')}:</strong></label><br>
        <input type="text" name="items[${index}][${f}]" />
      </div>
    `;
  });
  html += '</div>';
  row.innerHTML = html;
  container.appendChild(row);
}
</script>

<?php include_once(__DIR__ . '/layout_end.php'); ?>
