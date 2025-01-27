<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: login.php'); // Redirect to login page if user is not logged in
    exit();
}

// Display cancellation message
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Payment Canceled</title>
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
            color: #dc3545;
        }
        .message-box p {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <div class="message-box">
        <h2>Payment Canceled</h2>
        <p>Payment was canceled. Please try again.</p>
        <p>You will be redirected to the deposit page shortly.</p>
    </div>

    <script>
        // Redirect to the deposit page after 3 seconds
        setTimeout(function(){
            window.location.href = '/deposit'; // Update the path if necessary
        }, 3000); // 3000 milliseconds = 3 seconds
    </script>
</body>
</html>
