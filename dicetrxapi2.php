<?php
date_default_timezone_set('Asia/Bangkok');
session_start();

require 'includes/db.php';
require 'config/config.php';

$response = [
    'success' => false,
    'message' => '',
    'data' => []
];

// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    $response['message'] = 'Please log in to play the game.';
    echo json_encode($response);
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch the user's TRX balance
$stmt = $pdo->prepare('SELECT trx_balance FROM users WHERE id = :user_id');
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    $response['message'] = 'User not found.';
    echo json_encode($response);
    exit();
}

$trx_balance = (float) $user['trx_balance'];

// Handle bet request
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bet_amount = isset($_POST['bet_amount']) ? (float) $_POST['bet_amount'] : 0;
    $range_start = isset($_POST['range_start']) ? (int) $_POST['range_start'] : 0;
    $range_end = isset($_POST['range_end']) ? (int) $_POST['range_end'] : 9999;
    $auto_rolls = isset($_POST['auto_rolls']) ? (int) $_POST['auto_rolls'] : 1;
    $on_win_adjustment = isset($_POST['on_win_adjustment']) ? (float) $_POST['on_win_adjustment'] : 1.0;
    $on_loss_adjustment = isset($_POST['on_loss_adjustment']) ? (float) $_POST['on_loss_adjustment'] : 1.0;
    $stop_profit = isset($_POST['stop_profit']) ? (float) $_POST['stop_profit'] : 0;
    $stop_loss = isset($_POST['stop_loss']) ? (float) $_POST['stop_loss'] : 0;

    if ($bet_amount <= 0.01) {
        $response['message'] = 'The bet amount must be greater than 0.01 TRX.';
    } elseif ($range_start < 0 || $range_end > 9999 || $range_start >= $range_end) {
        $response['message'] = 'Invalid range selection.';
    } elseif ($bet_amount > $trx_balance) {
        $response['message'] = 'Insufficient balance.';
    } else {
        $total_win = 0;
        $total_loss = 0;
        $initial_balance = $trx_balance;
        $current_bet_amount = $bet_amount;

        for ($i = 0; $i < $auto_rolls; $i++) {
            // Roll the dice (generate a number between 0 and 9999)
            $dice_roll = rand(0, 9999);
            $is_win = $dice_roll >= $range_start && $dice_roll <= $range_end;

            // Calculate win/loss and update balance
            if ($is_win) {
                $payout = $current_bet_amount * (10000 / ($range_end - $range_start + 1));
                $total_win += $payout;
                $current_bet_amount *= $on_win_adjustment; // Adjust bet amount after win
            } else {
                $total_loss += $current_bet_amount;
                $current_bet_amount *= $on_loss_adjustment; // Adjust bet amount after loss
            }

            // Check for profit/loss stop conditions
            $net_profit = $total_win - $total_loss;
            if ($stop_profit > 0 && $net_profit >= $stop_profit) {
                break;
            }
            if ($stop_loss > 0 && abs($net_profit) >= $stop_loss) {
                break;
            }
        }

        // Update user balance
        $new_balance = $trx_balance + $total_win - $total_loss;
        $stmt = $pdo->prepare('UPDATE users SET trx_balance = :new_balance WHERE id = :user_id');
        $stmt->execute(['new_balance' => $new_balance, 'user_id' => $user_id]);

        $response['success'] = true;
        $response['message'] = 'Roll completed.';
        $response['data'] = [
            'new_balance' => number_format($new_balance, 8),
            'total_win' => number_format($total_win, 8),
            'total_loss' => number_format($total_loss, 8),
            'dice_roll' => $dice_roll,
            'is_win' => $is_win,
            'net_profit' => number_format($net_profit, 8)
        ];
    }

    echo json_encode($response);
    exit();
}
?>
