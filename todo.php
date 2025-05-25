<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "gymbridges");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

// Create task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task'])) {
    $task = trim($_POST['task']);
    $due = !empty($_POST['due_date']) ? $_POST['due_date'] : NULL;
    $stmt = $conn->prepare("INSERT INTO tasks (user_id, task, due_date) VALUES (?, ?, ?)");
    $stmt->bind_param("iss", $user_id, $task, $due);
    $stmt->execute();
    $stmt->close();
    header("Location: todo.php");
    exit();
}

// Edit task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['edit_id'], $_POST['edit_task'])) {
  $edit_id = (int)$_POST['edit_id'];
  $edit_task = trim($_POST['edit_task']);

  if ($edit_task !== '') {
      $stmt = $conn->prepare("UPDATE tasks SET task = ? WHERE id = ? AND user_id = ?");
      $stmt->bind_param("sii", $edit_task, $edit_id, $_SESSION['user_id']);
      $stmt->execute();
      $stmt->close();
  }

  header("Location: todo.php");
  exit();
}


// Toggle complete
if (isset($_GET['toggle'])) {
  $id = (int)$_GET['toggle'];

  // Узнаём текущее состояние
  $result = $conn->query("SELECT is_completed FROM tasks WHERE id = $id AND user_id = $user_id");
  $row = $result->fetch_assoc();

  if ($row) {
      $new_state = $row['is_completed'] ? 0 : 1;
      $completed_at = $new_state ? "'" . date('Y-m-d H:i:s') . "'" : "NULL";
      $conn->query("UPDATE tasks SET is_completed = $new_state, completed_at = $completed_at WHERE id = $id AND user_id = $user_id");
  }

  header("Location: todo.php");
  exit();
}


// Delete task
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM tasks WHERE id = $id AND user_id = $user_id");
    header("Location: todo.php");
    exit();
}

// Filters and Sort
$filter = $_GET['filter'] ?? 'all';
$sort = $_GET['sort'] ?? 'added';
$direction = $_GET['dir'] ?? 'asc';

$filter_sql = "1";
if ($filter === 'completed') $filter_sql = "is_completed = 1";
elseif ($filter === 'active') $filter_sql = "is_completed = 0";
elseif ($filter === 'has-due-date') $filter_sql = "due_date IS NOT NULL";

// Корректная сортировка (с учётом регистра и длины)
switch ($sort) {
  case 'alpha':
    $order_by = "LOWER(task) " . ($direction === 'desc' ? "DESC" : "ASC");
    break;
  case 'added':
    $order_by = "created_at " . ($direction === 'desc' ? "DESC" : "ASC");
    break;
  case 'due':
    $order_by = "due_date " . ($direction === 'desc' ? "DESC" : "ASC");
    break;
  case 'length':
    $order_by = "CHAR_LENGTH(task) " . ($direction === 'desc' ? "DESC" : "ASC");
    break;
  default:
    $order_by = "created_at DESC";
}

$tasks = $conn->query("SELECT * FROM tasks WHERE user_id = $user_id AND $filter_sql ORDER BY $order_by");

// Для отображения названия фильтра в кнопке
$filterLabels = [
  'all' => 'All',
  'completed' => 'Completed',
  'active' => 'Active',
  'has-due-date' => 'Has Due Date'
];
$currentFilterLabel = $filterLabels[$filter] ?? 'All';







?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>My Todo-s</title>
  <!-- Bootstrap 5 -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css" />
  <link href='https://fonts.googleapis.com/css?family=Anton' rel='stylesheet'>
  <link rel="icon" href="Logo1.svg" type="image/x-icon">
  <link rel="stylesheet" type="text/css" href="style.css">
  <style>
    body { font-family: "Open Sans", sans-serif;
        line-height: 1.6;
        min-height: 100vh;
        display: flex;
        flex-direction: column; }
    .edit-todo-input, .add-todo-input { outline: none; }
    .add-todo-input:focus, .edit-todo-input:focus { border: none !important; box-shadow: none !important; }
    .todo-actions { visibility: hidden; }
    .todo-item:hover .todo-actions { visibility: visible; }
    .view-opt-label, .date-label { font-size: 0.8rem; }
    .edit-todo-input { font-size: 1.7rem !important; }

    .todo-item.completed .edit-todo-input {
      text-decoration: line-through;
      color: #999;
    }
    .container {
    flex-grow: 1;
    }
    .sort-btn {
    min-width: 160px;
    text-align: center;
  }
  .todo-actions {
    display: flex;
    align-items: center;
    gap: 0.5rem;
  }


  </style>
</head>
<body>
<?php include 'includes/avatar_loader.php'; ?> 
<?php include 'includes/header.php'; ?>


<div class="container my-4 px-2">



  <!-- Header -->
  <div class="row justify-content-center mb-4">
    <div class="col-12 text-center">
      <h1 class="text-dark">
        <i class="bi bi-list-task bg-primary text-white rounded p-2"></i>
        <strong>My Tasks</strong>
      </h1>

    </div>
  </div>


  <!-- Add Task Form in Card -->
<div class="card shadow-sm mb-4">
  <div class="card-body">
    <form method="POST" class="row g-2 align-items-center">
      <div class="col-12 col-md-6">
        <input name="task" class="form-control form-control-lg border-0 bg-light rounded" placeholder="Add new .." required>
      </div>

      <div class="col-6 col-md-3 text-start text-md-end">
        <div class="d-flex align-items-center justify-content-md-end">
          <span class="me-2">Set a Due date</span>
          <i class="fa fa-calendar fa-2x text-primary due-date-button btn" title="Set a Due date"></i>
        </div>
        <input type="hidden" name="due_date" id="hidden-due-date">
      </div>

      <div class="col-6 col-md-3 text-end">
        <button class="btn btn-primary w-100" type="submit">Add</button>
      </div>
    </form>
  </div>
</div>


<!-- Filter & Sort -->
<form class="mb-4" method="GET">

  <!-- Строка поиска + фильтр -->
  <div class="row g-2 justify-content-between align-items-center mb-2 flex-wrap">

    <!-- Поиск -->
    <div class="col-md-9 col-12">
      <div class="input-group">
        <span class="input-group-text bg-white border-end-0">
          <i class="bi bi-search"></i>
        </span>
        <input
          type="text"
          id="searchInput"
          class="form-control rounded-end-pill border-start-0"
          placeholder="Search tasks by name..."
        >
      </div>
    </div>

    <!-- Кнопка фильтра -->
    <div class="col-md-3 col-12 text-md-end">
      <div class="dropdown">
        <button
          class="btn btn-outline-primary rounded-pill px-4 w-100"
          type="button"
          id="filterDropdown"
          data-bs-toggle="dropdown"
          aria-expanded="false"
        >
          <i class="bi bi-funnel me-2"></i> Filter: <?= $currentFilterLabel ?>
        </button>
        <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="filterDropdown">
          <?php foreach ($filterLabels as $key => $label): ?>
            <li>
              <a class="dropdown-item" href="?filter=<?= $key ?>&sort=<?= $sort ?>&dir=<?= $direction ?>"><?= $label ?></a>
            </li>
          <?php endforeach; ?>
        </ul>
      </div>
    </div>

  </div>

  <!-- Сортировки -->
  <div class="d-flex flex-wrap gap-2 justify-content-start">

    <!-- A-Z / Z-A -->
    <a href="?filter=<?= $filter ?>&sort=alpha&dir=<?= ($sort === 'alpha' && $direction === 'asc') ? 'desc' : 'asc' ?>"
       class="btn <?= $sort === 'alpha' ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill sort-btn text-nowrap">
      <i class="bi bi-sort-alpha-down me-2"></i>
      <?= ($sort === 'alpha' && $direction === 'desc') ? 'Z-A' : 'A-Z' ?>
    </a>

    <!-- Created date: Oldest/Newest -->
    <a href="?filter=<?= $filter ?>&sort=added&dir=<?= ($sort === 'added' && $direction === 'asc') ? 'desc' : 'asc' ?>"
       class="btn <?= $sort === 'added' ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill sort-btn text-nowrap">
      <i class="bi bi-clock me-2"></i>
      <?= ($sort === 'added' && $direction === 'desc') ? 'Newest' : 'Oldest' ?>
    </a>

    <!-- Due date: Nearest / Farthest -->
    <a href="?filter=<?= $filter ?>&sort=due&dir=<?= ($sort === 'due' && $direction === 'asc') ? 'desc' : 'asc' ?>"
       class="btn <?= $sort === 'due' ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill sort-btn text-nowrap">
      <i class="bi bi-calendar-range me-2"></i>
      <?= ($sort === 'due' && $direction === 'desc') ? 'Farthest Due' : 'Nearest Due' ?>
    </a>

    <!-- Length: Shorter / Longer -->
    <a href="?filter=<?= $filter ?>&sort=length&dir=<?= ($sort === 'length' && $direction === 'asc') ? 'desc' : 'asc' ?>"
       class="btn <?= $sort === 'length' ? 'btn-primary' : 'btn-outline-secondary' ?> rounded-pill sort-btn text-nowrap">
      <i class="bi bi-text-paragraph me-2"></i>
      <?= ($sort === 'length' && $direction === 'desc') ? 'Longer' : 'Shorter' ?>
    </a>

  </div>

</form>



  <!-- Task List -->
  <div class="card shadow-sm">
  <div class="card-body" id="taskList">
  <?php while ($task = $tasks->fetch_assoc()): ?>
    <div class="row todo-item p-3 my-2 rounded bg-white align-items-center <?= $task['is_completed'] ? 'completed' : '' ?>" data-id="<?= $task['id'] ?>">
      <div class="col-12 col-sm-1 text-center">
        <a href="?toggle=<?= $task['id'] ?>">
          <i class="fa <?= $task['is_completed'] ? 'fa-check-square-o fa-2x' : 'fa-square-o fa-2x' ?> text-primary btn"></i>
        </a>
      </div>
  
      <div class="col-12 col-sm-6 mt-2 mt-sm-0">
        <input type="text" class="form-control form-control-lg border-0 edit-todo-input bg-transparent rounded" readonly value="<?= htmlspecialchars($task['task']) ?>" />
      </div>

      <!-- Дедлайн -->
      <div class="col-12 col-sm-2 mt-2 mt-sm-0 text-sm-end text-start">
        <?php if (!empty($task['due_date'])): ?>
          <?php
            $dueDate = strtotime($task['due_date']);
            $today = strtotime(date('Y-m-d'));
            $isOverdue = $dueDate < $today && !$task['is_completed'];
            $color = $isOverdue ? 'danger' : 'warning';
          ?>
          <div class="d-inline-flex align-items-center bg-white border border-<?= $color ?> rounded px-2 py-1">
            <i class="fa fa-hourglass-2 text-<?= $color ?> me-2"></i>
            <span><?= date('j M Y', $dueDate) ?></span>
          </div>
        <?php else: ?>
          <div class="d-inline-block px-2 py-1">&nbsp;</div>
        <?php endif; ?>

      </div>

      <!-- Действия и даты -->
      <div class="col-12 col-sm-3 mt-2 mt-sm-0 text-sm-end text-start">
        <div class="todo-actions d-flex justify-content-between justify-content-sm-end align-items-center mb-1">
          <i class="fa fa-pencil text-info btn edit-icon" title="Edit"></i>
          <a href="?delete=<?= $task['id'] ?>" onclick="return confirm('Delete this task?')">
            <i class="fa fa-trash-o text-danger btn" title="Delete"></i>
          </a>
        </div>

        <div class="text-muted small">
          <div><i class="fa fa-info-circle"
                  data-bs-toggle="tooltip"
                  data-bs-placement="top"
                  title="Created at <?= date('j M Y, H:i:s', strtotime($task['created_at'])) ?>">
                </i>
                <?= date('j M Y', strtotime($task['created_at'])) ?></div>
          <?php if ($task['is_completed'] && !empty($task['completed_at'])): ?>
            <div class="text-success">Completed: <?= date('j M Y', strtotime($task['completed_at'])) ?></div>
          <?php endif; ?>
        </div>

      </div>
    </div>
  <?php endwhile; ?>
 
  </div>
</div>





<script src="https://code.jquery.com/jquery-3.5.1.min.js"></script>
<!-- ПРАВИЛЬНО: Bootstrap 5 -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/js/bootstrap-datepicker.min.js"></script>
<script>
$(function () {
  $('.due-date-button').datepicker({
    format: 'yyyy-mm-dd',
    autoclose: true,
    todayHighlight: true,
    orientation: "bottom left",
    startDate: new Date()
  }).on('changeDate', function (e) {
    const date = e.format('yyyy-mm-dd');
    $('#hidden-due-date').val(date);
    $('.due-date-label').removeClass('d-none').text('Due: ' + date);
    $('.clear-due-date-button').removeClass('d-none');
  });

  $('.due-date-button').on('click', function () {
    $(this).datepicker('show');
  });

  $('.clear-due-date-button').on('click', function () {
    $('#hidden-due-date').val('');
    $('.due-date-label').text('Due date not set');
    $(this).addClass('d-none');
  });

  $('.edit-icon').on('click', function () {
    const item = $(this).closest('.todo-item');
    const input = item.find('.edit-todo-input');
    input.prop('readonly', false).focus();

    input.on('keydown', function (e) {
      if (e.key === 'Enter') {
        e.preventDefault();
        input.prop('readonly', true);
        const form = $('<form>', { method: 'POST', action: 'todo.php' });
        form.append($('<input>', { type: 'hidden', name: 'edit_id', value: item.data('id') }));
        form.append($('<input>', { type: 'hidden', name: 'edit_task', value: input.val() }));
        $('body').append(form);
        form.submit();
      }
    });
  });
});
</script>
<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.edit-icon').forEach(function (icon) {
    icon.addEventListener('click', function () {
      const todoItem = this.closest('.todo-item');
      const input = todoItem.querySelector('.edit-todo-input');

      input.removeAttribute('readonly');
      input.focus();

      // Показываем кнопку "Save"
      let saveBtn = todoItem.querySelector('.save-btn');
      if (!saveBtn) {
        saveBtn = document.createElement('button');
        saveBtn.className = 'btn btn-success btn-sm ms-2 save-btn';
        saveBtn.innerHTML = '<i class="fa fa-save"></i> Save';

        this.parentElement.appendChild(saveBtn); // Добавляем кнопку рядом с иконками

        saveBtn.addEventListener('click', function () {
          const taskId = todoItem.dataset.id;
          const newValue = input.value.trim();

          if (newValue !== '') {
            const form = document.createElement('form');
            form.method = 'POST';
            form.action = 'todo.php';

            form.innerHTML = `
              <input type="hidden" name="edit_id" value="${taskId}">
              <input type="hidden" name="edit_task" value="${newValue}">
            `;
            document.body.appendChild(form);
            form.submit();
          }
        });
      }
    });
  });
});
</script>


<script>
document.getElementById('searchInput').addEventListener('input', function () {
  const query = this.value.toLowerCase();
  document.querySelectorAll('#taskList .todo-item').forEach(function (item) {
    const text = item.querySelector('.edit-todo-input').value.toLowerCase();
    item.style.display = text.includes(query) ? 'flex' : 'none';
  });
});
</script>

<script>
  var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
  tooltipTriggerList.forEach(function (tooltipTriggerEl) {
    new bootstrap.Tooltip(tooltipTriggerEl);
  });
</script>

</body>
</html>