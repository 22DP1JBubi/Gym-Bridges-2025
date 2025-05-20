<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

$conn = new mysqli("localhost", "root", "", "gymbridges");
if ($conn->connect_error) die("Connection failed: " . $conn->connect_error);
mysqli_set_charset($conn, "utf8mb4");

$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
$user_id = $_SESSION['user_id'];

$sql = "SELECT d.*, p.title AS program_title 
        FROM training_diary d
        LEFT JOIN workout_programs p ON d.program_id = p.id
        WHERE d.id = $id AND d.user_id = $user_id
        LIMIT 1";
$result = $conn->query($sql);

if ($result->num_rows === 0) {
    echo "Entry not found or access denied.";
    exit();
}

$entry = $result->fetch_assoc();

$exercises_result = $conn->query("
    SELECT tde.*, e.name AS exercise_name
    FROM training_diary_exercises tde
    LEFT JOIN exercises e ON tde.exercise_id = e.id
    WHERE tde.diary_id = $id
");

$exercises = [];
while ($row = $exercises_result->fetch_assoc()) {
    $row['sets_json'] = json_decode($row['sets_json'], true);
    $exercises[] = $row;
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Diary Entry Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        .card {
            border-radius: 15px;
            background-color: #ffffff;
        }
    </style>
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="card shadow rounded-4 p-4 mb-4">
        <h3 class="mb-3"><i class="bi bi-journal-text text-primary me-2"></i>Diary Entry – <?= htmlspecialchars($entry['training_date']) ?></h3>

        <div class="mb-3"><strong>Mode:</strong> <?= ucfirst($entry['mode']) ?></div>
        <?php if ($entry['program_title']): ?>
            <div class="mb-3"><strong>Program:</strong> <?= htmlspecialchars($entry['program_title']) ?></div>
        <?php endif; ?>
        <?php if ($entry['program_day']): ?>
            <div class="mb-3"><strong>Day:</strong> <?= htmlspecialchars($entry['program_day']) ?></div>
        <?php endif; ?>
        <div class="mb-3"><strong>Mood:</strong> <?= $entry['mood_level'] ?>/5</div>
        <div class="mb-3"><strong>Duration:</strong> <?= $entry['training_time'] ?: '—' ?></div>
        <div class="mb-3"><strong>Notes:</strong><br><?= nl2br(htmlspecialchars($entry['notes'])) ?></div>

        <?php if (!empty($entry['images'])): 
            $images = json_decode($entry['images'], true); ?>
            <div class="mb-4">
                <strong>Images:</strong>
                <div class="d-flex flex-wrap gap-2 mt-2">
                    <?php foreach ($images as $img): ?>
                        <a href="<?= $img ?>" target="_blank">
                            <img src="<?= $img ?>" style="height: 100px; border-radius: 8px; object-fit: cover;">
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>
        <?php endif; ?>

        <h5 class="mb-3"><i class="bi bi-list-task me-2 text-success"></i> Exercises</h5>

        <?php foreach ($exercises as $ex): ?>
            <div class="border rounded p-3 mb-3 bg-white shadow-sm">
                <strong><?= htmlspecialchars($ex['exercise_name'] ?? "Exercise #{$ex['exercise_id']}") ?></strong>
                <div class="mt-2">
                    <?php
                    $sets = json_decode($ex['sets_json'], true);
                    if (is_array($sets) && count($sets) > 0):
                    ?>
                        <ul class="mb-0">
                            <?php foreach ($sets as $i => $set): ?>
                                <li>
                                    Set <?= $i + 1 ?>:
                                    <?= isset($set['weight']) ? "Weight: <strong>{$set['weight']} kg</strong>" : '' ?>,
                                    <?= isset($set['reps']) ? "Reps: <strong>{$set['reps']}</strong>" : '' ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <div class="text-muted">No set details</div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>





        <a href="training_diary.php" class="btn btn-outline-secondary mt-3">
            <i class="bi bi-arrow-left"></i> Back to Diary
        </a>
    </div>
</div>
</body>
</html>
