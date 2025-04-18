<?php
// File: includes/student_actions.php
require_once 'auth.php';
require_once 'functions.php';

if (!isStudent()) {
    header('HTTP/1.0 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit();
}

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'update_profile':
        $student = getStudentByUserId($_SESSION['user_id']);
        
        $first_name = sanitizeInput($_POST['first_name']);
        $last_name = sanitizeInput($_POST['last_name']);
        $roll_number = sanitizeInput($_POST['roll_number']);
        $class = sanitizeInput($_POST['class']);
        $section = sanitizeInput($_POST['section']);
        $phone = sanitizeInput($_POST['phone'] ?? '');
        $address = sanitizeInput($_POST['address'] ?? '');
        $dob = sanitizeInput($_POST['dob'] ?? '');
        
        // Handle file upload
        $profile_image = $student['profile_image'] ?? '';
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
        
        // Update student record
        $stmt = $pdo->prepare("UPDATE students SET first_name = ?, last_name = ?, roll_number = ?, class = ?, section = ?, phone = ?, address = ?, dob = ?, profile_image = ? WHERE id = ?");
        $stmt->execute([
            $first_name,
            $last_name,
            $roll_number,
            $class,
            $section,
            $phone,
            $address,
            $dob,
            $profile_image,
            $student['id']
        ]);
        
        echo json_encode(['success' => true, 'message' => 'Profile updated successfully']);
        break;
        
    default:
        header('HTTP/1.0 400 Bad Request');
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>