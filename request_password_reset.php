<?php
session_start();
require 'includes/db.php';  // Database connection
$config = require 'config/config.php';  // Load config file
require 'send_password_reset.php';  // This is the function we created for sending emails
date_default_timezone_set('Asia/Bangkok');  // This sets GMT+7 timezone
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = $_POST['email'];
    $recaptcha_secret = '6Ld3FF8qAAAAAFBSoEHcYSFAMH7Fxb1Y_s2YQzqH';  // Replace with your secret key
    $recaptcha_response = $_POST['g-recaptcha-response'];

    // Verify Google reCAPTCHA
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptcha_secret&response=$recaptcha_response");
    $response_data = json_decode($response);

    if (!$response_data->success) {
        // reCAPTCHA failed
        $_SESSION['message'] = 'Captcha verification failed. Please try again.';
        header('Location: request_password_reset.php');
        exit();
    }

    // Check if the email exists in the database
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute(['email' => $email]);
    $user = $stmt->fetch();

    if ($user) {
        // Check if a password reset request has been made within the last hour
        $stmt = $pdo->prepare("SELECT * FROM password_resets WHERE user_id = :user_id AND expires_at > NOW()");
        $stmt->execute(['user_id' => $user['id']]);
        $existing_request = $stmt->fetch();

        if ($existing_request) {
            // Calculate the time remaining in minutes
            $current_time = new DateTime();
            $expires_at = new DateTime($existing_request['expires_at']);
            $time_remaining = $expires_at->getTimestamp() - $current_time->getTimestamp();
            $minutes_remaining = ceil($time_remaining / 60); // Convert to minutes and round up

            // Display a message indicating how many minutes are left
            $_SESSION['message'] = 'You have already requested a password reset. Please try again in ' . $minutes_remaining . ' minutes.';
            header('Location: request_password_reset.php');
            exit();
        }

        // Generate a reset token
        $token = bin2hex(random_bytes(32));
        $expires_at = new DateTime();
        $expires_at->modify('+1 hour');  // Token valid for 1 hour

        // Insert the token into the password_resets table
        $stmt = $pdo->prepare("INSERT INTO password_resets (user_id, token, expires_at) VALUES (:user_id, :token, :expires_at)");
        $stmt->execute([
            'user_id' => $user['id'],
            'token' => $token,
            'expires_at' => $expires_at->format('Y-m-d H:i:s')
        ]);

        // Send the password reset email
        sendPasswordResetEmail($email, $token);

        // Show a success message
        $_SESSION['message'] = 'If the email exists, a password reset link will be sent. <a href="login.php">Click here to go back to login page</a>';
    } else {
        $_SESSION['message'] = 'If the email exists, a password reset link will be sent. <a href="login.php">Click here to go back to login page</a>';
    }

    header('Location: request_password_reset.php');
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Password Reset</title>
    <link rel="stylesheet" href="assets/style.css"> <!-- Your CSS -->
    <script src="https://www.google.com/recaptcha/api.js?render=6Ld3FF8qAAAAALq64KzKcy7vx9hVwf4ou2fRJ86Q"></script> <!-- Replace with your site key -->
</head>
<body>
    <div class="container">
        <h2>Request Password Reset</h2>

        <?php if (isset($_SESSION['message'])): ?>
            <p><?php echo $_SESSION['message']; unset($_SESSION['message']); ?></p>
        <?php endif; ?>

        <form method="POST">
    <div class="form-group">
        <label for="email">Email Address:</label>
        <input type="email" name="email" id="email" required>
    </div>

    <!-- Add hidden input to hold reCAPTCHA token -->
    <input type="hidden" name="g-recaptcha-response" id="g-recaptcha-response">

    <button type="submit">Request Password Reset</button>
</form>

        <!-- Load the Google reCAPTCHA script -->
        <script src="https://www.google.com/recaptcha/api.js?render="6Ld3FF8qAAAAALq64KzKcy7vx9hVwf4ou2fRJ86Q"></script>
        <script>
            grecaptcha.ready(function() {
                grecaptcha.execute('6Ld3FF8qAAAAALq64KzKcy7vx9hVwf4ou2fRJ86Q', {action: 'submit'}).then(function(token) {
                    document.getElementById('g-recaptcha-response').value = token;
                });
            });
        </script>
    </div>
</body>
</html>
