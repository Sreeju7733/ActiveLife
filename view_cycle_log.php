<?php
include 'auth.php';
include 'db/conn.php';

$user_id = $_SESSION['user_id'];
$sql = "SELECT 
            id,
            cycle_start_date, 
            cycle_end_date, 
            duration_days,
            flow_level,
            symptoms, 
            notes,
            mood,
            is_irregular,
            log_date,
            last_updated
        FROM cycle_log 
        WHERE user_id = ? 
        ORDER BY cycle_start_date DESC";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$data_rows = [];

foreach ($result as $row) {
    $data_rows[] = $row;
}

$cycle_dates = array_column($data_rows, 'cycle_start_date');
$durations = array_map(fn($r) => (int)$r['duration_days'], $data_rows);
$flow_levels = array_column($data_rows, 'flow_level');
$moods = array_column($data_rows, 'mood');

$flow_counts = array_count_values($flow_levels);
$mood_counts = array_count_values($moods);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Menstrual Cycle Logs</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .mood-badge {
            font-size: 0.8rem;
            padding: 0.35em 0.65em;
        }
        .mood-very_good { background-color: #4CAF50; }
        .mood-good { background-color: #8BC34A; }
        .mood-neutral { background-color: #FFC107; }
        .mood-bad { background-color: #FF9800; }
        .mood-very_bad { background-color: #F44336; }

        .flow-badge {
            font-size: 0.8rem;
            padding: 0.35em 0.65em;
        }
        .flow-light { background-color: #E1BEE7; color: #000; }
        .flow-medium { background-color: #BA68C8; color: #fff; }
        .flow-heavy { background-color: #9C27B0; color: #fff; }

        .table-responsive {
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
    </style>
</head>
<body class="bg-light">
    <div class="container my-5">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2>Your Menstrual Cycle History</h2>
            <a href="log_cycle.php" class="btn btn-primary">+ Add New Cycle</a>
        </div>

        <div class="row mb-5">
            <div class="col-md-6">
                <h5 class="text-center">Cycle Duration Trend</h5>
                <canvas id="durationChart"></canvas>
            </div>
            <div class="col-md-3">
                <h5 class="text-center">Flow Level Breakdown</h5>
                <canvas id="flowChart"></canvas>
            </div>
            <div class="col-md-3">
                <h5 class="text-center">Mood Distribution</h5>
                <canvas id="moodChart"></canvas>
            </div>
        </div>

        <?php if (count($data_rows) > 0): ?>
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Cycle Dates</th>
                            <th>Duration</th>
                            <th>Flow</th>
                            <th>Mood</th>
                            <th>Irregular</th>
                            <th>Details</th>
                            <th>Logged</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($data_rows as $row): ?>
                            <tr>
                                <td>
                                    <strong><?= htmlspecialchars($row['cycle_start_date']) ?></strong>
                                    <?php if ($row['cycle_end_date']): ?>
                                        <br>to <?= htmlspecialchars($row['cycle_end_date']) ?>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= $row['duration_days'] ? htmlspecialchars($row['duration_days']) . ' days' : 'Ongoing' ?>
                                </td>
                                <td>
                                    <span class="badge flow-<?= htmlspecialchars($row['flow_level']) ?>">
                                        <?= ucfirst(htmlspecialchars($row['flow_level'])) ?>
                                    </span>
                                </td>
                                <td>
                                    <span class="badge mood-<?= htmlspecialchars($row['mood']) ?>">
                                        <?= str_replace('_', ' ', ucfirst(htmlspecialchars($row['mood']))) ?>
                                    </span>
                                </td>
                                <td>
                                    <?= $row['is_irregular'] ? '✅' : '❌' ?>
                                </td>
                                <td>
                                    <?php if ($row['symptoms'] || $row['notes']): ?>
                                        <button class="btn btn-sm btn-outline-info" data-bs-toggle="collapse" 
                                                data-bs-target="#details-<?= $row['id'] ?>">
                                            View Details
                                        </button>
                                        <div id="details-<?= $row['id'] ?>" class="collapse">
                                            <?php if ($row['symptoms']): ?>
                                                <p class="mb-1"><strong>Symptoms:</strong> <?= htmlspecialchars($row['symptoms']) ?></p>
                                            <?php endif; ?>
                                            <?php if ($row['notes']): ?>
                                                <p class="mb-0"><strong>Notes:</strong> <?= htmlspecialchars($row['notes']) ?></p>
                                            <?php endif; ?>
                                        </div>
                                    <?php else: ?>
                                        <span class="text-muted">None</span>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?= date('M j, Y', strtotime($row['log_date'])) ?>
                                    <?php if ($row['last_updated'] != $row['log_date']): ?>
                                        <br><small class="text-muted">(updated <?= date('M j', strtotime($row['last_updated'])) ?>)</small>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center py-4">
                <h4 class="alert-heading">No cycle logs found</h4>
                <p>Start by logging your first menstrual cycle</p>
                <a href="log_cycle.php" class="btn btn-primary">Log Your First Cycle</a>
            </div>
        <?php endif; ?>

        <div class="text-center mt-4">
            <a href="dashboard.php" class="btn btn-outline-secondary">← Back to Dashboard</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const cycleDates = <?= json_encode($cycle_dates) ?>;
        const durations = <?= json_encode($durations) ?>;
        const flowCounts = <?= json_encode($flow_counts) ?>;
        const moodCounts = <?= json_encode($mood_counts) ?>;

        new Chart(document.getElementById('durationChart'), {
            type: 'line',
            data: {
                labels: cycleDates,
                datasets: [{
                    label: 'Duration (days)',
                    data: durations,
                    borderColor: '#0d6efd',
                    backgroundColor: 'rgba(13, 110, 253, 0.2)',
                    fill: true,
                    tension: 0.3
                }]
            },
            options: {
                responsive: true,
                scales: { y: { beginAtZero: true } }
            }
        });

        new Chart(document.getElementById('flowChart'), {
            type: 'bar',
            data: {
                labels: Object.keys(flowCounts),
                datasets: [{
                    label: 'Flow Level Count',
                    data: Object.values(flowCounts),
                    backgroundColor: ['#E1BEE7', '#BA68C8', '#9C27B0'],
                    borderColor: '#000',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                indexAxis: 'y',
                scales: { x: { beginAtZero: true } }
            }
        });

        new Chart(document.getElementById('moodChart'), {
            type: 'pie',
            data: {
                labels: Object.keys(moodCounts).map(m => m.replace('_', ' ')),
                datasets: [{
                    label: 'Mood Distribution',
                    data: Object.values(moodCounts),
                    backgroundColor: ['#4CAF50', '#8BC34A', '#FFC107', '#FF9800', '#F44336']
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
