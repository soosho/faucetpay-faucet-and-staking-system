<?php
session_start();
require 'includes/db.php';
date_default_timezone_set('Asia/Bangkok');  // This sets GMT+7 timezone
// Ensure the user is logged in
if (!isset($_SESSION['user_id'])) {
    exit(); // End the script if the user is not logged in
}

// Update the last active time for the current user
$stmt = $pdo->prepare("UPDATE users SET last_active = NOW() WHERE id = :user_id");
$stmt->execute(['user_id' => $_SESSION['user_id']]);

echo json_encode(['success' => true]);
?>
