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

// Initialize variables
$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category = $_POST['category'] ?? 'Diskusi';
    
    if (empty($title) || empty($content)) {
        $error = 'Judul dan konten harus diisi.';
    } else {
        // Create new topic
        $topicData = [
            'user_id' => $_SESSION['user_id'],
            'title' => $title,
            'content' => $content,
            'category' => $category
        ];
        
        $result = createForumTopic($topicData);
        
        if ($result) {
            $success = 'Topik berhasil dibuat.';
            // Redirect to the new topic
            header('Location: topic.php?id=' . $result);
            exit;
        } else {
            $error = 'Gagal membuat topik. Silakan coba lagi.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Buat Topik Baru - HarvestWorld</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include 'includes/header.php'; ?>
    
    <div class="container mx-auto py-12 px-4">
        <div class="flex items-center mb-8">
            <a href="forum.php" class="mr-4 text-gray-600 hover:text-gray-900">
                <i class="fas fa-arrow-left"></i> Kembali ke Forum
            </a>
            <h1 class="text-3xl font-bold">Buat Topik Baru</h1>
        </div>
        
        <div class="bg-white rounded-lg shadow-md p-6">
            <?php if (!empty($error)): ?>
                <div class="bg-red-100 text-red-700 p-4 rounded-md mb-6">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($success)): ?>
                <div class="bg-green-100 text-green-700 p-4 rounded-md mb-6">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST" action="create-topic.php">
                <div class="mb-4">
                    <label for="title" class="block text-gray-700 font-medium mb-2">Judul Topik</label>
                    <input 
                        type="text" 
                        id="title" 
                        name="title" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                        required
                        value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>"
                    >
                </div>
                
                <div class="mb-4">
                    <label for="category" class="block text-gray-700 font-medium mb-2">Kategori</label>
                    <select 
                        id="category" 
                        name="category" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                    >
                        <option value="Pertanyaan" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Pertanyaan') ? 'selected' : ''; ?>>Pertanyaan</option>
                        <option value="Diskusi" <?php echo (!isset($_POST['category']) || $_POST['category'] === 'Diskusi') ? 'selected' : ''; ?>>Diskusi</option>
                        <option value="Tips" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Tips') ? 'selected' : ''; ?>>Tips & Trik</option>
                        <option value="Pengalaman" <?php echo (isset($_POST['category']) && $_POST['category'] === 'Pengalaman') ? 'selected' : ''; ?>>Berbagi Pengalaman</option>
                    </select>
                </div>
                
                <div class="mb-6">
                    <label for="content" class="block text-gray-700 font-medium mb-2">Konten</label>
                    <textarea 
                        id="content" 
                        name="content" 
                        rows="10" 
                        class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                        required
                    ><?php echo isset($_POST['content']) ? htmlspecialchars($_POST['content']) : ''; ?></textarea>
                </div>
                
                <button 
                    type="submit" 
                    class="bg-green-600 text-white py-2 px-6 rounded-md hover:bg-green-700 transition-colors"
                >
                    Buat Topik
                </button>
            </form>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>

