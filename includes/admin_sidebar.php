<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
<style>
    .sidebar {
        position: fixed;
        top: 0;
        left: 0;
        height: 100vh;
        width: 220px;
        background: #343a40;
        color: white;
        z-index: 1000;
        padding-top: 1rem;
    }
    .sidebar a {
        color: white;
        display: block;
        padding: 10px 15px;
        text-decoration: none;
    }
    .sidebar a:hover {
        background: #495057;
    }
</style>

<div class="sidebar">
    <h4 class="text-center">Admin Panel</h4>
    <a href="admin_panel.php"><i class="bi bi-speedometer2"></i> Dashboard</a>
    <a href="exercises_list.php"><i class="bi bi-activity"></i> Exercises</a>
    <a href="create_workout_program.php"><i class="bi bi-journal-check"></i> Programs</a>
    <a href="manage_users.php"><i class="bi bi-people"></i> Users</a>
    <!-- <a href="log_of_jobs.php"><i class="bi bi-clock-history"></i> Logs</a> -->
    <a href="welcome.php"><i class="bi bi-person-circle"></i> Your profile</a>
    <a href="index.php"><i class="bi bi-house-door"></i> Main page</a>

    <a href="logout.php"><i class="bi bi-box-arrow-right"></i> Logout</a>
</div>
