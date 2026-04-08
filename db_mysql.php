<?php
// db_mysql.php - MySQL connection for CRM
require_once __DIR__ . '/env_loader.php';
load_env();

function get_mysql_connection() {
    $serverName = strtolower((string)($_SERVER['SERVER_NAME'] ?? 'localhost'));
    $isLocal = in_array($serverName, ['localhost', '127.0.0.1'], true);

    $hasLocalEnv = getenv('DB_HOST') && getenv('DB_NAME') && getenv('DB_USER') && getenv('DB_PASSWORD');
    $hasProdEnv = getenv('PROD_DB_HOST') && getenv('PROD_DB_NAME') && getenv('PROD_DB_USER') && getenv('PROD_DB_PASSWORD');

    if ($isLocal && $hasLocalEnv) {
        $host = getenv('DB_HOST');
        $dbname = getenv('DB_NAME');
        $user = getenv('DB_USER');
        $password = getenv('DB_PASSWORD');
    } elseif (!$isLocal && $hasProdEnv) {
        $host = getenv('PROD_DB_HOST');
        $dbname = getenv('PROD_DB_NAME');
        $user = getenv('PROD_DB_USER');
        $password = getenv('PROD_DB_PASSWORD');
    } elseif (!$isLocal && $hasLocalEnv) {
        // Production fallback if only DB_* variables are defined.
        $host = getenv('DB_HOST');
        $dbname = getenv('DB_NAME');
        $user = getenv('DB_USER');
        $password = getenv('DB_PASSWORD');
    } else {
        $config = require __DIR__ . '/config.local.php';
        $env = $isLocal ? 'local' : 'production';
        $db = $config[$env];
        $host = $db['host'];
        $dbname = $db['dbname'];
        $user = $db['user'];
        $password = $db['password'];
    }

    mysqli_report(MYSQLI_REPORT_OFF);
    $conn = @new mysqli((string)$host, (string)$user, (string)$password);
    if (!$conn || $conn->connect_error) {
        $err = $conn ? $conn->connect_error : 'Unknown MySQL connection error';
        die('Error: Could not connect to MySQL database. ' . $err);
    }

    if (!$conn->select_db((string)$dbname)) {
        die('Error: Could not select database ' . (string)$dbname . '. ' . $conn->error);
    }

    return $conn;
}
?>
