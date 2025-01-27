<?php
date_default_timezone_set('Asia/Bangkok');  // Set timezone
// Start session
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require 'config/config.php';  // Load configuration settings
require 'includes/db.php';  // Database connection

$successMessage = '';
$errorMessage = '';

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user's balances and cast each balance as float
$stmt = $pdo->prepare('
    SELECT btc_balance, eth_balance, doge_balance, ltc_balance, bch_balance, dash_balance, dgb_balance, trx_balance, usdt_balance, fey_balance, zec_balance, bnb_balance, sol_balance, xrp_balance, matic_balance, ada_balance, ton_balance, xlm_balance, usdc_balance, xmr_balance, tara_balance 
    FROM users WHERE id = :user_id
');
$stmt->execute(['user_id' => $user_id]);
$balances = $stmt->fetch(PDO::FETCH_ASSOC);

foreach ($balances as $currency => $balance) {
    $balances[$currency] = (float)$balance; // Cast each balance to float
}

// List of all currencies the user can swap between (from currency and to currency)
$crypto_names = [
    'btc' => 'Bitcoin',
    'eth' => 'Ethereum',
    'doge' => 'Dogecoin',
    'ltc' => 'Litecoin',
    'bch' => 'Bitcoin Cash',
    'dash' => 'Dash',
    'dgb' => 'DigiByte',
    'trx' => 'Tron',
    'usdt' => 'Tether',
    'fey' => 'Feyorra',
    'zec' => 'Zcash',
    'bnb' => 'Binance Coin',
    'sol' => 'Solana',
    'xrp' => 'Ripple',
    'matic' => 'Matic',
    'ada' => 'Cardano',
    'ton' => 'Toncoin',
    'xlm' => 'Stellar',
    'usdc' => 'USD Coin',
    'xmr' => 'Monero',
    'tara' => 'Taraxa'
];

// Create a mapping between the currency codes and the full names used in the crypto_rates table
$crypto_rate_mapping = [
    'btc' => 'bitcoin',
    'eth' => 'ethereum',
    'doge' => 'dogecoin',
    'ltc' => 'litecoin',
    'bch' => 'bitcoin-cash',
    'dash' => 'dash',
    'dgb' => 'digibyte',
    'trx' => 'tron',
    'usdt' => 'tether',
    'fey' => 'feyorra',
    'zec' => 'zcash',
    'bnb' => 'binancecoin',
    'sol' => 'solana',
    'xrp' => 'ripple',
    'matic' => 'matic-network',
    'ada' => 'cardano',
    'ton' => 'the-open-network',
    'xlm' => 'stellar',
    'usdc' => 'usd-coin',
    'xmr' => 'monero',
    'tara' => 'taraxa'
];

// Fetch available rates from the crypto_rates table
$stmt = $pdo->query('SELECT crypto_name, rate_usd FROM crypto_rates');
$crypto_rates = $stmt->fetchAll(PDO::FETCH_KEY_PAIR);

// Ensure both balance and rates are present before proceeding
if ($balances === false || empty($crypto_rates)) {
    $errorMessage = 'Failed to load user balances or crypto rates.';
}

// Fetch the last 10 swap history records for the user
$stmt = $pdo->prepare('SELECT * FROM swap_history WHERE user_id = :user_id ORDER BY swap_time DESC LIMIT 10');
$stmt->execute(['user_id' => $user_id]);
$swap_history = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle swap request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $from_currency = strtolower($_POST['from_currency']);
    $to_currency = strtolower($_POST['to_currency']);
    $amount_to_swap = (float)$_POST['swap_amount'];

    // Map currency codes to crypto rate names
    $from_crypto_name = $crypto_rate_mapping[$from_currency] ?? null;
    $to_crypto_name = $crypto_rate_mapping[$to_currency] ?? null;

    // Check if the user has enough balance
    if (!isset($balances[$from_currency . '_balance']) || $balances[$from_currency . '_balance'] < $amount_to_swap) {
        $errorMessage = 'You do not have enough balance to perform the swap.';
    }
    // Ensure that valid currencies were selected
    elseif (!array_key_exists($from_currency, $crypto_names) || !array_key_exists($to_currency, $crypto_names)) {
        $errorMessage = 'Invalid currency selected for the swap.';
    }
    // Ensure that crypto rates are available
    elseif (!isset($crypto_rates[$from_crypto_name]) || !isset($crypto_rates[$to_crypto_name])) {
        $errorMessage = 'Crypto rate not found for selected currencies.';
    }
    elseif ($amount_to_swap <= 0) {
        $errorMessage = 'Please enter a valid amount to swap.';
    } else {
        // Calculate swap value based on current rates
        $usd_value = $amount_to_swap * $crypto_rates[$from_crypto_name];  // Convert from_currency to USD
        $converted_value = $usd_value / $crypto_rates[$to_crypto_name];  // Convert USD to to_currency

        // Apply 5% swap fee
        $swap_fee = 0.05 * $converted_value;
        $final_amount = $converted_value - $swap_fee;

        // Format the values only for display in the success message
        $amount_to_swap_display = number_format($amount_to_swap, 8);
        $final_amount_display = number_format($final_amount, 8);
        $swap_fee_display = number_format($swap_fee, 8);

        // Start a transaction
        $pdo->beginTransaction();

        // Deduct from the user's balance
        $stmt = $pdo->prepare("UPDATE users SET {$from_currency}_balance = {$from_currency}_balance - :amount WHERE id = :user_id");
        $stmt->execute(['amount' => $amount_to_swap, 'user_id' => $user_id]);

        // Add to the user's new currency balance
        $stmt = $pdo->prepare("UPDATE users SET {$to_currency}_balance = {$to_currency}_balance + :final_amount WHERE id = :user_id");
        $stmt->execute(['final_amount' => $final_amount, 'user_id' => $user_id]);

        // Record the swap in swap_history table
        $stmt = $pdo->prepare("
            INSERT INTO swap_history (user_id, from_currency, to_currency, swap_amount, fee, final_amount, swap_time) 
            VALUES (:user_id, :from_currency, :to_currency, :swap_amount, :fee, :final_amount, NOW())
        ");
        $stmt->execute([
            'user_id' => $user_id,
            'from_currency' => $from_currency,
            'to_currency' => $to_currency,
            'swap_amount' => $amount_to_swap,
            'fee' => $swap_fee,
            'final_amount' => $final_amount
        ]);

        // Commit transaction
        $pdo->commit();

        $successMessage = "Successfully swapped {$amount_to_swap_display} " . strtoupper($from_currency) .
            " to {$final_amount_display} " . strtoupper($to_currency) . 
            ". A 5% fee of {$swap_fee_display} " . strtoupper($to_currency) . " was applied.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Coin Swap</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f5f5f5;
        }
        .sticky-header {
            background-color: #6576FF;
            color: white;
            padding: 15px;
            text-align: center;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        }
        .container-content {
            max-width: 800px;
            margin: auto;
            margin-top: 100px;
            background: white;
            border: 2px solid #2c3782;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        .btn-primary, .btn-success {
            background-color: #6576FF;
            border: none;
        }
        .btn-primary:hover, .btn-success:hover {
            background-color: #2c3782;
        }
        .table {
            font-size: 0.85rem;
            border: 2px solid #2c3782;
            border-radius: 10px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }
        .table thead th {
            background-color: #6576FF;
            color: white;
            border-top-left-radius: 10px;
            border-top-right-radius: 10px;
            text-align: center;
            border: 2px solid #2c3782;
        }
        .table th, .table td {
            text-align: center;
            border: 2px solid #2c3782;
            vertical-align: middle;
        }
        .table-hover tbody tr:hover {
            background-color: #f1f1f1;
        }
    </style>
</head>
<body>

<div class="sticky-header">
    <h2>Coin Swap</h2>
</div>

<div class="container-content">
    <?php if (!empty($errorMessage)): ?>
        <div class="alert alert-danger text-center">
            <?= htmlspecialchars($errorMessage) ?>
        </div>
    <?php endif; ?>

    <?php if (!empty($successMessage)): ?>
        <div class="alert alert-success text-center">
            <?= htmlspecialchars($successMessage) ?>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="from_currency" class="form-label">From Currency:</label>
            <select name="from_currency" id="from_currency" class="form-select" required>
                <option value="">-- Select From Currency --</option>
                <?php foreach ($balances as $currency => $balance): ?>
                    <?php if ($balance > 0): ?>
                        <option value="<?= strtolower(str_replace('_balance', '', $currency)) ?>">
                            <?= strtoupper(str_replace('_balance', '', $currency)) ?> (<?= number_format($balance, 10) ?>)
                        </option>
                    <?php endif; ?>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="to_currency" class="form-label">To Currency:</label>
            <select name="to_currency" id="to_currency" class="form-select" required>
                <option value="">-- Select To Currency --</option>
                <?php foreach ($crypto_names as $code => $name): ?>
                    <option value="<?= $code ?>"><?= strtoupper($code) ?> (<?= $name ?>)</option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label for="swap_amount" class="form-label">Amount to Swap:</label>
            <input type="number" name="swap_amount" id="swap_amount" class="form-control" min="0.00001000" step="0.00001000" required>
        </div>

        <p class="text-center mt-3"><strong>Estimated Amount You Will Receive: <span id="estimated_amount">0</span></strong></p>

        <button type="submit" class="btn btn-success w-100">Swap</button>
    </form>

    <div class="text-center mt-4">
        <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
    </div>
    <br>
    <!-- Bottom Ad Placement -->
    <center><div id="ad_unit" data-unit-id="6624"></div><script src="https://api.fpadserver.com/static/js/advert_renderer.js"></script></center>

    <!-- Swap History Table -->
    <?php if (!empty($swap_history)): ?>
        <h3 class="text-center mt-5">Last 10 Swaps</h3>
        <div class="table-responsive">
            <table class="table table-bordered table-hover">
                <thead class="custom-header">
                    <tr>
                        <th>Date</th>
                        <th>From</th>
                        <th>To</th>
                        <th>Amount</th>
                        <th>Fee</th>
                        <th>Final</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($swap_history as $swap): ?>
                        <tr>
                            <td><?= htmlspecialchars($swap['swap_time']) ?></td>
                            <td><?= strtoupper(htmlspecialchars($swap['from_currency'])) ?></td>
                            <td><?= strtoupper(htmlspecialchars($swap['to_currency'])) ?></td>
                            <td><?= number_format($swap['swap_amount'], 8) ?></td>
                            <td><?= number_format($swap['fee'], 8) ?></td>
                            <td><?= number_format($swap['final_amount'], 8) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p class="text-center mt-5">No swap history available.</p>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
<script>
const cryptoRateMapping = {
    'btc': 'bitcoin',
    'eth': 'ethereum',
    'doge': 'dogecoin',
    'ltc': 'litecoin',
    'bch': 'bitcoin-cash',
    'dash': 'dash',
    'dgb': 'digibyte',
    'trx': 'tron',
    'usdt': 'tether',
    'fey': 'feyorra',
    'zec': 'zcash',
    'bnb': 'binancecoin',
    'sol': 'solana',
    'xrp': 'ripple',
    'matic': 'matic-network',
    'ada': 'cardano',
    'ton': 'the-open-network',
    'xlm': 'stellar',
    'usdc': 'usd-coin',
    'xmr': 'monero',
    'tara': 'taraxa'
};

const rates = <?= json_encode($crypto_rates) ?>;
const fromCurrencyElement = document.getElementById('from_currency');
const toCurrencyElement = document.getElementById('to_currency');
const swapAmountElement = document.getElementById('swap_amount');
const estimatedAmountElement = document.getElementById('estimated_amount');

function updateEstimatedAmount() {
    const fromCurrency = fromCurrencyElement.value;
    const toCurrency = toCurrencyElement.value;
    const amountToSwap = parseFloat(swapAmountElement.value);

    if (fromCurrency && toCurrency && amountToSwap > 0) {
        const fromRate = rates[cryptoRateMapping[fromCurrency]];
        const toRate = rates[cryptoRateMapping[toCurrency]];

        if (fromRate && toRate) {
            const usdValue = amountToSwap * fromRate; 
            const convertedValue = usdValue / toRate;
            const fee = 0.05 * convertedValue;
            const finalAmount = convertedValue - fee;

            estimatedAmountElement.textContent = finalAmount.toFixed(10);
        } else {
            estimatedAmountElement.textContent = '0';
        }
    } else {
        estimatedAmountElement.textContent = '0';
    }
}

function disableSelectedToCurrency() {
    const fromCurrency = fromCurrencyElement.value;
    const options = toCurrencyElement.options;

    for (let i = 0; i < options.length; i++) {
        options[i].disabled = options[i].value === fromCurrency;
    }
}

fromCurrencyElement.addEventListener('change', () => {
    updateEstimatedAmount();
    disableSelectedToCurrency();
});
toCurrencyElement.addEventListener('change', updateEstimatedAmount);
swapAmountElement.addEventListener('input', updateEstimatedAmount);

window.onload = disableSelectedToCurrency;
</script>

</body>
</html>

