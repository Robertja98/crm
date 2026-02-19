<?php
require_once __DIR__ . '/simple_auth/middleware.php';
$currentPage = basename(__FILE__); // Dynamically sets active tab
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Contact Saved</title>
  <link rel="stylesheet" href="styles.css"> <!-- Optional external CSS -->
  <style>
    body { font-family: Arial, sans-serif; margin: 0; padding: 0; background: #f9f9f9; }
    .navbar { background: #333; padding: 10px 20px; }
    .nav-list { list-style: none; margin: 0; padding: 0; display: flex; }
    .nav-list li { margin-right: 20px; }
    .nav-list a { color: #fff; text-decoration: none; }
    .nav-list .active a { font-weight: bold; text-decoration: underline; }
    .container { max-width: 600px; margin: 60px auto; background: #fff; padding: 30px; border-radius: 8px; box-shadow: 0 0 10px rgba(0,0,0,0.1); text-align: center; }
    .btn { display: inline-block; margin-top: 20px; padding: 10px 20px; background: #0077cc; color: #fff; text-decoration: none; border-radius: 4px; }
    .btn:hover { background: #005fa3; }
  </style>
</head>
<body>

<nav class="navbar">
  <ul class="nav-list">
    <li class="<?= $currentPage === 'dashboard.php' ? 'active' : '' ?>">
      <a href="dashboard.php">Dashboard</a>
    </li>
    <li class="<?= $currentPage === 'contact_form.php' ? 'active' : '' ?>">
      <a href="contact_form.php">Add Contact</a>
    </li>
    <li class="<?= $currentPage === 'contacts_list.php' ? 'active' : '' ?>">
      <a href="contacts_list.php">View Contacts</a>
    </li>
    <li class="<?= $currentPage === 'add_opportunity.php' ? 'active' : '' ?>">
      <a href="add_opportunity.php">Add Opportunity</a>
    </li>
    <li class="<?= $currentPage === 'opportunities_list.php' ? 'active' : '' ?>">
      <a href="opportunities_list.php">View Opportunities</a>
    </li>
    <li class="<?= $currentPage === 'import_contacts.php' ? 'active' : '' ?>">
      <a href="import_contacts.php">Import Contacts</a>
    </li>
    <li class="<?= $currentPage === 'export_contacts.php' ? 'active' : '' ?>">
      <a href="export_contacts.php">Export Contacts</a>
    </li>
  </ul>
</nav>

<div class="container">
  <h2>✅ Contact Saved Successfully</h2>
  <p>Your new contact has been added to the system and logged for audit.</p>
  <a href="dashboard.php" class="btn">Return to Dashboard</a>
</div>

</body>
</html>
