<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ShopImageGenerator
{
    /**
     * Generate a default image for a shop with name and category
     *
     * @param string $shopName
     * @param string $categoryName
     * @param string|null $categoryIcon
     * @return string The path to the generated image
     */
    public function generateShopImage(string $shopName, string $categoryName, ?string $categoryIcon = null): string
    {
        // Image dimensions
        $width = 800;
        $height = 600;

        // Create image
        $image = imagecreatetruecolor($width, $height);

        // Generate a gradient background based on shop name hash
        $hash = crc32($shopName);
        $colors = $this->generateGradientColors($hash);
        
        // Create gradient background
        for ($y = 0; $y < $height; $y++) {
            $ratio = $y / $height;
            $r = (int)($colors['start'][0] + ($colors['end'][0] - $colors['start'][0]) * $ratio);
            $g = (int)($colors['start'][1] + ($colors['end'][1] - $colors['start'][1]) * $ratio);
            $b = (int)($colors['start'][2] + ($colors['end'][2] - $colors['start'][2]) * $ratio);
            $color = imagecolorallocate($image, $r, $g, $b);
            imageline($image, 0, $y, $width, $y, $color);
        }

        // Add overlay for better text visibility
        $overlay = imagecolorallocatealpha($image, 0, 0, 0, 40);
        imagefilledrectangle($image, 0, 0, $width, $height, $overlay);

        // Colors
        $white = imagecolorallocate($image, 255, 255, 255);
        $lightGray = imagecolorallocate($image, 240, 240, 240);

        // Add decorative elements
        $this->addDecorativeCircles($image, $colors);

        // Create a large centered circle with shop initial or icon
        $centerX = $width / 2;
        $centerY = $height / 2;
        
        // Draw large white circle background
        $circleBg = imagecolorallocate($image, 255, 255, 255);
        imagefilledellipse($image, $centerX, $centerY, 300, 300, $circleBg);
        
        // Draw decorative ring around the circle
        $ringColor = imagecolorallocatealpha($image, 255, 255, 255, 70);
        imageellipse($image, $centerX, $centerY, 320, 320, $ringColor);
        imageellipse($image, $centerX, $centerY, 322, 322, $ringColor);
        imageellipse($image, $centerX, $centerY, 324, 324, $ringColor);
        
        // Get first letter of shop name
        $firstLetter = mb_substr($shopName, 0, 1);
        
        // Draw the letter in a large, bold style
        $letterColor = imagecolorallocate($image, $colors['start'][0], $colors['start'][1], $colors['start'][2]);
        $this->drawLargeLetter($image, $firstLetter, $centerX, $centerY, $letterColor);
        
        // Add shop slug below the circle (English, clean text)
        $shopSlug = \Illuminate\Support\Str::slug($shopName);
        $slugColor = imagecolorallocate($image, 255, 255, 255);
        $this->drawCenteredText($image, strtoupper($shopSlug), $centerX, $centerY + 200, $slugColor, 3);

        // Add subtle branding at bottom
        $brandText = 'SENU Market';
        $this->drawCenteredText($image, $brandText, $width / 2, $height - 40, $lightGray, 2);

        // Save image
        $filename = 'shop_' . Str::slug($shopName) . '_' . time() . '.png';
        $directory = 'shops/generated';
        $path = $directory . '/' . $filename;

        // Ensure directory exists
        Storage::disk('public')->makeDirectory($directory);

        // Save to storage
        $fullPath = Storage::disk('public')->path($path);
        imagepng($image, $fullPath, 9);
        imagedestroy($image);

        return $path;
    }

    /**
     * Generate gradient colors based on hash using brand color scheme
     */
    private function generateGradientColors(int $hash): array
    {
        // Brand color schemes based on your CSS variables
        // --primary: #016B61, --secondary: #70B2B2, --accent: #9ECFD4, --light: #E5E9C5
        $schemes = [
            ['start' => [1, 107, 97], 'end' => [112, 178, 178]],      // Primary to Secondary
            ['start' => [112, 178, 178], 'end' => [158, 207, 212]],   // Secondary to Accent
            ['start' => [1, 107, 97], 'end' => [158, 207, 212]],      // Primary to Accent
            ['start' => [158, 207, 212], 'end' => [229, 233, 197]],   // Accent to Light
            ['start' => [1, 107, 97], 'end' => [229, 233, 197]],      // Primary to Light
            ['start' => [112, 178, 178], 'end' => [229, 233, 197]],   // Secondary to Light
            ['start' => [70, 130, 130], 'end' => [1, 107, 97]],       // Darker variation
            ['start' => [158, 207, 212], 'end' => [112, 178, 178]],   // Accent to Secondary
        ];

        $index = abs($hash) % count($schemes);
        return $schemes[$index];
    }

    /**
     * Add decorative circles to the background
     */
    private function addDecorativeCircles($image, array $colors): void
    {
        $width = imagesx($image);
        $height = imagesy($image);

        // Create semi-transparent circles
        $circleColor = imagecolorallocatealpha($image, 255, 255, 255, 110);

        // Large circle top-right
        imagefilledellipse($image, $width + 50, -50, 300, 300, $circleColor);

        // Medium circle bottom-left
        imagefilledellipse($image, -50, $height + 50, 250, 250, $circleColor);

        // Small circle middle-right
        imagefilledellipse($image, $width - 100, $height / 2, 150, 150, $circleColor);
    }

    /**
     * Draw large letter (first character of shop name)
     */
    private function drawLargeLetter($image, string $letter, int $x, int $y, int $color): void
    {
        $fontSize = 150;
        
        $fontPath = $this->findArabicFont();
        
        if ($fontPath && file_exists($fontPath)) {
            // Get text dimensions
            $bbox = imagettfbbox($fontSize, 0, $fontPath, $letter);
            $textWidth = abs($bbox[4] - $bbox[0]);
            $textHeight = abs($bbox[5] - $bbox[1]);
            
            // Center the letter
            $posX = (int)($x - ($textWidth / 2));
            $posY = (int)($y + ($textHeight / 2));
            
            // Draw shadow for depth
            $shadow = imagecolorallocatealpha($image, 0, 0, 0, 30);
            imagettftext($image, $fontSize, 0, $posX + 4, $posY + 4, $shadow, $fontPath, $letter);
            
            // Draw main letter
            imagettftext($image, $fontSize, 0, $posX, $posY, $color, $fontPath, $letter);
        } else {
            // Fallback: draw a simple graphic shape if no font
            $shapeColor = $color;
            // Draw a simple geometric shape as placeholder
            imagefilledellipse($image, $x, $y, 120, 120, $shapeColor);
        }
    }
    
    /**
     * Draw centered text with TrueType font support (supports Arabic)
     */
    private function drawCenteredText($image, string $text, int $x, int $y, int $color, int $fontSize = 5): void
    {
        // Convert fontSize (1-5) to actual pixel size for TTF
        $fontSizeMap = [
            1 => 14,
            2 => 18,
            3 => 24,
            4 => 32,
            5 => 48
        ];
        $ttfSize = $fontSizeMap[$fontSize] ?? 24;
        
        // Try to find an Arabic-compatible font
        $fontPath = $this->findArabicFont();
        
        if ($fontPath && file_exists($fontPath)) {
            // Use TrueType font for proper Arabic rendering
            $bbox = imagettfbbox($ttfSize, 0, $fontPath, $text);
            $textWidth = abs($bbox[4] - $bbox[0]);
            $textHeight = abs($bbox[5] - $bbox[1]);
            
            $posX = (int)($x - ($textWidth / 2));
            $posY = (int)($y + ($textHeight / 2));
            
            // Draw shadow
            $shadow = imagecolorallocatealpha($image, 0, 0, 0, 60);
            imagettftext($image, $ttfSize, 0, $posX + 2, $posY + 2, $shadow, $fontPath, $text);
            
            // Draw main text
            imagettftext($image, $ttfSize, 0, $posX, $posY, $color, $fontPath, $text);
        } else {
            // Fallback to built-in fonts (won't display Arabic properly, but won't crash)
            $fontSize = max(1, min(5, $fontSize));
            $textWidth = imagefontwidth($fontSize) * mb_strlen($text);
            $textHeight = imagefontheight($fontSize);
            
            $posX = (int)($x - ($textWidth / 2));
            $posY = (int)($y - ($textHeight / 2));
            
            $shadow = imagecolorallocatealpha($image, 0, 0, 0, 60);
            imagestring($image, $fontSize, $posX + 2, $posY + 2, $text, $shadow);
            imagestring($image, $fontSize, $posX, $posY, $text, $color);
        }
    }
    
    /**
     * Find an Arabic-compatible TrueType font
     */
    private function findArabicFont(): ?string
    {
        // Common Arabic font paths on different systems
        $possibleFonts = [
            // Windows fonts
            'C:/Windows/Fonts/arial.ttf',
            'C:/Windows/Fonts/arialuni.ttf',
            'C:/Windows/Fonts/tahoma.ttf',
            'C:/Windows/Fonts/tahomabd.ttf',
            'C:/Windows/Fonts/times.ttf',
            'C:/Windows/Fonts/calibri.ttf',
            // Linux fonts
            '/usr/share/fonts/truetype/dejavu/DejaVuSans.ttf',
            '/usr/share/fonts/truetype/liberation/LiberationSans-Regular.ttf',
            '/usr/share/fonts/truetype/noto/NotoSansArabic-Regular.ttf',
            '/usr/share/fonts/arabic/ae_Arab.ttf',
            // macOS fonts
            '/System/Library/Fonts/Supplemental/Arial Unicode.ttf',
            '/Library/Fonts/Arial.ttf',
            '/System/Library/Fonts/Helvetica.ttc',
        ];
        
        foreach ($possibleFonts as $font) {
            if (file_exists($font)) {
                return $font;
            }
        }
        
        return null;
    }

    /**
     * Generate multiple shop images (for array of images)
     *
     * @param string $shopName
     * @param string $categoryName
     * @param string|null $categoryIcon
     * @param int $count Number of images to generate
     * @return array Array of image paths
     */
    public function generateMultipleImages(string $shopName, string $categoryName, ?string $categoryIcon = null, int $count = 3): array
    {
        $images = [];
        
        for ($i = 0; $i < $count; $i++) {
            // Add variation to each image by appending a number
            $variantName = $shopName . ' ' . ($i + 1);
            $images[] = $this->generateShopImage($variantName, $categoryName, $categoryIcon);
        }

        return $images;
    }
}
