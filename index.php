<?php
// Start session
session_start();

// Include the database file
require_once 'database.php';

// Get category data
$categories = getCategoryData();

// Count plants for each category
foreach ($categories as $id => &$category) {
    $category['count'] = countPlantsByCategory($id);
}
unset($category); // Break the reference
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HarvestWorld - Sistem Informasi Edukasi Tanaman Pertanian</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-50">
    <?php include 'includes/header.php'; ?>
    
    <main class="min-h-screen bg-gradient-to-b from-green-50 to-white">
        <!-- Hero Section -->
        <section class="relative h-[500px] flex items-center justify-center overflow-hidden">
            <div
                class="absolute inset-0 bg-cover bg-center z-0"
                style="
                    background-image: url('/images/hero-bg.jpg');
                    filter: brightness(0.7);
                "
            ></div>
            <div class="container relative z-10 text-center px-4">
                <h1 class="text-4xl md:text-6xl font-bold text-white mb-4">Sistem Informasi Edukasi Tanaman Pertanian</h1>
                <p class="text-xl text-white mb-8 max-w-3xl mx-auto">
                    Temukan informasi lengkap tentang tanaman pertanian, cara perawatan, dan manfaatnya
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="plants.php" class="inline-block px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700">
                        Jelajahi Tanaman
                    </a>
                </div>
            </div>
        </section>

        <!-- Search Section -->
        <section class="container mx-auto -mt-8 px-4 relative z-20">
            <div class="bg-white rounded-lg shadow-lg p-6">
                <form action="plants.php" method="GET" class="relative">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input
                        type="text"
                        name="search"
                        placeholder="Cari tanaman berdasarkan nama atau kategori..."
                        class="w-full pl-10 pr-4 py-3 rounded-lg border border-gray-200 focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent"
                    >
                </form>
            </div>
        </section>

        <!-- Plant Categories -->
        <section class="container mx-auto py-16 px-4">
            <h2 class="text-3xl font-bold text-center mb-12 text-gray-800">Kategori Tanaman</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <?php foreach ($categories as $id => $category): ?>
                    <a href="category.php?id=<?php echo htmlspecialchars($id); ?>" class="block">
                        <div class="bg-white rounded-lg shadow-md h-full transition-all hover:shadow-lg hover:-translate-y-1">
                            <div class="p-6 flex flex-col items-center text-center">
                                <div class="mb-4 p-3 rounded-full bg-gray-50">
                                    <i class="fas fa-<?php echo htmlspecialchars($category['icon']); ?> text-4xl <?php echo htmlspecialchars($category['color']); ?>"></i>
                                </div>
                                <h3 class="text-xl font-semibold mb-2"><?php echo htmlspecialchars($category['name']); ?></h3>
                                <p class="text-gray-600 mb-3"><?php echo htmlspecialchars($category['description']); ?></p>
                                <span class="text-sm text-gray-500"><?php echo $category['count']; ?> tanaman</span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </section>
    </main>

    <?php include 'includes/footer.php'; ?>
</body>
</html>

