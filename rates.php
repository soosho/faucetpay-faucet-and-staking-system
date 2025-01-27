<?php
// Only start session if it isn't already active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Asia/Bangkok');  // This sets GMT+7 timezone
require 'includes/db.php';
$config = require 'config/config.php'; // Ensure config file is loaded

// Fetch user details and balances
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id");
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$user = $stmt->fetch();

// Check if user data is available
if (!$user) {
    die('User not found.');
}

// Function to fetch cryptocurrency rates from the database
function getCryptoRatesFromDB($pdo) {
    $stmt = $pdo->prepare("SELECT crypto_name, rate_usd FROM crypto_rates");
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_KEY_PAIR); // Fetch as associative array with crypto_name as key and rate_usd as value
}

// Get the cryptocurrency rates from the database
$crypto_rates = getCryptoRatesFromDB($pdo);

// Initialize total USD balance
$total_usd_balance = 0;

// Safely add the user's balances to the total USD balance
$currencies = [
    'btc_balance' => 'bitcoin',
    'eth_balance' => 'ethereum',
    'doge_balance' => 'dogecoin',
    'ltc_balance' => 'litecoin',
    'bch_balance' => 'bitcoin-cash',
    'dash_balance' => 'dash',
    'dgb_balance' => 'digibyte',
    'trx_balance' => 'tron',
    'usdt_balance' => 'tether',
    'fey_balance' => 'feyorra',
    'zec_balance' => 'zcash',
    'bnb_balance' => 'binancecoin',
    'sol_balance' => 'solana',
    'xrp_balance' => 'ripple',
    'matic_balance' => 'matic-network',
    'ada_balance' => 'cardano',
    'ton_balance' => 'the-open-network',
    'xlm_balance' => 'stellar',
    'usdc_balance' => 'usd-coin',
    'xmr_balance' => 'monero',
    'tara_balance' => 'taraxa'
];

// Loop through each currency and safely calculate the total balance
foreach ($currencies as $user_balance_column => $crypto_key) {
    if (isset($user[$user_balance_column]) && isset($crypto_rates[$crypto_key])) {
        $total_usd_balance += $user[$user_balance_column] * $crypto_rates[$crypto_key];
    } else {
        // Log or handle missing rates or user balances
        error_log("Missing rate or balance for $crypto_key.");
    }
}

// Return total USD balance
return $total_usd_balance;

?>
