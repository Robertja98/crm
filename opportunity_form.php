<?php
require_once 'db_mysql.php';
require_once 'sanitize_helper.php';
$schema = require __DIR__ . '/opportunity_schema.php';

// Load unique company names from contacts for dropdown
$conn = get_mysql_connection();
$companies = [];
$result = $conn->query("SELECT DISTINCT company FROM contacts WHERE company IS NOT NULL AND company != '' ORDER BY company");
if ($result) {
  while ($row = $result->fetch_assoc()) {
    $companies[] = $row['company'];
  }
  $result->free();
}
$conn->close();

// Calculate statistics
$totalValue = 0;
?>

<?php include_once(__DIR__ . '/layout_start.php'); ?>
<?php $currentPage = basename(__FILE__); ?>

<div class="container">
  <h2>Add New Opportunity</h2>

  <?php if (!empty($_GET['status']) && $_GET['status'] === 'success'): ?>
    <p class="success-msg">Opportunity saved successfully.</p>
  <?php elseif (!empty($_GET['status']) && $_GET['status'] === 'error'): ?>
    <p class="error-msg">Invalid opportunity data or contact ID.</p>
  <?php endif; ?>

  <form id="opportunity-form" action="add_opportunity.php" method="POST" class="form-block">
    <?php foreach ($schema as $field): ?>
      <?php if ($field === 'id') continue; ?>

      <div class="form-group">
        <label for="<?= $field ?>"><?= ucwords(str_replace('_', ' ', $field)) ?>:</label>

        <?php if ($field === 'company_id'): ?>
          <select name="company_id" id="company_id" class="form-control" required>
            <option value="">Select Company</option>
            <?php foreach ($companies as $company): ?>
              <option value="<?= htmlspecialchars($company) ?>">
                <?= htmlspecialchars($company) ?>
              </option>
            <?php endforeach; ?>
          </select>

        <?php elseif ($field === 'stage'): ?>
          <select name="stage" id="stage" class="form-control" required>
            <option value="">Select Stage</option>
            <option value="Prospecting">Prospecting</option>
            <option value="Proposal">Proposal</option>
            <option value="Negotiation">Negotiation</option>
            <option value="Closed Won">Closed Won</option>
            <option value="Closed Lost">Closed Lost</option>
          </select>

        <?php elseif ($field === 'expected_close'): ?>
          <input type="date" name="expected_close" id="expected_close" class="form-control" required>

        <?php elseif ($field === 'value' || $field === 'probability'): ?>
          <input type="number" name="<?= $field ?>" id="<?= $field ?>" class="form-control" required>

        <?php else: ?>
          <input type="text" name="<?= $field ?>" id="<?= $field ?>" class="form-control" required>
        <?php endif; ?>
      </div>
    <?php endforeach; ?>

    <button type="submit" class="btn-primary">Save Opportunity</button>
  </form>
</div>

<?php include_once(__DIR__ . '/layout_end.php'); ?>
