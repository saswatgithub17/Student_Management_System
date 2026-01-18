<?php
require_once('../includes/db_connect.php');

$action = $_GET['action'] ?? '';
$stu_id = $_GET['stu_id'] ?? 0;
$admin_id = $_GET['admin_id'] ?? 0;

if ($action == 'send_reminder') {
    // Fetch student email
    $res = mysqli_query($conn, "SELECT full_name, email FROM users WHERE id = '$stu_id'");
    $student = mysqli_fetch_assoc($res);

    // Simulation of Email Send
    // In a real server, you would use: mail($student['email'], "Fee Reminder", "Please pay your dues...");
    
    // Log the activity
    $action_text = "Sent fee payment reminder to student: " . $student['full_name'];
    mysqli_query($conn, "INSERT INTO activity_logs (user_id, action) VALUES ('$admin_id', '$action_text')");

    header("Location: ../views/fees_admin.php?user_id=$admin_id&status=success&msg=reminder_sent");
    exit();
}
?>