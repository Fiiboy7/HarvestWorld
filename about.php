<?php
// Start session
session_start();
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tentang Kami - HarvestWorld</title>
    <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="bg-gray-100">
    <?php include 'includes/header.php'; ?>
    
    <div class="container mx-auto py-12 px-4">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <!-- Hero Section -->
            <div class="relative h-80 bg-green-600">
                <div class="absolute inset-0 bg-cover bg-center" style="background-image: url('/images/about-hero.jpg'); opacity: 0.3;"></div>
                <div class="relative z-10 h-full flex flex-col items-center justify-center text-white p-6">
                    <h1 class="text-4xl font-bold mb-4 text-center">Tentang HarvestWorld</h1>
                    <p class="text-xl max-w-3xl text-center">
                        Platform edukasi tanaman pertanian dengan informasi lengkap tentang cara perawatan dan manfaat tanaman.
                    </p>
                </div>
            </div>
            
            <!-- About Content -->
            <div class="p-8">
                <div class="max-w-4xl mx-auto">
                    <h2 class="text-2xl font-bold mb-6">Visi Kami</h2>
                    <p class="text-gray-700 mb-8">
                        Menjadi platform edukasi pertanian terdepan yang membantu masyarakat Indonesia untuk memahami, menanam, dan merawat berbagai jenis tanaman dengan mudah dan berkelanjutan.
                    </p>
                    
                    <h2 class="text-2xl font-bold mb-6">Misi Kami</h2>
                    <p class="text-gray-700 mb-8">
                        Kami berkomitmen untuk:
                    </p>
                    <ul class="list-disc pl-6 text-gray-700 mb-8 space-y-2">
                        <li>Menyediakan informasi yang akurat dan komprehensif tentang berbagai jenis tanaman pertanian</li>
                        <li>Mengedukasi masyarakat tentang praktik pertanian yang berkelanjutan dan ramah lingkungan</li>
                        <li>Membangun komunitas petani dan pecinta tanaman yang saling mendukung</li>
                        <li>Mendorong kemandirian pangan melalui edukasi pertanian skala rumah tangga</li>
                    </ul>
                    
                    <h2 class="text-2xl font-bold mb-6">Cerita Kami</h2>
                    <p class="text-gray-700 mb-8">
                        HarvestWorld dimulai dari sebuah ide sederhana: membuat informasi tentang pertanian lebih mudah diakses oleh semua orang. Kami percaya bahwa dengan pengetahuan yang tepat, siapa pun dapat menanam dan merawat tanaman mereka sendiri, baik di pekarangan rumah, balkon apartemen, atau lahan pertanian.
                    </p>
                    <p class="text-gray-700 mb-8">
                        Didirikan pada tahun 2023, HarvestWorld terus berkembang berkat dukungan dari komunitas petani, ahli pertanian, dan pecinta tanaman di seluruh Indonesia. Kami berkomitmen untuk terus memperluas database tanaman kami dan meningkatkan fitur platform untuk memenuhi kebutuhan pengguna kami.
                    </p>
                    
                    <h2 class="text-2xl font-bold mb-6">Tim Kami</h2>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                        <div class="text-center">
                            <img src="/images/team-1.jpg" alt="Founder" class="w-32 h-32 rounded-full mx-auto mb-4 object-cover">
                            <h3 class="font-semibold text-lg">Budi Santoso</h3>
                            <p class="text-gray-600">Founder & CEO</p>
                        </div>
                        <div class="text-center">
                            <img src="/images/team-2.jpg" alt="Co-Founder" class="w-32 h-32 rounded-full mx-auto mb-4 object-cover">
                            <h3 class="font-semibold text-lg">Siti Rahayu</h3>
                            <p class="text-gray-600">Co-Founder & Ahli Pertanian</p>
                        </div>
                        <div class="text-center">
                            <img src="/images/team-3.jpg" alt="Lead Developer" class="w-32 h-32 rounded-full mx-auto mb-4 object-cover">
                            <h3 class="font-semibold text-lg">Andi Wijaya</h3>
                            <p class="text-gray-600">Lead Developer</p>
                        </div>
                    </div>
                    
                    <h2 class="text-2xl font-bold mb-6">Hubungi Kami</h2>
                    <div class="bg-gray-50 p-6 rounded-lg">
                        <p class="text-gray-700 mb-4">
                            Kami selalu terbuka untuk saran, pertanyaan, atau kolaborasi. Jangan ragu untuk menghubungi kami melalui:
                        </p>
                        <ul class="space-y-2">
                            <li class="flex items-center">
                                <i class="fas fa-envelope text-green-600 mr-3"></i>
                                <span>Email: info@harvestworld.com</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-phone text-green-600 mr-3"></i>
                                <span>Telepon: (0721) 123-4567</span>
                            </li>
                            <li class="flex items-center">
                                <i class="fas fa-map-marker-alt text-green-600 mr-3"></i>
                                <span>Alamat: Jl. Pertanian No. 123, Jakarta Selatan, Indonesia</span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php include 'includes/footer.php'; ?>
</body>
</html>

