<footer class="bg-green-800 text-white py-8">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
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
                    <?php if (isset($_SESSION['user_id'])): ?>
                    <li><a href="profile.php" class="text-green-100 hover:text-white">Profil Saya</a></li>
                    <?php endif; ?>
                </ul>
            </div>
            
            <!-- Column 3: Contact -->
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

