<?php
require 'includes/db.php';
$config = require 'config/config.php'; // Load the config file
date_default_timezone_set('Asia/Bangkok');  // This sets GMT+7 timezone
// Get current date
$current_date = new DateTime();

// Fetch all active stakes
$stmt = $pdo->prepare("SELECT * FROM staking WHERE status = 'active'");
$stmt->execute();
$stakes = $stmt->fetchAll();

foreach ($stakes as $stake) {
    // Calculate how many days have passed since the staking started
    $start_time = new DateTime($stake['start_time']);
    $days_staked = $start_time->diff($current_date)->days;

    // Fetch the reward rate based on the lock period from config
    $lock_period = $stake['lock_period'];
    if (isset($config['staking']['reward_rates'][$lock_period])) {
        $daily_reward_rate = $config['staking']['reward_rates'][$lock_period];
    } else {
        // Skip stake if no reward rate is defined for the lock period
        continue;
    }

    // Ensure the stake is still within the lock period
    if ($days_staked <= $stake['lock_period']) {
        // Calculate the daily reward based on the dynamic reward rate
        $daily_reward = ($stake['amount'] * $daily_reward_rate) / 100;

        // Update user's balance with the daily reward
        $currency = $stake['currency'] . '_balance'; // e.g., 'btc_balance', 'eth_balance'
        $stmt = $pdo->prepare("UPDATE users SET {$currency} = {$currency} + :reward WHERE id = :user_id");
        $stmt->execute([
            'reward' => $daily_reward,
            'user_id' => $stake['user_id']
        ]);

        // Optionally, log the reward in a separate table for record-keeping
        $stmt = $pdo->prepare("INSERT INTO staking_rewards (user_id, stake_id, reward_amount, reward_date) 
                               VALUES (:user_id, :stake_id, :reward_amount, NOW())");
        $stmt->execute([
            'user_id' => $stake['user_id'],
            'stake_id' => $stake['id'],
            'reward_amount' => $daily_reward
        ]);
    } else {
        // Mark the stake as completed once the lock period is over
        $stmt = $pdo->prepare("UPDATE staking SET status = 'completed' WHERE id = :stake_id");
        $stmt->execute(['stake_id' => $stake['id']]);
    }
}
