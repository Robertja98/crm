<?php
// db_mysql.php - MySQL connection for CRM
function get_mysql_connection() {
    $host = 'localhost'; // Update with your GoDaddy MySQL host if needed
    $dbname = 'crmdb'; // Set to your database name
    $user = 'admin'; // Update with your database username
    $password = 'M@sonnotte032'; // Update with your database password
    $conn = new mysqli($host, $user, $password, $dbname);
    if ($conn->connect_error) {
        die('Error: Could not connect to MySQL database. ' . $conn->connect_error);
    }
    return $conn;
}
?>
