<?php 
require_once('../includes/db_connect.php');
$student_id = $_GET['user_id'];
$user_data = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM student_details WHERE user_id='$student_id'"));
?>

<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="pro-card bg-white p-4">
                <h5 class="fw-bold mb-4">Edit Profile Settings</h5>
                <form action="../modules/update_profile.php?user_id=<?php echo $student_id; ?>" method="POST" enctype="multipart/form-data">
                    <div class="text-center mb-4">
                        <img src="../assets/images/default_user.png" class="rounded-circle mb-3" width="100">
                        <input type="file" name="profile_pic" class="form-control form-control-sm">
                    </div>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="small fw-bold">Phone Number</label>
                            <input type="text" name="phone" class="form-control" value="<?php echo $user_data['phone']; ?>">
                        </div>
                        <div class="col-md-6">
                            <label class="small fw-bold">Current Course</label>
                            <input type="text" class="form-control" value="<?php echo $user_data['course']; ?>" disabled>
                        </div>
                        <div class="col-12">
                            <label class="small fw-bold">Home Address</label>
                            <textarea name="address" class="form-control" rows="3"><?php echo $user_data['address']; ?></textarea>
                        </div>
                    </div>
                    <button type="submit" name="update_btn" class="btn btn-pro mt-4">Save Changes</button>
                </form>
            </div>
        </div>
    </div>
</div>