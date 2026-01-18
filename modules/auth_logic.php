<?php
require_once('../includes/db_connect.php');

if (isset($_POST['login_btn'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);

    // Direct check since we are not using hashed passwords
    $query = "SELECT * FROM users WHERE email='$email' AND password='$password' LIMIT 1";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
        $user_id = $user['id'];
        $role = $user['role'];

        // Redirecting with URL parameters (Replacing Session logic)
        if ($role == 'admin') {
            header("Location: ../views/admin_dashboard.php?user_id=$user_id&auth=success");
        } else {
            header("Location: ../views/student_dashboard.php?user_id=$user_id&auth=success");
        }
        exit();
    } else {
        echo "<script>alert('Invalid Email or Password'); window.location='../views/login.php';</script>";
    }
}
?>