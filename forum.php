<?php
// Start session
session_start();

// Include database connection
require_once 'database.php';

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

// Get forum topics
$topics = getForumTopics();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forum Diskusi - HarvestWorld</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include 'includes/header.php'; ?>
    
    <div class="container mx-auto py-12 px-4">
        <div class="flex justify-between items-center mb-8">
            <h1 class="text-3xl font-bold">Forum Diskusi</h1>
            
            <?php if ($isLoggedIn): ?>
                <a 
                    href="create-topic.php" 
                    class="bg-green-600 text-white py-2 px-6 rounded-md hover:bg-green-700 transition-colors"
                >
                    <i class="fas fa-plus-circle mr-2"></i> Buat Topik Baru
                </a>
            <?php else: ?>
                <a 
                    href="auth/login.php" 
                    class="bg-green-600 text-white py-2 px-6 rounded-md hover:bg-green-700 transition-colors"
                >
                    <i class="fas fa-sign-in-alt mr-2"></i> Login untuk Buat Topik
                </a>
            <?php endif; ?>
        </div>
        
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Forum Categories -->
            <div class="bg-gray-50 p-4 border-b border-gray-200">
                <div class="flex flex-wrap gap-2">
                    <a href="forum.php" class="px-3 py-1 rounded-full bg-green-50 text-green-800 border border-green-200">
                        Semua Topik
                    </a>
                    <a href="forum.php?category=pertanyaan" class="px-3 py-1 rounded-full text-gray-700 border border-gray-200 hover:bg-gray-100">
                        Pertanyaan
                    </a>
                    <a href="forum.php?category=diskusi" class="px-3 py-1 rounded-full text-gray-700 border border-gray-200 hover:bg-gray-100">
                        Diskusi
                    </a>
                    <a href="forum.php?category=tips" class="px-3 py-1 rounded-full text-gray-700 border border-gray-200 hover:bg-gray-100">
                        Tips & Trik
                    </a>
                    <a href="forum.php?category=pengalaman" class="px-3 py-1 rounded-full text-gray-700 border border-gray-200 hover:bg-gray-100">
                        Berbagi Pengalaman
                    </a>
                </div>
            </div>
            
            <!-- Topics List -->
            <div class="divide-y divide-gray-200">
                <?php if (empty($topics)): ?>
                    <div class="p-8 text-center">
                        <div class="text-gray-400 mb-4">
                            <i class="far fa-comment-dots text-6xl"></i>
                        </div>
                        <h3 class="text-xl font-semibold mb-2">Belum Ada Topik</h3>
                        <p class="text-gray-500 mb-4">
                            Jadilah yang pertama membuat topik diskusi di forum ini.
                        </p>
                        <?php if ($isLoggedIn): ?>
                            <a 
                                href="create-topic.php" 
                                class="inline-block bg-green-600 text-white py-2 px-6 rounded-md hover:bg-green-700 transition-colors"
                            >
                                Buat Topik Baru
                            </a>
                        <?php else: ?>
                            <a 
                                href="auth/login.php" 
                                class="inline-block bg-green-600 text-white py-2 px-6 rounded-md hover:bg-green-700 transition-colors"
                            >
                                Login untuk Buat Topik
                            </a>
                        <?php endif; ?>
                    </div>
                <?php else: ?>
                    <?php foreach ($topics as $topic): ?>
                        <div class="p-6 hover:bg-gray-50">
                            <div class="flex items-start">
                                <img 
                                    src="<?php echo htmlspecialchars($topic['profile_image'] ?? '/images/default-avatar.png'); ?>" 
                                    alt="<?php echo htmlspecialchars($topic['username']); ?>" 
                                    class="w-10 h-10 rounded-full object-cover mr-4"
                                >
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <a href="topic.php?id=<?php echo $topic['id']; ?>" class="text-lg font-semibold hover:text-green-600">
                                                <?php echo htmlspecialchars($topic['title']); ?>
                                            </a>
                                            <?php if ($topic['is_pinned']): ?>
                                                <span class="ml-2 px-2 py-0.5 bg-blue-100 text-blue-800 text-xs rounded-full">
                                                    <i class="fas fa-thumbtack mr-1"></i> Disematkan
                                                </span>
                                            <?php endif; ?>
                                            <div class="text-sm text-gray-500 mt-1">
                                                <span>Oleh <a href="#" class="text-green-600 hover:underline"><?php echo htmlspecialchars($topic['username']); ?></a></span>
                                                <span class="mx-2">•</span>
                                                <span>
                                                    <?php 
                                                        $date = new DateTime($topic['created_at']);
                                                        echo $date->format('d M Y, H:i');
                                                    ?>
                                                </span>
                                            </div>
                                        </div>
                                        <div class="text-center">
                                            <div class="bg-gray-100 rounded-full px-3 py-1 text-sm">
                                                <i class="far fa-comment-dots mr-1"></i> <?php echo $topic['reply_count']; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <p class="text-gray-600 mt-2 line-clamp-2">
                                        <?php echo htmlspecialchars(substr($topic['content'], 0, 200)) . (strlen($topic['content']) > 200 ? '...' : ''); ?>
                                    </p>
                                    <div class="mt-3 flex items-center">
                                        <span class="px-2 py-0.5 bg-green-100 text-green-800 text-xs rounded-full">
                                            <?php echo htmlspecialchars($topic['category']); ?>
                                        </span>
                                        <?php if (!empty($topic['last_reply_at'])): ?>
                                            <span class="ml-4 text-xs text-gray-500">
                                                <i class="fas fa-reply fa-flip-horizontal mr-1"></i>
                                                Balasan terakhir: 
                                                <?php 
                                                    $lastReplyDate = new DateTime($topic['last_reply_at']);
                                                    $now = new DateTime();
                                                    $interval = $lastReplyDate->diff($now);
                                                    
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
                                        <?php endif; ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>

