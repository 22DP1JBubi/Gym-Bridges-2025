<?php
session_start();

$conn = new mysqli("localhost", "root", "", "gymbridges");
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
mysqli_set_charset($conn, "utf8mb4");

if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die('Invalid recipe ID.');
}

$recipe_id = (int)$_GET['id'];

// Получаем название рецепта
$recipe_name = '';
$stmt = $conn->prepare("SELECT name FROM recipes WHERE id = ?");
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$stmt->bind_result($recipe_name);
$stmt->fetch();
$stmt->close();

if (!$recipe_name) {
    die("Recipe not found.");
}

// Получаем продукты рецепта
$sql = "
SELECT 
    recipe_products.amount_grams,
    products.name,
    products.calories_per_100g,
    products.protein,
    products.fat,
    products.carbs,
    products.fiber
FROM recipe_products
JOIN products ON recipe_products.product_id = products.id
WHERE recipe_products.recipe_id = ?

";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $recipe_id);
$stmt->execute();
$stmt->store_result();
$stmt->bind_result($amount_grams, $name, $calories_per_100g, $protein, $fat, $carbs, $fiber);

$products = [];
$total = ['calories' => 0, 'protein' => 0, 'fat' => 0, 'carbs' => 0, 'fiber' => 0];

while ($stmt->fetch()) {
    $k = $amount_grams / 100;

    $calc = [
        'name' => $name,
        'amount_grams' => $amount_grams,
        'calc_calories' => $calories_per_100g * $k,
        'calc_protein'  => $protein * $k,
        'calc_fat'      => $fat * $k,
        'calc_carbs'    => $carbs * $k,
        'calc_fiber'    => $fiber * $k
    ];

    $total['calories'] += $calc['calc_calories'];
    $total['protein']  += $calc['calc_protein'];
    $total['fat']      += $calc['calc_fat'];
    $total['carbs']    += $calc['calc_carbs'];
    $total['fiber']    += $calc['calc_fiber'];

    $products[] = $calc;
}
$stmt->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($recipe_name) ?> – Recipe Nutrition</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container my-5">
    <div class="card shadow p-4">
        <h2 class="mb-4"><?= htmlspecialchars($recipe_name) ?></h2>
        <h5 class="text-muted">Ingredients:</h5>
        <table class="table table-bordered align-middle mt-3">
            <thead class="table-light">
                <tr>
                    <th>Product</th>
                    <th>Amount (g)</th>
                    <th>Calories</th>
                    <th>Protein (g)</th>
                    <th>Fat (g)</th>
                    <th>Carbs (g)</th>
                    <th>Fiber (g)</th>
                </tr>
            </thead>
            <tbody>
            <?php foreach ($products as $p): ?>
                <tr>
                    <td><?= htmlspecialchars($p['name']) ?></td>
                    <td><?= $p['amount_grams'] ?></td>
                    <td><?= round($p['calc_calories']) ?></td>
                    <td><?= round($p['calc_protein'], 1) ?></td>
                    <td><?= round($p['calc_fat'], 1) ?></td>
                    <td><?= round($p['calc_carbs'], 1) ?></td>
                    <td><?= round($p['calc_fiber'], 1) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot class="table-light fw-bold">
                <tr>
                    <td>Total</td>
                    <td>—</td>
                    <td><?= round($total['calories']) ?></td>
                    <td><?= round($total['protein'], 1) ?></td>
                    <td><?= round($total['fat'], 1) ?></td>
                    <td><?= round($total['carbs'], 1) ?></td>
                    <td><?= round($total['fiber'], 1) ?></td>
                </tr>
            </tfoot>
        </table>
        <p class="text-muted small mt-3">⚠️ Values are approximate. Consult a nutritionist for individual recommendations.</p>
    </div>
</div>
</body>
</html>
