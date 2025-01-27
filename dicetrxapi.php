<?php
// Set the timezone to GMT+7 globally
date_default_timezone_set('Asia/Bangkok');

// Start session if it's not already active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require 'config/config.php';  // Load configuration settings
require 'includes/db.php';  // Database connection

$successMessage = '';
$errorMessage = '';
$expMessage = 'Your experience has been increased by 1.';
// Redirect to login if not logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

// Prepare a query to fetch the username and balance based on the user_id
$stmt = $pdo->prepare('SELECT username, trx_balance, exp FROM users WHERE id = :user_id');
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if a user was found
if ($user) {
    // Retrieve the username and balance from the database result
    $username = htmlspecialchars($user['username'], ENT_QUOTES, 'UTF-8');
    $trx_balance = (float)$user['trx_balance'];
    $exp = (int)$user['exp'];  // Get the user's current experience
} else {
    $errorMessage = 'User not found.';
}

// Chatbox configuration
$secret = "1cKsFI7qogLAYbun";  // Your secret key here
$params = array(
    'boxid' => 953445,
    'boxtag' => '5KfazJ',
    'nme' => $username,
    'lnk' => '',  // Profile URL (optional)
    'pic' => 'https://faucetminehub.com/1485477097-avatar_78580.png'  // Avatar URL
);

// Generate chatbox URL with signature
$arr = array();
foreach ($params as $k => $v) {
    if (!$v) {
        continue;
    }
    $arr[] = $k . '=' . urlencode($v);
}

$path = '/box/?' . implode('&', $arr);
$sig = urlencode(base64_encode(hash_hmac("sha256", $path, $secret, true)));
$chatbox_url = 'https://www5.cbox.ws' . $path . '&sig=' . $sig;

// Generate client and server seeds for the first time visiting the page
if (!isset($_SESSION['client_seed'])) {
    $_SESSION['client_seed'] = bin2hex(random_bytes(8));  // Secure random client seed
}
if (!isset($_SESSION['server_seed'])) {
    $_SESSION['server_seed'] = bin2hex(random_bytes(16));  // Secure random server seed
}

// Set win percentage (default to 35%)
$win_percentage = isset($_POST['win_percentage']) ? (float)$_POST['win_percentage'] : 45;

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bet_amount = isset($_POST['bet_amount']) ? (float)$_POST['bet_amount'] : 0;
    $bet_choice = isset($_POST['bet_choice']) ? strtolower(trim($_POST['bet_choice'])) : '';
    $client_seed = isset($_POST['client_seed']) ? trim($_POST['client_seed']) : '';

    // Set the minimum bet amount to 0.01 TRX
    if ($bet_amount < 0.01) {
        $errorMessage = 'Please enter a valid bet amount of at least 0.01 TRX.';
    } elseif (!in_array($bet_choice, ['low', 'high'])) {
        $errorMessage = 'Please select a valid bet choice (low or high).';
    } elseif ($bet_amount > $trx_balance) {
        $errorMessage = 'You do not have enough balance to place this bet.';
    } elseif (empty($client_seed)) {
        $errorMessage = 'Please provide a client seed.';
    } else {
        // Use existing server seed for verification
        $server_seed = $_SESSION['server_seed'];
        
        // Create a hash of the server seed to display for verification (after the bet)
        $server_seed_hash = hash('sha256', $server_seed);

        // Combine server seed and client seed to determine the outcome
        $combined_seed = $server_seed . $client_seed;
        $hash_result = hash('sha256', $combined_seed);

        // Convert the hash result to a number between 0 and 9999
        $dice_roll = hexdec(substr($hash_result, 0, 4)) % 10000;

        // Adjust win probability based on user-defined win percentage
        $is_win = false;
        $low_range = 4850; // Numbers 0000-4850 are low, 4851-9999 are high

        if ($bet_choice === 'low' && $dice_roll <= $low_range) {
            // If user chose "low" and the roll is in the low range, consider it a potential win
            if (mt_rand(1, 100) <= $win_percentage) {
                $is_win = true;
            }
        } elseif ($bet_choice === 'high' && $dice_roll > $low_range) {
            // If user chose "high" and the roll is in the high range, consider it a potential win
            if (mt_rand(1, 100) <= $win_percentage) {
                $is_win = true;
            }
        }

        // If the user is supposed to lose, adjust the dice roll accordingly
        if (!$is_win) {
            if ($bet_choice === 'low') {
                // Ensure a losing roll for "low"
                $dice_roll = mt_rand($low_range + 1, 9999); // Ensure it's in the "high" range
            } elseif ($bet_choice === 'high') {
                // Ensure a losing roll for "high"
                $dice_roll = mt_rand(0, $low_range); // Ensure it's in the "low" range
            }
        }

        // Calculate new balance and prepare betting history data
        if ($is_win) {
            $profit = $bet_amount * 0.95;  // 95% profit of the bet amount
            $win_amount = $bet_amount + $profit;  // Original bet + profit

            $stmt = $pdo->prepare("UPDATE users SET trx_balance = trx_balance + :amount WHERE id = :user_id");
            $stmt->execute(['amount' => $win_amount, 'user_id' => $user_id]);

            $successMessage = 'Congratulations! You won ' . number_format($profit, 4) . ' TRX. ' . $expMessage;
            $amount_change = '+' . number_format($profit, 4);
        } else {
            $loss_amount = $bet_amount;
            $stmt = $pdo->prepare("UPDATE users SET trx_balance = trx_balance - :amount WHERE id = :user_id");
            $stmt->execute(['amount' => $loss_amount, 'user_id' => $user_id]);

            $errorMessage = 'Sorry, you lost. Better luck next time! ' . $expMessage;
            $amount_change = '-' . number_format($loss_amount, 4);
        }

        // Update the user's experience points (exp) by 1 after each bet
        $stmt = $pdo->prepare("UPDATE users SET exp = exp + 1 WHERE id = :user_id");
        $stmt->execute(['user_id' => $user_id]);

        // Insert betting history
        $stmt = $pdo->prepare("INSERT INTO betting_history (user_id, bet_amount, bet_choice, currency, client_seed, server_seed_hash, dice_roll, result, amount_change) 
                               VALUES (:user_id, :bet_amount, :bet_choice, 'TRX', :client_seed, :server_seed_hash, :dice_roll, :result, :amount_change)");
        $stmt->execute([
            'user_id' => $user_id,
            'bet_amount' => $bet_amount,
            'bet_choice' => $bet_choice,
            'client_seed' => $client_seed,
            'server_seed_hash' => $server_seed_hash,  // Store the hash of the server seed for verification
            'dice_roll' => $dice_roll,
            'result' => $is_win ? 'win' : 'lose',
            'amount_change' => $amount_change  // The actual profit or loss
        ]);

        // Generate new server seed for the next round (not the hash)
        $_SESSION['server_seed'] = bin2hex(random_bytes(16));
    }
}

// Fetch last 10 bets from betting history for the current user
$stmt = $pdo->prepare("SELECT bet_amount, bet_choice, currency, client_seed, server_seed_hash, dice_roll, result, amount_change FROM betting_history WHERE user_id = :user_id ORDER BY id DESC LIMIT 10");
$stmt->execute(['user_id' => $user_id]);
$betting_history = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch last 10 bets from all users with usernames
$stmt = $pdo->prepare("
    SELECT bh.bet_amount, bh.bet_choice, bh.currency, bh.client_seed, bh.server_seed_hash, bh.dice_roll, bh.result, bh.amount_change, u.username 
    FROM betting_history bh 
    JOIN users u ON bh.user_id = u.id 
    ORDER BY bh.id DESC 
    LIMIT 10
");
$stmt->execute();
$all_betting_history = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>
