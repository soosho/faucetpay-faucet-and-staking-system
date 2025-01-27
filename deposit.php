<?php
include 'headerlogged.php';
?>
<!-- content @s -->
                <div class="nk-content nk-content-fluid">
                    <div class="container-xl wide-lg">
                        <div class="nk-content-body">
                            <div class="buysell wide-xs m-auto">
                                <div class="buysell-title text-center">
                                    <h2 class="title">Start Earn Passive Income!</h2>
                                    <p>Depositing funds into your account is a simple and secure process that allows you to start staking and earning rewards.</p>
                                    <p><a href="https://faucetminehub.com/docs/deposit.html" target="_blank">Click here to read more about deposit system</a></p>
                                </div><!-- .buysell-title -->
                                <div class="buysell-block">
                                    <form action="https://faucetpay.io/merchant/webscr" class="buysell-form" method="POST">
    <div class="buysell-field form-group">
        <div class="form-label-group">
            <label class="form-label">Choose what you want to deposit</label>
        </div>
        <input type="hidden" value="BTC" name="currency1" id="buysell-choose-currency"> <!-- This will store the selected cryptocurrency -->
        <div class="dropdown buysell-cc-dropdown">
            <a href="#" class="buysell-cc-choosen dropdown-indicator" data-bs-toggle="dropdown" id="dropdown-toggle">
                <div class="coin-item coin-btc">
                    <div class="coin-icon">
                        <img id="selected-coin-icon" src="assets/icons/btc.svg" alt="btc Icon" width="32" height="32">
                    </div>
                    <div class="coin-info">
                        <span class="coin-name" id="selected-coin-name">Bitcoin (BTC)</span>
                    </div>
                </div>
            </a>
            <div class="dropdown-menu dropdown-menu-auto dropdown-menu-mxh">
                <ul class="buysell-cc-list">
                    <li class="buysell-cc-item selected">
                        <div class="buysell-cc-opt" data-currency="BTC" onclick="selectCurrency('BTC', 'Bitcoin (BTC)', 'btc')">
                            <div class="coin-item coin-btc">
                                <div class="coin-icon">
                                    <img id="selected-coin-icon" src="assets/icons/btc.svg" alt="btc Icon" width="32" height="32">
                                </div>
                                <div class="coin-info">
                                    <span class="coin-name">Bitcoin (BTC)</span>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="buysell-cc-item">
                        <div class="buysell-cc-opt" data-currency="ETH" onclick="selectCurrency('ETH', 'Ethereum (ETH)', 'eth')">
                            <div class="coin-item coin-eth">
                                <div class="coin-icon">
                                    <img id="selected-coin-icon" src="assets/icons/eth.svg" alt="btc Icon" width="32" height="32">
                                </div>
                                <div class="coin-info">
                                    <span class="coin-name">Ethereum (ETH)</span>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="buysell-cc-item">
                        <div class="buysell-cc-opt" data-currency="DOGE" onclick="selectCurrency('DOGE', 'Dogecoin (DOGE)', 'doge')">
                            <div class="coin-item coin-doge">
                                <div class="coin-icon">
                                    <img id="selected-coin-icon" src="assets/icons/doge.svg" alt="doge Icon" width="32" height="32">
                                </div>
                                <div class="coin-info">
                                    <span class="coin-name">Dogecoin (DOGE)</span>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="buysell-cc-item">
                        <div class="buysell-cc-opt" data-currency="LTC" onclick="selectCurrency('LTC', 'Litecoin (LTC)', 'ltc')">
                            <div class="coin-item coin-ltc">
                                <div class="coin-icon">
                                    <img id="selected-coin-icon" src="assets/icons/ltc.svg" alt="ltc Icon" width="32" height="32">
                                </div>
                                <div class="coin-info">
                                    <span class="coin-name">Litecoin (LTC)</span>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="buysell-cc-item">
                        <div class="buysell-cc-opt" data-currency="BCH" onclick="selectCurrency('BCH', 'Bitcoin Cash (BCH)', 'bch')">
                            <div class="coin-item coin-bch">
                                <div class="coin-icon">
                                    <img id="selected-coin-icon" src="assets/icons/bch.svg" alt="bch Icon" width="32" height="32">
                                </div>
                                <div class="coin-info">
                                    <span class="coin-name">Bitcoin Cash (BCH)</span>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="buysell-cc-item">
                        <div class="buysell-cc-opt" data-currency="DASH" onclick="selectCurrency('DASH', 'Dash (DASH)', 'dash')">
                            <div class="coin-item coin-dash">
                                <div class="coin-icon">
                                    <img id="selected-coin-icon" src="assets/icons/dash.svg" alt="btc Icon" width="32" height="32">
                                </div>
                                <div class="coin-info">
                                    <span class="coin-name">Dash (DASH)</span>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="buysell-cc-item">
                        <div class="buysell-cc-opt" data-currency="DGB" onclick="selectCurrency('DGB', 'DigiByte (DGB)', 'dgb')">
                            <div class="coin-item coin-dgb">
                                <div class="coin-icon">
                                    <img id="selected-coin-icon" src="assets/icons/dgb.svg" alt="btc Icon" width="32" height="32">
                                </div>
                                <div class="coin-info">
                                    <span class="coin-name">DigiByte (DGB)</span>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="buysell-cc-item">
                        <div class="buysell-cc-opt" data-currency="TRX" onclick="selectCurrency('TRX', 'Tron (TRX)', 'trx')">
                            <div class="coin-item coin-trx">
                                <div class="coin-icon">
                                    <img id="selected-coin-icon" src="assets/icons/trx.svg" alt="btc Icon" width="32" height="32">
                                </div>
                                <div class="coin-info">
                                    <span class="coin-name">Tron (TRX)</span>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="buysell-cc-item">
                        <div class="buysell-cc-opt" data-currency="USDT" onclick="selectCurrency('USDT', 'Tether TRC20 (USDT)', 'usdt')">
                            <div class="coin-item coin-usdt">
                                <div class="coin-icon">
                                    <img id="selected-coin-icon" src="assets/icons/usdt.svg" alt="btc Icon" width="32" height="32">
                                </div>
                                <div class="coin-info">
                                    <span class="coin-name">Tether TRC20 (USDT)</span>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="buysell-cc-item">
                        <div class="buysell-cc-opt" data-currency="FEY" onclick="selectCurrency('FEY', 'Feyorra (FEY)', 'fey')">
                            <div class="coin-item coin-fey">
                                <div class="coin-icon">
                                    <img id="selected-coin-icon" src="assets/icons/fey.svg" alt="btc Icon" width="32" height="32">
                                </div>
                                <div class="coin-info">
                                    <span class="coin-name">Feyorra (FEY)</span>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="buysell-cc-item">
                        <div class="buysell-cc-opt" data-currency="ZEC" onclick="selectCurrency('ZEC', 'Zcash (ZEC)', 'zec')">
                            <div class="coin-item coin-zec">
                                <div class="coin-icon">
                                    <img id="selected-coin-icon" src="assets/icons/zec.svg" alt="btc Icon" width="32" height="32">
                                </div>
                                <div class="coin-info">
                                    <span class="coin-name">Zcash (ZEC)</span>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="buysell-cc-item">
                        <div class="buysell-cc-opt" data-currency="BNB" onclick="selectCurrency('BNB', 'Binance Coin (BNB)', 'bnb')">
                            <div class="coin-item coin-bnb">
                                <div class="coin-icon">
                                    <img id="selected-coin-icon" src="assets/icons/bnb.svg" alt="btc Icon" width="32" height="32">
                                </div>
                                <div class="coin-info">
                                    <span class="coin-name">Binance Coin (BNB)</span>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="buysell-cc-item">
                        <div class="buysell-cc-opt" data-currency="SOL" onclick="selectCurrency('SOL', 'Solana (SOL)', 'sol')">
                            <div class="coin-item coin-sol">
                                <div class="coin-icon">
                                    <img id="selected-coin-icon" src="assets/icons/sol.svg" alt="btc Icon" width="32" height="32">
                                </div>
                                <div class="coin-info">
                                    <span class="coin-name">Solana (SOL)</span>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="buysell-cc-item">
                        <div class="buysell-cc-opt" data-currency="XRP" onclick="selectCurrency('XRP', 'Ripple (XRP)', 'xrp')">
                            <div class="coin-item coin-xrp">
                                <div class="coin-icon">
                                    <img id="selected-coin-icon" src="assets/icons/xrp.svg" alt="btc Icon" width="32" height="32">
                                </div>
                                <div class="coin-info">
                                    <span class="coin-name">Ripple (XRP)</span>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="buysell-cc-item">
                        <div class="buysell-cc-opt" data-currency="MATIC" onclick="selectCurrency('MATIC', 'Polygon (MATIC)', 'matic')">
                            <div class="coin-item coin-matic">
                                <div class="coin-icon">
                                    <img id="selected-coin-icon" src="assets/icons/matic.svg" alt="btc Icon" width="32" height="32">
                                </div>
                                <div class="coin-info">
                                    <span class="coin-name">Polygon (MATIC)</span>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="buysell-cc-item">
                        <div class="buysell-cc-opt" data-currency="ADA" onclick="selectCurrency('ADA', 'Cardano (ADA)', 'ada')">
                            <div class="coin-item coin-ada">
                                <div class="coin-icon">
                                    <img id="selected-coin-icon" src="assets/icons/ada.svg" alt="btc Icon" width="32" height="32">
                                </div>
                                <div class="coin-info">
                                    <span class="coin-name">Cardano (ADA)</span>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="buysell-cc-item">
                        <div class="buysell-cc-opt" data-currency="TON" onclick="selectCurrency('TON', 'Toncoin (TON)', 'ton')">
                            <div class="coin-item coin-ton">
                                <div class="coin-icon">
                                    <img id="selected-coin-icon" src="assets/icons/ton.svg" alt="btc Icon" width="32" height="32">
                                </div>
                                <div class="coin-info">
                                    <span class="coin-name">Toncoin (TON)</span>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="buysell-cc-item">
                        <div class="buysell-cc-opt" data-currency="XLM" onclick="selectCurrency('XLM', 'Stellar (XLM)', 'xlm')">
                            <div class="coin-item coin-xlm">
                                <div class="coin-icon">
                                    <img id="selected-coin-icon" src="assets/icons/xlm.svg" alt="btc Icon" width="32" height="32">
                                </div>
                                <div class="coin-info">
                                    <span class="coin-name">Stellar (XLM)</span>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="buysell-cc-item">
                        <div class="buysell-cc-opt" data-currency="USDC" onclick="selectCurrency('USDC', 'USD Coin (USDC)', 'usdc')">
                            <div class="coin-item coin-usdc">
                                <div class="coin-icon">
                                    <img id="selected-coin-icon" src="assets/icons/usdc.svg" alt="btc Icon" width="32" height="32">
                                </div>
                                <div class="coin-info">
                                    <span class="coin-name">USD Coin (USDC)</span>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="buysell-cc-item">
                        <div class="buysell-cc-opt" data-currency="XMR" onclick="selectCurrency('XMR', 'Monero (XMR)', 'xmr')">
                            <div class="coin-item coin-xmr">
                                <div class="coin-icon">
                                    <img id="selected-coin-icon" src="assets/icons/xmr.svg" alt="btc Icon" width="32" height="32">
                                </div>
                                <div class="coin-info">
                                    <span class="coin-name">Monero (XMR)</span>
                                </div>
                            </div>
                        </div>
                    </li>
                    <li class="buysell-cc-item">
                        <div class="buysell-cc-opt" data-currency="TARA" onclick="selectCurrency('TARA', 'Taraxa (TARA)', 'tara')">
                            <div class="coin-item coin-tara">
                                <div class="coin-icon">
                                    <img id="selected-coin-icon" src="assets/icons/tara.svg" alt="btc Icon" width="32" height="32">
                                </div>
                                <div class="coin-info">
                                    <span class="coin-name">Taraxa (TARA)</span>
                                </div>
                            </div>
                        </div>
                    </li>
                </ul>
            </div><!-- .dropdown-menu -->
        </div><!-- .dropdown -->
    </div><!-- .buysell-field -->

    <div class="buysell-field form-group">
        <div class="form-label-group">
            <label class="form-label" for="amount">Amount to Deposit (in selected currency)</label>
        </div>
        <div class="form-control-group">
            <input type="number" class="form-control form-control-lg form-control-number" id="amount" name="amount1" placeholder="Enter deposit amount" required min="0.00000001" step="0.00000001">
            <div class="form-dropdown">
                <div id="currency-label">BTC</div>
            </div>
        </div>
        <div class="form-note-group">
            <span class="ms-1" id="min-deposit">Minimum: 0.00000001 / 0.003 USD (click here)</span>
            <span class="buysell-rate form-note-alt">0% Deposit Fee</span>
        </div>
    </div><!-- .buysell-field -->

    <!-- Hidden Fields for FaucetPay API -->
    <input type="hidden" name="merchant_username" value="wepayou"> <!-- Replace with your merchant username -->
    <input type="hidden" name="currency2" value=""> <!-- Let FaucetPay determine the payment currency automatically -->
    <input type="hidden" name="item_description" value="Deposit for user #<?php echo htmlspecialchars($user['username']); ?>"> <!-- Custom description -->
    <input type="hidden" name="custom" value="<?php echo $user_id; ?>"> <!-- Custom identifier like user_id -->
    <input type="hidden" name="callback_url" value="https://faucetminehub.com/payment_callback"> <!-- Replace with your callback URL -->
    <input type="hidden" name="success_url" value="https://faucetminehub.com/success"> <!-- Redirect on success -->
    <input type="hidden" name="cancel_url" value="https://faucetminehub.com/cancel"> <!-- Redirect on cancel -->

    <div class="buysell-field form-action">
        <button type="submit" class="btn btn-lg btn-block btn-primary">Proceed to Pay</button>
    </div><!-- .buysell-field -->

</form><!-- .buysell-form -->
                                </div><!-- .buysell-block -->
                            </div><!-- .buysell -->
                        </div>
                    </div>
                </div>
                <!-- content @e -->
<script>
// Function to select a currency and update the dropdown display
function selectCurrency(currency, displayName, iconName) {
    // Set the new icon source and alt text
    var iconElement = document.getElementById('selected-coin-icon');
    
    if (iconName) {
        iconElement.src = `assets/icons/${iconName}.svg`; // Set the new icon source
        iconElement.alt = `${displayName} Icon`; // Set the new alt text
    } else {
        console.error('iconName is not defined');
    }

    // Update any other parts of the UI related to the selection
    document.getElementById('buysell-choose-currency').value = currency; // Update hidden input
    document.getElementById('selected-coin-name').innerText = displayName; // Update selected coin name
    document.getElementById('currency-label').innerText = currency; // Update label near amount input

    // Update the hidden currency2 field for FaucetPay
    document.querySelector('input[name="currency2"]').value = currency;

    // Close the dropdown menu programmatically
    var dropdownToggle = document.getElementById('dropdown-toggle'); // The dropdown toggle button
    var dropdownInstance = bootstrap.Dropdown.getOrCreateInstance(dropdownToggle); // Bootstrap instance
    dropdownInstance.hide(); // Close the dropdown
}

// Add click event listener for the minimum deposit amount
document.getElementById('min-deposit').addEventListener('click', function() {
    document.getElementById('amount').value = '0.00000001'; // Set minimum deposit amount
});


</script>
<?php
include 'footerlogged.php';
?>
