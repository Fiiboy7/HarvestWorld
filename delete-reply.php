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

// Check if reply ID and topic ID are provided
if (!isset($_GET['id']) || !isset($_GET['topic_id'])) {
    header('Location: forum.php');
    exit;
}

$replyId = intval($_GET['id']);
$topicId = intval($_GET['topic_id']);
$userId = $_SESSION['user_id'];

// Delete the reply
$result = deleteForumReply($replyId, $userId);

// Redirect back to the topic page
header("Location: topic.php?id=$topicId");
exit;
?>

