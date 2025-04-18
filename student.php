<?php
// File: dashboard/student.php
require_once '../includes/auth.php';

if (!isStudent()) {
    redirect('home.php');
}

$pageTitle = 'Student Dashboard';
require_once 'header.php';

$student = getStudentByUserId($_SESSION['user_id']);
?>

<div class="student-dashboard">
    <div class="profile-section">
        <div class="profile-card">
            <div class="profile-image">
                <?php if (!empty($student['profile_image'])): ?>
                    <img src="<?php echo $student['profile_image']; ?>" alt="Profile Image">
                <?php else: ?>
                    <i class="fas fa-user-circle"></i>
                <?php endif; ?>
            </div>
            
            <div class="profile-info">
                <h2><?php echo htmlspecialchars($student['first_name'] . ' ' . htmlspecialchars($student['last_name'])); ?></h2>
                <p><i class="fas fa-id-card"></i> Roll Number: <?php echo htmlspecialchars($student['roll_number']); ?></p>
                <p><i class="fas fa-graduation-cap"></i> Class: <?php echo htmlspecialchars($student['class'] . ' ' . htmlspecialchars($student['section'])); ?></p>
                <?php if (!empty($student['phone'])): ?>
                    <p><i class="fas fa-phone"></i> Phone: <?php echo htmlspecialchars($student['phone']); ?></p>
                <?php endif; ?>
                <?php if (!empty($student['dob'])): ?>
                    <p><i class="fas fa-birthday-cake"></i> Date of Birth: <?php echo date('M d, Y', strtotime($student['dob'])); ?></p>
                <?php endif; ?>
            </div>
            
            <div class="profile-actions">
                <button class="btn btn-primary" id="editProfileBtn">
                    <i class="fas fa-edit"></i> Edit Profile
                </button>
            </div>
        </div>
    </div>
    
    <div class="student-stats">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-calendar-check"></i>
            </div>
            <div class="stat-info">
                <h3>Attendance</h3>
                <p>85%</p>
            </div>
        </div>
        <br>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-book"></i>
            </div>
            <div class="stat-info">
                <h3>Subjects</h3>
                <p>5</p>
            </div>
        </div>
        <br>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="stat-info">
                <h3>Performance</h3>
                <p>Good</p>
            </div>
        </div>
    </div>
    <br>
    <hr>
    <div class="student-notices">
        <h2>Recent Notices </h2>
        <hr>
        <div class="notice-card">
            <div class="notice-header">
                <h3>Exam Schedule Update</h3>
                <span class="notice-date">May 15, 2025</span>
            </div>
            <div class="notice-content">
                <p>The final exam schedule has been updated. Please check the timetable section for details.</p>
            </div>
        </div>
        <br><hr>
        <div class="notice-card">
            <div class="notice-header">
                <h3>Library Closure</h3>
                <span class="notice-date">May 10, 2025</span>
            </div>
            <div class="notice-content">
                <p>The library will be closed on May 12th for maintenance work.</p>
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
                    <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars($student['first_name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="last_name">Last Name</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars($student['last_name']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="roll_number">Roll Number</label>
                    <input type="text" id="roll_number" name="roll_number" value="<?php echo htmlspecialchars($student['roll_number']); ?>" required>
                </div>
                
                <div class="form-group">
                    <label for="class">Semester</label>
                    <select id="class" name="class" required>
                        <?php for ($i = 1; $i <= 6; $i++): ?>
                            <option value="<?php echo $i; ?>" <?php echo $student['class'] == $i ? 'selected' : ''; ?>>
                                 <?php echo $i; ?> Semester
                            </option>
                        <?php endfor; ?>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="section">Course</label>
                    <select id="section" name="section" required>
                    <option value="">Select Course</option>
                        <option value="BSC" <?php echo $student['section'] == 'BSc' ? 'selected' : ''; ?>>BSc</option>
                        <option value="BCom" <?php echo $student['section'] == 'BCom' ? 'selected' : ''; ?>>BCom</option>
                        <option value="BBA" <?php echo $student['section'] == 'BBA' ? 'selected' : ''; ?>>BBA</option>
                        <option value="BA" <?php echo $student['section'] == 'BA' ? 'selected' : ''; ?>>BA</option>
                    </select>
                    </select>
                    </select>
                </div>
                
                <div class="form-group">
                    <label for="phone">Phone</label>
                    <input type="text" id="phone" name="phone" value="<?php echo htmlspecialchars($student['phone'] ?? ''); ?>">
                </div>
                
                <div class="form-group">
                    <label for="address">Address</label>
                    <textarea id="address" name="address"><?php echo htmlspecialchars($student['address'] ?? ''); ?></textarea>
                </div>
                
                <div class="form-group">
                    <label for="dob">Date of Birth</label>
                    <input type="date" id="dob" name="dob" value="<?php echo $student['dob'] ?? ''; ?>">
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
        
        fetch('../includes/student_actions.php?action=update_profile', {
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