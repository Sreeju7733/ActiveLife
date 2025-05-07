<?php
include 'auth.php';  // Ensure the user is authenticated
include 'db/conn.php';  // Database connection

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM blood_log WHERE user_id = '$user_id' ORDER BY log_date ASC";
$result = $conn->query($sql);

// Prepare data for charts
$dates = [];
$pressures = [];
$glucose = [];

while ($row = $result->fetch_assoc()) {
    $dates[] = $row['log_date'];
    $pressures[] = $row['blood_pressure'];
    $glucose[] = $row['blood_glucose'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Blood Pressure & Glucose Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body class="bg-light">
<div class="container my-5">
    <h2 class="mb-4 text-center">Your Blood Pressure & Glucose Logs</h2>

    <?php if (count($dates) > 0): ?>
        <!-- Chart Section -->
        <div class="row mb-5">
            <div class="col-md-6">
                <canvas id="bpChart"></canvas>
            </div>
            <div class="col-md-6">
                <canvas id="glucoseChart"></canvas>
            </div>
        </div>

        <!-- Table Section -->
        <div class="table-responsive">
            <table class="table table-striped table-bordered">
                <thead class="table-dark">
                    <tr>
                        <th>Date</th>
                        <th>Blood Pressure</th>
                        <th>Blood Glucose (mg/dL)</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($dates as $index => $date): ?>
                        <tr>
                            <td><?= htmlspecialchars($date) ?></td>
                            <td><?= htmlspecialchars($pressures[$index]) ?></td>
                            <td><?= htmlspecialchars($glucose[$index]) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <div class="alert alert-info text-center">No blood pressure and glucose logs found.</div>
    <?php endif; ?>

    <div class="text-center mt-4">
        <a href="dashboard.php" class="btn btn-secondary">⬅ Back to Dashboard</a>
    </div>
</div>

<!-- Chart Script -->
<script>
    const dates = <?= json_encode($dates) ?>;
    const pressures = <?= json_encode($pressures) ?>;
    const glucose = <?= json_encode($glucose) ?>;

    new Chart(document.getElementById('bpChart'), {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: 'Blood Pressure',
                data: pressures,
                borderColor: 'rgba(255,99,132,1)',
                backgroundColor: 'rgba(255,99,132,0.2)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: false }
            }
        }
    });

    new Chart(document.getElementById('glucoseChart'), {
        type: 'line',
        data: {
            labels: dates,
            datasets: [{
                label: 'Blood Glucose (mg/dL)',
                data: glucose,
                borderColor: 'rgba(54,162,235,1)',
                backgroundColor: 'rgba(54,162,235,0.2)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { beginAtZero: false }
            }
        }
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
