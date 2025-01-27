<?php
// Only start session if it isn't already active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
date_default_timezone_set('Asia/Bangkok');
require 'includes/db.php';
$config = require 'config/config.php';

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

// Fetch the user's balances and last reward update time
$stmt = $pdo->prepare("SELECT *, UNIX_TIMESTAMP(last_reward_update) AS last_update_time FROM users WHERE id = :user_id");
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$user = $stmt->fetch();

$current_time = time();
$last_update_time = $user['last_update_time'] ?? 0;
$time_diff = $current_time - $last_update_time;

// Check if at least 1 second has passed since the last update
if ($time_diff < 1) {
    echo json_encode(['success' => false, 'error' => 'Update frequency too high. Please wait.']);
    exit();
}

// Fetch all active stakes for the user
$stmt = $pdo->prepare("SELECT * FROM staking WHERE user_id = :user_id AND status = 'active'");
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$stakes = $stmt->fetchAll();

$total_reward = 0;  // To accumulate the reward from all active stakes
$current_time_obj = new DateTime();  // Get the current timestamp

foreach ($stakes as $stake) {
    // Check if the stake is still within the lock period
    $start_time = new DateTime($stake['start_time']);
    $days_staked = $start_time->diff($current_time_obj)->days;

    if ($days_staked <= $stake['lock_period']) {
        // Get the reward rate based on the lock period from the config
        $lock_period = $stake['lock_period'];
        $reward_rate = $config['staking']['reward_rates'][$lock_period] ?? 0;

        // Calculate per-second reward (daily reward rate / 86,400 seconds)
        $per_second_reward = ($stake['amount'] * $reward_rate / 100) / 86400;

        // Add to the user's balance for the specific currency
        $currency_column = $stake['currency'] . '_balance';
        $stmt = $pdo->prepare("UPDATE users SET {$currency_column} = {$currency_column} + :reward WHERE id = :user_id");
        $stmt->execute([
            'reward' => $per_second_reward,
            'user_id' => $_SESSION['user_id']
        ]);

        // Accumulate the reward for the response
        $total_reward += $per_second_reward;
    } else {
        // Mark the stake as completed once the lock period is over
        $stmt = $pdo->prepare("UPDATE staking SET status = 'completed' WHERE id = :stake_id");
        $stmt->execute(['stake_id' => $stake['id']]);
    }
}

// Update the last reward update time to the current time
$stmt = $pdo->prepare("UPDATE users SET last_reward_update = NOW() WHERE id = :user_id");
$stmt->execute(['user_id' => $_SESSION['user_id']]);

// Return updated balances and total reward added
echo json_encode([
    'success' => true,
    'btc_balance' => number_format($user['btc_balance'], 10),
    'eth_balance' => number_format($user['eth_balance'], 10),
    'doge_balance' => number_format($user['doge_balance'], 10),
    'ltc_balance' => number_format($user['ltc_balance'], 10),
    'bch_balance' => number_format($user['bch_balance'], 10),
    'dash_balance' => number_format($user['dash_balance'], 10),
    'dgb_balance' => number_format($user['dgb_balance'], 10),
    'trx_balance' => number_format($user['trx_balance'], 10),
    'usdt_balance' => number_format($user['usdt_balance'], 10),
    'fey_balance' => number_format($user['fey_balance'], 10),
    'zec_balance' => number_format($user['zec_balance'], 10),
    'bnb_balance' => number_format($user['bnb_balance'], 10),
    'sol_balance' => number_format($user['sol_balance'], 10),
    'xrp_balance' => number_format($user['xrp_balance'], 10),
    'matic_balance' => number_format($user['matic_balance'], 10),
    'ada_balance' => number_format($user['ada_balance'], 10),
    'ton_balance' => number_format($user['ton_balance'], 10),
    'xlm_balance' => number_format($user['xlm_balance'], 10),
    'usdc_balance' => number_format($user['usdc_balance'], 10),
    'xmr_balance' => number_format($user['xmr_balance'], 10),
    'tara_balance' => number_format($user['tara_balance'], 10),
    'reward_added' => number_format($total_reward, 10)
]);
?>