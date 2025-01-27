
<?php
include 'headerlogged.php';
?>
<?php
// Fetch user's email and balances
$user_id = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT email, btc_balance, eth_balance, doge_balance, ltc_balance, bch_balance, dash_balance, dgb_balance, trx_balance, usdt_balance, fey_balance, zec_balance, bnb_balance, sol_balance, xrp_balance, matic_balance, ada_balance, ton_balance, xlm_balance, usdc_balance, xmr_balance, tara_balance FROM users WHERE id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch();
$message = '';
// Ensure the user has an email
if (empty($user['email'])) {
    echo "No email found. Please update your profile.";
    exit();
}

$user_email = $user['email'];  // Use the registered email for withdrawal
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $user_id = $_SESSION['user_id'];
    $amount = $_POST['amount'];
    $currency = $_POST['currency'];
    $user_email = $user['email'];  // Use the registered email for withdrawal

    // Validate the amount and check minimum withdrawal limit
    if (!is_numeric($amount) || $amount <= 0) {
        $message = '<div class="alert alert-danger">Invalid amount. Please enter a positive number.</div>';
    } elseif ($amount < 0.00000010) {
        $message = '<div class="alert alert-danger">The minimum withdrawal amount is 0.00000010 ' . strtoupper($currency) . '.</div>';
    } else {
        // Fetch the user's balance for the selected currency
        $currency_column = $currency . '_balance';
        // Check if the balance key exists in the user array
if (isset($user[$currency_column])) {
    $user_balance = $user[$currency_column];
} else {
    $user_balance = 0; // Default to 0 if the balance is not set
}

// Check if the user has enough balance to withdraw
if ($amount > $user_balance) {
    $message = '<div class="alert alert-danger">Insufficient balance.</div>';
} else {
    // Call FaucetPay API to process the withdrawal
    $withdraw_status = withdraw_funds($user_email, $user_id, $amount, $currency);

    if ($withdraw_status === true) {
        $message = '<div class="alert alert-success">Your withdrawal has been processed. You can check <a href="https://faucetminehub.com/history">History</a> for your withdrawal status.</div>';
    } else {
        $message = '<div class="alert alert-danger">Withdrawal failed. Please try again.</div>';
    }
}
    }
}

$currency_map = [
    'btc' => ['name' => 'Bitcoin (BTC)', 'icon' => 'btc', 'balance_column' => 'btc_balance'],
    'eth' => ['name' => 'Ethereum (ETH)', 'icon' => 'eth', 'balance_column' => 'eth_balance'],
    'doge' => ['name' => 'Dogecoin (DOGE)', 'icon' => 'doge', 'balance_column' => 'doge_balance'],
    'ltc' => ['name' => 'Litecoin (LTC)', 'icon' => 'ltc', 'balance_column' => 'ltc_balance'],
    'bch' => ['name' => 'Bitcoin Cash (BCH)', 'icon' => 'bch', 'balance_column' => 'bch_balance'],
    'dash' => ['name' => 'Dash (DASH)', 'icon' => 'dash', 'balance_column' => 'dash_balance'],
    'dgb' => ['name' => 'Digibyte (DGB)', 'icon' => 'dgb', 'balance_column' => 'dgb_balance'],
    'trx' => ['name' => 'Tron (TRX)', 'icon' => 'trx', 'balance_column' => 'trx_balance'],
    'usdt' => ['name' => 'Tether (USDT)', 'icon' => 'usdt', 'balance_column' => 'usdt_balance'],
    'fey' => ['name' => 'Feyorra (FEY)', 'icon' => 'fey', 'balance_column' => 'fey_balance'],
    'zec' => ['name' => 'Zcash (ZEC)', 'icon' => 'zec', 'balance_column' => 'zec_balance'],
    'bnb' => ['name' => 'Binance Coin (BNB)', 'icon' => 'bnb', 'balance_column' => 'bnb_balance'],
    'sol' => ['name' => 'Solana (SOL)', 'icon' => 'sol', 'balance_column' => 'sol_balance'],
    'xrp' => ['name' => 'Ripple (XRP)', 'icon' => 'xrp', 'balance_column' => 'xrp_balance'],
    'matic' => ['name' => 'Polygon (MATIC)', 'icon' => 'matic', 'balance_column' => 'matic_balance'],
    'ada' => ['name' => 'Cardano (ADA)', 'icon' => 'ada', 'balance_column' => 'ada_balance'],
    'ton' => ['name' => 'Toncoin (TON)', 'icon' => 'ton', 'balance_column' => 'ton_balance'],
    'xlm' => ['name' => 'Stellar (XLM)', 'icon' => 'xlm', 'balance_column' => 'xlm_balance'],
    'usdc' => ['name' => 'USD Coin (USDC)', 'icon' => 'usdc', 'balance_column' => 'usdc_balance'],
    'xmr' => ['name' => 'Monero (XMR)', 'icon' => 'xmr', 'balance_column' => 'xmr_balance'],
    'tara' => ['name' => 'Taraxa (TARA)', 'icon' => 'tara', 'balance_column' => 'tara_balance']
];

// Collect user balances for each currency
$user_balances = [];
foreach ($currency_map as $currency_code => $currency_data) {
    $user_balances[$currency_code] = number_format($user[$currency_data['balance_column']], 8);
}
?>
<!-- content @s -->
<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-lg">
        <center>
</center>
        <div class="nk-content-body">
            <div class="buysell wide-xs m-auto">
                <div class="buysell-title text-center">
                    <center><p><div id="ad_unit" data-unit-id="6624"></div><script src="https://api.fpadserver.com/static/js/advert_renderer.js"></script></p></center>
                    <p></p>
                    <h2 class="title">Withdraw Funds</h2>
                    <p>Withdrawing your earnings is easy, fast, and secure. Our system allows for instant withdrawals through FaucetPay using your registered email.</p>
                    <p><a href="https://faucetminehub.com/docs/withdraw.html" target="_blank">Click here to read more details about the withdraw system</a></p>
                </div><!-- .buysell-title -->
                <div class="buysell-block">
                    <?php if (!empty($message)) echo $message; ?>
                    <form method="POST" class="buysell-form">
                        <div class="buysell-field form-group">
                            <div class="form-label-group">
                                <label class="form-label">Select Currency:</label>
                            </div>
                            <input type="hidden" value="BTC" name="currency" id="buysell-choose-currency"> <!-- This will store the selected cryptocurrency -->
                            <div class="dropdown buysell-cc-dropdown">
                                <a href="#" class="buysell-cc-choosen dropdown-indicator" data-bs-toggle="dropdown" id="dropdown-toggle">
    <div class="coin-item coin-btc">
        <div class="coin-icon" id="selected-coin-icon">
            <img src="images/coins/fmh.svg" alt="fmh Icon" width="32" height="32">
        </div>
        <div class="coin-info">
            <span class="coin-name" id="selected-coin-name">Select Currency</span>
            <span class="coin-text" id="selected-coin-balance">
                Your Balance Will shown here
            </span>
        </div>
    </div>
</a>

                                <div class="dropdown-menu dropdown-menu-auto dropdown-menu-mxh">
                                    <ul class="buysell-cc-list">
                                        <?php foreach ($currency_map as $currency_code => $currency_data): ?>
                                            <?php if ($user[$currency_data['balance_column']] > 0): ?>
                                                <li class="buysell-cc-item" onclick="selectCurrency('<?php echo strtoupper($currency_code); ?>', '<?php echo $currency_data['name']; ?>', '<?php echo $currency_data['icon']; ?>', '<?php echo $user_balances[$currency_code]; ?>')">
                                                    <div class="coin-item coin-<?php echo $currency_data['icon']; ?>">
                                                        <div class="coin-icon">
                                                            <img src="assets/icons/<?php echo $currency_data['icon']; ?>.svg" alt="<?php echo $currency_data['icon']; ?> Icon" width="32" height="32">
                                                        </div>
                                                        <div class="coin-info">
                                                            <span class="coin-name"><?php echo $currency_data['name']; ?></span>
                                                            <span class="coin-text"><?php echo $user_balances[$currency_code]; ?></span>
                                                        </div>
                                                    </div>
                                                </li>
                                            <?php endif; ?>
                                        <?php endforeach; ?>
                                    </ul>
                                </div><!-- .dropdown-menu -->
                            </div><!-- .dropdown -->
                        </div><!-- .buysell-field -->

                        <div class="buysell-field form-group">
                            <div class="form-label-group">
                                <label class="form-label" for="amount">Amount to Withdraw (<a href="#" id="min-amount">min: 0.00000010</a>):</label>
                                <span class="buysell-rate form-note-alt">2% Withdraw Fee</span>
                            </div>
                            <div class="form-control-group">
                                <input type="number" class="form-control form-control-lg form-control-number" id="amount" name="amount" placeholder="Enter withdrawal amount" required min="0.00000010" step="0.00000010">
                                <div class="form-dropdown">
                                    <div id="currency-label">BTC</div>
                                </div>
                            </div>
                        </div><!-- .buysell-field -->
<center><div id="ad_unit" data-unit-id="6624"></div><script src="https://api.fpadserver.com/static/js/advert_renderer.js"></script></center>
                        <div class="buysell-field form-action">
                            <button type="submit" class="btn btn-lg btn-block btn-primary">Proceed to Withdraw</button>
                        </div><!-- .buysell-field -->
                    </form><!-- .buysell-form -->
                </div><!-- .buysell-block -->
            </div><!-- .buysell -->
        </div>
    </div>
</div>
<!-- content @e -->

<script>
function selectCurrency(currency, displayName, iconName, balance) {
    document.getElementById('buysell-choose-currency').value = currency.toLowerCase();
    document.getElementById('selected-coin-name').innerText = displayName;
    document.getElementById('currency-label').innerText = currency;
    document.getElementById('selected-coin-balance').innerText = balance;

    // Change the icon dynamically
    var iconElement = document.getElementById('selected-coin-icon');
    iconElement.innerHTML = '<img src="assets/icons/' + iconName + '.svg" alt="' + displayName + ' Icon" width="32" height="32">';

    // Close the dropdown after selecting the currency
    var dropdownToggle = document.getElementById('dropdown-toggle');
    var dropdown = new bootstrap.Dropdown(dropdownToggle);
    dropdown.hide();
}

// Add click event listener for the minimum amount to auto-fill the amount field
document.getElementById('min-amount').addEventListener('click', function(e) {
    e.preventDefault(); // Prevent the default link behavior
    document.getElementById('amount').value = '0.00000010'; // Auto-fill with the minimum amount
});
</script>

<?php
include 'footerlogged.php';
?>
