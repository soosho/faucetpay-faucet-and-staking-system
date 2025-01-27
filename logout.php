<?php
// public/logout.php
session_start(); // Always start the session at the beginning
date_default_timezone_set('Asia/Bangkok');  // This sets GMT+7 timezone
require 'includes/db.php';
require 'config/config.php';

if (isset($_SESSION['user_id'])) {
    // Insert a new record for logout activity
    $stmt = $pdo->prepare("INSERT INTO user_activity_log (user_id, logout_time, ip_address, user_agent, status) 
                           VALUES (:user_id, NOW(), :ip_address, :user_agent, 'logged_out')");
    $stmt->execute([
        'user_id' => $_SESSION['user_id'],
        'ip_address' => $_SERVER['REMOTE_ADDR'],
        'user_agent' => $_SERVER['HTTP_USER_AGENT']
    ]);
}

// Unset all session variables
$_SESSION = [];

// Destroy the session entirely (session data and session cookie)
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Finally, destroy the session
session_destroy();

// Remove login token cookie if it exists
if (isset($_COOKIE['login_token'])) {
    setcookie('login_token', '', time() - 3600, '/', '', false, true);  // Expire the cookie
}

// Redirect to the index page (or login page)
header("Location: index.php");
exit();
