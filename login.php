<?php
include 'header.php'; // Make sure this includes connect.php

// If user is already logged in, redirect to dashboard
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit;
}

$username = $password = "";
$loginError = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // sanitizes user and pass
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $loginError = 'Please enter both username and password.';
    } else {
        $stmt = $pdo->prepare('SELECT user_id, username, password FROM users WHERE username = :username');
        $stmt->bindParam(':username', $username);
        $stmt->execute();

        if ($stmt->rowCount() == 1) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            if (password_verify($password, $user['password'])) {
                // Fetch the most recent net worth calculation for the user
                $stmtNetWorth = $pdo->prepare("SELECT net_worth FROM net_worth_calculations WHERE user_id = :user_id ORDER BY calculation_date DESC LIMIT 1");
                $stmtNetWorth->execute(['user_id' => $user['user_id']]);
                $netWorthResult = $stmtNetWorth->fetch(PDO::FETCH_ASSOC);

                // Store data in session variables
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['net_worth'] = $netWorthResult ? $netWorthResult['net_worth'] : 0;

                // Redirect user to dashboard
                header('Location: dashboard.php');
                exit();
            } else {
                $loginError = 'The password you entered was not valid.';
            }
        } else {
            $loginError = 'No account found with that username.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Net Worth Calculator</title>
</head>
<body>
<div class="login-form">
    <h2>Login</h2>
    <?php if (!empty($loginError)): ?>
        <div class="alert alert-danger"><?php echo $loginError; ?></div>
    <?php endif; ?>
    <form action="login.php" method="post">
        <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username" required>
        </div>
        <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password" required>
        </div>
        <div class="form-group">
            <button type="submit">Login</button>
        </div>
    </form>
</div>

<?php include 'footer.php'; ?>
</body>
</html>
