<?php
include 'auth.php';
include 'db/conn.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $user_id = $_SESSION['user_id'];
    $bmi = $_POST['bmi'];
    $bmr = $_POST['bmr'];
    $caloric_use = $_POST['caloric_use'];
    $log_date = $_POST['log_date'] ?? date('Y-m-d');

    // Prepare the SQL statement to prevent SQL injection
    $stmt = $conn->prepare("INSERT INTO body_log (user_id, bmi, bmr, caloric_use, log_date) VALUES (?, ?, ?, ?, ?)");
    $stmt->bind_param("iddis", $user_id, $bmi, $bmr, $caloric_use, $log_date);
    
    if ($stmt->execute()) {
        $message = "✅ Logged: Successfully!";
        header("Location: index.php?message=" . urlencode($message));
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Body Data</title>
    <!-- Add Bootstrap for responsive design -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h1>Log Body Metrics</h1>

        <!-- Add success/error message after form submission -->
        <?php if (isset($message)): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="bmi" class="form-label">BMI:</label>
                <input type="number" step="0.1" name="bmi" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="bmr" class="form-label">BMR:</label>
                <input type="number" step="0.1" name="bmr" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="caloric_use" class="form-label">Caloric Use:</label>
                <input type="number" name="caloric_use" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="log_date" class="form-label">Log Date (optional):</label>
                <input type="date" name="log_date" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">Log Body Data</button>
        </form>

        <p class="mt-3"><a href="index.php">Back to Dashboard</a></p>
    </div>

    <!-- Bootstrap JS (for responsive and interactive components) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
