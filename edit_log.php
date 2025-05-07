<?php
include 'auth.php';
include __DIR__ . '/db/conn.php';

$user_id = $_SESSION['user_id'] ?? null;
$log_id = $_GET['id'] ?? null;

if (!$user_id || !$log_id) {
    header("Location: index.php"); // Redirecting to index.php instead of dashboard.php
    exit;
}

// Fetch the current log details using direct query
$sql = "SELECT food_item, calories FROM food_log WHERE id = '$log_id' AND user_id = '$user_id'";
$result = $conn->query($sql);
$log = $result->fetch_assoc();

if (!$log) {
    echo "Log not found!";
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $food = $_POST['food'] ?? '';
    $calories = $_POST['calories'] ?? 0;

    // Validate inputs
    if (empty($food) || $calories <= 0) {
        echo "Invalid input!";
        exit;
    }

    // Update the food log
    $sql = "UPDATE food_log SET food_item = '$food', calories = '$calories' WHERE id = '$log_id' AND user_id = '$user_id'";

    if ($conn->query($sql)) {
        header("Location: index.php"); // Redirect to index.php after successful update
    } else {
        echo "Error updating food log!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Food Log</title>
    <!-- Add Bootstrap 5 CDN for styles -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Edit Food Log</h2>
        <form method="POST">
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="food" class="form-label">Food Item:</label>
                    <input type="text" class="form-control" name="food" value="<?php echo htmlspecialchars($log['food_item']); ?>" required>
                </div>
                <div class="col-md-6">
                    <label for="calories" class="form-label">Calories:</label>
                    <input type="number" class="form-control" name="calories" value="<?php echo htmlspecialchars($log['calories']); ?>" required>
                </div>
            </div>
            <button type="submit" class="btn btn-primary">Update Log</button>
        </form>

        <br>
        <a href="index.php" class="btn btn-secondary">⬅ Back to Home</a>
    </div>

    <!-- Add Bootstrap 5 JS for interactivity (optional) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
