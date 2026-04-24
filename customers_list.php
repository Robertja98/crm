<?php
// customers_list.php

header('Content-Type: text/html; charset=utf-8');
header('X-Content-Type-Options: nosniff');

include_once(__DIR__ . '/layout_start.php');
require_once 'db_mysql.php';

// Load customers with company info from MySQL
$conn = get_mysql_connection();
$sql = "SELECT customers.*, contacts.company, contacts.first_name, contacts.email FROM customers LEFT JOIN contacts ON customers.contact_id = contacts.contact_id";
$result = $conn->query($sql);
$customers = [];
if ($result) {
  while ($row = $result->fetch_assoc()) {
    $customers[] = $row;
  }
  $result->free();
}
$conn->close();
?>

<div class="container">
  <h2>Customer List</h2>

  <table class="table-grid">
    <thead>
      <tr>
		<th>Company</th>
        <th>First Name</th>
        <th>Email</th>
        <th>Customer Status</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($customers as $contact): ?>
        <?php
          $hasContact = !empty($contact['company']) || !empty($contact['first_name']) || !empty($contact['email']);
        ?>
        <?php if ($hasContact): ?>
          <tr>
            <td><?= htmlspecialchars($contact['company'] ?? '') ?></td>
            <td><?= htmlspecialchars($contact['first_name'] ?? '') ?></td>
            <td><?= htmlspecialchars($contact['email'] ?? '') ?></td>
            <td>
              <?php
                // Show readable customer status
                if (isset($contact['is_customer'])) {
                  if ($contact['is_customer'] == 1 || $contact['is_customer'] === '1') {
                    echo '<span style="color:#28a745;font-weight:600;">Active</span>';
                  } elseif ($contact['is_customer'] == 0 || $contact['is_customer'] === '0') {
                    echo '<span style="color:#999;">Inactive</span>';
                  } else {
                    echo htmlspecialchars($contact['is_customer']);
                  }
                } else {
                  echo '—';
                }
              ?>
            </td>
            <td>
              <a href="customer_view.php?id=<?= urlencode($contact['customer_id']) ?>" class="btn-primary">👁 View</a>
              <a href="edit_customer.php?customer_id=<?= urlencode($contact['customer_id']) ?>" class="btn-warning">✏️ Edit</a>
              <form method="GET" action="delete_customer.php" style="display:inline;">
                <input type="hidden" name="customer_id" value="<?= htmlspecialchars($contact['customer_id']) ?>">
                <button type="submit" class="btn-danger" onclick="return confirm('Are you sure you want to delete this customer? This will archive their info and remove them permanently.');">🗑️ Delete</button>
              </form>
              <?php if (!empty($contact['contact_id'])): ?>
                <a href="contact_view.php?id=<?= urlencode($contact['contact_id']) ?>" class="btn-secondary">👤 View Contact</a>
              <?php endif; ?>
              <?php
                $deliveryFile = "{$contact['customer_id']}_deliveries.csv";
                if (file_exists(__DIR__ . "/$deliveryFile")):
              ?>
                <a href="<?= htmlspecialchars($deliveryFile) ?>" class="btn-secondary" target="_blank">📦 Delivery Archive</a>
              <?php endif; ?>
            </td>
          </tr>
        <?php else: ?>
          <tr style="background:#fffbe6;color:#c00;">
            <td colspan="5"><strong>Incomplete Customer Record:</strong> Customer ID <?= htmlspecialchars($contact['customer_id']) ?> has missing contact info. <a href="customer_view.php?id=<?= urlencode($contact['customer_id']) ?>" class="btn-primary">👁 View</a><?php if (!empty($contact['customer_id'])): ?> <a href="edit_customer.php?customer_id=<?= urlencode($contact['customer_id']) ?>" class="btn-warning">✏️ Edit</a> <form method="GET" action="delete_customer.php" style="display:inline;"><input type="hidden" name="customer_id" value="<?= htmlspecialchars($contact['customer_id']) ?>"><button type="submit" class="btn-danger" onclick="return confirm('Are you sure you want to delete this customer? This will archive their info and remove them permanently.');">🗑️ Delete</button></form><?php endif; ?></td>
          </tr>
        <?php endif; ?>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="navigation">
    <a href="index.php" class="btn-outline">⬅ Back to Home</a>
  </div>
</div>

<?php include_once(__DIR__ . '/layout_end.php'); ?>
