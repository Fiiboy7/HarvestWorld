<?php
// Start session
session_start();

// Include the database file
require_once 'database.php';

// Get plant ID from URL parameter
$plantId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get plant data
$plant = getPlantById($plantId);

// If plant not found, redirect to plants page
if (!$plant) {
    header('Location: plants.php');
    exit;
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

// Handle comment submission
$commentError = '';
$commentSuccess = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_comment'])) {
    if (!$isLoggedIn) {
        // Store the current URL to redirect back after login
        $_SESSION['redirect_after_login'] = "plant-detail.php?id=$plantId";
        header('Location: auth/login.php');
        exit;
    }
    
    $comment = trim($_POST['comment'] ?? '');
    
    if (empty($comment)) {
        $commentError = 'Komentar tidak boleh kosong';
    } else {
        $commentData = [
            'plant_id' => $plantId,
            'user_id' => $_SESSION['user_id'],
            'comment' => $comment
        ];
        
        $result = addComment($commentData);
        
        if ($result) {
            $commentSuccess = 'Komentar berhasil ditambahkan';
            // Clear the comment form
            $_POST['comment'] = '';
        } else {
            $commentError = 'Gagal menambahkan komentar';
        }
    }
}

// Get comments for this plant
$comments = getCommentsByPlantId($plantId);

// Get category data
$categories = getCategoryData();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($plant['name']); ?> - HarvestWorld</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-50">
    <?php include 'includes/header.php'; ?>
    
    <div class="container mx-auto py-12 px-4">
        <!-- Back Button -->
        <a href="javascript:history.back()" class="inline-flex items-center gap-2 px-4 py-2 mb-6 text-gray-700 hover:text-gray-900">
            <i class="fas fa-arrow-left text-sm"></i>
            Kembali
        </a>

        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="md:flex">
                <!-- Plant Image -->
                <div class="md:w-1/3">
                    <div class="aspect-square">
                        <img 
                            src="<?php echo htmlspecialchars($plant['image']); ?>" 
                            alt="<?php echo htmlspecialchars($plant['name']); ?>" 
                            class="w-full h-full object-cover"
                        >
                    </div>
                </div>
                
                <!-- Plant Details -->
                <div class="md:w-2/3 p-6">
                    <h1 class="text-3xl font-bold mb-2"><?php echo htmlspecialchars($plant['name']); ?></h1>
                    <p class="text-gray-500 italic mb-4"><?php echo htmlspecialchars($plant['scientific_name']); ?></p>
                    
                    <div class="flex flex-wrap gap-2 mb-6">
                        <span class="<?php 
                            $bgColor = 'bg-emerald-100 text-emerald-800';
                            if ($plant['category'] === 'Buah-buahan') {
                                $bgColor = 'bg-orange-100 text-orange-800';
                            } elseif ($plant['category'] === 'Rempah-rempah') {
                                $bgColor = 'bg-amber-100 text-amber-800';
                            } elseif ($plant['category'] === 'Tanaman Hias') {
                                $bgColor = 'bg-purple-100 text-purple-800';
                            }
                            echo $bgColor;
                        ?> px-3 py-1 rounded-full text-sm">
                            <?php echo htmlspecialchars($plant['category']); ?>
                        </span>
                        <span class="<?php 
                            $bgColor = 'bg-green-100 text-green-800';
                            if ($plant['difficulty'] === 'Sedang') {
                                $bgColor = 'bg-yellow-100 text-yellow-800';
                            } elseif ($plant['difficulty'] === 'Sulit') {
                                $bgColor = 'bg-red-100 text-red-800';
                            }
                            echo $bgColor;
                        ?> px-3 py-1 rounded-full text-sm">
                            Tingkat kesulitan: <?php echo htmlspecialchars($plant['difficulty']); ?>
                        </span>
                        <span class="bg-blue-100 text-blue-800 px-3 py-1 rounded-full text-sm">
                            <i class="fas fa-clock mr-1"></i>
                            Waktu panen: <?php echo htmlspecialchars($plant['growth_time']); ?>
                        </span>
                    </div>
                    
                    <?php if (!empty($plant['description'])): ?>
                        <div class="mb-6">
                            <h2 class="text-xl font-semibold mb-2">Deskripsi</h2>
                            <p class="text-gray-700"><?php echo nl2br(htmlspecialchars($plant['description'])); ?></p>
                        </div>
                    <?php else: ?>
                        <div class="mb-6">
                            <h2 class="text-xl font-semibold mb-2">Deskripsi</h2>
                            <p class="text-gray-500 italic">Informasi deskripsi belum tersedia.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Planting Guide and Care Instructions -->
            <div class="border-t border-gray-200 p-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <div>
                        <h2 class="text-xl font-semibold mb-4">
                            <i class="fas fa-seedling text-green-600 mr-2"></i>
                            Panduan Penanaman
                        </h2>
                        <?php if (!empty($plant['planting_guide'])): ?>
                            <div class="text-gray-700">
                                <?php echo nl2br(htmlspecialchars($plant['planting_guide'])); ?>
                            </div>
                        <?php else: ?>
                            <p class="text-gray-500 italic">Panduan penanaman belum tersedia.</p>
                        <?php endif; ?>
                    </div>
                    
                    <div>
                        <h2 class="text-xl font-semibold mb-4">
                            <i class="fas fa-hand-holding-water text-blue-600 mr-2"></i>
                            Perawatan
                        </h2>
                        <?php if (!empty($plant['care_instructions'])): ?>
                            <div class="text-gray-700">
                                <?php echo nl2br(htmlspecialchars($plant['care_instructions'])); ?>
                            </div>
                        <?php else: ?>
                            <p class="text-gray-500 italic">Instruksi perawatan belum tersedia.</p>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
            
            <!-- Harvest Instructions -->
            <div class="border-t border-gray-200 p-6">
                <h2 class="text-xl font-semibold mb-4">
                    <i class="fas fa-leaf text-green-600 mr-2"></i>
                    Cara Panen
                </h2>
                <?php if (!empty($plant['harvest_instructions'])): ?>
                    <div class="text-gray-700">
                        <?php echo nl2br(htmlspecialchars($plant['harvest_instructions'])); ?>
                    </div>
                <?php else: ?>
                    <p class="text-gray-500 italic">Instruksi panen belum tersedia.</p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Comments Section -->
        <div class="mt-12">
            <h2 class="text-2xl font-bold mb-6">
                <i class="fas fa-comments text-green-600 mr-2"></i>
                Diskusi (<?php echo count($comments); ?>)
            </h2>
            
            <!-- Comment Form -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <h3 class="text-lg font-semibold mb-4">Tambahkan Komentar</h3>
                
                <?php if ($isLoggedIn): ?>
                    <?php if (!empty($commentError)): ?>
                        <div class="bg-red-100 text-red-700 p-3 rounded-md mb-4">
                            <?php echo htmlspecialchars($commentError); ?>
                        </div>
                    <?php endif; ?>
                    
                    <?php if (!empty($commentSuccess)): ?>
                        <div class="bg-green-100 text-green-700 p-3 rounded-md mb-4">
                            <?php echo htmlspecialchars($commentSuccess); ?>
                        </div>
                    <?php endif; ?>
                    
                    <form method="POST" action="plant-detail.php?id=<?php echo $plantId; ?>">
                        <div class="mb-4">
                            <textarea 
                                name="comment" 
                                rows="4" 
                                class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                                placeholder="Tulis komentar atau pertanyaan Anda..."
                                required
                            ><?php echo isset($_POST['comment']) ? htmlspecialchars($_POST['comment']) : ''; ?></textarea>
                        </div>
                        
                        <button 
                            type="submit" 
                            name="submit_comment" 
                            class="bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition-colors"
                        >
                            Kirim Komentar
                        </button>
                    </form>
                <?php else: ?>
                    <div class="bg-gray-100 p-4 rounded-md text-center">
                        <p class="text-gray-700 mb-3">Anda harus login untuk menambahkan komentar</p>
                        <a 
                            href="auth/login.php" 
                            class="inline-block bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition-colors"
                            onclick="sessionStorage.setItem('redirect_after_login', 'plant-detail.php?id=<?php echo $plantId; ?>');"
                        >
                            Login
                        </a>
                        <span class="mx-2 text-gray-500">atau</span>
                        <a 
                            href="auth/register.php" 
                            class="inline-block bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 transition-colors"
                        >
                            Daftar
                        </a>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Comments List -->
            <?php if (count($comments) > 0): ?>
                <div class="space-y-6">
                    <?php foreach ($comments as $comment): ?>
                        <div class="bg-white rounded-lg shadow-md p-6">
                            <div class="flex items-start">
                                <img 
                                    src="<?php echo htmlspecialchars($comment['profile_image'] ?? '/images/default-avatar.png'); ?>" 
                                    alt="<?php echo htmlspecialchars($comment['username']); ?>" 
                                    class="w-10 h-10 rounded-full object-cover mr-4"
                                >
                                <div class="flex-1">
                                    <div class="flex justify-between items-center mb-2">
                                        <div>
                                            <h4 class="font-semibold"><?php echo htmlspecialchars($comment['full_name']); ?></h4>
                                            <p class="text-sm text-gray-500">@<?php echo htmlspecialchars($comment['username']); ?></p>
                                        </div>
                                        <span class="text-sm text-gray-500">
                                            <?php 
                                                $commentDate = new DateTime($comment['created_at']);
                                                $now = new DateTime();
                                                $interval = $commentDate->diff($now);
                                                
                                                if ($interval->y > 0) {
                                                    echo $interval->y . ' tahun yang lalu';
                                                } elseif ($interval->m > 0) {
                                                    echo $interval->m . ' bulan yang lalu';
                                                } elseif ($interval->d > 0) {
                                                    echo $interval->d . ' hari yang lalu';
                                                } elseif ($interval->h > 0) {
                                                    echo $interval->h . ' jam yang lalu';
                                                } elseif ($interval->i > 0) {
                                                    echo $interval->i . ' menit yang lalu';
                                                } else {
                                                    echo 'Baru saja';
                                                }
                                            ?>
                                        </span>
                                    </div>
                                    <div class="text-gray-700">
                                        <?php echo nl2br(htmlspecialchars($comment['comment'])); ?>
                                    </div>
                                    
                                    <?php if ($isLoggedIn && ($_SESSION['user_id'] == $comment['user_id'] || $_SESSION['role'] === 'admin')): ?>
                                        <div class="mt-3 text-right">
                                            <form method="POST" action="delete-comment.php" class="inline">
                                                <input type="hidden" name="comment_id" value="<?php echo $comment['id']; ?>">
                                                <input type="hidden" name="plant_id" value="<?php echo $plantId; ?>">
                                                <button 
                                                    type="submit" 
                                                    class="text-red-600 text-sm hover:text-red-800"
                                                    onclick="return confirm('Apakah Anda yakin ingin menghapus komentar ini?');"
                                                >
                                                    <i class="fas fa-trash-alt mr-1"></i> Hapus
                                                </button>
                                            </form>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="bg-white rounded-lg shadow-md p-6 text-center">
                    <div class="text-gray-400 mb-4">
                        <i class="far fa-comment-dots text-6xl"></i>
                    </div>
                    <h3 class="text-xl font-semibold mb-2">Belum Ada Komentar</h3>
                    <p class="text-gray-500 mb-4">
                        Jadilah yang pertama memberikan komentar atau pertanyaan tentang tanaman ini.
                    </p>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Related Plants -->
        <?php
        $relatedPlants = getPlantsData(['category_id' => $plant['category_id']]);
        // Remove current plant from related plants
        foreach ($relatedPlants as $key => $relatedPlant) {
            if ($relatedPlant['id'] == $plantId) {
                unset($relatedPlants[$key]);
                break;
            }
        }
        
        // Limit to 4 related plants
        $relatedPlants = array_slice($relatedPlants, 0, 4);
        
        if (count($relatedPlants) > 0):
        ?>
        <div class="mt-12">
            <h2 class="text-2xl font-bold mb-6">Tanaman Terkait</h2>
            
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-6">
                <?php foreach ($relatedPlants as $relatedPlant): ?>
                    <a href="plant-detail.php?id=<?php echo $relatedPlant['id']; ?>" class="block">
                        <div class="bg-white rounded-lg shadow-md overflow-hidden transition-all hover:shadow-lg hover:-translate-y-1">
                            <div class="aspect-square overflow-hidden">
                                <img 
                                    src="<?php echo htmlspecialchars($relatedPlant['image']); ?>" 
                                    alt="<?php echo htmlspecialchars($relatedPlant['name']); ?>" 
                                    class="w-full h-full object-cover transition-transform hover:scale-105"
                                >
                            </div>
                            <div class="p-4">
                                <h3 class="font-semibold text-lg mb-1"><?php echo htmlspecialchars($relatedPlant['name']); ?></h3>
                                <p class="text-sm text-gray-500 italic"><?php echo htmlspecialchars($relatedPlant['scientific_name']); ?></p>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php include 'includes/footer.php'; ?>
    
    <!-- Login Modal (for non-logged in users) -->
    <?php if (!$isLoggedIn): ?>
    <div id="login-modal" class="fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center hidden">
        <div class="bg-white rounded-lg shadow-xl p-6 w-full max-w-md">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold">Login Diperlukan</h3>
                <button id="close-modal" class="text-gray-500 hover:text-gray-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            
            <p class="mb-6">Anda perlu login untuk menambahkan komentar.</p>
            
            <div class="flex justify-center space-x-4">
                <a 
                    href="auth/login.php" 
                    class="bg-green-600 text-white py-2 px-6 rounded-md hover:bg-green-700 transition-colors"
                    onclick="sessionStorage.setItem('redirect_after_login', 'plant-detail.php?id=<?php echo $plantId; ?>');"
                >
                    Login
                </a>
                <a 
                    href="auth/register.php" 
                    class="bg-gray-200 text-gray-800 py-2 px-6 rounded-md hover:bg-gray-300 transition-colors"
                >
                    Daftar
                </a>
            </div>
        </div>
    </div>
    
    <script>
        // Show login modal when trying to comment without being logged in
        document.addEventListener('DOMContentLoaded', function() {
            const commentForm = document.querySelector('textarea[name="comment"]');
            if (commentForm) {
                commentForm.addEventListener('focus', function() {
                    document.getElementById('login-modal').classList.remove('hidden');
                });
            }
            
            document.getElementById('close-modal').addEventListener('click', function() {
                document.getElementById('login-modal').classList.add('hidden');
            });
        });
    </script>
    <?php endif; ?>
</body>
</html>

