<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shop;
use App\Models\City;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Str;

class ShopSeeder extends Seeder
{
    /**
     * Run the database seeds.
     * Creates sample shops with realistic data.
     */
    public function run(): void
    {
        // Get existing data - ONLY city with ID 4
        $city = City::find(4);
        $categories = Category::all();
        $shopOwners = User::where('user_type', User::TYPE_SHOP_OWNER)->get();
        
        // Validate required data exists
        if (!$city) {
            $this->command->error('❌ City with ID 4 not found. Please seed cities first.');
            return;
        }
        
        if ($categories->isEmpty()) {
            $this->command->error('❌ No categories found. Please seed categories first.');
            return;
        }
        
        if ($shopOwners->isEmpty()) {
            $this->command->warn('⚠️  No shop owners found. Creating sample shop owners...');
            $shopOwners = $this->createShopOwners(10);
        }
        
        $this->command->info('🏪 Starting to seed shops...');
        
        // Shop names by category (you can customize these)
        $shopNameTemplates = [
            'Restaurant' => ['The Golden', 'Royal', 'Grand', 'Premium', 'Fresh', 'Tasty'],
            'Clothing' => ['Fashion', 'Style', 'Elite', 'Trendy', 'Chic', 'Modern'],
            'Electronics' => ['Tech', 'Digital', 'Smart', 'Future', 'Innovation'],
            'Pharmacy' => ['Health', 'Care', 'Life', 'Wellness', 'Medical'],
            'Supermarket' => ['Fresh', 'Market', 'Super', 'Daily', 'Express'],
            'Cafe' => ['Coffee', 'Brew', 'Bean', 'Aroma', 'Cafe'],
            'Beauty' => ['Beauty', 'Glow', 'Radiant', 'Salon', 'Spa'],
            'Fitness' => ['Fit', 'Gym', 'Active', 'Power', 'Strong'],
        ];
        
        // Determine how many shops to create
        $numberOfShops = min($shopOwners->count() * 2, 800); // Each owner can have 1-2 shops, max 800
        
        $this->command->getOutput()->progressStart($numberOfShops);
        
        for ($i = 0; $i < $numberOfShops; $i++) {
            // Use only city ID 4
            $category = $categories->random();
            $owner = $shopOwners->random();
            
            // Generate shop name
            $categoryName = $category->name ?? 'Store';
            $templateKey = array_rand($shopNameTemplates);
            $prefixes = $shopNameTemplates[$templateKey];
            $prefix = $prefixes[array_rand($prefixes)];
            $shopName = "{$prefix} {$categoryName} " . fake()->numberBetween(1, 99);
            
            // Determine shop status
            $statuses = [
                Shop::STATUS_APPROVED => 60,    // 60% approved
                Shop::STATUS_PENDING => 25,     // 25% pending
                Shop::STATUS_REJECTED => 10,    // 10% rejected
                Shop::STATUS_SUSPENDED => 5,    // 5% suspended
            ];
            $status = $this->weightedRandom($statuses);
            
            // Set verification based on status
            $isVerified = $status === Shop::STATUS_APPROVED;
            $isActive = in_array($status, [Shop::STATUS_APPROVED, Shop::STATUS_PENDING]);
            $isFeatured = $isVerified && fake()->boolean(20); // 20% of verified shops are featured
            
            // Create opening hours
            $openingHours = [
                'monday' => ['open' => '09:00', 'close' => '18:00', 'is_closed' => false],
                'tuesday' => ['open' => '09:00', 'close' => '18:00', 'is_closed' => false],
                'wednesday' => ['open' => '09:00', 'close' => '18:00', 'is_closed' => false],
                'thursday' => ['open' => '09:00', 'close' => '18:00', 'is_closed' => false],
                'friday' => ['open' => '09:00', 'close' => '18:00', 'is_closed' => false],
                'saturday' => ['open' => '10:00', 'close' => '16:00', 'is_closed' => false],
                'sunday' => ['open' => '10:00', 'close' => '16:00', 'is_closed' => true],
            ];
            
            // Generate shop images and store them
            $shopImages = $this->generateShopImages($category->slug ?? 'general', fake()->numberBetween(1, 4));
            
            // Create shop
            $shop = Shop::create([
                'user_id' => $owner->id,
                'city_id' => $city->id,
                'category_id' => $category->id,
                'name' => $shopName,
                'slug' => Str::slug($shopName) . '-' . Str::random(6),
                'description' => fake()->paragraph(3),
                'address' => fake()->address(),
                'latitude' => fake()->latitude(24, 32),  // Egypt latitude range
                'longitude' => fake()->longitude(25, 37), // Egypt longitude range
                'phone' => fake()->phoneNumber(),
                'email' => fake()->optional(0.7)->safeEmail(),
                'website' => fake()->optional(0.3)->url(),
                'images' => $shopImages,
                'opening_hours' => $openingHours,
                'rating' => $isVerified ? fake()->randomFloat(2, 3.5, 5.0) : 0,
                'review_count' => $isVerified ? fake()->numberBetween(0, 150) : 0,
                'is_featured' => $isFeatured,
                'featured_priority' => $isFeatured ? fake()->numberBetween(1, 10) : 0,
                'featured_until' => $isFeatured ? now()->addDays(fake()->numberBetween(30, 90)) : null,
                'is_verified' => $isVerified,
                'is_active' => $isActive,
                'verified_at' => $isVerified ? now()->subDays(fake()->numberBetween(1, 365)) : null,
                'verification_notes' => $status === Shop::STATUS_REJECTED ? 'Missing required documents' : null,
                'status' => $status,
                'created_at' => now()->subDays(fake()->numberBetween(1, 365)),
                'updated_at' => now()->subDays(fake()->numberBetween(0, 30)),
            ]);
            
            $this->command->getOutput()->progressAdvance();
        }
        
        $this->command->getOutput()->progressFinish();
        
        // Display summary
        $total = Shop::count();
        $approved = Shop::where('status', Shop::STATUS_APPROVED)->count();
        $pending = Shop::where('status', Shop::STATUS_PENDING)->count();
        $rejected = Shop::where('status', Shop::STATUS_REJECTED)->count();
        $suspended = Shop::where('status', Shop::STATUS_SUSPENDED)->count();
        $featured = Shop::where('is_featured', true)->count();
        
        $this->command->newLine();
        $this->command->info("✅ Successfully seeded {$numberOfShops} shops!");
        $this->command->table(
            ['Status', 'Count'],
            [
                ['Total Shops', $total],
                ['Approved', $approved],
                ['Pending', $pending],
                ['Rejected', $rejected],
                ['Suspended', $suspended],
                ['Featured', $featured],
            ]
        );
    }
    
    /**
     * Create shop owner users.
     */
    private function createShopOwners(int $count): \Illuminate\Database\Eloquent\Collection
    {
        $owners = collect();
        
        for ($i = 1; $i <= $count; $i++) {
            $owner = User::create([
                'name' => fake()->name(),
                'email' => 'shopowner' . $i . '@example.com',
                'password' => bcrypt('password'),
                'user_type' => User::TYPE_SHOP_OWNER,
                'phone' => fake()->phoneNumber(),
                'city_id' => 4, // Always use city ID 4
                'is_verified' => true,
                'is_active' => true,
                'email_verified_at' => now(),
            ]);
            
            $owners->push($owner);
        }
        
        $this->command->info("✅ Created {$count} shop owners.");
        return $owners;
    }
    
    /**
     * Generate shop images and store them in the correct path.
     */
    private function generateShopImages(string $categorySlug, int $count): array
    {
        $images = [];
        $storagePath = storage_path('app/public/shops');
        
        // Create directory if it doesn't exist
        if (!file_exists($storagePath)) {
            mkdir($storagePath, 0755, true);
        }
        
        // Category-based image dimensions and colors
        $imageSettings = [
            'restaurant' => ['width' => 1200, 'height' => 800, 'colors' => ['#FF6B6B', '#FFD93D', '#6BCF7F']],
            'cafe' => ['width' => 1200, 'height' => 800, 'colors' => ['#8B4513', '#D2691E', '#F4A460']],
            'clothing' => ['width' => 1000, 'height' => 1200, 'colors' => ['#FFB6C1', '#FF69B4', '#FF1493']],
            'electronics' => ['width' => 1200, 'height' => 900, 'colors' => ['#4169E1', '#1E90FF', '#00BFFF']],
            'pharmacy' => ['width' => 1200, 'height' => 800, 'colors' => ['#32CD32', '#00FF00', '#ADFF2F']],
            'supermarket' => ['width' => 1200, 'height' => 900, 'colors' => ['#FFA500', '#FF8C00', '#FF4500']],
            'beauty' => ['width' => 1000, 'height' => 1200, 'colors' => ['#FF69B4', '#FFB6C1', '#FFC0CB']],
            'fitness' => ['width' => 1200, 'height' => 800, 'colors' => ['#FF4500', '#FF6347', '#DC143C']],
        ];
        
        $settings = $imageSettings[$categorySlug] ?? ['width' => 1200, 'height' => 800, 'colors' => ['#888888', '#AAAAAA', '#CCCCCC']];
        
        for ($i = 0; $i < $count; $i++) {
            $filename = uniqid('shop_' . $categorySlug . '_') . '.jpg';
            $filepath = $storagePath . '/' . $filename;
            
            // Create a simple colored image using GD
            if (extension_loaded('gd')) {
                $image = imagecreatetruecolor($settings['width'], $settings['height']);
                $color = $settings['colors'][array_rand($settings['colors'])];
                list($r, $g, $b) = sscanf($color, "#%02x%02x%02x");
                $bgColor = imagecolorallocate($image, $r, $g, $b);
                imagefill($image, 0, 0, $bgColor);
                
                // Add text overlay
                $textColor = imagecolorallocate($image, 255, 255, 255);
                $text = strtoupper($categorySlug) . ' #' . ($i + 1);
                imagestring($image, 5, (int)($settings['width'] / 2 - 50), (int)($settings['height'] / 2), $text, $textColor);
                
                imagejpeg($image, $filepath, 85);
                imagedestroy($image);
            } else {
                // If GD is not available, create an empty file
                file_put_contents($filepath, '');
            }
            
            // Store the relative path that will be used in the database
            $images[] = 'shops/' . $filename;
        }
        
        return $images;
    }
    
    /**
     * Select a random key based on weighted probabilities.
     */
    private function weightedRandom(array $weights): string
    {
        $random = fake()->numberBetween(1, 100);
        $cumulative = 0;
        
        foreach ($weights as $key => $weight) {
            $cumulative += $weight;
            if ($random <= $cumulative) {
                return $key;
            }
        }
        
        return array_key_first($weights);
    }
}
