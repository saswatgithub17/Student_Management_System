<?php 
require_once('../includes/db_connect.php');
$admin_id = $_GET['user_id'];

// Get list of students for the dropdown
$students = mysqli_query($conn, "SELECT id, full_name FROM users WHERE role='student'");
?>

<div class="container py-5">
    <div class="pro-card bg-white p-5 shadow-sm max-w-600 mx-auto">
        <h4 class="fw-800 mb-4">Assign Student Grades</h4>
        <form action="../modules/grade_logic.php?user_id=<?php echo $admin_id; ?>" method="POST">
            <div class="mb-3">
                <label class="form-label fw-bold">Select Student</label>
                <select name="student_id" class="form-select rounded-3">
                    <?php while($st = mysqli_fetch_assoc($students)) {
                        echo "<option value='{$st['id']}'>{$st['full_name']}</option>";
                    } ?>
                </select>
            </div>
            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Subject</label>
                    <input type="text" name="subject" class="form-control" placeholder="e.g. Data Structures" required>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label fw-bold">Marks (Out of 100)</label>
                    <input type="number" name="marks" class="form-control" min="0" max="100" required>
                </div>
            </div>
            <button type="submit" name="save_grade" class="btn btn-pro w-100">Post Result</button>
        </form>
    </div>
</div>