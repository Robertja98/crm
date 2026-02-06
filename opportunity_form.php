<?php
require_once 'csv_handler.php';
$schema = require __DIR__ . '/opportunity_schema.php';
$contacts = readCSV('contacts.csv', require __DIR__ . '/contact_schema.php');
?>

<?php include_once(__DIR__ . '/layout_start.php'); ?>
<?php $currentPage = basename(__FILE__); include_once(__DIR__ . '/navbar.php'); ?>

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

        <?php if ($field === 'contact_id'): ?>
          <select name="contact_id" id="contact_id" class="form-control" required>
            <option value="">Select Contact</option>
            <?php foreach ($contacts as $contact): ?>
              <option value="<?= $contact['id'] ?>">
                <?= trim($contact['first_name'] . ' ' . $contact['last_name']) ?: 'Unnamed Contact' ?>
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
