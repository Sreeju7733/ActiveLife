<?php
include 'auth.php';  // Ensure the user is authenticated
include 'db/conn.php';  // Database connection

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $blood_pressure = $_POST['blood_pressure'];
    $blood_glucose = $_POST['blood_glucose'];

    // Validate input values
    if (empty($blood_pressure) || empty($blood_glucose)) {
        echo "Please provide both blood pressure and glucose readings.";
        exit;
    }

    // Insert data into blood_log table using prepared statements
    $user_id = $_SESSION['user_id'];
    $log_date = date('Y-m-d');  // Current date

    // Prepare and bind
    $stmt = $conn->prepare("INSERT INTO blood_log (user_id, blood_pressure, blood_glucose, log_date) 
                            VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssss", $user_id, $blood_pressure, $blood_glucose, $log_date);

    // Execute statement and check for success
    if ($stmt->execute()) {
        $message = "Blood pressure and glucose data logged successfully!";
    } else {
        $message = "Error logging data: " . $stmt->error;
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Your Blood Pressure & Glucose</title>
    <!-- Add Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Log Your Blood Pressure & Glucose</h2>
        
        <!-- Display success or error message -->
        <?php if (isset($message)): ?>
            <div class="alert alert-info"><?php echo $message; ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label for="blood_pressure" class="form-label">Blood Pressure (e.g., 120/80 mmHg):</label>
                <input type="text" name="blood_pressure" class="form-control" required>
            </div>

            <div class="mb-3">
                <label for="blood_glucose" class="form-label">Blood Glucose (in mg/dL):</label>
                <input type="number" step="0.1" name="blood_glucose" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-primary">Log Data</button>
        </form>

        <p class="mt-3"><a href="index.php">Back to Dashboard</a></p>
    </div>

    <!-- Add Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
