<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserService;
use App\Models\ServiceCategory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UserServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $cityId = 4;

        // Arabic service data with different categories
        $servicesData = [
            [
                'title' => 'خدمات السباكة والصيانة المنزلية',
                'description' => 'نقدم خدمات السباكة الشاملة بما في ذلك إصلاح التسريبات، تركيب الأدوات الصحية، صيانة السخانات، وحل جميع مشاكل المياه. فريق محترف ومعدات حديثة لضمان أفضل خدمة.',
                'pricing_type' => 'hourly',
                'base_price' => 150.00,
                'hourly_rate' => 150.00,
                'experience_years' => 8,
            ],
            [
                'title' => 'تركيب وصيانة التكييفات',
                'description' => 'متخصصون في تركيب وصيانة جميع أنواع المكيفات. خدمة التنظيف، شحن الفريون، إصلاح الأعطال، وعقود الصيانة الدورية. نعمل على جميع الماركات بكفاءة عالية.',
                'pricing_type' => 'fixed',
                'base_price' => 200.00,
                'minimum_charge' => 100.00,
                'experience_years' => 10,
            ],
            [
                'title' => 'أعمال الكهرباء والإنارة',
                'description' => 'كهربائي معتمد لجميع أعمال الكهرباء المنزلية والتجارية. تركيب اللوحات الكهربائية، توصيل الأجهزة، إصلاح الأعطال، تركيب الإنارة الحديثة، وفحص شامل للسلامة.',
                'pricing_type' => 'hourly',
                'base_price' => 120.00,
                'hourly_rate' => 120.00,
                'experience_years' => 6,
            ],
            [
                'title' => 'نقل الأثاث والعفش',
                'description' => 'خدمة نقل الأثاث بأمان وسرعة. فريق محترف، سيارات مجهزة، تغليف احترافي، تركيب وفك الأثاث. نخدم جميع المناطق مع ضمان سلامة منقولاتكم.',
                'pricing_type' => 'negotiable',
                'base_price' => 500.00,
                'experience_years' => 5,
            ],
            [
                'title' => 'خدمات التنظيف المنزلي الشامل',
                'description' => 'تنظيف شامل للمنازل والشقق والفلل. تنظيف عميق للمطابخ والحمامات، تلميع الأرضيات، تنظيف النوافذ والستائر. فريق نسائي متاح. منظفات آمنة وصديقة للبيئة.',
                'pricing_type' => 'fixed',
                'base_price' => 250.00,
                'minimum_charge' => 150.00,
                'experience_years' => 4,
            ],
            [
                'title' => 'صيانة وبرمجة الحاسوب',
                'description' => 'خدمات تقنية متكاملة: صيانة الكمبيوتر واللابتوب، تركيب البرامج، إزالة الفيروسات، ترقية الأجهزة، استرجاع البيانات، وإعداد الشبكات المنزلية. خدمة سريعة وموثوقة.',
                'pricing_type' => 'hourly',
                'base_price' => 100.00,
                'hourly_rate' => 100.00,
                'experience_years' => 7,
            ],
            [
                'title' => 'تصليح وصيانة السيارات',
                'description' => 'ورشة متنقلة لصيانة السيارات. تغيير الزيت، فحص الفرامل، صيانة المحرك، إصلاح الأعطال الكهربائية، صيانة التكييف. نأتي إليك أينما كنت مع قطع غيار أصلية.',
                'pricing_type' => 'fixed',
                'base_price' => 300.00,
                'minimum_charge' => 150.00,
                'experience_years' => 12,
            ],
            [
                'title' => 'تصميم وتنسيق الحدائق',
                'description' => 'تصميم وتنفيذ الحدائق المنزلية. زراعة النباتات والأشجار، تركيب شبكات الري، تنسيق الديكورات الخارجية، صيانة دورية للحدائق. نحول حديقتك إلى واحة خضراء.',
                'pricing_type' => 'negotiable',
                'base_price' => 400.00,
                'experience_years' => 6,
            ],
            [
                'title' => 'دروس خصوصية في الرياضيات',
                'description' => 'معلم رياضيات خبير لجميع المراحل الدراسية. شرح مبسط، حل الواجبات، مراجعات قبل الامتحانات، تقوية المهارات الأساسية. دروس فردية أو جماعية، أونلاين أو حضوري.',
                'pricing_type' => 'hourly',
                'base_price' => 80.00,
                'hourly_rate' => 80.00,
                'experience_years' => 9,
            ],
            [
                'title' => 'تدريب اللياقة البدنية المنزلي',
                'description' => 'مدرب لياقة معتمد يأتي إلى منزلك. برامج تدريبية مخصصة، خطط غذائية صحية، متابعة يومية، تمارين القوة والكارديو. نتائج مضمونة مع الالتزام.',
                'pricing_type' => 'hourly',
                'base_price' => 120.00,
                'hourly_rate' => 120.00,
                'experience_years' => 5,
            ],
        ];

        // Users data
        $usersData = [
            [
                'name' => 'محمد أحمد السباك',
                'email' => 'mohammed.plumber@example.com',
                'phone' => '0500123456',
                'address' => 'حي النزهة، شارع الأمير سلطان',
            ],
            [
                'name' => 'خالد عبدالله للتكييف',
                'email' => 'khaled.ac@example.com',
                'phone' => '0500234567',
                'address' => 'حي السلامة، طريق الملك فهد',
            ],
            [
                'name' => 'عمر حسن الكهربائي',
                'email' => 'omar.electric@example.com',
                'phone' => '0500345678',
                'address' => 'حي الروضة، شارع التحلية',
            ],
            [
                'name' => 'سعد محمود للنقليات',
                'email' => 'saad.moving@example.com',
                'phone' => '0500456789',
                'address' => 'حي الشفا، طريق المدينة',
            ],
            [
                'name' => 'فاطمة علي للتنظيف',
                'email' => 'fatima.cleaning@example.com',
                'phone' => '0500567890',
                'address' => 'حي الزهراء، شارع فلسطين',
            ],
        ];

        // Service category IDs (adjust based on your database)
        // We'll use generic IDs - you may need to adjust these
        $categoryIds = [1, 2, 3, 4, 5];

        // Create users and their services
        $userIndex = 0;
        foreach ($usersData as $userData) {
            // Create or find user
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                [
                    'name' => $userData['name'],
                    'password' => Hash::make('password123'),
                    'phone' => $userData['phone'],
                    'address' => $userData['address'],
                    'city_id' => $cityId,
                    'user_type' => 'shop_owner',
                    'is_active' => true,
                    'is_verified' => true,
                    'email_verified_at' => now(),
                ]
            );

            $this->command->info("Created user: {$user->name}");

            // Create 2 services for this user
            for ($i = 0; $i < 2; $i++) {
                $serviceIndex = ($userIndex * 2) + $i;
                if ($serviceIndex >= count($servicesData)) {
                    break;
                }

                $serviceData = $servicesData[$serviceIndex];
                
                // Get a random service category or use default
                $categoryId = ServiceCategory::inRandomOrder()->first()?->id ?? 1;

                $service = UserService::create([
                    'user_id' => $user->id,
                    'service_category_id' => $categoryId,
                    'city_id' => $cityId,
                    'title' => $serviceData['title'],
                    'description' => $serviceData['description'],
                    'pricing_type' => $serviceData['pricing_type'],
                    'price_from' => $serviceData['base_price'],
                    'price_to' => $serviceData['base_price'] * 1.5,
                    'phone' => $userData['phone'],
                    'whatsapp' => $userData['phone'],
                    'is_active' => true,
                    'is_verified' => true,
                    'service_areas' => json_encode(['city_wide' => true, 'radius_km' => 50]),
                    'availability' => json_encode([
                        'saturday' => ['start' => '09:00', 'end' => '18:00'],
                        'sunday' => ['start' => '09:00', 'end' => '18:00'],
                        'monday' => ['start' => '09:00', 'end' => '18:00'],
                        'tuesday' => ['start' => '09:00', 'end' => '18:00'],
                        'wednesday' => ['start' => '09:00', 'end' => '18:00'],
                        'thursday' => ['start' => '09:00', 'end' => '18:00'],
                        'friday' => ['start' => '10:00', 'end' => '16:00'],
                    ]),
                    'images' => json_encode($this->generateServiceImages($serviceIndex)),
                    'requirements' => 'خبرة ' . $serviceData['experience_years'] . ' سنوات في المجال',
                ]);

                $this->command->info("  - Created service: {$service->title}");
            }

            $userIndex++;
        }

        $this->command->info("\n✅ Successfully created {$userIndex} users with their services for city ID: {$cityId}");
    }

    /**
     * Generate placeholder service images
     */
    private function generateServiceImages(int $index): array
    {
        // Using placeholder image services - these will generate appropriate service images
        $imageUrls = [
            "https://picsum.photos/seed/service{$index}a/800/600",
            "https://picsum.photos/seed/service{$index}b/800/600",
        ];

        return $imageUrls;
    }
}
