<?php include 'dicetrxapi.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tron Dice Game</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <style>
        body {
            background-color: #f5f5f5;
        }

        .container-content {
            max-width: 700px;
            margin: auto;
            margin-top: 50px;
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

        .dice-roll {
            font-size: 3rem;
            color: #6576FF;
            border: 2px solid #2c3782;
            border-radius: 10px;
            padding: 20px;
            background: #e9ecef;
        }

        #chat-container {
            position: fixed;
            bottom: 40px;
            right: 20px;
            width: 300px;
            height: 400px;
            background-color: #fff;
            border: 2px solid #2c3782;
            border-radius: 10px 10px 0 0;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
            z-index: 1000;
            display: block;
        }

        #chat-iframe {
            width: 100%;
            height: 100%;
            border-radius: 10px 10px 0 0;
            border: none;
        }

        #chat-toggle {
            position: fixed;
            bottom: 0;
            right: 20px;
            width: 300px;
            height: 40px;
            background-color: #6576FF;
            color: #fff;
            text-align: center;
            line-height: 40px;
            font-size: 16px;
            cursor: pointer;
            border-radius: 10px 10px 0 0;
            z-index: 1001;
        }

        table thead {
            background-color: #6576FF;
            color: white;
        }

        table tbody tr:hover {
            background-color: #f0f0f0;
        }

        .alert {
            border: 2px solid #2c3782;
            border-radius: 10px;
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
    <div class="container-content">
        <center>
            <p><div id="ad_unit" data-unit-id="6624"></div><script src="https://api.fpadserver.com/static/js/advert_renderer.js"></script></p>
        </center>
        <h1 class="text-center mb-4">Tron Dice Game</h1>

        <p class="text-center fs-5">Current TRX Balance: <strong id="trx-balance"><?= number_format((float)$trx_balance, 8) ?></strong> TRX</p>

        <div class="dice-roll text-center mb-4" id="dice-roll">0000</div>

        <div id="message-container">
            <?php if (!empty($successMessage)): ?>
                <div class="alert alert-success text-center">
                    <?= htmlspecialchars($successMessage, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php elseif (!empty($errorMessage)): ?>
                <div class="alert alert-danger text-center">
                    <?= htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8') ?>
                </div>
            <?php endif; ?>
        </div>

        <form method="POST" id="dice-form" class="mb-4" onsubmit="return preventDoubleSubmit()">
            <div class="mb-3">
                <label for="bet_amount" class="form-label">Bet Amount (TRX):</label>
                <input type="number" name="bet_amount" id="bet_amount" class="form-control" step="0.01" min="0.01" value="0.01" required>
            </div>
            <div class="mb-3">
                <label for="bet_choice" class="form-label">Bet Choice:</label>
                <select name="bet_choice" id="bet_choice" class="form-select" required>
                    <option value="">-- Select Choice --</option>
                    <option value="low">Low (0000 - 4850)</option>
                    <option value="high">High (5149 - 9999)</option>
                </select>
                <input type="hidden" name="client_seed" id="client_seed" value="<?= htmlspecialchars($_SESSION['client_seed'], ENT_QUOTES, 'UTF-8') ?>">
            </div>
            <button type="submit" class="btn btn-success w-100">Roll the Dice</button>
        </form>

        <div class="text-center mb-4">
            <a href="dashboard.php" class="btn btn-primary">Back to Dashboard</a>
        </div>

        <ul class="nav nav-tabs mb-3" id="historyTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="user-bets-tab" data-bs-toggle="tab" data-bs-target="#user-bets" type="button" role="tab" aria-controls="user-bets" aria-selected="true">Last 10 Bets (You)</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="all-bets-tab" data-bs-toggle="tab" data-bs-target="#all-bets" type="button" role="tab" aria-controls="all-bets" aria-selected="false">Last 10 Bets (All Users)</button>
            </li>
        </ul>

        <div class="tab-content" id="historyTabContent">
            <div class="tab-pane fade show active" id="user-bets" role="tabpanel" aria-labelledby="user-bets-tab">
                <h3>Last 10 Bets (You):</h3>
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Bet Amount (TRX)</th>
                            <th>Choice</th>
                            <th>Result</th>
                            <th>Change (TRX)</th>
                            <th>Roll</th>
                        </tr>
                    </thead>
                    <tbody id="user-bets-body">
                        <!-- Updated dynamically via JavaScript -->
                    </tbody>
                </table>
            </div>

            <div class="tab-pane fade" id="all-bets" role="tabpanel" aria-labelledby="all-bets-tab">
                <h3>Last 10 Bets (All Users):</h3>
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Bet Amount (TRX)</th>
                            <th>Choice</th>
                            <th>Result</th>
                            <th>Change (TRX)</th>
                            <th>Roll</th>
                            <th>Username</th>
                        </tr>
                    </thead>
                    <tbody id="all-bets-body">
                        <!-- Updated dynamically via JavaScript -->
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div id="chat-container">
        <iframe id="chat-iframe" src="<?= htmlspecialchars($chatbox_url, ENT_QUOTES, 'UTF-8') ?>"></iframe>
    </div>
    <div id="chat-toggle">Hide Chat</div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            updateBalance();
            updateHistory();

            // Automatically assign the client seed from the server (hidden in the form)
            const clientSeed = "<?= htmlspecialchars($_SESSION['client_seed'], ENT_QUOTES, 'UTF-8') ?>";

            // Set interval to update history every 5 seconds
            setInterval(updateHistory, 5000);
        });

        let diceRollElement = document.getElementById('dice-roll');
        let rollInterval = setInterval(function() {
            let randomRoll = ('0000' + Math.floor(Math.random() * 10000)).slice(-4);
            diceRollElement.textContent = randomRoll;
        }, 100);

        <?php if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($dice_roll)): ?>
            setTimeout(function() {
                clearInterval(rollInterval);
                diceRollElement.textContent = ('0000' + <?= (int)$dice_roll ?>).slice(-4);  // Cast to int for safety
            }, 1000);
        <?php endif; ?>

        function updateBalance() {
            fetch('fetch_balance.php')
                .then(response => response.json())
                .then(data => {
                    if (data.trx_balance !== undefined) { // Check if data exists
                        document.getElementById('trx-balance').textContent = parseFloat(data.trx_balance).toFixed(8);
                    }
                })
                .catch(error => console.error('Error fetching balance:', error));
        }

        function updateHistory() {
            fetch('fetch_history.php')
                .then(response => response.json())
                .then(data => {
                    let userBetsBody = document.getElementById('user-bets-body');
                    let allBetsBody = document.getElementById('all-bets-body');
                    
                    // Update User Bets Table
                    userBetsBody.innerHTML = '';
                    data.user_bets.forEach(bet => {
                        userBetsBody.innerHTML += `
                            <tr>
                                <td>${parseFloat(bet.bet_amount).toFixed(2)}</td>
                                <td>${htmlEscape(bet.bet_choice)}</td>
                                <td>${htmlEscape(bet.result)}</td>
                                <td>${parseFloat(bet.amount_change).toFixed(2)}</td>
                                <td>${parseInt(bet.dice_roll)}</td>
                            </tr>
                        `;
                    });

                    // Update All Bets Table
                    allBetsBody.innerHTML = '';
                    data.all_bets.forEach(bet => {
                        allBetsBody.innerHTML += `
                            <tr>
                                <td>${parseFloat(bet.bet_amount).toFixed(2)}</td>
                                <td>${htmlEscape(bet.bet_choice)}</td>
                                <td>${htmlEscape(bet.result)}</td>
                                <td>${parseFloat(bet.amount_change).toFixed(2)}</td>
                                <td>${parseInt(bet.dice_roll)}</td>
                                <td>${htmlEscape(bet.username)}</td>
                            </tr>
                        `;
                    });
                })
                .catch(error => console.error('Error fetching history:', error));
        }

        function htmlEscape(str) {
            return String(str)
                .replace(/&/g, "&amp;")
                .replace(/</g, "&lt;")
                .replace(/>/g, "&gt;")
                .replace(/"/g, "&quot;")
                .replace(/'/g, "&#039;");
        }

        function preventDoubleSubmit() {
            const button = document.querySelector('button[type="submit"]');
            if (button.disabled) {
                return false;
            }
            button.disabled = true;
            return true;
        }

        // Toggle chat visibility
        var chatToggle = document.getElementById('chat-toggle');
        var chatContainer = document.getElementById('chat-container');

        chatToggle.addEventListener('click', function() {
            if (chatContainer.style.display === "none") {
                chatContainer.style.display = "block";
                chatToggle.innerHTML = "Hide Chat";
                chatToggle.style.bottom = "410px";  // Moves the button up when the chat box is open
            } else {
                chatContainer.style.display = "none";
                chatToggle.innerHTML = "Chat with us!";
                chatToggle.style.bottom = "0";  // Moves the button back down when the chat box is closed
            }
        });
    </script>
</body>
</html>
