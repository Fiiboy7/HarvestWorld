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
    
    <main>
        <!-- Hero Section -->
        <section class="relative h-[500px] flex items-center justify-center overflow-hidden bg-gray-400">
            <!-- Background image placeholder - you can add your image later -->
            <div class="container relative z-10 text-center px-4">
                <h1 class="text-4xl md:text-6xl font-bold text-white mb-4">Sistem Informasi Edukasi Tanaman Pertanian</h1>
                <p class="text-xl text-white mb-8 max-w-3xl mx-auto">
                    Temukan informasi lengkap tentang tanaman pertanian, cara perawatan, dan manfaatnya
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a href="plants.php" class="inline-block px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700">
                        Jelajahi Tanaman
                    </a>
                    <a href="artikel.php" class="inline-block px-6 py-3 bg-white text-green-600 font-medium rounded-lg hover:bg-gray-100">
                        Baca Artikel
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

        <!-- Articles Section -->
        <section class="container mx-auto py-16 px-4">
            <h2 class="text-3xl font-bold text-center mb-12 text-gray-800">Artikel Terbaru</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Article 1 -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <img src="/images/article1.jpg" alt="Article 1" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">Cara Menanam Tomat yang Baik dan Benar</h3>
                        <p class="text-gray-600 mb-4">Panduan lengkap menanam tomat dari biji hingga panen dengan hasil melimpah dan mudah dijangkau.</p>
                        <div class="flex justify-between items-center text-sm text-gray-500">
                            <span><i class="far fa-calendar mr-1"></i> 10 Maret 2024</span>
                            <span><i class="far fa-user mr-1"></i> Dr. Agus Sutanto</span>
                        </div>
                    </div>
                </div>
                
                <!-- Article 2 -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <img src="/images/article2.jpg" alt="Article 2" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">Mengatasi Hama pada Tanaman Sayuran</h3>
                        <p class="text-gray-600 mb-4">Tips dan trik efektif untuk mengatasi berbagai jenis hama yang sering menyerang tanaman sayuran secara organik.</p>
                        <div class="flex justify-between items-center text-sm text-gray-500">
                            <span><i class="far fa-calendar mr-1"></i> 5 Maret 2024</span>
                            <span><i class="far fa-user mr-1"></i> Siti Nurhaliza</span>
                        </div>
                    </div>
                </div>
                
                <!-- Article 3 -->
                <div class="bg-white rounded-lg shadow-md overflow-hidden">
                    <img src="/images/article3.jpg" alt="Article 3" class="w-full h-48 object-cover">
                    <div class="p-6">
                        <h3 class="text-xl font-semibold mb-2">Manfaat Tanaman Herbal untuk Kesehatan</h3>
                        <p class="text-gray-600 mb-4">Mengenal berbagai tanaman herbal dan khasiatnya untuk menjaga kesehatan dan mengobati penyakit ringan.</p>
                        <div class="flex justify-between items-center text-sm text-gray-500">
                            <span><i class="far fa-calendar mr-1"></i> 28 Februari 2024</span>
                            <span><i class="far fa-user mr-1"></i> Budi Santoso</span>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="text-center mt-8">
                <a href="artikel.php" class="inline-block px-6 py-3 border border-green-600 text-green-600 rounded-lg hover:bg-green-50">
                    Lihat Semua Artikel
                </a>
            </div>
        </section>
    </main>

    <!-- Footer -->
    <footer class="bg-green-800 text-white py-8">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
                <!-- Column 1: About -->
                <div>
                    <div class="flex items-center mb-4">
                        <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center mr-3">
                            <i class="fas fa-seedling text-green-600"></i>
                        </div>
                        <span class="text-white font-bold text-xl">HarvestWorld</span>
                    </div>
                    <p class="text-green-100 mb-4">
                        Platform edukasi tanaman pertanian dengan informasi lengkap tentang cara perawatan dan manfaat tanaman.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#" class="text-white hover:text-green-200"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="text-white hover:text-green-200"><i class="fab fa-twitter"></i></a>
                        <a href="#" class="text-white hover:text-green-200"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="text-white hover:text-green-200"><i class="far fa-envelope"></i></a>
                    </div>
                </div>
                
                <!-- Column 2: Quick Links -->
                <div>
                    <h4 class="text-lg font-semibold mb-4">Tautan Cepat</h4>
                    <ul class="space-y-2">
                        <li><a href="plants.php" class="text-green-100 hover:text-white">Daftar Tanaman</a></li>
                        <li><a href="artikel.php" class="text-green-100 hover:text-white">Artikel Edukasi</a></li>
                        <li><a href="forum.php" class="text-green-100 hover:text-white">Forum Diskusi</a></li>
                        <li><a href="about.php" class="text-green-100 hover:text-white">Tentang Kami</a></li>
                    </ul>
                </div>
                
                <!-- Column 3: Categories -->
                <div>
                    <h4 class="text-lg font-semibold mb-4">Kategori</h4>
                    <ul class="space-y-2">
                        <li><a href="category.php?id=1" class="text-green-100 hover:text-white">Sayuran</a></li>
                        <li><a href="category.php?id=2" class="text-green-100 hover:text-white">Buah-buahan</a></li>
                        <li><a href="category.php?id=3" class="text-green-100 hover:text-white">Rempah-rempah</a></li>
                        <li><a href="category.php?id=4" class="text-green-100 hover:text-white">Tanaman Hias</a></li>
                    </ul>
                </div>
                
                <!-- Column 4: Contact -->
                <div>
                    <h4 class="text-lg font-semibold mb-4">Kontak</h4>
                    <p class="text-green-100 mb-2">Program Studi Ilmu Komputer</p>
                    <p class="text-green-100 mb-2">Fakultas Matematika dan Ilmu Pengetahuan Alam</p>
                    <p class="text-green-100 mb-2">Universitas Lampung</p>
                    <p class="text-green-100 mb-2">Bandar Lampung, Indonesia</p>
                    <p class="text-green-100 mb-2">Email: info@harvestworld.ac.id</p>
                    <p class="text-green-100">Telepon: (0721) 123-4567</p>
                </div>
            </div>
            
            <div class="border-t border-green-700 mt-8 pt-6 text-center text-green-100">
                <p>&copy; 2025 HarvestWorld - Sistem Informasi Edukasi Tanaman Pertanian. All rights reserved.</p>
                <p class="mt-2">Dikembangkan oleh Rofik Ramadani, Muhammad Fa'ji Ramadhani, Adi Rahmad Safei, Khomarul Hidayat</p>
            </div>
        </div>
    </footer>
</body>
</html>

