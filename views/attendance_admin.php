<?php 
require_once('../includes/db_connect.php');
$admin_id = $_GET['user_id'] ?? 0;

// Fetch attendance with student names
$query = "SELECT u.full_name, s.roll_number, a.date, a.status 
          FROM attendance a 
          JOIN users u ON a.user_id = u.id 
          JOIN student_details s ON u.id = s.user_id 
          ORDER BY a.date DESC";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Attendance Log | EduFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold"><i class="fas fa-calendar-check text-primary me-2"></i>Attendance Log</h2>
        <a href="admin_dashboard.php?user_id=<?php echo $admin_id; ?>" class="btn btn-pro btn-sm">Dashboard</a>
    </div>

    <div class="pro-card bg-white p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Roll No</th>
                        <th>Student Name</th>
                        <th>Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td class="fw-bold text-primary"><?php echo $row['roll_number']; ?></td>
                        <td><?php echo $row['full_name']; ?></td>
                        <td><?php echo date('M d, Y', strtotime($row['date'])); ?></td>
                        <td>
                            <?php if($row['status'] == 'Present'): ?>
                                <span class="badge bg-success-subtle text-success px-3">Present</span>
                            <?php else: ?>
                                <span class="badge bg-danger-subtle text-danger px-3">Absent</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>