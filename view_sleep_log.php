<?php
include 'auth.php';  // Ensure the user is authenticated
include 'db/conn.php';  // Database connection

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM sleep_log WHERE user_id = '$user_id' ORDER BY log_date DESC";
$result = $conn->query($sql);

$dates = [];
$sleep_durations = [];
$sleep_qualities = [];

while ($row = $result->fetch_assoc()) {
    $dates[] = $row['log_date'];
    $sleep_durations[] = (float)$row['sleep_duration'];
    $sleep_qualities[] = $row['sleep_quality'];
}

// Reset result pointer for re-use
$result->data_seek(0);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Sleep Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container my-5">
        <h2 class="text-center mb-4">Your Sleep Logs</h2>
<div class="row mb-5">
    <div class="col-md-8">
        <h5 class="text-center">Sleep Duration Over Time</h5>
        <canvas id="durationChart"></canvas>
    </div>
    <div class="col-md-4">
        <h5 class="text-center">Sleep Quality Distribution</h5>
        <canvas id="qualityChart"></canvas>
    </div>
</div>

        <?php if ($result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Date</th>
                            <th>Sleep Duration (hours)</th>
                            <th>Sleep Quality</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['log_date']) ?></td>
                                <td><?= htmlspecialchars($row['sleep_duration']) ?> hours</td>
                                <td><?= htmlspecialchars($row['sleep_quality']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">No sleep logs found!</div>
        <?php endif; ?>

        <div class="text-center mt-4">
            <a href="dashboard.php" class="btn btn-secondary">⬅ Back to Dashboard</a>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const logDates = <?= json_encode(array_reverse($dates)) ?>;
    const sleepDurations = <?= json_encode(array_reverse($sleep_durations)) ?>;
    const sleepQualities = <?= json_encode($sleep_qualities) ?>;

    // Line Chart - Sleep Duration Over Time
    new Chart(document.getElementById('durationChart'), {
        type: 'line',
        data: {
            labels: logDates,
            datasets: [{
                label: 'Sleep Duration (hours)',
                data: sleepDurations,
                borderColor: '#1E88E5',
                backgroundColor: 'rgba(30, 136, 229, 0.2)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: { 
                    beginAtZero: true 
                }
            }
        }
    });

    // Pie Chart - Sleep Quality Distribution
    const qualityCounts = {
        "Good": 0,
        "Average": 0,
        "Poor": 0
    };

    sleepQualities.forEach(quality => {
        if (quality in qualityCounts) {
            qualityCounts[quality]++;
        }
    });

    new Chart(document.getElementById('qualityChart'), {
        type: 'pie',
        data: {
            labels: Object.keys(qualityCounts),
            datasets: [{
                label: 'Sleep Quality Distribution',
                data: Object.values(qualityCounts),
                backgroundColor: ['#4CAF50', '#FFEB3B', '#F44336']
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
