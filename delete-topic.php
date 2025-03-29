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

// Check if topic ID is provided
if (!isset($_GET['id'])) {
    header('Location: forum.php');
    exit;
}

$topicId = intval($_GET['id']);
$userId = $_SESSION['user_id'];

// Delete the topic
$result = deleteForumTopic($topicId, $userId);

// Redirect to the forum page
header('Location: forum.php');
exit;
?>

