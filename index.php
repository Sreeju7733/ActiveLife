<?php
session_start();
include 'db/conn.php';

// Check authentication
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Get user details from database
$user_id = $_SESSION['user_id'];

$user_sql = "SELECT username, email FROM users WHERE id = ?";
$stmt = $conn->prepare($user_sql);

if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param("i", $user_id);
$stmt->execute();

$user_result = $stmt->get_result();

if ($user_result && $user_result->num_rows > 0) {
    $user_data = $user_result->fetch_assoc();
    $user_name = $user_data['username'] ?? 'User';
    $user_email = $user_data['email'] ?? '';
} else {
    $user_name = 'User';
    $user_email = '';
}

// Fetch dashboard data
$today = date('Y-m-d');

// Calories
$calories_sql = "SELECT SUM(calories) AS total FROM food_log WHERE user_id = ? AND log_date = ?";
$stmt = $conn->prepare($calories_sql);
$stmt->bind_param("is", $user_id, $today);
$stmt->execute();
$calories_result = $stmt->get_result();
$calories_data = $calories_result->fetch_assoc();
$total_calories_consumed = $calories_data['total'] ?? 0;

// Exercise
$exercise_sql = "SELECT SUM(calories_burned) AS total FROM exercise_log WHERE user_id = ? AND log_date = ?";
$stmt = $conn->prepare($exercise_sql);
$stmt->bind_param("is", $user_id, $today);
$stmt->execute();
$exercise_result = $stmt->get_result();
$exercise_data = $exercise_result->fetch_assoc();
$total_calories_burned = $exercise_data['total'] ?? 0;

// Water
$today = date('Y-m-d'); // Make sure today’s date is set
$user_id = intval($_SESSION['user_id']); // Basic sanitation
$water_sql = "SELECT SUM(water_intake) AS total FROM water_log WHERE user_id = $user_id AND log_date = '$today'";

$water_result = $conn->query($water_sql);

if (!$water_result) {
    die("Query failed: " . $conn->error);
}

$water_data = $water_result->fetch_assoc();
$total_water_intake = $water_data['total'] ?? 0;
$target_water_intake = 2000; // 2L target


// Mindfulness
$mindfulness_sql = "SELECT activity_type, duration FROM mindfulness_log WHERE user_id = ? AND log_date = ?";
$stmt = $conn->prepare($mindfulness_sql);
$stmt->bind_param("is", $user_id, $today);
$stmt->execute();
$mindfulness_result = $stmt->get_result();
$mindfulness_minutes = 0;
$mindfulness_activities = [];
while ($row = $mindfulness_result->fetch_assoc()) {
    $mindfulness_minutes += $row['duration'];
    $mindfulness_activities[] = $row['activity_type'] . " ({$row['duration']} mins)";
}

// Body metrics
$body_sql = "SELECT bmi, bmr, caloric_use FROM body_log WHERE user_id = ? ORDER BY log_date DESC LIMIT 1";
$stmt = $conn->prepare($body_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$body_result = $stmt->get_result();
$body_data = $body_result->fetch_assoc();
$bmi = $body_data['bmi'] ?? 'N/A';
$bmr = $body_data['bmr'] ?? 'N/A';
$caloric_use = $body_data['caloric_use'] ?? 'N/A';

// Blood metrics
$blood_sql = "SELECT blood_pressure, blood_glucose FROM blood_log WHERE user_id = ? ORDER BY log_date DESC LIMIT 1";
$stmt = $conn->prepare($blood_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$blood_result = $stmt->get_result();
$blood_data = $blood_result->fetch_assoc();
$blood_pressure = $blood_data['blood_pressure'] ?? 'N/A';
$blood_glucose = $blood_data['blood_glucose'] ?? 'N/A';

// Weekly data for charts
$weekly_data = [];
for ($i = 6; $i >= 0; $i--) {
    $date = date('Y-m-d', strtotime("-$i days"));
    $weekly_data['dates'][] = date('D, M j', strtotime($date));
    
    // Calories
    $stmt = $conn->prepare("SELECT SUM(calories) AS total FROM food_log WHERE user_id = ? AND log_date = ?");
    $stmt->bind_param("is", $user_id, $date);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $weekly_data['calories'][] = $data['total'] ?? 0;
    
    // Exercise
    $stmt = $conn->prepare("SELECT SUM(calories_burned) AS total FROM exercise_log WHERE user_id = ? AND log_date = ?");
    $stmt->bind_param("is", $user_id, $date);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $weekly_data['exercise'][] = $data['total'] ?? 0;
    
    // Water
$stmt = $conn->prepare("SELECT SUM(water_intake) AS total FROM water_log WHERE user_id = ? AND log_date = ?");
if (!$stmt) {
    die("Prepare failed: " . $conn->error);
}
$stmt->bind_param("is", $user_id, $date);
$stmt->execute();
$result = $stmt->get_result();
$data = $result->fetch_assoc();
$weekly_data['water'][] = $data['total'] ?? 0;

    
    // Mindfulness
    $stmt = $conn->prepare("SELECT SUM(duration) AS total FROM mindfulness_log WHERE user_id = ? AND log_date = ?");
    $stmt->bind_param("is", $user_id, $date);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();
    $weekly_data['mindfulness'][] = $data['total'] ?? 0;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Health Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root {
            --primary: #3498db;
            --secondary: #2ecc71;
            --danger: #e74c3c;
            --warning: #f39c12;
            --info: #1abc9c;
            --dark: #2c3e50;
            --light: #f8f9fa;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: var(--dark);
        }
        
        .dashboard-header {
            background: linear-gradient(135deg, #db3434, #5d4aff);
            color: white;
            border-radius: 0 0 20px 20px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .chart-card {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
            padding: 20px;
            margin-bottom: 30px;
            height: 100%;
            transition: transform 0.3s;
        }
        
        .chart-card:hover {
            transform: translateY(-5px);
        }
        
        .chart-container {
            position: relative;
            height: 250px;
            margin-top: 15px;
        }
        
        .metric-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        
        .nav-card {
            background: white;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        
        .btn-report {
            background: var(--danger);
            border: none;
            padding: 10px 20px;
            border-radius: 30px;
            font-weight: 600;
            transition: all 0.3s;
        }
        
        .btn-report:hover {
            background: #c0392b;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(231, 76, 60, 0.3);
        }
        
        .badge-calories { background-color: #ff6384; }
        .badge-water { background-color: #36a2eb; }
        .badge-exercise { background-color: #4bc0c0; }
        .badge-mindfulness { background-color: #9966ff; }
    </style>
</head>
<body>
    <header class="dashboard-header py-4 px-3">
        <div class="container">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="mb-0">Welcome, <?php echo htmlspecialchars($user_name); ?>!</h1>
                    <p class="mb-0">Your health dashboard for <?php echo date('F j, Y'); ?></p>
                </div>
                <form method="post" action="send_report.php">
                    <button type="submit" class="btn btn-report text-white">
                        <i class="bi bi-envelope-fill"></i> Email Report
                    </button>
                </form>
            </div>
        </div>
    </header>

    <div class="container mb-5">

        <?php if (!empty($message)): ?>
            <div class="alert alert-info"><?= $message ?></div>
        <?php endif; ?>

        <!-- First Row of Charts -->
        <div class="row mb-4">
            <!-- Chart 1: Daily Calories -->
            <div class="col-lg-4 col-md-6">
                <div class="chart-card">
                    <h3><i class="bi bi-fire text-danger"></i> Daily Calories</h3>
                    <div class="chart-container">
                        <canvas id="caloriesChart"></canvas>
                    </div>
                    <div class="d-flex justify-content-between mt-2">
                        <span class="badge bg-primary">Consumed: <?= $total_calories_consumed ?> kcal</span>
                        <span class="badge bg-success">Burned: <?= $total_calories_burned ?> kcal</span>
                    </div>
                </div>
            </div>
            
            <!-- Chart 2: Water Intake -->
            <div class="col-lg-4 col-md-6">
                <div class="chart-card">
                    <h3><i class="bi bi-droplet-fill text-primary"></i> Water Intake</h3>
                    <div class="chart-container">
                        <canvas id="waterChart"></canvas>
                    </div>
                    <div class="progress mt-3" style="height: 10px;">
                        <div class="progress-bar bg-info" role="progressbar" 
                             style="width: <?= ($total_water_intake/$target_water_intake)*100 ?>%" 
                             aria-valuenow="<?= $total_water_intake ?>" 
                             aria-valuemin="0" 
                             aria-valuemax="<?= $target_water_intake ?>">
                        </div>
                    </div>
                    <small class="text-muted"><?= $total_water_intake ?> / <?= $target_water_intake ?> ml</small>
                </div>
            </div>
            
            <!-- Chart 3: Weekly Activity -->
            <div class="col-lg-4 col-md-6">
                <div class="chart-card">
                    <h3><i class="bi bi-activity text-success"></i> Weekly Activity</h3>
                    <div class="chart-container">
                        <canvas id="weeklyActivityChart"></canvas>
                    </div>
                    <div class="text-center mt-2">
                        <span class="badge bg-secondary">Last 7 days</span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Second Row of Charts -->
        <div class="row mb-4">
            <!-- Chart 4: Mindfulness -->
            <div class="col-lg-4 col-md-6">
                <div class="chart-card">
                    <h3><i class="bi bi-emoji-smile text-warning"></i> Mindfulness</h3>
                    <div class="chart-container">
                        <canvas id="mindfulnessChart"></canvas>
                    </div>
                    <div class="mt-2">
                        <?php if (!empty($mindfulness_activities)): ?>
                            <small>Activities: <?= implode(', ', $mindfulness_activities) ?></small>
                        <?php else: ?>
                            <small class="text-muted">No mindfulness activities today</small>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Chart 5: BMI Trend -->
            <div class="col-lg-4 col-md-6">
                <div class="chart-card">
                    <h3><i class="bi bi-graph-up text-info"></i> BMI Trend</h3>
                    <div class="chart-container">
                        <canvas id="bmiChart"></canvas>
                    </div>
                    <div class="text-center mt-2">
                        <span class="badge bg-<?= ($bmi >= 25) ? 'danger' : 'success' ?>">
                            Current BMI: <?= $bmi ?>
                        </span>
                    </div>
                </div>
            </div>
            
            <!-- Chart 6: Blood Pressure -->
            <div class="col-lg-4 col-md-6">
                <div class="chart-card">
                    <h3><i class="bi bi-heart-pulse text-danger"></i> Blood Pressure</h3>
                    <div class="chart-container">
                        <canvas id="bpChart"></canvas>
                    </div>
                    <div class="text-center mt-2">
                        <span class="badge bg-<?= (explode('/', $blood_pressure)[0] > 120) ? 'danger' : 'success' ?>">
                            Current: <?= $blood_pressure ?>
                        </span>
                    </div>
                </div>
            </div>
        </div>
        
        <!-- Navigation and Quick Links -->
        <div class="row">
            <div class="col-md-6">
                <div class="nav-card">
                    <h3><i class="bi bi-plus-circle-fill text-success"></i> Log Activities</h3>
                    <div class="d-grid gap-2">
                        <a href="log_food.php" class="btn btn-outline-primary text-start"><i class="bi bi-egg-fried"></i> Log Food</a>
                        <a href="log_sleep.php" class="btn btn-outline-secondary text-start"><i class="bi bi-moon"></i> Log Sleep</a>
                        <a href="log_exercise.php" class="btn btn-outline-success text-start"><i class="bi bi-bicycle"></i> Log Exercise</a>
                        <a href="log_water.php" class="btn btn-outline-info text-start"><i class="bi bi-cup-straw"></i> Log Water</a>
                        <a href="log_mindfulness.php" class="btn btn-outline-secondary text-start"><i class="bi bi-emoji-smile"></i> Log Mindfulness</a>
                        <a href="log_blood.php" class="btn btn-outline-danger text-start"><i class="bi bi-droplet-half"></i> Log Blood Data</a>
                        <a href="log_body.php" class="btn btn-outline-warning text-start"><i class="bi bi-person-lines-fill"></i> Log Body Data</a>
                        <a href="log_cycle.php" class="btn btn-outline-danger text-start"><i class="bi bi-droplet-half"></i> Log Cycle Data</a>
                    </div>
                </div>
            </div>
            
            <div class="col-md-6">
                <div class="nav-card">
                    <h3><i class="bi bi-journal-text text-primary"></i> View Logs</h3>
                    <div class="d-grid gap-2">
                        <a href="view_food_logs.php" class="btn btn-outline-primary text-start"><i class="bi bi-egg-fried"></i> Food Logs</a>
                        <a href="view_sleep_log.php" class="btn btn-outline-secondary text-start"><i class="bi bi-moon"></i> Sleep Logs</a>
                        <a href="view_exercise_logs.php" class="btn btn-outline-success text-start"><i class="bi bi-bicycle"></i> Exercise Logs</a>
                        <a href="view_water_logs.php" class="btn btn-outline-info text-start"><i class="bi bi-cup-straw"></i> Water Logs</a>
                        <a href="view_mindfulness_log.php" class="btn btn-outline-secondary text-start"><i class="bi bi-emoji-smile"></i> Mindfulness Logs</a>
                        <a href="view_blood_log.php" class="btn btn-outline-danger text-start"><i class="bi bi-droplet-half"></i> Blood Logs</a>
                        <a href="view_body_log.php" class="btn btn-outline-warning text-start"><i class="bi bi-person-lines-fill"></i> Body Logs</a>
                        <a href="view_cycle_log.php" class="btn btn-outline-danger text-start"><i class="bi bi-droplet-half"></i> Cycle Logs</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer class="bg-light py-3">
        <div class="container text-center">
            <a href="logout.php" class="btn btn-outline-danger"><i class="bi bi-box-arrow-right"></i> Logout</a>
        </div>
    </footer>

    <script>
        // Initialize all charts
        document.addEventListener('DOMContentLoaded', function() {
            // Chart 1: Daily Calories
            new Chart(document.getElementById('caloriesChart'), {
                type: 'doughnut',
                data: {
                    labels: ['Consumed', 'Burned'],
                    datasets: [{
                        data: [<?= $total_calories_consumed ?>, <?= $total_calories_burned ?>],
                        backgroundColor: ['#36a2eb', '#ff6384'],
                        borderWidth: 0
                    }]
                },
                options: {
                    cutout: '70%',
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
            
            // Chart 2: Water Intake
            new Chart(document.getElementById('waterChart'), {
                type: 'bar',
                data: {
                    labels: ['Today'],
                    datasets: [
                        {
                            label: 'Current',
                            data: [<?= $total_water_intake ?>],
                            backgroundColor: '#4bc0c0'
                        },
                        {
                            label: 'Target',
                            data: [<?= $target_water_intake ?>],
                            backgroundColor: '#ffcd56'
                        }
                    ]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
            
            // Chart 3: Weekly Activity
            new Chart(document.getElementById('weeklyActivityChart'), {
                type: 'line',
                data: {
                    labels: <?= json_encode($weekly_data['dates']) ?>,
                    datasets: [
                        {
                            label: 'Calories Consumed',
                            data: <?= json_encode($weekly_data['calories']) ?>,
                            borderColor: '#ff6384',
                            backgroundColor: 'rgba(255, 99, 132, 0.1)',
                            fill: true
                        },
                        {
                            label: 'Exercise (kcal)',
                            data: <?= json_encode($weekly_data['exercise']) ?>,
                            borderColor: '#4bc0c0',
                            backgroundColor: 'rgba(75, 192, 192, 0.1)',
                            fill: true
                        }
                    ]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: true
                        }
                    },
                    plugins: {
                        legend: {
                            position: 'bottom'
                        }
                    }
                }
            });
            
            // Chart 4: Mindfulness
            new Chart(document.getElementById('mindfulnessChart'), {
                type: 'pie',
                data: {
                    labels: ['Mindfulness Minutes'],
                    datasets: [{
                        data: [<?= $mindfulness_minutes ?>],
                        backgroundColor: ['#9966ff'],
                        borderWidth: 0
                    }]
                },
                options: {
                    plugins: {
                        legend: {
                            display: false
                        }
                    }
                }
            });
            
            // Chart 5: BMI Trend (sample data)
            new Chart(document.getElementById('bmiChart'), {
                type: 'line',
                data: {
                    labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'],
                    datasets: [{
                        label: 'BMI',
                        data: [22.1, 22.3, 22.5, 22.4, 22.2, 22.0],
                        borderColor: '#36a2eb',
                        backgroundColor: 'rgba(54, 162, 235, 0.1)',
                        fill: true
                    }]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: false
                        }
                    }
                }
            });
            
            // Chart 6: Blood Pressure (sample data)
            new Chart(document.getElementById('bpChart'), {
                type: 'line',
                data: {
                    labels: ['Morning', 'Noon', 'Evening'],
                    datasets: [
                        {
                            label: 'Systolic',
                            data: [120, 118, 122],
                            borderColor: '#ff6384',
                            backgroundColor: 'rgba(255, 99, 132, 0.1)',
                            fill: true
                        },
                        {
                            label: 'Diastolic',
                            data: [80, 78, 82],
                            borderColor: '#36a2eb',
                            backgroundColor: 'rgba(54, 162, 235, 0.1)',
                            fill: true
                        }
                    ]
                },
                options: {
                    scales: {
                        y: {
                            beginAtZero: false
                        }
                    }
                }
            });
        });
    </script>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>