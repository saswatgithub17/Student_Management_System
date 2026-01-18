<?php
require_once('../includes/db_connect.php');
$admin_id = $_GET['user_id'];

if (isset($_POST['save_grade'])) {
    $student_id = $_POST['student_id'];
    $subject = mysqli_real_escape_string($conn, $_POST['subject']);
    $marks = $_POST['marks'];

    $query = "INSERT INTO grades (user_id, subject_name, marks_obtained, semester) 
              VALUES ('$student_id', '$subject', '$marks', 'Spring 2026')";

    if (mysqli_query($conn, $query)) {
        header("Location: ../views/admin_dashboard.php?user_id=$admin_id&view=grades&status=success");
    } else {
        echo "Error: " . mysqli_error($conn);
    }
}
?>