<?php
include 'auth.php';
include 'db/conn.php';

$user_id = $_SESSION['user_id'] ?? null;
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user_id) {
    $sleep_duration = (float)($_POST['sleep_duration'] ?? 0);
    $sleep_quality = trim($_POST['sleep_quality'] ?? '');

    if ($sleep_duration <= 0 || $sleep_duration > 24) {
        $message = "❌ Invalid sleep duration.";
    } else {
        $log_date = date('Y-m-d');
        $stmt = $conn->prepare("INSERT INTO sleep_log (user_id, sleep_duration, sleep_quality, log_date) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("idss", $user_id, $sleep_duration, $sleep_quality, $log_date);

        if ($stmt->execute()) {
            $message = "✅ Sleep data logged successfully!";
        } else {
            $message = "❌ Error: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Log Sleep</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2>Log Your Sleep</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST">
        <div class="form-group">
            <label for="sleep_duration">Sleep Duration (hours):</label>
            <input type="number" class="form-control" name="sleep_duration" id="sleep_duration" required min="1" max="24">
        </div>

        <div class="form-group">
            <label for="sleep_quality">Sleep Quality (Optional):</label>
            <input type="text" class="form-control" name="sleep_quality" id="sleep_quality">
        </div>

        <button type="submit" class="btn btn-success">Log Sleep</button>
    </form>

    <a href="index.php" class="btn btn-link mt-3">⬅ Back to Dashboard</a>
</div>

<!-- Bootstrap JS -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
