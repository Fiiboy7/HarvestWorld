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

// Get user data
$userId = $_SESSION['user_id'];
$user = getUserById($userId);

// Handle profile update
$error = '';
$success = '';

// Update the profile update handling section to include username
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
  $fullName = $_POST['full_name'] ?? '';
  $username = $_POST['username'] ?? '';
  $bio = $_POST['bio'] ?? '';
  $location = $_POST['location'] ?? '';
  
  if (empty($fullName) || empty($username)) {
      $error = 'Nama lengkap dan username tidak boleh kosong';
  } else {
      $userData = [
          'full_name' => $fullName,
          'username' => $username,
          'bio' => $bio,
          'location' => $location
      ];
      
      // Handle profile image upload
      if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === UPLOAD_ERR_OK) {
          $uploadDir = 'uploads/profiles/';
          
          // Create directory if it doesn't exist
          if (!file_exists($uploadDir)) {
              mkdir($uploadDir, 0777, true);
          }
          
          $fileName = $userId . '_' . time() . '_' . basename($_FILES['profile_image']['name']);
          $uploadFile = $uploadDir . $fileName;
          
          // Check if file is an image
          $imageFileType = strtolower(pathinfo($uploadFile, PATHINFO_EXTENSION));
          $allowedExtensions = ['jpg', 'jpeg', 'png', 'gif'];
          
          if (!in_array($imageFileType, $allowedExtensions)) {
              $error = 'Hanya file JPG, JPEG, PNG, dan GIF yang diperbolehkan';
          } elseif ($_FILES['profile_image']['size'] > 2000000) { // 2MB max
              $error = 'Ukuran file terlalu besar (maksimal 2MB)';
          } else {
              if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $uploadFile)) {
                  $userData['profile_image'] = '/' . $uploadFile;
              } else {
                  $error = 'Gagal mengunggah gambar profil';
              }
          }
      }
      
      if (empty($error)) {
          $result = updateUserProfile($userId, $userData);
          
          if ($result) {
              $success = 'Profil berhasil diperbarui';
              
              // Check if username was changed
              if ($username !== $_SESSION['username']) {
                  // Update session data
                  $_SESSION['username'] = $username;
                  $success .= '. Username telah diubah - silakan login kembali';
                  
                  // Log the user out after 2 seconds to refresh their session
                  echo '<meta http-equiv="refresh" content="2;url=auth/logout.php">';
              } else {
                  // Update other session data
                  $_SESSION['full_name'] = $fullName;
                  if (isset($userData['profile_image'])) {
                      $_SESSION['profile_image'] = $userData['profile_image'];
                  }
              }
              
              // Refresh user data
              $user = getUserById($userId);
          } else {
              $error = 'Gagal memperbarui profil. Username mungkin sudah digunakan.';
          }
      }
  }
}

// Handle password change
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $currentPassword = $_POST['current_password'] ?? '';
    $newPassword = $_POST['new_password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';
    
    if (empty($currentPassword) || empty($newPassword) || empty($confirmPassword)) {
        $error = 'Semua field password harus diisi';
    } elseif ($newPassword !== $confirmPassword) {
        $error = 'Password baru dan konfirmasi password tidak cocok';
    } elseif (strlen($newPassword) < 6) {
        $error = 'Password baru harus minimal 6 karakter';
    } else {
        // Verify current password
        $loginResult = loginUser($user['username'], $currentPassword);
        
        if (!$loginResult['success']) {
            $error = 'Password saat ini salah';
        } else {
            $userData = [
                'password' => $newPassword
            ];
            
            $result = updateUserProfile($userId, $userData);
            
            if ($result) {
                $success = 'Password berhasil diubah';
            } else {
                $error = 'Gagal mengubah password';
            }
        }
    }
}

// Get active tab
$activeTab = isset($_GET['tab']) ? $_GET['tab'] : 'profile';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profil Saya - HarvestWorld</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include 'includes/header.php'; ?>
    
    <div class="container mx-auto py-12 px-4">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="md:flex">
                <!-- Sidebar -->
                <div class="md:w-1/4 bg-gray-50 p-6 border-r border-gray-200">
                    <div class="flex flex-col items-center mb-6">
                        <img 
                            src="<?php echo htmlspecialchars($user['profile_image'] ?? '/images/default-avatar.png'); ?>" 
                            alt="<?php echo htmlspecialchars($user['username']); ?>" 
                            class="w-32 h-32 rounded-full object-cover border-4 border-white shadow-md"
                        >
                        <h2 class="text-xl font-bold mt-4"><?php echo htmlspecialchars($user['full_name']); ?></h2>
                        <p class="text-gray-500">@<?php echo htmlspecialchars($user['username']); ?></p>
                    </div>
                    
                    <nav>
                        <a 
                            href="profile.php" 
                            class="flex items-center py-3 px-4 rounded-md <?php echo $activeTab === 'profile' ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-100'; ?>"
                        >
                            <i class="fas fa-user mr-3"></i> Profil Saya
                        </a>
                        <a 
                            href="profile.php?tab=settings" 
                            class="flex items-center py-3 px-4 rounded-md <?php echo $activeTab === 'settings' ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-100'; ?>"
                        >
                            <i class="fas fa-cog mr-3"></i> Pengaturan
                        </a>
                        <a 
                            href="profile.php?tab=security" 
                            class="flex items-center py-3 px-4 rounded-md <?php echo $activeTab === 'security' ? 'bg-green-50 text-green-700' : 'text-gray-700 hover:bg-gray-100'; ?>"
                        >
                            <i class="fas fa-lock mr-3"></i> Keamanan
                        </a>
                    </nav>
                </div>
                
                <!-- Main Content -->
                <div class="md:w-3/4 p-6">
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
                    
                    <?php if ($activeTab === 'profile'): ?>
                        <h1 class="text-2xl font-bold mb-6">Profil Saya</h1>
                        
                        <form method="POST" action="profile.php" enctype="multipart/form-data">
                            <div class="mb-6">
                                <label for="profile_image" class="block text-gray-700 font-medium mb-2">Foto Profil</label>
                                <div class="flex items-center">
                                    <img 
                                        src="<?php echo htmlspecialchars($user['profile_image'] ?? '/images/default-avatar.png'); ?>" 
                                        alt="Profile" 
                                        class="w-20 h-20 rounded-full object-cover mr-4"
                                        id="profile-preview"
                                    >
                                    <div>
                                        <input 
                                            type="file" 
                                            id="profile_image" 
                                            name="profile_image" 
                                            class="hidden"
                                            accept="image/*"
                                            onchange="previewImage(this)"
                                        >
                                        <label 
                                            for="profile_image" 
                                            class="bg-gray-200 text-gray-700 py-2 px-4 rounded-md hover:bg-gray-300 transition-colors cursor-pointer inline-block"
                                        >
                                            <i class="fas fa-upload mr-2"></i> Pilih Foto
                                        </label>
                                        <p class="text-sm text-gray-500 mt-1">JPG, PNG, atau GIF. Maks 2MB.</p>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="mb-4">
                                <label for="full_name" class="block text-gray-700 font-medium mb-2">Nama Lengkap</label>
                                <input 
                                    type="text" 
                                    id="full_name" 
                                    name="full_name" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                                    value="<?php echo htmlspecialchars($user['full_name']); ?>"
                                    required
                                >
                            </div>
                            
                            <div class="mb-4">
                                <label for="username" class="block text-gray-700 font-medium mb-2">Username</label>
                                <input 
                                    type="text" 
                                    id="username" 
                                    name="username" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                                    value="<?php echo htmlspecialchars($user['username']); ?>"
                                >
                                <p class="text-sm text-gray-500 mt-1">Mengubah username akan membuat Anda harus login ulang.</p>
                            </div>

                            <div class="mb-4">
                                <label for="bio" class="block text-gray-700 font-medium mb-2">Bio</label>
                                <textarea 
                                    id="bio" 
                                    name="bio" 
                                    rows="4" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                                ><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                            </div>
                            
                            <div class="mb-6">
                                <label for="location" class="block text-gray-700 font-medium mb-2">Lokasi</label>
                                <input 
                                    type="text" 
                                    id="location" 
                                    name="location" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                                    value="<?php echo htmlspecialchars($user['location'] ?? ''); ?>"
                                >
                            </div>
                            
                            <button 
                                type="submit" 
                                name="update_profile" 
                                class="bg-green-600 text-white py-2 px-6 rounded-md hover:bg-green-700 transition-colors"
                            >
                                Simpan Perubahan
                            </button>
                        </form>
                    <?php elseif ($activeTab === 'settings'): ?>
                        <h1 class="text-2xl font-bold mb-6">Pengaturan Akun</h1>
                        
                        <div class="mb-6">
                            <h2 class="text-lg font-semibold mb-2">Informasi Akun</h2>
                            <div class="bg-gray-50 p-4 rounded-md">
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-gray-600">Username</span>
                                    <span class="font-medium"><?php echo htmlspecialchars($user['username']); ?></span>
                                </div>
                                <div class="flex justify-between items-center mb-2">
                                    <span class="text-gray-600">Email</span>
                                    <span class="font-medium"><?php echo htmlspecialchars($user['email']); ?></span>
                                </div>
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-600">Tanggal Bergabung</span>
                                    <span class="font-medium">
                                        <?php 
                                            $date = new DateTime($user['created_at']);
                                            echo $date->format('d F Y');
                                        ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="border-t border-gray-200 pt-6">
                            <h2 class="text-lg font-semibold mb-4 text-red-600">Hapus Akun</h2>
                            <p class="text-gray-600 mb-4">
                                Menghapus akun Anda akan menghapus semua data Anda secara permanen. Tindakan ini tidak dapat dibatalkan.
                            </p>
                            <button 
                                type="button" 
                                class="bg-red-600 text-white py-2 px-6 rounded-md hover:bg-red-700 transition-colors"
                                onclick="confirm('Apakah Anda yakin ingin menghapus akun Anda? Tindakan ini tidak dapat dibatalkan.') && window.location.href='delete-account.php'"
                            >
                                Hapus Akun Saya
                            </button>
                        </div>
                    <?php elseif ($activeTab === 'security'): ?>
                        <h1 class="text-2xl font-bold mb-6">Keamanan</h1>
                        
                        <form method="POST" action="profile.php?tab=security">
                            <div class="mb-4">
                                <label for="current_password" class="block text-gray-700 font-medium mb-2">Password Saat Ini</label>
                                <input 
                                    type="password" 
                                    id="current_password" 
                                    name="current_password" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                                    required
                                >
                            </div>
                            
                            <div class="mb-4">
                                <label for="new_password" class="block text-gray-700 font-medium mb-2">Password Baru</label>
                                <input 
                                    type="password" 
                                    id="new_password" 
                                    name="new_password" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                                    required
                                >
                                <p class="text-sm text-gray-500 mt-1">Minimal 6 karakter</p>
                            </div>
                            
                            <div class="mb-6">
                                <label for="confirm_password" class="block text-gray-700 font-medium mb-2">Konfirmasi Password Baru</label>
                                <input 
                                    type="password" 
                                    id="confirm_password" 
                                    name="confirm_password" 
                                    class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                                    required
                                >
                            </div>
                            
                            <button 
                                type="submit" 
                                name="change_password" 
                                class="bg-green-600 text-white py-2 px-6 rounded-md hover:bg-green-700 transition-colors"
                            >
                                Ubah Password
                            </button>
                        </form>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
    
    <script>
        function previewImage(input) {
            if (input.files && input.files[0]) {
                var reader = new FileReader();
                
                reader.onload = function(e) {
                    document.getElementById('profile-preview').src = e.target.result;
                }
                
                reader.readAsDataURL(input.files[0]);
            }
        }
    </script>
</body>
</html>

