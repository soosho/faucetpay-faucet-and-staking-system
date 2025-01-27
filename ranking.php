<?php include 'includes/ranking.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ranking Page</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        table { font-size: 0.85rem; border: 2px solid #2c3782; }
        .ranking-number { width: 40px; }
        .table-responsive { margin-bottom: 30px; }
        .custom-header th { background-color: #6576FF; color: white; text-align: center; }
        .table-hover tbody tr:hover { background-color: #f1f1f1; }
        .table th, .table td { vertical-align: middle; text-align: center; border: 2px solid #2c3782; }
        h3 { background-color: #e9ecef; padding: 10px; border-radius: 5px; text-align: center; border: 2px solid #2c3782; color: #2c3782; }
        .container { max-width: 1200px; }
    </style>
     <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-F4ERE043ZG"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-F4ERE043ZG');
</script>
</head>
<body>
<div class="container mt-4">
    <h1 class="text-center mb-4">Ranking - Total Wagered, Claims, and Levels</h1>
    <p>Welcome to the FaucetMineHub Leaderboard! Here, you can track the top users across various categories such as wagering, claiming, leveling, and referrals. Compete with others to climb the ranks and earn exclusive rewards. Check back frequently to see where you stand among other top performers on the platform.</p>

    <center>
        <p><div id="ad_unit" data-unit-id="6624"></div><script src="https://api.fpadserver.com/static/js/advert_renderer.js"></script></p>
    </center>
<h3>Monthly Challenges</h3>
<p>Participate in our monthly challenges and get a chance to win exclusive rewards! This month’s challenge: <strong>Top Wagered User</strong> will receive an additional $100 USDT on 1st of Each Month. Don’t miss out!</p>

<h3>Last Month Winner</h3>
<div class="table-responsive" style="border: 2px solid #2c3782; border-radius: 5px;">
                <table class="table table-bordered table-hover">
                    <thead class="custom-header">
                    <tr>
                        <th>Username</th>
                        <th>Reward</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>jkirk263</td>
                            <td>$100 USDT</td>
                        </tr>
                    </tbody>
                </table>
            </div>

<h3>Tips to Climb the Leaderboard</h3>
<ul>
    <li><strong>Wager More:</strong> Participate in the Dice game and other games to increase your total wagered amount.</li>
    <li><strong>Be Consistent:</strong> Make daily claims to maximize your claim count and stay active every day.</li>
    <li><strong>Invite Friends:</strong> Use your referral link to bring new users to FaucetMineHub and increase your referral count.</li>
    <li><strong>Level Up:</strong> Engage in various activities to earn experience points and level up faster, unlocking bonuses along the way.</li>
</ul>

    <div class="row">
        <div class="col-md-8">
            <h3>Total Wagered (7 Days & 30 Days)</h3>
            <div class="table-responsive" style="border: 2px solid #2c3782; border-radius: 5px;">
                <table class="table table-bordered table-hover">
                    <thead class="custom-header">
                    <tr>
                        <th class="ranking-number">#</th>
                        <th>Username (7 Days)</th>
                        <th>Total Wagered (7 Days)</th>
                        <th>Username (30 Days)</th>
                        <th>Total Wagered (30 Days)</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php for ($i = 0; $i < 10; $i++): ?>
                        <tr>
                            <td><?= $i + 1; ?></td>
                            <td><?= isset($topWagers7[$i]) ? htmlspecialchars($topWagers7[$i]['username']) : '-'; ?></td>
                            <td><?= isset($topWagers7[$i]) ? number_format($topWagers7[$i]['total_wagered_usd'], 4) : '-'; ?></td>
                            <td><?= isset($topWagers30[$i]) ? htmlspecialchars($topWagers30[$i]['username']) : '-'; ?></td>
                            <td><?= isset($topWagers30[$i]) ? number_format($topWagers30[$i]['total_wagered_usd'], 4) : '-'; ?></td>
                        </tr>
                    <?php endfor; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="col-md-4">
            <h3>Top Levels</h3>
            <div class="table-responsive" style="border: 2px solid #2c3782; border-radius: 5px;">
                <table class="table table-bordered table-hover">
                    <thead class="custom-header">
                    <tr>
                        <th class="ranking-number">#</th>
                        <th>Username</th>
                        <th>Level</th>
                        <th>Exp</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($topLevels as $index => $user): ?>
                        <tr>
                            <td><?= $index + 1; ?></td>
                            <td><?= htmlspecialchars($user['username']); ?></td>
                            <td><?= $user['level']; ?></td>
                            <td><?= $user['exp']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<center><p><div id="ad_unit" data-unit-id="6624"></div><script src="https://api.fpadserver.com/static/js/advert_renderer.js"></script></p></center>
    <div class="row">
    <div class="col-md-8">
        <h3>Total Claims (7 Days & 30 Days)</h3>
        <div class="table-responsive" style="border: 2px solid #2c3782; border-radius: 5px;">
            <table class="table table-bordered table-hover">
                <thead class="custom-header">
                <tr>
                    <th class="ranking-number">#</th>
                    <th>Username (7 Days)</th>
                    <th>Total Claims (7 Days)</th>
                    <th>Username (30 Days)</th>
                    <th>Total Claims (30 Days)</th>
                </tr>
                </thead>
                <tbody>
                <?php for ($i = 0; $i < 10; $i++): ?>
                    <tr>
                        <td><?= $i + 1; ?></td>
                        <td><?= isset($topClaims7[$i]) ? htmlspecialchars($topClaims7[$i]['username']) : '-'; ?></td>
                        <td><?= isset($topClaims7[$i]) ? $topClaims7[$i]['total_claims'] : '-'; ?></td>
                        <td><?= isset($topClaims30[$i]) ? htmlspecialchars($topClaims30[$i]['username']) : '-'; ?></td>
                        <td><?= isset($topClaims30[$i]) ? $topClaims30[$i]['total_claims'] : '-'; ?></td>
                    </tr>
                <?php endfor; ?>
                </tbody>
            </table>
        </div>
    </div>

    <div class="col-md-4">
        <h3>Most Referrers</h3>
        <div class="table-responsive" style="border: 2px solid #2c3782; border-radius: 5px;">
            <table class="table table-bordered table-hover">
                <thead class="custom-header">
                <tr>
                    <th class="ranking-number">#</th>
                    <th>Username</th>
                    <th>Total Referrals</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($mostReferrers as $index => $user): ?>
                    <tr>
                        <td><?= $index + 1; ?></td>
                        <td><?= htmlspecialchars($user['username']); ?></td>
                        <td><?= $user['total_referrals']; ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
<center><p><div id="ad_unit" data-unit-id="6624"></div><script src="https://api.fpadserver.com/static/js/advert_renderer.js"></script></p></center>


</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
