<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Shop;
use App\Models\City;
use App\Models\Category;
use App\Models\User;
use Illuminate\Support\Str;
use Faker\Factory as Faker;

class EgyptianShopsSeeder extends Seeder
{
    /**
     * Run the database seeds for Egyptian Shops.
     * Creates 1000 realistic shops across Egyptian cities.
     */
    public function run(): void
    {
        $faker = Faker::create('ar_SA'); // Arabic locale
        
        // Get all cities and categories
        $cities = City::where('country', 'مصر')->get();
        $categories = Category::all();
        $users = User::all();
        
        if ($cities->isEmpty()) {
            $this->command->error('❌ No Egyptian cities found. Please run EgyptianNewCitiesSeeder first.');
            return;
        }
        
        if ($categories->isEmpty()) {
            $this->command->error('❌ No categories found. Please run ArabicCategorySeeder first.');
            return;
        }
        
        if ($users->isEmpty()) {
            $this->command->info('⚠️ No users found. Creating sample shop owners...');
            // Create sample shop owners
            for ($i = 1; $i <= 10; $i++) {
                User::create([
                    'name' => "مالك متجر {$i}",
                    'email' => "shopowner{$i}@example.com",
                    'password' => bcrypt('password'),
                    'user_role_id' => 2, // Shop Owner role (assuming it exists)
                    'email_verified_at' => now(),
                ]);
            }
            $users = User::all();
            $this->command->info('✅ Created 10 sample shop owners.');
        }

        // Egyptian shop names by category
        $shopNames = [
            'مطاعم' => [
                'مطعم الفراعنة', 'مطعم النيل الذهبي', 'مطعم أم كلثوم', 'مطعم الأهرامات',
                'مطعم المحروسة', 'مطعم بلدي', 'مطعم الفسطاط', 'مطعم الملوك',
                'مطعم كشري التحرير', 'مطعم فول وطعمية الشعب', 'مطعم المولوخية المصرية',
                'مطعم الملوخية والفراخ', 'مطعم البحر المتوسط', 'مطعم الصعيد الأصيل'
            ],
            'ملابس' => [
                'بوتيك الملكة', 'أزياء النيل', 'موضة الأهرامات', 'بوتيك كليوباترا',
                'أزياء مصر الجديدة', 'بوتيك الزمالك', 'موضة المعادي', 'أزياء الهرم',
                'بوتيك العاصمة', 'موضة الشباب', 'أزياء الأناقة', 'بوتيك النجوم'
            ],
            'إلكترونيات' => [
                'الكترونيات المستقبل', 'تكنولوجيا النيل', 'الكترونيات الأهرامات',
                'مركز الابتكار التقني', 'الكترونيات العاصمة', 'تقنية مصر الحديثة',
                'مركز الكمبيوتر المصري', 'الكترونيات السلام', 'تكنولوجيا الغد'
            ],
            'صيدليات' => [
                'صيدلية النيل', 'صيدلية الشفاء', 'صيدلية الأهرامات', 'صيدلية المحروسة',
                'صيدلية العاصمة', 'صيدلية الصحة', 'صيدلية الأمل', 'صيدلية السلامة',
                'صيدلية الحياة', 'صيدلية المستقبل', 'صيدلية النور', 'صيدلية البشرى'
            ],
            'سوبر ماركت' => [
                'سوبر ماركت النيل', 'هايبر الأهرامات', 'سوبر ماركت المحروسة',
                'هايبر العاصمة', 'سوبر ماركت مصر', 'هايبر النجوم', 'سوبر ماركت الملوك',
                'هايبر الحديث', 'سوبر ماركت الشعب', 'هايبر المدينة'
            ],
            'مقاهي' => [
                'قهوة النيل', 'كافيه الأهرامات', 'قهوة المحروسة', 'كافيه العاصمة',
                'قهوة الفراعنة', 'كافيه النجوم', 'قهوة الشعب', 'كافيه الملوك',
                'قهوة الأصالة', 'كافيه الحديث', 'قهوة البلد', 'كافيه المدينة'
            ]
        ];

        // Egyptian street names
        $streets = [
            'شارع النيل', 'شارع الهرم', 'شارع التحرير', 'شارع الجمهورية',
            'شارع النصر', 'شارع السلام', 'شارع الشهداء', 'شارع المعز',
            'شارع الفسطاط', 'شارع الأزهر', 'شارع رمسيس', 'شارع الجيش',
            'شارع الثورة', 'شارع مصر', 'شارع القاهرة', 'شارع الجلاء',
            'شارع النهضة', 'شارع التنمية', 'شارع الحرية', 'شارع الوحدة'
        ];

        $this->command->info('🏪 Starting to seed 1000 Egyptian shops...');
        $this->command->getOutput()->progressStart(1000);

        for ($i = 0; $i < 1000; $i++) {
            $city = $cities->random();
            $category = $categories->random();
            $user = $users->random();
            
            // Get shop names for this category
            $categoryNames = $shopNames[$category->name] ?? $shopNames['مطاعم'];
            $shopName = $faker->randomElement($categoryNames) . ' ' . $faker->numberBetween(1, 99);
            
            // Generate coordinates within city bounds (small radius around city center)
            $latOffset = $faker->randomFloat(4, -0.01, 0.01);
            $lngOffset = $faker->randomFloat(4, -0.01, 0.01);
            
            // Create shop using Eloquent model (handles casting automatically)
            Shop::create([
                'user_id' => $user->id,
                'city_id' => $city->id,
                'category_id' => $category->id,
                'name' => $shopName,
                'slug' => Str::slug($shopName . '-' . $city->name . '-' . ($i + 1)),
                'description' => $this->generateShopDescription($category->name, $faker),
                'address' => $faker->randomElement($streets) . ' ' . $faker->numberBetween(1, 200) . '، ' . $city->name,
                'latitude' => $city->latitude + $latOffset,
                'longitude' => $city->longitude + $lngOffset,
                'phone' => $this->generateEgyptianPhone($faker),
                'email' => Str::slug($shopName) . '@gmail.com',
                'website' => 'https://' . Str::slug($shopName) . '.com',
                'images' => [
                    'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=400',
                    'https://images.unsplash.com/photo-1507003211169-0a1dd7228f2d?w=400'
                ],
                'opening_hours' => $this->generateOpeningHours($faker),
                'rating' => $faker->randomFloat(1, 3.0, 5.0),
                'review_count' => $faker->numberBetween(5, 200),
                'is_featured' => $faker->boolean(20), // 20% chance to be featured
                'is_verified' => $faker->boolean(85), // 85% chance to be verified
                'is_active' => $faker->boolean(95), // 95% chance to be active
                'verified_at' => $faker->boolean(85) ? now() : null,
            ]);
            
            $this->command->getOutput()->progressAdvance();
        }
        
        $this->command->getOutput()->progressFinish();
        $this->command->info('✅ Successfully seeded 1000 Egyptian shops!');
        $this->command->line('📊 Shop Distribution:');
        
        // Show distribution by city
        foreach ($cities as $city) {
            $count = Shop::where('city_id', $city->id)->count();
            $this->command->line("   • {$city->name}: {$count} shops");
        }
    }
    
    private function generateShopDescription($categoryName, $faker)
    {
        $descriptions = [
            'مطاعم' => [
                'نقدم أشهى الأطباق المصرية الأصيلة في أجواء مميزة',
                'مطعم عائلي يقدم أفضل المأكولات التقليدية والعصرية',
                'تذوق أروع النكهات المصرية في قلب المدينة',
                'مطعم راقي يجمع بين الأصالة والحداثة في تقديم الطعام'
            ],
            'ملابس' => [
                'أحدث صيحات الموضة والأزياء العصرية للرجال والنساء',
                'بوتيك أنيق يقدم أرقى الملابس والإكسسوارات',
                'موضة عصرية بأسعار مناسبة وجودة عالية',
                'أزياء راقية تناسب جميع المناسبات والأعمار'
            ],
            'إلكترونيات' => [
                'أحدث الأجهزة الإلكترونية والتكنولوجيا المتطورة',
                'مركز متخصص في بيع وصيانة الأجهزة الإلكترونية',
                'تشكيلة واسعة من الهواتف والحاسوب والأجهزة الذكية',
                'خدمة ما بعد البيع والضمان على جميع المنتجات'
            ],
            'صيدليات' => [
                'صيدلية متكاملة تقدم جميع الأدوية والمستلزمات الطبية',
                'خدمة صيدلانية متميزة على مدار الساعة',
                'أدوية أصلية ومستحضرات تجميل وعناية شخصية',
                'فريق صيدلاني مؤهل لتقديم الاستشارات الطبية'
            ],
            'سوبر ماركت' => [
                'سوبر ماركت شامل يوفر جميع احتياجاتك اليومية',
                'تشكيلة واسعة من المنتجات الغذائية والمنزلية',
                'أسعار تنافسية وعروض يومية مميزة',
                'خدمة توصيل سريعة لجميع أنحاء المدينة'
            ],
            'مقاهي' => [
                'قهوة فاخرة ومشروبات متنوعة في أجواء هادئة ومريحة',
                'كافيه عصري يقدم أجود أنواع القهوة والحلويات',
                'مكان مثالي للقاءات العمل والجلسات الودية',
                'واي فاي مجاني وخدمة متميزة في قلب المدينة'
            ]
        ];
        
        $categoryDescriptions = $descriptions[$categoryName] ?? $descriptions['مطاعم'];
        return $faker->randomElement($categoryDescriptions);
    }
    
    private function generateEgyptianPhone($faker)
    {
        // Egyptian mobile numbers start with 010, 011, 012, 015
        $prefixes = ['010', '011', '012', '015'];
        $prefix = $faker->randomElement($prefixes);
        $number = $faker->numberBetween(10000000, 99999999);
        
        return '+20 ' . $prefix . ' ' . substr($number, 0, 4) . ' ' . substr($number, 4);
    }
    
    private function generateOpeningHours($faker)
    {
        $days = ['sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday'];
        $hours = [];
        
        foreach ($days as $day) {
            if ($faker->boolean(90)) { // 90% chance the shop is open on any given day
                $openHour = $faker->numberBetween(6, 10);
                $closeHour = $faker->numberBetween(20, 23);
                
                $hours[$day] = [
                    'open' => sprintf('%02d:00', $openHour),
                    'close' => sprintf('%02d:00', $closeHour)
                ];
            }
        }
        
        return $hours;
    }
}