<?php
include 'connect.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Net Worth Calculator</title>
    <link rel="stylesheet" href="main.css">
</head>
<body>
<header>
    <div class="logo">
        <img src="media/logo.png" alt="Net Worth Calculator Logo" class="logo-img" <p>Net Worth Calculator</p>

    </div>
    <nav class="top-nav">
        <ul>
            <li><a href="index.php">Home</a></li>
            <?php if (isset($_SESSION['user_id'])): ?>
                <li><a href="dashboard.php">Dashboard</a></li>
                <li><a href="logout.php">Logout</a></li>
            <?php else: ?>
                <li><a href="login.php">Login</a></li>
                <li><a href="register.php">Register</a></li>
            <?php endif; ?>
        </ul>
    </nav>
</header>
