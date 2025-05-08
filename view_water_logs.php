<?php
include 'auth.php';  // Ensure the user is authenticated
include 'db/conn.php';  // Database connection

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header('Location: login.php');
    exit;
}

// Fetch water intake logs from the database
$sql = "SELECT water_intake, log_date FROM water_log WHERE user_id = '$user_id' ORDER BY log_date DESC";
$result = $conn->query($sql);

$dates = [];
$water_intakes = [];
$logs = [];

while ($row = $result->fetch_assoc()) {
    $dates[] = $row['log_date'];
    $water_intakes[] = (int)$row['water_intake'];
    $logs[] = $row; // Store for later use in table
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Water Intake Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container my-5">
        <h2 class="text-center mb-4">Your Water Intake Logs</h2>

        <div class="row mb-5 justify-content-center">
            <div class="col-md-9">
                <h5 class="text-center mb-3">Water Intake Over Time</h5>
                <canvas id="waterIntakeChart"></canvas>
            </div>
        </div>

        <?php if (count($logs) > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Water Intake (ml)</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($logs as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['water_intake']) ?> ml</td>
                                <td><?= htmlspecialchars($row['log_date']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">No water intake logs found!</div>
        <?php endif; ?>

        <div class="text-center mt-4">
            <a href="dashboard.php" class="btn btn-secondary">⬅ Back to Dashboard</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const logDates = <?= json_encode(array_reverse($dates)) ?>;
        const waterIntakes = <?= json_encode(array_reverse($water_intakes)) ?>;

        new Chart(document.getElementById('waterIntakeChart'), {
            type: 'line',
            data: {
                labels: logDates,
                datasets: [{
                    label: 'Water Intake (ml)',
                    data: waterIntakes,
                    borderColor: '#0D6EFD',
                    backgroundColor: 'rgba(13, 110, 253, 0.2)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        title: {
                            display: true,
                            text: 'Water Intake (ml)'
                        }
                    },
                    x: {
                        title: {
                            display: true,
                            text: 'Date'
                        }
                    }
                }
            }
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
