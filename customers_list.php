<?php
// customers_list.php

header('Content-Type: text/html; charset=utf-8');
header('X-Content-Type-Options: nosniff');

include_once(__DIR__ . '/layout_start.php');
include_once(__DIR__ . '/navbar.php');
require_once __DIR__ . '/csv_handler.php';

$schema = require __DIR__ . '/contact_schema.php';
$contacts = readCSV('contacts.csv', $schema);

// Filter only customers
$customers = array_filter($contacts, function($c) {
    return in_array(strtolower(trim($c['is_customer'] ?? '')), ['yes', 'true', '1']);
});
?>

<div class="container">
  <h2>Customer List</h2>

  <table class="table-grid">
    <thead>
      <tr>
        <th>First Name</th>
        <th>Last Name</th>
        <th>Company</th>
        <th>Email</th>
        <th>Phone</th>
        <th>City</th>
        <th>Last Modified</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($customers as $contact): ?>
        <tr>
          <td><?= htmlspecialchars($contact['first_name'] ?? '') ?></td>
          <td><?= htmlspecialchars($contact['last_name'] ?? '') ?></td>
          <td><?= htmlspecialchars($contact['company'] ?? '') ?></td>
          <td><?= htmlspecialchars($contact['email'] ?? '') ?></td>
          <td><?= htmlspecialchars($contact['phone'] ?? '') ?></td>
          <td><?= htmlspecialchars($contact['city'] ?? '') ?></td>
          <td><?= htmlspecialchars($contact['last_modified'] ?? '') ?></td>
          <td>
            <a href="contact_view.php?id=<?= urlencode($contact['id']) ?>" class="btn-primary">👁 View</a>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div style="margin-top: 20px;">
    <a href="contact_form.php" class="btn-outline">➕ Add New Contact</a>
  </div>
</div>
    </tbody>
  </table>

  <div class="navigation">
    <a href="index.php" class="btn-outline">⬅ Back to Home</a>
  </div>
</div>

<?php include_once(__DIR__ . '/layout_end.php'); ?>
