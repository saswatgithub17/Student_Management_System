<?php 
require_once('../includes/db_connect.php');
$user_id = $_GET['user_id'] ?? 0;

// Fetch current details
$query = "SELECT u.*, s.phone, s.address, s.photo FROM users u 
          LEFT JOIN student_details s ON u.id = s.user_id WHERE u.id = '$user_id'";
$user = mysqli_fetch_assoc(mysqli_query($conn, $query));

if (!$user) { header("Location: ../index.php"); exit(); }
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Account Settings | EduFlow Pro</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="pro-card bg-white p-5 shadow-lg" style="border-radius: 25px;">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="fw-800 text-primary mb-0">Account Settings</h2>
                    <a href="javascript:history.back()" class="btn btn-light rounded-pill"><i class="fas fa-arrow-left me-2"></i>Back</a>
                </div>

                <form action="../modules/update_profile.php?user_id=<?php echo $user_id; ?>" method="POST" enctype="multipart/form-data">
                    <div class="text-center mb-5">
                        <div class="position-relative d-inline-block">
                            <img src="<?php echo (!empty($user['photo']) && $user['photo'] != 'default_user.png') ? '../'.$user['photo'] : 'https://i.pravatar.cc/150?u='.$user_id; ?>" 
                                 class="rounded-circle border border-4 border-primary shadow" style="width: 130px; height: 130px; object-fit: cover;">
                            <label for="photo-upload" class="position-absolute bottom-0 end-0 bg-primary text-white p-2 rounded-circle shadow" style="cursor: pointer;">
                                <i class="fas fa-camera"></i>
                            </label>
                            <input type="file" id="photo-upload" name="profile_pic" class="d-none">
                        </div>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="small fw-bold">Full Name</label>
                            <input type="text" name="full_name" class="form-control bg-light border-0 p-3" value="<?php echo $user['full_name']; ?>" required>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold">Email Address</label>
                            <input type="email" class="form-control bg-light border-0 p-3" value="<?php echo $user['email']; ?>" disabled>
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold">Phone Number</label>
                            <input type="text" name="phone" class="form-control bg-light border-0 p-3" value="<?php echo $user['phone'] ?? ''; ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold">New Password (Leave blank to keep current)</label>
                            <input type="password" name="new_password" class="form-control bg-light border-0 p-3" placeholder="••••••••">
                        </div>
                        <div class="col-12">
                            <label class="small fw-bold">Residential Address</label>
                            <textarea name="address" class="form-control bg-light border-0 p-3" rows="3"><?php echo $user['address'] ?? ''; ?></textarea>
                        </div>
                    </div>

                    <button type="submit" name="update_account" class="btn btn-primary w-100 py-3 mt-4 shadow fw-bold">Save All Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>
</body>
</html>