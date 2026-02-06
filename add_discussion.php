<?php
require_once 'contact_validator.php';
require_once 'discussion_logger.php'; // contains logDiscussionEntry()

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate input
    $errors = validateContact($_POST);
    if (!empty($errors)) {
        foreach ($errors as $field => $msg) {
            echo "<p>Error in $field: $msg</p>";
        }
        echo "<p><a href='contact_view.php'>Go back</a></p>";
        exit;
    }

    // Log the discussion entry
    if (logDiscussionEntry($_POST)) {
        header('Location: contact_view.php');
        exit;
    } else {
        echo "<p style='color:red;'>Failed to log discussion entry. Please check the error log.</p>";
        echo "<p><a href='contact_view.php'>Go back</a></p>";
    }
}
?>

