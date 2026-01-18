<?php
function logActivity($conn, $user_id, $action) {
    $action = mysqli_real_escape_string($conn, $action);
    mysqli_query($conn, "INSERT INTO activity_logs (user_id, action) VALUES ('$user_id', '$action')");
}
?>