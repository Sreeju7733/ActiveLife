<?php
include 'auth.php';  // Ensure the user is authenticated
include 'db/conn.php';  // Database connection

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $exercise_type = $_POST['exercise_type'];
    $calories_burned = $_POST['calories_burned'];

    $user_id = $_SESSION['user_id'];

    // Use prepared statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO exercise_log (user_id, exercise_type, calories_burned, log_date) VALUES (?, ?, ?, CURRENT_DATE)");
    $stmt->bind_param("ssi", $user_id, $exercise_type, $calories_burned);

    if ($stmt->execute()) {
        $message = "✅ Logged: Successfully!";
    } else {
        echo "Error logging exercise: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Exercise</title>
    <!-- Bootstrap for responsive design -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Log Your Exercise</h1>
        
        <?php if (isset($message)): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="exercise_type" class="form-label">Exercise Type</label>
                <input type="text" name="exercise_type" class="form-control" placeholder="Exercise Type" required>
            </div>

            <div class="mb-3">
                <label for="calories_burned" class="form-label">Calories Burned</label>
                <input type="number" name="calories_burned" class="form-control" placeholder="Calories Burned" required>
            </div>

            <button type="submit" class="btn btn-primary">Log Exercise</button>
        </form>

        <p class="mt-3"><a href="index.php">Back to Dashboard</a></p>
    </div>

    <!-- Bootstrap JS (for responsive and interactive components) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
