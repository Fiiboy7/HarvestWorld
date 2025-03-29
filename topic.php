<?php
// Start session
session_start();

// Include database connection
require_once 'database.php';

// Check if topic ID is provided
if (!isset($_GET['id'])) {
    header('Location: forum.php');
    exit;
}

$topicId = intval($_GET['id']);
$topic = getForumTopicById($topicId);

// If topic not found, redirect to forum page
if (!$topic) {
    header('Location: forum.php');
    exit;
}

// Get replies for this topic
$replies = getForumRepliesByTopicId($topicId);

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);

// Handle reply submission
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_reply'])) {
    if (!$isLoggedIn) {
        // Store the current URL to redirect back after login
        $_SESSION['redirect_after_login'] = "topic.php?id=$topicId";
        header('Location: auth/login.php');
        exit;
    }
    
    $content = trim($_POST['content'] ?? '');
    
    if (empty($content)) {
        $error = 'Balasan tidak boleh kosong';
    } else {
        $replyData = [
            'topic_id' => $topicId,
            'user_id' => $_SESSION['user_id'],
            'content' => $content
        ];
        
        $result = addForumReply($replyData);
        
        if ($result) {
            $success = 'Balasan berhasil ditambahkan';
            // Reload page to show the new reply
            header("Location: topic.php?id=$topicId&success=reply_added");
            exit;
        } else {
            $error = 'Gagal menambahkan balasan';
        }
    }
}

// Check for success message from redirect
if (isset($_GET['success']) && $_GET['success'] === 'reply_added') {
    $success = 'Balasan berhasil ditambahkan';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($topic['title']); ?> - Forum HarvestWorld</title>
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
        </div>
        
        <!-- Topic -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-8">
            <div class="flex justify-between items-start mb-4">
                <h1 class="text-2xl font-bold"><?php echo htmlspecialchars($topic['title']); ?></h1>
                <span class="px-3 py-1 bg-green-100 text-green-800 text-xs rounded-full">
                    <?php echo htmlspecialchars($topic['category']); ?>
                </span>
            </div>
            
            <div class="flex items-start mb-6">
                <img 
                    src="<?php echo htmlspecialchars($topic['profile_image'] ?? '/images/default-avatar.png'); ?>" 
                    alt="<?php echo htmlspecialchars($topic['username']); ?>" 
                    class="w-12 h-12 rounded-full object-cover mr-4"
                >
                <div>
                    <div class="font-semibold"><?php echo htmlspecialchars($topic['full_name']); ?></div>
                    <div class="text-sm text-gray-500">@<?php echo htmlspecialchars($topic['username']); ?></div>
                    <div class="text-sm text-gray-500 mt-1">
                        <?php 
                            $date = new DateTime($topic['created_at']);
                            echo $date->format('d M Y, H:i');
                        ?>
                    </div>
                </div>
            </div>
            
            <div class="prose max-w-none mb-6">
                <?php echo nl2br(htmlspecialchars($topic['content'])); ?>
            </div>
            
            <?php if ($isLoggedIn && ($_SESSION['user_id'] == $topic['user_id'] || $_SESSION['role'] === 'admin')): ?>
                <div class="flex justify-end space-x-2">
                    <a 
                        href="edit-topic.php?id=<?php echo $topic['id']; ?>" 
                        class="text-blue-600 hover:text-blue-800"
                    >
                        <i class="fas fa-edit mr-1"></i> Edit
                    </a>
                    <a 
                        href="delete-topic.php?id=<?php echo $topic['id']; ?>" 
                        class="text-red-600 hover:text-red-800"
                        onclick="return confirm('Apakah Anda yakin ingin menghapus topik ini?');"
                    >
                        <i class="fas fa-trash mr-1"></i> Hapus
                    </a>
                </div>
            <?php endif; ?>
        </div>
        
        <!-- Replies -->
        <h2 class="text-xl font-bold mb-6">Balasan (<?php echo count($replies); ?>)</h2>
        
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
        
        <?php if (count($replies) > 0): ?>
            <div class="space-y-6 mb-8">
                <?php foreach ($replies as $reply): ?>
                    <div class="bg-white rounded-lg shadow-md p-6" id="reply-<?php echo $reply['id']; ?>">
                        <div class="flex items-start">
                            <img 
                                src="<?php echo htmlspecialchars($reply['profile_image'] ?? '/images/default-avatar.png'); ?>" 
                                alt="<?php echo htmlspecialchars($reply['username']); ?>" 
                                class="w-10 h-10 rounded-full object-cover mr-4"
                            >
                            <div class="flex-1">
                                <div class="flex justify-between items-center mb-2">
                                    <div>
                                        <span class="font-semibold"><?php echo htmlspecialchars($reply['full_name']); ?></span>
                                        <span class="text-sm text-gray-500 ml-2">@<?php echo htmlspecialchars($reply['username']); ?></span>
                                    </div>
                                    <span class="text-sm text-gray-500">
                                        <?php 
                                            $replyDate = new DateTime($reply['created_at']);
                                            echo $replyDate->format('d M Y, H:i');
                                        ?>
                                    </span>
                                </div>
                                <div class="text-gray-700">
                                    <?php echo nl2br(htmlspecialchars($reply['content'])); ?>
                                </div>
                                
                                <?php if ($isLoggedIn && ($_SESSION['user_id'] == $reply['user_id'] || $_SESSION['role'] === 'admin')): ?>
                                    <div class="mt-3 text-right">
                                        <a 
                                            href="delete-reply.php?id=<?php echo $reply['id']; ?>&topic_id=<?php echo $topicId; ?>" 
                                            class="text-red-600 text-sm hover:text-red-800"
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus balasan ini?');"
                                        >
                                            <i class="fas fa-trash-alt mr-1"></i> Hapus
                                        </a>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="bg-white rounded-lg shadow-md p-6 text-center mb-8">
                <div class="text-gray-400 mb-4">
                    <i class="far fa-comment-dots text-6xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">Belum Ada Balasan</h3>
                <p class="text-gray-500 mb-4">
                    Jadilah yang pertama memberikan balasan pada topik ini.
                </p>
            </div>
        <?php endif; ?>
        
        <!-- Reply Form -->
        <div class="bg-white rounded-lg shadow-md p-6">
            <h3 class="text-lg font-semibold mb-4">Tambahkan Balasan</h3>
            
            <?php if ($isLoggedIn): ?>
                <form method="POST" action="topic.php?id=<?php echo $topicId; ?>">
                    <div class="mb-4">
                        <textarea 
                            name="content" 
                            rows="4" 
                            class="w-full px-4 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-green-500"
                            placeholder="Tulis balasan Anda..."
                            required
                        ></textarea>
                    </div>
                    
                    <button 
                        type="submit" 
                        name="submit_reply" 
                        class="bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition-colors"
                    >
                        Kirim Balasan
                    </button>
                </form>
            <?php else: ?>
                <div class="bg-gray-100 p-4 rounded-md text-center">
                    <p class="text-gray-700 mb-3">Anda harus login untuk menambahkan balasan</p>
                    <a 
                        href="auth/login.php" 
                        class="inline-block bg-green-600 text-white py-2 px-4 rounded-md hover:bg-green-700 transition-colors"
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
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>

