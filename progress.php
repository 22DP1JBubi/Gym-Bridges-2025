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

include 'includes/avatar_loader.php';

mysqli_set_charset($conn, "utf8mb4");

include 'includes/header.php';





// Запись данных о весе
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['weight'])) {
    $weight = $_POST['weight'];
    $date = $_POST['date'];
    $id = isset($_POST['id']) ? $_POST['id'] : null;

    if (is_numeric($weight) && $weight >= 5 && $weight <= 250) {
        if ($id) {
            $update_sql = "UPDATE progress SET date='$date', weight='$weight' WHERE id='$id' AND user_id='$user_id'";
            if ($conn->query($update_sql) === TRUE) {
                $_SESSION['message'] = "Weight updated successfully.";
                $_SESSION['message_type'] = "success";
                header("Location: progress.php"); // 🔁 <--- вот это добавь
                exit();
            }
        } else {
            $insert_sql = "INSERT INTO progress (user_id, date, weight) VALUES ('$user_id', '$date', '$weight')";
            if ($conn->query($insert_sql) === TRUE) {
                $_SESSION['message'] = "Weight recorded successfully.";
                $_SESSION['message_type'] = "success";
                header("Location: progress.php"); // 🔁 <--- и тут тоже
                exit();
            }
        }
    } else {
        $_SESSION['message'] = "Invalid weight value.";
        $_SESSION['message_type'] = "danger";
        header("Location: progress.php"); // 👈 даже при ошибке можно редиректнуть
        exit();
    }
}


// Удаление записи
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];
    $delete_sql = "DELETE FROM progress WHERE id='$id' AND user_id='$user_id'";
    if ($conn->query($delete_sql) === TRUE) {
        $_SESSION['message'] = "Weight entry deleted successfully.";
        $_SESSION['message_type'] = "success";
    } else {
        $_SESSION['message'] = "Error: " . $conn->error;
        $_SESSION['message_type'] = "danger";
    }
}

// Получение данных прогресса для графика и таблицы
$progress_sql = "SELECT * FROM progress WHERE user_id='$user_id' ORDER BY date DESC";

$progress_result = $conn->query($progress_sql);
$progress_data = [];
while ($row = $progress_result->fetch_assoc()) {
    $progress_data[] = $row;
}

$conn->close();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Track Your Progress</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <link rel="stylesheet" type="text/css" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-annotation@1.1.0"></script>


    <style>
        .card-style {
            border-radius: 15px;
            background-color: #ffffff;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            padding: 30px;
        }

        .weight-record-row .date-cell,
        .weight-record-row .weight-cell {
            font-size: 1.1rem;
            font-weight: 600;
            color: #333;
        }

        .btn-custom-sort {
        background-color: #f8f9fa;
        color: #212529;
        border: 1px solid #ced4da;
        transition: background-color 0.3s ease, color 0.3s ease;
        }

        .btn-custom-sort:hover,
        .btn-custom-sort:focus {
        background-color: #e2e6ea;
        color: #212529;
        border-color: #adb5bd;
        text-decoration: none;
        box-shadow: 0 0 0 0.1rem rgba(0, 0, 0, 0.15);
        }

    </style>
</head>
<body>


<?php if (isset($_SESSION['message'])): ?>
  <div id="session-alert" class="position-fixed top-0 start-50 translate-middle-x alert alert-<?= $_SESSION['message_type']; ?> text-center shadow" 
       style="z-index: 1050; margin-top: 60px; min-width: 300px;">
    <?= $_SESSION['message']; ?>
    <?php unset($_SESSION['message'], $_SESSION['message_type']); ?>
  </div>
<?php endif; ?>



<div class="container mt-5">
    <div class="row justify-content-center">
            <!-- 🔝 Общий заголовок страницы -->
            <div class="text-center mb-5">
                <div class="d-flex justify-content-center align-items-center mb-2">
                    <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 50px; height: 50px;">
                        <i class="bi bi-graph-up" style="font-size: 1.5rem;"></i>
                    </div>
                    <h2 class="mb-0">Track Your Weight Progress</h2>
                </div>
                <p class="text-muted mb-0">Monitor and manage your weight history with daily tracking, visual charts and insights.</p>
            </div>

            <!-- 🟦 Карточка добавления веса -->
            <div class="card shadow rounded-4 p-4 mb-4 card-style">

                <!-- 🔹 Заголовок карточки формы -->
                <div class="d-flex align-items-center mb-2">
                    <div class="bg-info text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 50px; height: 50px;">
                        <i class="bi bi-pencil-square" style="font-size: 1.4rem;"></i>
                    </div>
                    <h4 class="mb-0">Add New Weight Entry</h4>
                </div>
                <p class="text-muted mb-4">Enter your current weight and save it to track your progress over time.</p>




            
            <!-- Форма для записи и редактирования веса -->
            <form method="POST" action="progress.php" class="mt-3">
                <input type="hidden" name="id" id="recordId">



               <div class="mb-3">
                <label for="date" class="form-label fw-bold">Date of Entry</label>
                <div class="input-group">
                    <span class="input-group-text bg-light" id="calendar-addon" style="cursor: pointer;">
                    <i class="bi bi-calendar-event"></i>
                    </span>
                    <input type="text" id="date" name="date" class="form-control" placeholder="Select a date" required aria-describedby="calendar-addon">
                </div>
            </div>




            <label for="weightInput" class="form-label fw-bold">Weight</label>

            <div class="input-group mb-3">
                <span class="input-group-text bg-light"><i class="bi bi-speedometer2"></i></span>
                <input type="number" class="form-control" id="weightInput" name="weight" min="25" max="200" step="0.1" value="75" required>
                <span class="input-group-text">kg</span>
            </div>

            <!-- Ползунок со списком значений -->
            <input type="range" class="form-range" id="weightSlider" min="25" max="200" step="1" list="tickmarks" value="75">

            <datalist id="tickmarks">
                <option value="25"></option>
                <option value="50"></option>
                <option value="75"></option>
                <option value="100"></option>
                <option value="125"></option>
                <option value="150"></option>
                <option value="175"></option>
                <option value="200"></option>
            </datalist>

            <!-- Метки -->
            <div class="d-flex justify-content-between small text-muted px-1">
                <span>25</span>
                <span>50</span>
                <span>75</span>
                <span>100</span>
                <span>125</span>
                <span>150</span>
                <span>175</span>
                <span>200</span>
            </div>

                <div class="d-flex flex-wrap justify-content-end gap-2 mt-3">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-1"></i> Record Weight
                    </button>

                    <button type="button" class="btn btn-outline-secondary" onclick="clearForm()">
                        <i class="bi bi-x-circle me-1"></i> Clear
                    </button>

                </div>

            </form>
        </div>

            


        <!-- Блок графика прогресса -->
        <div class="card shadow rounded-4 p-4 mb-4 card-style mt-4">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-success text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 50px; height: 50px;">
                    <i class="bi bi-bar-chart-line" style="font-size: 1.4rem;"></i>
                </div>
                <h4 class="mb-0">Weight Trend Chart</h4>
            </div>
            <p class="text-muted mb-4">Visual overview of your weight changes over time.</p>


            <!-- Фильтры диапазона -->

            <!-- Фильтры диапазона -->
            <div class="d-flex flex-wrap align-items-center justify-content-between gap-3 mb-3">
                <div class="btn-group" role="group" id="dateRangeButtons">
                    <button type="button" class="btn btn-outline-secondary" data-range="day">Day</button>
                    <button type="button" class="btn btn-outline-secondary" data-range="3days">3 Days</button>
                    <button type="button" class="btn btn-outline-secondary" data-range="week">Week</button>
                    <button type="button" class="btn btn-outline-secondary" data-range="month">Month</button>
                    <button type="button" class="btn btn-outline-secondary active" data-range="year">Year</button>
                    <button type="button" class="btn btn-outline-secondary" data-range="all">All Time</button>
                    <button type="button" class="btn btn-outline-secondary" data-range="custom">Custom</button>
                </div>

                <div class="d-flex align-items-center flex-nowrap gap-2" id="customDatePickers" style="display: none; white-space: nowrap;">
                    <label class="mb-0 text-muted small" for="startDate">Set custom range:</label>
                    <input type="text" id="startDate" class="form-control form-control-sm" placeholder="Start date" style="min-width: 120px;">
                    <span>to</span>
                    <input type="text" id="endDate" class="form-control form-control-sm" placeholder="End date" style="min-width: 120px;">
                    <button class="btn btn-sm btn-secondary" type="button" id="clearCustomDates" title="Clear dates">
                        <i class="bi bi-x-lg"></i>
                    </button>
                </div>
            </div>

            <div class="text-start mb-3">
                <button id="toggleAreaChart" class="btn btn-sm btn-outline-secondary">
                    <i class="bi bi-graph-up-arrow me-1"></i> Toggle Area Chart
                </button>

                <button class="btn btn-sm btn-outline-secondary" data-bs-toggle="modal" data-bs-target="#chartSettingsModal">
                    <i class="bi bi-sliders"></i> Chart Settings
                </button>
            </div>






            <div class="bg-light rounded-3 p-3 shadow-sm">
                <canvas id="progressChart" style="max-height: 320px;"></canvas>
            </div>
        </div>


        <!-- Карточка Weight Records -->
        <div class="card shadow rounded-4 p-4 mb-4 card-style mt-4">
            <div class="d-flex align-items-center mb-3">
                <div class="bg-warning text-white rounded-circle d-flex align-items-center justify-content-center me-2" style="width: 50px; height: 50px;">
                    <i class="bi bi-journal-text" style="font-size: 1.4rem;"></i>
                </div>
                <h4 class="mb-0">Weight Records</h4>
            </div>
            <p class="text-muted mb-4">Your tracked weight entries displayed below.</p>

            <div class="d-flex justify-content-start gap-2 mb-3">
                <button id="sortByDate" class="btn btn-sm btn-custom-sort rounded-pill px-3">
                    <i class="bi bi-calendar-date me-1"></i> Sort by Date <span id="dateSortIcon" class="bi"></span>
                </button>

                <button id="sortByWeight" class="btn btn-sm btn-custom-sort rounded-pill px-3">
                    <i class="bi bi-speedometer2 me-1"></i> Sort by Weight <span id="weightSortIcon" class="bi"></span>
                </button>

            </div>




            <div class="table-responsive">
                <div class="row fw-bold border-bottom pb-2 mb-2 text-muted d-none d-md-flex">
                    <div class="col-md-4">Date</div>
                    <div class="col-md-4">Weight (kg)</div>
                    <div class="col-md-4">Actions</div>
                </div>

                <?php foreach ($progress_data as $entry): ?>
                    <div id="record-row-<?php echo $entry['id']; ?>" class="row align-items-center border-bottom py-2 weight-record-row"
                        data-date="<?php echo htmlspecialchars($entry['date']); ?>"
                        data-weight="<?php echo htmlspecialchars($entry['weight']); ?>">
                        <div class="col-md-4 date-cell"><?php echo htmlspecialchars($entry['date']); ?></div>
                        <div class="col-md-4 weight-cell"><?php echo htmlspecialchars($entry['weight']); ?> kg</div>

                        <div class="col-md-4 d-flex gap-2 flex-wrap">
                            <button class="btn btn-info btn-sm" onclick="enableEdit(<?php echo $entry['id']; ?>)">
                                <i class="bi bi-pencil"></i> Edit
                            </button>
                            <a href="progress.php?delete=<?php echo $entry['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Delete this entry?')">
                                <i class="bi bi-trash"></i> Delete
                            </a>
                        </div>
                    

                        <!-- Редактирование -->
                        <form method="POST" action="progress.php" class="d-none edit-mode row align-items-center g-2 flex-nowrap">
                            <input type="hidden" name="id" value="<?php echo $entry['id']; ?>">

                            <div class="col-md-4">
                                <input type="date" name="date" class="form-control form-control-sm" value="<?php echo htmlspecialchars($entry['date']); ?>" required>
                            </div>
                            <div class="col-md-4">
                                <div class="input-group input-group-sm">
                                    <input type="number" name="weight" class="form-control" min="25" max="200" step="0.1" value="<?php echo htmlspecialchars($entry['weight']); ?>" required>
                                    <span class="input-group-text">kg</span>
                                </div>
                            </div>
                            <div class="col-md-4 d-flex flex-wrap gap-2">
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="bi bi-check2"></i> Save
                                </button>
                                <button type="button" class="btn btn-secondary btn-sm" onclick="cancelEdit(<?php echo $entry['id']; ?>)">
                                    <i class="bi bi-x-lg"></i> Cancel
                                </button>
                            </div>
                        </form>
                    </div>
                <?php endforeach; ?>


            </div>
        </div>
            

    </div>
    </div>
</div>


<div class="modal fade" id="chartSettingsModal" tabindex="-1">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title">Chart Settings</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        <div class="mb-2">
          <label for="lineColor" class="form-label">Line Color</label>
          <input type="color" id="lineColor" class="form-control form-control-color" value="#4bc0c0">
        </div>
        <div class="mb-2">
          <label for="fillColor" class="form-label">Fill Color</label>
          <input type="color" id="fillColor" class="form-control form-control-color" value="#4bc0c033">
        </div>
        <div class="mb-2">
          <label for="lineWidth" class="form-label">Line Width</label>
          <input type="range" id="lineWidth" class="form-range" min="1" max="10" value="2">
        </div>
        <div class="form-check mb-2">
          <input class="form-check-input" type="checkbox" id="showPoints" checked>
          <label class="form-check-label" for="showPoints">Show Points</label>
        </div>
        <div class="form-check mb-2">
          <input class="form-check-input" type="checkbox" id="enableFill">
          <label class="form-check-label" for="enableFill">Enable Fill</label>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" onclick="applyChartSettings()">Apply</button>
      </div>
    </div>
  </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
    function editRecord(id, date, weight) {
    document.getElementById('recordId').value = id;
    document.getElementById('date').value = date;
    document.getElementById('weightInput').value = weight;
    document.getElementById('weightSlider').value = weight;
}



function clearForm() {
    // Сброс скрытого id
    document.getElementById('recordId').value = '';

    // Сброс даты (flatpickr)
    fp.clear();

    // Сброс веса и ползунка к значению по умолчанию
    const defaultWeight = 75;
    weightInput.value = defaultWeight;
    weightSlider.value = defaultWeight;
}
</script>

<script>
function enableEdit(id) {
    const row = document.getElementById(`record-row-${id}`);
    row.querySelectorAll('.view-mode').forEach(el => el.classList.add('d-none'));
    row.querySelector('.edit-mode').classList.remove('d-none');
}

function cancelEdit(id) {
    const row = document.getElementById(`record-row-${id}`);
    row.querySelector('.edit-mode').classList.add('d-none');
    row.querySelectorAll('.view-mode').forEach(el => el.classList.remove('d-none'));
}
</script>


<script>
let chart;
const originalData = <?php echo json_encode($progress_data); ?>;

document.addEventListener('DOMContentLoaded', function () {
    const ctx = document.getElementById('progressChart').getContext('2d');

    const progressData = <?php echo json_encode($progress_data); ?>;

    // 🔁 Получаем уникальные года из всех данных (не только отфильтрованных)
    const uniqueYears = [...new Set(progressData.map(item => item.date.slice(0, 4)))];
    const yearAnnotations = {};
    uniqueYears.forEach(year => {
        yearAnnotations[year] = {
            type: 'line',
            scaleID: 'x',
            value: `${year}-01-01`,
            borderColor: '#888',
            borderWidth: 1,
            borderDash: [4, 4],
            label: {
                content: year,
                enabled: true,
                position: 'start',
                backgroundColor: '#444',
                color: '#fff',
                font: {
                    weight: 'bold'
                }
            }
        };
    });




    chart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: originalData.map(item => item.date),
            datasets: [{
                label: 'Weight (kg)',
                data: originalData.map(item => item.weight),
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2,
                fill: false
            }]
        },
        options: {
        responsive: true,
        scales: {
            x: {
                type: 'time',
                time: {
                    unit: 'day',
                    tooltipFormat: 'MMM D, YYYY'
                },
                ticks: {
                    autoSkip: true,
                    maxRotation: 45,
                    minRotation: 30
                }
            },
            y: {
                beginAtZero: false
            }
        },
        plugins: {
            annotation: {
                annotations: yearAnnotations
            },
            legend: {
                display: true
            }
        }
    }


    });


    let isAreaChart = false;

    document.getElementById('toggleAreaChart').addEventListener('click', function () {
    isAreaChart = !isAreaChart;

    chart.data.datasets[0].fill = isAreaChart;
    chart.data.datasets[0].backgroundColor = isAreaChart
        ? 'rgba(75, 192, 192, 0.2)'
        : 'transparent';

    chart.update();
    });


    // Слушатель на изменения custom-даты
    document.getElementById("startDate").addEventListener("change", () => filterChart('custom'));
    document.getElementById("endDate").addEventListener("change", () => filterChart('custom'));

     // Вызов фильтра по умолчанию — найти активную кнопку
    const defaultRangeBtn = document.querySelector('#dateRangeButtons .btn.active');
    if (defaultRangeBtn) {
        const defaultRange = defaultRangeBtn.getAttribute('data-range');
        filterChart(defaultRange);
    }

});

function filterChart(range) {
    const now = moment();
    let filtered = [];

    if (range === 'all') {
        filtered = originalData;
    } else if (range === 'custom') {
        const start = moment(document.getElementById('startDate').value);
        const end = moment(document.getElementById('endDate').value);
        filtered = originalData.filter(item => {
            const date = moment(item.date);
            return date.isSameOrAfter(start) && date.isSameOrBefore(end);
        });
    } else {
        let days = {
            day: 1,
            '3days': 3,
            week: 7,
            month: 30,
            year: 365
        }[range] || 0;

        filtered = originalData.filter(item => {
            const date = moment(item.date);
            return date.isSameOrAfter(now.clone().subtract(days, 'days'));
        });
    }

    // Обновление графика
    chart.data.labels = filtered.map(item => item.date);
    chart.data.datasets[0].data = filtered.map(item => item.weight);

    // 🔧 Установка минимальной и максимальной даты
    if (filtered.length > 0) {
        const dates = filtered.map(item => item.date).sort();
        chart.options.scales.x.min = dates[0];
        chart.options.scales.x.max = dates[dates.length - 1];
    }

    chart.update();
}

</script>


<script>
  const fp = flatpickr("#date", {
    dateFormat: "Y-m-d",
    altInput: true,
    altFormat: "F j, Y",
    maxDate: "today",
    defaultDate: "today"
  });

  // Клик по иконке также открывает календарь
  document.getElementById("calendar-addon").addEventListener("click", function () {
    fp.open();
  });
</script>

<script>
const weightInput = document.getElementById("weightInput");
const weightSlider = document.getElementById("weightSlider");

// Слайдер -> поле
weightSlider.addEventListener("input", () => {
  weightInput.value = weightSlider.value;
});

// Поле -> слайдер (с округлением до шага)
weightInput.addEventListener("input", () => {
  let val = parseFloat(weightInput.value);
  if (!isNaN(val)) {
    weightSlider.value = val;
  }
});

</script>


<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

<script>
flatpickr("#startDate", {
    dateFormat: "Y-m-d",
    maxDate: "today"
});

flatpickr("#endDate", {
    dateFormat: "Y-m-d",
    maxDate: "today"
});

document.querySelectorAll('#dateRangeButtons .btn').forEach(btn => {
    btn.addEventListener('click', () => {
        // Сброс всех кнопок
        document.querySelectorAll('#dateRangeButtons .btn').forEach(b => b.classList.remove('active'));
        btn.classList.add('active');

        const range = btn.getAttribute('data-range');

        // Показать/скрыть custom input
        document.getElementById('customDatePickers').style.display = (range === 'custom') ? 'flex' : 'none';

        // Вызываем отрисовку графика с нужным фильтром
        filterChart(range);
    });
});
</script>

<script>
document.getElementById('clearCustomDates').addEventListener('click', () => {
    document.getElementById('startDate').value = '';
    document.getElementById('endDate').value = '';
    // Можно скрыть график или сбросить фильтрацию
});
</script>

<script>
function applyChartSettings() {
  const lineColor = document.getElementById("lineColor").value;
  const fillColor = document.getElementById("fillColor").value;
  const lineWidth = parseInt(document.getElementById("lineWidth").value);
  const showPoints = document.getElementById("showPoints").checked;
  const enableFill = document.getElementById("enableFill").checked;

  chart.data.datasets[0].borderColor = lineColor;
  chart.data.datasets[0].borderWidth = lineWidth;
  chart.data.datasets[0].pointRadius = showPoints ? 3 : 0;
  chart.data.datasets[0].fill = enableFill;
  chart.data.datasets[0].backgroundColor = enableFill ? fillColor : "transparent";

  chart.update();
}
</script>


<script>
let sortDateAsc = false;
let sortWeightAsc = false;

document.getElementById('sortByDate').addEventListener('click', () => {
    sortDateAsc = !sortDateAsc; // ← Сначала инвертируем

    const container = document.querySelector('.table-responsive');
    const rows = Array.from(document.querySelectorAll('.weight-record-row'));

    rows.sort((a, b) => {
        const d1 = new Date(a.dataset.date);
        const d2 = new Date(b.dataset.date);
        return sortDateAsc ? d1 - d2 : d2 - d1;
    });

    sortWeightAsc = false;

    document.getElementById('dateSortIcon').className = sortDateAsc ? 'bi bi-arrow-up-short' : 'bi bi-arrow-down-short';
    document.getElementById('weightSortIcon').className = '';

    rows.forEach(row => container.appendChild(row));
});

document.getElementById('sortByWeight').addEventListener('click', () => {
    sortWeightAsc = !sortWeightAsc; // ← Сначала инвертируем

    const container = document.querySelector('.table-responsive');
    const rows = Array.from(document.querySelectorAll('.weight-record-row'));

    rows.sort((a, b) => {
        const w1 = parseFloat(a.dataset.weight);
        const w2 = parseFloat(b.dataset.weight);
        return sortWeightAsc ? w1 - w2 : w2 - w1;
    });

    sortDateAsc = false;

    document.getElementById('weightSortIcon').className = sortWeightAsc ? 'bi bi-arrow-up-short' : 'bi bi-arrow-down-short';
    document.getElementById('dateSortIcon').className = '';

    rows.forEach(row => container.appendChild(row));
});

</script>

<script>
  setTimeout(() => {
    const alert = document.getElementById('session-alert');
    if (alert) {
      alert.style.transition = 'opacity 0.5s ease';
      alert.style.opacity = '0';
      setTimeout(() => alert.remove(), 500);
    }
  }, 3000); // исчезает через 3 секунды
</script>


<?php include 'includes/footer.php'; ?>
</body>
</html>
