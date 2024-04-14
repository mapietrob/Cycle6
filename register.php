<?php
include 'header.php'; // Includes connect.php and starts the session

// Initialize variables to store user input and potential error message
$username = $email = $password = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);

    // Basic validation
    if (empty($username) || empty($email) || empty($password)) {
        $error = "Please fill in all fields.";
    } else {
        // Check if username or email already exists
        $stmt = $pdo->prepare("SELECT user_id FROM users WHERE username = ? OR email = ?");
        $stmt->execute([$username, $email]);
        if ($stmt->rowCount() > 0) {
            $error = "Username or Email already exists.";
        } else {
            // Insert new user
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $insert_stmt = $pdo->prepare("INSERT INTO users (username, email, password) VALUES (?, ?, ?)");
            if ($insert_stmt->execute([$username, $email, $hashed_password])) {
                $_SESSION['user_id'] = $pdo->lastInsertId();
                $_SESSION['username'] = $username;
                header("Location: dashboard.php"); // Redirect to dashboard
                exit();
            } else {
                $error = "Something went wrong. Please try again later.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - Net Worth Calculator</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
<h2>Register</h2>
<?php if (!empty($error)): ?>
    <p><?php echo $error; ?></p>
<?php endif; ?>
<form method="POST" action="register.php">
    <label for="username">Username:</label>
    <input type="text" name="username" id="username" required>
    <label for="email">Email:</label>
    <input type="email" name="email" id="email" required>
    <label for="password">Password:</label>
    <input type="password" name="password" id="password" required>
    <button type="submit">Register</button>
</form>

<?php include 'footer.php'; ?>
</body>
</html>
