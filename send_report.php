<?php
session_start();
include 'db/conn.php';

// Check authentication
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

// Fetch user details
$user_id = $_SESSION['user_id'];
$user_sql = "SELECT username, email FROM users WHERE id = ?";
$stmt = $conn->prepare($user_sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$user_result = $stmt->get_result();
$user_data = $user_result->fetch_assoc();
$user_name = $user_data['name'] ?? 'User';
$user_email = $user_data['email'] ?? '';

if (!$user_email) {
    die('User email not found in the database.');
}

// Fetch health data (replace with actual queries)
$total_calories_consumed = 2000; // Example data
$total_calories_burned = 500;   // Example data
$total_water_intake = 1500;     // Example data
$target_water_intake = 2000;    // Example data
$mindfulness_minutes = 30;      // Example data
$weekly_data = [
    'dates' => ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
    'calories' => [2000, 1800, 2200, 2100, 1900, 2500, 2300],
    'exercise' => [30, 40, 35, 50, 20, 60, 45],
];
$bmi = 22.5; // Example data
$bmr = 1500; // Example data
$blood_pressure = '120/80'; // Example data
$blood_glucose = 90;       // Example data

// Generate chart configurations
$chartConfigs = [
    'calories' => [
        'type' => 'doughnut',
        'data' => [
            'labels' => ['Consumed', 'Burned'],
            'datasets' => [[
                'data' => [$total_calories_consumed, $total_calories_burned],
                'backgroundColor' => ['#36a2eb', '#ff6384']
            ]]
        ]
    ],
    'water' => [
        'type' => 'bar',
        'data' => [
            'labels' => ['Today'],
            'datasets' => [
                [
                    'label' => 'Current',
                    'data' => [$total_water_intake],
                    'backgroundColor' => '#4bc0c0'
                ],
                [
                    'label' => 'Target',
                    'data' => [$target_water_intake],
                    'backgroundColor' => '#ffcd56'
                ]
            ]
        ]
    ],
    'mindfulness' => [
        'type' => 'pie',
        'data' => [
            'labels' => ['Mindfulness'],
            'datasets' => [[
                'data' => [$mindfulness_minutes],
                'backgroundColor' => ['#9966ff']
            ]]
        ]
    ],
    'weekly_activity' => [
        'type' => 'line',
        'data' => [
            'labels' => $weekly_data['dates'],
            'datasets' => [
                [
                    'label' => 'Calories',
                    'data' => $weekly_data['calories'],
                    'borderColor' => '#ff6384',
                    'fill' => false
                ],
                [
                    'label' => 'Exercise',
                    'data' => $weekly_data['exercise'],
                    'borderColor' => '#4bc0c0',
                    'fill' => false
                ]
            ]
        ]
    ]
];

// Generate email content
$email_content = '
<!DOCTYPE html>
<html>
<head>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 800px; margin: 0 auto; }
        .header { background: linear-gradient(135deg, #3498db, #2ecc71); color: white; padding: 30px; text-align: center; border-radius: 10px 10px 0 0; }
        .content { padding: 20px; background: #f9f9f9; }
        .chart-grid { display: grid; grid-template-columns: repeat(2, 1fr); gap: 20px; margin: 20px 0; }
        .chart-container { background: white; border-radius: 8px; padding: 15px; box-shadow: 0 2px 8px rgba(0,0,0,0.1); }
        .chart-title { font-weight: bold; margin-bottom: 10px; color: #2c3e50; text-align: center; }
        .chart-img { width: 100%; height: auto; border-radius: 5px; }
    </style>
</head>
<body>
    <div class="header">
        <h1>Health Report</h1>
        <p>For '.htmlspecialchars($user_name).' | '.date('F j, Y').'</p>
    </div>
    
    <div class="content">
        <h2>Today\'s Summary</h2>
        <div class="chart-grid">
            <div class="chart-container">
                <div class="chart-title">Daily Calories</div>
                <img src="https://quickchart.io/chart?c='.urlencode(json_encode($chartConfigs['calories'])).'" class="chart-img">
            </div>
            <div class="chart-container">
                <div class="chart-title">Water Intake</div>
                <img src="https://quickchart.io/chart?c='.urlencode(json_encode($chartConfigs['water'])).'" class="chart-img">
            </div>
        </div>
        <div class="chart-grid">
            <div class="chart-container">
                <div class="chart-title">Mindfulness</div>
                <img src="https://quickchart.io/chart?c='.urlencode(json_encode($chartConfigs['mindfulness'])).'" class="chart-img">
            </div>
            <div class="chart-container">
                <div class="chart-title">Weekly Activity</div>
                <img src="https://quickchart.io/chart?c='.urlencode(json_encode($chartConfigs['weekly_activity'])).'" class="chart-img">
            </div>
        </div>
    </div>
</body>
</html>';

// Send email using PHPMailer
require 'PHPMailer/src/Exception.php';
require 'PHPMailer/src/PHPMailer.php';
require 'PHPMailer/src/SMTP.php';

$mail = new PHPMailer\PHPMailer\PHPMailer();

try {
    // SMTP server settings
    $mail->isSMTP();
    $mail->Host       = 'smtp.gmail.com'; // Set your SMTP server
    $mail->SMTPAuth   = true;
    $mail->Username   = 'sree.gowtham.v@gmail.com'; // Your email
    $mail->Password   = 'crdz gggj lbtf ulic';   // Your app password
    $mail->SMTPSecure = PHPMailer\PHPMailer\PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;

    // Recipients
    $mail->setFrom($user_email, 'Health Tracker');
    $mail->addAddress($user_email, $user_name);

    // Email content
    $mail->isHTML(true);
    $mail->Subject = 'Your Health Dashboard Report - ' . date('F j, Y');
    $mail->Body    = $email_content;

    $mail->send();
    echo "<script>alert('Email sent successfully!');window.location.href = 'index';
</script>";
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}
?>