<?php 
require_once('../includes/db_connect.php');

// Non-session auth: Getting student ID from URL
$student_id = $_GET['user_id'] ?? 0;

// Fetch Student Profile & Join with Details
$query = "SELECT u.*, s.* FROM users u 
          JOIN student_details s ON u.id = s.user_id 
          WHERE u.id = '$student_id'";
$res = mysqli_query($conn, $query);
$data = mysqli_fetch_assoc($res);

// If no data found (e.g., direct access without ID)
if(!$data) {
    header("Location: login.php");
    exit();
}

// 1. DYNAMIC IMAGE LOGIC: Consistent but random face for every different student ID
// If user has uploaded a custom photo, use it; otherwise use pravatar
$profile_img = (!empty($data['photo']) && $data['photo'] != 'default_user.png') ? '../'.$data['photo'] : "https://i.pravatar.cc/150?u=" . $student_id;

// 2. PERFORMANCE LOGIC: Fetch grades and calculate a mock GPA
$grade_res = mysqli_query($conn, "SELECT AVG(marks_obtained) as avg_m FROM grades WHERE user_id = '$student_id'");
$grade_stats = mysqli_fetch_assoc($grade_res);
$avg_marks = $grade_stats['avg_m'] ?? 0;
$gpa = round(($avg_marks / 100) * 4, 2);

// 3. FEE LOGIC: Fetch fee status
$fee_query = mysqli_query($conn, "SELECT * FROM fees WHERE user_id = '$student_id'");
$fee = mysqli_fetch_assoc($fee_query);

// 4. NOTIFICATION LOGIC
$notif_res = mysqli_query($conn, "SELECT COUNT(*) as count FROM notifications WHERE user_id = '$student_id' AND is_read = 0");
$notif_count = mysqli_fetch_assoc($notif_res)['count'] ?? 0;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $data['full_name']; ?> | Student Portal</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body { background-color: #f0f2f5; font-family: 'Plus Jakarta Sans', sans-serif; }
        
        /* ID Card Professional Styling */
        .id-card-canvas {
            background: linear-gradient(135deg, #0f172a 0%, #1e3a8a 100%);
            border-radius: 20px;
            color: white;
            padding: 30px;
            position: relative;
            overflow: hidden;
            box-shadow: 0 15px 35px rgba(0,0,0,0.2);
        }
        
        .id-card-canvas::before {
            content: "";
            position: absolute;
            top: -20%; right: -10%;
            width: 200px; height: 200px;
            background: rgba(255,255,255,0.05);
            border-radius: 50%;
        }

        .avatar-frame {
            width: 110px; height: 110px;
            border-radius: 50%;
            border: 4px solid rgba(255,255,255,0.2);
            padding: 3px;
            margin: 0 auto 15px;
        }

        .avatar-frame img {
            width: 100%; height: 100%;
            border-radius: 50%;
            object-fit: cover;
        }

        .qr-section {
            background: white;
            padding: 8px;
            border-radius: 12px;
            display: inline-block;
        }

        /* Print Logic for ID Card */
        @media print {
            body * { visibility: hidden; }
            .id-card-canvas, .id-card-canvas * { visibility: visible; }
            .id-card-canvas {
                position: absolute;
                left: 0; top: 0;
                width: 350px;
                height: 500px;
            }
            .no-print { display: none; }
        }
    </style>
</head>
<body>

<div class="bg-primary text-white py-2 overflow-hidden no-print">
    <div class="d-flex align-items-center">
    <span class="badge bg-danger ms-3 me-3">LATEST</span>

    <marquee 
        behavior="scroll" 
        direction="left" 
        scrollamount="10"
        class="fw-bold">

        <?php 
        $notices = mysqli_query($conn, "SELECT * FROM notices ORDER BY id DESC LIMIT 3");
        while($n = mysqli_fetch_assoc($notices)) {
            echo "<span style='margin-right: 80px;'>"
                . "🔔 <strong>{$n['title']}:</strong> {$n['message']}"
                . "</span>";
        }
        ?>
        
    </marquee>
</div>

</div>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-5 no-print">
        <h2 class="fw-800 mb-0"><i class="fas fa-user-circle text-primary me-2"></i>Student Hub</h2>
        
        <div class="d-flex align-items-center">
            <div class="dropdown me-3">
                <button class="btn btn-light position-relative rounded-circle shadow-sm" data-bs-toggle="dropdown">
                    <i class="fas fa-bell"></i>
                    <?php if($notif_count > 0): ?>
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger" style="font-size: 0.6rem;">
                            <?php echo $notif_count; ?>
                        </span>
                    <?php endif; ?>
                </button>
                <ul class="dropdown-menu dropdown-menu-end border-0 shadow p-3" style="width: 300px; border-radius: 15px;">
                    <h6 class="fw-bold border-bottom pb-2">Recent Alerts</h6>
                    <?php 
                    $notifs = mysqli_query($conn, "SELECT * FROM notifications WHERE user_id = '$student_id' ORDER BY id DESC LIMIT 5");
                    if(mysqli_num_rows($notifs) > 0) {
                        while($n = mysqli_fetch_assoc($notifs)): ?>
                            <li class="small mb-2 pb-2 border-bottom text-muted">
                                <i class="fas fa-info-circle me-2 text-primary"></i>
                                <?php echo $n['message']; ?>
                            </li>
                        <?php endwhile;
                    } else { echo "<li class='small text-center text-muted py-2'>No new alerts</li>"; } ?>
                </ul>
            </div>

            <a href="profile_settings.php?user_id=<?php echo $student_id; ?>" class="btn btn-light rounded-circle shadow-sm me-3">
                <i class="fas fa-user-cog"></i>
            </a>

            <a href="../index.php" class="btn btn-outline-danger rounded-pill px-4 shadow-sm">
                <i class="fas fa-power-off me-2"></i>Logout
            </a>
        </div>
    </div>

    <div class="row g-4">
        <div class="col-lg-4">
            <div class="id-card-canvas text-center">
                <div class="avatar-frame">
                    <img src="<?php echo $profile_img; ?>" alt="Student Photo">
                </div>
                <h4 class="fw-bold mb-1"><?php echo $data['full_name']; ?></h4>
                <p class="small text-info text-uppercase fw-bold mb-4"><?php echo $data['course']; ?></p>
                
                <div class="row text-start g-3 border-top border-white border-opacity-10 pt-4 mt-2">
                    <div class="col-6">
                        <small class="opacity-50 d-block text-uppercase" style="font-size: 10px;">ID Number</small>
                        <span class="fw-bold"><?php echo $data['roll_number']; ?></span>
                    </div>
                    <div class="col-6 text-end">
                        <small class="opacity-50 d-block text-uppercase" style="font-size: 10px;">Academic Year</small>
                        <span class="fw-bold">2026-27</span>
                    </div>
                </div>

                <div class="mt-4 pt-2">
                    <div class="qr-section shadow-sm">
                        <img src="https://api.qrserver.com/v1/create-qr-code/?size=70x70&data=STU-VERIFY-<?php echo $student_id; ?>" alt="QR">
                    </div>
                </div>

                <button class="btn btn-light w-100 mt-4 rounded-3 fw-bold no-print" onclick="window.print()">
                    <i class="fas fa-print me-2"></i>Print Identity Card
                </button>
            </div>
        </div>

        <div class="col-lg-8">
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="pro-card bg-white p-4 h-100 shadow-sm" style="border-radius: 20px;">
                        <div class="d-flex justify-content-between mb-3">
                            <span class="fw-bold text-muted">Academic GPA</span>
                            <i class="fas fa-chart-line text-primary"></i>
                        </div>
                        <h2 class="fw-800"><?php echo ($gpa > 0) ? $gpa : '0.00'; ?> <span class="fs-6 text-muted">/ 4.00</span></h2>
                        <p class="small text-muted mb-0">Current Performance Index</p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="pro-card bg-white p-4 h-100 shadow-sm" style="border-radius: 20px;">
                        <div class="d-flex justify-content-between mb-3">
                            <span class="fw-bold text-muted">Fee Status</span>
                            <i class="fas fa-receipt text-success"></i>
                        </div>
                        <h2 class="fw-800 text-success">₹<?php echo number_format($fee['paid_amount'] ?? 0, 2); ?></h2>
                        <div class="progress mb-2" style="height: 6px; border-radius: 10px;">
                            <?php $perc = ($fee['total_amount'] > 0) ? ($fee['paid_amount']/$fee['total_amount'])*100 : 0; ?>
                            <div class="progress-bar bg-success" style="width: <?php echo $perc; ?>%"></div>
                        </div>
                        <small class="text-muted">Current: <b><?php echo $fee['status'] ?? 'Unpaid'; ?></b></small>
                    </div>
                </div>
            </div>

            <div class="pro-card bg-white p-4 shadow-sm" style="border-radius: 20px;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h5 class="fw-800 mb-0">Marksheet / Results</h5>
                    <span class="badge bg-light text-dark border px-3">Spring 2026</span>
                </div>
                <div class="table-responsive">
                    <table class="table table-hover align-middle border-0">
                        <thead class="table-light">
                            <tr>
                                <th>Subject</th>
                                <th>Marks</th>
                                <th>Grade</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $grades = mysqli_query($conn, "SELECT * FROM grades WHERE user_id = '$student_id'");
                            if(mysqli_num_rows($grades) > 0) {
                                while($g = mysqli_fetch_assoc($grades)):
                                    $m = $g['marks_obtained'];
                                    $grad = ($m >= 80) ? 'A' : (($m >= 60) ? 'B' : (($m >= 40) ? 'C' : 'F'));
                                    $color = ($m >= 40) ? 'text-success' : 'text-danger';
                            ?>
                            <tr>
                                <td class="fw-bold"><?php echo $g['subject_name']; ?></td>
                                <td><?php echo $m; ?> <small class="text-muted">/ 100</small></td>
                                <td class="fw-800 text-primary"><?php echo $grad; ?></td>
                                <td><span class="badge <?php echo ($m >= 40) ? 'bg-success-subtle text-success' : 'bg-danger-subtle text-danger'; ?> px-3">
                                    <?php echo ($m >= 40) ? 'Passed' : 'Failed'; ?>
                                </span></td>
                            </tr>
                            <?php endwhile; 
                            } else { echo "<tr><td colspan='4' class='text-center py-4 text-muted'>Evaluation in progress.</td></tr>"; } ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="pro-card bg-white p-4 shadow-sm mt-4" style="border-radius: 20px;">
                <h5 class="fw-800 mb-4 text-primary"><i class="fas fa-layer-group me-2"></i>Curriculum Resources</h5>
                <div class="row g-3">
                    <?php 
                    $stu_course = $data['course'];
                    $docs = mysqli_query($conn, "SELECT * FROM resources WHERE course = '$stu_course' ORDER BY id DESC LIMIT 6");
                    
                    if(mysqli_num_rows($docs) > 0) {
                        while($d = mysqli_fetch_assoc($docs)): ?>
                        <div class="col-md-6">
                            <div class="p-3 border rounded-3 d-flex align-items-center bg-light bg-opacity-50">
                                <div class="bg-primary text-white p-2 rounded-3 me-3" style="width: 40px; height:40px; display:flex; align-items:center; justify-content:center;">
                                    <i class="fas fa-file-pdf"></i>
                                </div>
                                <div class="overflow-hidden flex-grow-1">
                                    <h6 class="mb-0 text-truncate small fw-bold"><?php echo $d['title']; ?></h6>
                                    <small class="text-muted" style="font-size: 10px;">Ref: <?php echo $d['uploaded_by']; ?></small>
                                </div>
                                <a href="../<?php echo $d['file_path']; ?>" download class="btn btn-sm btn-white shadow-sm ms-2">
                                    <i class="fas fa-download text-primary"></i>
                                </a>
                            </div>
                        </div>
                    <?php endwhile; 
                    } else { echo "<p class='text-muted small ps-2'>Digital repository is currently empty for your course.</p>"; } ?>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if(isset($_GET['status']) && $_GET['status'] == 'success'): ?>
<div class="toast-container position-fixed bottom-0 end-0 p-3 no-print">
  <div class="toast show align-items-center text-white bg-success border-0" role="alert" style="border-radius: 12px;">
    <div class="d-flex">
      <div class="toast-body"><i class="fas fa-check-circle me-2"></i> Your portal has been updated successfully!</div>
      <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
    </div>
  </div>
</div>
<?php endif; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>