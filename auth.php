<?php
// File: includes/auth.php
session_start();
require_once 'functions.php';

if (!isLoggedIn() && basename($_SERVER['PHP_SELF']) != 'index.php' && basename($_SERVER['PHP_SELF']) != 'register.php' && basename($_SERVER['PHP_SELF']) != 'forgot-password.php') {
    redirect('index.php');
}

if (isset($_SESSION['role'])) {
    $currentPage = basename($_SERVER['PHP_SELF']);
    
    // Admin can access all pages except student and teacher specific pages
    if (isAdmin() && (strpos($currentPage, 'student.php') !== false || strpos($currentPage, 'teacher.php') !== false)) {
        redirect('admin.php');
    }
    
    // Teacher can't access admin or student pages
    if (isTeacher() && (strpos($currentPage, 'admin.php') !== false || strpos($currentPage, 'student.php') !== false)) {
        redirect('teacher.php');
    }
    
    // Student can't access admin or teacher pages
    if (isStudent() && (strpos($currentPage, 'admin.php') !== false || strpos($currentPage, 'teacher.php') !== false)) {
        redirect('student.php');
    }
}
?>