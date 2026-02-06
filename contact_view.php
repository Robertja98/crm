<?php
session_start();

// Security headers
header('Content-Type: text/html; charset=utf-8');
header('X-Content-Type-Options: nosniff');

// Include layout and dependencies (all files are in root)
include_once('layout_start.php');
include_once('navbar.php');
require_once 'csv_handler.php';
require_once 'discussion_validator.php';

// Load schemas and data
$schema = require 'contact_schema.php';
$customerSchema = require 'customer_schema.php';
$contacts = readCSV('contacts.csv', $schema);
$customers = readCSV('customers.csv', $customerSchema);

// Index contacts by ID for faster lookup
$contactsById = [];
foreach ($contacts as $c) {
    if (empty($c['id'])) {
        $c['id'] = 'CNT_' . date('YmdHis') . '_' . bin2hex(random_bytes(3));
    }
    $contactsById[$c['id']] = $c;
}

// Helper: resolve customer by ID
function findCustomerById($customers, $cid) {
    foreach ($customers as $c) {
        if ($c['customer_id'] === $cid) return $c;
    }
    return null;
}

// Handle POST requests
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Update contact details
    if (isset($_POST['id']) && isset($contactsById[$_POST['id']])) {
        $id = $_POST['id'];
        foreach ($schema as $f) {
            $contactsById[$id][$f] = htmlspecialchars(trim($_POST[$f] ?? $contactsById[$id][$f]));
        }
        if (isset($_POST['is_customer'])) {
            $contactsById[$id]['is_customer'] = $_POST['is_customer'];
        }
        writeCSV('contacts.csv', array_values($contactsById), $schema);
        header("Location: contact_view.php?id=" . urlencode($id));
        exit;
    }

    // Handle discussion entry
    if (isset($_POST['contact_id']) && isset($contactsById[$_POST['contact_id']])) {
        $data = [
            'contact_id' => $_POST['contact_id'],
            'author' => htmlspecialchars(trim($_POST['author'])),
            'timestamp' => date('Y-m-d H:i'),
            'entry_text' => htmlspecialchars(trim($_POST['entry_text'])),
            'linked_opportunity_id' => htmlspecialchars($_POST['linked_opportunity_id'] ?? ''),
            'visibility' => htmlspecialchars($_POST['visibility'] ?? 'public')
        ];
        $fp = fopen('discussion_log.csv', 'a');
        if ($fp) {
            fputcsv($fp, array_values($data));
            fclose($fp);
        }
        header("Location: contact_view.php?id=" . urlencode($data['contact_id']));
        exit;
    }
}

// Load contact
$id = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_STRING);
$contact = $contactsById[$id] ?? null;

if (!$contact) {
    echo "<div class='container'><h2>Contact not found</h2></div>";
    include_once('layout_end.php');
    exit;
}

// Resolve associated customer
$customer = findCustomerById($customers, $contact['customer_id'] ?? '');
?>

<div class="container">
  <h2>Contact Details</h2>

  <div class="contact-header" style="display: flex; align-items: center; justify-content: space-between;">
    <?php if ($customer): ?>
      <div class="customer-info">
        <strong>Company:</strong> <?= htmlspecialchars($customer['contact_name'] ?? '') ?><br>
        <strong>Address:</strong> <?= htmlspecialchars($customer['address'] ?? '') ?>
      </div>
    <?php else: ?>
      <div class="customer-info muted">
        <em>This contact is not assigned to a company.</em>
      </div>
    <?php endif; ?>

    <?php if (($contact['is_customer'] ?? '') !== 'yes'): ?>
      <form method="post" style="margin-left: 2em;">
        <input type="hidden" name="id" value="<?= htmlspecialchars($contact['id']) ?>">
        <input type="hidden" name="is_customer" value="yes">
        <button type="submit" class="btn-outline">✅ Mark as Client</button>
      </form>
    <?php else: ?>
      <p style="margin-left: 2em;"><strong>Status:</strong> This contact is already marked as a client.</p>
    <?php endif; ?>
  </div>

  <!-- Editable Contact Form -->
  <form method="post" class="contact-form">
    <input type="hidden" name="id" value="<?= htmlspecialchars($contact['id']) ?>">

    <div class="form-grid">
      <div class="form-group">
        <label for="customer_id"><strong>Company:</strong></label><br>
        <select name="customer_id" id="customer_id">
          <option value="">-- None --</option>
          <?php foreach ($customers as $cust): ?>
            <option value="<?= htmlspecialchars($cust['customer_id']) ?>"
              <?= ($contact['customer_id'] ?? '') === $cust['customer_id'] ? 'selected' : '' ?>>
              <?= htmlspecialchars($cust['contact_name']) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <?php foreach ($schema as $f): ?>
        <div class="form-group">
          <label for="<?= $f ?>"><strong><?= ucfirst(str_replace('_', ' ', $f)) ?>:</strong></label><br>
          <?php if ($f === 'notes' || str_contains($f, 'description')): ?>
            <textarea name="<?= $f ?>" id="<?= $f ?>" rows="3"><?= htmlspecialchars($contact[$f] ?? '') ?></textarea>
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

  <?php include_once('discussion_module.php'); ?>
  <?php include_once('follow_up_email.php'); ?>

  <div class="navigation">
    <a href="contacts_list.php" class="btn-outline">⬅ Back to Contact List</a>
  </div>
</div>

<?php include_once('layout_end.php'); ?>
