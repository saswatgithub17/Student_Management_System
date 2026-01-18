<?php 
require_once('../includes/db_connect.php');

// Catching parameters (Non-session auth logic)
$admin_id = $_GET['user_id'] ?? 0;
$view = $_GET['view'] ?? 'overview';

// Fetch Admin Data
$query_admin = mysqli_query($conn, "SELECT full_name FROM users WHERE id = '$admin_id'");
$admin_data = mysqli_fetch_assoc($query_admin);

// --- DYNAMIC OVERVIEW STATISTICS ---
$res_stu = mysqli_query($conn, "SELECT COUNT(id) as total FROM users WHERE role='student'");
$total_students = mysqli_fetch_assoc($res_stu)['total'] ?? 0;

$res_fac = mysqli_query($conn, "SELECT COUNT(id) as total FROM users WHERE role='faculty'");
$total_faculty = mysqli_fetch_assoc($res_fac)['total'] ?? 0;

$res_course = mysqli_query($conn, "SELECT COUNT(DISTINCT course) as total FROM student_details");
$total_courses = mysqli_fetch_assoc($res_course)['total'] ?? 0;

$res_att = mysqli_query($conn, "SELECT (SUM(CASE WHEN status = 'Present' THEN 1 ELSE 0 END) / COUNT(*)) * 100 as avg_perc FROM attendance");
$att_data = mysqli_fetch_assoc($res_att);
$avg_attendance = round($att_data['avg_perc'] ?? 0, 1);

// --- ADVANCED ANALYTICS DATA ---
// 1. Course-wise Pending Fees (For Fee Donut)
$fee_analytics_res = mysqli_query($conn, "SELECT s.course, SUM(f.total_amount - f.paid_amount) as pending 
                                          FROM student_details s JOIN fees f ON s.user_id = f.user_id 
                                          GROUP BY s.course");
$course_labels = []; $pending_amounts = [];
while($fa = mysqli_fetch_assoc($fee_analytics_res)){
    $course_labels[] = $fa['course'];
    $pending_amounts[] = $fa['pending'];
}

// 2. Attendance Stats (For Attendance Donut - Present vs Absent vs Leave)
$att_pie_res = mysqli_query($conn, "SELECT status, COUNT(*) as count FROM attendance GROUP BY status");
$att_labels = []; $att_counts = [];
while($ap = mysqli_fetch_assoc($att_pie_res)){
    $att_labels[] = $ap['status'];
    $att_counts[] = $ap['count'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | EduFlow Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        :root { --sidebar-width: 260px; }
        body { background-color: #f4f7fe; font-family: 'Plus Jakarta Sans', sans-serif;}
        .sidebar { width: var(--sidebar-width); height: 100vh; position: fixed; background: var(--primary); color: white; padding: 20px; z-index: 1000; }
        .main-content { margin-left: var(--sidebar-width); padding: 30px; min-height: 100vh; }
        .nav-link-custom { color: rgba(255,255,255,0.7); padding: 12px 15px; display: block; text-decoration: none; border-radius: 12px; margin-bottom: 5px; transition: 0.3s; }
        .nav-link-custom:hover, .nav-link-custom.active { background: rgba(255,255,255,0.1); color: white; }
        .stat-card { border: none; border-radius: 20px; padding: 25px; background: white; box-shadow: 0 10px 20px rgba(0,0,0,0.02); text-align: center; }
        .recent-list-container { max-height: 380px; overflow-y: auto; }
        .pro-card { border: none; border-radius: 20px; box-shadow: 0 10px 20px rgba(0,0,0,0.02); }
        .search-input:focus { box-shadow: 0 4px 12px rgba(0,0,0,0.05) !important; background-color: #fff !important; }
    </style>
</head>
<body>

<div class="sidebar">
    <div class="mb-5 px-3"><h4 class="fw-bold"><i class="fas fa-layer-group me-2"></i>EduFlow</h4></div>
    <nav>
        <a href="admin_dashboard.php?user_id=<?php echo $admin_id; ?>&view=overview" class="nav-link-custom <?php echo ($view == 'overview') ? 'active' : ''; ?>"><i class="fas fa-home me-2"></i> Overview</a>
        <a href="manage_students.php?user_id=<?php echo $admin_id; ?>" class="nav-link-custom"><i class="fas fa-user-graduate me-2"></i> Students</a>
        <a href="attendance_admin.php?user_id=<?php echo $admin_id; ?>" class="nav-link-custom"><i class="fas fa-clipboard-check me-2"></i> Attendance</a>
        <a href="admin_dashboard.php?user_id=<?php echo $admin_id; ?>&view=grades" class="nav-link-custom <?php echo ($view == 'grades') ? 'active' : ''; ?>"><i class="fas fa-medal me-2"></i> Grades</a>
        <a href="admin_dashboard.php?user_id=<?php echo $admin_id; ?>&view=notices" class="nav-link-custom <?php echo ($view == 'notices') ? 'active' : ''; ?>"><i class="fas fa-bullhorn me-2"></i> Notices</a>
        <a href="fees_admin.php?user_id=<?php echo $admin_id; ?>" class="nav-link-custom"><i class="fas fa-file-invoice-dollar me-2"></i> Fees</a>
        <a href="profile_settings.php?user_id=<?php echo $admin_id; ?>" class="nav-link-custom"><i class="fas fa-cog me-2"></i> Settings</a>
        <hr class="opacity-25">
        <a href="../index.php" class="nav-link-custom text-danger"><i class="fas fa-sign-out-alt me-2"></i> Logout</a>
    </nav>
</div>

<div class="main-content">
    <div class="d-flex justify-content-between align-items-center mb-5">
        <div class="d-flex align-items-center flex-grow-1">
            <div class="me-4">
                <h2 class="fw-800 mb-0 fs-4">Hello, <?php echo explode(' ', $admin_data['full_name'])[0] ?? 'Admin'; ?> 👋</h2>
                <p class="text-muted small mb-0">System Overview</p>
            </div>
            <form action="manage_students.php" method="GET" class="d-none d-md-flex ms-4 w-50">
                <input type="hidden" name="user_id" value="<?php echo $admin_id; ?>">
                <div class="input-group">
                    <span class="input-group-text bg-white border-0 shadow-sm" style="border-radius: 12px 0 0 12px;"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-0 shadow-sm search-input" placeholder="Quick Search Student...">
                </div>
            </form>
        </div>
        <div class="pro-card px-4 py-2 bg-white d-flex align-items-center shadow-sm" style="border-radius: 12px;">
            <i class="fas fa-calendar-alt text-primary me-2"></i><span class="fw-bold small"><?php echo date('d M, Y'); ?></span>
        </div>
    </div>

    <?php if ($view == 'grades'): ?>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="fw-800 text-primary">Result Management</h2>
            <button class="btn btn-pro shadow-sm" data-bs-toggle="modal" data-bs-target="#addGradeModal"><i class="fas fa-plus me-2"></i>Assign Marks</button>
        </div>
        <div class="pro-card bg-white p-4 shadow-sm">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light"><tr><th>Student</th><th>Subject</th><th>Marks</th><th>Status</th><th>Date</th></tr></thead>
                    <tbody>
                        <?php
                        $gr = mysqli_query($conn, "SELECT u.full_name, g.subject_name, g.marks_obtained, g.total_marks FROM grades g JOIN users u ON g.user_id = u.id ORDER BY g.id DESC LIMIT 10");
                        while($row = mysqli_fetch_assoc($gr)){
                            $pass = $row['marks_obtained'] >= 40;
                            echo "<tr><td class='fw-bold'>{$row['full_name']}</td><td>{$row['subject_name']}</td><td>{$row['marks_obtained']}/{$row['total_marks']}</td><td><span class='badge rounded-pill ".($pass?'bg-success-subtle text-success':'bg-danger-subtle text-danger')."'>".($pass?'Pass':'Fail')."</span></td><td class='text-muted small'>".date('d M')."</td></tr>";
                        } ?>
                    </tbody>
                </table>
            </div>
        </div>

    <?php elseif ($view == 'notices'): ?>
        <div class="d-flex justify-content-between align-items-center mb-4"><h2 class="fw-800 text-primary">Announcement Center</h2></div>
        <div class="row g-4">
            <div class="col-lg-4">
                <div class="pro-card bg-white p-4 shadow-sm">
                    <h5 class="fw-bold mb-3">Post New Notice</h5>
                    <form action="../modules/admin_logic.php?user_id=<?php echo $admin_id; ?>" method="POST">
                        <input type="text" name="title" class="form-control bg-light border-0 mb-3" placeholder="Notice Title" required>
                        <textarea name="msg" class="form-control bg-light border-0 mb-3" rows="4" required></textarea>
                        <button type="submit" name="post_notice" class="btn btn-pro w-100">Broadcast Now</button>
                    </form>
                </div>
            </div>
            <div class="col-lg-8">
                <div class="pro-card bg-white p-4 shadow-sm">
                    <h5 class="fw-bold mb-4">Active Broadcasts</h5>
                    <?php 
                    $nl = mysqli_query($conn, "SELECT * FROM notices ORDER BY id DESC");
                    while($n = mysqli_fetch_assoc($nl)): ?>
                        <div class="border-bottom py-3">
                            <div class="d-flex justify-content-between"><h6 class="fw-bold mb-1 text-primary"><?php echo $n['title']; ?></h6><small class="text-muted"><?php echo date('d M', strtotime($n['created_at'])); ?></small></div>
                            <p class="small text-muted mb-2"><?php echo $n['message']; ?></p>
                            <a href="../modules/admin_logic.php?del_notice=<?php echo $n['id']; ?>&user_id=<?php echo $admin_id; ?>" class="text-danger small text-decoration-none"><i class="fas fa-trash me-1"></i> Remove</a>
                        </div>
                    <?php endwhile; ?>
                </div>
            </div>
        </div>

    <?php else: ?>
        <div class="row g-4 mb-5">
            <div class="col-md-3"><div class="stat-card"><div class="text-primary mb-2"><i class="fas fa-users fa-2x"></i></div><h3 class="fw-bold"><?php echo $total_students; ?></h3><p class="text-muted mb-0 small">Students</p></div></div>
            <div class="col-md-3"><div class="stat-card"><div class="text-success mb-2"><i class="fas fa-user-tie fa-2x"></i></div><h3 class="fw-bold"><?php echo $total_faculty; ?></h3><p class="text-muted mb-0 small">Faculty</p></div></div>
            <div class="col-md-3"><div class="stat-card"><div class="text-warning mb-2"><i class="fas fa-book fa-2x"></i></div><h3 class="fw-bold"><?php echo $total_courses; ?></h3><p class="text-muted mb-0 small">Courses</p></div></div>
            <div class="col-md-3"><div class="stat-card"><div class="text-info mb-2"><i class="fas fa-check-circle fa-2x"></i></div><h3 class="fw-bold"><?php echo $avg_attendance; ?>%</h3><p class="text-muted mb-0 small">Attendance</p></div></div>
        </div>

        <div class="row g-4 mb-5">
            <div class="col-lg-4">
                <div class="pro-card bg-white p-4 h-100 shadow-sm text-center">
                    <h6 class="fw-800 mb-4 text-start">Academic Distribution</h6>
                    <div style="height: 250px;"><canvas id="performancePieChart"></canvas></div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="pro-card bg-white p-4 h-100 shadow-sm text-center">
                    <h6 class="fw-800 mb-4 text-start">Fee Collection (Dues)</h6>
                    <div style="height: 250px;"><canvas id="feeDonutChart"></canvas></div>
                </div>
            </div>
            <div class="col-lg-4">
                <div class="pro-card bg-white p-4 h-100 shadow-sm text-center">
                    <h6 class="fw-800 mb-4 text-start">Attendance Overview</h6>
                    <div style="height: 250px;"><canvas id="attendanceDonutChart"></canvas></div>
                </div>
            </div>
        </div>

        <div class="row g-4">
            <div class="col-lg-12">
                <div class="pro-card bg-white p-4 shadow-sm">
                    <div class="d-flex justify-content-between align-items-center mb-4">
                        <h5 class="fw-800 mb-0">Recently Joined Students</h5>
                        <a href="manage_students.php?user_id=<?php echo $admin_id; ?>" class="text-primary small fw-bold text-decoration-none">View Full Directory</a>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-hover align-middle border-0">
                            <tbody>
                                <?php
                                $res = mysqli_query($conn, "SELECT id, full_name, role FROM users WHERE role='student' ORDER BY id DESC LIMIT 5");
                                while($row = mysqli_fetch_assoc($res)) {
                                    echo "<tr><td style='width: 40px;'><div class='bg-light rounded-circle d-flex align-items-center justify-content-center' style='width: 35px; height: 35px;'><i class='fas fa-user-graduate text-primary'></i></div></td><td><div class='fw-bold'>{$row['full_name']}</div><div class='text-muted small'>ID: #{$row['id']}</div></td><td class='text-end'><span class='badge bg-success-subtle text-success'>New Member</span></td></tr>";
                                } ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<div class="modal fade" id="addGradeModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow">
            <div class="modal-header border-0 p-4"><h5 class="modal-title fw-800">Assign Marks</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button></div>
            <form action="../modules/grade_logic.php?user_id=<?php echo $admin_id; ?>" method="POST">
                <div class="modal-body p-4 pt-0">
                    <div class="mb-3"><label class="small fw-bold">Student Name</label>
                        <select name="student_id" class="form-select border-0 bg-light p-3 rounded-3" required>
                            <option value="">Select Student...</option>
                            <?php $sl = mysqli_query($conn, "SELECT id, full_name FROM users WHERE role='student' LIMIT 50");
                            while($s = mysqli_fetch_assoc($sl)) { echo "<option value='{$s['id']}'>{$s['full_name']}</option>"; } ?>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-7"><label class="small fw-bold">Subject</label><input type="text" name="subject" class="form-control bg-light border-0 p-3" required></div>
                        <div class="col-5"><label class="small fw-bold">Marks</label><input type="number" name="marks" class="form-control bg-light border-0 p-3" max="100" required></div>
                    </div>
                </div>
                <div class="modal-footer border-0 p-4"><button type="submit" name="save_grade" class="btn btn-pro w-100 py-3">Publish Results</button></div>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    <?php if ($view == 'overview'): 
        $a = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM grades WHERE marks_obtained >= 80"))['c'];
        $b = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM grades WHERE marks_obtained >= 60 AND marks_obtained < 80"))['c'];
        $c = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM grades WHERE marks_obtained >= 40 AND marks_obtained < 60"))['c'];
        $f = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM grades WHERE marks_obtained < 40"))['c'];
    ?>
    // 1. Performance Donut
    new Chart(document.getElementById('performancePieChart'), {
        type: 'doughnut', 
        data: {
            labels: ['Grade A', 'Grade B', 'Grade C', 'Failed'],
            datasets: [{
                data: [<?php echo "$a, $b, $c, $f"; ?>],
                backgroundColor: ['#10b981', '#3b82f6', '#f59e0b', '#ef4444'],
                borderWidth: 2, borderColor: '#ffffff'
            }]
        },
        options: { cutout: '70%', responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom'} } }
    });

    // 2. Fee Donut (Course-wise Pending)
    new Chart(document.getElementById('feeDonutChart'), {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($course_labels); ?>,
            datasets: [{
                data: <?php echo json_encode($pending_amounts); ?>,
                backgroundColor: ['#6366f1', '#8b5cf6', '#ec4899', '#f43f5e'],
                borderWidth: 2, borderColor: '#ffffff'
            }]
        },
        options: { cutout: '70%', responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom'} } }
    });

    // 3. Attendance Donut
    new Chart(document.getElementById('attendanceDonutChart'), {
        type: 'doughnut',
        data: {
            labels: <?php echo json_encode($att_labels); ?>,
            datasets: [{
                data: <?php echo json_encode($att_counts); ?>,
                backgroundColor: ['#22c55e', '#ef4444', '#f59e0b'],
                borderWidth: 2, borderColor: '#ffffff'
            }]
        },
        options: { cutout: '70%', responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom'} } }
    });
    <?php endif; ?>
</script>
</body>
</html>