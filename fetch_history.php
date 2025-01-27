<?php
// Set the timezone to GMT+7 globally
date_default_timezone_set('Asia/Bangkok');

// Only start session if it isn't already active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require 'config/config.php'; // Load configuration settings
require 'includes/db.php';  // Database connection

header('Content-Type: application/json');


if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // Fetch the last 10 bets from the current user's betting history
    $stmt = $pdo->prepare("SELECT bet_amount, bet_choice, currency, client_seed, server_seed, dice_roll, result, amount_change FROM betting_history WHERE user_id = :user_id ORDER BY id DESC LIMIT 10");
    $stmt->execute(['user_id' => $user_id]);
    $user_bets = $stmt->fetchAll(PDO::FETCH_ASSOC);


    // Fetch the last 10 bets from all users
    $stmt = $pdo->prepare("SELECT bh.bet_amount, bh.bet_choice, bh.currency, bh.client_seed, bh.server_seed, bh.dice_roll, bh.result, bh.amount_change, u.username 
                           FROM betting_history bh 
                           JOIN users u ON bh.user_id = u.id 
                           ORDER BY bh.id DESC LIMIT 10");
    $stmt->execute();
    $all_bets = $stmt->fetchAll(PDO::FETCH_ASSOC);


    echo json_encode([
        'user_bets' => $user_bets,
        'all_bets' => $all_bets
    ]);
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
