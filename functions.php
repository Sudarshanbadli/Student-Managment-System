<?php
// File: includes/functions.php
require_once 'db.php';

function sanitizeInput($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

function redirect($url) {
    header("Location: $url");
    exit();
}

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'admin';
}

function isTeacher() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'teacher';
}

function isStudent() {
    return isset($_SESSION['role']) && $_SESSION['role'] === 'student';
}

function getUserById($id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getStudentByUserId($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM students WHERE user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getTeacherByUserId($user_id) {
    global $pdo;
    $stmt = $pdo->prepare("SELECT * FROM teachers WHERE user_id = ?");
    $stmt->execute([$user_id]);
    return $stmt->fetch(PDO::FETCH_ASSOC);
}

function getAllStudents() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM students");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function getAllTeachers() {
    global $pdo;
    $stmt = $pdo->query("SELECT * FROM teachers");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

function uploadFile($file, $targetDir) {
    $fileName = basename($file['name']);
    $targetPath = $targetDir . uniqid() . '_' . $fileName;
    
    if (move_uploaded_file($file['tmp_name'], $targetPath)) {
        return $targetPath;
    }
    return false;
}
?>