<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
$isLoggedIn = isset($_SESSION['user_id']);
?>

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

            <!-- Desktop Navigation Links - Centered -->
            <nav class="hidden md:flex items-center justify-center flex-1 space-x-8">
                <a href="plants.php" class="text-gray-700 hover:text-green-600 transition-colors">Tanaman</a>
                <a href="artikel.php" class="text-gray-700 hover:text-green-600 transition-colors">Artikel</a>
                <a href="forum.php" class="text-gray-700 hover:text-green-600 transition-colors">Forum</a>
                <a href="about.php" class="text-gray-700 hover:text-green-600 transition-colors">Tentang Kami</a>
            </nav>

            <!-- User Menu / Login Buttons -->
            <div class="flex items-center space-x-4">
                <?php if ($isLoggedIn): ?>
                    <div class="relative group">
                        <button class="flex items-center space-x-2 focus:outline-none" id="profile-menu-button">
                            <img 
                                src="<?php echo htmlspecialchars($_SESSION['profile_image'] ?? '/images/default-avatar.png'); ?>" 
                                alt="Profile" 
                                class="w-8 h-8 rounded-full object-cover border border-gray-200"
                            >
                            <span class="text-gray-700"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                            <i class="fas fa-chevron-down text-xs text-gray-500"></i>
                        </button>
                        
                        <div id="profile-dropdown" class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 hidden z-50">
                            <a href="profile.php" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-user mr-2"></i> Profil Saya
                            </a>
                            <a href="profile.php?tab=settings" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                <i class="fas fa-cog mr-2"></i> Pengaturan
                            </a>
                            <div class="border-t border-gray-100 my-1"></div>
                            <a href="auth/logout.php" class="block px-4 py-2 text-sm text-red-600 hover:bg-gray-100">
                                <i class="fas fa-sign-out-alt mr-2"></i> Logout
                            </a>
                        </div>
                    </div>
                <?php else: ?>
                    <a href="auth/login.php" class="text-gray-700 hover:text-green-600 transition-colors">Masuk</a>
                    <a href="auth/register.php" class="bg-green-600 text-white px-4 py-2 rounded-md hover:bg-green-700 transition-colors">Daftar</a>
                <?php endif; ?>
            </div>

            <!-- Mobile Menu Button -->
            <div class="md:hidden ml-4">
                <button id="mobile-menu-button" class="text-gray-700 focus:outline-none">
                    <i class="fas fa-bars text-xl"></i>
                </button>
            </div>
        </div>
    </div>
    
    <!-- Mobile Menu -->
    <div id="mobile-menu" class="md:hidden bg-white shadow-md hidden">
        <div class="px-4 py-3 space-y-1">
            <a href="plants.php" class="block py-2 px-4 text-gray-700 hover:bg-gray-100 rounded-md">
                <i class="fas fa-leaf mr-2"></i> Tanaman
            </a>
            <a href="artikel.php" class="block py-2 px-4 text-gray-700 hover:bg-gray-100 rounded-md">
                <i class="fas fa-newspaper mr-2"></i> Artikel
            </a>
            <a href="forum.php" class="block py-2 px-4 text-gray-700 hover:bg-gray-100 rounded-md">
                <i class="fas fa-comments mr-2"></i> Forum
            </a>
            <a href="about.php" class="block py-2 px-4 text-gray-700 hover:bg-gray-100 rounded-md">
                <i class="fas fa-info-circle mr-2"></i> Tentang Kami
            </a>
            
            <div class="border-t border-gray-200 my-2"></div>
            
            <?php if ($isLoggedIn): ?>
                <a href="profile.php" class="block py-2 px-4 text-gray-700 hover:bg-gray-100 rounded-md">
                    <i class="fas fa-user mr-2"></i> Profil Saya
                </a>
                <a href="profile.php?tab=settings" class="block py-2 px-4 text-gray-700 hover:bg-gray-100 rounded-md">
                    <i class="fas fa-cog mr-2"></i> Pengaturan
                </a>
                <a href="auth/logout.php" class="block py-2 px-4 text-red-600 hover:bg-red-50 rounded-md">
                    <i class="fas fa-sign-out-alt mr-2"></i> Logout
                </a>
            <?php else: ?>
                <a href="auth/login.php" class="block py-2 px-4 text-gray-700 hover:bg-gray-100 rounded-md">
                    <i class="fas fa-sign-in-alt mr-2"></i> Masuk
                </a>
                <a href="auth/register.php" class="block py-2 px-4 bg-green-600 text-white hover:bg-green-700 rounded-md">
                    <i class="fas fa-user-plus mr-2"></i> Daftar
                </a>
            <?php endif; ?>
        </div>
    </div>
</header>

<script>
    // Mobile menu toggle
    document.addEventListener('DOMContentLoaded', function() {
        const mobileMenuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        
        if (mobileMenuButton && mobileMenu) {
            mobileMenuButton.addEventListener('click', function() {
                mobileMenu.classList.toggle('hidden');
            });
        }
        
        // Profile dropdown toggle for desktop
        const profileMenuButton = document.getElementById('profile-menu-button');
        const profileDropdown = document.getElementById('profile-dropdown');
        
        if (profileMenuButton && profileDropdown) {
            // Toggle dropdown on click
            profileMenuButton.addEventListener('click', function(e) {
                e.stopPropagation();
                profileDropdown.classList.toggle('hidden');
            });
            
            // Close dropdown when clicking elsewhere
            document.addEventListener('click', function(e) {
                if (!profileMenuButton.contains(e.target) && !profileDropdown.contains(e.target)) {
                    profileDropdown.classList.add('hidden');
                }
            });
            
            // Also handle hover for desktop
            const profileContainer = profileMenuButton.parentElement;
            
            profileContainer.addEventListener('mouseenter', function() {
                profileDropdown.classList.remove('hidden');
            });
            
            profileContainer.addEventListener('mouseleave', function() {
                // Small delay to allow moving to dropdown
                setTimeout(function() {
                    if (!profileDropdown.matches(':hover')) {
                        profileDropdown.classList.add('hidden');
                    }
                }, 100);
            });
            
            profileDropdown.addEventListener('mouseleave', function() {
                profileDropdown.classList.add('hidden');
            });
        }
    });
</script>

