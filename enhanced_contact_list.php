// This file has been removed as requested. All functionality is now in contacts_list.php.
// Unconditional execution log for troubleshooting
file_put_contents(__DIR__ . '/enhanced_contact_list_exec.txt', 'Executed at ' . date('Y-m-d H:i:s') . "\n", FILE_APPEND);

<?php
session_start();

include_once(__DIR__ . '/layout_start.php');
$currentPage = basename(__FILE__);
require_once 'db_mysql.php';

$schemaFile = __DIR__ . '/contact_schema.php';
if (!file_exists($schemaFile)) {
  die('Error: contact_schema.php not found.');
// DEBUG: Log state for troubleshooting (moved to very top)
}
$schema = require $schemaFile;
if (!is_array($schema)) {
  die('Error: contact_schema.php must return an array.');
}


// Handle custom column selection (session + cookie + GET)
function getColumnsFromGet($schema) {
  if (!empty($_GET['columns'])) {
    $cols = $_GET['columns'];
    if (!is_array($cols)) $cols = [$cols];
    return array_values(array_intersect($schema, $cols));
  }
  return null;
}

if (isset($_POST['custom_columns']) && is_array($_POST['custom_columns'])) {
  $selected = array_values(array_intersect($schema, $_POST['custom_columns']));
  $_SESSION['custom_columns'] = $selected;
  setcookie('custom_columns', json_encode($selected), time() + 60*60*24*30, '/');
  // Redirect to GET with columns[] in URL
  $colParams = '';
  foreach ($selected as $col) { $colParams .= '&columns[]=' . urlencode($col); }
  header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?') . '?' . ltrim($colParams, '&'));
  exit;
}
if (isset($_POST['reset_columns'])) {
  unset($_SESSION['custom_columns']);
  setcookie('custom_columns', '', time() - 3600, '/');
  header('Location: ' . strtok($_SERVER['REQUEST_URI'], '?'));
  exit;
}
$activeColumns = getColumnsFromGet($schema);
if (!$activeColumns) {
  if (isset($_SESSION['custom_columns'])) {
    $activeColumns = $_SESSION['custom_columns'];
  } elseif (!empty($_COOKIE['custom_columns'])) {
    $decoded = json_decode($_COOKIE['custom_columns'], true);
    $activeColumns = is_array($decoded) ? array_values(array_intersect($schema, $decoded)) : $schema;
    $_SESSION['custom_columns'] = $activeColumns;
  } else {
    $activeColumns = $schema;
  }
}


// Handle query parameters
$query = strtolower(trim($_GET['query'] ?? ''));
$statusFilter = $_GET['status'] ?? '';
$tagFilter = $_GET['tag'] ?? '';
$sortField = $_GET['sort'] ?? '';
$sortDirection = $_GET['direction'] ?? 'asc';
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 25;

$conn = get_mysql_connection();

// Build WHERE clause and bind params for prepared statements
$whereParts = [];
$bindTypes = '';
$bindValues = [];

if ($query !== '') {
  $likeVal = '%' . $query . '%';
  $whereParts[] = '(LOWER(first_name) LIKE ? OR LOWER(last_name) LIKE ? OR LOWER(email) LIKE ? OR LOWER(company) LIKE ?)';
  $bindTypes .= 'ssss';
  $bindValues[] = $likeVal;
  $bindValues[] = $likeVal;
  $bindValues[] = $likeVal;
  $bindValues[] = $likeVal;
}
if ($statusFilter !== '') {
  $whereParts[] = 'status = ?';
  $bindTypes .= 's';
  $bindValues[] = $statusFilter;
}
if ($tagFilter !== '') {
  $whereParts[] = 'FIND_IN_SET(?, tags)';
  $bindTypes .= 's';
  $bindValues[] = $tagFilter;
}
$whereClause = $whereParts ? ('WHERE ' . implode(' AND ', $whereParts)) : '';

// Build ORDER BY
if ($sortField && in_array($sortField, $schema, true)) {
  $orderBy = 'ORDER BY `' . $sortField . '` ' . ($sortDirection === 'desc' ? 'DESC' : 'ASC');
} else {
  $orderBy = '';
}

$selectCols = implode(',', array_map(function($col) { return "`$col`"; }, $activeColumns));
$offset = ($page - 1) * $perPage;

// Helper: run prepared statement with dynamic params and return result set
function run_parameterized(mysqli $conn, string $sql, string $types, array $values): ?mysqli_result {
  $stmt = $conn->prepare($sql);
  if (!$stmt) {
    return null;
  }
  if ($types !== '') {
    $stmt->bind_param($types, ...$values);
  }
  $stmt->execute();
  $res = $stmt->get_result();
  $stmt->close();
  return $res ?: null;
}

// Get total count for pagination
$countRes = run_parameterized($conn, "SELECT COUNT(*) as cnt FROM contacts $whereClause", $bindTypes, $bindValues);
$total = ($countRes && ($row = $countRes->fetch_assoc())) ? intval($row['cnt']) : 0;
if ($countRes) {
  $countRes->free();
}

// Main select
$contacts = [];
$dataRes = run_parameterized(
  $conn,
  "SELECT $selectCols FROM contacts $whereClause $orderBy LIMIT $perPage OFFSET $offset",
  $bindTypes . 'ii',
  array_merge($bindValues, [$perPage, $offset])
);
if ($dataRes) {
  while ($row = $dataRes->fetch_assoc()) {
    $contacts[] = $row;
  }
  $dataRes->free();
}

// Extract unique values for dynamic filters (status, tags)
$statusOptions = [];
$tagOptions = [];
$statusRes = $conn->query("SELECT DISTINCT status FROM contacts WHERE status IS NOT NULL AND status != ''");
if ($statusRes) {
  while ($row = $statusRes->fetch_assoc()) {
    $statusOptions[$row['status']] = true;
  }
  $statusRes->free();
}
$tagRes = $conn->query("SELECT tags FROM contacts WHERE tags IS NOT NULL AND tags != ''");
if ($tagRes) {
  while ($row = $tagRes->fetch_assoc()) {
    foreach (explode(',', $row['tags']) as $tag) {
      $tagOptions[trim($tag)] = true;
    }
  }
  $tagRes->free();
}
ksort($statusOptions);
ksort($tagOptions);

// Export CSV
if (isset($_GET['export']) && $_GET['export'] === '1') {
  $filename = 'contacts_export_' . date('Ymd_His') . '.csv';
  header('Content-Type: text/csv');
  header("Content-Disposition: attachment; filename=\"$filename\"");
  $output = fopen('php://output', 'w');
  fputcsv($output, $activeColumns);
  foreach ($contacts as $contact) {
    $row = [];
    foreach ($activeColumns as $f) {
      $row[] = $contact[$f] ?? '';
    }
    fputcsv($output, $row);
  }
  fclose($output);
  exit;
}
?>

<?php if (!empty($GLOBALS['debug_log_error'])): ?>
  <div style="background:#ffdddd;color:#a00;padding:10px;margin:10px 0;border:1px solid #a00;font-weight:bold;">
    <?= htmlspecialchars($GLOBALS['debug_log_error']) ?>
  </div>
<?php endif; ?>
<div class="container">
  <h2>Contact List</h2>


  <!-- Custom Columns Form -->

  <form method="post" class="filter-form" style="margin-bottom:18px;">
    <label style="font-weight:600;">Customize Columns:</label>
    <?php foreach ($schema as $col): ?>
      <label style="margin-right:10px;">
        <input type="checkbox" name="custom_columns[]" value="<?= htmlspecialchars($col) ?>" <?= in_array($col, $activeColumns) ? 'checked' : '' ?>>
        <?= htmlspecialchars(ucwords(str_replace('_', ' ', $col))) ?>
      </label>
    <?php endforeach; ?>
    <button type="submit" style="margin-left:10px;">Apply</button>
    <button type="submit" name="reset_columns" value="1" style="margin-left:5px;">Reset</button>
  </form>

  <form method="get" class="filter-form">
    <?php foreach ($activeColumns as $col): ?>
      <input type="hidden" name="columns[]" value="<?= htmlspecialchars($col) ?>">
    <?php endforeach; ?>
    <input type="text" name="query" placeholder="Search..." value="<?= htmlspecialchars($_GET['query'] ?? '') ?>">
    <select name="status">
      <option value="">-- Status --</option>
      <?php foreach (array_keys($statusOptions) as $status): ?>
        <option value="<?= htmlspecialchars($status) ?>" <?= $status === $statusFilter ? 'selected' : '' ?>><?= htmlspecialchars($status) ?></option>
      <?php endforeach; ?>
    </select>
    <select name="tag">
      <option value="">-- Tag --</option>
      <?php foreach (array_keys($tagOptions) as $tag): ?>
        <option value="<?= htmlspecialchars($tag) ?>" <?= $tag === $tagFilter ? 'selected' : '' ?>><?= htmlspecialchars($tag) ?></option>
      <?php endforeach; ?>
    </select>
    <button type="submit">🔍 Filter</button>
    <a href="?export=1<?php foreach ($activeColumns as $col) { echo '&columns[]=' . urlencode($col); } ?>" class="btn-outline">⬇ Export CSV</a>
  </form>

  <table class="table-grid">
    <thead>
      <tr>
        <?php foreach ($activeColumns as $field): ?>
          <th>
            <?php
              // Build sort link with all columns[] as columns[]=...
              $sortParams = $_GET;
              $sortParams['sort'] = $field;
              $sortParams['direction'] = ($sortField === $field && $sortDirection === 'asc') ? 'desc' : 'asc';
              // Always build columns[] as repeated params
              $colQuery = '';
              foreach ($activeColumns as $col) { $colQuery .= '&columns[]=' . urlencode($col); }
              $sortUrl = '?' . http_build_query($sortParams) . $colQuery;
            ?>
            <a href="<?= $sortUrl ?>">
              <?= htmlspecialchars(ucwords(str_replace('_', ' ', $field))) ?>
              <?= $sortField === $field ? ($sortDirection === 'asc' ? '↑' : '↓') : '' ?>
            </a>
          </th>
        <?php endforeach; ?>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($contacts as $c): ?>
        <tr>
          <?php foreach ($activeColumns as $f): ?>
            <td><?= htmlspecialchars($c[$f] ?? '') ?></td>
          <?php endforeach; ?>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>

  <div class="pagination">
    <?php for ($i = 1; $i <= ceil($total / $perPage); $i++): ?>
      <a href="?<?= http_build_query(array_merge($_GET, ['page' => $i])) ?>" class="<?= $i === $page ? 'active' : '' ?>"><?= $i ?></a>
    <?php endfor; ?>
  </div>
</div>

<script>
document.querySelector('input[name="query"]').addEventListener('input', function(e) {
  const form = e.target.closest('form');
  clearTimeout(window.liveSearchTimeout);
  window.liveSearchTimeout = setTimeout(() => form.submit(), 500);
});
</script>

<?php include_once(__DIR__ . '/layout_end.php'); ?>
