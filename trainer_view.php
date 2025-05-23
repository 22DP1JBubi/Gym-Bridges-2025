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

if (!isset($_GET['id'])) {
    echo "Trainer ID missing.";
    exit();
}

$trainer_id = intval($_GET['id']);
$stmt = $conn->prepare("SELECT * FROM trainers WHERE id = ?");
$stmt->bind_param("i", $trainer_id);
$stmt->execute();
$result = $stmt->get_result();
$trainer = $result->fetch_assoc();

if (!$trainer) {
    echo "Trainer not found.";
    exit();
}

// Decode social links
$socialLinks = json_decode($trainer['social_links'], true) ?? [];

function iconForLink($name) {
    $icons = [
        'instagram' => 'bi-instagram',
        'facebook' => 'bi-facebook',
        'youtube' => 'bi-youtube',
        'tiktok' => 'bi-tiktok',
        'linkedin' => 'bi-linkedin'
    ];
    return $icons[$name] ?? 'bi-globe';
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= htmlspecialchars($trainer['first_name'] . ' ' . $trainer['last_name']) ?> - Trainer Profile</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <link rel="stylesheet" href="style.css">
  <style>
    body { background-color: #f8f9fa; }
    .profile-card {
      background: #fff;
      border-radius: 16px;
      padding: 30px;
      box-shadow: 0 10px 25px rgba(0,0,0,0.08);
      margin: 40px auto;
      max-width: 1000px;
    }
    .profile-img {
      max-width: 100%;
      border-radius: 12px;
      object-fit: cover;
    }
    .section-title {
      font-weight: bold;
      font-size: 1.2rem;
      margin-bottom: 10px;
    }
    .badge-pill {
      margin: 3px;
    }
    .icon-label {
      font-weight: 500;
      margin-right: 10px;
    }
  </style>
</head>
<body>
<div class="container">
  <div class="profile-card">
    <div class="row">
      <div class="col-md-5">
        <img src="<?= htmlspecialchars($trainer['image']) ?>" alt="Trainer Photo" class="profile-img w-100">
      </div>
      <div class="col-md-7">
        <h2 class="mb-3"><?= htmlspecialchars($trainer['first_name'] . ' ' . $trainer['last_name']) ?></h2>
        <p><span class="icon-label"><i class="bi bi-envelope"></i> Email:</span> <?= htmlspecialchars($trainer['email']) ?></p>
        <p><span class="icon-label"><i class="bi bi-telephone"></i> Phone:</span> <?= htmlspecialchars($trainer['phone']) ?></p>
        <p><span class="icon-label"><i class="bi bi-geo-alt"></i> Location:</span> <?= htmlspecialchars($trainer['city']) ?>, <?= htmlspecialchars($trainer['country']) ?></p>
        <p><span class="icon-label"><i class="bi bi-award"></i> Experience:</span> <?= intval($trainer['experience']) ?> years</p>
        <p><span class="icon-label"><i class="bi bi-activity"></i> Availability:</span> <?= htmlspecialchars($trainer['availability']) ?></p>
        <p><span class="icon-label"><i class="bi bi-bar-chart"></i> Client Level:</span> <?= htmlspecialchars($trainer['level']) ?></p>
        <p><span class="icon-label"><i class="bi bi-cash"></i> Price:</span> &euro;<?= htmlspecialchars($trainer['price']) ?></p>
        <p>
          <span class="icon-label"><i class="bi bi-translate"></i> Languages:</span>
          <?php foreach (explode(',', $trainer['languages']) as $lang): ?>
            <span class="badge bg-primary badge-pill"><?= trim($lang) ?></span>
          <?php endforeach; ?>
        </p>
        <p>
          <span class="icon-label"><i class="bi bi-tags"></i> Specialization:</span>
          <?php foreach (explode(',', $trainer['specialization']) as $spec): ?>
            <span class="badge bg-secondary badge-pill"><?= trim($spec) ?></span>
          <?php endforeach; ?>
        </p>
        <?php if (!empty($socialLinks)): ?>
          <p class="mt-3">
            <span class="icon-label"><i class="bi bi-share"></i> Socials:</span>
            <?php foreach ($socialLinks as $platform => $link): ?>
              <a href="<?= htmlspecialchars($link) ?>" class="me-2" target="_blank">
                <i class="bi <?= iconForLink($platform) ?> fs-5"></i>
              </a>
            <?php endforeach; ?>
          </p>
        <?php endif; ?>
      </div>
    </div>

    <hr class="my-4">
    <div class="mt-3">
      <h4 class="section-title"><i class="bi bi-person-lines-fill"></i> About the Trainer</h4>
      <p><?= nl2br(htmlspecialchars($trainer['description'])) ?></p>
    </div>
  </div>
</div>

<?php include 'includes/footer.php'; ?>
</body>
</html>
