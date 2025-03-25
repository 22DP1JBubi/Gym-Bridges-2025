<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'user_database');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$user_id = $_SESSION['user_id'];

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
            } else {
                $_SESSION['message'] = "Error: " . $conn->error;
                $_SESSION['message_type'] = "danger";
            }
        } else {
            $insert_sql = "INSERT INTO progress (user_id, date, weight) VALUES ('$user_id', '$date', '$weight')";
            if ($conn->query($insert_sql) === TRUE) {
                $_SESSION['message'] = "Weight recorded successfully.";
                $_SESSION['message_type'] = "success";
            } else {
                $_SESSION['message'] = "Error: " . $conn->error;
                $_SESSION['message_type'] = "danger";
            }
        }
    } else {
        $_SESSION['message'] = "Invalid weight value. Weight must be between 5 and 250 kg.";
        $_SESSION['message_type'] = "danger";
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
$progress_sql = "SELECT * FROM progress WHERE user_id='$user_id' ORDER BY date ASC";
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
    <link href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/moment"></script>
    <script src="https://cdn.jsdelivr.net/npm/chartjs-adapter-moment"></script>
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8 text-center">
            <?php if (isset($_SESSION['message'])): ?>
                <div class="alert alert-<?php echo $_SESSION['message_type']; ?>" role="alert">
                    <?php echo $_SESSION['message']; ?>
                    <?php unset($_SESSION['message']); unset($_SESSION['message_type']); ?>
                </div>
            <?php endif; ?>
            <h1 class="mt-3">Track Your Weight Progress</h1>
            
            <!-- Форма для записи и редактирования веса -->
            <form method="POST" action="progress.php" class="mt-3">
                <input type="hidden" name="id" id="recordId">
                <div class="form-group">
                    <label for="date">Date:</label>
                    <input type="date" id="date" name="date" class="form-control" required>
                </div>
                <div class="form-group">
                    <label for="weight">Weight (kg):</label>
                    <input type="number" id="weight" name="weight" step="0.1" class="form-control" required>
                </div>
                <button type="submit" class="btn btn-primary">Record Weight</button>
                <button type="reset" class="btn btn-danger" onclick="clearForm()">Clear</button>
            </form>
            <a href="welcome.php" class="btn btn-secondary mt-3">Back to Profile</a>
            <a href="logout.php" class="btn btn-secondary mt-3">Logout</a>
            
            <!-- Таблица записей веса -->
            <h2 class="mt-5">Weight Records</h2>
            <table class="table table-bordered mt-3">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Weight (kg)</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($progress_data as $entry): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($entry['date']); ?></td>
                            <td><?php echo htmlspecialchars($entry['weight']); ?></td>
                            <td>
                                <button class="btn btn-info btn-sm" onclick="editRecord(<?php echo $entry['id']; ?>, '<?php echo $entry['date']; ?>', <?php echo $entry['weight']; ?>)">Edit</button>
                                <a href="progress.php?delete=<?php echo $entry['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this entry?');">Delete</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            
            <!-- График прогресса -->
            <canvas id="progressChart" class="mt-5"></canvas>
        </div>
    </div>
</div>
<script>
    function editRecord(id, date, weight) {
        document.getElementById('recordId').value = id;
        document.getElementById('date').value = date;
        document.getElementById('weight').value = weight;
    }

    function clearForm() {
        document.getElementById('recordId').value = '';
    }

    document.addEventListener('DOMContentLoaded', function () {
        var ctx = document.getElementById('progressChart').getContext('2d');
        var progressData = <?php echo json_encode($progress_data); ?>;
        
        var dates = progressData.map(function(item) {
            return item.date;
        });
        var weights = progressData.map(function(item) {
            return item.weight;
        });

        var chart = new Chart(ctx, {
            type: 'line',
            data: {
                labels: dates,
                datasets: [{
                    label: 'Weight (kg)',
                    data: weights,
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
                            unit: 'day'
                        }
                    },
                    y: {
                        beginAtZero: false
                    }
                }
            }
        });
    });
</script>
</body>
</html>
