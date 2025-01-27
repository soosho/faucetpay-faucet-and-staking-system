<?php
include 'headerlogged.php';
date_default_timezone_set('Asia/Bangkok');  // This sets GMT+7 timezone
// Initialize message
$message = ''; 

// If form is submitted (for password update)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];
    
    // Fetch user details
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id");
    $stmt->execute(['user_id' => $_SESSION['user_id']]);
    $user = $stmt->fetch();
    
    // Verify current password
    if (password_verify($current_password, $user['password'])) {
        // Check if new passwords match
        if ($new_password === $confirm_new_password) {
            // Hash the new password
            $new_password_hashed = password_hash($new_password, PASSWORD_BCRYPT);
            
            // Update the password in the database
            $stmt = $pdo->prepare("UPDATE users SET password = :new_password WHERE id = :user_id");
            $stmt->execute(['new_password' => $new_password_hashed, 'user_id' => $_SESSION['user_id']]);
            
            $message = '<div class="alert alert-success">Password updated successfully!</div>';
        } else {
            $message = '<div class="alert alert-danger">New passwords do not match!</div>';
        }
    } else {
        $message = '<div class="alert alert-danger">Current password is incorrect!</div>';
    }
}

// Fetch the last 5 login/logout records
$stmt = $pdo->prepare("
    SELECT login_time, logout_time, ip_address, user_agent, status 
    FROM user_activity_log 
    WHERE user_id = :user_id 
    ORDER BY login_time DESC 
    LIMIT 5
");
$stmt->execute(['user_id' => $_SESSION['user_id']]);
$activity_logs = $stmt->fetchAll();

?>

<br><br>

<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="card card-bordered">
                <div class="card-inner card-inner-lg">
                    <div class="nk-block-head">
                        <div class="nk-block-head-content">
                            <center><p><div id="ad_unit" data-unit-id="6624"></div><script src="https://api.fpadserver.com/static/js/advert_renderer.js"></script></p></center>
                            <p></p>
                            <h4 class="nk-block-title">Change Password</h4>
                            <div class="nk-block-des">
                                <p>Update your current password for a more secure account.</p>
                            </div>
                        </div>
                    </div>

                    <!-- Display message if available -->
                    <?php if (!empty($message)) echo $message; ?>

                    <!-- Change Password Form -->
                    <form method="POST" action="">
                        <!-- Current Password -->
                        <div class="form-group">
                            <div class="form-label-group">
                                <label class="form-label" for="current_password">Current Password</label>
                            </div>
                            <div class="form-control-wrap">
                                <input type="password" class="form-control form-control-lg" name="current_password" id="current_password" placeholder="Enter current password" required>
                            </div>
                        </div>

                        <!-- New Password -->
                        <div class="form-group">
                            <div class="form-label-group">
                                <label class="form-label" for="new_password">New Password</label>
                            </div>
                            <div class="form-control-wrap">
                                <input type="password" class="form-control form-control-lg" name="new_password" id="new_password" placeholder="Enter new password" required>
                            </div>
                        </div>

                        <!-- Confirm New Password -->
                        <div class="form-group">
                            <div class="form-label-group">
                                <label class="form-label" for="confirm_new_password">Confirm New Password</label>
                            </div>
                            <div class="form-control-wrap">
                                <input type="password" class="form-control form-control-lg" name="confirm_new_password" id="confirm_new_password" placeholder="Confirm new password" required>
                            </div>
                        </div>

                        <!-- Submit Button -->
                        <div class="form-group">
                            <button type="submit" class="btn btn-lg btn-primary btn-block">Update Password</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- User Activity Log Table -->
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card card-bordered">
                <div class="card-inner card-inner-lg">
                    <h4 class="nk-block-title">Last 5 Login and Logout Records</h4>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th scope="col">Login Time</th>
                                <th scope="col">Logout Time</th>
                                <th scope="col">IP Address</th>
                                <th scope="col">Status</th>
                                <th scope="col">User Agent</th>
                            </tr>
                        </thead>
                        <tbody>
    <?php foreach ($activity_logs as $log): ?>
    <tr>
        <td><?php echo htmlspecialchars($log['login_time']); ?></td>
        <td><?php echo htmlspecialchars($log['logout_time'] ?: 'N/A'); ?></td>
        <td><?php echo htmlspecialchars($log['ip_address']); ?></td>
        <td>
            <?php 
                if ($log['status'] === 'logged_in') {
                    echo 'Login';
                } elseif ($log['status'] === 'logged_out') {
                    echo 'Logout';
                }
            ?>
        </td>
        <td><?php echo htmlspecialchars(substr($log['user_agent'], 0, 30)) . '...'; ?></td> <!-- Display part of user agent -->
    </tr>
    <?php endforeach; ?>
</tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<br><br>

<?php
include 'footerlogged.php';
?>
