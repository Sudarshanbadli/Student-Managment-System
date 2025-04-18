<?php
// File: includes/media_actions.php
require_once 'auth.php';
require_once 'functions.php';

if (!isAdmin() && !isTeacher()) {
    header('HTTP/1.0 403 Forbidden');
    echo json_encode(['success' => false, 'message' => 'Access denied']);
    exit();
}

$action = $_GET['action'] ?? '';

switch ($action) {
    case 'delete_media':
        $id = sanitizeInput($_GET['id']);
        
        // First check if user has permission to delete
        $stmt = $pdo->prepare("SELECT * FROM multimedia WHERE id = ?");
        $stmt->execute([$id]);
        $media = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (!$media) {
            echo json_encode(['success' => false, 'message' => 'Media not found']);
            exit();
        }
        
        // Only admin or the uploader can delete
        if (!isAdmin() && $media['uploaded_by'] != $_SESSION['user_id']) {
            echo json_encode(['success' => false, 'message' => 'You can only delete your own media files']);
            exit();
        }
        
        // Delete file
        if (file_exists($media['file_path'])) {
            unlink($media['file_path']);
        }
        
        // Delete record
        $stmt = $pdo->prepare("DELETE FROM multimedia WHERE id = ?");
        $stmt->execute([$id]);
        
        echo json_encode(['success' => true, 'message' => 'Media deleted successfully']);
        break;
        
    default:
        header('HTTP/1.0 400 Bad Request');
        echo json_encode(['success' => false, 'message' => 'Invalid action']);
        break;
}
?>