<?php
session_start();
date_default_timezone_set('Asia/Jakarta');

// Check if the user is logged in, otherwise redirect to login
if (!isset($_SESSION['user_id'])) {
    header('Location: login');
    exit();
}

// Include necessary files
require 'includes/db.php';
require 'config/config.php';

// Mapping between currency codes and crypto_names in crypto_rates table
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

// Helper to get the first day of the current month
$firstOfMonth = date('Y-m-01');

// Fetch total wagered amount in USD for the last 7 days (starting from the 1st of the month)
$wagersLast7Days = $pdo->prepare("
    SELECT u.username, SUM(bh.bet_amount * cr.rate_usd) AS total_wagered_usd
    FROM betting_history bh
    JOIN crypto_rates cr ON cr.crypto_name = :mapped_currency
    JOIN users u ON bh.user_id = u.id
    WHERE bh.created_at >= :first_of_month
    AND bh.currency = :currency
    GROUP BY bh.user_id
    ORDER BY total_wagered_usd DESC
    LIMIT 10
");

// Fetch total wagered amount in USD for the last 30 days (starting from the 1st of the month)
$wagersLast30Days = $pdo->prepare("
    SELECT u.username, SUM(bh.bet_amount * cr.rate_usd) AS total_wagered_usd
    FROM betting_history bh
    JOIN crypto_rates cr ON cr.crypto_name = :mapped_currency
    JOIN users u ON bh.user_id = u.id
    WHERE bh.created_at >= :first_of_month
    AND bh.currency = :currency
    GROUP BY bh.user_id
    ORDER BY total_wagered_usd DESC
    LIMIT 10
");

// Process wager data for both 7 days and 30 days
$topWagers7 = [];
$topWagers30 = [];

foreach ($crypto_rate_mapping as $currency_code => $crypto_name) {
    // 7 days
    $wagersLast7Days->execute([
        ':currency' => $currency_code,
        ':mapped_currency' => $crypto_name,
        ':first_of_month' => $firstOfMonth
    ]);
    $results7 = $wagersLast7Days->fetchAll(PDO::FETCH_ASSOC);
    $topWagers7 = array_merge($topWagers7, $results7);

    // 30 days
    $wagersLast30Days->execute([
        ':currency' => $currency_code,
        ':mapped_currency' => $crypto_name,
        ':first_of_month' => $firstOfMonth
    ]);
    $results30 = $wagersLast30Days->fetchAll(PDO::FETCH_ASSOC);
    $topWagers30 = array_merge($topWagers30, $results30);
}

// Sort and limit to top 10
usort($topWagers7, function ($a, $b) {
    return $b['total_wagered_usd'] - $a['total_wagered_usd'];
});
$topWagers7 = array_slice($topWagers7, 0, 10);

usort($topWagers30, function ($a, $b) {
    return $b['total_wagered_usd'] - $a['total_wagered_usd'];
});
$topWagers30 = array_slice($topWagers30, 0, 10);

// Fetch number of claims for the last 7 days (starting from the 1st of the month)
$claimsLast7Days = $pdo->prepare("
    SELECT u.username, COUNT(dc.id) AS total_claims
    FROM daily_claims dc
    JOIN users u ON dc.user_id = u.id
    WHERE dc.claim_time >= :first_of_month
    GROUP BY dc.user_id
    ORDER BY total_claims DESC
    LIMIT 10
");

$claimsLast7Days->execute([':first_of_month' => $firstOfMonth]);
$topClaims7 = $claimsLast7Days->fetchAll(PDO::FETCH_ASSOC);

// Fetch number of claims for the last 30 days (starting from the 1st of the month)
$claimsLast30Days = $pdo->prepare("
    SELECT u.username, COUNT(dc.id) AS total_claims
    FROM daily_claims dc
    JOIN users u ON dc.user_id = u.id
    WHERE dc.claim_time >= :first_of_month
    GROUP BY dc.user_id
    ORDER BY total_claims DESC
    LIMIT 10
");

$claimsLast30Days->execute([':first_of_month' => $firstOfMonth]);
$topClaims30 = $claimsLast30Days->fetchAll(PDO::FETCH_ASSOC);

// Fetch top 10 users by level
$topLevelUsers = $pdo->prepare("
    SELECT username, level, exp 
    FROM users 
    ORDER BY level DESC, exp DESC 
    LIMIT 10
");
$topLevelUsers->execute();
$topLevels = $topLevelUsers->fetchAll(PDO::FETCH_ASSOC);

// Fetch the top 10 users with the most referrals
$topReferrers = $pdo->prepare("
    SELECT u.username, COUNT(r.id) AS total_referrals
    FROM users u
    LEFT JOIN users r ON r.referrer_id = u.id
    GROUP BY u.id
    ORDER BY total_referrals DESC
    LIMIT 10
");
$topReferrers->execute();
$mostReferrers = $topReferrers->fetchAll(PDO::FETCH_ASSOC);
?>
