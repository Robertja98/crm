<?php
$pageTitle = 'Task Calendar';
header('Content-Type: text/html; charset=UTF-8');

require_once 'layout_start.php';
require_once 'csv_handler.php';

$filename = 'tasks.csv';
$schema = ['title', 'due_date', 'status', 'timestamp'];
$tasks = readCSV($filename, $schema);

$statusFilter = isset($_GET['status']) ? $_GET['status'] : '';
$year = isset($_GET['year']) ? $_GET['year'] : date('Y');
$month = isset($_GET['month']) ? $_GET['month'] : date('m');

$tasksByDate = [];
foreach ($tasks as $task) {
    if ($task['status'] !== 'archived' && !empty($task['due_date'])) {
        if ($statusFilter === '' || $task['status'] === $statusFilter) {
            $tasksByDate[$task['due_date']][] = $task;
        }
    }
}

$prevMonth = $month - 1;
$prevYear = $year;
if ($prevMonth < 1) {
    $prevMonth = 12;
    $prevYear--;
}

$nextMonth = $month + 1;
$nextYear = $year;
if ($nextMonth > 12) {
    $nextMonth = 1;
    $nextYear++;
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

<div style="text-align:center; margin-bottom:20px;">
  <a href="?year=<?= $prevYear ?>&month=<?= str_pad($prevMonth, 2, '0', STR_PAD_LEFT) ?>">← Previous</a>
  <strong><?= date('F Y', strtotime("$year-$month-01")) ?></strong>
  <a href="?year=<?= $nextYear ?>&month=<?= str_pad($nextMonth, 2, '0', STR_PAD_LEFT) ?>">Next →</a>
</div>

<form method="GET" style="margin-bottom: 20px;">
  <label for="status">Filter by status:</label>
  <select name="status" id="status" onchange="this.form.submit()">
    <option value="">All</option>
    <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>Pending</option>
    <option value="completed" <?= $statusFilter === 'completed' ? 'selected' : '' ?>>Completed</option>
  </select>
  <input type="hidden" name="year" value="<?= $year ?>">
  <input type="hidden" name="month" value="<?= $month ?>">
</form>

<h3>Add New Task</h3>
<form method="POST" action="add_task.php" onsubmit="return validateForm()">
  <input type="text" name="title" id="title" placeholder="Task Title" required>
  <input type="date" name="due_date" id="due_date" required>
  <select name="status" id="status" required>
    <option value="">Select Status</option>
    <option value="pending">Pending</option>
    <option value="completed">Completed</option>
  </select>
  <button type="submit">Add Task</button>
</form>

<table>
  <tr>
    <th>Sun</th><th>Mon</th><th>Tue</th><th>Wed</th><th>Thu</th><th>Fri</th><th>Sat</th>
  </tr>
<?php
$daysInMonth = date('t', strtotime("$year-$month-01"));
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
            $taskTitles .= "edit_task.php?timestamp=Edit</a> | ";
            $taskTitles .= "delete_task.php?timestamp=Delete</a><br><br>";
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


    if (count($week) == 7) {
        echo "<tr>" . implode('', $week) . "</tr>";
        $week = [];
    }

    $day++;

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

function validateForm() {
  const title = document.getElementById('title').value.trim();
  const dueDate = document.getElementById('due_date').value;
  const status = document.getElementById('status').value;

  if (title === '') {
    alert('Please enter a task title.');
    return false;
  }

  if (dueDate === '') {
    alert('Please select a due date.');
    return false;
  }

  if (status === '') {
    alert('Please select a status.');
    return false;
  }

  return true;
}
</script>

<?php include 'layout_end.php'; ?>
