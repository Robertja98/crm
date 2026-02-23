<?php
// db_mysql.php - MySQL connection for CRM
require_once __DIR__ . '/env_loader.php';
load_env();
function get_mysql_connection() {
    $isLocal = in_array($_SERVER['SERVER_NAME'] ?? 'localhost', ['localhost', '127.0.0.1']);
    if (getenv('DB_HOST') && getenv('DB_NAME') && getenv('DB_USER') && getenv('DB_PASSWORD')) {
        $host = $isLocal ? getenv('DB_HOST') : getenv('PROD_DB_HOST');
        $dbname = $isLocal ? getenv('DB_NAME') : getenv('PROD_DB_NAME');
        $user = $isLocal ? getenv('DB_USER') : getenv('PROD_DB_USER');
        $password = $isLocal ? getenv('DB_PASSWORD') : getenv('PROD_DB_PASSWORD');
    } else {
        $config = require __DIR__ . '/config.local.php';
        $env = $isLocal ? 'local' : 'production';
        $db = $config[$env];
        $host = $db['host'];
        $dbname = $db['dbname'];
        $user = $db['user'];
        $password = $db['password'];
    }
    $conn = new mysqli($host, $user, $password, $dbname);
    if ($conn->connect_error) {
        die('Error: Could not connect to MySQL database. ' . $conn->connect_error);
    }
    return $conn;
}
?>
