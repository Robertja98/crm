
<?php
$pageTitle = 'Task Calendar';
header('Content-Type: text/html; charset=UTF-8');

require_once 'layout_start.php';
require_once 'tasks_mysql.php';

$tasks = fetch_tasks_mysql();
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
  .droptarget.drag-over {
    outline: 2px solid #0099A8;
    background: #e0f7fa !important;
  }
  .dragging-task {
    opacity: 0.6;
    border: 2px dashed #0099A8;
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
<div style="margin-bottom:12px;">
  <button id="undoMoveBtn" onclick="undoMove()" disabled>Undo</button>
  <button id="redoMoveBtn" onclick="redoMove()" disabled>Redo</button>
</div>
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
        $taskDetails = '';
        foreach ($tasksByDate[$dateStr] as $task) {
          $taskId = htmlspecialchars($task['id']);
          $taskDetails .= "<div style='margin-bottom:10px;text-align:left;cursor:pointer;' draggable='true' ondragstart=\"onTaskDragStart(event, '$taskId')\" ondragend=\"onTaskDragEnd(event)\" onclick=\"event.stopPropagation();openEditTaskModal('$taskId')\">";
          $taskDetails .= "<strong>" . htmlspecialchars($task['title']) . "</strong><br>";
          $taskDetails .= "<span style='font-size:0.95em;color:#666;'>Created: " . htmlspecialchars($task['timestamp']) . "</span><br>";
          if (!empty($task['description'])) $taskDetails .= "<em>Description:</em> " . nl2br(htmlspecialchars($task['description'])) . "<br>";
          if (!empty($task['comments'])) $taskDetails .= "<em>Comments:</em> " . nl2br(htmlspecialchars($task['comments'])) . "<br>";
          if (!empty($task['recurrence'])) $taskDetails .= "<em>Recurrence:</em> " . htmlspecialchars($task['recurrence']) . "<br>";
          if (!empty($task['attachment'])) $taskDetails .= "<em>Attachment:</em> <a href='" . htmlspecialchars($task['attachment']) . "' target='_blank'>View</a><br>";
          $taskDetails .= "<em>Status:</em> " . htmlspecialchars($task['status']) . "<br>";
          $taskDetails .= "<em>Assignee:</em> " . htmlspecialchars($task['assigned_to']) . "<br>";
          $taskDetails .= "<em>Priority:</em> " . htmlspecialchars($task['priority']) . "<br>";
          $taskDetails .= "<span style='color:#007489;text-decoration:underline;cursor:pointer;'>Edit Task</span>";
          $taskDetails .= "</div>";
        }
        $week[] = "<td class='has-task droptarget' onclick=\"showTasks('$dateStr')\" ondragover=\"onDayDragOver(event)\" ondragenter=\"onDayDragEnter(event)\" ondragleave=\"onDayDragLeave(event)\" ondrop=\"onDayDrop(event, '$dateStr')\">$day<div id='popup-$dateStr' class='task-popup'>$taskDetails</div></td>";
      } else {
        $week[] = "<td class='empty-date droptarget' onclick=\"openAddTaskModal('$dateStr')\" ondragover=\"onDayDragOver(event)\" ondragenter=\"onDayDragEnter(event)\" ondragleave=\"onDayDragLeave(event)\" ondrop=\"onDayDrop(event, '$dateStr')\">$day</td>";
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

<div id="modal-bg" style="display:none;position:fixed;top:0;left:0;width:100vw;height:100vh;background:rgba(0,0,0,0.3);z-index:200;"></div>
<div id="add-task-modal" style="display:none;position:fixed;top:50px;left:50%;transform:translateX(-50%);background:#fff;padding:24px 32px;border-radius:10px;box-shadow:0 4px 24px rgba(0,0,0,0.18);z-index:201;min-width:320px;max-width:90vw;">
  <h3>Add Task</h3>
  <form id="addTaskForm">
    <input type="hidden" name="due_date" id="add_due_date">
    <label>Title: <input type="text" name="title" required></label><br>
    <label>Description:<br><textarea name="description" rows="2" cols="32"></textarea></label><br>
    <label>Comments:<br><textarea name="comments" rows="2" cols="32"></textarea></label><br>
    <label>Recurrence:
      <select name="recurrence">
        <option value="">None</option>
        <option value="daily">Daily</option>
        <option value="weekly">Weekly</option>
        <option value="monthly">Monthly</option>
      </select>
    </label><br>
    <label>Attachment: <input type="text" name="attachment" placeholder="URL or filename"></label><br>
    <label>Status:
      <select name="status" required>
        <option value="not_started">Not Started</option>
        <option value="in_progress">In Progress</option>
        <option value="waiting">Waiting/Blocked</option>
        <option value="review">Review</option>
        <option value="completed">Completed</option>
        <option value="archived">Archived</option>
      </select>
    </label><br>
    <button type="submit">Add Task</button>
    <button type="button" onclick="closeModal()">Cancel</button>
  </form>
</div>
<div id="edit-task-modal" style="display:none;position:fixed;top:50px;left:50%;transform:translateX(-50%);background:#fff;padding:24px 32px;border-radius:10px;box-shadow:0 4px 24px rgba(0,0,0,0.18);z-index:201;min-width:320px;max-width:90vw;">
  <h3>Edit Task</h3>
  <form id="editTaskForm">
    <input type="hidden" name="id" id="edit_id">
    <label>Title: <input type="text" name="title" id="edit_title" required></label><br>
    <label>Description:<br><textarea name="description" id="edit_description" rows="2" cols="32"></textarea></label><br>
    <label>Comments:<br><textarea name="comments" id="edit_comments" rows="2" cols="32"></textarea></label><br>
    <label>Recurrence:
      <select name="recurrence" id="edit_recurrence">
        <option value="">None</option>
        <option value="daily">Daily</option>
        <option value="weekly">Weekly</option>
        <option value="monthly">Monthly</option>
      </select>
    </label><br>
    <label>Attachment: <input type="text" name="attachment" id="edit_attachment" placeholder="URL or filename"></label><br>
    <label>Status:
      <select name="status" id="edit_status" required>
        <option value="not_started">Not Started</option>
        <option value="in_progress">In Progress</option>
        <option value="waiting">Waiting/Blocked</option>
        <option value="review">Review</option>
        <option value="completed">Completed</option>
        <option value="archived">Archived</option>
      </select>
    </label><br>
    <button type="submit">Update Task</button>
    <button type="button" onclick="closeModal()">Cancel</button>
  </form>
</div>
<script>
// Undo/redo stacks for drag-and-drop moves
let moveHistory = [];
let redoStack = [];

function setUndoRedoState() {
  document.getElementById('undoMoveBtn').disabled = moveHistory.length === 0;
  document.getElementById('redoMoveBtn').disabled = redoStack.length === 0;
}

function undoMove() {
  if (moveHistory.length === 0) return;
  const last = moveHistory.pop();
  redoStack.push(last);
  // Move task back to old date
  const fd = new FormData();
  fd.append('action', 'edit');
  fd.append('id', last.taskId);
  fd.append('due_date', last.from);
  fetch('calendar_task_ajax.php', {method:'POST', body:fd})
    .then(r => r.json())
    .then(data => {
      if (data.success) location.reload();
      else alert('Undo failed.');
    });
  setUndoRedoState();
}
function redoMove() {
  if (redoStack.length === 0) return;
  const last = redoStack.pop();
  moveHistory.push(last);
  // Move task to new date
  const fd = new FormData();
  fd.append('action', 'edit');
  fd.append('id', last.taskId);
  fd.append('due_date', last.to);
  fetch('calendar_task_ajax.php', {method:'POST', body:fd})
    .then(r => r.json())
    .then(data => {
      if (data.success) location.reload();
      else alert('Redo failed.');
    });
  setUndoRedoState();
}
let draggedTaskId = null;
let lastDraggedElem = null;
let draggedTaskOrigDate = null;
function onTaskDragStart(e, taskId) {
  draggedTaskId = taskId;
  e.dataTransfer.effectAllowed = 'move';
  lastDraggedElem = e.target;
  e.target.classList.add('dragging-task');
  // Find original date from DOM
  const parentTd = e.target.closest('td');
  if (parentTd) {
    draggedTaskOrigDate = parentTd.querySelector('.task-popup')?.id?.replace('popup-', '') || null;
  } else {
    draggedTaskOrigDate = null;
  }
}
function onTaskDragEnd(e) {
  if (lastDraggedElem) lastDraggedElem.classList.remove('dragging-task');
  lastDraggedElem = null;
  draggedTaskOrigDate = null;
}
function onDayDragOver(e) {
  e.preventDefault();
  e.dataTransfer.dropEffect = 'move';
}
function onDayDragEnter(e) {
  e.currentTarget.classList.add('drag-over');
}
function onDayDragLeave(e) {
  e.currentTarget.classList.remove('drag-over');
}
function onDayDrop(e, dateStr) {
  e.preventDefault();
  e.currentTarget.classList.remove('drag-over');
  if (!draggedTaskId || !draggedTaskOrigDate || draggedTaskOrigDate === dateStr) return;
  // AJAX update due_date
  const fd = new FormData();
  fd.append('action', 'edit');
  fd.append('id', draggedTaskId);
  fd.append('due_date', dateStr);
  fetch('calendar_task_ajax.php', {method:'POST', body:fd})
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        moveHistory.push({taskId: draggedTaskId, from: draggedTaskOrigDate, to: dateStr});
        redoStack = [];
        setUndoRedoState();
        location.reload();
      } else alert('Failed to move task.');
    });
  if (lastDraggedElem) lastDraggedElem.classList.remove('dragging-task');
  draggedTaskId = null;
  lastDraggedElem = null;
  draggedTaskOrigDate = null;
}
function showTasks(date) {
  var popup = document.getElementById('popup-' + date);
  if (popup.style.display === 'block') {
    popup.style.display = 'none';
  } else {
    document.querySelectorAll('.task-popup').forEach(p => p.style.display = 'none');
    popup.style.display = 'block';
  }
}
function openAddTaskModal(date) {
  document.getElementById('add_due_date').value = date;
  document.getElementById('addTaskForm').reset();
  document.getElementById('modal-bg').style.display = 'block';
  document.getElementById('add-task-modal').style.display = 'block';
}
function openEditTaskModal(taskId) {
  fetch('calendar_task_ajax.php?id=' + encodeURIComponent(taskId))
    .then(r => r.json())
    .then(data => {
      if (data.success) {
        const t = data.task;
        document.getElementById('edit_id').value = t.id;
        document.getElementById('edit_title').value = t.title;
        document.getElementById('edit_description').value = t.description || '';
        document.getElementById('edit_comments').value = t.comments || '';
        document.getElementById('edit_recurrence').value = t.recurrence || '';
        document.getElementById('edit_attachment').value = t.attachment || '';
        document.getElementById('edit_status').value = t.status;
        document.getElementById('modal-bg').style.display = 'block';
        document.getElementById('edit-task-modal').style.display = 'block';
      }
    });
}
function closeModal() {
  document.getElementById('modal-bg').style.display = 'none';
  document.getElementById('add-task-modal').style.display = 'none';
  document.getElementById('edit-task-modal').style.display = 'none';
}
document.getElementById('modal-bg').onclick = closeModal;
setUndoRedoState();

document.getElementById('addTaskForm').onsubmit = function(e) {
  e.preventDefault();
  const form = e.target;
  const fd = new FormData(form);
  fd.append('action', 'add');
  fetch('calendar_task_ajax.php', {method:'POST', body:fd})
    .then(r => r.json())
    .then(data => {
      if (data.success) location.reload();
      else alert('Failed to add task.');
    });
};
document.getElementById('editTaskForm').onsubmit = function(e) {
  e.preventDefault();
  const form = e.target;
  const fd = new FormData(form);
  fd.append('action', 'edit');
  fetch('calendar_task_ajax.php', {method:'POST', body:fd})
    .then(r => r.json())
    .then(data => {
      if (data.success) location.reload();
      else alert('Failed to update task.');
    });
};
</script>

<?php include 'layout_end.php'; ?>
