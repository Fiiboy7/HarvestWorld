<?php
// Start session
session_start();

// Include the database file
require_once '../database.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    // Store the current URL to redirect back after login
    $_SESSION['redirect_after_login'] = "/admin/users.php";
    header('Location: ../auth/login.php');
    exit;
}

// Get all users
$users = getAllUsers();

// Function to get all users (add this to database.php)
function getAllUsers() {
    $conn = getDbConnection();
    
    $sql = "SELECT id, username, email, full_name, role, profile_image, created_at, last_active
            FROM users 
            ORDER BY created_at DESC";
    
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    
    return $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pengguna - HarvestWorld Admin</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <!-- Admin Dashboard -->
    <div class="flex h-screen bg-gray-100">
        <!-- Sidebar -->
        <div class="w-64 bg-white shadow-md">
            <div class="p-4 border-b">
                <div class="flex items-center">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-seedling text-green-600"></i>
                    </div>
                    <span class="text-green-600 font-bold text-xl">HarvestWorld</span>
                </div>
            </div>
            
            <!-- Update the sidebar navigation -->
            <nav class="p-4">
                <p class="text-xs font-semibold text-gray-400 mb-2">MENU</p>
                <a href="dashboard.php" class="flex items-center py-2 px-4 text-gray-700 hover:bg-gray-100 rounded-md mb-1">
                    <i class="fas fa-tachometer-alt mr-3"></i>
                    Dashboard
                </a>
                <a href="plants.php" class="flex items-center py-2 px-4 text-gray-700 hover:bg-gray-100 rounded-md mb-1">
                    <i class="fas fa-leaf mr-3"></i>
                    Tanaman
                </a>
                <a href="articles.php" class="flex items-center py-2 px-4 text-gray-700 hover:bg-gray-100 rounded-md mb-1">
                    <i class="fas fa-newspaper mr-3"></i>
                    Artikel
                </a>
                <a href="users.php" class="flex items-center py-2 px-4 bg-green-50 text-green-700 rounded-md mb-1">
                    <i class="fas fa-users mr-3"></i>
                    Pengguna
                </a>
                
                <div class="border-t border-gray-200 my-4"></div>
                
                <a href="../index.php" class="flex items-center py-2 px-4 text-gray-700 hover:bg-gray-100 rounded-md mb-1">
                    <i class="fas fa-home mr-3"></i>
                    Kembali ke Website
                </a>
                <a href="../auth/logout.php" class="flex items-center py-2 px-4 text-red-600 hover:bg-red-50 rounded-md">
                    <i class="fas fa-sign-out-alt mr-3"></i>
                    Logout
                </a>
            </nav>
        </div>
        
        <!-- Main Content -->
        <div class="flex-1 overflow-auto">
            <!-- Top Bar -->
            <div class="bg-white shadow-sm p-4 flex justify-between items-center">
                <h1 class="text-xl font-semibold">Kelola Pengguna</h1>
            </div>
            
            <!-- Users Content -->
            <div class="p-6">
                <div class="bg-white rounded-lg shadow-sm p-6">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold">Daftar Pengguna</h3>
                    </div>
                    
                    <div class="overflow-x-auto">
                        <!-- Update the users table to show last active instead of action -->
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead>
                                <tr>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">User</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal Bergabung</th>
                                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Terakhir Aktif</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <?php foreach ($users as $user): ?>
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <img class="h-10 w-10 rounded-full object-cover" src="<?php echo htmlspecialchars($user['profile_image'] ?? '/images/default-avatar.png'); ?>" alt="">
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($user['username']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?php echo htmlspecialchars($user['email']); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="<?php echo $user['role'] === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'; ?> px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                            <?php echo htmlspecialchars(ucfirst($user['role'])); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php 
                                            $date = new DateTime($user['created_at']);
                                            echo $date->format('d M Y, H:i');
                                        ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php 
                                            $lastActiveDate = new DateTime($user['last_active'] ?? $user['created_at']);
                                            echo $lastActiveDate->format('d M Y, H:i');
                                        ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>

