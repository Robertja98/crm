<?php
$pageTitle = 'Task Calendar';
header('Content-Type: text/html; charset=UTF-8');

// Load tasks from handler
require_once 'layout_start.php';
require_once 'csv_handler.php'; // or whatever file defines readCSV()

$filename = 'tasks.csv';
$schema = ['title', 'due_date', 'status', 'timestamp']; // adjust based on your actual CSV columns
$tasks = readCSV($filename, $schema);

$tasksByDate = [];
foreach ($tasks as $task) {
    if ($task['status'] !== 'archived' && !empty($task['due_date'])) {
        $tasksByDate[$task['due_date']][] = $task;
    }
}

?>


<div class="calendar-container">
<style>
  .calendar-container {
    max-width: 1000px;
    margin: 0 auto;
    padding: 20px;
  }

  table {
    border-collapse: collapse;
    width: 100%;
    table-layout: fixed;
  }

  th, td {
    border: 1px solid #ccc;
    text-align: center;
    padding: 20px;
    vertical-align: top;
    word-wrap: break-word;
  }

  th {
    background-color: #f4f4f4;
    font-weight: bold;
  }

  .has-task {
    background-color: #ffeeba;
    cursor: pointer;
    position: relative;
  }

  .task-popup {
    display: none;
    position: absolute;
    background: #fff;
    border: 1px solid #ccc;
    padding: 10px;
    z-index: 100;
    top: 100%;
    left: 0;
    width: 250px;
    box-shadow: 0 2px 8px rgba(0,0,0,0.1);
  }
</style>
<h2>Task Calendar</h2>
<table>
  <tr>
    <th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th>
  </tr>
  <?php
  $year = date('Y');
  $month = date('m');
  $daysInMonth = date('t');
  $firstDayOfMonth = date('w', strtotime("$year-$month-01"));

  $day = 1;
  $week = [];

  for ($i = 0; $i < $firstDayOfMonth; $i++) {
      $week[] = "<td></td>";
  }

  while ($day <= $daysInMonth) {
      $dateStr = "$year-$month-" . str_pad($day, 2, '0', STR_PAD_LEFT);
      if (isset($tasksByDate[$dateStr])) {
          $taskTitles = '';
          foreach ($tasksByDate[$dateStr] as $task) {
              $taskTitles .= htmlspecialchars($task['title']) . " (Created: " . htmlspecialchars($task['timestamp']) . ")<br>";
          }
          $week[] = "<td class='has-task' onclick=\"showTasks('$dateStr')\">$day<div id='popup-$dateStr' class='task-popup'>$taskTitles</div></td>";
      } else {
          $week[] = "<td>$day</td>";
      }

      if (count($week) == 7) {
          echo "<tr>" . implode('', $week) . "</tr>";
          $week = [];
      }

      $day++;
  }

  if (!empty($week)) {
      while (count($week) < 7) {
          $week[] = "<td></td>";
      }
      echo "<tr>" . implode('', $week) . "</tr>";
  }
  ?>
</table>
</div>

<script>
function showTasks(date) {
  var popup = document.getElementById('popup-' + date);
  if (popup.style.display === 'block') {
    popup.style.display = 'none';
  } else {
    document.querySelectorAll('.task-popup').forEach(p => p.style.display = 'none');
    popup.style.display = 'block';
  }
}
</script>

<?php include 'layout_end.php'; ?>
