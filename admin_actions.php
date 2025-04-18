<?php
// File: includes/admin_actions.php
require_once 'auth.php';
require_once 'functions.php';

if (!isAdmin()) {
    header('HTTP/1.0 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit();
}

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'add_user':
        $email = sanitizeInput($_POST['email']);
        $password = sanitizeInput($_POST['password']);
        $role = sanitizeInput($_POST['role']);
        
        // Check if email exists
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Email already exists']);
            exit();
        }
        
        // Insert new user
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
        $stmt = $pdo->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$email, $hashed_password, $role]);
        
        echo json_encode(['success' => true, 'message' => 'User added successfully']);
        break;
        
    case 'edit_user':
        $id = sanitizeInput($_POST['id']);
        $email = sanitizeInput($_POST['email']);
        $role = sanitizeInput($_POST['role']);
        
        // Check if email exists for another user
        $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->execute([$email, $id]);
        
        if ($stmt->fetch()) {
            echo json_encode(['success' => false, 'message' => 'Email already exists for another user']);
            exit();
        }
        
        // Update user
        $stmt = $pdo->prepare("UPDATE users SET email = ?, role = ? WHERE id = ?");
        $stmt->execute([$email, $role, $id]);
        
        echo json_encode(['success' => true, 'message' => 'User updated successfully']);
        break;
        
    case 'delete_user':
        $id = sanitizeInput($_GET['id']);
        
        // Delete user (cascade delete will handle related records)
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$id]);
        
        echo json_encode(['success' => true, 'message' => 'User deleted successfully']);
        break;
        
    case 'delete_timetable':
        $id = sanitizeInput($_GET['id']);
        
        // First get file path to delete the file
        $stmt = $pdo->prepare("SELECT file_path FROM timetable WHERE id = ?");
        $stmt->execute([$id]);
        $timetable = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if ($timetable && file_exists($timetable['file_path'])) {
            unlink($timetable['file_path']);
        }
        
        // Delete timetable record
        $stmt = $pdo->prepare("DELETE FROM timetable WHERE id = ?");
        $stmt->execute([$id]);
        
        echo json_encode(['success' => true, 'message' => 'Timetable deleted successfully']);
        break;
        
    default:
        header('HTTP/1.0 400 Bad Request');
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>