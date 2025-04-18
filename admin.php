<?php
// File: dashboard/admin.php
require_once '../includes/auth.php';

if (!isAdmin()) {
    redirect('home.php');
}

$pageTitle = 'Admin Dashboard';
require_once 'header.php';

// Get all users
global $pdo;
$users = $pdo->query("SELECT * FROM users")->fetchAll(PDO::FETCH_ASSOC);
?>

<div class="admin-dashboard">
    <div class="admin-section">
       <hr> <h2>User Management</h2><hr>
        <br>
        <div class="admin-actions">
            <button class="btn btn-primary" id="addUserBtn">
                <i class="fas fa-plus"></i> Add User
            </button>
        </div>
        <br>
        <div class="table-responsive">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Created At</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?php echo $user['id']; ?></td>
                            <td><?php echo htmlspecialchars($user['email']); ?></td>
                            <td><?php echo ucfirst($user['role']); ?></td>
                            <td><?php echo date('M d, Y', strtotime($user['created_at'])); ?></td>
                            <td>
                                <button class="btn btn-sm btn-edit" data-id="<?php echo $user['id']; ?>">
                                    <i class="fas fa-edit"></i>
                                </button>
                                <button class="btn btn-sm btn-delete" data-id="<?php echo $user['id']; ?>">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <hr>
    <div class="admin-section">
        <h2>System Statistics</h2>
        <hr><br>
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-users"></i>
                </div>
                <div class="stat-info">
                    <h3>Total Students</h3>
                    <p><?php echo count(getAllStudents()); ?></p>
                </div>
            </div>
            <br>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-chalkboard-teacher"></i>
                </div>
                <div class="stat-info">
                    <h3>Total Teachers</h3>
                    <p><?php echo count(getAllTeachers()); ?></p>
                </div>
            </div>
            <br>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div class="stat-info">
                    <h3>Today's Attendance</h3>
                    <p>85%</p>
                </div>
            </div>
            <br>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-file-upload"></i>
                </div>
                <div class="stat-info">
                    <h3>Recent Uploads</h3>
                    <p>12</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add User Modal -->
<div class="modal" id="addUserModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Add New User</h3>
            <button class="close-modal">&times;</button>
        </div>
        <div class="modal-body">
            <form id="addUserForm">
                <div class="form-group">
                    <label for="modal_email">Email</label>
                    <input type="email" id="modal_email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="modal_password">Password</label>
                    <input type="password" id="modal_password" name="password" required>
                </div>
                
                <div class="form-group">
                    <label for="modal_role">Role</label>
                    <select id="modal_role" name="role" required>
                        <option value="">Select Role</option>
                        <option value="admin">Admin</option>
                        <option value="teacher">Teacher</option>
                        <option value="student">Student</option>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary close-modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit User Modal -->
<div class="modal" id="editUserModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Edit User</h3>
            <button class="close-modal">&times;</button>
        </div>
        <div class="modal-body">
            <form id="editUserForm">
                <input type="hidden" id="edit_id" name="id">
                
                <div class="form-group">
                    <label for="edit_email">Email</label>
                    <input type="email" id="edit_email" name="email" required>
                </div>
                
                <div class="form-group">
                    <label for="edit_role">Role</label>
                    <select id="edit_role" name="role" required>
                        <option value="">Select Role</option>
                        <option value="admin">Admin</option>
                        <option value="teacher">Teacher</option>
                        <option value="student">Student</option>
                    </select>
                </div>
                
                <div class="form-actions">
                    <button type="button" class="btn btn-secondary close-modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal" id="deleteModal">
    <div class="modal-content">
        <div class="modal-header">
            <h3>Confirm Delete</h3>
            <button class="close-modal">&times;</button>
        </div>
        <div class="modal-body">
            <p>Are you sure you want to delete this user?</p>
            <input type="hidden" id="delete_id">
            
            <div class="form-actions">
                <button type="button" class="btn btn-secondary close-modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmDelete">Delete</button>
            </div>
        </div>
    </div>
</div>

<script>
// JavaScript for admin dashboard functionality
document.addEventListener('DOMContentLoaded', function() {
    // Add User Modal
    const addUserBtn = document.getElementById('addUserBtn');
    const addUserModal = document.getElementById('addUserModal');
    
    addUserBtn.addEventListener('click', function() {
        addUserModal.style.display = 'flex';
    });
    
    // Edit User Buttons
    const editButtons = document.querySelectorAll('.btn-edit');
    const editUserModal = document.getElementById('editUserModal');
    
    editButtons.forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-id');
            const row = this.closest('tr');
            const email = row.cells[1].textContent;
            const role = row.cells[2].textContent.toLowerCase();
            
            document.getElementById('edit_id').value = userId;
            document.getElementById('edit_email').value = email;
            document.getElementById('edit_role').value = role;
            
            editUserModal.style.display = 'flex';
        });
    });
    
    // Delete User Buttons
    const deleteButtons = document.querySelectorAll('.btn-delete');
    const deleteModal = document.getElementById('deleteModal');
    
    deleteButtons.forEach(button => {
        button.addEventListener('click', function() {
            const userId = this.getAttribute('data-id');
            document.getElementById('delete_id').value = userId;
            deleteModal.style.display = 'flex';
        });
    });
    
    // Close Modals
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
    
    // Add User Form Submission
    document.getElementById('addUserForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('../includes/admin_actions.php?action=add_user', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('User added successfully!');
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
    
    // Edit User Form Submission
    document.getElementById('editUserForm').addEventListener('submit', function(e) {
        e.preventDefault();
        
        const formData = new FormData(this);
        
        fetch('../includes/admin_actions.php?action=edit_user', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('User updated successfully!');
                window.location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    });
    
    // Delete User Confirmation
    document.getElementById('confirmDelete').addEventListener('click', function() {
        const userId = document.getElementById('delete_id').value;
        
        fetch('../includes/admin_actions.php?action=delete_user&id=' + userId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('User deleted successfully!');
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