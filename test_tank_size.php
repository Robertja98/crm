<?php
// test_tank_size.php - Simple test for tank_size column access
$host = 'localhost';
$user = 'admin';
$password = 'M@sonnotte032';
$dbname = 'crmdb';
$conn = new mysqli($host, $user, $password, $dbname);
if ($conn->connect_error) {
    die('Connection failed: ' . $conn->connect_error);
}
$sql = "SELECT tank_size FROM contracts LIMIT 1";
$result = $conn->query($sql);
if ($result) {
    $row = $result->fetch_assoc();
    echo 'tank_size: ' . ($row['tank_size'] ?? 'NULL');
    $result->free();
} else {
    echo 'SQL Error: ' . $conn->error;
}
$conn->close();
?>
