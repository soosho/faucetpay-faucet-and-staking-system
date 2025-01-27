<?php
session_start();
date_default_timezone_set('Asia/Jakarta');

// Check if the user is logged in, otherwise redirect to login
if (!isset($_SESSION['user_id'])) {
    header('Location: login');
    exit();
}

// Include necessary files
require 'includes/db.php';
require 'config/config.php';

$user_id = $_SESSION['user_id'];

// Fetch user's current level and experience
$userStmt = $pdo->prepare("
    SELECT level, exp 
    FROM users 
    WHERE id = :user_id
");
$userStmt->execute([':user_id' => $user_id]);
$user = $userStmt->fetch(PDO::FETCH_ASSOC);

// Fetch all levels, their required experience, and claim bonuses
$levelsStmt = $pdo->query("
    SELECT level, experience_required, claim_bonus 
    FROM user_levels 
    ORDER BY level ASC
");
$levels = $levelsStmt->fetchAll(PDO::FETCH_ASSOC);

// Get the claim bonus for the user's current level
$currentLevelBonus = 0;
foreach ($levels as $level) {
    if ($level['level'] == $user['level']) {
        $currentLevelBonus = $level['claim_bonus'];
        break;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Level and Claim Bonuses</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
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
        .card-body {
            padding: 20px;
            font-size: 1.1rem;
        }
        table { font-size: 0.85rem; border: 2px solid #2c3782; }
        .table-responsive { margin-bottom: 30px; }
        .custom-header th { background-color: #6576FF; color: white; text-align: center; }
        .table th, .table td { vertical-align: middle; text-align: center; border: 2px solid #2c3782; }
        h3 { background-color: #e9ecef; padding: 10px; border-radius: 5px; text-align: center; border: 2px solid #2c3782; color: #2c3782; }
        .container { max-width: 800px; }
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
    <h1 class="text-center mb-4">User Level and Claim Bonuses</h1>

    <div class="card">
        
        <div class="card">
    <div class="card-body">
        <p>Welcome to the User Level and Claim Bonuses page on FaucetMineHub! Here, you can track your progress, view your current level, and see the benefits of leveling up. The higher your level, the more bonuses you can earn on your daily claims. Start leveling up today and maximize your rewards!</p>
    </div>
</div>
<div class="card mt-3">
    <div class="card-header">
        Tips for Leveling Up
    </div>
    <div class="card-body">
        <ul>
            <li><strong>Be Consistent:</strong> Make sure to log in daily and claim your rewards. Consistency is key to earning more experience points.</li>
            <li><strong>Roll the Dice:</strong> Participate in the Dice game regularly to earn additional experience points.</li>
            <li><strong>Set Goals:</strong> Aim for a specific level each month and track your progress using the table below.</li>
        </ul>
    </div>
</div>
<div class="card">
    <div class="card-header">
        Your Current Level
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-6">
                <p><strong>Level:</strong> <?= htmlspecialchars($user['level']); ?></p>
            </div>
            <div class="col-md-6">
                <p><strong>Experience:</strong> <?= htmlspecialchars($user['exp']); ?></p>
            </div>
        </div>
        <p class="mb-0"><strong>Claim Bonus:</strong> <?= number_format($currentLevelBonus, 2); ?>%</p>
        <p>Every Daily Claims and Dice Roll you did will increase your Experience by 1</p>

        <?php
        // Find the next level's experience requirement
        $nextLevelExp = 0;
        foreach ($levels as $level) {
            if ($level['level'] == $user['level'] + 1) {
                $nextLevelExp = $level['experience_required'];
                break;
            }
        }

        // Calculate progress to the next level
        $progress = $nextLevelExp > 0 ? ($user['exp'] / $nextLevelExp) * 100 : 100;
        ?>
        
        <div class="mt-3">
            <p><strong>Progress to Next Level:</strong></p>
            <p>Current Experience: <?= htmlspecialchars($user['exp']); ?> / <?= $nextLevelExp; ?> (Next Level)</p>
            <div class="progress">
                <div class="progress-bar" role="progressbar" 
                     style="width: <?= min($progress, 100); ?>%;" 
                     aria-valuenow="<?= $progress; ?>" 
                     aria-valuemin="0" 
                     aria-valuemax="100">
                    <?= number_format(min($progress, 100), 2); ?>%
                </div>
            </div>
        </div>
    </div>
</div>

<center><p><div id="ad_unit" data-unit-id="6624"></div><script src="https://api.fpadserver.com/static/js/advert_renderer.js"></script></p></center>
<p></p>
    <h3>Level Requirements and Bonuses</h3>
    <div class="table-responsive" style="border: 2px solid #2c3782; border-radius: 5px;">
        <table class="table table-bordered table-hover">
            <thead class="custom-header">
            <tr>
                <th>Level</th>
                <th>Experience Required</th>
                <th>Claim Bonus (%)</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($levels as $level): ?>
                <tr>
                    <td><?= htmlspecialchars($level['level']); ?></td>
                    <td><?= number_format($level['experience_required']); ?></td>
                    <td><?= number_format($level['claim_bonus'], 2); ?>%</td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="card mt-3">
    <div class="card-header">
        Frequently Asked Questions
    </div>
    <div class="card-body">
        <p><strong>Q: How do I increase my experience points?</strong><br>
        A: You can increase your experience points by making daily claims and participating in the Dice game.</p>
        <p><strong>Q: What is the benefit of reaching a higher level?</strong><br>
        A: Higher levels give you a greater bonus percentage on your daily claims, allowing you to earn more rewards.</p>
        <p><strong>Q: How is the claim bonus applied?</strong><br>
        A: The claim bonus is a percentage added to each claim based on your current level. For example, if your bonus is 5%, you will receive an extra 5% on top of your base claim amount.</p>
    </div>
</div>

</div>

<!-- Bootstrap JS and dependencies -->
<script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
<script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>
</html>
