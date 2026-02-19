<?php
// Migrate to MySQL: Delete opportunity by ID
require_once 'db_mysql.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: opportunities_list.php?error=' . urlencode('Invalid request method'));
    exit;
}

$idToDelete = $_POST['id'] ?? null;

if (!$idToDelete) {
    header('Location: opportunities_list.php?error=' . urlencode('No opportunity ID provided'));
    exit;
}

// Use the correct MySQL connection function
$conn = get_mysql_connection();
if (!$conn) {
    header('Location: opportunities_list.php?error=' . urlencode('Database connection failed'));
    exit;
}

$stmt = $conn->prepare('DELETE FROM opportunities WHERE id = ?');
if (!$stmt) {
    header('Location: opportunities_list.php?error=' . urlencode('Failed to prepare statement'));
    $conn->close();
    exit;
}
$stmt->bind_param('i', $idToDelete);
if ($stmt->execute()) {
    if ($stmt->affected_rows > 0) {
        $stmt->close();
        $conn->close();
        header('Location: opportunities_list.php?success=3');
        exit;
    } else {
        $stmt->close();
        $conn->close();
        header('Location: opportunities_list.php?error=' . urlencode('Opportunity not found'));
        exit;
    }
} else {
    $stmt->close();
    $conn->close();
    header('Location: opportunities_list.php?error=' . urlencode('Failed to delete opportunity'));
    exit;
}
