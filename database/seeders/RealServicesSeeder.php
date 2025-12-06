<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\UserService;
use App\Models\ServiceCategory;
use App\Models\City;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class RealServicesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get or create a default city (you can change this to your specific city)
        $city = City::where('id', 4)->first();
        
        if (!$city) {
            $city = City::create([
                'name' => 'المدينة الافتراضية',
                'slug' => 'default-city',
                'name_en' => 'Default City',
                'is_active' => true,
            ]);
        }

        $this->command->info('🏙️  المدينة المستخدمة: ' . $city->name);

        // Create Service Categories
        $categories = [
            [
                'name' => 'Doors & Furniture',
                'name_ar' => 'أبواب وأثاث',
                'slug' => 'doors-furniture',
                'icon' => 'fas fa-door-open',
                'description' => 'Doors, couches, furniture renovation',
                'description_ar' => 'أبواب مصفحة، ركنات، تجديد أنتريهات',
            ],
            [
                'name' => 'Satellite & Receiver',
                'name_ar' => 'دش ورسيفر',
                'slug' => 'satellite-receiver',
                'icon' => 'fas fa-satellite-dish',
                'description' => 'Satellite and receiver installation',
                'description_ar' => 'تركيب وصيانة دش ورسيفرات',
            ],
            [
                'name' => 'Security Cameras',
                'name_ar' => 'كاميرات مراقبة',
                'slug' => 'security-cameras',
                'icon' => 'fas fa-video',
                'description' => 'Security camera installation',
                'description_ar' => 'تركيب وصيانة كاميرات المراقبة',
            ],
            [
                'name' => 'Decoration & Finishing',
                'name_ar' => 'ديكور وتشطيبات',
                'slug' => 'decoration-finishing',
                'icon' => 'fas fa-paint-roller',
                'description' => 'Decoration and finishing works',
                'description_ar' => 'أعمال الديكور والتشطيبات والموبيليا',
            ],
            [
                'name' => 'Gypsum Board',
                'name_ar' => 'جبسيوم بورد',
                'slug' => 'gypsum-board',
                'icon' => 'fas fa-th-large',
                'description' => 'Gypsum board installation',
                'description_ar' => 'تركيب وتشكيل جبسيوم بورد',
            ],
            [
                'name' => 'Painting',
                'name_ar' => 'نقاشة',
                'slug' => 'painting',
                'icon' => 'fas fa-brush',
                'description' => 'Painting and decoration',
                'description_ar' => 'أعمال النقاشة والديكورات',
            ],
            [
                'name' => 'Marble & Granite',
                'name_ar' => 'رخام وجرانيت',
                'slug' => 'marble-granite',
                'icon' => 'fas fa-gem',
                'description' => 'Marble and granite supply',
                'description_ar' => 'توريد وتركيب رخام وجرانيت',
            ],
            [
                'name' => 'Electrician',
                'name_ar' => 'كهربائي',
                'slug' => 'electrician',
                'icon' => 'fas fa-bolt',
                'description' => 'Electrical works',
                'description_ar' => 'أعمال الكهرباء والإضاءة',
            ],
            [
                'name' => 'Electronics Repair',
                'name_ar' => 'صيانة أجهزة إلكترونية',
                'slug' => 'electronics-repair',
                'icon' => 'fas fa-tv',
                'description' => 'Electronics repair',
                'description_ar' => 'صيانة الشاشات والرسيفرات والموبايلات',
            ],
            [
                'name' => 'Computer & Laptop',
                'name_ar' => 'كمبيوتر ولاب توب',
                'slug' => 'computer-laptop',
                'icon' => 'fas fa-laptop',
                'description' => 'Computer and laptop repair',
                'description_ar' => 'صيانة كمبيوتر ولاب توب ومشاكل إنترنت',
            ],
            [
                'name' => 'Welding',
                'name_ar' => 'حدادة',
                'slug' => 'welding',
                'icon' => 'fas fa-wrench',
                'description' => 'Welding and metal works',
                'description_ar' => 'أعمال الحدادة والأبواب الحديد',
            ],
            [
                'name' => 'Plumbing',
                'name_ar' => 'سباكة',
                'slug' => 'plumbing',
                'icon' => 'fas fa-faucet',
                'description' => 'Plumbing works',
                'description_ar' => 'أعمال السباكة وصيانة المواسير',
            ],
            [
                'name' => 'Water Filters',
                'name_ar' => 'فلاتر ومعالجة مياه',
                'slug' => 'water-filters',
                'icon' => 'fas fa-tint',
                'description' => 'Water filters installation',
                'description_ar' => 'تركيب وصيانة فلاتر وأنظمة معالجة المياه',
            ],
            [
                'name' => 'Aluminum',
                'name_ar' => 'المنيوم',
                'slug' => 'aluminum',
                'icon' => 'fas fa-border-all',
                'description' => 'Aluminum windows and kitchens',
                'description_ar' => 'شبابيك ومطابخ ألومنيوم',
            ],
            [
                'name' => 'Sewing & Tailoring',
                'name_ar' => 'خياطة وتفصيل',
                'slug' => 'sewing-tailoring',
                'icon' => 'fas fa-cut',
                'description' => 'Sewing and tailoring',
                'description_ar' => 'خياطة وتفصيل ملابس ومفروشات',
            ],
            [
                'name' => 'Landscaping',
                'name_ar' => 'تنسيق حدائق',
                'slug' => 'landscaping',
                'icon' => 'fas fa-leaf',
                'description' => 'Landscaping and gardens',
                'description_ar' => 'نجيل صناعي وتصميم بلكونات وحدائق',
            ],
            [
                'name' => 'Air Conditioning',
                'name_ar' => 'تكييفات وتبريد',
                'slug' => 'air-conditioning',
                'icon' => 'fas fa-fan',
                'description' => 'AC installation and repair',
                'description_ar' => 'تركيب وصيانة تكييفات وثلاجات',
            ],
            [
                'name' => 'Barber',
                'name_ar' => 'كوافير',
                'slug' => 'barber',
                'icon' => 'fas fa-cut',
                'description' => 'Barber services',
                'description_ar' => 'كوافير رجالي وحلاقة',
            ],
        ];

        foreach ($categories as $categoryData) {
            ServiceCategory::firstOrCreate(
                ['slug' => $categoryData['slug']],
                $categoryData
            );
        }

        $this->command->info('✅ تم إنشاء ' . count($categories) . ' فئة خدمة');

        // Services Data
        $services = [
            [
                'name' => 'هاني فهمى',
                'title' => 'أبواب مصفحة وركنات وتجديد انتريهات',
                'phone' => '01098218139',
                'category_slug' => 'doors-furniture',
                'pricing_type' => 'negotiable',
            ],
            [
                'name' => 'م اشرف البغدادى',
                'title' => 'دش ورسيفر',
                'phone' => '01001517656',
                'category_slug' => 'satellite-receiver',
                'pricing_type' => 'negotiable',
            ],
            [
                'name' => 'م مصطفى فيديو',
                'title' => 'كاميرات مراقبة',
                'phone' => '01114561362',
                'category_slug' => 'security-cameras',
                'pricing_type' => 'negotiable',
            ],
            [
                'name' => 'م/ مصطفى جنيرال',
                'title' => 'صيانة وتركيب دش ورسيفر',
                'phone' => '01558350160',
                'category_slug' => 'satellite-receiver',
                'pricing_type' => 'negotiable',
            ],
            [
                'name' => 'م / كريم مصطفى',
                'title' => 'مهندس ديكور - تشطيبات وموبيليا',
                'phone' => '01275804975',
                'category_slug' => 'decoration-finishing',
                'pricing_type' => 'negotiable',
            ],
            [
                'name' => 'احمد ربيع',
                'title' => 'جبسيوم بورد',
                'phone' => '01020918663',
                'category_slug' => 'gypsum-board',
                'pricing_type' => 'negotiable',
            ],
            [
                'name' => 'ا / محمد السيد',
                'title' => 'نقاشة وديكورات',
                'phone' => '01279923340',
                'category_slug' => 'painting',
                'pricing_type' => 'negotiable',
            ],
            [
                'name' => 'محمد سمير',
                'title' => 'توريد وتركيب رخام وجرانيت',
                'phone' => '01126020728',
                'category_slug' => 'marble-granite',
                'pricing_type' => 'negotiable',
            ],
            [
                'name' => 'طارق',
                'title' => 'كهربائي',
                'phone' => '01065542460',
                'category_slug' => 'electrician',
                'pricing_type' => 'negotiable',
            ],
            [
                'name' => 'اسلام ادم',
                'title' => 'كهربائي وكوالين سمارت',
                'phone' => '01146922920',
                'category_slug' => 'electrician',
                'pricing_type' => 'negotiable',
            ],
            [
                'name' => 'محمد ابو جنه',
                'title' => 'كهربائي',
                'phone' => '01014523137',
                'category_slug' => 'electrician',
                'pricing_type' => 'negotiable',
            ],
            [
                'name' => 'م فرج فتحي',
                'title' => 'صيانة الشاشات والرسيفرات وتحديثها',
                'phone' => '01024176683',
                'category_slug' => 'electronics-repair',
                'pricing_type' => 'negotiable',
            ],
            [
                'name' => 'م / مصطفى عبد الغفار',
                'title' => 'صيانة شاشات وموبيلات',
                'phone' => '01157227774',
                'category_slug' => 'electronics-repair',
                'pricing_type' => 'negotiable',
            ],
            [
                'name' => 'م/ محمد صقر',
                'title' => 'صيانة موبيلات',
                'phone' => '01010249410',
                'category_slug' => 'electronics-repair',
                'pricing_type' => 'negotiable',
            ],
            [
                'name' => 'م / شعبان',
                'title' => 'صيانه كمبيوتر ولاب توب ومشاكل انترنت',
                'phone' => '01146448044',
                'category_slug' => 'computer-laptop',
                'pricing_type' => 'negotiable',
            ],
            [
                'name' => 'نبيل ابو الذهب',
                'title' => 'حداد',
                'phone' => '01007123608',
                'category_slug' => 'welding',
                'pricing_type' => 'negotiable',
            ],
            [
                'name' => 'ياسر',
                'title' => 'حداد',
                'phone' => '01147043377',
                'category_slug' => 'welding',
                'pricing_type' => 'negotiable',
            ],
            [
                'name' => 'ميدو الحداد',
                'title' => 'حداد - عمارات ال٧٥',
                'phone' => '01122877709',
                'whatsapp' => '01002344039',
                'category_slug' => 'welding',
                'pricing_type' => 'negotiable',
            ],
            [
                'name' => 'فتحي',
                'title' => 'سباك من سكان المدينة',
                'phone' => '01091648264',
                'category_slug' => 'plumbing',
                'pricing_type' => 'negotiable',
            ],
            [
                'name' => 'سمير',
                'title' => 'سباك',
                'phone' => '01004642033',
                'category_slug' => 'plumbing',
                'pricing_type' => 'negotiable',
            ],
            [
                'name' => 'شريف السباك',
                'title' => 'سباك',
                'phone' => '01151820595',
                'category_slug' => 'plumbing',
                'pricing_type' => 'negotiable',
            ],
            [
                'name' => 'م / محمود حسين',
                'title' => 'فلاتر وانظمة معالجة المياه - شركة الصياد',
                'phone' => '01157757533',
                'whatsapp' => '01228846729',
                'category_slug' => 'water-filters',
                'pricing_type' => 'negotiable',
            ],
            [
                'name' => 'م محمد امام المويتال',
                'title' => 'المنيوم',
                'phone' => '01060162726',
                'category_slug' => 'aluminum',
                'pricing_type' => 'negotiable',
            ],
            [
                'name' => 'م نبيل القاضي',
                'title' => 'المنيوم شبابيك ومطابخ',
                'phone' => '01007182859',
                'category_slug' => 'aluminum',
                'pricing_type' => 'negotiable',
            ],
            [
                'name' => 'مدام شيماء',
                'title' => 'ترزي تفصيل ملابس',
                'phone' => '01117197982',
                'category_slug' => 'sewing-tailoring',
                'pricing_type' => 'negotiable',
            ],
            [
                'name' => 'ام فهد',
                'title' => 'خياطه وتفصيل ملابس',
                'phone' => '01125703078',
                'whatsapp' => '01019034091',
                'category_slug' => 'sewing-tailoring',
                'pricing_type' => 'negotiable',
            ],
            [
                'name' => 'ام اياد',
                'title' => 'تصليح ملابس وتفصيل ملايات ومفروشات',
                'phone' => '01103903772',
                'whatsapp' => '01091288055',
                'category_slug' => 'sewing-tailoring',
                'pricing_type' => 'negotiable',
            ],
            [
                'name' => 'ا خالد',
                'title' => 'نجيل صناعي وأحواض زرع للبلكونات وتصميم بلكونات',
                'phone' => '01120709180',
                'category_slug' => 'landscaping',
                'pricing_type' => 'negotiable',
            ],
            [
                'name' => 'م / محمد فتحي',
                'title' => 'تكييفات وثلاجات',
                'phone' => '01143846140',
                'category_slug' => 'air-conditioning',
                'pricing_type' => 'negotiable',
            ],
            [
                'name' => 'م/ مصطفى امام',
                'title' => 'تبريد وتكييف',
                'phone' => '01140159985',
                'whatsapp' => '01060676208',
                'category_slug' => 'air-conditioning',
                'pricing_type' => 'negotiable',
            ],
            [
                'name' => 'م / عمرو',
                'title' => 'تكييفات',
                'phone' => '01120237464',
                'category_slug' => 'air-conditioning',
                'pricing_type' => 'negotiable',
            ],
            [
                'name' => 'م / حسن الزهار',
                'title' => 'تبريد وتكييف وفلاتر مياه',
                'phone' => '01205086459',
                'whatsapp' => '01147083251',
                'category_slug' => 'air-conditioning',
                'pricing_type' => 'negotiable',
            ],
            [
                'name' => 'كريم',
                'title' => 'كوافير رجالى (الأيتام مجاناً)',
                'phone' => '01125585125',
                'category_slug' => 'barber',
                'pricing_type' => 'negotiable',
            ],
        ];

        // Create users and services
        $createdServices = 0;
        $createdUsers = 0;
        
        foreach ($services as $serviceData) {
            // Create user account
            $user = User::firstOrCreate(
                ['phone' => $serviceData['phone']],
                [
                    'name' => $serviceData['name'],
                    'email' => Str::slug($serviceData['name']) . rand(1000, 9999) . '@service.local',
                    'password' => Hash::make('password123'),
                    'phone' => $serviceData['phone'],
                    'email_verified_at' => now(),
                ]
            );

            if ($user->wasRecentlyCreated) {
                $createdUsers++;
            }

            // Get category
            $category = ServiceCategory::where('slug', $serviceData['category_slug'])->first();

            if (!$category) {
                $this->command->warn('⚠️  الفئة غير موجودة: ' . $serviceData['category_slug']);
                continue;
            }

            // Create service
            $service = UserService::firstOrCreate(
                [
                    'user_id' => $user->id,
                    'phone' => $serviceData['phone'],
                ],
                [
                    'title' => $serviceData['title'],
                    'description' => 'خدمة ' . $serviceData['title'] . ' - تواصل للحصول على أفضل الأسعار والخدمة المميزة',
                    'slug' => Str::slug($serviceData['title']) . '-' . rand(1000, 9999),
                    'service_category_id' => $category->id,
                    'city_id' => $city->id,
                    'phone' => $serviceData['phone'],
                    'whatsapp' => $serviceData['whatsapp'] ?? $serviceData['phone'],
                    'pricing_type' => $serviceData['pricing_type'],
                    'is_active' => true,
                    'is_verified' => true,
                    'is_featured' => false,
                ]
            );

            if ($service->wasRecentlyCreated) {
                $createdServices++;
            }
        }

        $this->command->info('✅ تم إنشاء ' . $createdUsers . ' مستخدم جديد');
        $this->command->info('✅ تم إنشاء ' . $createdServices . ' خدمة جديدة');
        $this->command->info('🎉 اكتمل استيراد البيانات بنجاح!');
    }
}
