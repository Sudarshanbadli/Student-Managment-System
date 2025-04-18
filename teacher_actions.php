<?php
// File: includes/teacher_actions.php
require_once 'auth.php';
require_once 'functions.php';

if (!isTeacher()) {
    header('HTTP/1.0 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit();
}

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'update_profile':
        $teacher = getTeacherByUserId($_SESSION['user_id']);
        
        $first_name = sanitizeInput($_POST['first_name']);
        $last_name = sanitizeInput($_POST['last_name']);
        $subject = sanitizeInput($_POST['subject']);
        $qualification = sanitizeInput($_POST['qualification'] ?? '');
        $phone = sanitizeInput($_POST['phone'] ?? '');
        $address = sanitizeInput($_POST['address'] ?? '');
        
        // Handle file upload
        $profile_image = $teacher['profile_image'] ?? '';
        if (!empty($_FILES['profile_image']['name'])) {
            $uploadDir = '../assets/uploads/profiles/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }
            
            $fileName = uniqid() . '_' . basename($_FILES['profile_image']['name']);
            $filePath = $uploadDir . $fileName;
            
            if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $filePath)) {
                // Delete old profile image if it exists
                if (!empty($profile_image) && file_exists($profile_image)) {
                    unlink($profile_image);
                }
                
                $profile_image = $filePath;
            }
        }
        
        // Update teacher record
        $stmt = $pdo->prepare("UPDATE teachers SET first_name = ?, last_name = ?, subject = ?, qualification = ?, phone = ?, address = ?, profile_image = ? WHERE id = ?");
        $stmt->execute([
            $first_name,
            $last_name,
            $subject,
            $qualification,
            $phone,
            $address,
            $profile_image,
            $teacher['id']
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
        break;
        
    default:
        header('HTTP/1.0 400 Bad Request');
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>