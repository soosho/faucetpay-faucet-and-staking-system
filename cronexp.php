<?php
require 'includes/db.php'; // Include your database connection
require 'config/config.php'; // Include your database connection
// Fetch all users
$stmt = $pdo->query("SELECT id, exp FROM users");
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($users as $user) {
    $user_id = $user['id'];
    $user_exp = $user['exp'];

    // Find the level based on experience
    $stmt_level = $pdo->prepare("
        SELECT level FROM user_levels
        WHERE experience_required <= :user_exp
        ORDER BY level DESC
        LIMIT 1
    ");
    $stmt_level->execute(['user_exp' => $user_exp]);
    $level = $stmt_level->fetchColumn();

    if ($level !== false) {
        // Update the user's level if necessary
        $stmt_update = $pdo->prepare("
            UPDATE users 
            SET level = :level 
            WHERE id = :user_id
        ");
        $stmt_update->execute(['level' => $level, 'user_id' => $user_id]);
    }
}

echo "User levels updated based on experience.";
?>