<?php
session_start();

$conn = new mysqli("localhost", "root", "", "gymbridges");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
include 'includes/avatar_loader.php';

mysqli_set_charset($conn, "utf8mb4");

include 'includes/header.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>About Us – GymBridges</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"/>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.7.2/font/bootstrap-icons.css"/>
  <link rel="stylesheet" href="style.css"/>
  <style>
    .section-title {
      font-family: Anton, sans-serif;
      font-size: 40px;
      color: #0D1C2E;
      letter-spacing: 1.5px;
      text-align: center;
      margin-bottom: 40px;
    }
    .info-card {
      background: #fff;
      border-radius: 12px;
      padding: 30px;
      box-shadow: 0 0 15px rgba(0,0,0,0.08);
      margin-bottom: 30px;
    }
    .info-card h3 {
      font-weight: 600;
      font-size: 24px;
      margin-bottom: 15px;
    }
    .info-card p {
      font-size: 18px;
    }
    .info-card i {
      font-size: 32px;
      color: #0d6efd;
      margin-right: 15px;
    }
    .embed-responsive {
      position: relative;
      display: block;
      width: 100%;
      padding-top: 56.25%;
      overflow: hidden;
      border-radius: 12px;
      box-shadow: 0 0 12px rgba(0,0,0,0.15);
      margin-bottom: 50px;
    }
    .embed-responsive iframe {
      position: absolute;
      top: 0; left: 0; width: 100%; height: 100%;
      border: 0;
    }
  </style>
</head>
<body class="bg-light">


<div class="container py-5">
  <h1 class="section-title">About GymBridges</h1>

  <div class="embed-responsive">
    <iframe src="https://www.youtube.com/embed/honOlDzo45Q?si=OJ8apvULjbfK3Deh&vq=hd1080"
            allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
            allowfullscreen></iframe>
  </div>

  <div class="row">
    <div class="col-md-6">
      <div class="info-card">
        <h3><i class="bi bi-lightbulb"></i> The Problem</h3>
        <p>Many fitness users lack consistency and struggle to find centralized and reliable information about exercises, nutrition, or workout planning. Fitness content is often scattered across multiple platforms, causing confusion and reducing motivation.</p>
      </div>
    </div>
    <div class="col-md-6">
      <div class="info-card">
        <h3><i class="bi bi-patch-check"></i> The Solution</h3>
        <p>GymBridges is a multifunctional platform that unites workout programs, exercise instructions, gym maps, progress tracking and trainer collaboration – all in one place. It simplifies your journey and helps you stay on track.</p>
      </div>
    </div>
  </div>

  <div class="info-card">
    <h3><i class="bi bi-bullseye"></i> Our Goals</h3>
    <ul>
      <li><strong>Centralized experience:</strong> One place for everything — training, nutrition, progress, and expert help.</li>
      <li><strong>Personalization:</strong> Smart recommendations for workouts and meals tailored to your profile.</li>
      <li><strong>Motivation:</strong> Track your progress, receive encouragement, and achieve real results.</li>
      <li><strong>Collaboration:</strong> Trainers can offer programs and connect directly with clients.</li>
    </ul>
  </div>

  <div class="info-card">
    <h3><i class="bi bi-people"></i> Target Audience</h3>
    <ul>
      <li><strong>Beginners:</strong> Who want guidance and structure from the start.</li>
      <li><strong>Experienced athletes:</strong> Seeking variety, progress tracking and challenge.</li>
      <li><strong>Busy individuals:</strong> Who want smart, time-saving workout planning.</li>
      <li><strong>Personal trainers:</strong> Looking to share their programs and grow their client base.</li>
    </ul>
  </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
