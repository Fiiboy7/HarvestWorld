<?php
// Start session
session_start();

// Redirect to the dashboard if already logged in as admin
if (isset($_SESSION['user_id']) && $_SESSION['role'] === 'admin') {
    header('Location: dashboard.php');
    exit;
}

// Otherwise, redirect to the login page
$_SESSION['redirect_after_login'] = "/admin/dashboard.php";
header('Location: ../auth/login.php');
exit;
?>

