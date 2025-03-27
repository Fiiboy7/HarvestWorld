<?php
// This script downloads placeholder images for the HarvestWorld website

// Create images directory if it doesn't exist
$imagesDir = __DIR__ . '/../images';
if (!file_exists($imagesDir)) {
    mkdir($imagesDir, 0777, true);
}

// Plant images to download
$plantImages = [
    'tomat.jpg' => 'https://source.unsplash.com/random/800x800/?tomato',
    'cabai-rawit.jpg' => 'https://source.unsplash.com/random/800x800/?chili',
    'bayam.jpg' => 'https://source.unsplash.com/random/800x800/?spinach',
    'kangkung.jpg' => 'https://source.unsplash.com/random/800x800/?water+spinach',
    'jeruk-nipis.jpg' => 'https://source.unsplash.com/random/800x800/?lime',
    'mangga.jpg' => 'https://source.unsplash.com/random/800x800/?mango',
    'jahe.jpg' => 'https://source.unsplash.com/random/800x800/?ginger',
    'kunyit.jpg' => 'https://source.unsplash.com/random/800x800/?turmeric',
    'lidah-buaya.jpg' => 'https://source.unsplash.com/random/800x800/?aloe+vera',
    'anggrek.jpg' => 'https://source.unsplash.com/random/800x800/?orchid',
];

// Other images to download
$otherImages = [
    'hero-bg.jpg' => 'https://source.unsplash.com/random/1920x1080/?farm',
    'about-hero.jpg' => 'https://source.unsplash.com/random/1920x1080/?agriculture',
    'team-1.jpg' => 'https://source.unsplash.com/random/400x400/?man+portrait',
    'team-2.jpg' => 'https://source.unsplash.com/random/400x400/?woman+portrait',
    'team-3.jpg' => 'https://source.unsplash.com/random/400x400/?man+portrait',
];

// Download plant images
echo "Downloading plant images...\n";
foreach ($plantImages as $filename => $url) {
    $filepath = $imagesDir . '/' . $filename;
    if (!file_exists($filepath)) {
        echo "Downloading $filename...\n";
        file_put_contents($filepath, file_get_contents($url));
    } else {
        echo "$filename already exists.\n";
    }
}

// Download other images
echo "\nDownloading other images...\n";
foreach ($otherImages as $filename => $url) {
    $filepath = $imagesDir . '/' . $filename;
    if (!file_exists($filepath)) {
        echo "Downloading $filename...\n";
        file_put_contents($filepath, file_get_contents($url));
    } else {
        echo "$filename already exists.\n";
    }
}

// Create default avatar
$defaultAvatarPath = $imagesDir . '/default-avatar.png';
if (!file_exists($defaultAvatarPath)) {
    echo "\nCreating default avatar...\n";
    
    // Create a 200x200 image
    $image = imagecreatetruecolor(200, 200);
    
    // Colors
    $bgColor = imagecolorallocate($image, 240, 240, 240);
    $textColor = imagecolorallocate($image, 100, 100, 100);
    
    // Fill the background
    imagefilledrectangle($image, 0, 0, 199, 199, $bgColor);
    
    // Add text
    $text = 'User';
    $font = 5; // Built-in font
    $textWidth = imagefontwidth($font) * strlen($text);
    $textHeight = imagefontheight($font);
    $x = (200 - $textWidth) / 2;
    $y = (200 - $textHeight) / 2;
    imagestring($image, $font, $x, $y, $text, $textColor);
    
    // Save the image
    imagepng($image, $defaultAvatarPath);
    imagedestroy($image);
} else {
    echo "\nDefault avatar already exists.\n";
}

echo "\nAll images have been downloaded successfully!\n";
?>

