<?php
require_once('../includes/db_connect.php');
$uid = $_GET['user_id'];

if (isset($_POST['update_account'])) {
    $name = mysqli_real_escape_string($conn, $_POST['full_name']);
    $phone = mysqli_real_escape_string($conn, $_POST['phone']);
    $addr = mysqli_real_escape_string($conn, $_POST['address']);
    $pass = $_POST['new_password'];

    // Update Users table
    mysqli_query($conn, "UPDATE users SET full_name = '$name' WHERE id = '$uid'");
    if(!empty($pass)) {
        mysqli_query($conn, "UPDATE users SET password = '$pass' WHERE id = '$uid'");
    }

    // Update Student Details
    mysqli_query($conn, "UPDATE student_details SET phone = '$phone', address = '$addr' WHERE user_id = '$uid'");

    // Handle Photo Upload
    if(!empty($_FILES['profile_pic']['name'])) {
        $path = "uploads/profiles/" . time() . "_" . $_FILES['profile_pic']['name'];
        if(move_uploaded_file($_FILES['profile_pic']['tmp_name'], "../" . $path)) {
            mysqli_query($conn, "UPDATE student_details SET photo = '$path' WHERE user_id = '$uid'");
        }
    }

    header("Location: ../views/profile_settings.php?user_id=$uid&status=success");
}