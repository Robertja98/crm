<?php
// customers_list.php

header('Content-Type: text/html; charset=utf-8');
header('X-Content-Type-Options: nosniff');

include_once(__DIR__ . '/layout_start.php');
require_once 'db_mysql.php';

// Load customers from MySQL
$conn = get_mysql_connection();
$sql = "SELECT * FROM customers";
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
        <tr>
          <td><?= htmlspecialchars($contact['company'] ?? '') ?></td>
		  <td><?= htmlspecialchars($contact['first_name'] ?? '') ?></td>
          <td><?= htmlspecialchars($contact['email'] ?? '') ?></td>
          <td><?= htmlspecialchars($contact['is_customer'] ?? '') ?></td>
          <td>
            <a href="customer_view.php?id=<?= urlencode($contact['id']) ?>" class="btn-primary">👁 View</a>
            <?php
              $deliveryFile = "{$contact['id']}_deliveries.csv";
              if (file_exists(__DIR__ . "/$deliveryFile")):
            ?>
              <a href="<?= htmlspecialchars($deliveryFile) ?>" class="btn-secondary" target="_blank">📦 Delivery Archive</a>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="navigation">
    <a href="index.php" class="btn-outline">⬅ Back to Home</a>
  </div>
</div>

<?php include_once(__DIR__ . '/layout_end.php'); ?>
