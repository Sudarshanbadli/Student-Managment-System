<?php
// File: dashboard/teacher.php
require_once '../includes/auth.php';

if (!isTeacher()) {
    redirect('home.php');
}

$pageTitle = 'Teacher Dashboard';
require_once 'header.php';

$teacher = getTeacherByUserId($_SESSION['user_id']);
?>

<div class="teacher-dashboard">
    <div class="profile-section">
        <div class="profile-card">
            <div class="profile-image">
                <?php if (!empty($teacher['profile_image'])): ?>
                    <img src="<?php echo $teacher['profile_image']; ?>" alt="Profile Image">
                <?php else: ?>
                    <i class="fas fa-user-circle"></i>
                <?php endif; ?>
            </div>
            
            <div class="profile-info">
                <h2><?php echo htmlspecialchars($teacher['first_name'] . ' ' . htmlspecialchars($teacher['last_name'])); ?></h2>
                <p><i class="fas fa-book"></i> Subject: <?php echo htmlspecialchars($teacher['subject']); ?></p>
                <?php if (!empty($teacher['qualification'])): ?>
                    <p><i class="fas fa-graduation-cap"></i> Qualification: <?php echo htmlspecialchars($teacher['qualification']); ?></p>
                <?php endif; ?>
                <?php if (!empty($teacher['phone'])): ?>
                    <p><i class="fas fa-phone"></i> Phone: <?php echo htmlspecialchars($teacher['phone']); ?></p>
                <?php endif; ?>
            </div>
            
            <div class="profile-actions">
                <button class="btn btn-primary" id="editProfileBtn">
                    <i class="fas fa-edit"></i> Edit Profile
                </button>
            </div>
        </div>
    </div>
    
    <div class="teacher-stats">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-users"></i>
            </div>
            <div class="stat-info">
                <h3>Students</h3>
                <p>45</p>
            </div>
        </div>
        <br>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-book"></i>
            </div>
            <div class="stat-info">
                <h3>Subjects</h3>
                <p>3</p>
            </div>
        </div>
        <br>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-chalkboard"></i>
            </div>
            <div class="stat-info">
                <h3>Classes Today</h3>
                <p>3</p>
            </div>
        </div>
    </div>
    <br><hr>
    <div class="teacher-classes">
        <h2>Your Classes</h2><hr>
        
        <div class="class-card">
            <h3>Web Technology - Class BSc 6sem</h3>
            <p>Monday, Wednesday, Friday - 10:00 AM to 11:00 AM</p>
            <div class="class-actions">
                <button class="btn btn-sm btn-primary">View Students</button>
                <button class="btn btn-sm btn-secondary">Mark Attendance</button>
            </div>
        </div>
        <br><hr>
        <div class="class-card">
            <h3> DBMS- Class BSc 4sem</h3>
            <p>Tuesday, Thursday - 11:00 AM to 12:00 PM</p>
            <div class="class-actions">
                <button class="btn btn-sm btn-primary">View Students</button>
                <button class="btn btn-sm btn-secondary">Mark Attendance</button>
            </div>
            <br><hr>
            <div class="class-card">
            <h3> C programming- Class BSc 2sem</h3>
            <p>Tuesday, Thursday,saturday - 12:00 PM to 1:00 PM</p>
            <div class="class-actions">
                <button class="btn btn-sm btn-primary">View Students</button>
                <button class="btn btn-sm btn-secondary">Mark Attendance</button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Profile Modal -->
<div class="modal" id="editProfileModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit Profile</h3>
            <button class="close-modal">&times;</button>
        </div>
        <div class="modal-body">
            <form id="editProfileForm" enctype="multipart/form-data">
                <div class="form-group">
                    <label for="first_name">First Name</label>
                    <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($teacher['first_name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($teacher['last_name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" value="<?php echo htmlspecialchars($teacher['subject']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="qualification">Qualification</label>
                    <input type="text" id="qualification" name="qualification" value="<?php echo htmlspecialchars($teacher['qualification'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($teacher['phone'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address"><?php echo htmlspecialchars($teacher['address'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="profile_image">Profile Image</label>
                    <input type="file" id="profile_image" name="profile_image">
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary close-modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save Changes</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Edit Profile Button
    const editProfileBtn = document.getElementById('editProfileBtn');
    const editProfileModal = document.getElementById('editProfileModal');
    
    editProfileBtn.addEventListener('click', function() {
        editProfileModal.style.display = 'flex';
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
    
    // Edit Profile Form Submission
    document.getElementById('editProfileForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('../includes/teacher_actions.php?action=update_profile', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Profile updated successfully!');
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