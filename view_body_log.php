<?php
include 'auth.php';  // Ensure the user is authenticated
include 'db/conn.php';  // Database connection

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM body_log WHERE user_id = '$user_id' ORDER BY log_date ASC";
$result = $conn->query($sql);

// Prepare data for charts
$dates = [];
$bmis = [];
$bmrs = [];
$calories = [];

while ($row = $result->fetch_assoc()) {
    $dates[] = $row['log_date'];
    $bmis[] = $row['bmi'];
    $bmrs[] = $row['bmr'];
    $calories[] = $row['caloric_use'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Body Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">
    <div class="container my-5">
        <h2 class="mb-4 text-center">Your Body Logs</h2>

        <?php if (count($dates) > 0): ?>
            <!-- Chart Section -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <canvas id="bmiChart"></canvas>
                </div>
                <div class="col-md-4">
                    <canvas id="bmrChart"></canvas>
                </div>
                <div class="col-md-4">
                    <canvas id="calChart"></canvas>
                </div>
            </div>

            <!-- Table Section -->
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Date</th>
                            <th>BMI</th>
                            <th>BMR</th>
                            <th>Caloric Use (kcal)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dates as $i => $date): ?>
                            <tr>
                                <td><?= htmlspecialchars($date) ?></td>
                                <td><?= htmlspecialchars($bmis[$i]) ?></td>
                                <td><?= htmlspecialchars($bmrs[$i]) ?></td>
                                <td><?= htmlspecialchars($calories[$i]) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">No body logs found.</div>
        <?php endif; ?>

        <div class="text-center mt-4">
            <a href="dashboard.php" class="btn btn-secondary">⬅ Back to Dashboard</a>
        </div>
    </div>

    <!-- Chart JS -->
    <script>
        const labels = <?= json_encode($dates) ?>;
        const bmiData = <?= json_encode($bmis) ?>;
        const bmrData = <?= json_encode($bmrs) ?>;
        const calData = <?= json_encode($calories) ?>;

        new Chart(document.getElementById('bmiChart'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'BMI',
                    data: bmiData,
                    borderColor: '#f97316',
                    backgroundColor: 'rgba(249, 115, 22, 0.2)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: false } } }
        });

        new Chart(document.getElementById('bmrChart'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'BMR',
                    data: bmrData,
                    borderColor: '#6366f1',
                    backgroundColor: 'rgba(99, 102, 241, 0.2)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: false } } }
        });

        new Chart(document.getElementById('calChart'), {
            type: 'line',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Caloric Use (kcal)',
                    data: calData,
                    borderColor: '#10b981',
                    backgroundColor: 'rgba(16, 185, 129, 0.2)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: { responsive: true, scales: { y: { beginAtZero: false } } }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
