<?php 
require_once('../includes/db_connect.php');
$admin_id = $_GET['user_id'] ?? 0;

// --- PAGINATION LOGIC ---
$limit = 20; 
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Fetch Total Records for Pagination count
$total_res = mysqli_query($conn, "SELECT COUNT(id) AS id FROM fees");
$total_data = mysqli_fetch_assoc($total_res);
$total_records = $total_data['id'];
$total_pages = ceil($total_records / $limit);

// Fetch Financial Totals (Summary Tiles - stays global)
$stats_query = mysqli_query($conn, "SELECT 
    SUM(total_amount) as total_expected, 
    SUM(paid_amount) as total_collected
    FROM fees");
$stats = mysqli_fetch_assoc($stats_query);
$total_pending = $stats['total_expected'] - $stats['total_collected'];

// Fetch individual records for the CURRENT PAGE
$query = "SELECT u.id as user_id, u.full_name, s.roll_number, f.total_amount, f.paid_amount, f.status 
          FROM fees f 
          JOIN users u ON f.user_id = u.id
          JOIN student_details s ON u.id = s.user_id
          ORDER BY f.id DESC 
          LIMIT $start, $limit";
$result = mysqli_query($conn, $query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Financial Management | EduFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        :root { --accent: #3b82f6; }
        .finance-card { border: none; border-left: 5px solid var(--accent); border-radius: 15px; }
        .progress { height: 8px; border-radius: 10px; background-color: #e9ecef; }
        .table-v-align td { vertical-align: middle; }
        .page-link { border: none; margin: 0 3px; border-radius: 8px !important; color: #444; }
        .page-item.active .page-link { background-color: var(--primary); color: white; }
        @media print { .no-print { display: none; } }
    </style>
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-5 no-print">
        <div>
            <h2 class="fw-800 mb-0 text-primary">Fee Management</h2>
            <p class="text-muted">Displaying 20 records per page of your institution's finances.</p>
        </div>
        <div class="d-flex gap-2">
            <a href="admin_dashboard.php?user_id=<?php echo $admin_id; ?>" class="btn btn-outline-secondary rounded-pill px-4">
                <i class="fas fa-arrow-left me-2"></i>Dashboard
            </a>
            <button class="btn btn-pro shadow-sm rounded-pill px-4" onclick="window.print()">
                <i class="fas fa-file-pdf me-2"></i>Export Report
            </button>
        </div>
    </div>

    <div class="row g-4 mb-5 no-print">
        <div class="col-md-4">
            <div class="pro-card p-4 bg-white finance-card" style="border-left-color: #10b981;">
                <small class="text-uppercase fw-bold text-muted">Total Collected</small>
                <h2 class="fw-800 mb-0">₹<?php echo number_format($stats['total_collected'], 2); ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="pro-card p-4 bg-white finance-card" style="border-left-color: #f59e0b;">
                <small class="text-uppercase fw-bold text-muted">Total Pending</small>
                <h2 class="fw-800 mb-0">₹<?php echo number_format($total_pending, 2); ?></h2>
            </div>
        </div>
        <div class="col-md-4">
            <div class="pro-card p-4 bg-white finance-card" style="border-left-color: #3b82f6;">
                <small class="text-uppercase fw-bold text-muted">Total Revenue</small>
                <h2 class="fw-800 mb-0">₹<?php echo number_format($stats['total_expected'], 2); ?></h2>
            </div>
        </div>
    </div>

    <div class="pro-card bg-white p-4 shadow-sm">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h5 class="fw-800 mb-0">Student Fee Records</h5>
            <span class="badge bg-light text-dark border">Showing <?php echo $start+1; ?>-<?php echo min($start+$limit, $total_records); ?> of <?php echo $total_records; ?></span>
        </div>
        
        <div class="table-responsive">
            <table class="table table-hover table-v-align border-0">
                <thead class="table-light">
                    <tr class="text-muted small text-uppercase">
                        <th>Student Details</th>
                        <th>Payment Progress</th>
                        <th>Total Fee</th>
                        <th>Amount Paid</th>
                        <th>Status</th>
                        <th class="no-print">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while($row = mysqli_fetch_assoc($result)): 
                        $percentage = ($row['total_amount'] > 0) ? ($row['paid_amount'] / $row['total_amount']) * 100 : 0;
                    ?>
                    <tr>
                        <td>
                            <div class="fw-bold"><?php echo $row['full_name']; ?></div>
                            <small class="text-muted"><?php echo $row['roll_number']; ?></small>
                        </td>
                        <td style="width: 180px;">
                            <div class="d-flex align-items-center">
                                <div class="progress w-100 me-2">
                                    <div class="progress-bar bg-primary" style="width: <?php echo $percentage; ?>%"></div>
                                </div>
                                <small class="fw-bold"><?php echo round($percentage); ?>%</small>
                            </div>
                        </td>
                        <td class="fw-bold">₹<?php echo number_format($row['total_amount'], 2); ?></td>
                        <td><span class="text-success fw-bold">₹<?php echo number_format($row['paid_amount'], 2); ?></span></td>
                        <td>
                            <?php 
                                $status_class = ($row['status'] == 'Paid') ? 'bg-success' : (($row['status'] == 'Pending') ? 'bg-warning text-dark' : 'bg-danger');
                            ?>
                            <span class="badge rounded-pill <?php echo $status_class; ?> px-3 py-2">
                                <?php echo $row['status']; ?>
                            </span>
                        </td>
                        <td class="no-print">
                            <div class="d-flex gap-2">
                                <a href="generate_receipt.php?user_id=<?php echo $row['user_id']; ?>" target="_blank" class="btn btn-sm btn-outline-primary rounded-pill px-3">
                                    <i class="fas fa-file-invoice me-1"></i> Receipt
                                </a>
                                
                                <?php if($row['status'] != 'Paid'): ?>
                                <a href="../modules/fee_actions.php?action=send_reminder&stu_id=<?php echo $row['user_id']; ?>&admin_id=<?php echo $admin_id; ?>" class="btn btn-sm btn-outline-dark rounded-pill px-3">
                                    <i class="fas fa-bell me-1"></i> Remind
                                </a>
                                <?php endif; ?>
                            </div>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

        <nav class="mt-4 no-print">
            <ul class="pagination justify-content-center">
                <?php if($page > 1): ?>
                    <li class="page-item"><a class="page-link shadow-sm" href="?user_id=<?php echo $admin_id; ?>&page=<?php echo $page-1; ?>"><i class="fas fa-chevron-left"></i></a></li>
                <?php endif; ?>

                <?php 
                $visible_pages = 5;
                $start_page = max(1, $page - 2);
                $end_page = min($total_pages, $start_page + $visible_pages - 1);

                for($i = $start_page; $i <= $end_page; $i++): ?>
                    <li class="page-item <?php echo ($page == $i) ? 'active' : ''; ?>">
                        <a class="page-link shadow-sm" href="?user_id=<?php echo $admin_id; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>

                <?php if($page < $total_pages): ?>
                    <li class="page-item"><a class="page-link shadow-sm" href="?user_id=<?php echo $admin_id; ?>&page=<?php echo $page+1; ?>"><i class="fas fa-chevron-right"></i></a></li>
                <?php endif; ?>
            </ul>
        </nav>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>