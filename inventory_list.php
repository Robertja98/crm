
<?php

include_once(__DIR__ . '/layout_start.php');
require_once 'inventory_mysql.php';

$schema = require __DIR__ . '/inventory_schema.php';
$items = fetch_inventory_mysql($schema);
$ledgerRows = read_ledger_mysql();
$serialRows = read_serials_mysql();
$ledgerStatuses = [];
$serialStatuses = [];
$statusTotalsByItem = [];

foreach ($ledgerRows as $entry) {
    $itemId = trim($entry['item_id'] ?? '');
    $status = trim($entry['status'] ?? '');
    $qty = is_numeric($entry['quantity'] ?? '') ? (float)$entry['quantity'] : 0.0;
    add_status_total($statusTotalsByItem, $itemId, $status, $qty);
    if ($status !== '') {
        $ledgerStatuses[] = $status;
    }
}

foreach ($serialRows as $entry) {
    $itemId = trim($entry['item_id'] ?? '');
    $status = trim($entry['status'] ?? '');
    add_status_total($statusTotalsByItem, $itemId, $status, 1.0);
    if ($status !== '') {
        $serialStatuses[] = $status;
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status_action'])) {
    $action = trim($_POST['status_action'] ?? '');
    $value = trim($_POST['status_value'] ?? '');
    $options = read_status_options_mysql();
    if ($action === 'add' && $value !== '') {
        if (!in_array($value, $options, true)) {
            $options[] = $value;
        }
    } elseif ($action === 'remove' && $value !== '') {
        $options = array_values(array_filter($options, function($opt) use ($value) {
            return $opt !== $value;
        }));
    }
    sort($options);
    write_status_options_mysql($options);
    header('Content-Type: application/json');
    echo json_encode(['statusOptions' => $options]);
    exit;
}
  $ledgerStatuses = [];
  $serialStatuses = [];
  $statusTotalsByItem = [];

  foreach ($ledgerRows as $entry) {
      $itemId = trim($entry['item_id'] ?? '');
      $status = trim($entry['status'] ?? '');
      $qty = is_numeric($entry['quantity'] ?? '') ? (float)$entry['quantity'] : 0.0;
      add_status_total($statusTotalsByItem, $itemId, $status, $qty);
      if ($status !== '') {
          $ledgerStatuses[] = $status;
      }
  }

  foreach ($serialRows as $entry) {
      $itemId = trim($entry['item_id'] ?? '');
      $status = trim($entry['status'] ?? '');
      add_status_total($statusTotalsByItem, $itemId, $status, 1.0);
      if ($status !== '') {
          $serialStatuses[] = $status;
      }
  }

  if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['status_action'])) {
      $action = trim($_POST['status_action'] ?? '');
      $value = trim($_POST['status_value'] ?? '');
      $options = read_status_options_mysql();
      if ($action === 'add' && $value !== '') {
          if (!in_array($value, $options, true)) {
              $options[] = $value;
          }
      } elseif ($action === 'remove' && $value !== '') {
          $options = array_values(array_filter($options, function($opt) use ($value) {
              return $opt !== $value;
          }));
      }
      sort($options);
      write_status_options_mysql($options);
      header('Content-Type: application/json');
      echo json_encode(['statusOptions' => $options]);
      exit;
  }

  // TODO: Implement inventory update logic using PostgreSQL if needed

  // Build filter array from GET
  $filters = [];
  foreach ($schema as $f) {
      $filters[$f] = isset($_GET[$f]) ? trim($_GET[$f]) : '';
  }

  // Bypass filtering: always show all items
  $filtered = $items;

  // Load status options from MySQL
  $statusOptions = ['Stock', 'Production'];
  $statusOptions = array_values(array_unique(array_merge(
      $statusOptions,
      read_status_options_mysql(),
      $ledgerStatuses,
      $serialStatuses
  )));
  if (!is_array($statusOptions)) {
      $statusOptions = [];
  }
  foreach ($items as $row) {
      $status = trim($row['status'] ?? '');
      if ($status !== '' && !in_array($status, $statusOptions, true)) {
          $statusOptions[] = $status;
      }
  }
  sort($statusOptions);
?>
<style>
    .inv-list-item { margin-bottom: 8px; }
    .inv-list-item.active {
      background: #e9eef6;
      border-color: #8aa4c8;
    }
    .inv-list-title { font-weight: 600; margin-bottom: 4px; }
    .inv-list-meta {
      display: flex;
      gap: 8px;
      flex-wrap: wrap;
      font-size: 0.9em;
      color: #555;
    }
    .inv-pill {
      display: inline-flex;
      align-items: center;
      padding: 2px 8px;
      border-radius: 999px;
      background: #f1f3f6;
      border: 1px solid #d9dee7;
    }
    .inv-pill.status { background: #eef4ff; border-color: #cddcff; color: #274690; }
    .inv-pill.qty { background: #e9f6ec; border-color: #b8e0c2; color: #1f6a35; }
    .inv-pill.stock-level { background: #fff6e5; border-color: #f5d19a; color: #8a5a00; }
    .inv-pill.low { background: #ffe8e8; border-color: #f2b6b6; color: #9b1c1c; }
    .inv-pill.ok { background: #e9f6ec; border-color: #b8e0c2; color: #1f6a35; }
    .inv-detail {
      border: 1px solid #e2e2e2;
      border-radius: 8px;
      padding: 14px;
      background: #fff;
      min-height: 200px;
    }
    .inv-detail-panel { display: none; }
    .inv-detail-panel.active { display: block; }
    .inv-detail-title { font-weight: 700; margin-bottom: 10px; }
    .inv-section {
      margin-bottom: 14px;
      border: 1px solid #e6e6e6;
      border-radius: 6px;
      padding: 10px 12px;
      background: #fafafa;
    }
    .inv-section-title {
      font-weight: 800;
      font-size: 1.05em;
      margin-bottom: 8px;
    }
    .inv-section-grid {
      display: grid;
      grid-template-columns: 160px 1fr 160px 1fr;
      gap: 8px 16px;
      align-items: center;
    }
    .inv-label { font-weight: 600; color: #222; }
    .inv-input {
      width: 100%;
      padding: 6px 8px;
      border-radius: 4px;
      border: 1px solid #e6e6e6;
      background: #fff;
      box-sizing: border-box;
      font-size: 0.95em;
    }
    .inv-input[readonly] { background: #f5f5f5; }
    .inv-textarea { height: 70px; resize: vertical; }
    @media (max-width: 900px) {
      .inv-layout { grid-template-columns: 1fr; }
      .inv-list { max-height: none; }
      .inv-section-grid { grid-template-columns: 160px 1fr; }
    }
  </style>
  <?php if (empty($filtered)): ?>
    <div style="text-align:center; color:#888;">No items found.</div>
  <?php else: ?>
    <div class="container" style="margin:32px auto;max-width:900px;">
      <h3 style="margin-bottom:24px;">Inventory Items</h3>
      <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
        <?php foreach ($filtered as $item): ?>
          <div class="col">
            <div class="card shadow-sm h-100">
              <div class="card-body">
                <h5 class="card-title mb-2"><?= htmlspecialchars($item['item_name']) ?> <span class="badge bg-secondary ms-2">ID: <?= htmlspecialchars($item['item_id']) ?></span></h5>
                <p class="card-text mb-1"><b>Qty:</b> <?= htmlspecialchars($item['quantity_in_stock']) ?></p>
                <?php if (!empty($item['description'])): ?>
                  <p class="card-text text-muted" style="font-size:0.95em;"><?= htmlspecialchars($item['description']) ?></p>
                <?php endif; ?>
                <div class="d-flex gap-2 mt-3">
                  <a href="inventory_edit.php?item_id=<?= urlencode($item['item_id']) ?>" class="btn btn-sm btn-primary">Edit</a>
                  <form action="inventory_delete.php" method="post" style="display:inline;">
                    <input type="hidden" name="item_id" value="<?= htmlspecialchars($item['item_id']) ?>">
                    <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this item?');">Delete</button>
                  </form>
                </div>
              </div>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  <!-- Bootstrap CSS for modern look -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <?php endif; ?>
</div>
<?php include_once(__DIR__ . '/layout_end.php'); ?>
