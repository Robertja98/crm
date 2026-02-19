<?php


// Security headers
header('Content-Type: text/html; charset=utf-8');
header('X-Content-Type-Options: nosniff');

// Includes
include_once(__DIR__ . '/layout_start.php');
require_once 'db_mysql.php';
require_once 'discussion_validator.php';

// Load schemas and data

$schema = require __DIR__ . '/contact_schema.php';

// Helper: fetch contact by ID from MySQL
function fetchContactById($id, $schema) {
    if ($id === '') return null;
    $conn = get_mysql_connection();
    $fields = implode(',', array_map(function($f) { return '`' . $f . '`'; }, $schema));
    $stmt = $conn->prepare("SELECT $fields FROM contacts WHERE id = ? LIMIT 1");
    $stmt->bind_param('s', $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $contact = $result ? $result->fetch_assoc() : null;
    $stmt->close();
    $conn->close();
    return $contact;
}

// Helper: update contact in MySQL
function updateContactById($id, $schema, $postData) {
    $conn = get_mysql_connection();
    $fields = [];
    $values = [];
    foreach ($schema as $f) {
        if ($f === 'id') continue;
        $fields[] = "`$f` = ?";
        $values[] = $postData[$f] ?? null;
    }
    $values[] = $id;
    $sql = "UPDATE contacts SET " . implode(',', $fields) . ", last_modified = NOW() WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $types = str_repeat('s', count($values));
    $stmt->bind_param($types, ...$values);
    $stmt->execute();
    $stmt->close();
    $conn->close();
}

// ...existing code...

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
    updateContactById($id, $schema, $_POST);
    // Create delivery file if marked as customer
    if (isset($_POST['is_customer']) && strtolower(trim($_POST['is_customer'])) === 'yes') {
        if (!createDeliveryFileIfNeeded($id)) {
            echo "<div class='alert-error'>⚠️ Delivery file could not be created for customer ID: $id</div>";
        }
    }
    header("Location: customer_view.php?id=" . urlencode($id));
    exit;
}


// Load contact from MySQL
$id = $_GET['id'] ?? '';
$contact = fetchContactById($id, $schema);

if (!$contact) {
    echo "<div class='container'><h2>Contact not found</h2></div>";
    include_once(__DIR__ . '/layout_end.php');
    exit;
}
?>

<div class="container">
    <h2>Customer Details</h2>
  
    <form method="post" class="contact-form" id="edit">
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
    </form>

    <div class="customer-actions">
        <a href="customers_list.php" class="btn-outline">⬅ Back to Customers</a>
        <a href="index.php" class="btn-outline">⬅ Back to Home</a>
        <?php
        $deliveryFile = "{$contact['id']}_deliveries.csv";
        if (file_exists(__DIR__ . "/$deliveryFile")) {
                echo '<a href="' . htmlspecialchars($deliveryFile) . '" class="btn-secondary" target="_blank">📦 View Deliveries</a>';
        }

        $isCustomer = strtolower(trim($contact['is_customer'] ?? '')) === 'yes';
        $deliveryFilePath = __DIR__ . "/$deliveryFile";
        if ($isCustomer && file_exists($deliveryFilePath)) {
                $deliveryUrl = "deliveries.php?id=" . urlencode($contact['id']);
                echo '<a href="' . $deliveryUrl . '" class="btn-primary">📦 View Delivery Archive</a>';
        }
        ?>
    </div>
</div>

<?php include_once(__DIR__ . '/layout_end.php'); ?>