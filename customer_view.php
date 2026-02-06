<?php


// Security headers
header('Content-Type: text/html; charset=utf-8');
header('X-Content-Type-Options: nosniff');

// Includes
include_once(__DIR__ . '/layout_start.php');
include_once(__DIR__ . '/navbar.php');
require_once __DIR__ . '/csv_handler.php';
require_once 'discussion_validator.php';

// Load schemas and data
$schema = require __DIR__ . '/contact_schema.php';
$contacts = readCSV('contacts.csv', $schema);

// Helper: find contact by ID
function findContactById($contacts, $id) {
    if ($id === '') return null;
    foreach ($contacts as $c) {
        if ($c['id'] === $id) return $c;
    }
    return null;
}

// Helper: create delivery file
function createDeliveryFileIfNeeded($contactId) {
    $deliveryFile = __DIR__ . "/{$contactId}_deliveries.csv";
    if (!file_exists($deliveryFile)) {
        $fp = fopen($deliveryFile, 'w');
        if ($fp) {
            fputcsv($fp, ['delivery_date', 'tank_number', 'tank_size']);
            fclose($fp);
            return true;
        } else {
            error_log("Failed to create delivery file for contact ID: $contactId");
            return false;
        }
    }
    return true;
}

// Handle contact update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['id'])) {
    $id = $_POST['id'];
    foreach ($contacts as &$c) {
        if ($c['id'] === $id) {
            foreach ($schema as $f) {
                $c[$f] = $_POST[$f] ?? $c[$f];
            }

            // Create delivery file if marked as customer
            if (isset($_POST['is_customer']) && strtolower(trim($_POST['is_customer'])) === 'yes') {
                if (!createDeliveryFileIfNeeded($id)) {
                    echo "<div class='alert-error'>⚠️ Delivery file could not be created for customer ID: $id</div>";
                }
            }

            break;
        }
    }
    writeCSV('contacts.csv', $contacts, $schema);
    header("Location: customer_view.php?id=" . urlencode($id));
    exit;
}

// Load contact
$id = $_GET['id'] ?? '';
$contact = findContactById($contacts, $id);

if (!$contact) {
    echo "<div class='container'><h2>Contact not found</h2></div>";
    include_once(__DIR__ . '/layout_end.php');
    exit;
}
// Delivery of the tanks to database

$deliveryFile = "{$contact['id']}_deliveries.csv";
if (file_exists(__DIR__ . "/$deliveryFile")) {
    echo '<a href="' . htmlspecialchars($deliveryFile) . '" class="btn-secondary" target="_blank">📦 View Deliveries</a>';
} else {
    echo '<p>No deliveries found for this customer.</p>';
}
?>

?>

<div class="container">
  <h2>Customer Details</h2>
  

  <form method="post" class="contact-form">
    <input type="hidden" name="id" value="<?= htmlspecialchars($contact['id']) ?>">

    <div class="form-grid">
      <?php foreach ($schema as $f): ?>
        <div class="form-group">
          <label for="<?= $f ?>"><strong><?= ucfirst(str_replace('_', ' ', $f)) ?>:</strong></label><br>
          <?php if ($f === 'notes' || str_contains($f, 'description')): ?>
            <textarea name="<?= $f ?>" id="<?= $f ?>" rows="3"><?= htmlspecialchars($contact[$f] ?? '') ?></textarea>
          <?php elseif ($f === 'id'): ?>
            <input type="text" id="<?= $f ?>" value="<?= htmlspecialchars($contact[$f] ?? '') ?>" disabled>
          <?php else: ?>
            <input type="text" name="<?= $f ?>" id="<?= $f ?>" value="<?= htmlspecialchars($contact[$f] ?? '') ?>">
          <?php endif; ?>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="form-actions">
  <button type="submit" class="btn-primary">💾 Save Changes</button>
	</div>

<div class="customer-actions">
    <a href="edit_customer.php?id=code($contact[" class="btn-primary">✏️ Edit</a>
    index.php⬅ Back</a>
    <?php
    $deliveryFile = "{$contact['id']}_deliveries.csv";
    if (file_exists(__DIR__ . "/$deliveryFile")) {
        echo '<a href="' . htmlspecialchars($deliveryFile) . '" class="btn-secondary" target="_blank">📦 View Deliveries</a>';
    }
    ?>
</div>

<?php
if (strtolower(trim($contact['is_customer'] ?? '')) === 'yes') {
    $deliveryFile = "{$contact['id']}_deliveries.csv";
    $deliveryFilePath = __DIR__ . "/$deliveryFile";
    if (file_exists($deliveryFilePath)) {
        $deliveryUrl = "deliveries.php?id=" . urlencode($contact['id']);
        echo '<a href="' . $deliveryUrl . '" class="btn-primary">📦 View Delivery Archive</a>';
    }
}
?>




<?php include_once(__DIR__ . '/layout_end.php'); ?>