<?php
include_once(__DIR__ . '/layout_start.php');
$currentPage = basename(__FILE__);
require_once 'csv_handler.php';

$schemaFile = __DIR__ . '/contact_schema.php';
if (!file_exists($schemaFile)) {
    die('Error: contact_schema.php not found.');
}
$schema = require $schemaFile;
if (!is_array($schema)) {
    die('Error: contact_schema.php must return an array.');
}

$contacts = readCSV('contacts.csv', $schema);
if (!is_array($contacts)) {
    $contacts = [];
}

// Extract unique values for dynamic filters
$statusOptions = [];
$tagOptions = [];
foreach ($contacts as $c) {
    if (!empty($c['status'])) {
        $statusOptions[$c['status']] = true;
    }
    if (!empty($c['tags'])) {
        foreach (explode(',', $c['tags']) as $tag) {
            $tagOptions[trim($tag)] = true;
        }
    }
}
ksort($statusOptions);
ksort($tagOptions);

// Handle query parameters
$query = strtolower(trim($_GET['query'] ?? ''));
$statusFilter = $_GET['status'] ?? '';
$tagFilter = $_GET['tag'] ?? '';
$sortField = $_GET['sort'] ?? '';
$sortDirection = $_GET['direction'] ?? 'asc';
$page = max(1, intval($_GET['page'] ?? 1));
$perPage = 25;

// Filter contacts
$contacts = array_filter($contacts, function($c) use ($query, $statusFilter, $tagFilter) {
    $matchQuery = $query === '' || (
        stripos($c['first_name'] ?? '', $query) !== false ||
        stripos($c['last_name'] ?? '', $query) !== false ||
        stripos($c['email'] ?? '', $query) !== false ||
        stripos($c['company'] ?? '', $query) !== false
    );
    $matchStatus = $statusFilter === '' || ($c['status'] ?? '') === $statusFilter;
    $matchTag = $tagFilter === '' || in_array($tagFilter, array_map('trim', explode(',', $c['tags'] ?? '')));
    return $matchQuery && $matchStatus && $matchTag;
});

// Sort contacts
if ($sortField && in_array($sortField, $schema)) {
    usort($contacts, function($a, $b) use ($sortField, $sortDirection) {
        $valA = $a[$sortField] ?? '';
        $valB = $b[$sortField] ?? '';
        $cmp = is_numeric($valA) && is_numeric($valB) ? ($valA <=> $valB) : strnatcasecmp($valA, $valB);
        return $sortDirection === 'desc' ? -$cmp : $cmp;
    });
}

// Pagination
$total = count($contacts);
$contacts = array_slice($contacts, ($page - 1) * $perPage, $perPage);

// Export CSV
if (isset($_GET['export']) && $_GET['export'] === '1') {
    $filename = 'contacts_export_' . date('Ymd_His') . '.csv';
    header('Content-Type: text/csv');
    header("Content-Disposition: attachment; filename="$filename"");
    $output = fopen('php://output', 'w');
    fputcsv($output, $schema);
    foreach ($contacts as $contact) {
        $row = [];
        foreach ($schema as $f) {
            $row[] = $contact[$f] ?? '';
        }
        fputcsv($output, $row);
    }
    fclose($output);
    exit;
}
?>

<div class="container">
  <h2>Contact List</h2>

  <form method="get" class="filter-form">
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
    <a href="?export=1" class="btn-outline">⬇ Export CSV</a>
  </form>

  <table class="table-grid">
    <thead>
      <tr>
        <?php foreach ($schema as $field): ?>
          <th>
            <a href="?<?= http_build_query(array_merge($_GET, ['sort' => $field, 'direction' => ($sortField === $field && $sortDirection === 'asc') ? 'desc' : 'asc'])) ?>">
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
          <?php foreach ($schema as $f): ?>
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
