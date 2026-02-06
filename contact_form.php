<?php
$schema = require __DIR__ . '/contact_schema.php';
?>

<?php include_once(__DIR__ . '/layout_start.php'); ?>
<?php $currentPage = basename(__FILE__); include_once(__DIR__ . '/navbar.php'); ?>

<div class="container">
  <h2>Add New Contact</h2>

  <?php if (isset($_GET['status']) && $_GET['status'] === 'success'): ?>
    <p class="success-msg">Contact saved successfully.</p>
  <?php endif; ?>

  <form id="contact-form" action="add_contact.php" method="POST" class="form-block">
    
<?php foreach ($schema as $field): ?>
  <?php if ($field === 'id') continue; ?>
  <div class="form-group">
    <label for="<?= $field ?>"><?= ucwords(str_replace('_', ' ', $field)) ?>:</label>
    <input type="<?= $field === 'email' ? 'email' : ($field === 'phone' ? 'tel' : 'text') ?>"
           name="<?= $field ?>" id="<?= $field ?>" class="form-control"
           <?= $field === 'company' ? 'required' : '' ?>>
  </div>
<?php endforeach; ?>


    <button type="submit" class="btn-primary">Save Contact</button>
  </form>
</div>

<?php include_once(__DIR__ . '/layout_end.php'); ?>
