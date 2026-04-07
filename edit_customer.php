<?php
include_once(__DIR__ . '/layout_start.php');
require_once 'db_mysql.php';
$schema = require __DIR__ . '/customer_schema.php';
$errors = [];
$success = false;
$customer = [];

if (isset($_GET['customer_id'])) {
    $customerId = trim($_GET['customer_id']);
    $conn = get_mysql_connection();
    $stmt = $conn->prepare('SELECT * FROM customers WHERE customer_id = ?');
    $stmt->bind_param('s', $customerId);
    $stmt->execute();
    $result = $stmt->get_result();
    $customer = $result ? $result->fetch_assoc() : [];
    $stmt->close();
    $conn->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['customer_id'])) {
    $customerId = trim($_POST['customer_id']);
    $conn = get_mysql_connection();
    $fields = [];
    $values = [];
    foreach ($schema as $field) {
      if ($field === 'customer_id') continue;
      $fields[] = "$field = ?";
      $val = $_POST[$field] ?? '';
      if (($field === 'last_delivery' || $field === 'last_modified') && $val === '') {
        $values[] = null;
      } else {
        $values[] = $val;
      }
    }
    $sql = 'UPDATE customers SET ' . implode(', ', $fields) . ' WHERE customer_id = ?';
    $stmt = $conn->prepare($sql);
    $values[] = $customerId;
    // Use 's' for all fields, MySQLi will handle NULLs
    $types = str_repeat('s', count($values));
    $stmt->bind_param($types, ...$values);
    if ($stmt->execute()) {
        $success = true;
    } else {
        $errors[] = 'Failed to update customer: ' . $stmt->error;
    }
    $stmt->close();
    $conn->close();
    // Reload updated customer
    $conn = get_mysql_connection();
    $stmt = $conn->prepare('SELECT * FROM customers WHERE customer_id = ?');
    $stmt->bind_param('s', $customerId);
    $stmt->execute();
    $result = $stmt->get_result();
    $customer = $result ? $result->fetch_assoc() : [];
    $stmt->close();
    $conn->close();
}
?>

<div class="container">
  <h2>Edit Customer</h2>

  <?php if ($success): ?>
    <p style="color:green;">✅ Customer updated successfully.</p>
  <?php endif; ?>

  <?php if (!empty($errors)): ?>
    <ul style="color:red;">
      <?php foreach ($errors as $e): ?>
        <li><?= htmlspecialchars($e) ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <?php if (!empty($customer)): ?>
  <form method="POST">
    <input type="hidden" name="customer_id" value="<?= htmlspecialchars($customer['customer_id']) ?>">
    <fieldset style="margin-bottom:20px;">
      <legend><strong>Main Customer Info</strong></legend>
      <div style="display:grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap:20px;">
        <?php foreach ($schema as $field): ?>
          <?php if ($field === 'customer_id') continue; ?>
          <div>
            <label for="<?= $field ?>"><strong><?= ucfirst(str_replace('_', ' ', $field)) ?>:</strong></label><br>
            <input type="text" name="<?= $field ?>" id="<?= $field ?>" value="<?= htmlspecialchars($customer[$field] ?? '') ?>">
          </div>
        <?php endforeach; ?>
      </div>
    </fieldset>
    <div style="margin-top:20px;">
      <button type="submit" class="btn-outline">💾 Save Changes</button>
    </div>
  </form>
  <?php else: ?>
    <p style="color:#c00;">Customer not found.</p>
  <?php endif; ?>
</div>

<?php include_once(__DIR__ . '/layout_end.php'); ?>
