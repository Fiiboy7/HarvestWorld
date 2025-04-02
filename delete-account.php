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

// Get user ID
$userId = $_SESSION['user_id'];

// Handle account deletion
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['confirm_delete'])) {
    // Delete user's comments
    $conn = getDbConnection();
    $sql = "DELETE FROM comments WHERE user_id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([':user_id' => $userId]);
    
    // Delete user's forum topics and replies (if implemented)
    // ...
    
    // Finally, delete the user
    $sql = "DELETE FROM users WHERE id = :user_id";
    $stmt = $conn->prepare($sql);
    $result = $stmt->execute([':user_id' => $userId]);
    
    if ($result) {
        // Clear session and redirect to home page
        session_destroy();
        header('Location: index.php?account_deleted=1');
        exit;
    } else {
        $error = 'Gagal menghapus akun. Silakan coba lagi.';
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hapus Akun - HarvestWorld</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include 'includes/header.php'; ?>
    
    <div class="container mx-auto py-12 px-4">
        <div class="max-w-md mx-auto bg-white rounded-lg shadow-md overflow-hidden">
            <div class="p-6">
                <div class="text-center mb-6">
                    <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mb-4">
                        <i class="fas fa-exclamation-triangle text-red-600 text-2xl"></i>
                    </div>
                    <h1 class="text-2xl font-bold">Hapus Akun</h1>
                </div>
                
                <?php if (isset($error)): ?>
                    <div class="bg-red-100 text-red-700 p-3 rounded-md mb-4">
                        <?php echo htmlspecialchars($error); ?>
                    </div>
                <?php endif; ?>
                
                <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4 mb-6">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <i class="fas fa-exclamation-circle text-yellow-400"></i>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm text-yellow-700">
                                <strong>Peringatan:</strong> Menghapus akun Anda akan menghapus semua data Anda secara permanen. Tindakan ini tidak dapat dibatalkan.
                            </p>
                        </div>
                    </div>
                </div>
                
                <form method="POST" action="delete-account.php">
                    <div class="mb-6">
                        <label class="flex items-center">
                            <input 
                                type="checkbox" 
                                name="confirm" 
                                required 
                                class="form-checkbox h-5 w-5 text-red-600"
                            >
                            <span class="ml-2 text-gray-700">
                                Saya mengerti bahwa tindakan ini tidak dapat dibatalkan
                            </span>
                        </label>
                    </div>
                    
                    <div class="flex justify-between">
                        <a 
                            href="profile.php?tab=settings" 
                            class="bg-gray-200 text-gray-700 py-2 px-6 rounded-md hover:bg-gray-300 transition-colors"
                        >
                            Batal
                        </a>
                        <button 
                            type="submit" 
                            name="confirm_delete" 
                            class="bg-red-600 text-white py-2 px-6 rounded-md hover:bg-red-700 transition-colors"
                        >
                            Hapus Akun Saya
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>

