<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="View real-time updates on the latest cryptocurrency withdrawals and deposits on our platform. Track transactions, amounts, and currencies with ease.">
    <meta name="keywords" content="cryptocurrency, withdrawals, deposits, real-time updates, crypto transactions, Bitcoin, Ethereum, Dogecoin, Litecoin, staking">
    <title>Real-Time Latest Withdrawals and Deposits</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <link rel="icon" type="image/png" href="../images/favicon2.png">
    <style>
        .stats-container {
            font-family: Arial, sans-serif;
            padding: 20px;
            max-width: 1200px;
            margin: auto;
        }
        .stats-box {
            padding: 15px;
            margin: 15px 0;
            background-color: #f4f4f4;
            border: 2px solid #2c3782;
            border-radius: 5px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        table th, table td {
            padding: 10px;
            text-align: center;
            border: 2px solid #2c3782;
        }
        table th {
            background-color: #6576FF;
            color: white;
        }
        .pagination {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }
        .pagination a {
            padding: 10px 15px;
            margin: 0 5px;
            background-color: #ddd;
            border: 2px solid #2c3782;
            text-decoration: none;
            color: #2c3782;
            border-radius: 5px;
        }
        .pagination a.active {
            background-color: #6576FF;
            color: white;
            pointer-events: none;
        }
        .pagination span {
            padding: 10px 15px;
            margin: 0 5px;
            background-color: #f4f4f4;
            color: #888;
            border: 2px solid #2c3782;
            border-radius: 5px;
        }
        .card {
            border: 2px solid #2c3782;
            border-radius: 5px;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1, h2 {
            color: #2c3782;
            text-align: center;
        }
        .btn-primary {
            background-color: #2c3782;
            border: none;
        }
        .btn-primary:hover {
            background-color: #1f2768;
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
</head>
<body>

<div class="stats-container">
    <h1>Real-Time Withdrawals and Deposits</h1>
<div class="stats-box">
    <h2>About This Page</h2>
    <p>Welcome to the Real-Time Withdrawals and Deposits page of FaucetMineHub. Here, you can monitor the latest cryptocurrency transactions on our platform, including user withdrawals and deposits across various digital assets. Stay updated on the most recent activity and track the movement of funds on our secure and transparent platform.</p>
</div>
<div class="stats-box">
    <h2>How to Use This Page</h2>
    <ul>
        <li><strong>Monitor Transactions:</strong> View recent withdrawals and deposits in real-time to stay informed about platform activity.</li>
        <li><strong>Track Your Transactions:</strong> Find your own username in the list to verify your transactions and their statuses.</li>
        <li><strong>Verify Payouts:</strong> Use the payout hash or transaction ID to verify your transactions on the blockchain.</li>
        <li><strong>Navigate History:</strong> Use the pagination below each table to view earlier transactions.</li>
    </ul>
</div>
    <div class="stats-box">
        <p><strong>Total Deposits Served:</strong> <span id="total-deposits">0</span></p>
        <p><strong>Total Withdrawals Processed:</strong> <span id="total-withdrawals">0</span></p>
        <p><strong>Total Deposits in USD:</strong> $<span id="total-deposits-usd">0.00</span></p>
        <p><strong>Total Withdrawals in USD:</strong> $<span id="total-withdrawals-usd">0.00</span></p>
    </div>
<center>
    <p>
        <div id="ad_unit" data-unit-id="6624"></div><script src="https://api.fpadserver.com/static/js/advert_renderer.js"></script>
    </p>
</center>
    <h2>Withdrawals</h2>
    <div class="card">
        <table id="withdrawals-table" class="table table-hover">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Amount</th>
                    <th>Currency</th>
                    <th>Status</th>
                    <th>Completed At</th>
                    <th>Payout Hash</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    <div class="pagination" id="withdrawals-pagination"></div>
<center><p><div id="ad_unit" data-unit-id="6624"></div><script src="https://api.fpadserver.com/static/js/advert_renderer.js"></script></p></center>
    <h2>Deposits</h2>
    <div class="card">
        <table id="deposits-table" class="table table-hover">
            <thead>
                <tr>
                    <th>Username</th>
                    <th>Amount</th>
                    <th>Currency</th>
                    <th>Status</th>
                    <th>Completed At</th>
                    <th>Transaction ID</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
    </div>
    <div class="pagination" id="deposits-pagination"></div>
    <div class="stats-box">
    <h2>Frequently Asked Questions</h2>
    <p><strong>Q: How often is the data updated?</strong><br>
    A: The data on this page is updated every few minutes to reflect the latest transactions.</p>
    <p><strong>Q: What do the transaction statuses mean?</strong><br>
    A: 'Pending' means the transaction is being processed, while 'Completed' means it has been successfully processed and verified on the blockchain.</p>
    <p><strong>Q: How can I verify a payout?</strong><br>
    A: Use the payout hash or transaction ID to search on the respective blockchain explorer for verification.</p>
</div>

<div class="stats-box">
    <h2>Our Commitment to Transparency and Security</h2>
    <p>At FaucetMineHub, we prioritize the security of your transactions. Every withdrawal and deposit is processed with high standards of security and transparency. You can track each transaction with its unique hash or ID, ensuring you always have the details you need.</p>
</div>

</div>

<script>
    let withdrawalsPage = 1;
    let depositsPage = 1;
    const rowsPerPage = 10;
    const maxVisiblePages = 5;

    function fetchWithdrawals(page = 1) {
        withdrawalsPage = page;
        $.ajax({
            url: `statistic_api.php?withdrawals_page=${withdrawalsPage}&rowsPerPage=${rowsPerPage}`,
            method: 'GET',
            success: function(data) {
                $('#total-withdrawals').text(data.total_withdrawals);
                $('#total-withdrawals-usd').text(data.total_withdrawals_usd);

                let withdrawalsHtml = '';
                data.withdrawals.forEach(function(withdrawal) {
                    withdrawalsHtml += `<tr>
                        <td>${withdrawal.username}</td>
                        <td>${withdrawal.amount}</td>
                        <td>${withdrawal.currency}</td>
                        <td>${withdrawal.status}</td>
                        <td>${withdrawal.completed_at}</td>
                        <td>${withdrawal.payout_user_hash}</td>
                    </tr>`;
                });
                $('#withdrawals-table tbody').html(withdrawalsHtml);

                generatePagination(data.total_withdrawals, '#withdrawals-pagination', withdrawalsPage, fetchWithdrawals);
            }
        });
    }

    function fetchDeposits(page = 1) {
        depositsPage = page;
        $.ajax({
            url: `statistic_api.php?deposits_page=${depositsPage}&rowsPerPage=${rowsPerPage}`,
            method: 'GET',
            success: function(data) {
                $('#total-deposits').text(data.total_deposits);
                $('#total-deposits-usd').text(data.total_deposits_usd);

                let depositsHtml = '';
                data.deposits.forEach(function(deposit) {
                    depositsHtml += `<tr>
                        <td>${deposit.username}</td>
                        <td>${deposit.amount}</td>
                        <td>${deposit.currency}</td>
                        <td>${deposit.status}</td>
                        <td>${deposit.completed_at}</td>
                        <td>${deposit.transaction_id}</td>
                    </tr>`;
                });
                $('#deposits-table tbody').html(depositsHtml);

                generatePagination(data.total_deposits, '#deposits-pagination', depositsPage, fetchDeposits);
            }
        });
    }

    function generatePagination(totalItems, paginationContainer, currentPage, fetchFunction) {
        let totalPages = Math.ceil(totalItems / rowsPerPage);
        let paginationHtml = '';
        let startPage = Math.max(1, currentPage - Math.floor(maxVisiblePages / 2));
        let endPage = Math.min(totalPages, startPage + maxVisiblePages - 1);

        if (startPage > 1) {
            paginationHtml += `<a href="#" class="pagination-link" data-page="1">1</a>`;
            if (startPage > 2) {
                paginationHtml += `<span>...</span>`;
            }
        }

        for (let i = startPage; i <= endPage; i++) {
            paginationHtml += `<a href="#" class="pagination-link" data-page="${i}">${i}</a>`;
        }

        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                paginationHtml += `<span>...</span>`;
            }
            paginationHtml += `<a href="#" class="pagination-link" data-page="${totalPages}">${totalPages}</a>`;
        }

        $(paginationContainer).html(paginationHtml);

        $(paginationContainer).find(`[data-page="${currentPage}"]`).addClass('active');

        $('.pagination-link').on('click', function(e) {
            e.preventDefault();
            const page = $(this).data('page');
            fetchFunction(page);
        });
    }

    $(document).ready(function() {
        fetchWithdrawals(withdrawalsPage);
        fetchDeposits(depositsPage);
    });
</script>
</body>
</html>
