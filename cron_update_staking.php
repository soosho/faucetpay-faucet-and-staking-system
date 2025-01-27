<?php
require 'includes/db.php';
$config = require 'config/config.php'; // Load the config file
date_default_timezone_set('Asia/Bangkok');  // This sets GMT+7 timezone
// Define a secret key for protection
$secret_key = '#####'; // Replace with your actual secret key

// Check if the correct secret key is provided
if (!isset($_GET['key']) || $_GET['key'] !== $secret_key) {
    // Return a 403 Forbidden response if the key is missing or incorrect
    http_response_code(403);
    echo json_encode(['error' => 'Forbidden. Invalid key.']);
    exit();
}

$current_time = new DateTime();  // Get the current time

// Define the interval in seconds for the cron run (e.g., 60 seconds if the cron runs every minute)
$cron_interval = 60; // 1 minute (60 seconds)

// Fetch all active stakes where the refund has not been processed
$stmt = $pdo->prepare("SELECT * FROM staking WHERE status = 'active' AND refund = 0");
$stmt->execute();
$stakes = $stmt->fetchAll();

foreach ($stakes as $stake) {
    // Fetch the reward rate from config based on the lock period
    $lock_period = $stake['lock_period'];
    $reward_rate = 0;

    // Iterate over the reward rates from the config and select the correct rate based on the lock period
    foreach ($config['staking']['reward_rates'] as $days => $rate) {
        if ($lock_period >= $days) {
            $reward_rate = $rate;
        }
    }

    // Calculate the per-second reward (daily reward rate / 86,400 seconds)
    $per_second_reward = ($stake['amount'] * $reward_rate / 100) / 86400;

    // Calculate the total reward for the cron interval (e.g., 60 seconds)
    $total_reward = $per_second_reward * $cron_interval;

    // Add the reward to the user's balance for the specific currency
    $currency_column = $stake['currency'] . '_balance';
    $stmt = $pdo->prepare("UPDATE users SET {$currency_column} = {$currency_column} + :reward WHERE id = :user_id");
    $stmt->execute([
        'reward' => $total_reward,
        'user_id' => $stake['user_id']
    ]);

    // Check if the staking period has ended and refund the staked amount
    $start_time = new DateTime($stake['start_time']);
    $days_staked = $start_time->diff($current_time)->days;

    // Refund the staked amount if the staking period is over and the status is 'active'
    if ($days_staked >= $stake['lock_period'] && $stake['refund'] == 0) {
        // Fetch user's current balance before refund
        $stmt = $pdo->prepare("SELECT {$currency_column} FROM users WHERE id = :user_id");
        $stmt->execute(['user_id' => $stake['user_id']]);
        $before_balance = $stmt->fetchColumn();

        // Refund the staked amount to the user's balance
        $stmt = $pdo->prepare("UPDATE users SET {$currency_column} = {$currency_column} + :amount WHERE id = :user_id");
        $stmt->execute([
            'amount' => $stake['amount'],
            'user_id' => $stake['user_id']
        ]);

        // Fetch user's balance after refund
        $stmt = $pdo->prepare("SELECT {$currency_column} FROM users WHERE id = :user_id");
        $stmt->execute(['user_id' => $stake['user_id']]);
        $after_balance = $stmt->fetchColumn();

        // Log before and after balances to a debug file
        $log_entry = sprintf(
            "User ID: %d, Stake ID: %d, Currency: %s, Before Balance: %.8f, After Balance: %.8f, Refunded Amount: %.8f, Start Time: %s, Lock Period: %d days, Refund Time: %s\n",
            $stake['user_id'],
            $stake['id'],
            $stake['currency'],
            $before_balance,
            $after_balance,
            $stake['amount'],
            $stake['start_time'],
            $stake['lock_period'],
            $current_time->format('Y-m-d H:i:s')
        );
        file_put_contents('debug_refund.txt', $log_entry, FILE_APPEND);

        // Mark the stake as refunded by setting refund = 1
        $stmt = $pdo->prepare("UPDATE staking SET refund = 1, status = 'completed', last_update = NOW() WHERE id = :stake_id");
        $stmt->execute(['stake_id' => $stake['id']]);
    }

    // Update the last update timestamp to track the time of the last reward calculation
    $stmt = $pdo->prepare("UPDATE staking SET last_update = NOW() WHERE id = :stake_id");
    $stmt->execute(['stake_id' => $stake['id']]);
}
?>
