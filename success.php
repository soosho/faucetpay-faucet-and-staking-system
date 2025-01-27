<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page if user is not logged in
    exit();
}

// You can also fetch details about the payment here, for example using $_GET or $_POST if necessary

// Display success message
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Payment Success</title>
    <!-- You can include Bootstrap or other styles here -->
    <link rel="stylesheet" href="assets/css/bootstrap.min.css"> <!-- Bootstrap CSS -->
    <style>
        body {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
            background-color: #f7f9fc;
            font-family: Arial, sans-serif;
        }
        .message-box {
            background-color: #ffffff;
            border: 1px solid #e2e8f0;
            border-radius: 8px;
            padding: 20px;
            text-align: center;
            max-width: 400px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .message-box h2 {
            font-size: 24px;
            margin-bottom: 10px;
            color: #28a745;
        }
        .message-box p {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="message-box">
        <h2>Payment Successful!</h2>
        <p>Thank you for your payment! Your deposit is being processed.</p>
        <p>You will be redirected to your transaction history shortly.</p>
    </div>

    <script>
        // Redirect to history.php after 3 seconds
        setTimeout(function(){
            window.location.href = 'history.php'; // Ensure the file extension is correct
        }, 3000); // 3000 milliseconds = 3 seconds
    </script>
</body>
</html>
