<?php
require_once('../includes/db_connect.php');

if (isset($_POST['title'])) {
    $faculty_id = $_GET['user_id'];
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $course = mysqli_real_escape_string($conn, $_POST['course']);
    
    // Fetch faculty name for the 'uploaded_by' field
    $faculty_res = mysqli_query($conn, "SELECT full_name FROM users WHERE id = '$faculty_id'");
    $faculty = mysqli_fetch_assoc($faculty_res);
    $uploaded_by = $faculty['full_name'];

    // File Upload Configuration
    $target_dir = "../uploads/resources/";
    
    // Create directory if it doesn't exist
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $file_name = time() . "_" . basename($_FILES["material"]["name"]);
    $target_file = $target_dir . $file_name;
    $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Security Check: Only allow PDF, DOC, and PPT
    $allowed_types = array("pdf", "doc", "docx", "ppt", "pptx");

    if (in_array($file_type, $allowed_types)) {
        if (move_uploaded_file($_FILES["material"]["tmp_name"], $target_file)) {
            // Save relative path for the database
            $db_path = "uploads/resources/" . $file_name;
            
            $sql = "INSERT INTO resources (title, file_path, course, uploaded_by) 
                    VALUES ('$title', '$db_path', '$course', '$uploaded_by')";
            
            if (mysqli_query($conn, $sql)) {
                header("Location: ../views/faculty_dashboard.php?user_id=$faculty_id&status=success");
            }
        } else {
            echo "Error uploading file.";
        }
    } else {
        echo "Invalid file format. Only PDF, DOC, and PPT allowed.";
    }
}
?>