<?php
require_once('../includes/db_connect.php');
$stu_id = $_GET['user_id'];

$query = "SELECT u.full_name, u.email, s.roll_number, s.course, f.* FROM users u 
          JOIN student_details s ON u.id = s.user_id 
          JOIN fees f ON u.id = f.user_id 
          WHERE u.id = '$stu_id'";
$data = mysqli_fetch_assoc(mysqli_query($conn, $query));
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Fee Receipt - <?php echo $data['roll_number']; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background: #f0f0f0; padding: 50px; }
        .receipt-card { background: white; padding: 40px; border-radius: 0; box-shadow: 0 0 20px rgba(0,0,0,0.1); max-width: 800px; margin: auto; border-top: 10px solid #1e3a8a; }
        @media print { .no-print { display: none; } body { background: white; padding: 0; } .receipt-card { box-shadow: none; width: 100%; } }
    </style>
</head>
<body>
    <div class="text-center mb-4 no-print">
        <button onclick="window.print()" class="btn btn-primary px-4">Print Receipt</button>
    </div>

    <div class="receipt-card">
        <div class="d-flex justify-content-between mb-5">
            <div>
                <h2 class="fw-bold text-primary">EduFlow University</h2>
                <p class="text-muted small">123 Education Lane, New Delhi, India<br>Contact: support@eduflow.edu.in</p>
            </div>
            <div class="text-end">
                <h4 class="text-uppercase text-muted">Fee Receipt</h4>
                <p class="mb-0">Date: <?php echo date('d-m-Y'); ?></p>
                <p>Receipt #: <?php echo time(); ?></p>
            </div>
        </div>

        <div class="row mb-5">
            <div class="col-6">
                <h6 class="text-muted text-uppercase small fw-bold">Student Details</h6>
                <p class="mb-0"><strong><?php echo $data['full_name']; ?></strong></p>
                <p class="mb-0">ID: <?php echo $data['roll_number']; ?></p>
                <p class="mb-0">Course: <?php echo $data['course']; ?></p>
            </div>
        </div>

        <table class="table table-bordered">
            <thead class="table-light">
                <tr>
                    <th>Description</th>
                    <th class="text-end">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Semester Tuition & Administrative Fees</td>
                    <td class="text-end">₹<?php echo number_format($data['total_amount'], 2); ?></td>
                </tr>
                <tr class="fw-bold">
                    <td class="text-end">Total Amount Paid</td>
                    <td class="text-end text-success">₹<?php echo number_format($data['paid_amount'], 2); ?></td>
                </tr>
                <tr class="fw-bold text-danger">
                    <td class="text-end">Outstanding Balance</td>
                    <td class="text-end">₹<?php echo number_format($data['total_amount'] - $data['paid_amount'], 2); ?></td>
                </tr>
            </tbody>
        </table>

        <div class="mt-5 pt-5 text-center">
            <p class="small text-muted">This is a computer-generated document and requires no signature.</p>
            <div class="mt-4" style="border-top: 1px solid #eee; padding-top: 10px;">
                <p class="fw-bold text-primary">Thank you for your payment!</p>
            </div>
        </div>
    </div>
</body>
</html>