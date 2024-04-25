<?php
/*
This file contains database configuration assuming you are running MySQL using user "root" and password ""
*/

define('DB_SERVER', 'localhost');
define('DB_USERNAME', 'root');
define('DB_PASSWORD', '');
define('DB_NAME', 'ticketdata');

// Try connecting to the Database
$conn = mysqli_connect(DB_SERVER, DB_USERNAME, DB_PASSWORD, DB_NAME);

// Check the connection
if ($conn === false) {
    die('Error: Cannot connect to the database');
}

// Function to insert a new password reset token into the database
function insertPasswordResetToken($email, $token) {
    global $conn;
    $sql = "INSERT INTO password_resets (email, token) VALUES (?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "ss", $email, $token);
    return mysqli_stmt_execute($stmt);
}

// Function to delete a password reset token from the database
function deletePasswordResetToken($token) {
    global $conn;
    $sql = "DELETE FROM password_resets WHERE token = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $token);
    return mysqli_stmt_execute($stmt);
}

// Function to check if a password reset token exists in the database
function isTokenValid($token) {
    global $conn;
    $sql = "SELECT id FROM password_resets WHERE token = ? AND created_at >= NOW() - INTERVAL 1 HOUR";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "s", $token);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);
    return mysqli_stmt_num_rows($stmt) == 1;
}
?>
