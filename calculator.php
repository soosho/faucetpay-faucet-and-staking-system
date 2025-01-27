<?php
session_start();

require 'config/config.php';  // Load the config file
require 'includes/functions.php';  // Any helper functions you need
require 'includes/db.php';  // Database connection

// Function to calculate the reward rate based on the lock period
function get_reward_rate($days, $config) {
    if ($days >= 365) {
        return $config['staking']['reward_rates'][365]; // 2.5% for 365 days or more
    } elseif ($days >= 180) {
        return $config['staking']['reward_rates'][180]; // 1.0% for 180 days or more
    } elseif ($days >= 90) {
        return $config['staking']['reward_rates'][90];  // 0.5% for 90 days or more
    } elseif ($days >= 60) {
        return $config['staking']['reward_rates'][60];  // 0.3% for 60 days or more
    } elseif ($days >= 30) {
        return $config['staking']['reward_rates'][30];  // 0.2% for 30 days or more
    } elseif ($days >= 7) {
        return $config['staking']['reward_rates'][7];   // 0.01% for 7 days or more
    } else {
        return 0; // No reward for periods less than 7 days
    }
}

// If the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $amount = (float)$_POST['amount'];  // The amount to stake
    $currency = $_POST['currency'];  // The selected cryptocurrency
    $lock_period = (int)$_POST['lock_period'];  // Lock period in days

    // Get the reward rate based on the lock period
    $daily_reward_rate = get_reward_rate($lock_period, $config);

    // Calculate rewards
    $daily_reward_amount = ($amount * $daily_reward_rate) / 100;
    $minute_reward_amount = $daily_reward_amount / (24 * 60);  // Reward per minute
    $weekly_reward_amount = $daily_reward_amount * 7;  // 7 days in a week
    $monthly_reward_amount = $daily_reward_amount * 30;  // Approx 30 days in a month
    $total_reward_amount = $daily_reward_amount * $lock_period;  // Total reward for the entire lock period

    // Total amount after the lock period (includes staked amount and rewards)
    $total_amount_after_stake = $amount + $total_reward_amount;

    // Output the results
    $result = [
        'amount' => $amount,
        'currency' => strtoupper($currency),
        'lock_period' => $lock_period,
        'daily_reward_rate' => $daily_reward_rate,
        'daily_reward_amount' => $daily_reward_amount,
        'minute_reward_amount' => $minute_reward_amount,
        'weekly_reward_amount' => $weekly_reward_amount,
        'monthly_reward_amount' => $monthly_reward_amount,
        'total_reward_amount' => $total_reward_amount,
        'total_after_stake' => $total_amount_after_stake,
    ];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Staking Reward Calculator</title>
    <center><p><div id="ad_unit" data-unit-id="6624"></div><script src="https://api.fpadserver.com/static/js/advert_renderer.js"></script></p></center>
    <style>
        /* Global Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f7f9;
            margin: 0;
            padding: 20px;
        }

        .container {
            max-width: 800px;
            margin: 0 auto;
            background-color: #ffffff;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            padding: 20px;
        }

        h4 {
            font-size: 24px;
            text-align: center;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
        }

        .form-group {
            width: 100%;
        }

        label {
            font-weight: bold;
            margin-bottom: 5px;
            display: block;
        }

        input[type="number"],
        select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            outline: none;
            transition: border-color 0.3s ease;
        }

        input[type="number"]:focus,
        select:focus {
            border-color: #5e72e4;
        }

        button {
            width: 100%;
            padding: 12px;
            background-color: #5e72e4;
            border: none;
            border-radius: 5px;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #324cdd;
        }

        .table-responsive {
            margin-top: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        table th,
        table td {
            padding: 12px 15px;
            border: 1px solid #ddd;
        }

        table th {
            background-color: #f4f7f9;
            font-weight: bold;
        }

        table td {
            text-align: center;
        }

        .result-box {
            padding: 20px;
            background-color: #e9f1ff;
            border: 1px solid #5e72e4;
            border-radius: 5px;
            margin-top: 20px;
        }

        .result-box h5 {
            text-align: center;
            margin-bottom: 10px;
        }

    </style>
</head>
<body>

<div class="container">
    <h4>Staking Reward Calculator</h4>
    <form method="POST" class="form-validate">
        <!-- Amount to Stake -->
        <div class="form-group">
            <label class="form-label" for="amount">Amount to Stake:</label>
            <input type="number" step="0.00000001" name="amount" id="amount" placeholder="Enter amount" required>
        </div>

        <!-- Select Cryptocurrency -->
        <div class="form-group">
            <label class="form-label" for="currency">Select Cryptocurrency:</label>
            <select name="currency" id="currency" required>
                <option value="btc">Bitcoin (BTC)</option>
                <option value="eth">Ethereum (ETH)</option>
                <option value="doge">Dogecoin (DOGE)</option>
                <option value="ltc">Litecoin (LTC)</option>
                <option value="bch">Bitcoin Cash (BCH)</option>
                <option value="dash">Dash (DASH)</option>
                <option value="dgb">Digibyte (DGB)</option>
                <option value="trx">Tron (TRX)</option>
                <option value="usdt">Tether (USDT)</option>
                <option value="fey">Feyorra (FEY)</option>
                <option value="zec">Zcash (ZEC)</option>
                <option value="bnb">Binance Coin (BNB)</option>
                <option value="sol">Solana (SOL)</option>
                <option value="xrp">Ripple (XRP)</option>
                <option value="matic">Polygon (MATIC)</option>
                <option value="ada">Cardano (ADA)</option>
                <option value="ton">Toncoin (TON)</option>
                <option value="xlm">Stellar (XLM)</option>
                <option value="usdc">USD Coin (USDC)</option>
                <option value="xmr">Monero (XMR)</option>
                <option value="tara">Taraxa (TARA)</option>
            </select>
        </div>

        <!-- Lock Period -->
        <div class="form-group">
            <label class="form-label" for="lock_period">Lock Period (in days):</label>
            <select name="lock_period" id="lock_period" required>
                <option value="" disabled selected>Select lock period</option>
                <option value="7">7 days (0.01% daily reward)</option>
                <option value="30">30 days (0.2% daily reward)</option>
                <option value="60">60 days (0.3% daily reward)</option>
                <option value="90">90 days (0.5% daily reward)</option>
                <option value="180">180 days (0.7% daily reward)</option>
                <option value="365">365 days (1.0% daily reward)</option>
            </select>
        </div>

        <!-- Submit Button -->
        <div class="form-group">
            <button type="submit">Calculate Rewards</button>
        </div>
        
    </form>
<br>
        <center><a href="dashboard">Click here to go back</a></center>
    <!-- Display Calculation Results -->
    <?php if (isset($result)): ?>
        <div class="result-box">
            <h5>Calculation Results</h5>
            <table>
                <tr>
                    <td><strong>Amount Staked:</strong></td>
                    <td><?php echo htmlspecialchars($result['amount']); ?> <?php echo htmlspecialchars($result['currency']); ?></td>
                </tr>
                <tr>
                    <td><strong>Lock Period:</strong></td>
                    <td><?php echo htmlspecialchars($result['lock_period']); ?> days</td>
                </tr>
                <tr>
                    <td><strong>Daily Reward Rate:</strong></td>
                    <td><?php echo htmlspecialchars($result['daily_reward_rate']); ?>%</td>
                </tr>
                <tr>
                    <td><strong>Per Minute Reward:</strong></td>
                    <td><?php echo number_format($result['minute_reward_amount'], 8); ?> <?php echo htmlspecialchars($result['currency']); ?></td>
                </tr>
                <tr>
                    <td><strong>Daily Reward:</strong></td>
                    <td><?php echo number_format($result['daily_reward_amount'], 8); ?> <?php echo htmlspecialchars($result['currency']); ?></td>
                </tr>
                <tr>
                    <td><strong>Weekly Reward:</strong></td>
                    <td><?php echo number_format($result['weekly_reward_amount'], 8); ?> <?php echo htmlspecialchars($result['currency']); ?></td>
                </tr>
                <tr>
                    <td><strong>Monthly Reward:</strong></td>
                    <td><?php echo number_format($result['monthly_reward_amount'], 8); ?> <?php echo htmlspecialchars($result['currency']); ?></td>
                </tr>
                <tr>
                    <td><strong>Total Reward (for <?php echo htmlspecialchars($result['lock_period']); ?> days):</strong></td>
                    <td><?php echo number_format($result['total_reward_amount'], 8); ?> <?php echo htmlspecialchars($result['currency']); ?></td>
                </tr>
                <tr>
                    <td><strong>Total After Stake Period (Staked + Rewards):</strong></td>
                    <td><?php echo number_format($result['total_after_stake'], 8); ?> <?php echo htmlspecialchars($result['currency']); ?></td>
                </tr>
            </table>
        </div>
    <?php endif; ?>
</div>

</body>
</html>

