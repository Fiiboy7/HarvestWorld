<?php
// Include the database file
require_once 'database.php';

// Get category ID from URL parameter
$categoryId = isset($_GET['id']) ? $_GET['id'] : '';

// Get category information
$category = getCategoryById($categoryId);

if (!$category) {
    // Category not found, set default values
    $category = [
        'name' => 'Kategori Tidak Ditemukan',
        'description' => 'Kategori yang Anda cari tidak ditemukan',
        'icon' => 'plant',
        'color' => 'text-gray-600'
    ];
}

// Get search query from URL parameter
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

// Get difficulty filter from URL parameter
$difficultyFilter = isset($_GET['difficulty']) ? $_GET['difficulty'] : [];
// if (!is_array($difficultyFilter)) {
//     $difficultyFilter = [$difficultyFilter];
// }

// Set up filters for database query
$filters = [
    'category_id' => $categoryId
];

if (!empty($searchQuery)) {
    $filters['search'] = $searchQuery;
}

if (!empty($difficultyFilter)) {
    $filters['difficulty'] = $difficultyFilter;
}

// Get filtered plants
$filteredPlants = getPlantsData($filters);

// Function to check if a difficulty is selected
function isDifficultySelected($difficulty, $selectedDifficulties) {
    return in_array($difficulty, $selectedDifficulties);
}

// Function to generate URL with updated parameters
function buildFilterUrl($params = []) {
    $currentParams = $_GET;
    $newParams = array_merge($currentParams, $params);
    return '?' . http_build_query($newParams);
}

// Function to generate URL with a single difficulty
function buildDifficultyUrl($difficulty) {
    $currentParams = $_GET;
    $currentParams['difficulty'] = $difficulty;
    return '?' . http_build_query($currentParams);
}

// Function to generate URL with removed parameter
function buildRemoveParamUrl($param) {
    $currentParams = $_GET;
    unset($currentParams[$param]);
    return '?' . http_build_query($currentParams);
}

// Function to generate URL with cleared filters
function buildClearFiltersUrl() {
    $currentParams = $_GET;
    unset($currentParams['search']);
    unset($currentParams['difficulty']);
    return '?' . http_build_query($currentParams);
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kategori: <?php echo htmlspecialchars($category['name']); ?> - HarvestWorld</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Header/Navbar -->
    <header class="bg-white shadow-sm sticky top-0 z-50">
        <div class="container mx-auto px-4">
            <div class="flex justify-between items-center h-16">
                <!-- Logo and Title -->
                <a href="index.php" class="flex items-center">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center mr-3">
                        <i class="fas fa-seedling text-green-600"></i>
                    </div>
                    <span class="text-green-600 font-bold text-xl">HarvestWorld</span>
                </a>

                <!-- Navigation Links -->
                <nav class="hidden md:flex items-center space-x-8">
                    <a href="plants.php" class="text-gray-700 hover:text-green-600 transition-colors">Tanaman</a>
                    <a href="#" class="text-gray-700 hover:text-green-600 transition-colors">Forum</a>
                    <a href="#" class="text-gray-700 hover:text-green-600 transition-colors">Tentang Kami</a>
                </nav>

                <!-- Mobile Menu Button -->
                <div class="md:hidden">
                    <button class="text-gray-700">
                        <i class="fas fa-bars text-xl"></i>
                    </button>
                </div>
            </div>
        </div>
    </header>
    
    <div class="container mx-auto py-12 px-4">
        <!-- Back Button -->
        <a href="index.php" class="inline-flex items-center gap-2 px-4 py-2 mb-6 text-gray-700 hover:text-gray-900">
            <i class="fas fa-arrow-left text-sm"></i>
            Kembali ke Beranda
        </a>

        <div class="mb-8">
            <h1 class="text-3xl font-bold mb-2">Kategori: <?php echo htmlspecialchars($category['name']); ?></h1>
            <p class="text-gray-600"><?php echo htmlspecialchars($category['description']); ?></p>
        </div>

        <!-- Search and Filter Form -->
        <form action="category.php" method="GET" class="mb-8">
            <input type="hidden" name="id" value="<?php echo htmlspecialchars($categoryId); ?>">
            
            <div class="flex flex-col md:flex-row gap-4 mb-4">
                <div class="relative flex-grow">
                    <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                    <input 
                        type="text" 
                        name="search" 
                        placeholder="<?php echo 'Cari tanaman ' . strtolower($category['name']) . '...'; ?>" 
                        class="w-full pl-10 pr-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                        value="<?php echo htmlspecialchars($searchQuery); ?>"
                    >
                    <?php if (!empty($searchQuery)): ?>
                        <a href="<?php echo buildRemoveParamUrl('search'); ?>" class="absolute right-3 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                            <i class="fas fa-times"></i>
                        </a>
                    <?php endif; ?>
                </div>
                
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Filter
                </button>
                
                <?php if (!empty($searchQuery) || !empty($difficultyFilter)): ?>
                    <a href="<?php echo buildClearFiltersUrl(); ?>" class="px-4 py-2 border border-red-500 text-red-500 rounded-lg hover:bg-red-50">
                        Reset Filter
                    </a>
                <?php endif; ?>
            </div>
            
            <!-- Difficulty Checkboxes -->
            <div class="mb-4">
                <p class="text-sm font-medium mb-2">Tingkat Kesulitan:</p>
                <select 
                    name="difficulty" 
                    class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-green-500"
                >
                    <option value="">Semua Tingkat Kesulitan</option>
                    <option value="Mudah" <?php echo (isset($_GET['difficulty']) && $_GET['difficulty'] === 'Mudah') ? 'selected' : ''; ?>>Mudah</option>
                    <option value="Sedang" <?php echo (isset($_GET['difficulty']) && $_GET['difficulty'] === 'Sedang') ? 'selected' : ''; ?>>Sedang</option>
                    <option value="Sulit" <?php echo (isset($_GET['difficulty']) && $_GET['difficulty'] === 'Sulit') ? 'selected' : ''; ?>>Sulit</option>
                </select>
            </div>
        </form>

        <!-- Active Filters -->
        <?php if (!empty($searchQuery) || !empty($difficultyFilter)): ?>
            <div class="flex flex-wrap gap-2 mb-6">
                <div class="text-sm text-gray-500 flex items-center">Filter Aktif:</div>
                
                <?php if (!empty($searchQuery)): ?>
                    <div class="bg-green-50 text-green-800 text-sm rounded-full px-3 py-1 flex items-center">
                        Pencarian: <?php echo htmlspecialchars($searchQuery); ?>
                        <a href="<?php echo buildRemoveParamUrl('search'); ?>" class="ml-2 text-green-600 hover:text-green-800">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($_GET['difficulty'])): ?>
                    <div class="bg-green-50 text-green-800 text-sm rounded-full px-3 py-1 flex items-center">
                        <?php echo htmlspecialchars($_GET['difficulty']); ?>
                        <a href="<?php 
                            $currentParams = $_GET;
                            unset($currentParams['difficulty']);
                            echo '?' . http_build_query($currentParams);
                        ?>" class="ml-2 text-green-600 hover:text-green-800">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                <?php endif; ?>
                
                <a href="<?php echo buildClearFiltersUrl(); ?>" class="text-sm text-red-500 hover:text-red-700 flex items-center">
                    Hapus Semua
                </a>
            </div>
        <?php endif; ?>

        <!-- Filter Pills -->
        <div class="flex flex-wrap gap-2 mb-8">
            <a href="<?php echo buildFilterUrl(['difficulty' => '']); ?>" 
               class="px-3 py-1 rounded-full border border-gray-300 text-sm <?php echo empty($difficultyFilter) ? 'bg-green-50 text-green-800' : 'text-gray-700 hover:bg-gray-100'; ?>">
                Semua <?php echo htmlspecialchars($category['name']); ?>
            </a>
            <a href="<?php echo buildDifficultyUrl('Mudah'); ?>" 
               class="px-3 py-1 rounded-full border border-gray-300 text-sm <?php echo (isset($_GET['difficulty']) && $_GET['difficulty'] === 'Mudah') ? 'bg-green-50 text-green-800' : 'text-gray-700 hover:bg-gray-100'; ?>">
                Mudah Ditanam
            </a>
            <a href="<?php echo buildDifficultyUrl('Sedang'); ?>" 
               class="px-3 py-1 rounded-full border border-gray-300 text-sm <?php echo (isset($_GET['difficulty']) && $_GET['difficulty'] === 'Sedang') ? 'bg-green-50 text-green-800' : 'text-gray-700 hover:bg-gray-100'; ?>">
                Sedang
            </a>
            <a href="<?php echo buildDifficultyUrl('Sulit'); ?>" 
               class="px-3 py-1 rounded-full border border-gray-300 text-sm <?php echo (isset($_GET['difficulty']) && $_GET['difficulty'] === 'Sulit') ? 'bg-green-50 text-green-800' : 'text-gray-700 hover:bg-gray-100'; ?>">
                Sulit
            </a>
        </div>

        <?php if (count($filteredPlants) > 0): ?>
            <!-- Plants Grid -->
            <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                <?php foreach ($filteredPlants as $plant): ?>
                    <a href="plant-detail.php?id=<?php echo $plant['id']; ?>" class="block">
                        <div class="bg-white rounded-lg shadow-md overflow-hidden transition-all hover:shadow-lg hover:-translate-y-1">
                            <div class="aspect-square overflow-hidden">
                                <img 
                                    src="<?php echo htmlspecialchars($plant['image']); ?>" 
                                    alt="<?php echo htmlspecialchars($plant['name']); ?>" 
                                    class="w-full h-full object-cover transition-transform hover:scale-105"
                                >
                            </div>
                            <div class="p-4">
                                <h3 class="font-semibold text-lg mb-1"><?php echo htmlspecialchars($plant['name']); ?></h3>
                                <p class="text-sm text-gray-500 italic mb-3"><?php echo htmlspecialchars($plant['scientific_name']); ?></p>
                                <div class="flex flex-wrap gap-2 mb-3">
                                    <span class="<?php 
                                        $bgColor = 'bg-green-100 text-green-800';
                                        if ($plant['difficulty'] === 'Sedang') {
                                            $bgColor = 'bg-yellow-100 text-yellow-800';
                                        } elseif ($plant['difficulty'] === 'Sulit') {
                                            $bgColor = 'bg-red-100 text-red-800';
                                        }
                                        echo $bgColor;
                                    ?> text-xs px-2 py-1 rounded-full">
                                        <?php echo htmlspecialchars($plant['difficulty']); ?>
                                    </span>
                                </div>
                            </div>
                            <div class="px-4 py-3 border-t text-sm text-gray-500 flex items-center">
                                <i class="fas fa-clock mr-1"></i>
                                <span>Waktu panen: <?php echo htmlspecialchars($plant['growth_time']); ?></span>
                            </div>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>

            <!-- Results count -->
            <div class="mt-6 text-sm text-gray-500 text-center">
                Menampilkan <?php echo count($filteredPlants); ?> tanaman
            </div>

            <!-- Pagination - only show if we have enough items -->
            <?php if (count($filteredPlants) > 8): ?>
                <div class="flex justify-center mt-8">
                    <nav class="flex items-center gap-1">
                        <span class="px-3 py-1 border border-gray-300 rounded text-gray-400">&lt;</span>
                        <span class="px-3 py-1 border border-gray-300 rounded bg-green-50 text-green-800">1</span>
                        <span class="px-3 py-1 border border-gray-300 rounded text-gray-400">&gt;</span>
                    </nav>
                </div>
            <?php endif; ?>
        <?php else: ?>
            <div class="text-center py-12">
                <div class="text-gray-400 mb-4">
                    <i class="far fa-frown text-6xl"></i>
                </div>
                <h3 class="text-xl font-semibold mb-2">Tidak Ada Tanaman</h3>
                <p class="text-gray-500 mb-6">
                    <?php if (!empty($searchQuery)): ?>
                        Tidak ada tanaman yang cocok dengan pencarian "<?php echo htmlspecialchars($searchQuery); ?>"
                    <?php else: ?>
                        Tidak ada tanaman dalam kategori <?php echo htmlspecialchars($category['name']); ?> dengan filter yang dipilih.
                    <?php endif; ?>
                </p>
                <a href="<?php echo buildClearFiltersUrl(); ?>" class="inline-block px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                    Hapus Filter
                </a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Footer -->
    <footer class="bg-green-800 text-white py-8">
        <div class="container mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between">
                <div class="mb-6 md:mb-0">
                    <h3 class="text-xl font-bold mb-4">HarvestWorld</h3>
                    <p class="text-green-100 max-w-md">
                        Platform edukasi tanaman pertanian dengan informasi lengkap tentang cara perawatan dan manfaat tanaman.
                    </p>
                </div>
                <div>
                    <h4 class="text-lg font-semibold mb-3">Kontak</h4>
                    <p class="text-green-100">Email: info@harvestworld.com</p>
                    <p class="text-green-100">Telepon: (0721) 123-4567</p>
                </div>
            </div>
            <div class="border-t border-green-700 mt-8 pt-6 text-center text-green-100">
                <p>&copy; <?php echo date('Y'); ?> HarvestWorld. All rights reserved.</p>
            </div>
        </div>
    </footer>
</body>
</html>

