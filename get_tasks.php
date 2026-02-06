<
<?php
$filename = 'tasks.csv';

if (!file_exists($filename)) {
    echo '<li>No tasks found.</li>';
    exit;
}

$tasks = [];

if (($fp = fopen($filename, 'r')) !== false) {
    $headers = fgetcsv($fp); // Read header row
    while (($row = fgetcsv($fp)) !== false) {
        if (empty($row) || count($row) < count($headers)) continue;
        $task = array_combine($headers, $row);
        if ($task['status'] !== 'archived') {
            $id = htmlspecialchars($task['id']);
            $title = htmlspecialchars($task['title']);
            $timestamp = htmlspecialchars($task['timestamp']);
            $tasks[] = "<li>$title (Created: $timestamp) <button onclick=\"archiveTask('$id')\">Archive</button></li>";
        }
    }
    fclose($fp);
}

echo '<ul>' . implode('', $tasks) . '</ul>';
?>
!R0b9052597723