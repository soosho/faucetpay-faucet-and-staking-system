<?php
date_default_timezone_set('Asia/Bangkok');  // Set to GMT+7 (Bangkok timezone)
session_start();
require 'includes/db.php';
require 'config/config.php';
require 'includes/PHPMailer/src/PHPMailer.php';
require 'includes/PHPMailer/src/SMTP.php';
require 'includes/PHPMailer/src/Exception.php';

$success_message = ''; // Variable to hold success message
$error_message = '';   // Variable to hold error message

// Handle form submission if the user inputs their email
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);

    // Validate the email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Please enter a valid email address.';
    } else {
        // Check if the email exists in the users table
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        if ($user) {
            // Check if the account is already verified
            if ($user['is_verified'] == 1) {
                $error_message = 'Your account is already verified. You can log in now.';
            } else {
                // Check if the user requested verification in the last 30 minutes
                $lastRequestTime = !empty($user['last_verification_request']) ? new DateTime($user['last_verification_request']) : null;
                $currentTime = new DateTime();

                if ($lastRequestTime && $currentTime->diff($lastRequestTime)->i < 30) {
                    $error_message = 'You have already requested a verification email recently. Please wait 30 minutes from your last request before requesting again.';
                } else {
                    // Generate a new verification token or reuse the existing one
                    $verification_token = $user['verification_token'] ?? bin2hex(random_bytes(16));

                    // Update the verification token and last verification request timestamp in the database
                    $stmt = $pdo->prepare("UPDATE users SET verification_token = :token, last_verification_request = NOW() WHERE id = :user_id");
                    $stmt->execute(['token' => $verification_token, 'user_id' => $user['id']]);

                    // Send the verification email
                    $mail = new PHPMailer\PHPMailer\PHPMailer();

                    // Configure PHPMailer settings
                    $mail->isSMTP();                                            // Send using SMTP
					$mail->Host       = $config['smtp']['host'];                // Set the SMTP server to send through
					$mail->SMTPAuth   = true;                                   // Enable SMTP authentication
					$mail->Username   = $config['smtp']['username'];            // SMTP username
					$mail->Password   = $config['smtp']['password'];            // SMTP password
					$mail->SMTPSecure = $config['smtp']['encryption'];          // Enable TLS encryption or SSL
					$mail->Port       = $config['smtp']['port'];                // TCP port to connect to

                    // Email content
                    $mail->setFrom($smtp_config['from_email'], $smtp_config['from_name']);
                    $mail->addAddress($user['email']);  // Add recipient

                    $mail->isHTML(true);  // Set email format to HTML
                    $mail->Subject = 'Verify your email address';
                    $mail->Body    = 'Please verify your email by clicking the following link: <a href="' . $config['app']['url'] . '/verify.php?token=' . $verification_token . '">Verify Email</a>';

                    // Try to send the email
                    if ($mail->send()) {
                        $success_message = 'A verification email has been sent to your email address. Please check your inbox.';
                    } else {
                        $error_message = 'Error sending the verification email: ' . $mail->ErrorInfo;
                    }
                }
            }
        } else {
            $error_message = 'Email not found. Please check your email address.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resend Verification Email</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
    <div class="container mt-5">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <div class="card">
                    <div class="card-body">
                        <h2 class="text-center">Resend Verification Email</h2>

                        <!-- Display success or error messages -->
                        <?php if (!empty($success_message)): ?>
                            <div class="alert alert-success">
                                <?php echo htmlspecialchars($success_message); ?>
                            </div>
                        <?php endif; ?>

                        <?php if (!empty($error_message)): ?>
                            <div class="alert alert-danger">
                                <?php echo htmlspecialchars($error_message); ?>
                            </div>
                        <?php endif; ?>

                        <p>If you haven't received a verification email, enter your email address below to request a new one.</p>

                        <!-- Form to input email -->
                        <form method="POST">
                            <div class="form-group mb-3">
                                <label for="email">Email address:</label>
                                <input type="email" class="form-control" name="email" id="email" placeholder="Enter your email" required>
                            </div>
                            <button type="submit" class="btn btn-primary w-100">Resend Verification Email</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
