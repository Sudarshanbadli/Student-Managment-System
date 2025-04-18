<?php
// File: index.php
require_once 'includes/functions.php';

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Debugging - Check session and role (remove in production)
// echo "<pre>"; print_r($_SESSION); echo "</pre>";

// Auto-redirect logged-in users
if (isLoggedIn()) {
    // Debugging - Check where we're redirecting (remove in production)
    // error_log("User is logged in, role: " . $_SESSION['role']);
    
    if (isAdmin()) {
        header("Location: dashboard/admin.php");
        exit();
    } elseif (isTeacher()) {
        header("Location: dashboard/teacher.php");
        exit();
    } else {
        header("Location: dashboard/student.php");
        exit();
    }
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email']);
    $password = sanitizeInput($_POST['password']);
    
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['email'] = $user['email'];
        $_SESSION['role'] = $user['role'];
        
        // Write session data and close it before redirect
        session_write_close();
        
        // Debugging - Check role before redirect (remove in production)
        // error_log("Login successful, redirecting to: " . $user['role']);
        
        // Redirect based on role
        switch ($user['role']) {
            case 'admin':
                header("Location: dashboard/admin.php");
                exit();
            case 'teacher':
                header("Location: dashboard/teacher.php");
                exit();
            default:
                header("Location: dashboard/student.php");
                exit();
        }
    } else {
        $error = 'Invalid email or password';
    }
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
    <h1 style="
          font-family: 'Arial', sans-serif;
          font-size: 4em;
          background:linear-gradient(90deg,rgb(230, 211, 211),rgb(117, 4, 95),rgb(230, 211, 211));
          -webkit-background-clip:text;
          -webkit-text-fill-color:transparent;
          text-align:center;
          padding:5px;
          border-radius:15px;
          backdrop-filter:blur(10px);
          box-shadow:0 4px 30px rgba(0,0,0,0,1);
          border:1px solid rgba(255, 255, 255, 0.2);
          ">Student Management System</h1>
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