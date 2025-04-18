<?php
// File: dashboard/header.php
require_once '../includes/auth.php';

$user = getUserById($_SESSION['user_id']);
$profile = null;

if (isStudent()) {
    $profile = getStudentByUserId($_SESSION['user_id']);
} elseif (isTeacher()) {
    $profile = getTeacherByUserId($_SESSION['user_id']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle : 'Dashboard'; ?> | Student Management System</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="dashboard-container">
        <div class="sidebar">
            <div class="sidebar-header">
                <div class="profile-info">
                    <div class="profile-image">
                        <?php if ($profile && !empty($profile['profile_image'])): ?>
                            <img src="<?php echo $profile['profile_image']; ?>" alt="Profile Image">
                        <?php else: ?>
                            <i class="fas fa-user-circle"></i>
                        <?php endif; ?>
                    </div>
                    <div class="profile-details">
                        <h3><?php echo $profile ? htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']) : 'Admin'; ?></h3>
                        <p><?php echo ucfirst($_SESSION['role']); ?></p>
                    </div>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <ul>
                    <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'home.php' ? 'active' : ''; ?>">
                        <a href="home.php"><i class="fas fa-home"></i> <span>Home</span></a>
                    </li>
                    
                    <?php if (isAdmin()): ?>
                        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'admin.php' ? 'active' : ''; ?>">
                            <a href="admin.php"><i class="fas fa-user-shield"></i> <span>Admin Panel</span></a>
                        </li>
                    <?php endif; ?>
                    
                    <?php if (isTeacher()): ?>
                        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'teacher.php' ? 'active' : ''; ?>">
                            <a href="teacher.php"><i class="fas fa-chalkboard-teacher"></i> <span>Teacher Dashboard</span></a>
                        </li>
                    <?php endif; ?>
                    
                    <?php if (isStudent()): ?>
                        <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'student.php' ? 'active' : ''; ?>">
                            <a href="student.php"><i class="fas fa-user-graduate"></i> <span>Student Dashboard</span></a>
                        </li>
                    <?php endif; ?>
                    
                    <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'attendance.php' ? 'active' : ''; ?>">
                        <a href="attendance.php"><i class="fas fa-calendar-check"></i> <span>Attendance</span></a>
                    </li>
                    
                    <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'timetable.php' ? 'active' : ''; ?>">
                        <a href="timetable.php"><i class="fas fa-calendar-alt"></i> <span>Timetable</span></a>
                    </li>
                    
                    <li class="<?php echo basename($_SERVER['PHP_SELF']) == 'multimedia.php' ? 'active' : ''; ?>">
                        <a href="multimedia.php"><i class="fas fa-images"></i> <span>Multimedia</span></a>
                    </li>
                    
                    <li>
                        <a href="logout.php"><i class="fas fa-sign-out-alt"></i> <span>Logout</span></a>
                    </li>
                </ul>
            </nav>
        </div>
        
        <div class="main-content">
            <header class="topbar">
                <div class="topbar-left">
                    <button class="sidebar-toggle">
                        <i class="fas fa-bars"></i>
                    </button>
                    <h1><?php echo isset($pageTitle) ? $pageTitle : 'Dashboard'; ?></h1>
                </div>
                
                <div class="topbar-right">
                    <div class="user-menu">
                        <div class="user-image">
                            <?php if ($profile && !empty($profile['profile_image'])): ?>
                                <img src="<?php echo $profile['profile_image']; ?>" alt="Profile Image">
                            <?php else: ?>
                                <i class="fas fa-user-circle"></i>
                            <?php endif; ?>
                        </div>
                        <span><?php echo $profile ? htmlspecialchars($profile['first_name']) : 'Admin'; ?></span>
                        <i class="fas fa-chevron-down"></i>
                        
                        <div class="dropdown-menu">
                            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
                        </div>
                    </div>
                </div>
            </header>
            
            <main class="content">