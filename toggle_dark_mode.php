<?php
session_start();

header('Content-Type: application/json');

// Check if user_id is set in session
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['success' => false, 'message' => 'User not logged in.']);
    exit();
}

require 'includes/db.php';
require 'config/config.php';

$user_id = $_SESSION['user_id'];

try {
    // Fetch the current dark_mode value
    $stmt = $pdo->prepare("SELECT dark_mode FROM users WHERE id = :user_id");
    $stmt->execute(['user_id' => $user_id]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($user) {
        // Toggle the dark_mode value (if 1, set to 0, if 0, set to 1)
        $new_dark_mode = $user['dark_mode'] == 1 ? 0 : 1;

        // Update the dark_mode value in the database
        $update_stmt = $pdo->prepare("UPDATE users SET dark_mode = :new_dark_mode WHERE id = :user_id");
        $update_stmt->execute([
            'new_dark_mode' => $new_dark_mode,
            'user_id' => $user_id
        ]);

        // Check if the update was successful
        if ($update_stmt->rowCount() > 0) {
            echo json_encode(['success' => true, 'dark_mode' => $new_dark_mode]);
        } else {
            echo json_encode(['success' => false, 'message' => 'No changes were made.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'User not found.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
