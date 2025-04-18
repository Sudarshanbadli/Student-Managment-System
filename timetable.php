<?php
// File: dashboard/timetable.php
require_once '../includes/auth.php';

$pageTitle = 'Timetable Management';
require_once 'header.php';

global $pdo;

// Get classes for dropdown
$classes = [];
$stmt = $pdo->query("SELECT DISTINCT class FROM students ORDER BY class");
$classes = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Get sections for dropdown
$sections = [];
$stmt = $pdo->query("SELECT DISTINCT section FROM students ORDER BY section");
$sections = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Handle timetable upload
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isAdmin()) {
    $class = sanitizeInput($_POST['class']);
    $section = sanitizeInput($_POST['section']);
    $file = $_FILES['timetable_file'];
    
    try {
        // Validate file
        $allowedTypes = ['image/jpeg', 'image/png', 'application/pdf'];
        if (!in_array($file['type'], $allowedTypes)) {
            throw new Exception('Only JPEG, PNG, and PDF files are allowed.');
        }
        
        if ($file['size'] > 5 * 1024 * 1024) { // 5MB
            throw new Exception('File size must be less than 5MB.');
        }
        
        // Upload file
        $uploadDir = '../assets/uploads/timetables/';
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0777, true);
        }
        
        $fileName = uniqid() . '_' . basename($file['name']);
        $filePath = $uploadDir . $fileName;
        
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            // Check if timetable already exists for this class and section
            $stmt = $pdo->prepare("SELECT id FROM timetable WHERE class = ? AND section = ?");
            $stmt->execute([$class, $section]);
            
            if ($stmt->fetch()) {
                // Update existing record
                $stmt = $pdo->prepare("UPDATE timetable SET file_path = ?, uploaded_by = ? WHERE class = ? AND section = ?");
                $stmt->execute([$filePath, $_SESSION['user_id'], $class, $section]);
            } else {
                // Insert new record
                $stmt = $pdo->prepare("INSERT INTO timetable (class, section, file_path, uploaded_by) VALUES (?, ?, ?, ?)");
                $stmt->execute([$class, $section, $filePath, $_SESSION['user_id']]);
            }
            
            $_SESSION['success'] = 'Timetable uploaded successfully!';
        } else {
            throw new Exception('Error uploading file.');
        }
    } catch (Exception $e) {
        $_SESSION['error'] = $e->getMessage();
    }
    
    redirect('timetable.php');
}

// Get all timetables
$timetables = [];
if (isAdmin()) {
    $stmt = $pdo->query("SELECT t.*, u.email as uploaded_by_email FROM timetable t JOIN users u ON t.uploaded_by = u.id ORDER BY t.class, t.section");
    $timetables = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif (isTeacher()) {
    // Teachers can see all timetables
    $stmt = $pdo->query("SELECT t.*, u.email as uploaded_by_email FROM timetable t JOIN users u ON t.uploaded_by = u.id ORDER BY t.class, t.section");
    $timetables = $stmt->fetchAll(PDO::FETCH_ASSOC);
} elseif (isStudent()) {
    // Students can only see their class timetable
    $student = getStudentByUserId($_SESSION['user_id']);
    $stmt = $pdo->prepare("SELECT t.*, u.email as uploaded_by_email FROM timetable t JOIN users u ON t.uploaded_by = u.id WHERE t.class = ? AND t.section = ?");
    $stmt->execute([$student['class'], $student['section']]);
    $timetables = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="timetable-management">
    <h2>Timetable</h2>
    
    <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
    <?php endif; ?>
    
    <?php if (isAdmin()): ?>
        <div class="upload-timetable">
            <h3>Upload Timetable</h3>
            
            <form method="POST" action="timetable.php" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="class">Class</label>
                    <select id="class" name="class" required>
                        <option value="">Select Class</option>
                        <?php foreach ($classes as $class): ?>
                            <option value="<?php echo $class; ?>">Class <?php echo $class; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="section">Section</label>
                    <select id="section" name="section" required>
                        <option value="">Select Section</option>
                        <?php foreach ($sections as $section): ?>
                            <option value="<?php echo $section; ?>">Section <?php echo $section; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="timetable_file">Timetable File</label>
                    <input type="file" id="timetable_file" name="timetable_file" accept="image/jpeg,image/png,application/pdf" required>
                    <small>Allowed formats: JPEG, PNG, PDF (Max 5MB)</small>
                </div>
                
                <button type="submit" class="btn btn-primary">Upload Timetable</button>
            </form>
        </div>
    <?php endif; ?>
    
    <div class="timetable-list">
        <h3><?php echo isAdmin() ? 'All Timetables' : (isTeacher() ? 'Class Timetables' : 'My Class Timetable'); ?></h3>
        
        <?php if (!empty($timetables)): ?>
            <div class="timetable-grid">
                <?php foreach ($timetables as $timetable): ?>
                    <div class="timetable-card">
                        <div class="timetable-header">
                            <h4>Class <?php echo htmlspecialchars($timetable['class'] . ' - Section ' . htmlspecialchars($timetable['section'])); ?></h4>
                            <small>Uploaded by: <?php echo htmlspecialchars($timetable['uploaded_by_email']); ?></small>
                            <small>Uploaded on: <?php echo date('M d, Y', strtotime($timetable['uploaded_at'])); ?></small>
                        </div>
                        
                        <div class="timetable-preview">
                            <?php if (strpos($timetable['file_path'], '.pdf') !== false): ?>
                                <embed src="<?php echo $timetable['file_path']; ?>" type="application/pdf" width="100%" height="300px">
                            <?php else: ?>
                                <img src="<?php echo $timetable['file_path']; ?>" alt="Timetable Image" class="timetable-image">
                            <?php endif; ?>
                        </div>
                        
                        <div class="timetable-actions">
                            <a href="<?php echo $timetable['file_path']; ?>" class="btn btn-sm btn-primary" download>
                                <i class="fas fa-download"></i> Download
                            </a>
                            
                            <?php if (isAdmin()): ?>
                                <button class="btn btn-sm btn-danger delete-timetable" data-id="<?php echo $timetable['id']; ?>">
                                    <i class="fas fa-trash"></i> Delete
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">No timetables found.</div>
        <?php endif; ?>
    </div>
</div>

<!-- Delete Timetable Modal -->
<div class="modal" id="deleteTimetableModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Confirm Delete</h3>
            <button class="close-modal">&times;</button>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete this timetable?</p>
            <input type="hidden" id="delete_timetable_id">
            
            <div class="form-actions">
                <button type="button" class="btn btn-secondary close-modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmTimetableDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Delete Timetable Buttons
    const deleteButtons = document.querySelectorAll('.delete-timetable');
    const deleteModal = document.getElementById('deleteTimetableModal');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const timetableId = this.getAttribute('data-id');
            document.getElementById('delete_timetable_id').value = timetableId;
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
    
    // Confirm Timetable Delete
    document.getElementById('confirmTimetableDelete').addEventListener('click', function() {
        const timetableId = document.getElementById('delete_timetable_id').value;
        
        fetch('../includes/admin_actions.php?action=delete_timetable&id=' + timetableId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Timetable deleted successfully!');
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
    
    // Preview timetable images in lightbox
    const timetableImages = document.querySelectorAll('.timetable-image');
    timetableImages.forEach(image => {
        image.addEventListener('click', function() {
            const lightbox = document.createElement('div');
            lightbox.className = 'lightbox';
            lightbox.innerHTML = `
                <div class="lightbox-content">
                    <img src="${this.src}" alt="Timetable">
                    <button class="close-lightbox">&times;</button>
                </div>
            `;
            
            document.body.appendChild(lightbox);
            
            lightbox.querySelector('.close-lightbox').addEventListener('click', function() {
                lightbox.remove();
            });
            
            lightbox.addEventListener('click', function(e) {
                if (e.target === lightbox) {
                    lightbox.remove();
                }
            });
        });
    });
});
</script>

<?php
require_once 'footer.php';
?>