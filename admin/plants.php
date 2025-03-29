<?php
// Start session
session_start();

// Include the database file
require_once '../database.php';

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
  // Store the current URL to redirect back after login
  $_SESSION['redirect_after_login'] = "/admin/plants.php";
  header('Location: ../auth/login.php');
  exit;
}

// Get all categories for the form
$categories = getCategoryData();

// Initialize variables
$plant = [
  'id' => '',
  'name' => '',
  'scientific_name' => '',
  'category_id' => '',
  'image' => '',
  'difficulty' => 'Mudah',
  'growth_time' => '',
  'description' => '',
  'planting_guide' => '',
  'care_instructions' => '',
  'harvest_instructions' => ''
];
$error = '';
$success = '';

// Handle form submission for adding/editing plants
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Get form data
  $plantData = [
    'name' => $_POST['name'] ?? '',
    'scientific_name' => $_POST['scientific_name'] ?? '',
    'category_id' => $_POST['category_id'] ?? '',
    'difficulty' => $_POST['difficulty'] ?? 'Mudah',
    'growth_time' => $_POST['growth_time'] ?? '',
    'description' => $_POST['description'] ?? '',
    'planting_guide' => $_POST['planting_guide'] ?? '',
    'care_instructions' => $_POST['care_instructions'] ?? '',
    'harvest_instructions' => $_POST['harvest_instructions'] ?? ''
  ];
  
  // Validate required fields
  if (empty($plantData['name']) || empty($plantData['scientific_name']) || empty($plantData['category_id'])) {
    $error = 'Nama, nama ilmiah, dan kategori harus diisi';
  } else {
    // Handle image upload
    $plantData['image'] = $_POST['current_image'] ?? '';
    
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
      $uploadDir = '../uploads/plants/';
      
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
          $plantData['image'] = '/' . $uploadFile;
        } else {
          $error = 'Gagal mengunggah gambar';
        }
      }
    }
    
    if (empty($error)) {
      // Add or update plant
      if (isset($_POST['id']) && !empty($_POST['id'])) {
        // Update existing plant
        $result = updatePlant($_POST['id'], $plantData);
        if ($result) {
          $success = 'Tanaman berhasil diperbarui';
          // Refresh plant data
          $plant = getPlantById($_POST['id']);
        } else {
          $error = 'Gagal memperbarui tanaman';
        }
      } else {
        // Add new plant
        $result = addPlant($plantData);
        if ($result) {
          $success = 'Tanaman berhasil ditambahkan';
          // Redirect to plants list
          header('Location: plants.php');
          exit;
        } else {
          $error = 'Gagal menambahkan tanaman';
        }
      }
    }
  }
}

// Handle delete action
if (isset($_GET['action']) && $_GET['action'] === 'delete' && isset($_GET['id'])) {
  $plantId = intval($_GET['id']);
  $result = deletePlant($plantId);
  
  if ($result) {
    header('Location: plants.php?deleted=1');
    exit;
  } else {
    $error = 'Gagal menghapus tanaman';
  }
}

// Handle edit action
if (isset($_GET['action']) && $_GET['action'] === 'edit' && isset($_GET['id'])) {
  $plantId = intval($_GET['id']);
  $plantData = getPlantById($plantId);
  
  if ($plantData) {
    $plant = $plantData;
  } else {
    $error = 'Tanaman tidak ditemukan';
  }
}

// Get all plants for the list view
$plants = getPlantsData();

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
  $success = 'Tanaman berhasil dihapus';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kelola Tanaman - HarvestWorld Admin</title>
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
          <a href="plants.php" class="flex items-center py-2 px-4 bg-green-50 text-green-700 rounded-md mb-1">
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
        <h1 class="text-xl font-semibold">
          <?php echo $currentView === 'list' ? 'Kelola Tanaman' : ($plant['id'] ? 'Edit Tanaman' : 'Tambah Tanaman Baru'); ?>
        </h1>
        
        <?php if ($currentView === 'list'): ?>
          <a href="plants.php?action=add" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
            <i class="fas fa-plus mr-2"></i> Tambah Tanaman
          </a>
        <?php else: ?>
          <a href="plants.php" class="text-gray-600 hover:text-gray-900">
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
          <!-- Plant Form -->
          <div class="bg-white rounded-lg shadow-md p-6">
            <form method="POST" action="plants.php" enctype="multipart/form-data">
              <?php if ($plant['id']): ?>
                <input type="hidden" name="id" value="<?php echo htmlspecialchars($plant['id']); ?>">
              <?php endif; ?>
              
              <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                  <div class="mb-4">
                    <label for="name" class="block text-gray-700 font-medium mb-2">Nama Tanaman <span class="text-red-500">*</span></label>
                    <input 
                      type="text" 
                      id="name" 
                      name="name" 
                      class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                      value="<?php echo htmlspecialchars($plant['name']); ?>"
                      required
                    >
                  </div>
                  
                  <div class="mb-4">
                    <label for="scientific_name" class="block text-gray-700 font-medium mb-2">Nama Ilmiah <span class="text-red-500">*</span></label>
                    <input 
                      type="text" 
                      id="scientific_name" 
                      name="scientific_name" 
                      class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                      value="<?php echo htmlspecialchars($plant['scientific_name']); ?>"
                      required
                    >
                  </div>
                  
                  <div class="mb-4">
                    <label for="category_id" class="block text-gray-700 font-medium mb-2">Kategori <span class="text-red-500">*</span></label>
                    <select 
                      id="category_id" 
                      name="category_id" 
                      class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                      required
                    >
                      <option value="">Pilih Kategori</option>
                      <?php foreach ($categories as $id => $category): ?>
                        <option value="<?php echo $id; ?>" <?php echo $plant['category_id'] == $id ? 'selected' : ''; ?>>
                          <?php echo htmlspecialchars($category['name']); ?>
                        </option>
                      <?php endforeach; ?>
                    </select>
                  </div>
                  
                  <div class="mb-4">
                    <label for="difficulty" class="block text-gray-700 font-medium mb-2">Tingkat Kesulitan</label>
                    <select 
                      id="difficulty" 
                      name="difficulty" 
                      class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                    >
                      <option value="Mudah" <?php echo $plant['difficulty'] === 'Mudah' ? 'selected' : ''; ?>>Mudah</option>
                      <option value="Sedang" <?php echo $plant['difficulty'] === 'Sedang' ? 'selected' : ''; ?>>Sedang</option>
                      <option value="Sulit" <?php echo $plant['difficulty'] === 'Sulit' ? 'selected' : ''; ?>>Sulit</option>
                    </select>
                  </div>
                  
                  <div class="mb-4">
                    <label for="growth_time" class="block text-gray-700 font-medium mb-2">Waktu Panen</label>
                    <input 
                      type="text" 
                      id="growth_time" 
                      name="growth_time" 
                      class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                      value="<?php echo htmlspecialchars($plant['growth_time']); ?>"
                      placeholder="Contoh: 3-4 bulan"
                    >
                  </div>
                  
                  <div class="mb-4">
                    <label for="image" class="block text-gray-700 font-medium mb-2">Gambar Tanaman</label>
                    <?php if (!empty($plant['image'])): ?>
                      <div class="mb-2">
                        <img 
                          src="<?php echo htmlspecialchars($plant['image']); ?>" 
                          alt="<?php echo htmlspecialchars($plant['name']); ?>" 
                          class="w-32 h-32 object-cover rounded-md"
                        >
                        <input type="hidden" name="current_image" value="<?php echo htmlspecialchars($plant['image']); ?>">
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
                </div>
                
                <div>
                  <div class="mb-4">
                    <label for="description" class="block text-gray-700 font-medium mb-2">Deskripsi</label>
                    <textarea 
                      id="description" 
                      name="description" 
                      rows="4" 
                      class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                    ><?php echo htmlspecialchars($plant['description']); ?></textarea>
                  </div>
                  
                  <div class="mb-4">
                    <label for="planting_guide" class="block text-gray-700 font-medium mb-2">Panduan Penanaman</label>
                    <textarea 
                      id="planting_guide" 
                      name="planting_guide" 
                      rows="4" 
                      class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                    ><?php echo htmlspecialchars($plant['planting_guide']); ?></textarea>
                  </div>
                  
                  <div class="mb-4">
                    <label for="care_instructions" class="block text-gray-700 font-medium mb-2">Perawatan</label>
                    <textarea 
                      id="care_instructions" 
                      name="care_instructions" 
                      rows="4" 
                      class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                    ><?php echo htmlspecialchars($plant['care_instructions']); ?></textarea>
                  </div>
                  
                  <div class="mb-4">
                    <label for="harvest_instructions" class="block text-gray-700 font-medium mb-2">Cara Panen</label>
                    <textarea 
                      id="harvest_instructions" 
                      name="harvest_instructions" 
                      rows="4" 
                      class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                    ><?php echo htmlspecialchars($plant['harvest_instructions']); ?></textarea>
                  </div>
                </div>
              </div>
              
              <div class="mt-6 flex justify-end">
                <a href="plants.php" class="bg-gray-300 text-gray-700 px-4 py-2 rounded-md hover:bg-gray-400 mr-2">
                  Batal
                </a>
                <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700">
                  <?php echo $plant['id'] ? 'Perbarui Tanaman' : 'Tambah Tanaman'; ?>
                </button>
              </div>
            </form>
          </div>
        <?php else: ?>
          <!-- Plants List -->
          <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="overflow-x-auto">
              <table class="min-w-full divide-y divide-gray-200">
                <thead>
                  <tr>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tanaman</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kategori</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Kesulitan</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Waktu Panen</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aksi</th>
                  </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                  <?php if (count($plants) > 0): ?>
                    <?php foreach ($plants as $plant): ?>
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
                          <?php echo htmlspecialchars($plant['growth_time']); ?>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                          <a href="plants.php?action=edit&id=<?php echo $plant['id']; ?>" class="text-blue-600 hover:text-blue-900 mr-3">
                            <i class="fas fa-edit"></i> Edit
                          </a>
                          <a href="../plant-detail.php?id=<?php echo $plant['id']; ?>" target="_blank" class="text-green-600 hover:text-green-900 mr-3">
                            <i class="fas fa-eye"></i> Lihat
                          </a>
                          <a href="plants.php?action=delete&id=<?php echo $plant['id']; ?>" class="text-red-600 hover:text-red-900" onclick="return confirm('Apakah Anda yakin ingin menghapus tanaman ini?');">
                            <i class="fas fa-trash"></i> Hapus
                          </a>
                        </td>
                      </tr>
                    <?php endforeach; ?>
                  <?php else: ?>
                    <tr>
                      <td colspan="5" class="px-6 py-4 text-center text-gray-500">
                        Belum ada tanaman. <a href="plants.php?action=add" class="text-blue-600 hover:underline">Tambah tanaman baru</a>.
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

