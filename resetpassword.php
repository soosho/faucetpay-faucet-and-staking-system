<?php
session_start();
require 'includes/db.php';  // Your database connection
$config = require 'config/config.php';  // Config file
date_default_timezone_set('Asia/Bangkok');  // This sets GMT+7 timezone
// Check if the token exists in the URL
if (!isset($_GET['token'])) {
    die('Invalid request. Token is missing.');
}

$token = $_GET['token'];

// Verify the token exists in the database and is not expired
$stmt = $pdo->prepare("SELECT user_id, expires_at FROM password_resets WHERE token = :token");
$stmt->execute(['token' => $token]);
$reset_request = $stmt->fetch();

if (!$reset_request) {
    die('Invalid or expired token.');
}

// Check if the token has expired
$expires_at = new DateTime($reset_request['expires_at']);
$current_time = new DateTime();

if ($current_time > $expires_at) {
    die('This token has expired. Please request a new password reset.');
}

// If form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate the new passwords
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($new_password) || empty($confirm_password)) {
        $error_message = 'Please fill out all fields.';
    } elseif ($new_password !== $confirm_password) {
        $error_message = 'Passwords do not match.';
    } elseif (strlen($new_password) < 8) {
        $error_message = 'Password must be at least 8 characters.';
    } else {
        // Hash the new password
        $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);

        // Update the user's password
        $stmt = $pdo->prepare("UPDATE users SET password = :password WHERE id = :user_id");
        $stmt->execute([
            'password' => $hashed_password,
            'user_id' => $reset_request['user_id']
        ]);

        // Remove the reset token from the database
        $stmt = $pdo->prepare("DELETE FROM password_resets WHERE token = :token");
        $stmt->execute(['token' => $token]);

        // Redirect to login page or show success message
        header('Location: login.php?message=Password has been reset. You can now log in.');
        exit();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password</title>
    <link rel="stylesheet" href="assets/style.css"> <!-- Include your CSS file -->
</head>
<body>
    <div class="container">
        <h2>Reset Password</h2>

        <?php if (isset($error_message)): ?>
            <p style="color: red;"><?php echo htmlspecialchars($error_message); ?></p>
        <?php endif; ?>

        <form method="POST">
            <div class="form-group">
                <label for="new_password">New Password:</label>
                <input type="password" name="new_password" id="new_password" required>
            </div>
            <div class="form-group">
                <label for="confirm_password">Confirm New Password:</label>
                <input type="password" name="confirm_password" id="confirm_password" required>
            </div>
            <button type="submit">Reset Password</button>
        </form>
    </div>
</body>
</html>
