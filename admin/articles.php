<?php
// Start session
session_start();

// Include the database file
require_once '../database.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  // Store the current URL to redirect back after login
  $_SESSION['redirect_after_login'] = "/admin/articles.php";
  header('Location: ../auth/login.php');
  exit;
}

// Initialize variables
$article = [
  'id' => '',
  'title' => '',
  'excerpt' => '',
  'content' => '',
  'image' => '',
  'author' => $_SESSION['full_name'],
  'created_at' => date('Y-m-d H:i:s')
];
$error = '';
$success = '';

// Handle form submission for adding/editing articles
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Get form data
  $articleData = [
    'title' => $_POST['title'] ?? '',
    'excerpt' => $_POST['excerpt'] ?? '',
    'content' => $_POST['content'] ?? '',
    'author' => $_POST['author'] ?? $_SESSION['full_name']
  ];
  
  // Validate required fields
  if (empty($articleData['title']) || empty($articleData['content'])) {
    $error = 'Judul dan konten artikel harus diisi';
  } else {
    // Handle image upload
    $articleData['image'] = $_POST['current_image'] ?? '';
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
      $uploadDir = '../uploads/articles/';
      
      // Create directory if it doesn't exist
      if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0777, true);
      }
      
      $fileName = time() . '_' . basename($_FILES['image']['name']);
      $uploadFile = $uploadDir . $fileName;
      
      // Check if file is an image
      $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));
      $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
      
      if (!in_array($imageFileType, $allowedExtensions)) {
        $error = 'Hanya file JPG, JPEG, PNG, dan GIF yang diperbolehkan';
      } elseif ($_FILES['image']['size'] > 5000000) { // 5MB max
        $error = 'Ukuran file terlalu besar (maksimal 5MB)';
      } else {
        if (move_uploaded_file($_FILES['image']['tmp_name'], $uploadFile)) {
          $articleData['image'] = '/' . $uploadFile;
        } else {
          $error = 'Gagal mengunggah gambar';
        }
      }
    }
    
    // Generate excerpt if not provided
    if (empty($articleData['excerpt'])) {
      $articleData['excerpt'] = substr(strip_tags($articleData['content']), 0, 150) . '...';
    }
    
    if (empty($error)) {
      // Add or update article
      if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Update existing article
        $result = updateArticle($_POST['id'], $articleData);
        if ($result) {
          $success = 'Artikel berhasil diperbarui';
          // Refresh article data
          $article = getArticleById($_POST['id']);
        } else {
          $error = 'Gagal memperbarui artikel';
        }
      } else {
        // Add new article
        $result = addArticle($articleData);
        if ($result) {
          $success = 'Artikel berhasil ditambahkan';
          // Redirect to articles list
          header('Location: articles.php');
          exit;
        } else {
          $error = 'Gagal menambahkan artikel';
        }
      }
    }
  }
}

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
  $articleId = intval($_GET['id']);
  $result = deleteArticle($articleId);
  
  if ($result) {
    header('Location: articles.php?deleted=1');
    exit;
  } else {
    $error = 'Gagal menghapus artikel';
  }
}

// Handle edit action
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
  $articleId = intval($_GET['id']);
  $articleData = getArticleById($articleId);
  
  if ($articleData) {
    $article = $articleData;
  } else {
    $error = 'Artikel tidak ditemukan';
  }
}

// Get all articles for the list view
$articles = getAllArticles();

// Determine current view (list, add, edit)
$currentView = 'list';
if (isset($_GET['action'])) {
  if ($_GET['action'] === 'add') {
    $currentView = 'form';
  } elseif ($_GET['action'] === 'edit' && isset($_GET['id'])) {
    $currentView = 'form';
  }
}

// Check for success message from redirect
if (isset($_GET['deleted']) && $_GET['deleted'] === '1') {
  $success = 'Artikel berhasil dihapus';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Artikel - HarvestWorld Admin</title>
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
          <a href="dashboard.php" class="flex items-center py-2 px-4 text-gray-700 hover:bg-gray-100 rounded-md mb-1">
              <i class="fas fa-tachometer-alt mr-3"></i>
              Dashboard
          </a>
          <a href="plants.php" class="flex items-center py-2 px-4 text-gray-700 hover:bg-gray-100 rounded-md mb-1">
              <i class="fas fa-leaf mr-3"></i>
              Tanaman
          </a>
          <a href="articles.php" class="flex items-center py-2 px-4 bg-green-50 text-green-700 rounded-md mb-1">
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
        <h1 class="text-xl font-semibold">
          <?php echo $currentView === 'list' ? 'Kelola Artikel' : ($article['id'] ? 'Edit Artikel' : 'Tambah Artikel Baru'); ?>
        </h1>
        
        <?php if ($currentView === 'list'): ?>
          <a href="articles.php?action=add" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
            <i class="fas fa-plus mr-2"></i> Tambah Artikel
          </a>
        <?php else: ?>
          <a href="articles.php" class="text-gray-600 hover:text-gray-900">
            <i class="fas fa-arrow-left mr-2"></i> Kembali ke Daftar
          </a>
        <?php endif; ?>
      </div>
      
      <!-- Content -->
      <div class="p-6">
        <?php if (!empty($error)): ?>
          <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6" role="alert">
            <p><?php echo htmlspecialchars($error); ?></p>
          </div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
          <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6" role="alert">
            <p><?php echo htmlspecialchars($success); ?></p>
          </div>
        <?php endif; ?>
        
        <?php if ($currentView === 'form'): ?>
          <!-- Article Form -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <form method="POST" action="articles.php" enctype="multipart/form-data">
              <?php if ($article['id']): ?>
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($article['id']); ?>">
              <?php endif; ?>
              
              <div class="mb-4">
                <label for="title" class="block text-gray-700 font-medium mb-2">Judul Artikel <span class="text-red-500">*</span></label>
                <input 
                  type="text" 
                  id="title" 
                  name="title" 
                  class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                  value="<?php echo htmlspecialchars($article['title']); ?>"
                  required
                >
              </div>
              
              <div class="mb-4">
                <label for="author" class="block text-gray-700 font-medium mb-2">Penulis</label>
                <input 
                  type="text" 
                  id="author" 
                  name="author" 
                  class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                  value="<?php echo htmlspecialchars($article['author']); ?>"
                >
              </div>
              
              <div class="mb-4">
                <label for="excerpt" class="block text-gray-700 font-medium mb-2">Ringkasan (Opsional)</label>
                <textarea 
                  id="excerpt" 
                  name="excerpt" 
                  rows="3" 
                  class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                  placeholder="Ringkasan singkat artikel (jika kosong akan diambil dari konten)"
                ><?php echo htmlspecialchars($article['excerpt']); ?></textarea>
                <p class="text-sm text-gray-500 mt-1">Jika kosong, ringkasan akan diambil otomatis dari konten artikel.</p>
              </div>
              
              <div class="mb-4">
                <label for="image" class="block text-gray-700 font-medium mb-2">Gambar Artikel</label>
                <?php if (!empty($article['image'])): ?>
                  <div class="mb-2">
                    <img 
                      src="<?php echo htmlspecialchars($article['image']); ?>" 
                      alt="<?php echo htmlspecialchars($article['title']); ?>" 
                      class="w-32 h-32 object-cover rounded-md"
                    >
                    <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($article['image']); ?>">
                  </div>
                <?php endif; ?>
                <input 
                  type="file" 
                  id="image" 
                  name="image" 
                  class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                  accept="image/*"
                >
                <p class="text-sm text-gray-500 mt-1">Format: JPG, PNG, GIF. Maks 5MB.</p>
              </div>
              
              <div class="mb-4">
                <label for="content" class="block text-gray-700 font-medium mb-2">Konten Artikel <span class="text-red-500">*</span></label>
                <textarea 
                  id="content" 
                  name="content" 
                  rows="15" 
                  class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                  required
                ><?php echo htmlspecialchars($article['content']); ?></textarea>
              </div>
              
              <div class="mt-6 flex justify-end">
                <a href="articles.php" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 mr-2">
                  Batal
                </a>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                  <?php echo $article['id'] ? 'Perbarui Artikel' : 'Tambah Artikel'; ?>
                </button>
              </div>
            </form>
          </div>
        <?php else: ?>
          <!-- Articles List -->
          <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead>
                  <tr>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Artikel</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Penulis</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanggal</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <?php if (count($articles) > 0): ?>
                    <?php foreach ($articles as $article): ?>
                      <tr>
                        <td class="px-6 py-4">
                          <div class="flex items-center">
                            <div class="flex-shrink-0 h-10 w-10">
                              <img class="h-10 w-10 rounded object-cover" src="<?php echo htmlspecialchars($article['image']); ?>" alt="">
                            </div>
                            <div class="ml-4">
                              <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($article['title']); ?></div>
                              <div class="text-sm text-gray-500"><?php echo htmlspecialchars(substr($article['excerpt'], 0, 100) . '...'); ?></div>
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
                          <a href="../article-detail.php?id=<?php echo $article['id']; ?>" target="_blank" class="text-green-600 hover:text-green-900 mr-3">
                            <i class="fas fa-eye"></i> Lihat
                          </a>
                          <a href="articles.php?action=delete&id=<?php echo $article['id']; ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Apakah Anda yakin ingin menghapus artikel ini?');">
                            <i class="fas fa-trash"></i> Hapus
                          </a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="4" class="px-6 py-4 text-center text-gray-500">
                        Belum ada artikel. <a href="articles.php?action=add" class="text-blue-600 hover:underline">Tambah artikel baru</a>.
                      </td>
                    </tr>
                  <?php endif; ?>
                </tbody>
              </table>
            </div>
          </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</body>
</html>

