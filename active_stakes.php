<?php
include 'headerlogged.php';
?>

<?php
// Fetch active stakes from the database
$active_stakes = $pdo->prepare("SELECT * FROM staking WHERE user_id = :user_id AND status = 'active'");
$active_stakes->execute(['user_id' => $_SESSION['user_id']]);
$stakes = $active_stakes->fetchAll();

$reward_rates = $config['staking']['reward_rates'];

// Initialize an array to hold the total amounts and earnings for each currency
$total_summary = [];

// Calculate total staked and earnings by currency, considering lock periods
foreach ($stakes as $stake) {
    $currency = $stake['currency'];
    $lock_period = $stake['lock_period'];
    $amount_staked = (float)$stake['amount'];

    // Get the daily reward rate based on the lock period
    $daily_reward_rate = isset($reward_rates[$lock_period]) ? $reward_rates[$lock_period] : 0;

    // Calculate daily, hourly, and minute earnings for this stake
    $daily_earnings = $amount_staked * ($daily_reward_rate / 100); // daily earnings
    $hourly_earnings = $daily_earnings / 24; // hourly earnings
    $minute_earnings = $hourly_earnings / 60; // minute earnings

    // If the currency is not yet in the summary, initialize it
    if (!isset($total_summary[$currency])) {
        $total_summary[$currency] = [
            'total_amount_staked' => 0,
            'total_daily_earnings' => 0,
            'total_hourly_earnings' => 0,
            'total_minute_earnings' => 0,
        ];
    }

    // Add this stake's amount and earnings to the total for this currency
    $total_summary[$currency]['total_amount_staked'] += $amount_staked;
    $total_summary[$currency]['total_daily_earnings'] += $daily_earnings;
    $total_summary[$currency]['total_hourly_earnings'] += $hourly_earnings;
    $total_summary[$currency]['total_minute_earnings'] += $minute_earnings;
}
?>

<div class="nk-content nk-content-fluid">
    <div class="container-xl wide-lg">
        <div class="nk-content-body">
            <!-- New Table for Total Stakes by Currency -->
            <div class="nk-block nk-block-lg">
                <div class="nk-block-head">
                    <div class="nk-block-head-content">
                        <center><p><div id="ad_unit" data-unit-id="6624"></div><script src="https://api.fpadserver.com/static/js/advert_renderer.js"></script></p></center>
                        <p></p>
                        <center><h4 class="nk-block-title">Total Stakes by Currency</h4>
                        <p>See the total amount you’ve staked for each currency.</p></center>
                    </div>
                </div>
                <div class="card card-bordered card-preview">
                    <?php if (!empty($total_summary)): ?>
                        <table class="table table-tranx">
                            <thead>
                                <tr class="tb-tnx-head">
                                    <th><span>Currency</span></th>
                                    <th><span>Total Amount Staked</span></th>
                                    <th><span>Total Daily Earnings</span></th>
                                    <th><span>Total Hourly Earnings</span></th>
                                    <th><span>Total Minute Earnings</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($total_summary as $currency => $totals): ?>
                                    <tr class="tb-tnx-item">
                                        <td>
                                            <span><img id="selected-coin-icon" src="assets/icons/<?php echo strtolower(htmlspecialchars($currency)); ?>.svg" alt="<?php echo strtolower(htmlspecialchars($currency)); ?> Icon" width="20" height="20"> <?php echo htmlspecialchars(strtoupper($currency)); ?></span>
                                        </td>
                                        <td>
                                            <span><?php echo number_format($totals['total_amount_staked'], 8); ?></span>
                                        </td>
                                        <td>
                                            <span><?php echo number_format($totals['total_daily_earnings'], 8); ?></span>
                                        </td>
                                        <td>
                                            <span><?php echo number_format($totals['total_hourly_earnings'], 8); ?></span>
                                        </td>
                                        <td>
                                            <span><?php echo number_format($totals['total_minute_earnings'], 8); ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No active stakes at the moment.</p>
                    <?php endif; ?>
                </div><!-- .card-preview -->
            </div><!-- nk-block -->
            <br><br>
            <div class="nk-block nk-block-lg">
                <div class="nk-block-head">
                    <div class="nk-block-head-content">
                        <div class="nk-block-head-content">
                            <div class="ad-container">
                                <div class="ad-text" id="ad_unit" data-unit-id="6624"></div>
                                <script src="https://api.fpadserver.com/static/js/advert_renderer.js"></script>
                            </div>
                            <br>
                            <center><h4 class="nk-block-title">Your Active Stakes</h4>
                            <p>View your current staked cryptocurrency details and rewards.</p>
                            <p>When your staking period ends, you will receive the amount staked back into your balance!</p></center>
                        </div>
                    </div>
                </div>
                <div class="card card-bordered card-preview">
                    <?php if (!empty($stakes)): ?>
                        <table class="table table-tranx">
                            <thead>
                                <tr class="tb-tnx-head">
                                    <th><span>Currency</span></th>
                                    <th><span>Amount Staked</span></th>
                                    <th><span>Lock Period</span></th>
                                    <th><span>Daily Reward Rate</span></th>
                                    <th><span>Daily Earnings</span></th>
                                    <th><span>Hourly Earnings</span></th>
                                    <th><span>Minute Earnings</span></th>
                                    <th><span>Start Time</span></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($stakes as $stake): ?>
                                    <?php
                                    // Get the daily reward rate based on the lock period
                                    $lock_period = $stake['lock_period'];
                                    $daily_reward_rate = isset($reward_rates[$lock_period]) ? $reward_rates[$lock_period] : 0;

                                    // Calculate daily, hourly, and minute earnings
                                    $amount_staked = (float)$stake['amount'];
                                    $daily_earnings = $amount_staked * ($daily_reward_rate / 100); // daily earnings
                                    $hourly_earnings = $daily_earnings / 24; // hourly earnings
                                    $minute_earnings = $hourly_earnings / 60; // minute earnings
                                    ?>
                                    <tr class="tb-tnx-item">
                                        <td>
                                            <span><img id="selected-coin-icon" src="assets/icons/<?php echo strtolower(htmlspecialchars($stake['currency'])); ?>.svg" alt="<?php echo strtolower(htmlspecialchars($stake['currency'])); ?> Icon" width="20" height="20"> <?php echo htmlspecialchars(strtoupper($stake['currency'])); ?></span>
                                        </td>
                                        <td>
                                            <span><?php echo number_format($amount_staked, 8); ?></span>
                                        </td>
                                        <td>
                                            <span><?php echo htmlspecialchars($lock_period); ?> days</span>
                                        </td>
                                        <td>
                                            <span><?php echo number_format($daily_reward_rate, 2); ?>%</span>
                                        </td>
                                        <td>
                                            <span><?php echo number_format($daily_earnings, 8); ?></span>
                                        </td>
                                        <td>
                                            <span><?php echo number_format($hourly_earnings, 8); ?></span>
                                        </td>
                                        <td>
                                            <span><?php echo number_format($minute_earnings, 8); ?></span>
                                        </td>
                                        <td>
                                            <span><?php echo htmlspecialchars($stake['start_time']); ?></span>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    <?php else: ?>
                        <p>No active stakes at the moment.</p>
                    <?php endif; ?>
                </div><!-- .card-preview -->
            </div><!-- nk-block -->
        </div>
    </div>
</div>

<?php
include 'footerlogged.php';
?>
