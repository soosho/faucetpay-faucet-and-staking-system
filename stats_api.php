<?php
require 'includes/db.php'; // Database connection
require 'config/config.php'; // Database connection

// Query to get total withdrawals from the 'withdraw_requests' table
$stmt_withdrawals = $pdo->prepare("SELECT COUNT(*) AS total_withdrawals FROM withdraw_requests");
$stmt_withdrawals->execute();
$total_withdrawals = $stmt_withdrawals->fetchColumn();

// Query to get total deposits from the 'deposit_requests' table
$stmt_deposits = $pdo->prepare("SELECT COUNT(*) AS total_deposits FROM deposit_requests");
$stmt_deposits->execute();
$total_deposits = $stmt_deposits->fetchColumn();

// Query to get total users from the 'users' table
$stmt_users = $pdo->prepare("SELECT COUNT(*) AS total_users FROM users");
$stmt_users->execute();
$total_users = $stmt_users->fetchColumn();

// Return data as JSON
$response = [
    'total_withdrawals' => $total_withdrawals,
    'total_deposits' => $total_deposits,
    'total_users' => $total_users
];

header('Content-Type: application/json');
echo json_encode($response);
