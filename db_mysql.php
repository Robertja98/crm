<?php
// db_mysql.php - MySQL connection for CRM
function get_mysql_connection() {
    if (in_array($_SERVER['SERVER_NAME'] ?? 'localhost', ['localhost', '127.0.0.1'])) {
        // Local development settings
        $host = 'localhost';
        $dbname = 'crmdb';
        $user = 'admin';
        $password = 'M@sonnotte032';
    } else {
        // Webserver/production settings
        $host = 'localhost'; // Update if your GoDaddy MySQL host is different
        $dbname = 'crmdb1';
        $user = 'crm_admin';
        $password = 'M@sonnotte032';
    }
    $conn = new mysqli($host, $user, $password, $dbname);
    if ($conn->connect_error) {
        die('Error: Could not connect to MySQL database. ' . $conn->connect_error);
    }
    return $conn;
}
?>
