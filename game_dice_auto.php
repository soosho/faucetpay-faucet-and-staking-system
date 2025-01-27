<?php
// game_dice_auto.php
// Set timezone
date_default_timezone_set('Asia/Bangkok');

// Start session if not started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require 'config/config.php';  // Include configuration
require 'includes/db.php';  // Include database connection

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Fetch user balance
$stmt = $pdo->prepare('SELECT trx_balance FROM users WHERE id = :user_id');
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$trx_balance = $user ? (float)$user['trx_balance'] : 0.0;

function calculateProfit($bet_amount, $multiplier, $is_win) {
    return $is_win ? round(($bet_amount * $multiplier) - $bet_amount, 8) : round(-$bet_amount, 8);
}

function getMultiplier($win_chance) {
    return round(99 / $win_chance, 4);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'roll') {
    $bet_amount = (float)$_POST['bet_amount'];
    $range_end = (int)$_POST['range_end'];
    $client_seed = $_POST['client_seed'] ?? '';
    $roll_type = $_POST['roll_type'] ?? 'under';
    $auto_rolls = isset($_POST['auto_rolls']) ? (int)$_POST['auto_rolls'] : 1;
    $on_win_adjust = isset($_POST['on_win_adjust']) ? (float)$_POST['on_win_adjust'] : 1.0;
    $on_loss_adjust = isset($_POST['on_loss_adjust']) ? (float)$_POST['on_loss_adjust'] : 1.0;
    $stop_profit = isset($_POST['stop_profit']) ? (float)$_POST['stop_profit'] : 0;
    $stop_loss = isset($_POST['stop_loss']) ? (float)$_POST['stop_loss'] : 0;

    // Calculate multiplier
    $multiplier = getMultiplier($roll_type === 'under' ? ($range_end + 1) : (100 - $range_end));
    $total_profit = 0;
    $roll_count = 0;
    $roll_results = [];

    // Perform the auto rolls one by one
    for ($i = 0; $i < $auto_rolls; $i++) {
        if ($bet_amount < 0.01 || $bet_amount > $trx_balance) {
            break;  // Stop if the bet is invalid or balance is insufficient.
        }

        $server_seed = bin2hex(random_bytes(16));
        $combined_seed = $server_seed . $client_seed;
        $hash_result = hash('sha256', $combined_seed);
        $dice_roll = hexdec(substr($hash_result, 0, 4)) % 100;

        $is_win = ($roll_type === 'under' && $dice_roll <= $range_end) || ($roll_type === 'over' && $dice_roll >= $range_end);
        $profit = calculateProfit($bet_amount, $multiplier, $is_win);

        $trx_balance += $profit;
        $total_profit += $profit;
        $roll_count++;
        $roll_results[] = [
            'dice_roll' => $dice_roll,
            'result' => $is_win ? 'win' : 'lose',
            'profit' => number_format($profit, 8),
            'bet_amount' => number_format($bet_amount, 2)
        ];

        // Adjust bet amount for next roll based on win or loss
        $bet_amount = $is_win ? $bet_amount * $on_win_adjust : $bet_amount * $on_loss_adjust;

        // Stop if profit or loss limit is reached
        if ($stop_profit > 0 && $total_profit >= $stop_profit) break;
        if ($stop_loss > 0 && $total_profit <= -$stop_loss) break;

        // Add a 1-second delay between rolls in auto mode
        sleep(1);
    }

    // Update the user's balance in the database
    $stmt = $pdo->prepare('UPDATE users SET trx_balance = :new_balance WHERE id = :user_id');
    $stmt->execute(['new_balance' => $trx_balance, 'user_id' => $user_id]);

    // Output the result
    echo json_encode([
        'new_balance' => number_format($trx_balance, 8),
        'roll_count' => $roll_count,
        'total_profit' => number_format($total_profit, 8),
        'roll_results' => array_reverse($roll_results) // Show latest roll results first
    ]);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tron Dice Game - Auto Mode</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #f5f5f5; }
        .container { max-width: 800px; margin-top: 50px; }
        .card { margin-bottom: 20px; }
        .btn-primary { background-color: #6576FF; border: none; }
        .btn-primary:hover { background-color: #2c3782; }
        .rolling-number { font-size: 3rem; color: #2c3782; text-align: center; margin: 20px 0; }
    </style>
</head>
<body>
    <div class="container">
        <h1 class="text-center">Tron Dice Game - Auto Mode</h1>
        <p>Balance: <strong id="balance"><?= number_format($trx_balance, 8) ?> TRX</strong></p>
        <a href="game_dice.php" class="btn btn-secondary">Switch to Manual Mode</a>

        <div class="card p-3">
            <form id="auto-form">
                <div class="mb-3">
                    <label for="bet_amount" class="form-label">Bet Amount (TRX):</label>
                    <input type="number" step="0.01" min="0.01" class="form-control" id="bet_amount" name="bet_amount" value="1" required>
                </div>
                <div class="mb-3">
                    <label for="range_end" class="form-label">Select Range (00-99):</label>
                    <input type="range" min="1" max="99" class="form-range" id="range_end" name="range_end" value="49" oninput="updateMultiplier()">
                    <p>Range End: <span id="range_end_display">49</span></p>
                    <p>Multiplier: <span id="multiplier_display">2.00x</span></p>
                    <p>Chance of Winning: <span id="win_chance_display">50.00%</span></p>
                    <p>Payout Preview: <span id="payout_preview">2.0000 TRX</span></p>
                </div>
                <div class="mb-3">
                    <label for="roll_type" class="form-label">Roll Type:</label>
                    <select class="form-select" id="roll_type" name="roll_type" onchange="updateMultiplier()">
                        <option value="under">Roll Under</option>
                        <option value="over">Roll Over</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="auto_rolls" class="form-label">Number of Auto Rolls:</label>
                    <input type="number" min="1" class="form-control" id="auto_rolls" name="auto_rolls" value="5" required>
                </div>
                <div class="mb-3">
                    <label for="on_win_adjust" class="form-label">On Win Adjustment (Multiplier):</label>
                    <input type="number" step="0.01" class="form-control" id="on_win_adjust" name="on_win_adjust" value="1.0" required>
                </div>
                <div class="mb-3">
                    <label for="on_loss_adjust" class="form-label">On Loss Adjustment (Multiplier):</label>
                    <input type="number" step="0.01" class="form-control" id="on_loss_adjust" name="on_loss_adjust" value="1.0" required>
                </div>
                <div class="mb-3">
                    <label for="stop_profit" class="form-label">Stop After Profit (TRX):</label>
                    <input type="number" step="0.01" class="form-control" id="stop_profit" name="stop_profit" value="0">
                </div>
                <div class="mb-3">
                    <label for="stop_loss" class="form-label">Stop After Loss (TRX):</label>
                    <input type="number" step="0.01" class="form-control" id="stop_loss" name="stop_loss" value="0">
                </div>
                <button type="button" class="btn btn-primary" onclick="startAutoRoll()">Start Auto Roll</button>
            </form>
        </div>

        <div class="card p-3">
            <h4>Result</h4>
            <div id="result"></div>
        </div>

        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
        <script>
            let rollingNumberInterval;

            function updateMultiplier() {
                const rangeEnd = parseInt(document.getElementById('range_end').value);
                const rollType = document.getElementById('roll_type').value;
                const winChance = rollType === 'under' ? (rangeEnd + 1) : (100 - rangeEnd);
                const multiplier = (99 / winChance).toFixed(4);
                document.getElementById('range_end_display').textContent = rangeEnd;
                document.getElementById('multiplier_display').textContent = `${multiplier}x`;
                document.getElementById('win_chance_display').textContent = `${winChance.toFixed(2)}%`;
                const betAmount = parseFloat(document.getElementById('bet_amount').value);
                const payoutPreview = (betAmount * multiplier).toFixed(4);
                document.getElementById('payout_preview').textContent = `${payoutPreview} TRX`;
            }

            async function startAutoRoll() {
                const autoRolls = parseInt(document.getElementById('auto_rolls').value);
                for (let i = 0; i < autoRolls; i++) {
                    await rollDice();
                    await new Promise(resolve => setTimeout(resolve, 1000));
                }
            }

            async function rollDice() {
                const formData = new FormData(document.getElementById('auto-form'));
                formData.append('action', 'roll');
                try {
                    const response = await fetch('', { method: 'POST', body: formData });
                    const data = await response.json();
                    if (data.error) {
                        alert(data.error);
                    } else {
                        document.getElementById('balance').textContent = `${data.new_balance} TRX`;
                        displayRollResults(data.roll_results, data.total_profit);
                    }
                } catch (error) {
                    console.error('Error:', error);
                }
            }

            function displayRollResults(rollResults, totalProfit) {
                const resultContainer = document.getElementById('result');
                resultContainer.innerHTML = `<p>Total Profit/Loss: ${totalProfit} TRX</p>`;
                rollResults.forEach(result => {
                    const resultDiv = document.createElement('div');
                    resultDiv.innerHTML = `Roll: ${result.dice_roll}, Result: ${result.result}, Profit: ${result.profit} TRX, Bet: ${result.bet_amount} TRX`;
                    resultContainer.prepend(resultDiv);
                });
            }
        </script>
    </div>
</body>
</html>
