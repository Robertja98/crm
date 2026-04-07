<?php
// discussion.php - Discussion Log Page
require_once __DIR__ . '/layout_start.php';
require_once __DIR__ . '/simple_auth/middleware.php';
require_once 'db_mysql.php';

$pageTitle = 'Discussion Log';

$conn = get_mysql_connection();
$result = $conn->query('SELECT * FROM discussion_log ORDER BY timestamp DESC LIMIT 100');
$discussions = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];
$result && $result->free();
$conn->close();
?>
<div class="container mt-4">
  <h1 class="mb-4">Discussion Log</h1>
  <table class="table table-striped table-hover">
    <thead>
      <tr>
        <th>Timestamp</th>
        <th>Author</th>
        <th>Company</th>
        <th>Entry</th>
        <th>Visibility</th>
        <th>Manual Contact ID</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($discussions as $row): ?>
        <tr>
          <td><?= htmlspecialchars($row['timestamp']) ?></td>
          <td><?= htmlspecialchars($row['author']) ?></td>
          <td><?= htmlspecialchars($row['company']) ?></td>
          <td><?= nl2br(htmlspecialchars($row['entry_text'])) ?></td>
          <td><?= htmlspecialchars($row['visibility']) ?></td>
          <td>
            <form method="POST" action="update_discussion_contact.php" class="d-flex align-items-center gap-2 mb-0">
              <input type="hidden" name="id" value="<?= (int)$row['id'] ?>">
              <input type="text" name="manual_contact_id" value="<?= htmlspecialchars($row['manual_contact_id'] ?? '') ?>" class="form-control form-control-sm" style="max-width:120px;">
              <button type="submit" class="btn btn-sm btn-primary">Save</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (empty($discussions)): ?>
        <tr><td colspan="6" class="text-center">No discussion log entries found.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>
<?php require_once __DIR__ . '/layout_end.php'; ?>
