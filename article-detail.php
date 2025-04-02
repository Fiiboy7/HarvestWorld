<?php
// Start session
session_start();

// Include the database file
require_once 'database.php';

// Get article ID from URL parameter
$articleId = isset($_GET['id']) ? intval($_GET['id']) : 0;

// Get article data
$article = getArticleById($articleId);

// If article not found, redirect to articles page
if (!$article) {
  header('Location: artikel.php');
  exit;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo htmlspecialchars($article['title']); ?> - HarvestWorld</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-50">
  <?php include 'includes/header.php'; ?>
  
  <div class="container mx-auto py-12 px-4">
    <!-- Back Button -->
    <a href="artikel.php" class="inline-flex items-center gap-2 px-4 py-2 mb-6 text-gray-700 hover:text-gray-900">
      <i class="fas fa-arrow-left text-sm"></i>
      Kembali ke Artikel
    </a>

    <article class="bg-white rounded-lg shadow-md overflow-hidden">
      <!-- Article Header -->
      <div class="relative h-64 md:h-96 bg-gray-800">
        <img 
          src="<?php echo htmlspecialchars($article['image']); ?>" 
          alt="<?php echo htmlspecialchars($article['title']); ?>" 
          class="w-full h-full object-cover opacity-75"
        >
        <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent"></div>
        <div class="absolute bottom-0 left-0 p-6 md:p-8 text-white">
          <h1 class="text-3xl md:text-4xl font-bold mb-2"><?php echo htmlspecialchars($article['title']); ?></h1>
          <div class="flex items-center text-sm">
            <span class="mr-4">
              <i class="far fa-user mr-1"></i> <?php echo htmlspecialchars($article['author']); ?>
            </span>
            <span>
              <i class="far fa-calendar mr-1"></i> 
              <?php 
                $date = new DateTime($article['created_at']);
                echo $date->format('d F Y');
              ?>
            </span>
          </div>
        </div>
      </div>
      
      <!-- Article Content -->
      <div class="p-6 md:p-8">
        <div class="prose prose-green max-w-none">
          <?php echo nl2br(htmlspecialchars($article['content'])); ?>
        </div>
      </div>
    </article>
    
    <!-- Related Articles -->
    <div class="mt-12">
      <h2 class="text-2xl font-bold mb-6">Artikel Terkait</h2>
      
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <?php 
        // Get 3 random articles excluding current one
        $relatedArticles = getAllArticles();
        $filteredArticles = [];
        foreach ($relatedArticles as $relatedArticle) {
          if ($relatedArticle['id'] != $articleId) {
            $filteredArticles[] = $relatedArticle;
            if (count($filteredArticles) >= 3) break;
          }
        }
        
        foreach ($filteredArticles as $relatedArticle): 
        ?>
          <a href="article-detail.php?id=<?php echo $relatedArticle['id']; ?>" class="block">
            <div class="bg-white rounded-lg shadow-md overflow-hidden transition-all hover:shadow-lg hover:-translate-y-1">
              <div class="h-48 overflow-hidden">
                <img 
                  src="<?php echo htmlspecialchars($relatedArticle['image']); ?>" 
                  alt="<?php echo htmlspecialchars($relatedArticle['title']); ?>" 
                  class="w-full h-full object-cover transition-transform hover:scale-105"
                >
              </div>
              <div class="p-4">
                <h3 class="font-semibold text-lg mb-2"><?php echo htmlspecialchars($relatedArticle['title']); ?></h3>
                <p class="text-gray-600 text-sm mb-3"><?php echo htmlspecialchars(substr($relatedArticle['excerpt'], 0, 100) . '...'); ?></p>
                <div class="flex justify-between items-center text-sm text-gray-500">
                  <span><?php echo htmlspecialchars($relatedArticle['author']); ?></span>
                  <span>
                    <?php 
                      $date = new DateTime($relatedArticle['created_at']);
                      echo $date->format('d M Y');
                    ?>
                  </span>
                </div>
              </div>
            </div>
          </a>
        <?php endforeach; ?>
      </div>
    </div>
  </div>
  
  <?php include 'includes/footer.php'; ?>
</body>
</html>

