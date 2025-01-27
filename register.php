<?php
date_default_timezone_set('Asia/Bangkok');  // Set to GMT+7 (Bangkok timezone)
session_start();
require 'includes/db.php';
require 'config/config.php';
require 'includes/PHPMailer/src/PHPMailer.php';
require 'includes/PHPMailer/src/SMTP.php';
require 'includes/PHPMailer/src/Exception.php';

$appName = $config['app']['name'];
$appUrl = $config['app']['url'];
$appAuthor = $config['app']['author'];
$appDescription = $config['app']['description'];
$logoUrl = $config['app']['logo_url'];
$faviconUrl = $config['app']['favicon_url'];

// If the user is already logged in, redirect them to the dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit(); // Always exit after a redirect to prevent further code execution
}

// Check if the `ref` parameter is present in the URL and store it in the session
if (isset($_GET['ref'])) {
    $_SESSION['referrer_id'] = (int)$_GET['ref'];
}

// Use the referrer ID from the session if available
$referrer_id = isset($_SESSION['referrer_id']) ? (int)$_SESSION['referrer_id'] : null;

$success_message = ''; // Variable to hold success message
$error_message = ''; // Variable to hold error message

// Forbidden usernames list
$forbidden_usernames = ['admin', 'administrator', 'staff', 'support', 'moderator', 'root'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $username = trim($_POST['username']);
    $password_hash = password_hash($password, PASSWORD_BCRYPT);
    $ip = $_SERVER['REMOTE_ADDR'];
    $recaptcha_secret = '6Le-FV8qAAAAAAt7WbQ94Hn36g_SyloDHrWWGjKT'; // Replace with your secret key
    $recaptcha_response = $_POST['g-recaptcha-response'];

    // Simulate device fingerprint by storing user-agent and IP address in a combined format
    $device_fingerprint = md5($_SERVER['HTTP_USER_AGENT'] . $ip);

    // Verify Google reCAPTCHA
    $response = file_get_contents("https://www.google.com/recaptcha/api/siteverify?secret=$recaptcha_secret&response=$recaptcha_response");
    $response_data = json_decode($response);

    if (!$response_data->success) {
        // reCAPTCHA failed
        $error_message = 'Captcha verification failed. Please try again.';
    } else {
        // Validate the email and username
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $error_message = 'Invalid email format.';
        } elseif (empty($username)) {
            $error_message = 'Username cannot be empty. Please provide a username.';
        } elseif (in_array(strtolower($username), $forbidden_usernames)) {
            $error_message = 'The username is not allowed. Please choose another username.';
        } elseif (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $error_message = 'Username can only contain letters, numbers, and underscores. No symbols allowed.';
        } else {
            // Check if the IP address has already been used to create an account
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE ip_address = :ip OR device_fingerprint = :fingerprint");
            $stmt->execute(['ip' => $ip, 'fingerprint' => $device_fingerprint]);
            $ip_exists = $stmt->fetchColumn();

            if ($ip_exists > 0) {
                $error_message = 'You already have an account, only one account is allowed per device/IP.';
            } else {
                // Validate if the username or email already exists in the database
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE username = :username OR email = :email");
                $stmt->execute(['username' => $username, 'email' => $email]);
                $user_exists = $stmt->fetchColumn();

                if ($user_exists > 0) {
                    $error_message = 'Username or email already taken. Please choose another.';
                } else {
                    // Generate a unique verification token
                    $verification_token = bin2hex(random_bytes(16));

                    // Insert the user into the database with referral information, IP address, and device fingerprint
                    $stmt = $pdo->prepare("INSERT INTO users (email, password, ip_address, username, referrer_id, device_fingerprint, verification_token, is_verified) 
                                           VALUES (:email, :password, :ip, :username, :referrer_id, :device_fingerprint, :verification_token, 0)"); // 0 = not verified
                    $stmt->execute([
                        'email' => $email,
                        'password' => $password_hash,
                        'ip' => $ip,
                        'username' => $username,
                        'referrer_id' => $referrer_id,
                        'device_fingerprint' => $device_fingerprint,
                        'verification_token' => $verification_token
                    ]);

                    // Send verification email
                    $mail = new PHPMailer\PHPMailer\PHPMailer();

                    // PHPMailer settings
                    $mail->isSMTP();                                            // Send using SMTP
					$mail->Host       = $config['smtp']['host'];                // Set the SMTP server to send through
					$mail->SMTPAuth   = true;                                   // Enable SMTP authentication
					$mail->Username   = $config['smtp']['username'];            // SMTP username
					$mail->Password   = $config['smtp']['password'];            // SMTP password
					$mail->SMTPSecure = $config['smtp']['encryption'];          // Enable TLS encryption or SSL
					$mail->Port       = $config['smtp']['port'];                // TCP port to connect to
					
                    // Email subject and body content
                    $mail->setFrom($smtp_config['from_email'], $smtp_config['from_name']);
                    $mail->addAddress($email);  // Add recipient
                    $mail->isHTML(true);
                    $mail->Subject = 'Verify your email address';
                    $mail->Body    = 'Please verify your email by clicking the following link: <a href="' . $appUrl . '/verify.php?token=' . $verification_token . '">Verify Email</a>';

                    if ($mail->send()) {
                        $success_message = 'Registration successful! Please check your email to verify your account.';
                    } else {
                        $error_message = 'Error sending verification email: ' . $mail->ErrorInfo;
                    }

                    // Clear the referrer ID from the session after registration (optional)
                    unset($_SESSION['referrer_id']);
                }
            }
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
    <meta name="description" content="get started with our <?php echo htmlspecialchars($appName); ?>.com staking platform!">
    <!-- Fav Icon  -->
    <link rel="shortcut icon" href="images/favicon2.png">
    <!-- Page Title  -->
    <title>Register | <?php echo htmlspecialchars($appName); ?></title>
    <!-- StyleSheets  -->
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

      <script async src="https://appsha-pnd.ctengine.io/js/script.js?wkey=9ck0lg7R8t"></script>
    
</head>
<body class="nk-body bg-white npc-general pg-auth">
    <div class="nk-app-root">
        <div class="nk-main ">
            <div class="nk-wrap nk-wrap-nosidebar">
                <div class="nk-content ">
                    <div class="nk-block nk-block-middle nk-auth-body  wide-xs">
                        <div class="brand-logo pb-4 text-center">
                            <a href="/" class="logo-link">
                                <img class="logo-light logo-img logo-img-lg" src="himages/new-logo.png" srcset="images/new-logo.png" alt="logo">
                                <img class="logo-dark logo-img logo-img-lg" src="images/new-logo.png" srcset="images/new-logo.png" alt="logo-dark">
                            </a>
                        </div>
                        <div class="card card-bordered">
                            <div class="card-inner card-inner-lg">
                                <div class="nk-block-head">
                                    <div class="nk-block-head-content">
                                        <h4 class="nk-block-title">Register</h4>
                                        <div class="nk-block-des">
                                            <p>Create New <?php echo htmlspecialchars($appName); ?> Account</p>
                                            <p>Keep in mind, making multiple account will cause a permanent ban and you will never can create another account anymore.</p>
                                        </div>
                                        <!-- Display success message if available -->
                                        <?php if (!empty($success_message)): ?>
                                            <div class="alert alert-success">
                                                <?php echo $success_message; ?>
                                            </div>
                                        <?php endif; ?>

                                        <!-- Display error message if available -->
                                        <?php if (!empty($error_message)): ?>
                                            <div class="alert alert-danger">
                                                <?php echo $error_message; ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                                <form method="POST">
                                    <div class="form-group">
                                        <label class="form-label" for="username">Username</label>
                                        <div class="form-control-wrap">
                                            <input type="text" name="username" class="form-control form-control-lg" id="username" placeholder="Enter your username" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="email">Email (FaucetPay Only):</label>
                                        <div class="form-control-wrap">
                                            <input type="email" name="email" id="email" class="form-control form-control-lg" placeholder="We only accept withdraw to FaucetPay email address" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="form-label" for="password">Password</label>
                                        <div class="form-control-wrap">
                                            <a href="#" class="form-icon form-icon-right passcode-switch lg" data-target="password">
                                                <em class="passcode-icon icon-show icon ni ni-eye"></em>
                                                <em class="passcode-icon icon-hide icon ni ni-eye-off"></em>
                                            </a>
                                            <input type="password" name="password" id="password" class="form-control form-control-lg" placeholder="Enter your password" required>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="custom-control custom-control-xs custom-checkbox">
                                            <input type="checkbox" class="custom-control-input" id="checkbox" required>
                                            <label class="custom-control-label" for="checkbox">I agree to <?php echo htmlspecialchars($appName); ?> <a href="privacy_policy" target="_blank">Privacy Policy</a> &amp; <a href="terms_of_service" target="_blank"> Terms.</a></label>
                                        </div>
                                    </div>
                                    <!-- Hidden input field for referral ID -->
                                    <input type="hidden" name="referrer_id" value="<?php echo htmlspecialchars($referrer_id); ?>">
                                    <div class="form-group">
                                        <!-- Add Google reCAPTCHA here -->
                                        <center>
                                            <div class="g-recaptcha" data-sitekey="6Le-FV8qAAAAAN0IwV_O__91dIph7yAQpqZNLkby"></div>
                                        </center><!-- Replace with your site key -->
                                        <button class="btn btn-lg btn-primary btn-block">Register</button>
                                    </div>
                                </form>
                                <div class="form-note-s2 text-center pt-4"> Already have an account? <a href="login"><strong>Sign in instead</strong></a>
                                <br>
                                <center>
                                    <span id="ct_cNkrbPu30Vx"></span>
                                    <iframe id='banner_advert_wrapper_6625' src='https://api.fpadserver.com/banner?id=6625&size=250x250' width='250px' height='250px' frameborder='0'></iframe>
                                </center>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="nk-footer nk-auth-footer-full">
                        <div class="container wide-lg">
                            <div class="row g-3">
                                <div class="col-lg-6 order-lg-last">
                                    <ul class="nav nav-sm justify-content-center justify-content-lg-end">
                                        <li class="nav-item">
                                            <a class="nav-link" href="terms_of_service">Terms & Condition</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="privacy_policy">Privacy Policy</a>
                                        </li>
                                        <li class="nav-item">
                                            <a class="nav-link" href="#">Help</a>
                                        </li>
                                    </ul>
                                </div>
                                <div class="col-lg-6">
                                    <div class="nk-block-content text-center text-lg-left">
                                        <p class="text-soft">&copy; 2024 <?php echo htmlspecialchars($appName); ?>.com. All Rights Reserved.</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- wrap @e -->
            </div>
            <!-- content @e -->
        </div>
        <!-- main @e -->
    </div>
    <!-- app-root @e -->
    <!-- JavaScript -->
    <script src="./assets/js/bundle.js?ver=3.2.0"></script>
    <script src="./assets/js/scripts.js?ver=3.2.0"></script>
    <!-- Load the Google reCAPTCHA script -->
    <script src="https://www.google.com/recaptcha/api.js" async defer></script>
</html>
