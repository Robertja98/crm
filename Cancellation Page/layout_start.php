<?php
$pageTitle = $pageTitle ?? 'CRM';
$currentPage = basename($_SERVER['SCRIPT_NAME']);
header('Content-Type: text/html; charset=UTF-8');
header('X-Content-Type-Options: nosniff');
?>
<!DOCTYPE html>
<html lang="en-CA">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <meta name="description" content="CRM system for managing contacts and customer interactions.">
  <meta name="author" content="Eclipse Water Technologies">
  <meta name="robots" content="index, follow">
  <meta property="og:title" content="<?= htmlspecialchars($pageTitle) ?>">
  <meta property="og:type" content="website">
  <meta property="og:url" content="https://yourdomain.com/<?= htmlspecialchars($currentPage) ?>">
  <meta property="og:image" content="https://yourdomain.com/images/preview.png">
  <link rel="icon" href="/favicon.ico" type="image/x-icon">
  <link rel="stylesheet" href="styles.css?v=20251002">
  <title><?= htmlspecialchars($pageTitle) ?></title>
</head>
<body>
<?php include_once 'navbar.php'; ?>
<div class="container">
  <div id="task-list-panel">
    <h3>Programming Tasks</h3>
    <ul id="task-list"></ul>
    <input type="text" id="new-task" placeholder="Add new task..." />
    <button onclick="addTask()">Add</button>
  </div>
</div>
<script src="/task-list.js"></script>
</body>
</html>
