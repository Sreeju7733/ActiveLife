<?php
include 'auth.php'; // Ensure the user is authenticated
include __DIR__ . '/db/conn.php'; // Database connection

$user_id = $_SESSION['user_id'] ?? null;
$message = '';

if ($_SERVER["REQUEST_METHOD"] === "POST" && $user_id) {
    $food = trim($_POST['food'] ?? '');
    $calories = (int)($_POST['calories'] ?? 0);

    if (!empty($food) && $calories > 0) {
        $log_date = date('Y-m-d');
        
        // Use a prepared statement to prevent SQL injection
        $stmt = $conn->prepare("INSERT INTO food_log (user_id, food_item, calories, log_date) VALUES (?, ?, ?, ?)");
        $stmt->bind_param("isis", $user_id, $food, $calories, $log_date);

        if ($stmt->execute()) {
            $message = "✅ Logged: $food ($calories kcal)";
            exit();
        } else {
            $message = "❌ DB error: " . $stmt->error;
        }
    } else {
        $message = "❌ Please select a valid food item.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Log Food</title>

    <!-- Bootstrap CDN -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        #suggestions { 
            background: #f1f1f1; 
            border: 1px solid #ccc; 
            max-width: 300px; 
            position: absolute; 
            z-index: 999; 
        }
        #suggestions div { 
            padding: 5px; 
            cursor: pointer; 
        }
        #suggestions div:hover { 
            background: #ddd; 
        }
    </style>
</head>
<body>

<div class="container mt-5">
    <h2>Log Your Food</h2>
    <?php if (!empty($message)) echo "<div class='alert alert-info'>$message</div>"; ?>

    <form method="POST">
        <div class="form-group">
            <input type="text" id="food" name="food" class="form-control" placeholder="Type food..." onkeyup="searchFood()" autocomplete="off" required>
            <div id="suggestions"></div>
        </div>
        <div class="form-group">
            <input type="number" id="calories" name="calories" class="form-control" placeholder="Calories" readonly required>
        </div>
        <button type="submit" class="btn btn-primary">Log Food</button>
    </form>

    <p class="mt-3"><a href="index.php">⬅ Back to Dashboard</a></p>
</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

<script>
    function searchFood() {
        const food = document.getElementById("food").value;
        if (food.length < 2) {
            document.getElementById("suggestions").innerHTML = "";
            return;
        }

        fetch("search_food.php?q=" + encodeURIComponent(food))
            .then(res => res.json())
            .then(data => {
                let html = '';
                if (data.length) {
                    data.forEach(item => {
                        html += `<div onclick="selectFood('${item.name}', ${item.calories})">${item.name} (${item.calories} kcal)</div>`;
                    });
                    document.getElementById("suggestions").innerHTML = html;
                } else {
                    document.getElementById("suggestions").innerHTML = "<div>No suggestions found</div>";
                }
            })
            .catch(error => {
                console.error('Error fetching food data:', error);
            });
    }

    function selectFood(name, calories) {
        document.getElementById("food").value = name;
        document.getElementById("calories").value = calories;
        document.getElementById("suggestions").innerHTML = "";
    }
</script>

</body>
</html>
