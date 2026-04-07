<?php
include_once(__DIR__ . '/layout_start.php');
require_once 'db_mysql.php';

$errors = [];
$success = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['customer_id'])) {
  $customerId = trim($_POST['customer_id']);
  if ($customerId === '') {
    $errors[] = 'Customer ID is required.';
  } else {
    require_once 'archive_customer.php';
    if (!archive_customer($customerId)) {
      $errors[] = 'Failed to archive customer information.';
    }
    $conn = get_mysql_connection();
    // Delete related customer_items first (if any)
    $stmtItems = $conn->prepare('DELETE FROM customer_items WHERE customer_id = ?');
    $stmtItems->bind_param('s', $customerId);
    $stmtItems->execute();
    $stmtItems->close();

    // Delete customer
    $stmt = $conn->prepare('DELETE FROM customers WHERE customer_id = ?');
    $stmt->bind_param('s', $customerId);
    if ($stmt->execute()) {
      $success = true;
    } else {
      $errors[] = 'Failed to delete customer: ' . $stmt->error;
    }
    $stmt->close();
    $conn->close();
  }
}
?>

<div class="container">
  <h2>Delete Customer</h2>

  <?php if ($success): ?>
    <p style="color:green;">✅ Customer deleted successfully.</p>
  <?php endif; ?>

  <?php if (!empty($errors)): ?>
    <ul style="color:red;">
      <?php foreach ($errors as $e): ?>
        <li><?= htmlspecialchars($e) ?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif; ?>

  <form method="POST">
    <label for="customer_id"><strong>Customer ID:</strong></label><br>
    <input type="text" name="customer_id" id="customer_id" required>
    <button type="submit" class="btn-outline" style="margin-left:10px;">🗑️ Delete Customer</button>
  </form>
</div>

<?php include_once(__DIR__ . '/layout_end.php'); ?>
