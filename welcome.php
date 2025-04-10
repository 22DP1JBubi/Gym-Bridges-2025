<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
$conn = new mysqli('localhost', 'root', '', 'gymbridges');
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM users WHERE userID='$user_id'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();


$avatar = !empty($user['avatar']) ? htmlspecialchars($user['avatar']) : 'images/default_avatar.png';

$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Welcome</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
  <link href="style.css" rel="stylesheet">
  <link href='https://fonts.googleapis.com/css?family=Anton' rel='stylesheet'>
  <style>
    .profile-header {
        background: linear-gradient(135deg, #4158D0 0%, #C850C0 100%);
        height: 150px;
        border-radius: 15px;
    }
    .profile-pic {
        width: 120px;
        height: 120px;
        border: 4px solid #fff;
        margin-top: -60px;
        background-color: #fff;
    }
    .settings-card {
        border-radius: 15px;
        border: none;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.05);
    }
    .settings-card:hover {
        box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    }
    .activity-item {
        border-left: 2px solid #e9ecef;
        padding-left: 20px;
        position: relative;
    }
    .activity-item::before {
        content: '';
        width: 12px;
        height: 12px;
        border-radius: 50%;
        background: #4158D0;
        position: absolute;
        left: -7px;
        top: 5px;
    }
  </style>
</head>
<body>
<?php include 'includes/header.php'; ?>

<div class="bg-light">
  <div class="container py-5">
    <div class="row">
      <div class="col-12 mb-4">
        <div class="profile-header position-relative mb-4">
          <div class="position-absolute top-0 end-0 p-3">
            <button class="btn btn-light"><i class="fas fa-edit me-2"></i>Edit Cover</button>
          </div>
        </div>
        <div class="text-center">
          <div class="position-relative d-inline-block">
          <img src="<?= $avatar ?>" class="rounded-circle profile-pic" alt="Avatar">
            <form method="POST" action="upload_avatar.php" enctype="multipart/form-data" class="position-absolute bottom-0 end-0">
              <label class="btn btn-primary btn-sm rounded-circle m-0">
                <i class="fas fa-camera"></i>
                <input type="file" name="avatar" onchange="this.form.submit()" hidden>
              </label>
            </form>
          </div>
          <h3 class="mt-3 mb-1"><?= htmlspecialchars($user['username']) ?></h3>
          <p class="text-muted mb-3"><?= htmlspecialchars($user['email']) ?></p>
        </div>
      </div>

      <div class="col-12">
        <div class="card border-0 shadow-sm">
          <div class="card-body p-0">
            <div class="row g-0">
              <div class="col-lg-3 border-end">
                <div class="p-4">
                  <div class="nav flex-column nav-pills">
                    <a class="nav-link active" href="#"><i class="fas fa-user me-2"></i>Profile</a>
                    <a class="nav-link" href="#"><i class="fas fa-lock me-2"></i>Security</a>
                    <a class="nav-link" href="#"><i class="fas fa-bell me-2"></i>Notifications</a>
                    <a class="nav-link" href="#"><i class="fas fa-credit-card me-2"></i>Billing</a>
                    <a class="nav-link" href="#"><i class="fas fa-chart-line me-2"></i>Progress</a>
                  </div>
                </div>
              </div>
              <div class="col-lg-9">
                <div class="p-4">
                  <h5 class="mb-4">Personal Info</h5>
                  <div class="row g-3">
                    <div class="col-md-6"><strong>Registration Date:</strong> <?= htmlspecialchars($user['registrationDate']) ?></div>
                    <div class="col-md-6"><strong>Last Login:</strong> <?= htmlspecialchars($user['lastLoginDate']) ?></div>
                    <div class="col-md-6"><strong>Premium:</strong> <?= $user['isPremium'] ? 'Yes' : 'No' ?></div>
                    <div class="col-md-6"><strong>Gender:</strong> <?= htmlspecialchars($user['gender']) ?></div>
                    <div class="col-md-6"><strong>Age:</strong> <?= htmlspecialchars($user['age']) ?></div>
                    <div class="col-md-6"><strong>Height:</strong> <?= htmlspecialchars($user['height']) ?> cm</div>
                    <div class="col-md-6"><strong>Weight:</strong> <?= htmlspecialchars($user['weight']) ?> kg</div>
                  </div>

                  <hr class="my-4">
                  <h5 class="mb-3">Actions</h5>
                  <a href="todo.php" class="btn btn-outline-primary me-2 mb-2"><i class="fas fa-tasks me-1"></i>To-Do List</a>
                  <a href="notes.php" class="btn btn-outline-secondary me-2 mb-2"><i class="fas fa-sticky-note me-1"></i>Notes</a>
                  <a href="progress.php" class="btn btn-outline-success me-2 mb-2"><i class="fas fa-chart-bar me-1"></i>Track Progress</a>
                  <a href="index.php" class="btn btn-outline-dark me-2 mb-2"><i class="fas fa-home me-1"></i>Home</a>
                  <a href="logout.php" class="btn btn-danger me-2 mb-2"><i class="fas fa-sign-out-alt me-1"></i>Logout</a>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>  
    </div>
  </div>
</div>

</body>
</html>
