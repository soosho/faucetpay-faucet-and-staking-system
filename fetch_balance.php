<?php
// Set the timezone to GMT+7 globally
date_default_timezone_set('Asia/Bangkok');

// Only start session if it isn't already active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require 'config/config.php';  // Load configuration settings
require 'includes/db.php';  // Database connection

header('Content-Type: application/json');

if (!isset($_SESSION['user_id'])) {
    echo json_encode(['error' => 'User not logged in']);
    exit();
}

$user_id = $_SESSION['user_id'];

try {
    // Fetch the user's TRX balance
    $stmt = $pdo->prepare('SELECT trx_balance FROM users WHERE id = :user_id');
    $stmt->execute(['user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        echo json_encode(['trx_balance' => number_format($user['trx_balance'], 8)]);
    } else {
        echo json_encode(['error' => 'User not found']);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
