<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Rating;
use App\Models\Shop;
use App\Models\User;

class RatingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first shop and user for testing
        $shop = Shop::first();
        $user = User::where('user_type', 'regular')->first();
        
        if (!$shop || !$user) {
            $this->command->info('No shop or user found. Please seed shops and users first.');
            return;
        }

        // Create sample ratings
        $ratings = [
            [
                'user_id' => $user->id,
                'shop_id' => $shop->id,
                'rating' => 5,
                'comment' => 'خدمة ممتازة ومنتجات عالية الجودة. أنصح بشدة بالتسوق من هذا المتجر.',
                'is_verified' => true,
                'helpful_votes' => []
            ],
            [
                'user_id' => $user->id,
                'shop_id' => $shop->id,
                'rating' => 4,
                'comment' => 'تعامل راقي وأسعار مناسبة. المتجر نظيف ومنظم بشكل جميل.',
                'is_verified' => false,
                'helpful_votes' => []
            ],
            [
                'user_id' => $user->id,
                'shop_id' => $shop->id,
                'rating' => 5,
                'comment' => 'سرعة في الخدمة ودقة في المواعيد. تجربة تسوق رائعة.',
                'is_verified' => true,
                'helpful_votes' => []
            ],
            [
                'user_id' => $user->id,
                'shop_id' => $shop->id,
                'rating' => 3,
                'comment' => 'جيد بشكل عام لكن يحتاج لتحسين في بعض الجوانب.',
                'is_verified' => false,
                'helpful_votes' => []
            ],
            [
                'user_id' => $user->id,
                'shop_id' => $shop->id,
                'rating' => 4,
                'comment' => null, // Rating without comment
                'is_verified' => false,
                'helpful_votes' => []
            ]
        ];

        // Create ratings but avoid duplicate user-shop combinations
        foreach ($ratings as $index => $ratingData) {
            // Use different user for each rating to avoid unique constraint
            $users = User::where('user_type', 'regular')->skip($index)->take(1)->get();
            if ($users->count() > 0) {
                $ratingData['user_id'] = $users->first()->id;
                Rating::create($ratingData);
            }
        }

        // Update shop rating
        $shop->updateRating();

        $this->command->info('Sample ratings created successfully!');
        $this->command->info("Shop rating updated to: {$shop->fresh()->rating}");
        $this->command->info("Total ratings: {$shop->fresh()->review_count}");
    }
}
