<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="Add a new contact to the CRM.">
  <title>Add New Contact</title>
  <link rel="stylesheet" href="styles.css">
  <style>
    .form-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 20px;
      margin-bottom: 24px;
    }
    .form-actions {
      display: flex;
      gap: 12px;
      justify-content: flex-start;
      padding-top: 16px;
      border-top: 1px solid #e5e7eb;
    }
  </style>
</head>
<body>
<header>
  <!-- Navigation can be included here if layout_start.php provides it -->
</header>
<main>
<?php
$schema = require __DIR__ . '/contact_schema.php';
?>
<section class="page-header">
  <h1>Add New Contact</h1>
  <div class="page-actions">
    <a href="contacts_list.php" class="btn btn-outline">Back to Contacts</a>
  </div>
</section>
<?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
  <div class="alert alert-success">&#10003; Contact saved successfully.</div>
<?php endif; ?>
<section class="card">
  <div class="card-header">
    <h3>Contact Information</h3>
  </div>
  <div class="card-body">
    <form id="contact-form" action="add_contact.php" method="POST" class="modern-form">
      <?php renderCSRFInput(); ?>
      <div class="form-grid">
        <?php foreach ($schema as $field): ?>
          <?php if ($field === 'id') continue; ?>
          <div class="form-group">
            <label for="<?= e($field) ?>"><?= e(ucwords(str_replace('_', ' ', $field))) ?>:</label>
            <input type="<?= $field === 'email' ? 'email' : ($field === 'phone' ? 'tel' : 'text') ?>"
                   name="<?= e($field) ?>" id="<?= e($field) ?>" class="form-control"
                   <?= $field === 'company' ? 'required' : '' ?> aria-required="true">
          </div>
        <?php endforeach; ?>
      </div>
      <div class="form-actions">
        <button type="submit" class="btn btn-primary">Save Contact</button>
        <a href="contacts_list.php" class="btn btn-outline">Cancel</a>
      </div>
    </form>
  </div>
</section>
<!-- Footer can be included here if layout_end.php provides it -->
</main>
</body>
</html>
