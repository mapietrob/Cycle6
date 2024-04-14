<?php include 'header.php'; ?>

<main class="container">
    <h1>Welcome to Net Worth Calculator</h1>
    <p>This web application helps you calculate and track your net worth over time. Enter your assets and liabilities, and let the calculator provide you with a comprehensive overview of your financial health.</p>

    <!-- Dynamic content based on whether the user is logged in -->
    <?php if (isset($_SESSION['user_id'])): ?>
        <p>Hello, <?php echo htmlspecialchars($_SESSION['username']); ?>. You are logged in.</p>
        <p>Go to your <a href="dashboard.php">Dashboard</a> to view or update your net worth.</p>
    <?php else: ?>
        <p>Get started by <a href="login.php">logging in</a> or <a href="register.php">registering</a> an account.</p>
    <?php endif; ?>
</main>

<?php include 'footer.php'; ?>

