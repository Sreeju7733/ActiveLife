<?php
include 'auth.php';
include 'db/conn.php';

$user_id = $_SESSION['user_id'] ?? null;
if (!$user_id) {
    header('Location: login.php');
    exit;
}

$sql = "SELECT exercise_type, calories_burned, log_date 
        FROM exercise_log 
        WHERE user_id = ? 
        ORDER BY log_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$data_rows = [];
$dates = [];
$calories = [];
$type_counts = [];

while ($row = $result->fetch_assoc()) {
    $data_rows[] = $row;
    $dates[] = $row['log_date'];
    $calories[] = (int)$row['calories_burned'];

    if (!isset($type_counts[$row['exercise_type']])) {
        $type_counts[$row['exercise_type']] = 0;
    }
    $type_counts[$row['exercise_type']]++;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Exercise Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container my-5">
        <h2 class="text-center mb-4">Your Exercise Logs</h2>

        <?php if (count($data_rows) > 0): ?>
            <div class="row mb-5">
                <div class="col-md-8">
                    <h5 class="text-center">Calories Burned Over Time</h5>
                    <canvas id="calorieChart"></canvas>
                </div>
                <div class="col-md-4">
                    <h5 class="text-center">Exercise Type Frequency</h5>
                    <canvas id="exerciseTypeChart"></canvas>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered table-striped">
                    <thead class="table-dark">
                        <tr>
                            <th>Exercise Type</th>
                            <th>Calories Burned</th>
                            <th>Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data_rows as $row): ?>
                            <tr>
                                <td><?= htmlspecialchars($row['exercise_type']) ?></td>
                                <td><?= htmlspecialchars($row['calories_burned']) ?> kcal</td>
                                <td><?= htmlspecialchars($row['log_date']) ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">No exercise logs found!</div>
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

        if (logDates.length > 0) {
            // Line Chart - Calories Burned
            new Chart(document.getElementById('calorieChart'), {
                type: 'line',
                data: {
                    labels: logDates,
                    datasets: [{
                        label: 'Calories Burned',
                        data: caloriesData,
                        borderColor: '#28a745',
                        backgroundColor: 'rgba(40, 167, 69, 0.1)',
                        fill: true,
                        tension: 0.3
                    }]
                },
                options: {
                    responsive: true,
                    scales: { y: { beginAtZero: true } }
                }
            });

            // Bar Chart - Exercise Types
            new Chart(document.getElementById('exerciseTypeChart'), {
                type: 'bar',
                data: {
                    labels: Object.keys(typeCounts),
                    datasets: [{
                        label: 'Times Performed',
                        data: Object.values(typeCounts),
                        backgroundColor: '#007bff'
                    }]
                },
                options: {
                    responsive: true,
                    scales: {
                        y: { beginAtZero: true }
                    }
                }
            });
        }
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
