<?php
include 'headerlogged.php';
?>
<?php

// Number of items per page
$items_per_page = 10;

// Get the current page number from the query string (default to 1 if not set)
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculate the offset for the SQL query
$offset = ($page - 1) * $items_per_page;

// Fetch the total number of referral earnings for the logged-in user
$total_earnings_stmt = $pdo->prepare("
    SELECT COUNT(*) AS total_earnings 
    FROM referral_earnings 
    WHERE referrer_id = :user_id
");
$total_earnings_stmt->execute(['user_id' => $user_id]);
$total_earnings_row = $total_earnings_stmt->fetch();
$total_earnings = $total_earnings_row['total_earnings'];

// Calculate the total number of pages
$total_pages = ceil($total_earnings / $items_per_page);

// Fetch referral earnings for the current page
$stmt = $pdo->prepare("
    SELECT 
        r.*, 
        u.username AS referred_user_email 
    FROM referral_earnings r 
    JOIN users u ON r.referred_user_id = u.id 
    WHERE r.referrer_id = :user_id
    ORDER BY r.earned_at DESC
    LIMIT :offset, :items_per_page
");
$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->bindValue(':items_per_page', $items_per_page, PDO::PARAM_INT);
$stmt->execute();
$referral_earnings = $stmt->fetchAll();

?>

    <div class="nk-content nk-content-fluid">
    <div class="container-xl wide-lg">
        <div class="nk-content-body">
            <div class="nk-block nk-block-lg">
                <div class="nk-block-head text-center">
                    <div class="nk-block-head-content">
                        <center><p><div id="ad_unit" data-unit-id="6624"></div><script src="https://api.fpadserver.com/static/js/advert_renderer.js"></script></p></center>
                        <p></p>
                        <h4 class="nk-block-title">Referral Earnings History</h4>
                        <p>2% FROM THEIR DEPOSIT AND DAILY CLAIM THEY MADE FOR LIFETIME!</p>
                        <p>Earn extra rewards by referring users to our platform! Our referral earning system allows you to receive bonuses every time a user you referred deposits funds.</p>
                        <p><a href="https://faucetminehub.com/docs/referrals.html" target="_blank">Click here to know more about how referral system works</a></p>
                        <p>You can view your referral earnings history here.</p>
                    </div>
                </div>
                <div class="card card-bordered card-preview">
                    <table class="table table-tranx">
                        <thead>
                            <tr class="tb-tnx-head">
                                <th class="tb-tnx-id"><span class="">Referred User</span></th>
                                <th class="tb-tnx-info">
                                    <span class="tb-tnx-desc d-none d-sm-inline-block">
                                        <span>Earning</span>
                                    </span>
                                    <span class="tb-tnx-date d-md-inline-block d-none">
                                        <span class="d-md-none">Date</span>
                                        <span class="d-none d-md-block">
                                            <span>Deposit Amount</span>
                                            <span>Date</span>
                                        </span>
                                    </span>
                                </th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($referral_earnings as $earning): ?>
                                <tr class="tb-tnx-item">
                                    <td class="tb-tnx-id">
                                        <a><span><?php echo strtoupper(htmlspecialchars($earning['referred_user_email'])); ?></span></a>
                                    </td>
                                    <td class="tb-tnx-info">
                                        <div class="tb-tnx-desc">
                                            <span class="amount"><img id="selected-coin-icon" src="assets/icons/<?php echo strtolower(htmlspecialchars($earning['currency'])); ?>.svg" alt="<?php echo strtolower(htmlspecialchars($earning['currency'])); ?> Icon" width="20" height="20">  <?php echo number_format($earning['bonus'], 8); ?> <?php echo strtoupper(htmlspecialchars($earning['currency'])); ?></span>
                                        </div>
                                        <div class="tb-tnx-date">
                                            <span class="date"><img id="selected-coin-icon" src="assets/icons/<?php echo strtolower(htmlspecialchars($earning['currency'])); ?>.svg" alt="<?php echo strtolower(htmlspecialchars($earning['currency'])); ?> Icon" width="20" height="20">  <?php echo number_format($earning['amount'], 8); ?> <?php echo strtoupper(htmlspecialchars($earning['currency'])); ?></span>
                                            <span class="date"><?php echo date('d M Y, H:i', strtotime($earning['earned_at'])); ?></span>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div><!-- .card-preview -->
            </div><!-- nk-block -->

<!-- Pagination Links -->
<nav>
    <ul class="pagination">
        <!-- Previous Button -->
        <?php if ($page > 1): ?>
            <li class="page-item">
                <a class="page-link" href="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo $page - 1; ?>" tabindex="-1" aria-disabled="true">Prev</a>
            </li>
        <?php else: ?>
            <li class="page-item disabled">
                <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Prev</a>
            </li>
        <?php endif; ?>

        <!-- Page Numbers -->
        <?php
        $max_visible_pages = 5; // Adjust this number to control how many pages are visible
        $start_page = max(1, $page - floor($max_visible_pages / 2));
        $end_page = min($total_pages, $page + floor($max_visible_pages / 2));

        if ($start_page > 1): ?>
            <li class="page-item">
                <a class="page-link" href="<?php echo $_SERVER['PHP_SELF']; ?>?page=1">1</a>
            </li>
            <?php if ($start_page > 2): ?>
                <li class="page-item disabled"><span class="page-link">...</span></li>
            <?php endif; ?>
        <?php endif; ?>

        <?php for ($i = $start_page; $i <= $end_page; $i++): ?>
            <?php if ($i == $page): ?>
                <li class="page-item active" aria-current="page">
                    <a class="page-link" href="#"><?php echo $i; ?> <span class="visually-hidden">(current)</span></a>
                </li>
            <?php else: ?>
                <li class="page-item">
                    <a class="page-link" href="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if ($end_page < $total_pages): ?>
            <?php if ($end_page < $total_pages - 1): ?>
                <li class="page-item disabled"><span class="page-link">...</span></li>
            <?php endif; ?>
            <li class="page-item">
                <a class="page-link" href="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo $total_pages; ?>"><?php echo $total_pages; ?></a>
            </li>
        <?php endif; ?>

        <!-- Next Button -->
        <?php if ($page < $total_pages): ?>
            <li class="page-item">
                <a class="page-link" href="<?php echo $_SERVER['PHP_SELF']; ?>?page=<?php echo $page + 1; ?>">Next</a>
            </li>
        <?php else: ?>
            <li class="page-item disabled">
                <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Next</a>
            </li>
        <?php endif; ?>
    </ul>
</nav>


        </div>
    </div>
</div>



<?php
include 'footerlogged.php';
?>