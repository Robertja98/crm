<?php
include_once(__DIR__ . '/layout_start.php');
require_once 'db_mysql.php';

$schema = require __DIR__ . '/customer_schema.php';
$itemFields = require __DIR__ . '/customer_item_config.php';
$errors = [];
$success = false;
$newCustomer = [];

// Get next customer ID from MySQL



$connId = get_mysql_connection();
$result = $connId->query('SELECT MAX(customer_id) AS max_id FROM customers');
$row = $result ? $result->fetch_assoc() : null;
$lastId = $row && $row['max_id'] ? intval($row['max_id']) : 0;
$nextId = str_pad($lastId + 1, 5, '0', STR_PAD_LEFT);
if ($result) $result->free();

// Ensure customer_id is unique
$uniqueId = $nextId;
while (true) {
  $checkResult = $connId->query("SELECT customer_id FROM customers WHERE customer_id = '" . $connId->real_escape_string($uniqueId) . "'");
  if ($checkResult && $checkResult->num_rows > 0) {
    $uniqueId = str_pad(intval($uniqueId) + 1, 5, '0', STR_PAD_LEFT);
    $checkResult->free();
    continue;
  }
  if ($checkResult) $checkResult->free();

  // Parse line items
  $items = [];
  if (isset($_POST['items']) && is_array($_POST['items'])) {
    foreach ($_POST['items'] as $item) {
      $clean = ['customer_id' => $uniqueId];
      foreach ($itemFields as $f) {
        $clean[$f] = trim($item[$f] ?? '');
      }
      $items[] = $clean;
    }
  }

  if (empty($errors)) {
    // Prevent creation if no contact is selected
    if (empty($_POST['contact_id']) && empty($selectedContactId)) {
      $errors[] = 'You must select a contact before creating a customer.';
    } else {
      // Insert customer
      $connPost = get_mysql_connection();
      $newCustomer['customer_id'] = $uniqueId;
      // Always use the contact_id from POST if provided, else fallback to selectedContactId
      $newCustomer['contact_id'] = $_POST['contact_id'] ?? $selectedContactId;
      $fieldsStr = implode(", ", $schema);
      $placeholders = implode(", ", array_fill(0, count($schema), '?'));
      // Set types: s for string, i for int, d for double
      $types = '';
      $params = [];
      foreach ($schema as $field) {
        if ($field === 'tank_count' || $field === 'contact_id') {
          $types .= 'i';
          $val = $newCustomer[$field] ?? null;
          $params[] = ($val === '' || $val === null || !is_numeric($val)) ? null : (int)$val;
        } else if ($field === 'last_delivery') {
          $types .= 's';
          $val = $newCustomer[$field] ?? '';
          $params[] = (empty($val) || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $val)) ? null : $val;
        } else if ($field === 'last_modified') {
          $types .= 's';
          $val = $newCustomer[$field] ?? '';
          $params[] = (empty($val) || !preg_match('/^\d{4}-\d{2}-\d{2} \d{2}:\d{2}:\d{2}$/', $val)) ? null : $val;
        } else {
          $types .= 's';
          $params[] = $newCustomer[$field] ?? '';
        }
      }
      $stmt = $connPost->prepare("INSERT INTO customers ($fieldsStr) VALUES ($placeholders)");
      $stmt->bind_param($types, ...$params);
      if (!$stmt->execute()) {
        $errors[] = 'Failed to add customer: ' . $stmt->error;
      } else {
        foreach ($items as $item) {
          $itemFieldsFull = array_merge(['customer_id'], $itemFields);
          $fieldsStr2 = implode(", ", $itemFieldsFull);
          $placeholders2 = implode(", ", array_fill(0, count($itemFieldsFull), '?'));
          $types2 = str_repeat('s', count($itemFieldsFull));
          $stmt2 = $connPost->prepare("INSERT INTO customer_items ($fieldsStr2) VALUES ($placeholders2)");
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
      $connPost->close();
    }
  }
  break;
}
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
          <input type="text" value="<?= $nextId ?>" readonly disabled>
          <input type="hidden" name="customer_id" value="<?= $nextId ?>">
        </div>
        <div>
          <label for="contact_id"><strong>Contact:</strong></label><br>
          <?php
          $selectedContactId = $_GET['contact_id'] ?? $_POST['contact_id'] ?? '';
          $connForm3 = get_mysql_connection();
          $result3 = $connForm3->query("SELECT contact_id, CONCAT(first_name, ' ', last_name) AS label, company, address FROM contacts WHERE contact_id = '" . $connForm3->real_escape_string($selectedContactId) . "'");
          $row3 = $result3 ? $result3->fetch_assoc() : null;
          if ($row3) {
            $label = htmlspecialchars($row3['label']);
            $company = htmlspecialchars($row3['company']);
            $address = htmlspecialchars($row3['address']);
            echo '<input type="hidden" name="contact_id" value="' . htmlspecialchars($selectedContactId) . '">';
            echo '<input type="text" value="' . $label . '" readonly style="background:#eee;">';
            echo '<input type="hidden" name="company" value="' . $company . '">';
            echo '<input type="text" value="' . $company . '" readonly disabled style="background:#eee;margin-top:8px;">';
            echo '<input type="hidden" name="address" value="' . $address . '">';
            echo '<input type="text" value="' . $address . '" readonly disabled style="background:#eee;margin-top:8px;">';
          } else {
            echo '<span style="color:#c00;">No contact selected. Please start from a contact profile.</span>';
          }
          if ($result3) $result3->free();
          $connForm3->close();
          ?>
        </div>
        <?php foreach ($schema as $field): ?>
          <?php if ($field === 'customer_id' || $field === 'contact_id' || $field === 'address') continue; ?>
          <div>
            <label for="<?= $field ?>"><strong><?= ucfirst(str_replace('_', ' ', $field)) ?>:</strong></label><br>
            <?php if ($field === 'tank_number'): ?>
              <input type="number" name="tank_number" id="tank_number" value="<?= htmlspecialchars($_POST['tank_number'] ?? '') ?>" min="0">
            <?php // Removed tank_size field: not present in DB schema ?>
            <?php else: ?>
              <input type="text" name="<?= $field ?>" id="<?= $field ?>" value="<?= htmlspecialchars($_POST[$field] ?? '') ?>">
            <?php endif; ?>
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

// Persist form values using localStorage
window.addEventListener('DOMContentLoaded', function() {
  const form = document.querySelector('form');
  if (!form) return;
  // Restore values
  Array.from(form.elements).forEach(el => {
    if (!el.name) return;
    const val = localStorage.getItem('add_customer_' + el.name);
    if (val !== null) {
      if (el.type === 'checkbox' || el.type === 'radio') {
        el.checked = val === 'true';
      } else {
        el.value = val;
      }
    }
  });
  // Save values on change
  form.addEventListener('input', function(e) {
    const el = e.target;
    if (!el.name) return;
    if (el.type === 'checkbox' || el.type === 'radio') {
      localStorage.setItem('add_customer_' + el.name, el.checked);
    } else {
      localStorage.setItem('add_customer_' + el.name, el.value);
    }
  });
  // Clear storage on submit
  form.addEventListener('submit', function() {
    Array.from(form.elements).forEach(el => {
      if (el.name) localStorage.removeItem('add_customer_' + el.name);
    });
  });
});
</script>

<?php include_once(__DIR__ . '/layout_end.php'); ?>
