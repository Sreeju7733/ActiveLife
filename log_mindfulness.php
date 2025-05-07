<?php
include 'auth.php';
include 'db/conn.php';

$user_id = $_SESSION['user_id'] ?? null;
$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && $user_id) {
    $activity_type = trim($_POST['activity_type'] ?? '');
    $duration = (int)($_POST['duration'] ?? 0);
    $notes = trim($_POST['notes'] ?? '');

    if ($activity_type && $duration > 0) {
        $log_date = date('Y-m-d');
        $stmt = $conn->prepare("INSERT INTO mindfulness_log (user_id, activity_type, duration, notes, log_date) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiss", $user_id, $activity_type, $duration, $notes, $log_date);

        if ($stmt->execute()) {
            $message = "✅ Mindfulness activity logged successfully!";
        } else {
            $message = "❌ Database error: " . $stmt->error;
        }
    } else {
        $message = "❌ Please provide valid activity type and duration.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Mindfulness Activity</title>

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h2 class="mb-4">Log Your Mindfulness Activity</h2>

    <?php if (!empty($message)): ?>
        <div class="alert alert-info"><?= $message ?></div>
    <?php endif; ?>

    <form method="POST" class="mb-4">
        <div class="form-group">
            <label for="activity_type">Activity Type (e.g., Meditation, Screen Time):</label>
            <input type="text" name="activity_type" id="activity_type" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="duration">Duration (in minutes):</label>
            <input type="number" name="duration" id="duration" class="form-control" required>
        </div>

        <div class="form-group">
            <label for="notes">Notes (Optional):</label>
            <textarea name="notes" id="notes" class="form-control" rows="3"></textarea>
        </div>

        <button type="submit" class="btn btn-success">Log Activity</button>
    </form>

    <a href="index.php" class="btn btn-link">⬅ Back to Dashboard</a>
</div>

<!-- Bootstrap JS + dependencies -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
