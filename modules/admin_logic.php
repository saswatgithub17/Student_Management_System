<?php
require_once('../includes/db_connect.php');
$admin_id = $_GET['user_id'];

// Save Notice
if (isset($_POST['post_notice'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $msg = mysqli_real_escape_string($conn, $_POST['msg']);
    
    $query = "INSERT INTO notices (title, message, posted_by) VALUES ('$title', '$msg', 'Administrator')";
    if(mysqli_query($conn, $query)) {
        header("Location: ../views/admin_dashboard.php?user_id=$admin_id&view=notices&status=success");
    }
}

// Delete Notice
if (isset($_GET['del_notice'])) {
    $id = $_GET['del_notice'];
    mysqli_query($conn, "DELETE FROM notices WHERE id = '$id'");
    header("Location: ../views/admin_dashboard.php?user_id=$admin_id&view=notices&status=success");
}
?>