<?php
include 'headerlogged.php';
?>
<?php

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $currency = $_POST['currency'];
    $amount = $_POST['amount'];
    $lock_period = (int)$_POST['lock_period']; // Lock period in days

    // Minimum staking amounts configuration
    $min_staking_amounts = [
        'btc' => 0.00001,
        'eth' => 0.0001,
        'doge' => 10,
        'ltc' => 0.001,
        'bch' => 0.001,
        'dash' => 0.001,
        'dgb' => 10,
        'trx' => 10,
        'usdt' => 1,
        'fey' => 100,
        'zec' => 0.001,
        'bnb' => 0.0001,
        'sol' => 0.01,
        'xrp' => 0.001,
        'matic' => 1,
        'ada' => 0.001,
        'ton' => 0.001,
        'xlm' => 0.001,
        'usdc' => 1,
        'xmr' => 0.001,
        'tara' => 100,
    ];

    // Get the reward rate based on the lock period
    if (isset($config['staking']['reward_rates'][$lock_period])) {
        $daily_reward_rate = $config['staking']['reward_rates'][$lock_period];
    } else {
        $error_message = "Invalid lock period selected.";
    }

    // Validate currency and minimum staking amount
    if (!isset($min_staking_amounts[$currency])) {
        $error_message = 'Invalid currency selected.';
    } else {
        $min_staking_amount = $min_staking_amounts[$currency];
        if ($amount < $min_staking_amount) {
            $error_message = "The minimum staking amount for " . strtoupper($currency) . " is " . number_format($min_staking_amount, 8) . ".";
        }
    }

    // Check if user has enough balance
    if (!isset($error_message) && $amount > $user[$currency . '_balance']) {
        $error_message = 'Insufficient balance.';
    }

    // If no errors, proceed with staking
    if (!isset($error_message) && isset($daily_reward_rate)) {
        // Deduct staked amount from user's balance
        $stmt = $pdo->prepare("UPDATE users SET {$currency}_balance = {$currency}_balance - :amount WHERE id = :user_id");
        $stmt->execute(['amount' => $amount, 'user_id' => $_SESSION['user_id']]);

        // Insert staking record
        $stmt = $pdo->prepare("INSERT INTO staking (user_id, currency, amount, lock_period, reward_rate) 
                               VALUES (:user_id, :currency, :amount, :lock_period, :reward_rate)");
        $stmt->execute([
            'user_id' => $_SESSION['user_id'],
            'currency' => $currency,
            'amount' => $amount,
            'lock_period' => $lock_period,
            'reward_rate' => $daily_reward_rate
        ]);

        $success_message = "You have successfully staked $amount $currency for $lock_period days with a daily reward rate of " . number_format($daily_reward_rate, 2) . "%!";
    }
}
?>



    <div class="nk-content nk-content-fluid">
    <div class="container-xl wide-lg">
        <div class="nk-content-body">
            <div class="nk-block-head">
                <div class="nk-block-head-content">
                    <div class="nk-block-head-content"><div class="ad-container">
                                            <div class="ad-text"id="ad_unit" data-unit-id="6624"></div><script src="https://api.fpadserver.com/static/js/advert_renderer.js"></script>
                                        </div>
                    <h4 class="nk-block-title">Stake Your Cryptocurrency</h4>
                    <p class="nk-block-des">Our staking platform allows you to lock your cryptocurrency assets for a specific period in return for rewards. It's an easy and efficient way to grow your crypto holdings while contributing to the security and functionality of the platform.</p>
                    <p class="nk-block-des">Your Staked Amount will being refunded into your balance after period end, <a href="calculator">Click here to calculate Staking</a> .</p>
                    <p class="nk-block-des"><a href="https://faucetminehub.com/docs/staking.html" target="_blank">Click here to read staking explaintation</a></p>
                </div>
            </div>

             <!-- Success / Error Messages -->
            <?php if (isset($error_message)): ?>
                <div class="alert alert-danger">
                    <?php echo htmlspecialchars($error_message); ?>
                </div>
            <?php elseif (isset($success_message)): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success_message); ?>
                </div>
            <?php endif; ?>

            <form method="POST" class="form-validate">
                <div class="row g-4">
                    <!-- Cryptocurrency Selection -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label" for="currency">Select Cryptocurrency:</label>
                            <select name="currency" id="currency" class="form-select" required>
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
                    </div>

                    <!-- Amount to Stake -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label" for="amount">Amount to Stake:</label>
                            <input type="number" class="form-control" name="amount" id="amount" min="0.0001" placeholder="Minimum 0.0001 BTC" required>
                        </div>
                    </div>

                    <!-- Lock Period Selection -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="form-label" for="lock_period">Lock Period (in days):</label>
                            <select name="lock_period" id="lock_period" class="form-select" required>
                                <?php foreach ($config['staking']['reward_rates'] as $period => $rate): ?>
                                    <option value="<?php echo $period; ?>">
                                        <?php echo $period; ?> days (<?php echo number_format($rate, 2); ?>% daily reward)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <!-- Submit Button -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <button type="submit" class="btn btn-lg btn-primary">Stake</button>
                        </div>
                    </div>
                </div>
            </form>

            <!-- Current Balances -->
            <div class="nk-block mt-5">
                <h4 class="nk-block-title">Your Current Balances</h4>
                <div class="row g-4">
                    <div class="col-md-6">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><img id="selected-coin-icon" src="assets/icons/btc.svg" alt="btc Icon" width="20" height="20">  Bitcoin (BTC): <strong><?php echo number_format($user['btc_balance'], 8); ?></strong></li>
                            <li class="list-group-item"><img id="selected-coin-icon" src="assets/icons/eth.svg" alt="eth Icon" width="20" height="20">  Ethereum (ETH): <strong><?php echo number_format($user['eth_balance'], 8); ?></strong></li>
                            <li class="list-group-item"><img id="selected-coin-icon" src="assets/icons/doge.svg" alt="doge Icon" width="20" height="20">  Dogecoin (DOGE): <strong><?php echo number_format($user['doge_balance'], 8); ?></strong></li>
                            <li class="list-group-item"><img id="selected-coin-icon" src="assets/icons/ltc.svg" alt="ltc Icon" width="20" height="20">  Litecoin (LTC): <strong><?php echo number_format($user['ltc_balance'], 8); ?></strong></li>
                            <li class="list-group-item"><img id="selected-coin-icon" src="assets/icons/bch.svg" alt="bch Icon" width="20" height="20">  Bitcoin Cash (BCH): <strong><?php echo number_format($user['bch_balance'], 8); ?></strong></li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><img id="selected-coin-icon" src="assets/icons/dash.svg" alt="dash Icon" width="20" height="20">  Dash (DASH): <strong><?php echo number_format($user['dash_balance'], 8); ?></strong></li>
                            <li class="list-group-item"><img id="selected-coin-icon" src="assets/icons/dgb.svg" alt="dgb Icon" width="20" height="20">  Digibyte (DGB): <strong><?php echo number_format($user['dgb_balance'], 8); ?></strong></li>
                            <li class="list-group-item"><img id="selected-coin-icon" src="assets/icons/trx.svg" alt="trx Icon" width="20" height="20">  Tron (TRX): <strong><?php echo number_format($user['trx_balance'], 8); ?></strong></li>
                            <li class="list-group-item"><img id="selected-coin-icon" src="assets/icons/usdt.svg" alt="usdt Icon" width="20" height="20">  Tether (USDT): <strong><?php echo number_format($user['usdt_balance'], 8); ?></strong></li>
                            <li class="list-group-item"><img id="selected-coin-icon" src="assets/icons/fey.svg" alt="fey Icon" width="20" height="20">  Feyorra (FEY): <strong><?php echo number_format($user['fey_balance'], 8); ?></strong></li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><img id="selected-coin-icon" src="assets/icons/zec.svg" alt="zec Icon" width="20" height="20">  Zcash (ZEC): <strong><?php echo number_format($user['zec_balance'], 8); ?></strong></li>
                            <li class="list-group-item"><img id="selected-coin-icon" src="assets/icons/bnb.svg" alt="bnb Icon" width="20" height="20">  Binance Coin (BNB): <strong><?php echo number_format($user['bnb_balance'], 8); ?></strong></li>
                            <li class="list-group-item"><img id="selected-coin-icon" src="assets/icons/sol.svg" alt="sol Icon" width="20" height="20">  Solana (SOL): <strong><?php echo number_format($user['sol_balance'], 8); ?></strong></li>
                            <li class="list-group-item"><img id="selected-coin-icon" src="assets/icons/xrp.svg" alt="xrp Icon" width="20" height="20">  Ripple (XRP): <strong><?php echo number_format($user['xrp_balance'], 8); ?></strong></li>
                            <li class="list-group-item"><img id="selected-coin-icon" src="assets/icons/matic.svg" alt="matic Icon" width="20" height="20">  Polygon (MATIC): <strong><?php echo number_format($user['matic_balance'], 8); ?></strong></li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><img id="selected-coin-icon" src="assets/icons/ada.svg" alt="ada Icon" width="20" height="20">  Cardano (ADA): <strong><?php echo number_format($user['ada_balance'], 8); ?></strong></li>
                            <li class="list-group-item"><img id="selected-coin-icon" src="assets/icons/ton.svg" alt="ton Icon" width="20" height="20">  Toncoin (TON): <strong><?php echo number_format($user['ton_balance'], 8); ?></strong></li>
                            <li class="list-group-item"><img id="selected-coin-icon" src="assets/icons/xlm.svg" alt="xlm Icon" width="20" height="20">  Stellar (XLM): <strong><?php echo number_format($user['xlm_balance'], 8); ?></strong></li>
                            <li class="list-group-item"><img id="selected-coin-icon" src="assets/icons/usdc.svg" alt="usdc Icon" width="20" height="20">  USD Coin (USDC): <strong><?php echo number_format($user['usdc_balance'], 8); ?></strong></li>
                            <li class="list-group-item"><img id="selected-coin-icon" src="assets/icons/xmr.svg" alt="xmr Icon" width="20" height="20">  Monero (XMR): <strong><?php echo number_format($user['xmr_balance'], 8); ?></strong></li>
                        </ul>
                    </div>
                    <div class="col-md-6">
                        <ul class="list-group list-group-flush">
                            <li class="list-group-item"><img id="selected-coin-icon" src="assets/icons/tara.svg" alt="tara Icon" width="20" height="20">  Taraxa (TARA): <strong><?php echo number_format($user['tara_balance'], 8); ?></strong></li>
                        </ul>
                    </div>
                </div>
            </div><!-- .nk-block -->
        </div>
    </div>
</div>
<script>
    // Updated minimum staking amounts
    const minStakingAmounts = {
        btc: 0.00001,
        eth: 0.0001,
        doge: 10,
        ltc: 0.001,
        bch: 0.001,
        dash: 0.001,
        dgb: 10,
        trx: 10,
        usdt: 1,
        fey: 100,
        zec: 0.001,
        bnb: 0.0001,
        sol: 0.01,
        xrp: 0.001,
        matic: 1,
        ada: 0.001,
        ton: 0.001,
        xlm: 0.001,
        usdc: 1,
        xmr: 0.001,
        tara: 100
    };

    // Event listener for currency selection change
    document.getElementById('currency').addEventListener('change', function () {
        const selectedCurrency = this.value;
        const minAmount = minStakingAmounts[selectedCurrency] || 0;
        const amountInput = document.getElementById('amount');

        // Update the minimum value and placeholder dynamically
        amountInput.min = minAmount;
        amountInput.placeholder = `Minimum ${minAmount}`;
    });
</script>

<?php
include 'footerlogged.php';
?>
