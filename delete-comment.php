<?php
// Start session
session_start();

// Include database connection
require_once 'database.php';

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header('Location: auth/login.php');
    exit;
}

// Check if comment_id and plant_id are provided
if (!isset($_POST['comment_id']) || !isset($_POST['plant_id'])) {
    header('Location: index.php');
    exit;
}

$commentId = intval($_POST['comment_id']);
$plantId = intval($_POST['plant_id']);
$userId = $_SESSION['user_id'];

// Delete the comment
$result = deleteComment($commentId, $userId);

// Redirect back to the plant detail page
header("Location: plant-detail.php?id=$plantId");
exit;
?>

