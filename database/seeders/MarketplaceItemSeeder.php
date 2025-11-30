<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MarketplaceItem;
use App\Models\User;
use App\Models\City;
use App\Models\Category;
use App\Models\MarketplaceSponsorship;

class MarketplaceItemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Sample items data
        $items = [
            [
                'title' => 'iPhone 13 Pro Max مستعمل بحالة ممتازة',
                'description' => 'هاتف iPhone 13 Pro Max 256GB لون أزرق سييرا، استخدام شخصي نظيف جداً، بدون خدوش، البطارية 95%، معاه العلبة الأصلية والشاحن الأصلي، جهاز ما فيهوش أي مشاكل، للجادين فقط.',
                'price' => 22000,
                'condition' => 'like_new',
                'is_negotiable' => true,
                'status' => 'active',
            ],
            [
                'title' => 'لاب توب Dell XPS 15 للجرافيك والبرمجة',
                'description' => 'لاب توب Dell XPS 15 حالة ممتازة، معالج Intel Core i7 الجيل العاشر، رام 16GB، هارد SSD 512GB، كارت شاشة NVIDIA GTX 1650، مناسب للجرافيك والبرمجة والألعاب، معاه الشاحن الأصلي والشنطة.',
                'price' => 18500,
                'condition' => 'good',
                'is_negotiable' => true,
                'status' => 'active',
            ],
            [
                'title' => 'سامسونج جالاكسي S22 Ultra جديد مش مفتوح',
                'description' => 'Samsung Galaxy S22 Ultra 256GB جديد تماماً لم يستخدم، لون أسود فانتوم، ضمان الوكيل سنة كاملة، معاه الكرتونة الأصلية وجميع الملحقات، الجهاز لسه مش مفتوح الشريط الأصلي موجود.',
                'price' => 24000,
                'condition' => 'new',
                'is_negotiable' => false,
                'status' => 'active',
            ],
            [
                'title' => 'بلايستيشن 5 مع يدين و5 ألعاب',
                'description' => 'PlayStation 5 استخدام بسيط جداً، معاه يدين أصليين، و5 ألعاب (FIFA 23، COD Modern Warfare، Spider-Man، God of War، Uncharted)، جميع الكابلات الأصلية موجودة، حالة ممتازة كأنه جديد.',
                'price' => 15000,
                'condition' => 'like_new',
                'is_negotiable' => true,
                'status' => 'active',
            ],
            [
                'title' => 'دراجة هوائية رياضية جبلية',
                'description' => 'دراجة هوائية جبلية ماركة Giant موديل 2023، مقاس 29 بوصة، 21 سرعة، استخدام خفيف، حالة ممتازة جداً، معاها مضخة هواء وقفل أمان، مناسبة للطرق الوعرة والرحلات.',
                'price' => 4500,
                'condition' => 'good',
                'is_negotiable' => true,
                'status' => 'active',
            ],
            [
                'title' => 'ثلاجة سامسونج 16 قدم نوفروست',
                'description' => 'ثلاجة Samsung 16 قدم نوفروست، لون سيلفر، حالة ممتازة جداً، اقتصادية في استهلاك الكهرباء، الفريزر كبير ومساحات تخزين واسعة، نظيفة جداً وشغالة بكفاءة عالية.',
                'price' => 7500,
                'condition' => 'good',
                'is_negotiable' => true,
                'status' => 'active',
            ],
            [
                'title' => 'طقم صالون مودرن 3+2+1 جديد',
                'description' => 'طقم صالون مودرن جديد لم يستخدم، 3 كراسي + 2 كراسي + كرسي واحد، قماش تركي عالي الجودة، لون بيج وبني، تصميم عصري أنيق، مريح جداً ومناسب للمساحات الكبيرة والصغيرة.',
                'price' => 12000,
                'condition' => 'new',
                'is_negotiable' => true,
                'status' => 'active',
            ],
            [
                'title' => 'ساعة Apple Watch Series 7 GPS',
                'description' => 'Apple Watch Series 7 مقاس 45mm، GPS، لون ميدنايت، حالة ممتازة جداً، معاها السير الأصلي + سير إضافي رياضي، الشاحن الأصلي، البطارية ممتازة، بدون أي خدوش.',
                'price' => 6500,
                'condition' => 'like_new',
                'is_negotiable' => true,
                'status' => 'active',
            ],
            [
                'title' => 'كاميرا Canon EOS 90D احترافية',
                'description' => 'Canon EOS 90D كاميرا احترافية للتصوير الفوتوغرافي والفيديو، معاها عدسة 18-135mm، حالة ممتازة، استخدام محترف بسيط، معاها الشنطة الأصلية، كارت ميموري 64GB، بطاريتين، الشاحن.',
                'price' => 16000,
                'condition' => 'like_new',
                'is_negotiable' => true,
                'status' => 'active',
            ],
            [
                'title' => 'مكتبة خشب زان طبيعي 4 أدراج',
                'description' => 'مكتبة خشب زان طبيعي 100%، 4 أدراج واسعة، تصميم كلاسيكي فاخر، حالة ممتازة جداً، مناسبة للمكتب أو غرفة المكتب المنزلي، صناعة دمياط، خشب صلب ومتين.',
                'price' => 5500,
                'condition' => 'good',
                'is_negotiable' => true,
                'status' => 'pending',
            ],
            [
                'title' => 'دراجة نارية هوندا 150cc موديل 2022',
                'description' => 'دراجة نارية Honda 150cc موديل 2022، استخدام قليل جداً 5000 كيلو فقط، رخصة سارية، حالة ممتازة كأنها جديدة، صيانة دورية منتظمة، لون أسود، اقتصادية جداً في البنزين.',
                'price' => 22000,
                'condition' => 'like_new',
                'is_negotiable' => true,
                'status' => 'pending',
            ],
            [
                'title' => 'تكييف كاريير 2.25 حصان بارد ساخن',
                'description' => 'تكييف Carrier 2.25 حصان بارد وساخن، استخدام موسم واحد فقط، حالة ممتازة جداً، موفر للكهرباء، شغال بكفاءة عالية، التركيب مجاناً في نفس المنطقة، ضمان من البائع شهر.',
                'price' => 8500,
                'condition' => 'like_new',
                'is_negotiable' => true,
                'status' => 'pending',
            ],
        ];

        // Get first user, city, and category
        $user = User::first();
        $cities = City::where('is_active', true)->get();
        $categories = Category::where('is_active', true)->get();

        if (!$user || $cities->isEmpty() || $categories->isEmpty()) {
            $this->command->warn('⚠️  Please ensure you have at least one user, city, and category in the database.');
            return;
        }

        foreach ($items as $index => $itemData) {
            // Randomly select city and category
            $city = $cities->random();
            $category = $categories->random();

            // Create sample image URLs (using placeholder images)
            $images = [
                'https://via.placeholder.com/800x600/4299e1/ffffff?text=' . urlencode($itemData['title']),
                'https://via.placeholder.com/800x600/48bb78/ffffff?text=Image+2',
                'https://via.placeholder.com/800x600/ed8936/ffffff?text=Image+3',
            ];

            // Create the item
            $item = MarketplaceItem::create([
                'user_id' => $user->id,
                'city_id' => $city->id,
                'category_id' => $category->id,
                'title' => $itemData['title'],
                'description' => $itemData['description'],
                'price' => $itemData['price'],
                'condition' => $itemData['condition'],
                'is_negotiable' => $itemData['is_negotiable'],
                'status' => $itemData['status'],
                'contact_phone' => $user->phone ?? '01234567890',
                'contact_whatsapp' => $user->phone ?? '01234567890',
                'images' => $images,
                'max_views' => 50,
                'view_count' => rand(5, 45),
                'contact_count' => rand(0, 10),
            ]);

            // Approve active items
            if ($itemData['status'] === 'active') {
                $item->approve();
            }

            // Sponsor some random active items
            if ($itemData['status'] === 'active' && $index % 3 === 0) {
                $packages = MarketplaceSponsorship::packages();
                $packageTypes = array_keys($packages);
                $selectedPackage = $packageTypes[array_rand($packageTypes)];
                $packageData = $packages[$selectedPackage];

                $sponsorship = MarketplaceSponsorship::create([
                    'marketplace_item_id' => $item->id,
                    'user_id' => $user->id,
                    'package_type' => $selectedPackage,
                    'duration_days' => $packageData['duration_days'],
                    'price_paid' => $packageData['price'],
                    'views_boost' => $packageData['views_boost'],
                    'priority_level' => $packageData['priority_level'],
                    'starts_at' => now(),
                    'payment_status' => 'completed',
                    'status' => 'active',
                    'views_gained' => rand(50, 200),
                    'contacts_gained' => rand(5, 25),
                ]);

                $sponsorship->activate();
            }
        }

        $this->command->info('✅ Successfully seeded ' . count($items) . ' marketplace items!');
        $this->command->info('   - ' . collect($items)->where('status', 'active')->count() . ' active items');
        $this->command->info('   - ' . collect($items)->where('status', 'pending')->count() . ' pending items');
        $this->command->info('   - ' . (int)(count($items) / 3) . ' sponsored items');
    }
}
