<?php
// File: index.php
require_once 'includes/functions.php';

if (isLoggedIn()) {
    if (isAdmin()) {
        redirect('dashboard/admin.php');
    } elseif (isTeacher()) {
        redirect('dashboard/teacher.php');
    } else {
        redirect('dashboard/student.php');
    }
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email']);
    $password = sanitizeInput($_POST['password']);
    
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];

        if ($user['role'] === 'admin') {
            redirect('dashboard/admin.php');
        } elseif ($user['role'] === 'teacher') {
            redirect('dashboard/teacher.php');
        } else {
            redirect('dashboard/student.php');
        }
    } else {
        $error = 'Invalid email or password';
    }
    header("Location:/Hi/dashboard/home.php");
        exit();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login | Student Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1>Welcome Back</h1>
                <p>Please login to your account</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <form action="index.php" method="POST" class="login-form">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                    <i class="fas fa-envelope icon"></i>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    <i class="fas fa-lock icon"></i>
                </div>
                
                <div class="form-options">
                    <label class="remember-me">
                        <input type="checkbox" name="remember"> Remember me
                    </label>
                    <a href="forgot-password.php" class="forgot-password">Forgot Password?</a>
                </div>
                
                <button type="submit" class="btn btn-primary btn-login">Login</button>
                
                <div class="login-footer">
                    <p>Don't have an account? <a href="register.php" class="register-link">Register here</a></p>
                </div>
            </form>
            
            <div class="role-switcher">
                <button class="role-btn active" data-role="student">Student</button>
                <button class="role-btn" data-role="teacher">Teacher</button>
                <button class="role-btn" data-role="admin">Admin</button>
            </div>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>