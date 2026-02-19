<?php
require_once 'layout_start.php';
require_once 'db_mysql.php';
$schema = require __DIR__ . '/equipment_schema.php';

// Get equipment ID from query string
$equipment_id = $_GET['id'] ?? '';
$equipment = null;
if ($equipment_id !== '') {
    $conn = get_mysql_connection();
    $fields = implode(',', array_map(function($f) { return '`' . $f . '`'; }, $schema));
    $stmt = $conn->prepare("SELECT $fields FROM equipment WHERE equipment_id = ?");
    $stmt->bind_param('s', $equipment_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $equipment = $result ? $result->fetch_assoc() : null;
    $stmt->close();
    $conn->close();
}
?>
<div class="container">
  <h2>Equipment Details</h2>
  <?php if (!$equipment): ?>
    <div style="color:red;">Equipment not found.</div>
  <?php else: ?>
    <table class="table-grid">
      <tbody>
        <?php foreach ($schema as $field): ?>
          <tr>
            <th><?= htmlspecialchars(ucwords(str_replace('_', ' ', $field))) ?></th>
            <td><?= htmlspecialchars($equipment[$field] ?? '') ?></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <form method="POST" onsubmit="return confirm('Are you sure you want to delete this equipment? This action cannot be undone.');" style="margin-top:20px;">
      <input type="hidden" name="delete_id" value="<?= htmlspecialchars($equipment_id) ?>">
      <button type="submit" style="background:#dc2626;color:#fff;padding:8px 16px;border:none;border-radius:4px;cursor:pointer;">Delete Equipment</button>
    </form>
    <a href="equipment_list.php">&larr; Back to Equipment List</a>
  <?php endif; ?>
</div>

<?php
// Handle delete POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['delete_id'])) {
  $delete_id = $_POST['delete_id'];
  $conn = get_mysql_connection();
  $stmt = $conn->prepare('DELETE FROM equipment WHERE equipment_id = ?');
  $stmt->bind_param('s', $delete_id);
  $stmt->execute();
  $stmt->close();
  $conn->close();
  // Redirect to equipment list after delete
  header('Location: equipment_list.php?deleted=1');
  exit;
}
