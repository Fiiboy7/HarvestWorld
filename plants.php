<?php
// Start session
session_start();

// Include the database file
require_once 'database.php';

// Get all plants data
$allPlants = getPlantsData();

// Get all categories
$categories = getCategoryData();

// Get search query from URL parameter
$searchQuery = isset($_GET['search']) ? $_GET['search'] : '';

// Get category filter from URL parameter
$categoryFilter = isset($_GET['category']) ? $_GET['category'] : [];
if (!is_array($categoryFilter)) {
  $categoryFilter = [$categoryFilter];
}

// Get difficulty filter from URL parameter
$difficultyFilter = isset($_GET['difficulty']) ? $_GET['difficulty'] : [];
if (!is_array($difficultyFilter)) {
  $difficultyFilter = [$difficultyFilter];
}

// Set up filters for database query
$filters = [];

if (!empty($searchQuery)) {
  $filters['search'] = $searchQuery;
}

if (!empty($categoryFilter)) {
  $filters['category'] = $categoryFilter;
}

if (!empty($difficultyFilter)) {
  $filters['difficulty'] = $difficultyFilter;
}

// Get filtered plants
$filteredPlants = getPlantsData($filters);

// Function to check if a category is selected
function isCategorySelected($category, $selectedCategories) {
  return in_array($category, $selectedCategories);
}

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

// Function to generate URL with a single category
function buildCategoryUrl($category) {
  $currentParams = $_GET;
  $currentParams['category'] = $category;
  return '?' . http_build_query($currentParams);
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
  return 'plants.php';
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Daftar Tanaman - HarvestWorld</title>
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-50">
  <?php include 'includes/header.php'; ?>
  
  <div class="container mx-auto py-12 px-4">
      <h1 class="text-3xl font-bold mb-8">Daftar Tanaman</h1>

      <!-- Search and Filter Form -->
      <form action="plants.php" method="GET" class="mb-8">
          <div class="flex flex-col md:flex-row gap-4 mb-4">
              <div class="relative flex-grow">
                  <i class="fas fa-search absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400"></i>
                  <input 
                      type="text" 
                      name="search" 
                      placeholder="Cari tanaman..." 
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
              
              <?php if (!empty($searchQuery) || !empty($categoryFilter) || !empty($difficultyFilter)): ?>
                  <a href="<?php echo buildClearFiltersUrl(); ?>" class="px-4 py-2 border border-red-500 text-red-500 rounded-lg hover:bg-red-50">
                      Reset Filter
                  </a>
              <?php endif; ?>
          </div>
          
          <!-- Category Checkboxes -->
          <div class="mb-4">
              <p class="text-sm font-medium mb-2">Kategori:</p>
              <div class="flex flex-wrap gap-4">
                  <?php foreach ($categories as $id => $category): ?>
                      <label class="inline-flex items-center">
                          <input 
                              type="checkbox" 
                              name="category[]" 
                              value="<?php echo htmlspecialchars($category['name']); ?>" 
                              <?php echo isCategorySelected($category['name'], $categoryFilter) ? 'checked' : ''; ?>
                              class="form-checkbox h-5 w-5 text-green-600"
                          >
                          <span class="ml-2"><?php echo htmlspecialchars($category['name']); ?></span>
                      </label>
                  <?php endforeach; ?>
              </div>
          </div>
          
          <!-- Replace the difficulty filter section with this dropdown -->
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
      <?php if (!empty($searchQuery) || !empty($categoryFilter) || !empty($difficultyFilter)): ?>
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
              
              <?php foreach ($categoryFilter as $category): ?>
                  <div class="bg-green-50 text-green-800 text-sm rounded-full px-3 py-1 flex items-center">
                      <?php echo htmlspecialchars($category); ?>
                      <a href="<?php 
                          $newCategories = array_diff($categoryFilter, [$category]);
                          $currentParams = $_GET;
                          $currentParams['category'] = $newCategories;
                          echo '?' . http_build_query($currentParams);
                      ?>" class="ml-2 text-green-600 hover:text-green-800">
                          <i class="fas fa-times"></i>
                      </a>
                  </div>
              <?php endforeach; ?>
              
              <?php foreach ($difficultyFilter as $difficulty): ?>
                  <div class="bg-green-50 text-green-800 text-sm rounded-full px-3 py-1 flex items-center">
                      <?php echo htmlspecialchars($difficulty); ?>
                      <a href="<?php 
                          $newDifficulties = array_diff($difficultyFilter, [$difficulty]);
                          $currentParams = $_GET;
                          $currentParams['difficulty'] = $newDifficulties;
                          echo '?' . http_build_query($currentParams);
                      ?>" class="ml-2 text-green-600 hover:text-green-800">
                          <i class="fas fa-times"></i>
                      </a>
                  </div>
              <?php endforeach; ?>
              
              <a href="<?php echo buildClearFiltersUrl(); ?>" class="text-sm text-red-500 hover:text-red-700 flex items-center">
                  Hapus Semua
              </a>
          </div>
      <?php endif; ?>

      <!-- Filter Pills -->
      <!-- Replace the filter section with this new design -->
<div class="flex flex-wrap gap-2 mb-8 bg-gray-100 p-2 rounded-md">
    <a href="plants.php" class="px-3 py-1.5 rounded-md bg-white shadow-sm text-sm font-medium">
        <i class="fas fa-filter mr-1"></i> Semua Filter
    </a>
    
    <div class="relative group">
        <button class="px-3 py-1.5 rounded-md bg-white shadow-sm text-sm font-medium flex items-center">
            Format <i class="fas fa-chevron-down ml-1 text-xs"></i>
        </button>
        <div class="absolute left-0 mt-1 w-48 bg-white rounded-md shadow-lg py-1 z-10 hidden group-hover:block">
            <a href="<?php echo buildDifficultyUrl('Mudah'); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Mudah</a>
            <a href="<?php echo buildDifficultyUrl('Sedang'); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sedang</a>
            <a href="<?php echo buildDifficultyUrl('Sulit'); ?>" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Sulit</a>
        </div>
    </div>
    
    <div class="relative group">
        <button class="px-3 py-1.5 rounded-md bg-white shadow-sm text-sm font-medium flex items-center">
            Gaya <i class="fas fa-chevron-down ml-1 text-xs"></i>
        </button>
        <div class="absolute left-0 mt-1 w-48 bg-white rounded-md shadow-lg py-1 z-10 hidden group-hover:block">
            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Gaya 1</a>
            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Gaya 2</a>
        </div>
    </div>
    
    <button class="px-3 py-1.5 rounded-md bg-white shadow-sm text-sm font-medium">
        Hanya bahasa saya
    </button>
    
    <div class="relative group">
        <button class="px-3 py-1.5 rounded-md bg-white shadow-sm text-sm font-medium flex items-center">
            Tema <i class="fas fa-chevron-down ml-1 text-xs"></i>
        </button>
        <div class="absolute left-0 mt-1 w-48 bg-white rounded-md shadow-lg py-1 z-10 hidden group-hover:block">
            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Tema 1</a>
            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Tema 2</a>
        </div>
    </div>
    
    <div class="relative group">
        <button class="px-3 py-1.5 rounded-md bg-white shadow-sm text-sm font-medium flex items-center">
            Fitur <i class="fas fa-chevron-down ml-1 text-xs"></i>
        </button>
        <div class="absolute left-0 mt-1 w-48 bg-white rounded-md shadow-lg py-1 z-10 hidden group-hover:block">
            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Fitur 1</a>
            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Fitur 2</a>
        </div>
    </div>
    
    <div class="relative group">
        <button class="px-3 py-1.5 rounded-md bg-white shadow-sm text-sm font-medium flex items-center">
            Warna <i class="fas fa-chevron-down ml-1 text-xs"></i>
        </button>
        <div class="absolute left-0 mt-1 w-48 bg-white rounded-md shadow-lg py-1 z-10 hidden group-hover:block">
            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Warna 1</a>
            <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">Warna 2</a>
        </div>
    </div>
    
    <button class="px-3 py-1.5 rounded-md bg-white shadow-sm text-sm font-medium">
        Presentasi Ulang Tahun
    </button>
    
    <button class="px-3 py-1.5 rounded-md bg-white shadow-sm text-sm font-medium">
        Tukar Pikiran
    </button>
    
    <button class="px-3 py-1.5 rounded-md bg-white shadow-sm text-sm font-medium">
        Presentasi Hari Ayah
    </button>
</div>

      <?php if (count($filteredPlants) > 0): ?>
          <!-- Replace the plant cards section with this updated version -->
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

          <!-- Pagination -->
          <div class="flex justify-center mt-12">
              <nav class="flex items-center gap-1">
                  <span class="px-3 py-1 border border-gray-300 rounded text-gray-400">&lt;</span>
                  <span class="px-3 py-1 border border-gray-300 rounded bg-green-50 text-green-800">1</span>
                  <span class="px-3 py-1 border border-gray-300 rounded text-gray-700 hover:bg-gray-100">2</span>
                  <span class="px-3 py-1 border border-gray-300 rounded text-gray-700 hover:bg-gray-100">3</span>
                  <span class="px-3 py-1 border border-gray-300 rounded text-gray-700 hover:bg-gray-100">&gt;</span>
              </nav>
          </div>
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
                      Tidak ada tanaman yang cocok dengan filter yang dipilih.
                  <?php endif; ?>
              </p>
              <a href="<?php echo buildClearFiltersUrl(); ?>" class="inline-block px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700">
                  Hapus Filter
              </a>
          </div>
      <?php endif; ?>
  </div>

  <?php include 'includes/footer.php'; ?>
</body>
</html>

