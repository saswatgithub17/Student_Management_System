<?php 
require_once('../includes/db_connect.php');
$faculty_id = $_GET['user_id'] ?? 0;

// Fetch Faculty Details with Error Suppression
$faculty_query = mysqli_query($conn, "SELECT full_name FROM users WHERE id = '$faculty_id'");
$faculty = mysqli_fetch_assoc($faculty_query);
$full_name = $faculty['full_name'] ?? 'Faculty Member';

// --- PAGINATION LOGIC ---
$limit = 10; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Fetch Students for Attendance
$students_res = mysqli_query($conn, "SELECT u.id, u.full_name, s.roll_number, s.course 
                                    FROM users u JOIN student_details s ON u.id = s.user_id 
                                    LIMIT $start, $limit");

// Get stats
$total_res = mysqli_query($conn, "SELECT COUNT(id) AS id FROM users WHERE role='student'");
$total_students = mysqli_fetch_assoc($total_res)['id'];
$total_pages = ceil($total_students / $limit);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Faculty Hub | EduFlow Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        .page-link { border-radius: 8px !important; margin: 0 3px; border: none; background: #fff; color: #333; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .page-item.active .page-link { background: #0d6efd; color: #fff; }
        .upload-card { border-top: 5px solid #0dcaf0; }
    </style>
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="faculty-hero shadow-lg mb-5 bg-primary text-white p-5 rounded-4">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h1 class="fw-800 mb-2">Welcome, Prof. <?php echo explode(' ', $full_name)[0]; ?>!</h1>
                <p class="opacity-75 mb-0"><i class="fas fa-users me-2"></i>You have access to <?php echo $total_students; ?> student records.</p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="../index.php" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm">Logout</a>
            </div>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <div class="pro-card bg-white p-4 shadow-sm h-100">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-800 mb-0">Class Attendance Checklist</h5>
                    <span class="badge bg-primary-subtle text-primary px-3">Page <?php echo $page; ?></span>
                </div>
                
                <form action="../modules/faculty_logic.php?user_id=<?php echo $faculty_id; ?>" method="POST">
                    <div class="table-responsive">
                        <table class="table align-middle table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th>Student Details</th>
                                    <th>Course</th>
                                    <th class="text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while($s = mysqli_fetch_assoc($students_res)): ?>
                                <tr>
                                    <td>
                                        <div class="fw-bold"><?php echo $s['full_name']; ?></div>
                                        <small class="text-muted"><?php echo $s['roll_number']; ?></small>
                                    </td>
                                    <td><span class="badge bg-light text-dark fw-normal border"><?php echo $s['course']; ?></span></td>
                                    <td class="text-center">
                                        <div class="btn-group shadow-sm">
                                            <input type="radio" class="btn-check" name="status[<?php echo $s['id']; ?>]" id="p<?php echo $s['id']; ?>" value="Present" checked>
                                            <label class="btn btn-outline-success btn-sm px-3" for="p<?php echo $s['id']; ?>">P</label>

                                            <input type="radio" class="btn-check" name="status[<?php echo $s['id']; ?>]" id="a<?php echo $s['id']; ?>" value="Absent">
                                            <label class="btn btn-outline-danger btn-sm px-3" for="a<?php echo $s['id']; ?>">A</label>
                                        </div>
                                    </td>
                                </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                    <nav class="mt-4">
                        <ul class="pagination justify-content-center">
                            <?php 
                            $range = 2;
                            for($i = 1; $i <= $total_pages; $i++): 
                                if($i == 1 || $i == $total_pages || ($i >= $page - $range && $i <= $page + $range)):
                            ?>
                            <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                                <a class="page-link shadow-sm" href="faculty_dashboard.php?user_id=<?php echo $faculty_id; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                            </li>
                            <?php endif; endfor; ?>
                        </ul>
                    </nav>

                    <button type="submit" name="bulk_attendance" class="btn btn-primary w-100 py-3 mt-3 shadow-sm fw-bold">
                        <i class="fas fa-check-double me-2"></i>Submit Attendance for Current Page
                    </button>
                </form>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="pro-card bg-white p-4 shadow-sm mb-4 upload-card">
                <h5 class="fw-800 mb-3"><i class="fas fa-file-upload text-info me-2"></i>Upload Material</h5>
                <p class="text-muted small">Share PDFs or documents with your students.</p>
                <form action="../modules/upload_resource.php?user_id=<?php echo $faculty_id; ?>" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="small fw-bold">Document Title</label>
                        <input type="text" name="title" class="form-control bg-light border-0" placeholder="e.g., Semester 2 Notes" required>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold">Target Course</label>
                        <select name="course" class="form-select bg-light border-0" required>
                            <option value="B.Tech CS">B.Tech CS</option>
                            <option value="B.Com">B.Com</option>
                            <option value="BCA">BCA</option>
                            <option value="BBA">BBA</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="small fw-bold">Select File (PDF/DOC)</label>
                        <input type="file" name="material" class="form-control bg-light border-0" required>
                    </div>
                    <button type="submit" class="btn btn-info text-white w-100 fw-bold shadow-sm">
                        <i class="fas fa-cloud-upload-alt me-2"></i>Upload to Portal
                    </button>
                </form>
            </div>

            <div class="pro-card bg-white p-4 shadow-sm">
                <h5 class="fw-800 mb-3">Your Recent Uploads</h5>
                <div class="list-group list-group-flush">
                    <?php 
                    $resources = mysqli_query($conn, "SELECT * FROM resources WHERE uploaded_by = '$full_name' ORDER BY id DESC LIMIT 5");
                    if(mysqli_num_rows($resources) > 0):
                        while($r = mysqli_fetch_assoc($resources)): ?>
                        <div class="list-group-item px-0 border-0 mb-2">
                            <div class="d-flex align-items-center">
                                <div class="bg-info-subtle text-info p-2 rounded-3 me-3">
                                    <i class="fas fa-file-pdf"></i>
                                </div>
                                <div class="overflow-hidden">
                                    <h6 class="mb-0 text-truncate small fw-bold"><?php echo $r['title']; ?></h6>
                                    <small class="text-muted"><?php echo $r['course']; ?></small>
                                </div>
                            </div>
                        </div>
                    <?php endwhile; 
                    else: echo "<p class='text-center text-muted small py-3'>No materials uploaded yet.</p>";
                    endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
<div class="toast-container position-fixed bottom-0 end-0 p-3">
    <div class="toast show align-items-center text-white bg-success border-0" role="alert">
        <div class="d-flex">
            <div class="toast-body"><i class="fas fa-check-circle me-2"></i> Upload Successful!</div>
            <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
        </div>
    </div>
</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>