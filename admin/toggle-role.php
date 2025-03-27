<?php
// Start session
session_start();

// Include database connection
require_once '../database.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header('Location: ../auth/login.php');
    exit;
}

// Check if user ID is provided
if (!isset($_GET['id'])) {
    header('Location: users.php');
    exit;
}

$userId = intval($_GET['id']);

// Don't allow changing own role
if ($userId === intval($_SESSION['user_id'])) {
    header('Location: users.php?error=cannot_change_own_role');
    exit;
}

// Get current user role
$user = getUserById($userId);

if (!$user) {
    header('Location: users.php?error=user_not_found');
    exit;
}

// Toggle role
$newRole = $user['role'] === 'admin' ? 'user' : 'admin';

// Update user role
$conn = getDbConnection();
$sql = "UPDATE users SET role = :role WHERE id = :id";
$stmt = $conn->prepare($sql);
$result = $stmt->execute([
    ':id' => $userId,
    ':role' => $newRole
]);

if ($result) {
    header('Location: users.php?success=role_updated');
} else {
    header('Location: users.php?error=update_failed');
}
exit;
?>

