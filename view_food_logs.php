<?php
include 'auth.php';
include 'db/conn.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header('Location: login.php');
    exit;
}

$sql = "SELECT food_item, calories, log_date FROM food_log WHERE user_id = '$user_id' ORDER BY log_date DESC";
$result = $conn->query($sql);

$dates = [];
$calories = [];
$food_items = [];

$type_counts = [];

while ($row = $result->fetch_assoc()) {
    $dates[] = $row['log_date'];
    $calories[] = (int)$row['calories'];
    $food_items[] = $row['food_item'];

    if (!isset($type_counts[$row['food_item']])) {
        $type_counts[$row['food_item']] = 0;
    }
    $type_counts[$row['food_item']]++;
}

// Reset result pointer so you can reuse the result set
$result->data_seek(0);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Food Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container my-5">
        <h2 class="text-center mb-4">Your Food Logs</h2>
<div class="row mb-5">
    <div class="col-md-8">
        <h5 class="text-center">Calories Consumed Over Time</h5>
        <canvas id="calorieChart"></canvas>
    </div>
    <div class="col-md-4">
        <h5 class="text-center">Food Item Frequency</h5>
        <canvas id="foodItemChart"></canvas>
    </div>
</div>

        <?php if ($result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Food Item</th>
                            <th>Calories</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['food_item']) ?></td>
                                <td><?= htmlspecialchars($row['calories']) ?> kcal</td>
                                <td><?= htmlspecialchars($row['log_date']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">No food logs found!</div>
        <?php endif; ?>

        <div class="text-center mt-4">
            <a href="dashboard.php" class="btn btn-secondary">⬅ Back to Dashboard</a>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const logDates = <?= json_encode(array_reverse($dates)) ?>;
    const caloriesData = <?= json_encode(array_reverse($calories)) ?>;
    const typeCounts = <?= json_encode($type_counts) ?>;

    // Line Chart - Calories Consumed
    new Chart(document.getElementById('calorieChart'), {
        type: 'line',
        data: {
            labels: logDates,
            datasets: [{
                label: 'Calories Consumed',
                data: caloriesData,
                borderColor: '#FF5733',
                backgroundColor: 'rgba(255, 87, 51, 0.1)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true } }
        }
    });

    // Pie Chart - Food Item Frequency
    new Chart(document.getElementById('foodItemChart'), {
        type: 'pie',
        data: {
            labels: Object.keys(typeCounts),
            datasets: [{
                label: 'Food Item Frequency',
                data: Object.values(typeCounts),
                backgroundColor: [
                    '#FF5733', '#33FF57', '#3357FF', '#F1C40F', '#9B59B6'
                ]
            }]
        },
        options: {
            responsive: true
        }
    });
</script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
