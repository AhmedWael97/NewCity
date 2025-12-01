<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\ForumCategory;
use App\Models\ForumThread;
use App\Models\ForumPost;
use App\Models\User;
use App\Models\City;

class ForumSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get first city and user
        $city = City::first();
        $user = User::first();

        if (!$user) {
            $this->command->warn('No users found. Please create a user first.');
            return;
        }

        // Create forum categories
        $categories = [
            [
                'name' => 'نقاش عام',
                'slug' => 'general-discussion',
                'description' => 'مواضيع عامة ونقاشات متنوعة',
                'icon' => 'fas fa-comments',
                'color' => '#3490dc',
                'order' => 1,
            ],
            [
                'name' => 'تقييمات المتاجر',
                'slug' => 'shop-reviews',
                'description' => 'مناقشة وتقييم المتاجر المحلية',
                'icon' => 'fas fa-store',
                'color' => '#38c172',
                'order' => 2,
            ],
            [
                'name' => 'السوق المفتوح',
                'slug' => 'marketplace-discussion',
                'description' => 'نقاشات حول الإعلانات والمنتجات',
                'icon' => 'fas fa-shopping-cart',
                'color' => '#f6993f',
                'order' => 3,
            ],
            [
                'name' => 'الفعاليات المحلية',
                'slug' => 'local-events',
                'description' => 'مناقشة الفعاليات والأحداث المحلية',
                'icon' => 'fas fa-calendar-alt',
                'color' => '#9561e2',
                'order' => 4,
            ],
            [
                'name' => 'الخدمات والتوصيات',
                'slug' => 'services-recommendations',
                'description' => 'طلب واقتراح الخدمات المختلفة',
                'icon' => 'fas fa-handshake',
                'color' => '#e3342f',
                'order' => 5,
            ],
            [
                'name' => 'الشكاوى والاقتراحات',
                'slug' => 'feedback-suggestions',
                'description' => 'مشاركة آرائكم واقتراحاتكم لتحسين المنصة',
                'icon' => 'fas fa-lightbulb',
                'color' => '#ffed4e',
                'order' => 6,
            ],
        ];

        foreach ($categories as $categoryData) {
            $category = ForumCategory::create($categoryData);

            // Create 2-3 threads per category
            $threadsCount = rand(2, 3);
            for ($i = 1; $i <= $threadsCount; $i++) {
                $thread = $category->threads()->create([
                    'user_id' => $user->id,
                    'city_id' => $city?->id,
                    'title' => $this->getThreadTitle($category->slug, $i),
                    'body' => $this->getThreadBody($category->slug),
                    'is_approved' => true,
                    'status' => 'active',
                    'views_count' => rand(10, 500),
                    'approved_at' => now()->subDays(rand(1, 30)),
                    'last_activity_at' => now()->subHours(rand(1, 72)),
                ]);

                // Create 1-3 replies per thread
                $repliesCount = rand(1, 3);
                for ($j = 1; $j <= $repliesCount; $j++) {
                    ForumPost::create([
                        'forum_thread_id' => $thread->id,
                        'user_id' => $user->id,
                        'body' => $this->getPostBody($j),
                        'is_approved' => true,
                        'status' => 'active',
                        'helpful_count' => rand(0, 10),
                        'approved_at' => now()->subDays(rand(1, 25)),
                    ]);
                }
            }

            // Update counters
            $category->updateCounters();
        }

        $this->command->info('Forum seeded successfully!');
    }

    private function getThreadTitle($categorySlug, $index): string
    {
        $titles = [
            'general-discussion' => [
                'ما هي أفضل المطاعم في المنطقة؟',
                'نصائح للمقيمين الجدد في المدينة',
                'أماكن ترفيهية ننصح بزيارتها',
            ],
            'shop-reviews' => [
                'تجربتي مع محل الإلكترونيات الجديد',
                'أفضل محلات الملابس من حيث الجودة والسعر',
                'تقييم خدمة التوصيل في المتاجر المحلية',
            ],
            'marketplace-discussion' => [
                'نصائح للبيع والشراء الآمن',
                'كيفية تقييم المنتجات المستعملة قبل الشراء',
                'أسعار السوق الحالية للإلكترونيات',
            ],
            'local-events' => [
                'فعاليات نهاية الأسبوع القادم',
                'معارض وأسواق خيرية قادمة',
                'أنشطة عائلية في المدينة',
            ],
            'services-recommendations' => [
                'أبحث عن سباك محترف',
                'توصية لمكتب محاماة موثوق',
                'خدمات توصيل سريعة وموثوقة',
            ],
            'feedback-suggestions' => [
                'اقتراح: إضافة نظام تقييم للخدمات',
                'مشكلة في عرض الصور على الموبايل',
                'طلب: إمكانية البحث المتقدم',
            ],
        ];

        return $titles[$categorySlug][$index - 1] ?? 'موضوع رقم ' . $index;
    }

    private function getThreadBody($categorySlug): string
    {
        $bodies = [
            'general-discussion' => 'مرحباً بالجميع، أود أن أشارككم تجربتي وأستمع لآرائكم حول هذا الموضوع. أتطلع لنقاش مفيد وتبادل الخبرات.',
            'shop-reviews' => 'السلام عليكم، زرت هذا المحل مؤخراً وأردت مشاركة تجربتي معكم. الخدمة كانت جيدة بشكل عام والأسعار معقولة.',
            'marketplace-discussion' => 'أود أن أطرح هذا الموضوع للنقاش وأستمع لتجاربكم وآرائكم. ما هي نصائحكم في هذا الخصوص؟',
            'local-events' => 'يسعدني أن أشارككم معلومات عن هذا الحدث القادم. أتمنى أن يكون مفيداً للجميع.',
            'services-recommendations' => 'أبحث عن توصيات موثوقة في هذا المجال. هل لديكم اقتراحات أو تجارب تودون مشاركتها؟',
            'feedback-suggestions' => 'أود تقديم هذا الاقتراح لتحسين المنصة. أتمنى أخذه بعين الاعتبار.',
        ];

        return $bodies[$categorySlug] ?? 'هذا موضوع للنقاش العام. أتطلع لمشاركتكم وآرائكم.';
    }

    private function getPostBody($index): string
    {
        $bodies = [
            'شكراً على المشاركة! معلومات مفيدة جداً.',
            'أتفق معك تماماً. لدي تجربة مشابهة في هذا الخصوص.',
            'نقطة مهمة! هل يمكنك مشاركة المزيد من التفاصيل؟',
            'تجربة رائعة، شكراً للمشاركة.',
            'معلومات قيمة، سأحاول تطبيق هذه النصائح.',
        ];

        return $bodies[$index - 1] ?? 'رد على الموضوع رقم ' . $index;
    }
}
