<?php
require_once 'db_mysql.php';
require_once 'archive_customer.php';
require_once __DIR__ . '/csrf_helper.php';
require_once __DIR__ . '/simple_auth/middleware.php';

$customerId = trim((string) ($_GET['customer_id'] ?? $_POST['customer_id'] ?? ''));

if ($customerId === '') {
  header('Location: customers_list.php');
  exit;
}

// ── POST: confirmed delete ────────────────────────────────────────────────────
$requestMethod = strtoupper((string) ($_SERVER['REQUEST_METHOD'] ?? 'GET'));
if ($requestMethod === 'POST') {
  if (!verifyCSRFToken($_POST['csrf_token'] ?? '')) {
    header('Location: customers_list.php?error=invalid_request');
    exit;
  }

  archive_customer($customerId);

  $conn = get_mysql_connection();
  $conn->begin_transaction();
  try {
    // Unlink equipment (keep records, just clear customer FK)
    $s = $conn->prepare('UPDATE equipment SET customer_id = NULL WHERE customer_id = ?');
    $s->bind_param('s', $customerId);
    $s->execute();
    $s->close();

    $s = $conn->prepare('DELETE FROM customers WHERE customer_id = ?');
    $s->bind_param('s', $customerId);
    $s->execute();
    $s->close();

    $conn->commit();
  } catch (Exception $e) {
    $conn->rollback();
    $conn->close();
    include_once(__DIR__ . '/layout_start.php');
    echo '<div class="container"><p style="color:red;">Failed to delete customer: ' . htmlspecialchars($e->getMessage()) . '</p></div>';
    include_once(__DIR__ . '/layout_end.php');
    exit;
  }
  $conn->close();
  header('Location: customers_list.php?deleted=1');
  exit;
}

// ── GET: show confirmation ────────────────────────────────────────────────────
$conn = get_mysql_connection();
$stmt = $conn->prepare(
  'SELECT c.customer_id, c.address, c.last_delivery,
          CONCAT(ct.first_name, " ", ct.last_name) AS contact_name, ct.company
   FROM customers c
   LEFT JOIN contacts ct ON ct.contact_id = c.contact_id
   WHERE c.customer_id = ? LIMIT 1'
);
$stmt->bind_param('s', $customerId);
$stmt->execute();
$customer = $stmt->get_result()->fetch_assoc();
$stmt->close();

// Count tanks
$tankCount = 0;
$tankStmt = $conn->prepare('SELECT COUNT(*) AS cnt FROM equipment WHERE customer_id = ?');
if ($tankStmt) {
  $tankStmt->bind_param('s', $customerId);
  $tankStmt->execute();
  $countResult = $tankStmt->get_result();
  if ($countResult && ($countRow = $countResult->fetch_assoc())) {
    $tankCount = (int) ($countRow['cnt'] ?? 0);
  }
  if ($countResult) {
    $countResult->free();
  }
  $tankStmt->close();
}
$conn->close();

if (!$customer) {
  header('Location: customers_list.php');
  exit;
}

include_once(__DIR__ . '/layout_start.php');
?>

<div class="container" style="max-width:520px;">
  <h2>🗑️ Delete Customer</h2>
  <p>Are you sure you want to permanently delete this customer?</p>

  <table style="width:100%;border-collapse:collapse;margin-bottom:20px;">
    <tr><th style="text-align:left;padding:6px 8px;background:#f5f5f5;">Customer ID</th><td style="padding:6px 8px;"><?= htmlspecialchars($customer['customer_id']) ?></td></tr>
    <tr><th style="text-align:left;padding:6px 8px;background:#f5f5f5;">Contact</th><td style="padding:6px 8px;"><?= htmlspecialchars($customer['contact_name'] ?? '—') ?></td></tr>
    <tr><th style="text-align:left;padding:6px 8px;background:#f5f5f5;">Company</th><td style="padding:6px 8px;"><?= htmlspecialchars($customer['company'] ?? '—') ?></td></tr>
    <tr><th style="text-align:left;padding:6px 8px;background:#f5f5f5;">Address</th><td style="padding:6px 8px;"><?= htmlspecialchars($customer['address'] ?? '—') ?></td></tr>
    <tr><th style="text-align:left;padding:6px 8px;background:#f5f5f5;">Tanks on record</th><td style="padding:6px 8px;"><?= $tankCount ?></td></tr>
  </table>

  <p style="color:#c00;"><strong>This cannot be undone.</strong> The customer record will be archived in the audit log and removed. <?= $tankCount > 0 ? "$tankCount tank(s) will be unlinked but not deleted." : '' ?></p>

  <form method="POST">
    <?php renderCSRFInput(); ?>
    <input type="hidden" name="customer_id" value="<?= htmlspecialchars($customerId) ?>">
    <a href="customers_list.php" class="btn-outline" style="margin-right:10px;">Cancel</a>
    <button type="submit" class="btn-danger">🗑️ Confirm Delete</button>
  </form>
</div>

<?php include_once(__DIR__ . '/layout_end.php'); ?>
