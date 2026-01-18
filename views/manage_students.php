<?php 
require_once('../includes/db_connect.php');
$admin_id = $_GET['user_id'] ?? 0;

// Pagination Logic
$limit = 10; 
$page = isset($_GET['page']) ? $_GET['page'] : 1;
$start = ($page - 1) * $limit;

$result = mysqli_query($conn, "SELECT * FROM users WHERE role='student' LIMIT $start, $limit");
$result_count = mysqli_query($conn, "SELECT count(id) AS id FROM users WHERE role='student'");
$total = mysqli_fetch_all($result_count, MYSQLI_ASSOC);
$pages = ceil($total[0]['id'] / $limit);
$search = $_GET['search'] ?? '';
$query_str = "SELECT * FROM users WHERE role='student'";

if($search != '') {
    $query_str .= " AND (full_name LIKE '%$search%' OR email LIKE '%$search%')";
}

$query_str .= " LIMIT $start, $limit";
$result = mysqli_query($conn, $query_str);
$search = $_GET['search'] ?? '';
$query = "SELECT u.*, s.roll_number FROM users u 
          JOIN student_details s ON u.id = s.user_id 
          WHERE u.role = 'student'";

if (!empty($search)) {
    $query .= " AND (u.full_name LIKE '%$search%' OR s.roll_number LIKE '%$search%')";
}
$query .= " LIMIT $start, $limit";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Manage Students | EduFlow</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="fw-bold">Student Directory</h2>
        <a href="admin_dashboard.php?user_id=<?php echo $admin_id; ?>" class="btn btn-outline-secondary btn-sm">Back to Dashboard</a>
    </div>

    <div class="pro-card bg-white p-4">
        <table class="table table-hover">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?php echo $row['id']; ?></td>
                    <td><?php echo $row['full_name']; ?></td>
                    <td><?php echo $row['email']; ?></td>
                    <td>
                        <button class="btn btn-sm btn-light text-primary"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-light text-danger"><i class="fas fa-trash"></i></button>
                    </td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <nav>
            <ul class="pagination justify-content-center">
                <?php for($i = 1; $i <= $pages; $i++): ?>
                    <li class="page-item <?php if($page == $i) echo 'active'; ?>">
                        <a class="page-link" href="manage_students.php?user_id=<?php echo $admin_id; ?>&page=<?php echo $i; ?>"><?php echo $i; ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    </div>
</div>
</body>
</html>