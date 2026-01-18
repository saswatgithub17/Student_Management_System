<?php
// A secret key for your institution
$secret_key = "EduFlow_India_2026_Secure";

// Function to generate a secure token for the URL
function generateToken($user_id) {
    global $secret_key;
    return hash_hmac('sha256', $user_id, $secret_key);
}

// Function to verify if the URL token is valid
function verifyToken($user_id, $token) {
    global $secret_key;
    $valid_token = hash_hmac('sha256', $user_id, $secret_key);
    if ($token !== $valid_token) {
        header("Location: ../login.php?error=unauthorized");
        exit();
    }
}
?>