<?php
include 'headerlogged.php';
?>
<?php
// Set the number of transactions per page
$limit = 10;

// Get the current page number from the query string (if not set, default to 1)
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

// Calculate the offset for the query
$offset = ($page - 1) * $limit;

// Fetch the combined deposit and withdrawal history for the logged-in user, sorted by latest date (most recent first)
$stmt = $pdo->prepare("
    (SELECT id, amount, currency, payout_user_hash AS transaction_id, status, created_at, completed_at, 'withdrawal' AS type
     FROM withdraw_requests
     WHERE user_id = :user_id)
    UNION ALL
    (SELECT id, amount, currency, transaction_id AS transaction_id, status, created_at, completed_at, 'deposit' AS type
     FROM deposit_requests
     WHERE user_id = :user_id)
    ORDER BY created_at DESC
    LIMIT :limit OFFSET :offset
");

$stmt->bindValue(':user_id', $user_id, PDO::PARAM_INT);
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$transactions = $stmt->fetchAll();

// Get the total number of transactions for pagination
$total_stmt = $pdo->prepare("
    SELECT COUNT(*) AS total 
    FROM (
        (SELECT id FROM withdraw_requests WHERE user_id = :user_id)
        UNION ALL
        (SELECT id FROM deposit_requests WHERE user_id = :user_id)
    ) AS total_transactions
");
$total_stmt->execute(['user_id' => $user_id]);
$total = $total_stmt->fetchColumn();

// Calculate the total number of pages
$total_pages = ceil($total / $limit);
?>

<div class="nk-content nk-content-fluid">
                    <div class="container-xl wide-lg">
                        <div class="nk-content-body">
                                <div class="nk-block nk-block-lg">
                                    <div class="nk-block-head">
                                        <div class="nk-block-head-content"><div class="ad-container">
                                            <div class="ad-text"id="ad_unit" data-unit-id="6624"></div><script src="https://api.fpadserver.com/static/js/advert_renderer.js"></script>
                                        </div>
                                            <h4 class="nk-block-title">Transaction History</h4>
                                            <p>You can view your withdraw and deposit history here.</p>
                                        </div>
                                    </div>
                                    <div class="card card-bordered card-preview">
        <table class="table table-tranx">
            <thead>
                <tr class="tb-tnx-head">
                    <th class="tb-tnx-id"><span class="">#</span></th>
                    <th class="tb-tnx-info">
                        <span class="tb-tnx-desc d-none d-sm-inline-block">
                            <span>Transaction ID</span>
                        </span>
                        <span class="tb-tnx-date d-md-inline-block d-none">
                            <span class="d-none d-md-block">
                                <span>Date</span>
                                <span>Type</span>
                            </span>
                        </span>
                    </th>
                    <th class="tb-tnx-amount is-alt">
                        <span class="tb-tnx-total">Amount</span>
                        <span class="tb-tnx-status d-none d-md-inline-block">Status</span>
                    </th>
                    <th class="tb-tnx-action">
                        <span>&nbsp;</span>
                    </th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($transactions as $transaction): ?>
                <tr class="tb-tnx-item">
                    <td class="tb-tnx-id">
                        <a><span><?php echo htmlspecialchars($transaction['id']); ?></span></a>
                    </td>
                    <td class="tb-tnx-info">
                        <div class="tb-tnx-desc">
                            <span><?php echo htmlspecialchars($transaction['transaction_id'] ?? 'N/A'); ?></span> <!-- Display transaction ID or 'N/A' -->
                        </div>
                        <div class="tb-tnx-date">
                            <span class="date"><?php echo htmlspecialchars($transaction['created_at']); ?></span>
                            <span class="title"><?php echo ucfirst(htmlspecialchars($transaction['type'])); ?></span>
                        </div>
                    </td>
                    <td class="tb-tnx-amount is-alt">
                        <div class="tb-tnx-total">
                            <span class="amount"><img id="selected-coin-icon" src="assets/icons/<?php echo strtolower(htmlspecialchars($transaction['currency'])); ?>.svg" alt="<?php echo strtolower(htmlspecialchars($transaction['currency'])); ?> Icon" width="20" height="20"><?php echo number_format($transaction['amount'], 8); ?> <?php echo strtoupper(htmlspecialchars($transaction['currency'])); ?></span>
                        </div>
                        <div class="tb-tnx-status">
                            <span class="badge badge-dot 
                                <?php 
                                    if ($transaction['status'] === 'completed') {
                                        echo 'bg-success';
                                    } elseif ($transaction['status'] === 'failed') {
                                        echo 'bg-danger';
                                    } else {
                                        echo 'bg-warning'; // Default for pending or other statuses
                                    }
                                ?>">
                                <?php 
                                    if ($transaction['status'] === 'completed') {
                                        echo 'Success';
                                    } elseif ($transaction['status'] === 'failed') {
                                        echo 'Fail';
                                    } else {
                                        echo ucfirst(htmlspecialchars($transaction['status']));
                                    }
                                ?>
                            </span>
                        </div>
                    </td>
                    <td class="tb-tnx-action">
                        <div class="dropdown">
                            <a class="text-soft dropdown-toggle btn btn-icon btn-trigger" data-bs-toggle="dropdown">
                                <em class="icon ni ni-more-h"></em>
                            </a>
                            <div class="dropdown-menu dropdown-menu-end dropdown-menu-xs">
                                <ul class="link-list-plain">
                                    <li><a href="#" onclick="copyTxId('<?php echo $transaction['transaction_id'] ?? $transaction['payout_user_hash']; ?>')">Copy TX ID</a></li>
                                </ul>
                            </div>
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
                <a class="page-link" href="history?page=<?php echo $page - 1; ?>" tabindex="-1" aria-disabled="true">Prev</a>
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
                <a class="page-link" href="history?page=1">1</a>
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
                    <a class="page-link" href="history?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                </li>
            <?php endif; ?>
        <?php endfor; ?>

        <?php if ($end_page < $total_pages): ?>
            <?php if ($end_page < $total_pages - 1): ?>
                <li class="page-item disabled"><span class="page-link">...</span></li>
            <?php endif; ?>
            <li class="page-item">
                <a class="page-link" href="history?page=<?php echo $total_pages; ?>"><?php echo $total_pages; ?></a>
            </li>
        <?php endif; ?>

        <!-- Next Button -->
        <?php if ($page < $total_pages): ?>
            <li class="page-item">
                <a class="page-link" href="history?page=<?php echo $page + 1; ?>">Next</a>
            </li>
        <?php else: ?>
            <li class="page-item disabled">
                <a class="page-link" href="#" tabindex="-1" aria-disabled="true">Next</a>
            </li>
        <?php endif; ?>
    </ul>
    <div class="nk-block-head-content">
        <div class="ad-container">
            <div class="ad-text" id="ad_unit" data-unit-id="6624"></div>
            <script src="https://api.fpadserver.com/static/js/advert_renderer.js"></script>
        </div>
    </div>
</nav>

<script>
function copyTxId(txId) {
    // Prevent any default behavior (like navigation)
    event.preventDefault();
    
    // Copy transaction ID to clipboard
    navigator.clipboard.writeText(txId).then(function() {
        alert('Transaction ID copied to clipboard: ' + txId);
    }, function() {
        alert('Failed to copy Transaction ID');
    });
}
</script>


                                <?php
include 'footerlogged.php';
?>
