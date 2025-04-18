<?php
// File: register.php
require_once 'includes/functions.php';

if (isLoggedIn()) {
    redirect('dashboard/home.php');
}

$errors = [];
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitizeInput($_POST['email']);
    $password = sanitizeInput($_POST['password']);
    $confirm_password = sanitizeInput($_POST['confirm_password']);
    $role = sanitizeInput($_POST['role']);
    $first_name = sanitizeInput($_POST['first_name']);
    $last_name = sanitizeInput($_POST['last_name']);
    
    // Validate inputs
    if (empty($email)) {
        $errors['email'] = 'Email is required';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Invalid email format';
    }
    
    if (empty($password)) {
        $errors['password'] = 'Password is required';
    } elseif (strlen($password) < 8) {
        $errors['password'] = 'Password must be at least 8 characters';
    }
    
    if ($password !== $confirm_password) {
        $errors['confirm_password'] = 'Passwords do not match';
    }
    
    if (empty($role)) {
        $errors['role'] = 'Role is required';
    }
    
    if (empty($first_name)) {
        $errors['first_name'] = 'First name is required';
    }
    
    if (empty($last_name)) {
        $errors['last_name'] = 'Last name is required';
    }
    
    // Check if email exists
    global $pdo;
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->execute([$email]);
    
    if ($stmt->fetch()) {
        $errors['email'] = 'Email already exists';
    }
    
    if (empty($errors)) {
        // Insert into users table
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$email, $hashed_password, $role]);
        $user_id = $pdo->lastInsertId();
        
        // Insert into respective role table
        if ($role === 'student') {
            $roll_number = sanitizeInput($_POST['roll_number']);
            $class = sanitizeInput($_POST['class']);
            $section = sanitizeInput($_POST['section']);
            
            $stmt = $pdo->prepare("INSERT INTO students (user_id, first_name, last_name, roll_number, class, section) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->execute([$user_id, $first_name, $last_name, $roll_number, $class, $section]);
        } elseif ($role === 'teacher') {
            $subject = sanitizeInput($_POST['subject']);
            
            $stmt = $pdo->prepare("INSERT INTO teachers (user_id, first_name, last_name, subject) VALUES (?, ?, ?, ?)");
            $stmt->execute([$user_id, $first_name, $last_name, $subject]);
        }
        
        $success = 'Registration successful! You can now login.';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register | Student Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="register-container">
        <div class="register-card">
            <div class="register-header">
                <h1>Create Account</h1>
                <p>Fill in your details to register</p>
            </div>
            
            <?php if ($success): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <form action="register.php" method="POST" class="register-form" id="registerForm">
                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" required>
                    <i class="fas fa-envelope icon"></i>
                    <?php if (isset($errors['email'])): ?>
                        <span class="error"><?php echo $errors['email']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="password">Password</label>
                    <input type="password" id="password" name="password" required>
                    <i class="fas fa-lock icon"></i>
                    <?php if (isset($errors['password'])): ?>
                        <span class="error"><?php echo $errors['password']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="confirm_password">Confirm Password</label>
                    <input type="password" id="confirm_password" name="confirm_password" required>
                    <i class="fas fa-lock icon"></i>
                    <?php if (isset($errors['confirm_password'])): ?>
                        <span class="error"><?php echo $errors['confirm_password']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="role">Role</label>
                    <select id="role" name="role" required>
                        <option value="">Select Role</option>
                        <option value="student">Student</option>
                        <option value="teacher">Teacher</option>
                    </select>
                    <?php if (isset($errors['role'])): ?>
                        <span class="error"><?php echo $errors['role']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" required>
                    <i class="fas fa-user icon"></i>
                    <?php if (isset($errors['first_name'])): ?>
                        <span class="error"><?php echo $errors['first_name']; ?></span>
                    <?php endif; ?>
                </div>
                
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" required>
                    <i class="fas fa-user icon"></i>
                    <?php if (isset($errors['last_name'])): ?>
                        <span class="error"><?php echo $errors['last_name']; ?></span>
                    <?php endif; ?>
                </div>
                
                <!-- Student specific fields -->
                <div class="student-fields">
                    <div class="form-group">
                        <label for="roll_number">Roll Number</label>
                        <input type="text" id="roll_number" name="roll_number">
                        <i class="fas fa-id-card icon"></i>
                    </div>
                    
                    <div class="form-group">
                        <label for="class">Class</label>
                        <select id="class" name="class">
                            <option value="">Select Class</option>
                            <option value="1">Class 1</option>
                            <option value="2">Class 2</option>
                            <option value="3">Class 3</option>
                            <option value="4">Class 4</option>
                            <option value="5">Class 5</option>
                            <option value="6">Class 6</option>
                            <option value="7">Class 7</option>
                            <option value="8">Class 8</option>
                            <option value="9">Class 9</option>
                            <option value="10">Class 10</option>
                            <option value="11">Class 11</option>
                            <option value="12">Class 12</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="section">Section</label>
                        <select id="section" name="section">
                            <option value="">Select Section</option>
                            <option value="A">A</option>
                            <option value="B">B</option>
                            <option value="C">C</option>
                            <option value="D">D</option>
                        </select>
                    </div>
                </div>
                
                <!-- Teacher specific fields -->
                <div class="teacher-fields" style="display: none;">
                    <div class="form-group">
                        <label for="subject">Subject</label>
                        <input type="text" id="subject" name="subject">
                        <i class="fas fa-book icon"></i>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary btn-register">Register</button>
                
                <div class="register-footer">
                    <p>Already have an account? <a href="index.php" class="login-link">Login here</a></p>
                </div>
            </form>
        </div>
    </div>

    <script src="assets/js/script.js"></script>
</body>
</html>