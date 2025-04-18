<?php
// File: dashboard/home.php
$pageTitle = 'Home';
require_once 'header.php';
?>

<div class="welcome-section">
    <div class="welcome-content" >
       <h2>Welcome to Student Management System</h2>
        <p>Manage your academic activities with ease and efficiency</p>
        
        <?php if (isAdmin()): ?>
            <div class="admin-stats">
                <div class="stat-card">
                    <i class="fas fa-users"></i>
                    <h3>Students</h3>
                    <p><?php echo count(getAllStudents()); ?></p>
                </div>
                
                <div class="stat-card">
                    <i class="fas fa-chalkboard-teacher"></i>
                    <h3>Teachers</h3>
                    <p><?php echo count(getAllTeachers()); ?></p>
                </div>
                
                <div class="stat-card">
                    <i class="fas fa-calendar-check"></i>
                    <h3>Attendance Today</h3>
                    <p>85%</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="welcome-image">
        <img src="https://iili.io/300VT8X.png" alt="Welcome Image">
    </div>
</div>

<div class="quick-actions">
    <hr><h3>Quick Actions</h3><hr><br>
    
    <div class="action-cards">
        <a href="attendance.php" class="action-card">
            <i class="fas fa-calendar-check"></i>
            <span>Mark Attendance</span>
        </a>
        
        <a href="timetable.php" class="action-card">
            <i class="fas fa-calendar-alt"></i>
            <span>View Timetable</span>
        </a>
        
        <a href="multimedia.php" class="action-card">
            <i class="fas fa-images"></i>
            <span>Multimedia</span>
        </a>
        
        <?php if (isAdmin()): ?>
            <a href="admin.php" class="action-card">
                <i class="fas fa-cog"></i>
                <span>Admin Panel</span>
            </a>
        <?php endif; ?>
    </div>
</div>

<?php
require_once 'footer.php';
?>