<?php
// File: dashboard/attendance.php
require_once '../includes/auth.php';

$pageTitle = 'Attendance Management';
require_once 'header.php';

global $pdo;

// Get classes for dropdown
$classes = [];
if (isAdmin() || isTeacher()) {
    $stmt = $pdo->query("SELECT DISTINCT class FROM students ORDER BY class");
    $classes = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Get sections for dropdown
$sections = [];
if (isAdmin() || isTeacher()) {
    $stmt = $pdo->query("SELECT DISTINCT section FROM students ORDER BY section");
    $sections = $stmt->fetchAll(PDO::FETCH_COLUMN);
}

// Get students based on class and section
$students = [];
$selectedClass = '';
$selectedSection = '';
$selectedDate = date('Y-m-d');

if (isset($_GET['class']) && isset($_GET['section']) && isset($_GET['date'])) {
    $selectedClass = sanitizeInput($_GET['class']);
    $selectedSection = sanitizeInput($_GET['section']);
    $selectedDate = sanitizeInput($_GET['date']);
    
    $stmt = $pdo->prepare("SELECT * FROM students WHERE class = ? AND section = ? ORDER BY roll_number");
    $stmt->execute([$selectedClass, $selectedSection]);
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get existing attendance for the selected date
    $attendance = [];
    if (!empty($students)) {
        $studentIds = array_column($students, 'id');
        $placeholders = implode(',', array_fill(0, count($studentIds), '?'));
        
        $stmt = $pdo->prepare("SELECT student_id, status, remarks FROM attendance WHERE student_id IN ($placeholders) AND date = ?");
        $stmt->execute(array_merge($studentIds, [$selectedDate]));
        
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $attendance[$row['student_id']] = $row;
        }
    }
}

// Handle attendance submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && (isAdmin() || isTeacher())) {
    $class = sanitizeInput($_POST['class']);
    $section = sanitizeInput($_POST['section']);
    $date = sanitizeInput($_POST['date']);
    $attendanceData = $_POST['attendance'];
    
    try {
        $pdo->beginTransaction();
        
        foreach ($attendanceData as $studentId => $data) {
            $status = sanitizeInput($data['status']);
            $remarks = sanitizeInput($data['remarks'] ?? '');
            
            // Check if attendance already exists for this student and date
            $stmt = $pdo->prepare("SELECT id FROM attendance WHERE student_id = ? AND date = ?");
            $stmt->execute([$studentId, $date]);
            
            if ($stmt->fetch()) {
                // Update existing record
                $stmt = $pdo->prepare("UPDATE attendance SET status = ?, remarks = ?, recorded_by = ? WHERE student_id = ? AND date = ?");
                $stmt->execute([$status, $remarks, $_SESSION['user_id'], $studentId, $date]);
            } else {
                // Insert new record
                $stmt = $pdo->prepare("INSERT INTO attendance (student_id, date, status, remarks, recorded_by) VALUES (?, ?, ?, ?, ?)");
                $stmt->execute([$studentId, $date, $status, $remarks, $_SESSION['user_id']]);
            }
        }
        
        $pdo->commit();
        $_SESSION['success'] = 'Attendance saved successfully!';
        redirect("attendance.php?class=$class&section=$section&date=$date");
    } catch (PDOException $e) {
        $pdo->rollBack();
        $_SESSION['error'] = 'Error saving attendance: ' . $e->getMessage();
    }
}

// For students, get their attendance records
$studentAttendance = [];
if (isStudent()) {
    $student = getStudentByUserId($_SESSION['user_id']);
    
    $stmt = $pdo->prepare("SELECT date, status, remarks FROM attendance WHERE student_id = ? ORDER BY date DESC LIMIT 30");
    $stmt->execute([$student['id']]);
    $studentAttendance = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<div class="attendance-management">
    <?php if (isAdmin() || isTeacher()): ?>
        <h2>Mark Attendance</h2>
        
        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success"><?php echo $_SESSION['success']; unset($_SESSION['success']); ?></div>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger"><?php echo $_SESSION['error']; unset($_SESSION['error']); ?></div>
        <?php endif; ?>
        

                <div class="attendance-filter">
    <form method="GET" action="attendance.php" id="filterForm">
        <div class="form-group">
            <label for="class">Course</label>
            <select id="class" name="class" required>
                <option value="">Select Course</option>
                <option value="BSc" <?php echo $selectedClass == 'BSc' ? 'selected' : ''; ?>>BSc</option>
                <option value="BCom" <?php echo $selectedClass == 'BCom' ? 'selected' : ''; ?>>BCom</option>
                <option value="BBA" <?php echo $selectedClass == 'BBA' ? 'selected' : ''; ?>>BBA</option>
                <option value="BA" <?php echo $selectedClass == 'BA' ? 'selected' : ''; ?>>BA</option>
            </select>
        </div>
                
                
                
                <div class="form-group">
    <label for="semester">Semester</label>
    <select id="section" name="section" required>
        <option value="">Select Semester</option>
        <option value="1" <?php echo ($selectedSection == '1') ? 'selected' : ''; ?>>Semester 1</option>
        <option value="2" <?php echo ($selectedSection == '2') ? 'selected' : ''; ?>>Semester 2</option>
        <option value="3" <?php echo ($selectedSection == '3') ? 'selected' : ''; ?>>Semester 3</option>
        <option value="4" <?php echo ($selectedSection == '4') ? 'selected' : ''; ?>>Semester 4</option>
        <option value="5" <?php echo ($selectedSection == '5') ? 'selected' : ''; ?>>Semester 5</option>
        <option value="6" <?php echo ($selectedSection == '6') ? 'selected' : ''; ?>>Semester 6</option>
    </select>
</div>

                <div class="form-group">
                    <label for="date">Date</label>
                    <input type="date" id="date" name="date" value="<?php echo $selectedDate; ?>" required>
                </div>
                
                <button type="submit" class="btn btn-primary">Load Students</button>
            </form>
        </div>
        
        <?php if (!empty($students)): ?>
            <form method="POST" action="attendance.php" class="attendance-form">
                <input type="hidden" name="class" value="<?php echo $selectedClass; ?>">
                <input type="hidden" name="section" value="<?php echo $selectedSection; ?>">
                <input type="hidden" name="date" value="<?php echo $selectedDate; ?>">
                
                <div class="table-responsive">
                    <table class="attendance-table">
                        <thead>
                            <tr>
                                <th>Roll No.</th>
                                <th>Student Name</th>
                                <th>Status</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($students as $student): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($student['roll_number']); ?></td>
                                    <td><?php echo htmlspecialchars($student['first_name'] . ' ' . htmlspecialchars($student['last_name'])); ?></td>
                                    <td>
                                        <select name="attendance[<?php echo $student['id']; ?>][status]" required>
                                            <option value="present" <?php echo isset($attendance[$student['id']]) && $attendance[$student['id']]['status'] == 'present' ? 'selected' : ''; ?>>Present</option>
                                            <option value="absent" <?php echo isset($attendance[$student['id']]) && $attendance[$student['id']]['status'] == 'absent' ? 'selected' : ''; ?>>Absent</option>
                                            <option value="late" <?php echo isset($attendance[$student['id']]) && $attendance[$student['id']]['status'] == 'late' ? 'selected' : ''; ?>>Late</option>
                                        </select>
                                    </td>
                                    <td>
                                        <input type="text" name="attendance[<?php echo $student['id']; ?>][remarks]" value="<?php echo isset($attendance[$student['id']]) ? htmlspecialchars($attendance[$student['id']]['remarks']) : ''; ?>" placeholder="Remarks">
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Save Attendance</button>
                </div>
            </form>
        <?php elseif (isset($_GET['class']) && isset($_GET['section'])): ?>
            <div class="alert alert-info">No students found for the selected course and semester.</div>
        <?php endif; ?>
        
        <div class="attendance-reports">
            <h3>Attendance Reports</h3>
            <div class="report-card">
                <canvas id="attendanceChart" width="400" height="200"></canvas>
            </div>
        </div>
    <?php else: ?>
        <h2>My Attendance</h2>
        
        <div class="student-attendance">
            <?php if (!empty($studentAttendance)): ?>
                <div class="table-responsive">
                    <table class="attendance-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Status</th>
                                <th>Remarks</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($studentAttendance as $record): ?>
                                <tr>
                                    <td><?php echo date('M d, Y', strtotime($record['date'])); ?></td>
                                    <td>
                                        <span class="status-badge <?php echo $record['status']; ?>">
                                            <?php echo ucfirst($record['status']); ?>
                                        </span>
                                    </td>
                                    <td><?php echo htmlspecialchars($record['remarks']); ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <div class="alert alert-info">No attendance records found.</div>
            <?php endif; ?>
        </div>
        
        <div class="attendance-stats">
            <h3>Attendance Summary</h3>
            <div class="stats-card">
                <canvas id="myAttendanceChart" width="400" height="200"></canvas>
            </div>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    <?php if (isAdmin() || isTeacher()): ?>
        // Attendance Chart
        const ctx = document.getElementById('attendanceChart').getContext('2d');
        const attendanceChart = new Chart(ctx, {
            type: 'bar',
            data: {
                labels: ['Class 9A', 'Class 9B', 'Class 10A', 'Class 10B', 'Class 11A', 'Class 12A'],
                datasets: [{
                    label: 'Attendance Percentage',
                    data: [85, 78, 92, 88, 75, 90],
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        max: 100
                    }
                }
            }
        });
    <?php else: ?>
        // Student Attendance Chart
        const myCtx = document.getElementById('myAttendanceChart').getContext('2d');
        const myAttendanceChart = new Chart(myCtx, {
            type: 'doughnut',
            data: {
                labels: ['Present', 'Absent', 'Late'],
                datasets: [{
                    data: [85, 10, 5],
                    backgroundColor: [
                        'rgba(75, 192, 192, 0.7)',
                        'rgba(255, 99, 132, 0.7)',
                        'rgba(255, 206, 86, 0.7)'
                    ],
                    borderColor: [
                        'rgba(75, 192, 192, 1)',
                        'rgba(255, 99, 132, 1)',
                        'rgba(255, 206, 86, 1)'
                    ],
                    borderWidth: 1
                }]
            }
        });
    <?php endif; ?>
    
    // Auto-submit filter form when any field changes
    document.querySelectorAll('#filterForm select, #filterForm input').forEach(element => {
        element.addEventListener('change', function() {
            document.getElementById('filterForm').submit();
        });
    });
});
</script>

<?php
require_once 'footer.php';
?>