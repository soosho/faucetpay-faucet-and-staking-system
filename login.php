<?php
date_default_timezone_set('Asia/Bangkok');  // Set to GMT+7 (Bangkok timezone)
session_start(); // Always start the session at the beginning

require 'includes/db.php';  // Make sure db connection is included before using it
require 'config/config.php';  // Include configuration

// Check if the user is logged in via a cookie
if (isset($_COOKIE['login_token'])) {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE login_token = :token");
    $stmt->execute(['token' => $_COOKIE['login_token']]);
    $user = $stmt->fetch();

    if ($user) {
        // Log in the user if token is valid
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];

        // Redirect to dashboard
        header('Location: dashboard.php');
        exit();
    }
}

// Redirect logged-in users away from the login page if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: dashboard.php');
    exit();
}

$appName = $config['app']['name'];
$appUrl = $config['app']['url'];
$appAuthor = $config['app']['author'];
$appDescription = $config['app']['description'];
$logoUrl = $config['app']['logo_url'];
$faviconUrl = $config['app']['favicon_url'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $device_fingerprint = $_POST['fingerprint'] ?? '';  // Retrieve fingerprint from JS
    $remember_me = isset($_POST['remember_me']);  // Check if "Remember me" is selected
    $ip_address = $_SERVER['REMOTE_ADDR'];

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = 'Invalid email format.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->execute(['email' => $email]);
        $user = $stmt->fetch();

        // Check if user exists
        if ($user) {
            // Check if the user has verified their email
            if ($user['is_verified'] == 0) {
                $error_message = 'Your email is not verified. Please check your inbox or <a href="resend_verification.php">click here</a> to resend the verification email.';
            }
            // Check if the user is banned
            elseif ($user['banned'] == 1) {
                // Display the ban reason if available
                $ban_reason = $user['ban_reason'] ? 'Reason: ' . htmlspecialchars($user['ban_reason']) : 'No specific reason provided.';
                $error_message = 'Your account has been banned. ' . $ban_reason . ' Please contact support on telegram for more information.';
            }
            // Verify the password if the user is not banned and verified
            elseif (password_verify($password, $user['password'])) {
                // Check for multiple accounts with the same fingerprint or IP address, excluding admin users
                $stmt_check_fingerprint = $pdo->prepare("
                    SELECT COUNT(*) as account_count 
                    FROM users 
                    WHERE (fingerprint = :fingerprint OR last_login_ip = :ip_address) 
                      AND id != :user_id
                      AND admin = 0
                ");
                $stmt_check_fingerprint->execute([
                    'fingerprint' => $device_fingerprint,
                    'ip_address' => $ip_address,
                    'user_id' => $user['id']
                ]);
                $result = $stmt_check_fingerprint->fetch();

                if ($result['account_count'] > 0) {
                    // Log the detection for further review
                    $stmt_log_detection = $pdo->prepare("
                        INSERT INTO user_detection_log (user_id, detected_fingerprint, detected_ip, created_at) 
                        VALUES (:user_id, :fingerprint, :ip_address, NOW())
                    ");
                    $stmt_log_detection->execute([
                        'user_id' => $user['id'],
                        'fingerprint' => $device_fingerprint,
                        'ip_address' => $ip_address
                    ]);
                    
                    // Show a warning or take further action (e.g., additional verification)
                    $error_message = 'Multiple accounts detected. Please verify your account.';
                } else {
                    // Regenerate session ID to prevent session fixation attacks
                    session_regenerate_id(true);

                    // Store user ID in the session
                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['email'] = $user['email'];

                    // Handle "Remember me" functionality
                    if ($remember_me) {
                        // Generate a token for persistent login and store it in the DB
                        $token = bin2hex(random_bytes(32));
                        $stmt = $pdo->prepare("UPDATE users SET login_token = :token WHERE id = :user_id");
                        $stmt->execute(['token' => $token, 'user_id' => $user['id']]);

                        // Set a cookie for the token (valid for 7 days)
                        setcookie('login_token', $token, time() + (86400 * 7), "/", "", false, true); // HttpOnly flag for security
                    }

                    // Insert or update the fingerprint in the users table
                    $stmt_update_fingerprint = $pdo->prepare("UPDATE users SET fingerprint = :fingerprint, last_login_ip = :ip_address WHERE id = :user_id");
                    $stmt_update_fingerprint->execute([
                        'fingerprint' => $device_fingerprint,
                        'ip_address' => $ip_address,
                        'user_id' => $user['id']
                    ]);

                    // Insert a new record for login activity with the fingerprint
                    $stmt = $pdo->prepare("INSERT INTO user_activity_log (user_id, login_time, ip_address, user_agent, status, fingerprint) 
                                           VALUES (:user_id, NOW(), :ip_address, :user_agent, 'logged_in', :fingerprint)");
                    $stmt->execute([
                        'user_id' => $user['id'],
                        'ip_address' => $_SERVER['REMOTE_ADDR'],
                        'user_agent' => $_SERVER['HTTP_USER_AGENT'],
                        'fingerprint' => $device_fingerprint
                    ]);

                    // Redirect to the dashboard
                    header('Location: dashboard.php');
                    exit();
                }
            } else {
                $error_message = 'Invalid login credentials!';
            }
        } else {
            $error_message = 'Invalid login credentials!';
        }
    }
}
?>




<!DOCTYPE html>
<html lang="zxx" class="js">

<head>
    <base href="/">
    <meta charset="utf-8">
    <meta name="author" content="melons">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="Get started with our <?php echo htmlspecialchars($appName); ?> staking platform!">
    <!-- Fav Icon -->
    <link rel="shortcut icon" href="images/favicon2.png">
    <!-- Page Title -->
    <title>Login | <?php echo htmlspecialchars($appName); ?></title>
    <!-- StyleSheets -->
    <link rel="stylesheet" href="assets/css/dashlite.css?ver=3.2.0">
    <link id="skin-default" rel="stylesheet" href="assets/css/theme.css?ver=3.2.0">
    <!-- Google tag (gtag.js) -->
<script async src="https://www.googletagmanager.com/gtag/js?id=G-F4ERE043ZG"></script>
<script>
  window.dataLayer = window.dataLayer || [];
  function gtag(){dataLayer.push(arguments);}
  gtag('js', new Date());

  gtag('config', 'G-F4ERE043ZG');
</script>
</head>
<body class="nk-body bg-white npc-general pg-auth">
    <div class="nk-app-root">
        <div class="nk-main">
            <div class="nk-wrap nk-wrap-nosidebar">
                <div class="nk-content">
                    <div class="nk-block nk-block-middle nk-auth-body wide-xs">
                        <div class="brand-logo pb-4 text-center">
                            <a href="/" class="logo-link">
                                <img class="logo-light logo-img logo-img-lg" src="images/new-logo.png" alt="logo">
                                <img class="logo-dark logo-img logo-img-lg" src="images/new-logo.png" alt="logo-dark">
                            </a>
                        </div>
                        <div class="card card-bordered">
                            <div class="card-inner card-inner-lg">
                                <div class="nk-block-head">
                                    <div class="nk-block-head-content">
                                        <h4 class="nk-block-title">Sign-In</h4>
                                        <div class="nk-block-des">
                                            <p>Access the <?php echo htmlspecialchars($appName); ?> panel using your email and password.</p>
                                            <?php if (isset($error_message)): ?>
                                                <div class="alert alert-danger">
                                                    <?php echo $error_message; ?>
                                                </div>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                                <form method="POST" id="login-form">
    <div class="form-group">
        <div class="form-label-group">
            <label class="form-label" for="default-01">Email</label>
        </div>
        <div class="form-control-wrap">
            <input type="email" class="form-control form-control-lg" id="default-01" name="email" placeholder="Enter your email address" autocomplete="username" required>
        </div>
    </div>
    <div class="form-group">
        <div class="form-label-group">
            <label class="form-label" for="password">Password</label>
            <a class="link link-primary link-sm" href="request_password_reset">Forgot Password?</a>
        </div>
        <div class="form-control-wrap">
            <a href="#" class="form-icon form-icon-right passcode-switch lg" data-target="password">
                <em class="passcode-icon icon-show icon ni ni-eye"></em>
                <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
            </a>
            <input type="password" name="password" id="password" class="form-control form-control-lg" placeholder="Enter your password" autocomplete="current-password" required>
        </div>
    </div>

    <!-- Remember me option -->
    <div class="form-group">
        <label>
            <input type="checkbox" name="remember_me" value="1" checked> Keep me logged in for 7 days
        </label>
    </div>

    <input type="hidden" name="fingerprint" id="fingerprint" value="">
    <center>
        <p><div id="ad_unit" data-unit-id="6624"></div><script src="https://api.fpadserver.com/static/js/advert_renderer.js"></script></p>
    </center>
    <p></p>
    <div class="form-group">
        <button class="btn btn-lg btn-primary btn-block">Sign in</button>
    </div>
</form>

                                <div class="form-note-s2 text-center pt-4">New on our platform? <a href="register">Create an account</a></div>
                                <div class="form-note-s2 text-center pt-4">Not receiving Email Verification? <a href="resend_verification">Resend Verification</a></div>
                            </div>
                        </div>
                    </div>
                    <div class="nk-footer nk-auth-footer-full">
                        <div class="container wide-lg">
                            <div class="row g-3">
                                <div class="col-lg-6 order-lg-last">
                                    <ul class="nav nav-sm justify-content-center justify-content-lg-end">
                                        <li class="nav-item"><a class="nav-link" href="terms_of_service">Terms & Condition</a></li>
                                        <li class="nav-item"><a class="nav-link" href="privacy_policy">Privacy Policy</a></li>
                                        <li class="nav-item"><a class="nav-link" href="#">Help</a></li>
                                    </ul>
                                </div>
                                <div class="col-lg-6">
                                    <div class="nk-block-content text-center text-lg-left">
                                        <p class="text-soft">&copy; 2024 <?php echo htmlspecialchars($appName); ?>. All Rights Reserved.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <!-- JavaScript -->
    <script src="./assets/js/bundle.js?ver=3.2.0"></script>
    <script src="./assets/js/scripts.js?ver=3.2.0"></script>
    <script>
        function getFingerprint() {
            const canvas = document.createElement("canvas");
            const ctx = canvas.getContext("2d");
            ctx.font = "40px Arial";
            ctx.fillText("Device fingerprinting", 10, 50);
            const canvasFingerprint = canvas.toDataURL();

            const screenResolution = window.screen.width + "x" + window.screen.height;
            const timezone = new Date().getTimezoneOffset();
            const memory = navigator.deviceMemory || 'unknown';
            const cpuCores = navigator.hardwareConcurrency || 'unknown';

            const fingerprint = {
                screenResolution: screenResolution,
                timezone: timezone,
                canvas: canvasFingerprint,
                memory: memory,
                cpuCores: cpuCores,
                userAgent: navigator.userAgent,
            };

            const fingerprintString = JSON.stringify(fingerprint);
            return btoa(fingerprintString);
        }

        document.addEventListener("DOMContentLoaded", function () {
            const fingerprint = getFingerprint();
            document.getElementById("fingerprint").value = fingerprint;
        });
    </script>
</body>
</html>
