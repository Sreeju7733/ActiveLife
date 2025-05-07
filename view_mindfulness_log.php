<?php
include 'auth.php';  // Ensure the user is authenticated
include 'db/conn.php';  // Database connection

$user_id = $_SESSION['user_id'];
$sql = "SELECT * FROM mindfulness_log WHERE user_id = '$user_id' ORDER BY log_date DESC";
$result = $conn->query($sql);

$dates = [];
$durations = [];
$activity_counts = [];

while ($row = $result->fetch_assoc()) {
    $dates[] = $row['log_date'];
    $durations[] = (int)$row['duration'];
    $activity_type = $row['activity_type'];

    if (!isset($activity_counts[$activity_type])) {
        $activity_counts[$activity_type] = 0;
    }
    $activity_counts[$activity_type]++;
}

// Reset result pointer for re-use
$result->data_seek(0);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Mindfulness Activity Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container my-5">
        <h2 class="text-center mb-4">Your Mindfulness Activity Logs</h2>
<div class="row mb-5">
    <div class="col-md-8">
        <h5 class="text-center">Mindfulness Duration Over Time</h5>
        <canvas id="durationChart"></canvas>
    </div>
    <div class="col-md-4">
        <h5 class="text-center">Activity Type Distribution</h5>
        <canvas id="activityChart"></canvas>
    </div>
</div>

        <?php if ($result->num_rows > 0): ?>
            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Date</th>
                            <th>Activity Type</th>
                            <th>Duration (min)</th>
                            <th>Notes</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['log_date']) ?></td>
                                <td><?= htmlspecialchars($row['activity_type']) ?></td>
                                <td><?= htmlspecialchars($row['duration']) ?> min</td>
                                <td><?= htmlspecialchars($row['notes']) ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">No mindfulness logs found!</div>
        <?php endif; ?>

        <div class="text-center mt-4">
            <a href="dashboard.php" class="btn btn-secondary">⬅ Back to Dashboard</a>
        </div>
    </div>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const logDates = <?= json_encode(array_reverse($dates)) ?>;
    const durations = <?= json_encode(array_reverse($durations)) ?>;
    const activityCounts = <?= json_encode($activity_counts) ?>;

    // Line Chart - Mindfulness Duration Over Time
    new Chart(document.getElementById('durationChart'), {
        type: 'line',
        data: {
            labels: logDates,
            datasets: [{
                label: 'Mindfulness Duration (min)',
                data: durations,
                borderColor: '#4CAF50',
                backgroundColor: 'rgba(76, 175, 80, 0.2)',
                fill: true,
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            scales: { y: { beginAtZero: true } }
        }
    });

    // Pie Chart - Activity Type Distribution
    new Chart(document.getElementById('activityChart'), {
        type: 'pie',
        data: {
            labels: Object.keys(activityCounts),
            datasets: [{
                label: 'Activity Type Distribution',
                data: Object.values(activityCounts),
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
