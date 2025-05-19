<?php
session_start();
if (!isset($_SESSION['role']) || !in_array($_SESSION['role'], ['admin', 'superadmin'])) {
    header("Location: index.php");
    exit();
}

$username = htmlspecialchars($_SESSION['username']);
$role = $_SESSION['role'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Admin Panel</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background: #f8f9fa; }
        .info-box {
            background: white;
            border-left: 5px solid #0d6efd;
            padding: 25px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0,0,0,0.05);
        }
    </style>
</head>
<body>
<div class="container-fluid">
    <div class="row">
    <?php include 'includes/admin_sidebar.php'; ?>
    <div class="main-content" style="margin-left: 220px; padding: 30px;">
        <div class="col-md-10 p-4">
            <div class="info-box">
                <h3 class="mb-3">Welcome, <?= $username ?>!</h3>
                <p class="lead mb-3">
                    You are logged in as <strong><?= $role ?></strong> and have administrative privileges on this platform.
                </p>
                <p>
                    From this panel, you can:
                </p>
                <ul>
                    <li>Create, edit, and delete <strong>exercises</strong> for the training catalog.</li>
                    <li>Manage and update <strong>workout programs</strong>, including their structure and content.</li>
                    <li>View and manage the list of all <strong>registered users</strong>.</li>
                    <li>Grant or revoke <strong>premium status</strong> to users.</li>
                    <?php if ($role === 'superadmin'): ?>
                        <li>Change user <strong>roles</strong> (promote to admin or superadmin).</li>
                    <?php endif; ?>
                    <li>Monitor system activity via the <strong>logs</strong>.</li>
                </ul>
                <p class="text-muted mt-4">
                    Use the menu on the left to navigate through administrative functions.
                </p>
            </div>
        </div>
    </div>
</div>
</body>
</html>
