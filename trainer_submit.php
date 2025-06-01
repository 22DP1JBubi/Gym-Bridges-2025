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

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $firstName = trim($_POST['first_name'] ?? '');
    $lastName = trim($_POST['last_name'] ?? '');
    $fullName = $firstName . ' ' . $lastName;

    $city = trim($_POST['city'] ?? '');
    $experience = intval($_POST['experience'] ?? 0);
    $availability = $_POST['availability'] ?? '';
    $level = $_POST['level'] ?? '';
    $price = trim($_POST['price'] ?? '');
    $description = trim($_POST['description'] ?? '');

    $specArray = $_POST['specialization'] ?? [];
    $specialization = implode(', ', $specArray);

    $languagesArray = $_POST['languages'] ?? [];
    $languages = implode(', ', $languagesArray);

    $social_links = json_encode($_POST['social_links'] ?? []);
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $country = trim($_POST['country'] ?? '');


    // Image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $uploadDir = 'uploads/trainers/';
        if (!is_dir($uploadDir)) mkdir($uploadDir, 0777, true);

        $fileName = basename($_FILES['image']['name']);
        $targetPath = $uploadDir . uniqid() . '_' . $fileName;

        move_uploaded_file($_FILES['image']['tmp_name'], $targetPath);
        $imagePath = $targetPath; // сохранить в базу
    } else {
        $imagePath = null;
    }


    if (empty($message)) {
        $stmt = $conn->prepare("INSERT INTO trainers 
            (first_name, last_name, email, phone, city, country, experience, specialization, availability, level, languages, price, description, image, social_links, status, created_at) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())");

            $stmt->bind_param("ssssssissssssss", 
            $firstName, $lastName, $email, $phone, $city, $country, $experience, 
            $specialization, $availability, $level, $languages, $price, 
            $description, $imagePath, $social_links
            );



        if ($stmt->execute()) {
            $message = "Your trainer profile has been submitted for review.";
        } else {
            $message = "Error saving data: " . $conn->error;
        }
        $stmt->close();
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Submit Trainer Profile</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">
  <link rel="stylesheet" type="text/css" href="style.css">
  

  <style>
    body {
      background-color: #f8f9fa;
    }
    .card {
      max-width: 800px;
      margin: 40px auto;
      padding: 30px;
      border-radius: 16px;
      box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
    }
    .form-control, .form-select, .select2-container .select2-selection--single {
      border-radius: 10px;
    }
    .form-title {
      font-size: 28px;
      font-weight: 600;
      text-align: center;
      margin-bottom: 30px;
    }
    .form-title i {
      margin-right: 8px;
      color: #0d6efd;
    }
    .select2-container--default .select2-selection--single {
      height: 38px;
      padding: 6px 12px;
    }
  </style>
</head>
<body>

<div class="card bg-white">
  <div class="form-title">
    <i class="bi bi-person-badge"></i> Submit Trainer Profile
  </div>
  <form method="POST" action="trainer_submit.php" enctype="multipart/form-data">

    <div class="row mb-3">
            
        <div class="col-md-6">
            <label class="form-label">First Name</label>
            <input type="text" name="first_name" class="form-control" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Last Name</label>
            <input type="text" name="last_name" class="form-control" required>
        </div>
        
        <div class="col-md-6">
            <label class="form-label">Email</label>
            <input type="email" name="email" class="form-control" placeholder="your@email.com" required>
        </div>

        <div class="col-md-6">
            <label class="form-label">Phone Number</label>
            <input type="tel" name="phone" class="form-control" placeholder="+371 12345678" required>
        </div>
    

      <div class="col-md-6">
        <label class="form-label">City</label>
        <input type="text" id="autocomplete" name="city" class="form-control" placeholder="Start typing city..." required>
        <input type="hidden" name="country" id="country">

      </div>
    

        <!-- остальная форма -->

      <div class="col-md-6">
        <label class="form-label">Experience (years)</label>
        <input type="number" name="experience" class="form-control" min="0" max="70" required>
      </div>

      <div class="mb-4">
        <label class="form-label d-block">Profile Photo</label>
        <input type="file" name="image" id="imageInput" class="form-control mb-2" accept="image/*" required>
        <img id="previewImage" src="#" alt="Preview" class="img-fluid rounded shadow" style="max-width: 200px; display: none;">

        <div class="form-text text-muted">
                    Max 20 images, total size must not exceed 40MB.
        </div>
      </div>

      <div class="col-md-12">
        <label class="form-label d-block">Availability</label>
        <div class="btn-group" role="group" aria-label="Availability">
            <input type="radio" class="btn-check" name="availability" id="availability1" value="Online" autocomplete="off" required>
            <label class="btn btn-outline-success" for="availability1">Online</label>

            <input type="radio" class="btn-check" name="availability" id="availability2" value="Offline" autocomplete="off">
            <label class="btn btn-outline-success" for="availability2">Offline</label>

            <input type="radio" class="btn-check" name="availability" id="availability3" value="Both" autocomplete="off">
            <label class="btn btn-outline-success" for="availability3">Both</label>
        </div>
      </div>

      <div class="col-md-12">
        <label class="form-label d-block">Client Level</label>
        <div class="btn-group" role="group" aria-label="Client Level">
            <input type="radio" class="btn-check" name="level" id="level1" value="Beginner" autocomplete="off" required>
            <label class="btn btn-outline-primary" for="level1">Beginner</label>

            <input type="radio" class="btn-check" name="level" id="level2" value="Intermediate" autocomplete="off">
            <label class="btn btn-outline-primary" for="level2">Intermediate</label>

            <input type="radio" class="btn-check" name="level" id="level3" value="Advanced" autocomplete="off">
            <label class="btn btn-outline-primary" for="level3">Advanced</label>

            <input type="radio" class="btn-check" name="level" id="level4" value="All levels" autocomplete="off">
            <label class="btn btn-outline-primary" for="level4">All levels</label>
        </div>
      </div>
      
    </div>

    <div class="mb-3">
        <label class="form-label d-block">Specialization</label>
        <div class="d-flex flex-wrap gap-2">
            <input type="checkbox" class="btn-check" name="specialization[]" id="spec1" value="Weight loss">
            <label class="btn btn-outline-secondary" for="spec1">Weight loss</label>

            <input type="checkbox" class="btn-check" name="specialization[]" id="spec2" value="Bodybuilding">
            <label class="btn btn-outline-secondary" for="spec2">Bodybuilding</label>

            <input type="checkbox" class="btn-check" name="specialization[]" id="spec3" value="Powerlifting">
            <label class="btn btn-outline-secondary" for="spec3">Powerlifting</label>

            <input type="checkbox" class="btn-check" name="specialization[]" id="spec4" value="Posture correction">
            <label class="btn btn-outline-secondary" for="spec4">Posture correction</label>

            <input type="checkbox" class="btn-check" name="specialization[]" id="spec5" value="Rehabilitation">
            <label class="btn btn-outline-secondary" for="spec5">Rehabilitation</label>

            <input type="checkbox" class="btn-check" name="specialization[]" id="spec6" value="Functional training">
            <label class="btn btn-outline-secondary" for="spec6">Functional training</label>

            <input type="checkbox" class="btn-check" name="specialization[]" id="spec7" value="CrossFit">
            <label class="btn btn-outline-secondary" for="spec7">CrossFit</label>

            <input type="checkbox" class="btn-check" name="specialization[]" id="spec8" value="Yoga / Pilates">
            <label class="btn btn-outline-secondary" for="spec8">Yoga / Pilates</label>

            <input type="checkbox" class="btn-check" name="specialization[]" id="spec9" value="Stretching / Mobility">
            <label class="btn btn-outline-secondary" for="spec9">Stretching / Mobility</label>

            <input type="checkbox" class="btn-check" name="specialization[]" id="spec10" value="Nutrition coaching">
            <label class="btn btn-outline-secondary" for="spec10">Nutrition coaching</label>
        </div>
    </div>

    <div class="mb-3">
        <label class="form-label d-block">Languages</label>
        <div class="d-flex flex-wrap gap-2">
            <input type="checkbox" class="btn-check" name="languages[]" id="lang1" value="English">
            <label class="btn btn-outline-secondary" for="lang1">English</label>

            <input type="checkbox" class="btn-check" name="languages[]" id="lang2" value="Latvian">
            <label class="btn btn-outline-secondary" for="lang2">Latvian</label>

            <input type="checkbox" class="btn-check" name="languages[]" id="lang3" value="Russian">
            <label class="btn btn-outline-secondary" for="lang3">Russian</label>

            <input type="checkbox" class="btn-check" name="languages[]" id="lang4" value="German">
            <label class="btn btn-outline-secondary" for="lang4">German</label>

            <input type="checkbox" class="btn-check" name="languages[]" id="lang5" value="Spanish">
            <label class="btn btn-outline-secondary" for="lang5">Spanish</label>

            <input type="checkbox" class="btn-check" name="languages[]" id="lang6" value="French">
            <label class="btn btn-outline-secondary" for="lang6">French</label>

            <input type="checkbox" class="btn-check" name="languages[]" id="lang7" value="Polish">
            <label class="btn btn-outline-secondary" for="lang7">Polish</label>

            <input type="checkbox" class="btn-check" name="languages[]" id="lang8" value="Italian">
            <label class="btn btn-outline-secondary" for="lang8">Italian</label>
        </div>
    </div>


    <div class="mb-3">
        <label class="form-label">Price (in EUR)</label>
        <input type="number" name="price" class="form-control" min="0" step="0.1" placeholder="Enter price in EUR" required>
    </div>


    <div class="mb-3">
      <label class="form-label">About You</label>
      <textarea name="description" class="form-control" rows="4" required></textarea>
    </div>

    <div class="mb-3">
        <label class="form-label d-block">Social Media Links</label>

        <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="checkInstagram" onchange="toggleField('instagramField')">
            <label class="form-check-label" for="checkInstagram">Instagram</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="checkFacebook" onchange="toggleField('facebookField')">
            <label class="form-check-label" for="checkFacebook">Facebook</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="checkYouTube" onchange="toggleField('youtubeField')">
            <label class="form-check-label" for="checkYouTube">YouTube</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="checkTikTok" onchange="toggleField('tiktokField')">
            <label class="form-check-label" for="checkTikTok">TikTok</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="checkbox" id="checkLinkedIn" onchange="toggleField('linkedinField')">
            <label class="form-check-label" for="checkLinkedIn">LinkedIn</label>
        </div>

        <div id="instagramField" class="mt-2" style="display: none;">
            <input type="url" name="social_links[instagram]" class="form-control" placeholder="Instagram URL">
        </div>
        <div id="facebookField" class="mt-2" style="display: none;">
            <input type="url" name="social_links[facebook]" class="form-control" placeholder="Facebook URL">
        </div>
        <div id="youtubeField" class="mt-2" style="display: none;">
            <input type="url" name="social_links[youtube]" class="form-control" placeholder="YouTube URL">
        </div>
        <div id="tiktokField" class="mt-2" style="display: none;">
            <input type="url" name="social_links[tiktok]" class="form-control" placeholder="TikTok URL">
        </div>
        <div id="linkedinField" class="mt-2" style="display: none;">
            <input type="url" name="social_links[linkedin]" class="form-control" placeholder="LinkedIn URL">
        </div>
    </div>

    <div class="text-center">
      <button type="submit" class="btn btn-success px-5 py-2">Submit Profile</button>
    </div>
  </form>
</div>


<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAdfFvIVxr2LFwZq37NDkl0prXsPet6KGM&libraries=places&callback=initAutocomplete" async defer></script>



<script>
function initAutocomplete() {
  const input = document.getElementById('autocomplete');
  if (!input || !(input instanceof HTMLInputElement)) {
    console.error("Autocomplete input not found or invalid.");
    return;
  }

  const autocomplete = new google.maps.places.Autocomplete(input, {
    types: ['(cities)'],
    fields: ['address_components', 'geometry']
  });

  autocomplete.addListener('place_changed', () => {
    const place = autocomplete.getPlace();
    const components = place.address_components || [];

    const cityObj = components.find(c => c.types.includes("locality")) ||
                    components.find(c => c.types.includes("administrative_area_level_1"));
    const countryObj = components.find(c => c.types.includes("country"));

    input.value = cityObj ? cityObj.long_name : '';
    document.getElementById('country').value = countryObj ? countryObj.long_name : '';
  });
}

</script>

<script>
  function toggleField(id) {
    const field = document.getElementById(id);
    field.style.display = field.style.display === 'none' ? 'block' : 'none';
  }
</script>

<script>
  document.getElementById('imageInput').addEventListener('change', function(event) {
    const [file] = event.target.files;
    const preview = document.getElementById('previewImage');
    if (file) {
      preview.src = URL.createObjectURL(file);
      preview.style.display = 'block';
    } else {
      preview.style.display = 'none';
    }
  });
</script>

<?php include 'includes/footer.php'; ?>
</body>
</html>

