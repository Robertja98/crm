<?php
// Initialize authentication
require_once __DIR__ . '/simple_auth/middleware.php';

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
<?php // Navbar is included by pages to avoid duplication ?>

<div class="container">
  <!-- Page content begins here -->
