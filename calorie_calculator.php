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
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Calorie Calculator | GymBridges</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href='https://fonts.googleapis.com/css?family=Anton' rel='stylesheet'>
  <link rel="stylesheet" type="text/css" href="style.css">

  <style>
    .result-box {
      padding: 20px;
      border-radius: 10px;
      margin-top: 20px;
    }
    .section-title {
      font-weight: 600;
      margin-bottom: 10px;
    }
    .form-select, .form-control {
      margin-bottom: 15px;
    }

    .option-group {
        margin-top: 10px;
        margin-bottom: 20px;
    }

    .option-group label {
        font-weight: 600;
        margin-bottom: 8px;
        display: block;
    }

    .option-group .btn-group {
        display: flex;
        gap: 10px;
        flex-wrap: nowrap;
    }

    .option-group .btn {
        flex: 1;
        min-width: 0;
        padding: 6px 12px;
        font-size: 0.875rem;
        border-radius: 8px !important;
    }

    .card {
    border-radius: 15px;
    background-color: #ffffff;
    }

    .gender-btn {
    flex: 1;
    border-radius: 10px !important;
    min-width: 120px;
    font-weight: 500;
    }
    .gender-btn i {
    font-size: 1.1rem;
    }

    /* Увеличенный ползунок */
    input[type="range"] {
    height: 10px;
    background-color: #dee2e6;
    border-radius: 5px;
    appearance: none;
    width: 100%;
    }

    input[type="range"]::-webkit-slider-thumb {
    appearance: none;
    width: 20px;
    height: 20px;
    background-color: #198754;
    border-radius: 50%;
    cursor: pointer;
    transition: background 0.3s ease;
    }

    input[type="range"]::-moz-range-thumb {
    width: 20px;
    height: 20px;
    background-color: #198754;
    border-radius: 50%;
    cursor: pointer;
    }

    /* Заголовки */
    .form-label {
    font-size: 1.1rem;
    font-weight: 600;
    }

    #activityLabel {
    font-size: 1rem;
    }

    #activityDescription {
    font-size: 0.95rem;
    }

    .alert-info {
    font-size: 0.95rem;
    }

    /* Кнопка расчёта */
    button[type="submit"] {
    padding: 12px 30px;
    font-size: 1.1rem;
    }

    /* Результаты */
    .result-box {
    font-size: 1rem;
    }


  </style>
</head>
<body>
<div class="container py-4">
<div class="card shadow p-4">
  <h1 class="mb-4 text-center">
    <i class="bi bi-calculator me-2 text-primary"></i>
    Calorie Calculator
  </h1>



    <div class="alert alert-info" role="alert">
        This calorie calculator estimates your daily caloric needs based on your age, height, weight, gender, physical activity level, and fitness goal.  
        You can also choose between two popular BMR formulas:  
        <strong>Mifflin-St Jeor</strong> (default, more accurate) and <strong>Harris-Benedict</strong> (classic version).
    </div>


  <form id="calorieForm" class="row g-3 ">
    <div class="row">
        <div class="col-md-6 option-group mt-4">
            <label class="form-label">Gender</label>
            <div id="gender" class="btn-group w-100" role="group">
                <button type="button" class="btn btn-outline-success gender-btn active" data-gender="male">
                <i class="bi bi-gender-male me-1"></i> Male
                </button>
                <button type="button" class="btn btn-outline-danger gender-btn" data-gender="female">
                <i class="bi bi-gender-female me-1"></i> Female
                </button>
            </div>
        </div>

    </div>


    <div class="col-md-4 mt-3">
      <label for="age" class="form-label">Age</label>
      <input type="number" id="age" class="form-control" min="1" max="130" required>
    </div>

    <div class="col-md-4 mt-3">
      <label for="height" class="form-label">Height (cm)</label>
      <input type="number" id="height" class="form-control" min="30" max="250" required>
    </div>

    <div class="col-md-4 mt-3">
      <label for="weight" class="form-label">Weight (kg)</label>
      <input type="number" id="weight" class="form-control" min="10" max="250" required>
    </div>

    <div class="col-md-12 mt-4 mb-3">
        <strong><label for="activityRange" class="form-label">
            Your Daily Activity Level
            <i class="bi bi-info-circle text-primary ms-1" data-bs-toggle="modal" data-bs-target="#activityModal" style="cursor: pointer;"></i>
        </label></strong>

        <input type="range" class="form-range" min="1" max="5" step="1" id="activityRange">
        
        <div id="activityLabel" class="mt-2 fw-bold text-primary">Minimal</div>
        <div id="activityDescription" class="text-muted small">
            Little or no exercise, mostly sedentary lifestyle (desk job, minimal walking).
        </div>
    </div>


    <div class="row">
        <div class="col-md-6 option-group mt-4">
            <label class="form-label">Your goal</label>
            <div id="goal" class="btn-group w-100" role="group">
                <button type="button" class="btn btn-outline-success btn-sm active" data-goal="lose">Lose weight</button>
                <button type="button" class="btn btn-outline-success btn-sm" data-goal="maintain">Maintain</button>
                <button type="button" class="btn btn-outline-success btn-sm" data-goal="gain">Gain</button>
            </div>
        </div>

        <div class="col-md-6 option-group mt-4">
            <label class="form-label">
                Calculation formula:
            <i class="bi bi-info-circle text-primary ms-1" data-bs-toggle="modal" data-bs-target="#formulaModal" style="cursor: pointer;"></i>
            </label>

            <div id="formula" class="btn-group w-100" role="group">
                <button type="button" class="btn btn-outline-primary btn-sm active" data-formula="mifflin">Mifflin-St Jeor</button>
                <button type="button" class="btn btn-outline-primary btn-sm" data-formula="harris">Harris-Benedict</button>
                </div>
        </div>
    </div>




    <div class="col-12 text-center mt-4">
      <button type="submit" class="btn btn-success">Calculate</button>
    </div>
  </form>
</div>

<div id="pdf-diary-content">
  <div id="result" class="d-none">
      <div id="bmiCard" class="card p-4 mt-4 shadow">
          <h4 class="text-center fw-bold mb-4" style="font-size: 1.8rem;">Your Result</h4>
          <div class="d-inline-flex align-items-center">
              <h5 class="mb-0 me-2">Body Mass Index (BMI)</h5>
              <i class="bi bi-info-circle text-primary" data-bs-toggle="modal" data-bs-target="#bmiInfoModal" style="cursor:pointer; font-size: 1rem;"></i>
          </div>



          <div class="text-center my-3">
              <span id="bmiValue" class="fw-bold" style="font-size: 2rem;">--</span>
          </div>

          <div class="position-relative">
              <div class="bmi-scale w-100" style="height: 8px; border-radius: 4px; background: linear-gradient(to right, #4dc0ff 0%, #28a745 25%, #ffc107 50%, #fd7e14 75%, #dc3545 100%);"></div>
              <div id="bmiPointer" class="position-absolute top-0" style="height: 20px; width: 2px; background: black; margin-top: -6px;"></div>
          </div>

          <div class="d-flex justify-content-between small text-muted mt-1 px-1">
              <span>Underweight</span>
              <span>Normal</span>
              <span>Overweight</span>
              <span>Obese</span>
          </div>
      </div>



      <div id="calorieCard" class="card p-4 mt-4 shadow">
          <div class="d-inline-flex align-items-center mb-3">
              <h5 class="mb-0 me-2">Daily Calorie Needs</h5>
              <i class="bi bi-info-circle text-primary" data-bs-toggle="modal" data-bs-target="#calorieInfoModal" style="cursor:pointer; font-size: 1rem;"></i>
          </div>


          <div class="row align-items-center">
              <div class="col-md-5 text-center">
              <div class="position-relative d-inline-block" style="width: 180px; height: 180px;">
                  <canvas id="macroChart" width="180" height="180"></canvas>
                  <div class="position-absolute top-50 start-50 translate-middle text-center">
                  <div class="fw-bold fs-4" id="calories">--</div>
                  <div class="text-muted small">kcal</div>
                  </div>
              </div>
              </div>

              <div class="col-md-7">
              <p id="goalText" class="text-muted small mb-3" style="line-height: 1.4;"></p>
              <ul class="list-unstyled mb-0">
                  <li class="d-flex align-items-center mb-2">
                      <span class="me-2" style="width: 12px; height: 12px; border-radius: 50%; background-color: #00b894; display: inline-block;"></span>
                      <strong class="me-1" id="protein">--</strong><span>g protein</span>
                  </li>
                  <li class="d-flex align-items-center mb-2">
                      <span class="me-2" style="width: 12px; height: 12px; border-radius: 50%; background-color: #fdcb6e; display: inline-block;"></span>
                      <strong class="me-1" id="fat">--</strong><span>g fat</span>
                  </li>
                  <li class="d-flex align-items-center">
                      <span class="me-2" style="width: 12px; height: 12px; border-radius: 50%; background-color: #0984e3; display: inline-block;"></span>
                      <strong class="me-1" id="carbs">--</strong><span>g carbs</span>
                  </li>
              </ul>

              <p class="text-muted small mt-3 mb-0" style="font-size: 0.85rem;">
              * This is a general estimate. Results may vary depending on individual metabolism, health conditions, and activity level.  
              For medical advice or concerns, please consult a certified healthcare professional.
              </p>
          </div>
          <div class="text-end mb-3">
            <button class="btn btn-danger" onclick="generatePDF()">
                <i class="bi bi-file-earmark-pdf-fill me-1"></i> Download Diary as PDF
            </button>
          </div>


        </div>
      </div>
  </div>
</div>



<div class="modal fade" id="formulaModal" tabindex="-1" aria-labelledby="formulaModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="formulaModalLabel">BMR Formula Details</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>
          Basal Metabolic Rate (BMR) represents the number of calories your body needs to maintain basic life functions
          (breathing, heartbeat, etc.) at rest. Accurate BMR estimation is the foundation of calorie calculation.
        </p>

        <hr>
        <h6>🔹 Mifflin-St Jeor Equation (1990, more accurate)</h6>
        <p>This is currently considered the most reliable BMR formula for modern populations.</p>
        <ul>
          <li><strong>Men:</strong> BMR = <code>10 × weight (kg) + 6.25 × height (cm) − 5 × age + 5</code></li>
          <li><strong>Women:</strong> BMR = <code>10 × weight (kg) + 6.25 × height (cm) − 5 × age − 161</code></li>
        </ul>

        <p><em>Recommended if you're of average body fat and want better accuracy.</em></p>

        <hr>
        <h6>🔹 Harris-Benedict Equation (1919, updated in 1984)</h6>
        <p>Older and slightly less accurate for modern body compositions. Originally based on studies in the early 20th century.</p>
        <ul>
          <li><strong>Men:</strong> BMR = <code>88.36 + (13.4 × weight) + (4.8 × height) − (5.7 × age)</code></li>
          <li><strong>Women:</strong> BMR = <code>447.6 + (9.2 × weight) + (3.1 × height) − (4.3 × age)</code></li>
        </ul>

        <p><em>Still used in clinical practice, but tends to slightly overestimate calorie needs.</em></p>

        <hr>
        <h6>💡 When to use which:</h6>
        <ul>
          <li><strong>Mifflin-St Jeor:</strong> Most accurate for general use, athletes, and weight planning.</li>
          <li><strong>Harris-Benedict:</strong> Useful for comparison or if following older calorie planning models.</li>
        </ul>

        <p class="mt-3">
          <strong>Note:</strong> Both formulas provide an estimate of your resting metabolic needs. Your total daily calorie requirement is calculated by multiplying BMR with an activity factor depending on your lifestyle.
        </p>
      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="activityModal" tabindex="-1" aria-labelledby="activityModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="activityModalLabel">How to Choose Your Daily Activity Level</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>
          Your daily activity level helps estimate how many calories you burn throughout the day. It includes both exercise and your general lifestyle (e.g. work, chores, walking).
        </p>

        <hr>
        <p><strong>🔴 Minimal (1.2):</strong> Sedentary lifestyle. Little or no physical activity. You spend most of your day sitting (desk job, studying, driving).</p>

        <p><strong>🟠 Low (1.375):</strong> Light activity 1–3 times per week. Occasional walking, yoga, or light workouts. Mostly sedentary but includes some movement.</p>

        <p><strong>🟢 Moderate (1.55):</strong> Moderate exercise 3–5 times per week. Regular gym visits, cardio, or sports. Daily movement is balanced.</p>

        <p><strong>🔵 High (1.725):</strong> Intense training 6–7 days per week or physically active job (e.g. construction, trainer, warehouse worker).</p>

        <p><strong>🟣 Very High (1.9):</strong> Professional athlete level. Training twice a day or extreme physical labor. Includes military training or elite sports.</p>

        <hr>
        <p class="mt-3"><strong>💡 Tip:</strong> If you're unsure — choose a lower level. It's better to underestimate slightly than to overeat.</p>
      </div>
    </div>
  </div>
</div>



<div class="modal fade" id="bmiInfoModal" tabindex="-1" aria-labelledby="bmiInfoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="bmiInfoModalLabel">What is BMI (Body Mass Index)?</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">

        <p>
          <strong>BMI (Body Mass Index)</strong> is a standardized metric that estimates whether your weight is appropriate for your height. It's commonly used to assess body fat and potential health risks.
        </p>

        <hr>
        <h6>🔹 BMI Categories</h6>
        <ul>
          <li><strong>Underweight (BMI &lt; 18.5):</strong> Possible nutritional deficiency, weakened immunity, fatigue.</li>
          <li><strong>Normal (BMI 18.5 – 24.9):</strong> Healthy weight range. Balanced metabolism and lower health risks.</li>
          <li><strong>Overweight (BMI 25 – 29.9):</strong> Slightly elevated risk of cardiovascular issues, joint stress, and metabolic syndrome.</li>
          <li><strong>Obese (BMI ≥ 30):</strong> Higher risk of heart disease, diabetes, hypertension, and other chronic conditions.</li>
        </ul>

        <hr>
        <h6>🧮 Formula</h6>
        <p>
          BMI is calculated as:<br>
          <code>BMI = weight (kg) / [height (m)]²</code>
        </p>

        <hr>
        <h6>💡 Notes</h6>
        <ul>
          <li>BMI does not differentiate between fat and muscle. Athletes may have high BMI due to muscle mass.</li>
          <li>It is a general guideline — not a diagnostic tool.</li>
          <li>Children, elderly people, and pregnant women should not rely solely on BMI.</li>
        </ul>

        <p class="mt-3 text-muted">
          For more accurate health assessment, consult with a medical professional and consider additional metrics like body fat %, waist-to-hip ratio, or metabolic health indicators.
        </p>

      </div>
    </div>
  </div>
</div>


<div class="modal fade" id="calorieInfoModal" tabindex="-1" aria-labelledby="calorieInfoModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="calorieInfoModalLabel">Understanding Daily Calorie Needs</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <p>
          Your daily calorie needs represent the total number of calories your body requires in a 24-hour period to perform all essential functions — including breathing, digesting food, moving, and even sleeping.
        </p>

        <hr>

        <h6>🔹 Components of Caloric Intake</h6>
        <ul>
          <li><strong>Protein:</strong> Essential for muscle repair, hormone production, and immune function. Especially important during weight loss or strength training.</li>
          <li><strong>Fat:</strong> Supports hormone health, brain function, and nutrient absorption. Healthy fats are crucial — don’t avoid them entirely.</li>
          <li><strong>Carbohydrates:</strong> Your body’s primary source of energy. Fuels workouts, brain activity, and metabolic processes.</li>
        </ul>

        <hr>

        <h6>🔹 Macronutrient Split</h6>
        <p>We use the following general guideline:</p>
        <ul>
          <li>💪 <strong>Protein:</strong> ~20% of total calories</li>
          <li>🥑 <strong>Fat:</strong> ~30% of total calories</li>
          <li>🍞 <strong>Carbs:</strong> ~50% of total calories</li>
        </ul>
        <p class="text-muted small">* These can be adjusted depending on your specific goal (e.g. low-carb, high-protein, keto, etc.)</p>

        <hr>

        <h6>📊 How to Use This Info</h6>
        <p>
          - If you're trying to <strong>lose weight</strong>, aim for a calorie deficit of 10–20% from your maintenance level.<br>
          - If you're aiming to <strong>gain muscle</strong>, a slight surplus (+10–15%) is recommended.<br>
          - For <strong>maintenance</strong>, consume roughly the amount shown here daily.
        </p>

        <p class="mt-3">
          Always pair calorie tracking with balanced nutrition, physical activity, and adequate rest. Consistency is key!
        </p>
      </div>
    </div>
  </div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>


<script>
  document.getElementById('calorieForm').addEventListener('submit', function (e) {
    e.preventDefault();

    const gender = document.querySelector('#gender .active').dataset.gender;
    const age = parseInt(document.getElementById('age').value);
    const height = parseFloat(document.getElementById('height').value);
    const weight = parseFloat(document.getElementById('weight').value);
    const activity = parseFloat(activityDescriptions[activityRange.value].value);

    const goal = document.querySelector('#goal .active').dataset.goal;


    // BMI
    const bmi = (weight / ((height / 100) ** 2)).toFixed(1);

    // Установка значения BMI
    document.getElementById('bmiValue').textContent = bmi;

    // Перемещение маркера по шкале
    const bmiFloat = parseFloat(bmi);
    const bmiMin = 12;
    const bmiMax = 40;
    const percent = Math.min(Math.max((bmiFloat - bmiMin) / (bmiMax - bmiMin), 0), 1) * 100;
    document.getElementById('bmiPointer').style.left = `calc(${percent}% - 1px)`;


    let bmiCategory = "";
    if (bmi < 18.5) bmiCategory = "Underweight";
    else if (bmi < 25) bmiCategory = "Normal";
    else if (bmi < 30) bmiCategory = "Overweight";
    else bmiCategory = "Obese";

    // BMR — Mifflin-St Jeor

    let bmr;
    const formula = document.querySelector('#formula .active').dataset.formula;
    if (formula === 'mifflin') {
    if (gender === 'male') {
        bmr = 10 * weight + 6.25 * height - 5 * age + 5;
    } else {
        bmr = 10 * weight + 6.25 * height - 5 * age - 161;
    }
    } else if (formula === 'harris') {
    if (gender === 'male') {
        bmr = 88.36 + (13.4 * weight) + (4.8 * height) - (5.7 * age);
    } else {
        bmr = 447.6 + (9.2 * weight) + (3.1 * height) - (4.3 * age);
    }
    }

    let totalCalories = bmr * activity;

    if (goal === 'lose') totalCalories *= 0.85;
    if (goal === 'gain') totalCalories *= 1.15;

    // Macros
    const protein = (totalCalories * 0.20 / 4).toFixed(1);
    const fat = (totalCalories * 0.30 / 9).toFixed(1);
    const carbs = (totalCalories * 0.50 / 4).toFixed(1);

    // Output
    // Эта строка уже есть выше и правильная:
    document.getElementById('bmiValue').textContent = bmi;
    document.getElementById('calories').textContent = Math.round(totalCalories);
    document.getElementById('protein').textContent = protein;
    document.getElementById('fat').textContent = fat;
    document.getElementById('carbs').textContent = carbs;
    document.getElementById('result').classList.remove('d-none');


    // Удаляем старую диаграмму, если уже была создана
    if (window.macroChart && typeof window.macroChart.destroy === 'function') {
    window.macroChart.destroy();
    }

    // Создаём новую диаграмму
    window.macroChart = new Chart(document.getElementById('macroChart'), {
    type: 'doughnut',
    data: {
        labels: ['Protein', 'Fat', 'Carbs'],
        datasets: [{
        data: [protein, fat, carbs],
        backgroundColor: ['#00b894', '#fdcb6e', '#0984e3'],
        borderWidth: 1
        }]
    },
    options: {
        cutout: '70%',
        plugins: { legend: { display: false } },
        responsive: false
    }
    });


    const goalDescription = {
        lose: "To lose weight, you need approximately",
        maintain: "To maintain your current weight, you need approximately",
        gain: "To gain weight, you need approximately"
    };

    document.getElementById('goalText').textContent =
        `${goalDescription[goal]} ${Math.round(totalCalories)} kcal daily, consisting of:`;



  });

</script>


<script>
    const activityDescriptions = {
    1: {
        label: "Minimal",
        value: 1.2,
        description: "Little or no exercise, mostly sedentary lifestyle (desk job, minimal walking)."
    },
    2: {
        label: "Low",
        value: 1.375,
        description: "Light exercise 1–3 times per week (short walks, yoga, light cycling)."
    },
    3: {
        label: "Moderate",
        value: 1.55,
        description: "Moderate exercise 3–5 times per week (gym, dancing, running)."
    },
    4: {
        label: "High",
        value: 1.725,
        description: "Daily intense training (weightlifting, crossfit, sports)."
    },
    5: {
        label: "Very High",
        value: 1.9,
        description: "Professional athlete level: multiple intense sessions per day."
    }
    };

    const activityRange = document.getElementById("activityRange");
    const activityLabel = document.getElementById("activityLabel");
    const activityDescription = document.getElementById("activityDescription");

    activityRange.addEventListener("input", () => {
    const val = activityRange.value;
    const info = activityDescriptions[val];
    activityLabel.textContent = info.label;
    activityDescription.textContent = info.description;
    });




    function updateActivityDisplay(val) {
        const info = activityDescriptions[val];
        activityLabel.textContent = info.label;
        activityDescription.textContent = info.description;
    }

    // Сразу при загрузке:
    updateActivityDisplay(activityRange.value);

    // При изменении:
    activityRange.addEventListener("input", () => {
        updateActivityDisplay(activityRange.value);
    });

</script>

<script>
    document.querySelectorAll('#goal .btn').forEach(btn =>
        btn.addEventListener('click', () => {
            document.querySelectorAll('#goal .btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        })
    );

    document.querySelectorAll('#formula .btn').forEach(btn =>
        btn.addEventListener('click', () => {
            document.querySelectorAll('#formula .btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        })
    );


    document.querySelectorAll('#gender .btn').forEach(btn =>
        btn.addEventListener('click', () => {
            document.querySelectorAll('#gender .btn').forEach(b => b.classList.remove('active'));
            btn.classList.add('active');
        })
    );

</script>

<script>
async function generatePDF() {
    if (!window.jspdf || !window.jspdf.jsPDF) {
        alert("jsPDF not loaded");
        return;
    }

    const { jsPDF } = window.jspdf;
    const diaryElement = document.getElementById("pdf-diary-content");
    const nickname = "<?= $_SESSION['username'] ?? 'User' ?>";

    const now = new Date();
    const date = `${String(now.getDate()).padStart(2, '0')}.${String(now.getMonth() + 1).padStart(2, '0')}.${now.getFullYear()}`;

    if (!diaryElement) {
        alert("Report not found");
        return;
    }

    // Элементы, которые надо временно скрыть
    const downloadButton = diaryElement.querySelector("button");
    const shadows = diaryElement.querySelectorAll(".shadow");

    // Скрыть кнопку и убрать тень перед рендером
    downloadButton.style.display = "none";
    shadows.forEach(el => el.classList.remove("shadow"));

    // Ждём отрисовки без этих элементов
    const canvas = await html2canvas(diaryElement, { scale: 2 });

    // Вернуть кнопку и тени
    downloadButton.style.display = "inline-block"; // или "block", если так было
    shadows.forEach(el => el.classList.add("shadow"));

    // Генерация PDF
    const pdf = new jsPDF('p', 'mm', 'a4');
    const img = new Image();
    img.src = 'images/logo_black_2.png';
    await img.decode();

    const imgData = canvas.toDataURL('image/png');
    const pageWidth = pdf.internal.pageSize.getWidth();
    const imgWidth = pageWidth - 20;
    const imgHeight = imgWidth * (canvas.height / canvas.width);

    const logoWidth = 35; // желаемая ширина
    const aspectRatio = img.height / img.width;
    const logoHeight = logoWidth * aspectRatio;

    pdf.addImage(img, 'PNG', 10, 10, logoWidth, logoHeight);


    // заголовок
    pdf.setFontSize(16);
    pdf.setFont("helvetica", "bold");
    pdf.text(`Calorie Calculator Report for ${nickname}`, 105, 30, { align: "center" });

    // дата
    pdf.setFontSize(10);
    pdf.setFont("helvetica", "normal");
    pdf.text(`Generated on: ${date}`, 105, 36, { align: "center" });

    // содержимое
    pdf.addImage(imgData, 'PNG', 10, 45, imgWidth, imgHeight);

    pdf.save('calorie_report.pdf');
}


</script>

<?php include 'includes/footer.php'; ?>
</body>
</html>
