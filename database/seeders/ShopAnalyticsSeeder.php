<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Shop;
use App\Models\User;
use App\Models\ShopAnalytics;
use Carbon\Carbon;

class ShopAnalyticsSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $shops = Shop::limit(20)->get();
        $users = User::limit(50)->get();
        
        if ($shops->isEmpty()) {
            $this->command->info('No shops found. Please run shop seeders first.');
            return;
        }

        // Generate analytics data for the last 30 days
        for ($i = 30; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i);
            
            // Generate random analytics for each day
            $dailyViews = rand(50, 200);
            
            for ($j = 0; $j < $dailyViews; $j++) {
                $shop = $shops->random();
                $user = $users->random();
                
                // Generate random IP addresses
                $ip = rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255);
                
                ShopAnalytics::create([
                    'shop_id' => $shop->id,
                    'user_id' => rand(1, 3) == 1 ? $user->id : null, // 1/3 chance of logged-in user
                    'event_type' => $this->getRandomEventType(),
                    'user_ip' => $ip,
                    'user_agent' => $this->getRandomUserAgent(),
                    'referrer' => $this->getRandomReferrer(),
                    'metadata' => $this->getRandomMetadata(),
                    'created_at' => $date->addSeconds(rand(0, 86400)) // Random time during the day
                ]);
            }
            
            // Generate some search events
            $dailySearches = rand(20, 80);
            for ($k = 0; $k < $dailySearches; $k++) {
                $user = $users->random();
                $shop = $shops->random(); // Assign a random shop for search tracking
                $ip = rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255) . '.' . rand(1, 255);
                
                ShopAnalytics::create([
                    'shop_id' => $shop->id, // Use a shop for search tracking
                    'user_id' => rand(1, 2) == 1 ? $user->id : null,
                    'event_type' => 'search',
                    'user_ip' => $ip,
                    'user_agent' => $this->getRandomUserAgent(),
                    'referrer' => $this->getRandomReferrer(),
                    'metadata' => [
                        'search_term' => $this->getRandomSearchTerm(),
                        'results_count' => rand(0, 50)
                    ],
                    'created_at' => $date->addSeconds(rand(0, 86400))
                ]);
            }
        }

        $this->command->info('Analytics data seeded successfully!');
    }

    private function getRandomEventType(): string
    {
        $events = ['shop_view', 'contact_click', 'phone_click', 'website_click', 'direction_click'];
        return $events[array_rand($events)];
    }

    private function getRandomUserAgent(): string
    {
        $agents = [
            'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Mozilla/5.0 (iPhone; CPU iPhone OS 14_6 like Mac OS X) AppleWebKit/605.1.15 (KHTML, like Gecko) Version/14.0 Mobile/15E148 Safari/604.1',
            'Mozilla/5.0 (Android 11; Mobile; rv:68.0) Gecko/68.0 Firefox/88.0',
            'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36'
        ];
        return $agents[array_rand($agents)];
    }

    private function getRandomReferrer(): ?string
    {
        $referrers = [
            'https://www.google.com',
            'https://www.facebook.com',
            'https://www.instagram.com',
            'https://www.twitter.com',
            'direct',
            null
        ];
        return $referrers[array_rand($referrers)];
    }

    private function getRandomSearchTerm(): string
    {
        $terms = [
            'مطعم',
            'مقهى',
            'صيدلية',
            'محل ملابس',
            'سوبر ماركت',
            'مخبز',
            'حلاق',
            'ورشة سيارات',
            'مكتبة',
            'عيادة طبيب',
            'جيم',
            'محل أحذية',
            'متجر إلكترونيات',
            'مطعم فاست فود',
            'محل حلويات'
        ];
        return $terms[array_rand($terms)];
    }

    private function getRandomMetadata(): array
    {
        $deviceTypes = ['desktop', 'mobile', 'tablet'];
        $sources = ['google', 'facebook', 'instagram', 'direct', 'referral'];
        
        return [
            'device_type' => $deviceTypes[array_rand($deviceTypes)],
            'source' => $sources[array_rand($sources)],
            'screen_resolution' => rand(1, 2) == 1 ? '1920x1080' : '375x667',
            'browser' => rand(1, 3) == 1 ? 'Chrome' : (rand(1, 2) == 1 ? 'Safari' : 'Firefox')
        ];
    }
}
