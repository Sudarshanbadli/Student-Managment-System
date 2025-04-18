<?php
// File: dashboard/multimedia.php
require_once '../includes/auth.php';

$pageTitle = 'Multimedia Gallery';
require_once 'header.php';

global $pdo;

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isAdmin() || isTeacher())) {
    $title = sanitizeInput($_POST['title']);
    $description = sanitizeInput($_POST['description']);
    $file = $_FILES['media_file'];
    
    try {
        // Validate file
        $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4', 'video/webm'];
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception('Only images (JPEG, PNG, GIF) and videos (MP4, WebM) are allowed.');
        }
        
        if ($file['size'] > 10 * 1024 * 1024) { // 10MB
            throw new Exception('File size must be less than 10MB.');
        }
        
        // Determine file type
        $fileType = strpos($file['type'], 'image') !== false ? 'image' : 'video';
        
        // Upload file
        $uploadDir = $fileType === 'image' ? '../assets/uploads/images/' : '../assets/uploads/videos/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = uniqid() . '_' . basename($file['name']);
        $filePath = $uploadDir . $fileName;
        
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            // Insert into database
            $stmt = $pdo->prepare("INSERT INTO multimedia (title, description, file_path, file_type, uploaded_by) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$title, $description, $filePath, $fileType, $_SESSION['user_id']]);
            
            $_SESSION['success'] = 'File uploaded successfully!';
        } else {
            throw new Exception('Error uploading file.');
        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
    
    redirect('multimedia.php');
}

// Get all media files
$mediaFiles = [];
$stmt = $pdo->query("SELECT m.*, u.email as uploaded_by_email FROM multimedia m JOIN users u ON m.uploaded_by = u.id ORDER BY m.uploaded_at DESC");
$mediaFiles = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="multimedia-gallery">
    <h2>Multimedia Gallery</h2>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    
    <?php if (isAdmin() || isTeacher()): ?>
        <div class="upload-media">
            <h3>Upload Media</h3>
            
            <form method="POST" action="multimedia.php" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="title">Title</label>
                    <input type="text" id="title" name="title" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description</label>
                    <textarea id="description" name="description"></textarea>
                </div>
                
                <div class="form-group">
                    <label for="media_file">Media File</label>
                    <input type="file" id="media_file" name="media_file" accept="image/*,video/*" required>
                    <small>Allowed formats: JPEG, PNG, GIF, MP4, WebM (Max 10MB)</small>
                </div>
                
                <button type="submit" class="btn btn-primary">Upload Media</button>
            </form>
        </div>
    <?php endif; ?>
    
    <div class="media-grid">
        <?php if (!empty($mediaFiles)): ?>
            <?php foreach ($mediaFiles as $media): ?>
                <div class="media-card">
                    <div class="media-header">
                        <h4><?php echo htmlspecialchars($media['title']); ?></h4>
                        <small>Uploaded by: <?php echo htmlspecialchars($media['uploaded_by_email']); ?></small>
                        <small>Uploaded on: <?php echo date('M d, Y', strtotime($media['uploaded_at'])); ?></small>
                    </div>
                    
                    <div class="media-content">
                        <?php if ($media['file_type'] === 'image'): ?>
                            <img src="<?php echo $media['file_path']; ?>" alt="<?php echo htmlspecialchars($media['title']); ?>" class="media-preview">
                        <?php else: ?>
                            <video controls class="media-preview">
                                <source src="<?php echo $media['file_path']; ?>" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        <?php endif; ?>
                    </div>
                    
                    <?php if (!empty($media['description'])): ?>
                        <div class="media-description">
                            <p><?php echo htmlspecialchars($media['description']); ?></p>
                        </div>
                    <?php endif; ?>
                    
                    <div class="media-actions">
                        <?php if ($media['file_type'] === 'image'): ?>
                            <button class="btn btn-sm btn-primary view-media" data-src="<?php echo $media['file_path']; ?>">
                                <i class="fas fa-expand"></i> View
                            </button>
                        <?php endif; ?>
                        
                        <a href="<?php echo $media['file_path']; ?>" class="btn btn-sm btn-secondary" download>
                            <i class="fas fa-download"></i> Download
                        </a>
                        
                        <?php if (isAdmin() || (isTeacher() && $media['uploaded_by'] == $_SESSION['user_id'])): ?>
                            <button class="btn btn-sm btn-danger delete-media" data-id="<?php echo $media['id']; ?>">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="alert alert-info">No media files found.</div>
        <?php endif; ?>
    </div>
</div>

<!-- Delete Media Modal -->
<div class="modal" id="deleteMediaModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Confirm Delete</h3>
            <button class="close-modal">&times;</button>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete this media file?</p>
            <input type="hidden" id="delete_media_id">
            
            <div class="form-actions">
                <button type="button" class="btn btn-secondary close-modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmMediaDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<!-- Image Lightbox -->
<div class="lightbox" id="imageLightbox" style="display: none;">
    <div class="lightbox-content">
        <img src="" alt="Media Preview" id="lightboxImage">
        <button class="close-lightbox">&times;</button>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // View Image Buttons
    const viewButtons = document.querySelectorAll('.view-media');
    const lightbox = document.getElementById('imageLightbox');
    const lightboxImage = document.getElementById('lightboxImage');
    
    viewButtons.forEach(button => {
        button.addEventListener('click', function() {
            const imageSrc = this.getAttribute('data-src');
            lightboxImage.src = imageSrc;
            lightbox.style.display = 'flex';
        });
    });
    
    // Close Lightbox
    const closeLightbox = document.querySelector('.close-lightbox');
    closeLightbox.addEventListener('click', function() {
        lightbox.style.display = 'none';
    });
    
    lightbox.addEventListener('click', function(e) {
        if (e.target === lightbox) {
            lightbox.style.display = 'none';
        }
    });
    
    // Delete Media Buttons
    const deleteButtons = document.querySelectorAll('.delete-media');
    const deleteModal = document.getElementById('deleteMediaModal');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const mediaId = this.getAttribute('data-id');
            document.getElementById('delete_media_id').value = mediaId;
            deleteModal.style.display = 'flex';
        });
    });
    
    // Close Modal
    const closeButtons = document.querySelectorAll('.close-modal');
    
    closeButtons.forEach(button => {
        button.addEventListener('click', function() {
            document.querySelectorAll('.modal').forEach(modal => {
                modal.style.display = 'none';
            });
        });
    });
    
    // Close modal when clicking outside
    window.addEventListener('click', function(event) {
        if (event.target.classList.contains('modal')) {
            event.target.style.display = 'none';
        }
    });
    
    // Confirm Media Delete
    document.getElementById('confirmMediaDelete').addEventListener('click', function() {
        const mediaId = document.getElementById('delete_media_id').value;
        
        fetch('../includes/media_actions.php?action=delete_media&id=' + mediaId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Media file deleted successfully!');
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
});
</script>

<?php
require_once 'footer.php';
?>