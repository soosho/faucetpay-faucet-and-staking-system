<?php
// config/config.php

return [
    'db' => [
        'host' => 'localhost',
        'dbname' => '###',
        'user' => '###',
        'password' => '###',
        'charset' => 'utf8mb4',
    ],
    'faucetpay' => [
        'api_key' => 'YOUR_API_KEY',
        'username' => 'wepayou',
        'callback_url' => 'https://faucetminehub.com/payment_callback',
        'success_url' => 'https://faucetminehub.com/success',
        'cancel_url' => 'https://faucetminehub.com/cancel',
    ],
    'smtp' => [
        'host' => '###',
        'username' => '###',
        'password' => '###',
        'port' => 587,  // Adjust this based on your SMTP provider
        'encryption' => 'tls',  // Use 'ssl' or 'tls' based on your SMTP server
        'from_email' => 'USERNAME',  // Sender email address
        'from_name' => 'FaucetMineHub',  // Sender name
    ],
    'fees' => [
        'withdraw_fee' => 0.01,  // 1% withdraw fee
        'deposit_fee' => 0.005,  // 0.5% deposit fee
    ],
    'limits' => [
        'minimum_withdrawal' => 0.00000001,  // Minimum withdrawal amount
        'maximum_withdrawal' => 10.0,        // Maximum withdrawal limit
    ],
    'app' => [
        'name' => 'FaucetMineHub',
        'url' => 'https://faucetminehub.com',
        'author' => 'Melons',
        'description' => 'Free Staking and Faucet',
        'favicon_url' => 'https://faucetminehub.com/images/favicon.png', // Example favicon URL
        'logo_url' => 'https://faucetminehub.com/images/logo.png', // Example logo URL
    ],
    'staking' => [
        'reward_rates' => [
            7  => 0.01,  // 30 days lock period, 0.2% daily reward
            30  => 0.2,  // 30 days lock period, 0.2% daily reward
            60  => 0.3,  // 60 days lock period, 0.3% daily reward
            90  => 0.5,  // 90 days lock period, 0.5% daily reward
            180 => 0.7,  // 180 days lock period, 0.7% daily reward
            365 => 1.0,  // 365 days lock period, 1.0% daily reward
        ],
        'minimum_staking_amount' => 0.001,  // Minimum amount to start staking
        'compounding' => true,  // Enable/Disable compounding of rewards
    ],
    'daily_claim' => [
        'usd_amount' => 0.0005,  // The USD value of the daily claim for all users
        'max_claims_per_day' => 100,  // Maximum number of claims per 24 hours
    ],
];
