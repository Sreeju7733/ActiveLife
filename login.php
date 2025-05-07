<?php
include_once 'db/conn.php';

session_start();
$message = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            header("Location: index.php");
            exit;
        } else {
            $message = "❌ Invalid email or password!";
        }
    } else {
        $message = "❌ No user found with that email!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Login</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body class="bg-light">
    <div class="container my-5">
        <h2 class="mb-4 text-center">Login</h2>
        <?php if (!empty($message)): ?>
        <div class="alert alert-info text-center"><?= $message ?></div>
        <?php endif; ?>

        <form method="POST" action=""class="mx-auto" style="max-width: 400px;">
            <div class="mb-3">
            <label for="email">Email:</label>
            <input type="email"  class="form-control"  name="email" required>
            </div>
            <div class="mb-3">
            <label for="password">Password:</label>
            <input type="password"  class="form-control"  name="password" required>
            </div>
            <div class="mb-3">
            <button type="submit" class="btn btn-primary w-100">Login</button>
            </div>
            <p class="text-center"><a href="signup">Don't have an account? Sign up</a></p>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
