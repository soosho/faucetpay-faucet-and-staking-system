<?php
require 'includes/db.php';
require 'config/config.php';

$withdrawals_page = isset($_GET['withdrawals_page']) ? (int)$_GET['withdrawals_page'] : 1;
$deposits_page = isset($_GET['deposits_page']) ? (int)$_GET['deposits_page'] : 1;
$rowsPerPage = isset($_GET['rowsPerPage']) ? (int)$_GET['rowsPerPage'] : 10;

$withdrawals_offset = ($withdrawals_page - 1) * $rowsPerPage;
$deposits_offset = ($deposits_page - 1) * $rowsPerPage;

$stmt_rates = $pdo->query("SELECT crypto_name, rate_usd FROM crypto_rates");
$crypto_rates = $stmt_rates->fetchAll(PDO::FETCH_KEY_PAIR);

$currency_mapping = [
    'BTC' => 'bitcoin',
    'ETH' => 'ethereum',
    'DOGE' => 'dogecoin',
    'LTC' => 'litecoin',
    'BCH' => 'bitcoin-cash',
    'DASH' => 'dash',
    'DGB' => 'digibyte',
    'TRX' => 'tron',
    'USDT' => 'tether',
    'FEY' => 'feyorra',
    'ZEC' => 'zcash',
    'BNB' => 'binancecoin',
    'SOL' => 'solana',
    'XRP' => 'ripple',
    'MATIC' => 'matic-network',
    'ADA' => 'cardano',
    'TON' => 'the-open-network',
    'XLM' => 'stellar',
    'USDC' => 'usd-coin',
    'XMR' => 'monero',
    'TARA' => 'taraxa'
];

$total_withdrawals_usd = 0;
$total_deposits_usd = 0;

$stmt_all_withdrawals = $pdo->query("SELECT amount, currency FROM withdraw_requests WHERE status = 'completed'");
$all_withdrawals = $stmt_all_withdrawals->fetchAll(PDO::FETCH_ASSOC);

foreach ($all_withdrawals as $withdrawal) {
    $currency = strtoupper($withdrawal['currency']);
    $amount = $withdrawal['amount'];

    if (isset($currency_mapping[$currency])) {
        $mapped_currency = $currency_mapping[$currency];
        if (isset($crypto_rates[$mapped_currency])) {
            $rate_usd = $crypto_rates[$mapped_currency];
            $total_withdrawals_usd += $amount * $rate_usd;
        }
    }
}

$stmt_all_deposits = $pdo->query("SELECT amount, currency FROM deposit_requests WHERE status = 'completed'");
$all_deposits = $stmt_all_deposits->fetchAll(PDO::FETCH_ASSOC);

foreach ($all_deposits as $deposit) {
    $currency = strtoupper($deposit['currency']);
    $amount = $deposit['amount'];

    if (isset($currency_mapping[$currency])) {
        $mapped_currency = $currency_mapping[$currency];
        if (isset($crypto_rates[$mapped_currency])) {
            $rate_usd = $crypto_rates[$mapped_currency];
            $total_deposits_usd += $amount * $rate_usd;
        }
    }
}

$stmt_withdrawals = $pdo->prepare("
    SELECT w.amount, w.currency, w.status, w.completed_at, w.payout_user_hash, u.username 
    FROM withdraw_requests w 
    JOIN users u ON w.user_id = u.id
    WHERE w.status = 'completed'
    ORDER BY w.completed_at DESC
    LIMIT :offset, :rowsPerPage
");
$stmt_withdrawals->bindParam(':offset', $withdrawals_offset, PDO::PARAM_INT);
$stmt_withdrawals->bindParam(':rowsPerPage', $rowsPerPage, PDO::PARAM_INT);
$stmt_withdrawals->execute();
$withdrawals = $stmt_withdrawals->fetchAll(PDO::FETCH_ASSOC);

$stmt_deposits = $pdo->prepare("
    SELECT d.amount, d.currency, d.status, d.completed_at, d.transaction_id, u.username 
    FROM deposit_requests d 
    JOIN users u ON d.user_id = u.id
    WHERE d.status = 'completed'
    ORDER BY d.completed_at DESC
    LIMIT :offset, :rowsPerPage
");
$stmt_deposits->bindParam(':offset', $deposits_offset, PDO::PARAM_INT);
$stmt_deposits->bindParam(':rowsPerPage', $rowsPerPage, PDO::PARAM_INT);
$stmt_deposits->execute();
$deposits = $stmt_deposits->fetchAll(PDO::FETCH_ASSOC);

$total_withdrawals_stmt = $pdo->query("SELECT COUNT(*) FROM withdraw_requests WHERE status = 'completed'");
$total_withdrawals = $total_withdrawals_stmt->fetchColumn();

$total_deposits_stmt = $pdo->query("SELECT COUNT(*) FROM deposit_requests WHERE status = 'completed'");
$total_deposits = $total_deposits_stmt->fetchColumn();

$response = [
    'total_withdrawals_usd' => number_format($total_withdrawals_usd, 2),
    'total_deposits_usd' => number_format($total_deposits_usd, 2),
    'total_deposits' => $total_deposits,
    'total_withdrawals' => $total_withdrawals,
    'withdrawals' => $withdrawals,
    'deposits' => $deposits,
    'rows_per_page' => $rowsPerPage
];

header('Content-Type: application/json');
echo json_encode($response);
?>
