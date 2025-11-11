<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Service;
use App\Models\Shop;
use App\Models\Category;
use Faker\Factory as Faker;
use Illuminate\Support\Str;


class ServicesSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('ar_SA');
        
        // Get all shops
        $shops = Shop::with('category')->get();
        
        $this->command->info('🔧 Starting to seed services for shops...');
        $this->command->getOutput()->progressStart($shops->count());
        
        foreach ($shops as $shop) {
            // Generate 2-8 services per shop
            $servicesCount = $faker->numberBetween(2, 8);
            
            for ($i = 0; $i < $servicesCount; $i++) {
                $this->createServiceForShop($shop, $faker);
            }
            
            $this->command->getOutput()->progressAdvance();
        }
        
        $this->command->getOutput()->progressFinish();
        $this->command->info('✅ Successfully seeded services!');
        
        // Show statistics
        $totalServices = Service::count();
        $this->command->line("📊 Total Services: {$totalServices}");
        
        // Show distribution by category
        $categories = Category::withCount('shops')->get();
        foreach ($categories as $category) {
            $servicesInCategory = Service::whereHas('shop', function($query) use ($category) {
                $query->where('category_id', $category->id);
            })->count();
            
            if ($servicesInCategory > 0) {
                $this->command->line("   • {$category->name}: {$servicesInCategory} services");
            }
        }
    }
    
    private function createServiceForShop($shop, $faker)
    {
        $categoryName = $shop->category->name ?? 'عام';
        
        $serviceData = $this->generateServiceByCategory($categoryName, $faker);
        
        // Generate price and discount
        $basePrice = $faker->randomFloat(2, 20, 1000);
        $hasDiscount = $faker->boolean(25); // 25% chance of discount
        
        if ($hasDiscount) {
            $discountPercentage = $faker->numberBetween(10, 40);
            $originalPrice = $basePrice;
            $finalPrice = $originalPrice - ($originalPrice * $discountPercentage / 100);
        } else {
            $originalPrice = null;
            $finalPrice = $basePrice;
            $discountPercentage = 0;
        }
        
        Service::create([
            'shop_id' => $shop->id,
            'name' => $serviceData['name'],
            'description' => $serviceData['description'],
            'slug' => Str::slug($serviceData['name'] . '-' . Str::random(6)),
            'price' => $finalPrice,
            'original_price' => $originalPrice,
            'discount_percentage' => $discountPercentage,
            'images' => $this->generateServiceImages($categoryName, $faker),
            'duration_minutes' => $serviceData['duration_minutes'],
            'duration_text' => $serviceData['duration_text'],
            'is_available' => $faker->boolean(92), // 92% available
            'is_featured' => $faker->boolean(15), // 15% featured
            'requires_appointment' => $serviceData['requires_appointment'],
            'requirements' => $serviceData['requirements'],
            'benefits' => $serviceData['benefits'],
            'category' => $serviceData['category'],
            'sort_order' => $faker->numberBetween(0, 100),
        ]);
    }
    
    private function generateServiceByCategory($categoryName, $faker)
    {
        $services = [
            'مطاعم' => [
                'names' => [
                    'توصيل طلبات', 'حجز طاولة', 'تنظيم مناسبات', 'بوفيه مفتوح',
                    'خدمة الإفطار', 'طلبات جماعية', 'قوائم خاصة', 'خدمة 24 ساعة'
                ],
                'categories' => ['توصيل', 'حجوزات', 'مناسبات', 'خدمات خاصة'],
                'requires_appointment' => [true, false, true, false, false, true, true, false]
            ],
            'ملابس' => [
                'names' => [
                    'تفصيل ملابس', 'تعديل المقاسات', 'كي وتنظيف', 'استشارة أزياء',
                    'تصميم فساتين', 'تطريز يدوي', 'صبغ ملابس', 'إصلاح الملابس'
                ],
                'categories' => ['تفصيل', 'تعديلات', 'تنظيف', 'استشارات'],
                'requires_appointment' => [true, true, false, true, true, true, false, true]
            ],
            'إلكترونيات' => [
                'names' => [
                    'صيانة أجهزة', 'تركيب وتشغيل', 'ضمان ممتد', 'استرداد وتبديل',
                    'دعم فني', 'تحديث برامج', 'تنظيف داخلي', 'فحص شامل'
                ],
                'categories' => ['صيانة', 'تركيب', 'ضمان', 'دعم فني'],
                'requires_appointment' => [true, true, false, false, true, true, false, true]
            ],
            'صيدليات' => [
                'names' => [
                    'استشارة صيدلانية', 'قياس ضغط الدم', 'قياس السكر', 'حقن طبية',
                    'توصيل أدوية', 'فحص كوليسترول', 'وزن وطول', 'اختبار حمل'
                ],
                'categories' => ['استشارات', 'فحوصات', 'خدمات طبية', 'توصيل'],
                'requires_appointment' => [true, false, false, true, false, false, false, false]
            ],
            'صالونات' => [
                'names' => [
                    'قص وتسريح شعر', 'صبغة شعر', 'فرد وتمليس', 'تصفيف عرائس',
                    'علاج الشعر', 'حلاقة رجالية', 'تشذيب لحية', 'مساج فروة الرأس'
                ],
                'categories' => ['قص', 'صبغ', 'علاج', 'مناسبات'],
                'requires_appointment' => [true, true, true, true, true, true, true, true]
            ],
            'ورش سيارات' => [
                'names' => [
                    'صيانة دورية', 'إصلاح محرك', 'تغيير زيت', 'فحص شامل',
                    'إصلاح فرامل', 'بطارية وكهرباء', 'تكييف سيارة', 'غسيل وتلميع'
                ],
                'categories' => ['صيانة', 'إصلاح', 'فحص', 'تنظيف'],
                'requires_appointment' => [true, true, false, true, true, true, true, false]
            ]
        ];
        
        $categoryServices = $services[$categoryName] ?? $services['مطاعم'];
        
        $index = $faker->numberBetween(0, count($categoryServices['names']) - 1);
        $name = $categoryServices['names'][$index];
        $category = $faker->randomElement($categoryServices['categories']);
        $requiresAppointment = $categoryServices['requires_appointment'][$index] ?? $faker->boolean(60);
        
        return [
            'name' => $name,
            'description' => $this->generateServiceDescription($name, $faker),
            'duration_minutes' => $this->generateDuration($categoryName, $faker),
            'duration_text' => null, // Will be calculated automatically
            'requires_appointment' => $requiresAppointment,
            'requirements' => $this->generateRequirements($categoryName, $faker),
            'benefits' => $this->generateBenefits($name, $faker),
            'category' => $category
        ];
    }
    
    private function generateServiceDescription($serviceName, $faker)
    {
        $templates = [
            "خدمة {service} احترافية بأيدي خبراء متخصصين وأدوات حديثة",
            "احصل على {service} عالية الجودة بأفضل الأسعار وأسرع وقت",
            "{service} متميزة مع ضمان الجودة والرضا التام للعميل",
            "خدمة {service} شاملة تلبي جميع احتياجاتك بمعايير عالمية",
            "تمتع بـ {service} على أعلى مستوى من الاحترافية والخبرة"
        ];
        
        $template = $faker->randomElement($templates);
        return str_replace('{service}', $serviceName, $template);
    }
    
    private function generateDuration($categoryName, $faker)
    {
        $durations = [
            'مطاعم' => [15, 30, 45, 60],
            'ملابس' => [30, 60, 120, 180],
            'إلكترونيات' => [30, 60, 120, 240],
            'صيدليات' => [5, 10, 15, 30],
            'صالونات' => [30, 60, 90, 120, 180],
            'ورش سيارات' => [60, 120, 180, 240, 480]
        ];
        
        $categoryDurations = $durations[$categoryName] ?? $durations['مطاعم'];
        return $faker->randomElement($categoryDurations);
    }
    
    private function generateRequirements($categoryName, $faker)
    {
        $requirements = [
            'مطاعم' => [
                'حجز مسبق للطاولات',
                'دفع مقدم للمناسبات',
                'تحديد عدد الأشخاص'
            ],
            'ملابس' => [
                'إحضار الملابس المراد تعديلها',
                'تحديد موعد للقياس',
                'دفع 50% مقدماً'
            ],
            'إلكترونيات' => [
                'إحضار فاتورة الشراء',
                'تحديد نوع العطل',
                'توفر قطع الغيار'
            ],
            'صالونات' => [
                'حجز موعد مسبق',
                'تحديد نوع الخدمة المطلوبة',
                'إحضار صور للتسريحة المطلوبة'
            ]
        ];
        
        $categoryRequirements = $requirements[$categoryName] ?? [];
        
        if (empty($categoryRequirements)) {
            return ['لا توجد متطلبات خاصة'];
        }
        
        $count = $faker->numberBetween(1, min(3, count($categoryRequirements)));
        return $faker->randomElements($categoryRequirements, $count);
    }
    
    private function generateBenefits($serviceName, $faker)
    {
        $benefits = [
            'جودة عالية مضمونة',
            'خدمة سريعة ومميزة',
            'أسعار تنافسية',
            'فريق عمل محترف',
            'ضمان على الخدمة',
            'خدمة عملاء متميزة',
            'مواعيد مرنة',
            'نتائج مرضية 100%'
        ];
        
        $count = $faker->numberBetween(2, 4);
        return $faker->randomElements($benefits, $count);
    }
    
    private function generateServiceImages($categoryName, $faker)
    {
        $imageCategories = [
            'مطاعم' => [
                'https://images.unsplash.com/photo-1514933651103-005eec06c04b?w=400',
                'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=400'
            ],
            'ملابس' => [
                'https://images.unsplash.com/photo-1558769132-cb1aea458c5e?w=400',
                'https://images.unsplash.com/photo-1582719478250-c89cae4dc85b?w=400'
            ],
            'إلكترونيات' => [
                'https://images.unsplash.com/photo-1581092160562-40aa08e78837?w=400',
                'https://images.unsplash.com/photo-1621905251189-08b45d6a269e?w=400'
            ],
            'صالونات' => [
                'https://images.unsplash.com/photo-1560066984-138dadb4c035?w=400',
                'https://images.unsplash.com/photo-1522337360788-8b13dee7a37e?w=400'
            ],
            'ورش سيارات' => [
                'https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?w=400',
                'https://images.unsplash.com/photo-1607860108855-64acf2078ed9?w=400'
            ]
        ];
        
        $categoryImages = $imageCategories[$categoryName] ?? $imageCategories['مطاعم'];
        
        // Return 1-2 random images
        $imageCount = $faker->numberBetween(1, min(2, count($categoryImages)));
        return $faker->randomElements($categoryImages, $imageCount);
    }
}
