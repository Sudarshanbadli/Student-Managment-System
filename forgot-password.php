<?php
// File: forgot-password.php
require_once 'includes/functions.php';

if (isLoggedIn()) {
    redirect('dashboard/home.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email']);
    $new_password = sanitizeInput($_POST['new_password']);
    $confirm_password = sanitizeInput($_POST['confirm_password']);
    
    if (empty($email)) {
        $error = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email format';
    } elseif (empty($new_password)) {
        $error = 'New password is required';
    } elseif (strlen($new_password) < 8) {
        $error = 'Password must be at least 8 characters';
    } elseif ($new_password !== $confirm_password) {
        $error = 'Passwords do not match';
    } else {
        global $pdo;
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("UPDATE users SET password = ? WHERE email = ?");
            $stmt->execute([$hashed_password, $email]);
            
            $success = 'Password updated successfully! You can now login with your new password.';
        } else {
            $error = 'Email not found';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password | Student Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="forgot-password-container">
        <div class="forgot-password-card">
            <div class="forgot-password-header">
                <h1>Reset Password</h1>
                <p>Enter your email and new password</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form action="forgot-password.php" method="POST" class="forgot-password-form">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                    <i class="fas fa-envelope icon"></i>
                </div>
                
                <div class="form-group">
                    <label for="new_password">New Password</label>
                    <input type="password" id="new_password" name="new_password" required>
                    <i class="fas fa-lock icon"></i>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm New Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                    <i class="fas fa-lock icon"></i>
                </div>
                
                <button type="submit" class="btn btn-primary btn-reset">Reset Password</button>
                
                <div class="forgot-password-footer">
                    <p>Remember your password? <a href="index.php" class="login-link">Login here</a></p>
                </div>
            </form>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>