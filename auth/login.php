<?php
// Start session
session_start();

// Include database connection
require_once '../database.php';

// Check if user is already logged in
if (isset($_SESSION['user_id'])) {
    // Redirect based on role
    if ($_SESSION['role'] === 'admin') {
        header('Location: ../admin/dashboard.php');
    } else {
        header('Location: ../index.php');
    }
    exit;
}

$error = '';
$success = '';

// Handle login form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $_POST['username'] ?? '';
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Silakan isi semua field';
    } else {
        $result = loginUser($username, $password);
        
        if ($result['success']) {
            // Set session variables
            $_SESSION['user_id'] = $result['user']['id'];
            $_SESSION['username'] = $result['user']['username'];
            $_SESSION['full_name'] = $result['user']['full_name'];
            $_SESSION['role'] = $result['user']['role'];
            $_SESSION['profile_image'] = $result['user']['profile_image'];
            
            // Redirect based on role
            if ($_SESSION['role'] === 'admin') {
                header('Location: ../admin/dashboard.php');
            } else {
                // Redirect to home page or previous page if set
                $redirect = isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : '../index.php';
                unset($_SESSION['redirect_after_login']);
                header('Location: ' . $redirect);
            }
            exit;
        } else {
            $error = $result['message'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - HarvestWorld</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <div class="min-h-screen flex items-center justify-center">
        <div class="bg-white p-8 rounded-lg shadow-md w-full max-w-md">
            <div class="text-center mb-8">
                <a href="../index.php" class="inline-flex items-center justify-center">
                    <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-seedling text-green-600 text-xl"></i>
                    </div>
                    <span class="text-green-600 font-bold text-2xl">HarvestWorld</span>
                </a>
                <h1 class="text-2xl font-bold mt-4">Masuk ke Akun Anda</h1>
            </div>
            
            <?php if (!empty($error)): ?>
                <div class="bg-red-100 text-red-700 p-3 rounded-md mb-4">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="bg-green-100 text-green-700 p-3 rounded-md mb-4">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="login.php">
                <div class="mb-4">
                    <label for="username" class="block text-gray-700 font-medium mb-2">Username atau Email</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="username" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                        required
                    >
                </div>
                
                <div class="mb-6">
                    <label for="password" class="block text-gray-700 font-medium mb-2">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                        required
                    >
                </div>
                
                <button 
                    type="submit" 
                    class="w-full bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition-colors"
                >
                    Masuk
                </button>
            </form>
            
            <div class="mt-6 text-center">
                <p class="text-gray-600">
                    Belum punya akun? 
                    <a href="register.php" class="text-green-600 hover:text-green-800">Daftar sekarang</a>
                </p>
            </div>
            
            <div class="mt-4 pt-4 border-t border-gray-200 text-center text-sm text-gray-500">
                <p>Admin dapat login menggunakan kredensial admin mereka.</p>
            </div>
        </div>
    </div>
</body>
</html>

