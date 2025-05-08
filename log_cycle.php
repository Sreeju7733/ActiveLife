<?php
include 'auth.php';
include 'db/conn.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cycle_start_date = $_POST['cycle_start_date'];
    $cycle_end_date = $_POST['cycle_end_date'] ?? null;
    $flow_level = $_POST['flow_level'] ?? 'medium';
    $symptoms = $_POST['symptoms'] ?? '';
    $notes = $_POST['notes'] ?? '';
    $mood = $_POST['mood'] ?? 'neutral';
    $is_irregular = isset($_POST['is_irregular']) ? 1 : 0;
    
    // Calculate duration if end date is provided
    $duration_days = null;
    if ($cycle_end_date) {
        $start = new DateTime($cycle_start_date);
        $end = new DateTime($cycle_end_date);
        $duration_days = $end->diff($start)->days + 1; // +1 to include both start and end days
    }

    $user_id = $_SESSION['user_id'];
    
    $stmt = $conn->prepare("INSERT INTO cycle_log (
        user_id, cycle_start_date, cycle_end_date, duration_days, 
        flow_level, symptoms, notes, mood, is_irregular
    ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
    
    $stmt->bind_param(
        "ississssi", 
        $user_id, 
        $cycle_start_date, 
        $cycle_end_date, 
        $duration_days,
        $flow_level, 
        $symptoms, 
        $notes, 
        $mood, 
        $is_irregular
    );

    if ($stmt->execute()) {
        $message = "✅ Cycle logged successfully!";
        header("Location: index.php?message=" . urlencode($message));
    } else {
        $error = "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Your Menstrual Cycle</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-section {
            background-color: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="container mt-5">
        <h2 class="mb-4">Log Your Menstrual Cycle</h2>

        <?php if (isset($message)): ?>
            <div class="alert alert-success"><?= $message ?></div>
        <?php elseif (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="form-section">
                <h4>Cycle Dates</h4>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="cycle_start_date" class="form-label">Start Date*</label>
                        <input type="date" name="cycle_start_date" class="form-control" required>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label for="cycle_end_date" class="form-label">End Date (optional)</label>
                        <input type="date" name="cycle_end_date" class="form-control">
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h4>Cycle Details</h4>
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <label for="flow_level" class="form-label">Flow Level</label>
                        <select name="flow_level" class="form-select">
                            <option value="light">Light</option>
                            <option value="medium" selected>Medium</option>
                            <option value="heavy">Heavy</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <label for="mood" class="form-label">Mood</label>
                        <select name="mood" class="form-select">
                            <option value="very_good">Very Good</option>
                            <option value="good">Good</option>
                            <option value="neutral" selected>Neutral</option>
                            <option value="bad">Bad</option>
                            <option value="very_bad">Very Bad</option>
                        </select>
                    </div>
                    <div class="col-md-4 mb-3">
                        <div class="form-check pt-4">
                            <input class="form-check-input" type="checkbox" name="is_irregular" id="is_irregular">
                            <label class="form-check-label" for="is_irregular">Irregular Cycle</label>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-section">
                <h4>Additional Information</h4>
                <div class="mb-3">
                    <label for="symptoms" class="form-label">Symptoms (optional)</label>
                    <textarea name="symptoms" class="form-control" rows="3" placeholder="Cramps, headaches, etc."></textarea>
                </div>
                <div class="mb-3">
                    <label for="notes" class="form-label">Notes (optional)</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Any additional notes"></textarea>
                </div>
            </div>

            <div class="d-grid gap-2 d-md-flex justify-content-md-end">
                <button type="submit" class="btn btn-primary">Log Cycle</button>
                <a href="dashboard.php" class="btn btn-outline-secondary">Cancel</a>
            </div>
        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>