<?php
include 'header.php'; // Ensures session_start() is called and connects to the database
include 'functions.php'; // Now contains fetchCryptoPrice and fetchStockPrice functions

// Redirect if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Initialize variables
$userId = $_SESSION['user_id'];
$message = ''; // For displaying messages to the user

// Handle investment addition
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['addInvestment'])) {
    $symbol = trim($_POST['symbol']);
    $quantity = floatval($_POST['quantity']);
    $isCrypto = isset($_POST['isCrypto']); // Boolean value, true if checked

    // Decide which function to call based on $isCrypto
    // Fetch the price per unit based on the symbol and whether it's a cryptocurrency
    $pricePerUnit = $isCrypto ? fetchCryptoPrice($symbol) : fetchStockPrice($symbol, 'OHg2z4ngsMJqmR6wKJejvyiNkt1aHTfI');

    // Validate fetched price and quantity
    if ($pricePerUnit !== null && $quantity > 0) {
        // Calculate the total current value of the investment
        $currentValue = $pricePerUnit * $quantity;

        // Temporarily store investment in session for display during this session
        if (!isset($_SESSION['temp_investments'])) {
            $_SESSION['temp_investments'] = [];
        }

        $_SESSION['temp_investments'][] = [
            'symbol' => $symbol,
            'quantity' => $quantity,
            'is_crypto' => $isCrypto,
            'current_value' => $currentValue // Correctly stores the total value of the investment
        ];

        // Success message displaying the calculated total value
        $message = "Investment added successfully. Current value: " . number_format($currentValue, 2);
    } else {
        // Error message if the price per unit couldn't be fetched or quantity is invalid
        $message = "Failed to fetch current value for {$symbol} or invalid quantity. Please check the symbol and quantity, then try again.";
    }
}


// Prepare for net worth calculation
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['calculate'])) {
    // Initialize totals
    $totalAssets = 0;
    $totalLiabilities = 0;

    // Sum assets and liabilities from form
    $assetsFields = ['checking_accounts', 'savings_accounts', 'cars', 'real_estate', 'other_assets'];
    foreach ($assetsFields as $field) {
        $totalAssets += $_POST[$field] ?? 0;
    }

    $liabilitiesFields = ['credit_card_debt', 'personal_loans', 'car_loans', 'student_loans', 'real_estate_loans', 'other_debt'];
    foreach ($liabilitiesFields as $field) {
        $totalLiabilities += $_POST[$field] ?? 0;
    }

    // Include session-stored investments in total assets calculation
    if (isset($_SESSION['temp_investments'])) {
        foreach ($_SESSION['temp_investments'] as $investment) {
            $totalAssets += $investment['current_value'];
        }
        // Optionally clear temporary investments after calculation
        unset($_SESSION['temp_investments']);
    }

    // Calculate net worth
    $netWorth = $totalAssets - $totalLiabilities;

    // Update net worth in session for immediate display
    $_SESSION['net_worth'] = $netWorth;

    // Save calculation to the database
    $stmt = $pdo->prepare("INSERT INTO net_worth_calculations (user_id, total_assets, total_liabilities, net_worth) VALUES (?, ?, ?, ?)");
    $stmt->execute([$userId, $totalAssets, $totalLiabilities, $netWorth]);

    // Redirect to clear POST data and refresh page display
    header('Location: dashboard.php');
    exit;
}

// Display the most recent net worth
$netWorth = $_SESSION['net_worth'] ?? 0;
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Net Worth Calculator</title>
    <link rel="stylesheet" href="main.css"> <!-- Ensure your CSS is linked here -->
</head>
<body>
<br/>

<div class="container">
    <h1>Welcome, <?php echo htmlspecialchars($_SESSION['username']); ?>!</h1>
    <p>Your current net worth: $<?php echo number_format($netWorth, 2); ?></p>


    <h2>Add New Investment</h2>
    <?php if (!empty($message)): ?>
        <p><?php echo $message; ?></p>
    <?php endif; ?>
    <form action="dashboard.php" method="post">
        <div class="form-group">
            <label for="symbol">Symbol:</label>
            <input type="text" id="symbol" name="symbol" required>
        </div>
        <div class="form-group">
            <label for="quantity">Quantity:</label>
            <input type="number" id="quantity" name="quantity" step="0.01" required>
        </div>
        <div class="form-group">
            <label for="isCrypto">Is this a cryptocurrency?</label>
            <input type="checkbox" id="isCrypto" name="isCrypto" value="1">
        </div>
        <button type="submit" name="addInvestment">Add Investment</button>
    </form>

    <h2>Your Investments</h2>
    <?php if (!empty($_SESSION['temp_investments'])): ?>
        <?php foreach ($_SESSION['temp_investments'] as $investment): ?>
            <p>
                <?php echo htmlspecialchars($investment['symbol']); ?>:
                <?php echo htmlspecialchars($investment['quantity']); ?> units @
                <?php echo number_format($investment['current_value'] / $investment['quantity'], 2); ?> each. Total value:
                <?php echo number_format($investment['current_value'], 2); ?>
            </p>
        <?php endforeach; ?>
    <?php endif; ?>



<form action="dashboard.php" method="post">
    <div class="financials-container">
        <div class="section">
            <h2>Assets</h2>

            <label for="checking_accounts">Checking Accounts:</label>
            <input type="number" name="checking_accounts" id="checking_accounts" value="0" step="0.01"><br>

            <label for="savings_accounts">Savings Accounts:</label>
            <input type="number" name="savings_accounts" id="savings_accounts" value="0" step="0.01"><br>

            <label for="cars">Cars:</label>
            <input type="number" name="cars" id="cars" value="0" step="0.01"><br>

            <label for="real_estate">Real Estate:</label>
            <input type="number" name="real_estate" id="real_estate" value="0" step="0.01"><br>

            <label for="other_assets">Other Assets:</label>
            <input type="number" name="other_assets" id="other_assets" value="0" step="0.01"><br>
        </div>

        <div class="section">
            <h2>Liabilities</h2>

            <label for="credit_card_debt">Credit Card Debt:</label>
            <input type="number" name="credit_card_debt" id="credit_card_debt" value="0" step="0.01"><br>

            <label for="personal_loans">Personal Loans:</label>
            <input type="number" name="personal_loans" id="personal_loans" value="0" step="0.01"><br>

            <label for="car_loans">Car Loans:</label>
            <input type="number" name="car_loans" id="car_loans" value="0" step="0.01"><br>

            <label for="student_loans">Student Loans:</label>
            <input type="number" name="student_loans" id="student_loans" value="0" step="0.01"><br>

            <label for="real_estate_loans">Real Estate Loans:</label>
            <input type="number" name="real_estate_loans" id="real_estate_loans" value="0" step="0.01"><br>

            <label for="other_debt">Other Debt:</label>
            <input type="number" name="other_debt" id="other_debt" value="0" step="0.01"><br>
        </div>
    </div>

    <div class="submit-btn-container">
        <button type="submit" name="calculate">Calculate Net Worth</button>
    </div>
</form>

<?php include 'footer.php'; ?>
</body>
</html>
