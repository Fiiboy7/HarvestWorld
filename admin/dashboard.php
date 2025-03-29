<?php
// Start session
session_start();

// Include the database file
require_once '../database.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  // Store the current URL to redirect back after login
  $_SESSION['redirect_after_login'] = "/admin/dashboard.php";
  header('Location: ../auth/login.php');
  exit;
}

// Get statistics
$plantCount = count(getPlantsData());
$categoryCount = count(getCategoryData());
$userCount = count(getAllUsers());
$articleCount = count(getAllArticles());

// Get user data
$user = getUserById($_SESSION['user_id']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Dashboard - HarvestWorld</title>
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
          
          <nav class="p-4">
              <p class="text-xs font-semibold text-gray-400 mb-2">MENU</p>
              <a href="dashboard.php" class="flex items-center py-2 px-4 bg-green-50 text-green-700 rounded-md mb-1">
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
              <a href="categories.php" class="flex items-center py-2 px-4 text-gray-700 hover:bg-gray-100 rounded-md mb-1">
                  <i class="fas fa-tags mr-3"></i>
                  Kategori
              </a>
              <a href="users.php" class="flex items-center py-2 px-4 text-gray-700 hover:bg-gray-100 rounded-md mb-1">
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
              <h1 class="text-xl font-semibold">Dashboard</h1>
              
              <div class="flex items-center">
                  <img 
                      src="<?php echo htmlspecialchars($user['profile_image'] ?? '/images/default-avatar.png'); ?>" 
                      alt="Profile" 
                      class="w-8 h-8 rounded-full object-cover border border-gray-200 mr-2"
                  >
                  <span class="text-gray-700 mr-2">Halo, <?php echo htmlspecialchars($user['full_name']); ?></span>
              </div>
          </div>
          
          <!-- Dashboard Content -->
          <div class="p-6">
              <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                  <!-- Total Plants Card -->
                  <div class="bg-white rounded-lg shadow-sm p-6">
                      <div class="flex items-center">
                          <div class="w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mr-4">
                              <i class="fas fa-leaf text-green-600 text-xl"></i>
                          </div>
                          <div>
                              <p class="text-gray-500 text-sm">Total Tanaman</p>
                              <h2 class="text-2xl font-bold"><?php echo $plantCount; ?></h2>
                          </div>
                      </div>
                  </div>
                  
                  <!-- Total Categories Card -->
                  <div class="bg-white rounded-lg shadow-sm p-6">
                      <div class="flex items-center">
                          <div class="w-12 h-12 bg-blue-100 rounded-full flex items-center justify-center mr-4">
                              <i class="fas fa-tags text-blue-600 text-xl"></i>
                          </div>
                          <div>
                              <p class="text-gray-500 text-sm">Total Kategori</p>
                              <h2 class="text-2xl font-bold"><?php echo $categoryCount; ?></h2>
                          </div>
                      </div>
                  </div>
                  
                  <!-- Total Articles Card -->
                  <div class="bg-white rounded-lg shadow-sm p-6">
                      <div class="flex items-center">
                          <div class="w-12 h-12 bg-purple-100 rounded-full flex items-center justify-center mr-4">
                              <i class="fas fa-newspaper text-purple-600 text-xl"></i>
                          </div>
                          <div>
                              <p class="text-gray-500 text-sm">Total Artikel</p>
                              <h2 class="text-2xl font-bold"><?php echo $articleCount; ?></h2>
                          </div>
                      </div>
                  </div>
                  
                  <!-- Total Users Card -->
                  <div class="bg-white rounded-lg shadow-sm p-6">
                      <div class="flex items-center">
                          <div class="w-12 h-12 bg-yellow-100 rounded-full flex items-center justify-center mr-4">
                              <i class="fas fa-users text-yellow-600 text-xl"></i>
                          </div>
                          <div>
                              <p class="text-gray-500 text-sm">Total Pengguna</p>
                              <h2 class="text-2xl font-bold"><?php echo $userCount; ?></h2>
                          </div>
                      </div>
                  </div>
              </div>
              
              <!-- Quick Actions Card -->
              <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                  <h3 class="font-semibold mb-4">Aksi Cepat</h3>
                  <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                      <div>
                          <h4 class="text-sm font-medium text-gray-500 mb-2">Tanaman</h4>
                          <div class="flex flex-col gap-2">
                              <a href="plants.php?action=add" class="text-green-600 hover:text-green-800">
                                  <i class="fas fa-plus-circle mr-2"></i> Tambah Tanaman Baru
                              </a>
                              <a href="plants.php" class="text-blue-600 hover:text-blue-800">
                                  <i class="fas fa-list mr-2"></i> Kelola Tanaman
                              </a>
                          </div>
                      </div>
                      <div>
                          <h4 class="text-sm font-medium text-gray-500 mb-2">Artikel</h4>
                          <div class="flex flex-col gap-2">
                              <a href="articles.php?action=add" class="text-green-600 hover:text-green-800">
                                  <i class="fas fa-plus-circle mr-2"></i> Tambah Artikel Baru
                              </a>
                              <a href="articles.php" class="text-blue-600 hover:text-blue-800">
                                  <i class="fas fa-list mr-2"></i> Kelola Artikel
                              </a>
                          </div>
                      </div>
                      <div>
                          <h4 class="text-sm font-medium text-gray-500 mb-2">Kategori</h4>
                          <div class="flex flex-col gap-2">
                              <a href="categories.php?action=add" class="text-green-600 hover:text-green-800">
                                  <i class="fas fa-plus-circle mr-2"></i> Tambah Kategori Baru
                              </a>
                              <a href="categories.php" class="text-blue-600 hover:text-blue-800">
                                  <i class="fas fa-list mr-2"></i> Kelola Kategori
                              </a>
                          </div>
                      </div>
                      <div>
                          <h4 class="text-sm font-medium text-gray-500 mb-2">Pengguna</h4>
                          <div class="flex flex-col gap-2">
                              <a href="users.php" class="text-blue-600 hover:text-blue-800">
                                  <i class="fas fa-list mr-2"></i> Kelola Pengguna
                              </a>
                              <a href="../index.php" target="_blank" class="text-gray-600 hover:text-gray-800">
                                  <i class="fas fa-external-link-alt mr-2"></i> Lihat Website
                              </a>
                          </div>
                      </div>
                  </div>
              </div>
              
              <!-- Recent Plants -->
              <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                  <div class="flex justify-between items-center mb-4">
                      <h3 class="font-semibold">Tanaman Terbaru</h3>
                      <a href="plants.php" class="text-sm text-green-600 hover:text-green-800">Lihat Semua</a>
                  </div>
                  
                  <div class="overflow-x-auto">
                      <table class="min-w-full divide-y divide-gray-200">
                          <thead>
                              <tr>
                                  <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nama</th>
                                  <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                                  <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kesulitan</th>
                                  <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                              </tr>
                          </thead>
                          <tbody class="bg-white divide-y divide-gray-200">
                              <?php 
                              $recentPlants = array_slice(getPlantsData(), 0, 5);
                              foreach ($recentPlants as $plant): 
                              ?>
                              <tr>
                                  <td class="px-6 py-4 whitespace-nowrap">
                                      <div class="flex items-center">
                                          <div class="flex-shrink-0 h-10 w-10">
                                              <img class="h-10 w-10 rounded-full object-cover" src="<?php echo htmlspecialchars($plant['image']); ?>" alt="">
                                          </div>
                                          <div class="ml-4">
                                              <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($plant['name']); ?></div>
                                              <div class="text-sm text-gray-500"><?php echo htmlspecialchars($plant['scientific_name']); ?></div>
                                          </div>
                                      </div>
                                  </td>
                                  <td class="px-6 py-4 whitespace-nowrap">
                                      <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                          <?php echo htmlspecialchars($plant['category']); ?>
                                      </span>
                                  </td>
                                  <td class="px-6 py-4 whitespace-nowrap">
                                      <span class="<?php 
                                          $bgColor = 'bg-green-100 text-green-800';
                                          if ($plant['difficulty'] === 'Sedang') {
                                              $bgColor = 'bg-yellow-100 text-yellow-800';
                                          } elseif ($plant['difficulty'] === 'Sulit') {
                                              $bgColor = 'bg-red-100 text-red-800';
                                          }
                                          echo $bgColor;
                                      ?> px-2 inline-flex text-xs leading-5 font-semibold rounded-full">
                                          <?php echo htmlspecialchars($plant['difficulty']); ?>
                                      </span>
                                  </td>
                                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                      <a href="plants.php?action=edit&id=<?php echo $plant['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                                          <i class="fas fa-edit"></i> Edit
                                      </a>
                                      <a href="../plant-detail.php?id=<?php echo $plant['id']; ?>" target="_blank" class="text-green-600 hover:text-green-900">
                                          <i class="fas fa-eye"></i> Lihat
                                      </a>
                                  </td>
                              </tr>
                              <?php endforeach; ?>
                          </tbody>
                      </table>
                  </div>
              </div>
              
              <!-- Recent Articles -->
              <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
                  <div class="flex justify-between items-center mb-4">
                      <h3 class="font-semibold">Artikel Terbaru</h3>
                      <a href="articles.php" class="text-sm text-green-600 hover:text-green-800">Lihat Semua</a>
                  </div>
                  
                  <div class="overflow-x-auto">
                      <table class="min-w-full divide-y divide-gray-200">
                          <thead>
                              <tr>
                                  <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Judul</th>
                                  <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penulis</th>
                                  <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                                  <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                              </tr>
                          </thead>
                          <tbody class="bg-white divide-y divide-gray-200">
                              <?php 
                              $recentArticles = array_slice(getAllArticles(), 0, 5);
                              foreach ($recentArticles as $article): 
                              ?>
                              <tr>
                                  <td class="px-6 py-4 whitespace-nowrap">
                                      <div class="flex items-center">
                                          <div class="flex-shrink-0 h-10 w-10">
                                              <img class="h-10 w-10 rounded object-cover" src="<?php echo htmlspecialchars($article['image']); ?>" alt="">
                                          </div>
                                          <div class="ml-4">
                                              <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($article['title']); ?></div>
                                              <div class="text-sm text-gray-500"><?php echo htmlspecialchars(substr($article['excerpt'], 0, 50) . '...'); ?></div>
                                          </div>
                                      </div>
                                  </td>
                                  <td class="px-6 py-4 whitespace-nowrap">
                                      <div class="text-sm text-gray-900"><?php echo htmlspecialchars($article['author']); ?></div>
                                  </td>
                                  <td class="px-6 py-4 whitespace-nowrap">
                                      <div class="text-sm text-gray-500">
                                          <?php 
                                              $date = new DateTime($article['created_at']);
                                              echo $date->format('d M Y');
                                          ?>
                                      </div>
                                  </td>
                                  <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                      <a href="articles.php?action=edit&id=<?php echo $article['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                                          <i class="fas fa-edit"></i> Edit
                                      </a>
                                      <a href="../article-detail.php?id=<?php echo $article['id']; ?>" target="_blank" class="text-green-600 hover:text-green-900">
                                          <i class="fas fa-eye"></i> Lihat
                                      </a>
                                  </td>
                              </tr>
                              <?php endforeach; ?>
                          </tbody>
                      </table>
                  </div>
              </div>
              
              <!-- Recent Users -->
              <div class="bg-white rounded-lg shadow-sm p-6">
                  <div class="flex justify-between items-center mb-4">
                      <h3 class="font-semibold">Pengguna Terbaru</h3>
                      <a href="users.php" class="text-sm text-green-600 hover:text-green-800">Lihat Semua</a>
                  </div>
                  
                  <div class="overflow-x-auto">
                      <table class="min-w-full divide-y divide-gray-200">
                          <thead>
                              <tr>
                                  <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Pengguna</th>
                                  <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                  <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Role</th>
                                  <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                              </tr>
                          </thead>
                          <tbody class="bg-white divide-y divide-gray-200">
                              <?php 
                              // Get recent users
                              $recentUsers = getRecentUsers(5);
                              foreach ($recentUsers as $user): 
                              ?>
                              <tr>
                                  <td class="px-6 py-4 whitespace-nowrap">
                                      <div class="flex items-center">
                                          <div class="flex-shrink-0 h-10 w-10">
                                              <img class="h-10 w-10 rounded-full object-cover" src="<?php echo htmlspecialchars($user['profile_image'] ?? '/images/default-avatar.png'); ?>" alt="">
                                          </div>
                                          <div class="ml-4">
                                              <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($user['full_name']); ?></div>
                                              <div class="text-sm text-gray-500">@<?php echo htmlspecialchars($user['username']); ?></div>
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
                                      <a href="users.php?action=edit&id=<?php echo $user['id']; ?>" class="text-blue-600 hover:text-blue-900">
                                          <i class="fas fa-edit"></i> Edit
                                      </a>
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

