<?php
include 'auth.php';
include __DIR__ . '/db/conn.php';

$user_id = $_SESSION['user_id'] ?? null;
$message = '';

if (!$user_id) {
    header("Location: login.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $water_intake = (int)($_POST['water_intake'] ?? 0);

    if ($water_intake <= 0) {
        $message = "❌ Invalid water intake value.";
    } else {
        $stmt = $conn->prepare("INSERT INTO water_log (user_id, water_intake, log_date) VALUES (?, ?, CURDATE())");
        $stmt->bind_param("ii", $user_id, $water_intake);

        if ($stmt->execute()) {
            $message = "✅ Water intake logged successfully.";            $message = "✅ Logged: $sleep_duration hours, Quality: $sleep_quality";
            header("Location: index.php?message=" . urlencode($message)); // Redirect to avoid resubmissio        } else {
            $message = "❌ Database error.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Log Water Intake</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>Log Your Water Intake</h2>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="water_intake">Water Intake (in ml):</label>
            <input type="number" name="water_intake" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Log Water Intake</button>
    </form>

    <a href="index.php" class="btn btn-link mt-3">⬅ Back to Dashboard</a>
</body>
</html>
