<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\MarketplaceItem;
use App\Models\User;
use App\Models\Category;
use App\Models\City;
use Carbon\Carbon;

class MarketplaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get users, categories, and cities
        $users = User::all();
        $categories = Category::all();
        $cities = City::all();

        if ($users->isEmpty() || $categories->isEmpty() || $cities->isEmpty()) {
            $this->command->error('Please seed users, categories, and cities first!');
            return;
        }

        // Sample marketplace items data
        $items = [
            // Electronics
            [
                'title' => 'آيفون 14 برو ماكس 256 جيجا',
                'description' => 'آيفون 14 برو ماكس 256 جيجا، حالة ممتازة، استخدام 3 أشهر فقط. اللون: بنفسجي داكن. الجهاز نظيف جداً وبدون أي خدوش. معاه العلبة الأصلية وجميع الملحقات. البطارية 100%. غير مفتوح ولا مصلح. السعر نهائي وغير قابل للتفاوض.',
                'price' => 42000,
                'condition' => 'like_new',
                'is_negotiable' => false,
            ],
            [
                'title' => 'لاب توب Dell XPS 15 - i7 الجيل 11',
                'description' => 'لاب توب ديل XPS 15 بمواصفات عالية. معالج Core i7 الجيل الحادي عشر، رامات 16 جيجا، هارد SSD 512 جيجا، كرت شاشة NVIDIA GTX 1650. الشاشة 15.6 بوصة FHD. حالة الجهاز ممتازة جداً. مناسب للألعاب والتصميم والبرمجة.',
                'price' => 28000,
                'condition' => 'good',
                'is_negotiable' => true,
            ],
            [
                'title' => 'ساعة Apple Watch Series 8',
                'description' => 'ساعة آبل واتش سيريز 8، مقاس 45 ملم، GPS + Cellular. اللون أسود. حالة ممتازة مع العلبة الأصلية وشاحن أصلي. استخدام خفيف لمدة شهرين. تدعم جميع المزايا الصحية والرياضية.',
                'price' => 9500,
                'condition' => 'like_new',
                'is_negotiable' => true,
            ],
            [
                'title' => 'PlayStation 5 مع يدين وألعاب',
                'description' => 'بلايستيشن 5 النسخة العادية (غير الديجيتال)، معاه يدين أصليين و 5 ألعاب (FIFA 24, God of War, Spider-Man, The Last of Us 2, Ghost of Tsushima). حالة ممتازة، استخدام منزلي بسيط.',
                'price' => 15000,
                'condition' => 'good',
                'is_negotiable' => true,
            ],
            [
                'title' => 'AirPods Pro الجيل الثاني',
                'description' => 'سماعات آبل اير بودز برو الجيل الثاني، جديدة بالكرتونة الأصلية مغلقة. مع خاصية إلغاء الضوضاء النشط وشريحة H2 الجديدة. ضمان سنة من تاريخ الشراء.',
                'price' => 6800,
                'condition' => 'new',
                'is_negotiable' => false,
            ],

            // Furniture
            [
                'title' => 'طقم كنب مودرن 7 قطع',
                'description' => 'طقم كنب حرف L مكون من 7 قطع، قماش مخمل فاخر لون بيج. الحالة ممتازة جداً، تنظيف دوري. مناسب للشقق الكبيرة أو القصور. مع طاولة صغيرة هدية.',
                'price' => 18000,
                'condition' => 'good',
                'is_negotiable' => true,
            ],
            [
                'title' => 'غرفة نوم كاملة خشب زان',
                'description' => 'غرفة نوم كاملة من خشب الزان الطبيعي: سرير + دولاب 6 أبواب + تسريحة بمرايا + 2 كومودينو. التصميم كلاسيكي فاخر. حالة ممتازة، بدون خدوش أو كسور.',
                'price' => 35000,
                'condition' => 'like_new',
                'is_negotiable' => true,
            ],
            [
                'title' => 'طاولة طعام 6 كراسي - رخام',
                'description' => 'طاولة طعام فخمة سطح رخام طبيعي مع 6 كراسي مريحة بتنجيد جلد. حالة ممتازة جداً. الأبعاد: 180x100 سم. مناسبة للعائلات الكبيرة.',
                'price' => 12000,
                'condition' => 'good',
                'is_negotiable' => true,
            ],

            // Vehicles & Spare Parts
            [
                'title' => 'عجلة رياضية رود بايك - كربون',
                'description' => 'دراجة رياضية رود بايك، إطار كربون خفيف الوزن، مقاس 28 بوصة. مناسبة للسباقات والتمارين الرياضية. حالة ممتازة جداً، استخدام خفيف.',
                'price' => 8500,
                'condition' => 'like_new',
                'is_negotiable' => true,
            ],
            [
                'title' => 'إطارات سيارة ميشلان - جديدة',
                'description' => '4 إطارات ميشلان جديدة بالكرتون، مقاس 205/55 R16. مناسبة للسيارات الصغيرة والمتوسطة. السعر للأربع إطارات معاً.',
                'price' => 5500,
                'condition' => 'new',
                'is_negotiable' => true,
            ],

            // Home Appliances
            [
                'title' => 'ثلاجة توشيبا 16 قدم - نوفروست',
                'description' => 'ثلاجة توشيبا 16 قدم نوفروست، لون فضي، موفرة للكهرباء. حالة ممتازة، عمر سنتين فقط. لا توجد أي أعطال. السبب في البيع: السفر للخارج.',
                'price' => 9000,
                'condition' => 'like_new',
                'is_negotiable' => true,
            ],
            [
                'title' => 'غسالة أوتوماتيك Samsung 9 كيلو',
                'description' => 'غسالة سامسونج أوتوماتيك فتحة أمامية، سعة 9 كيلو. بها خاصية البخار والتنظيف الذاتي. حالة جيدة جداً، استخدام منزلي.',
                'price' => 6500,
                'condition' => 'good',
                'is_negotiable' => true,
            ],
            [
                'title' => 'مكيف كاريير 2.25 حصان',
                'description' => 'مكيف هواء كاريير 2.25 حصان، بارد ساخن، إنفرتر موفر للكهرباء. حالة ممتازة، تم تركيبه قبل 6 أشهر. معاه الضمان والفاتورة الأصلية.',
                'price' => 11000,
                'condition' => 'like_new',
                'is_negotiable' => false,
            ],

            // Fashion & Accessories
            [
                'title' => 'شنطة Louis Vuitton أصلية',
                'description' => 'شنطة لويس فيتون أصلية موديل Neverfull، مقاس MM. حالة ممتازة جداً، استخدام خفيف جداً. معاها كرت الضمان والفاتورة الأصلية من المحل.',
                'price' => 28000,
                'condition' => 'like_new',
                'is_negotiable' => true,
            ],
            [
                'title' => 'حذاء رياضي Nike Air Jordan 1',
                'description' => 'حذاء نايكي اير جوردان 1، مقاس 43، لون أبيض وأحمر. جديد بالكرتونة الأصلية، لم يتم ارتداؤه. نسخة محدودة.',
                'price' => 4500,
                'condition' => 'new',
                'is_negotiable' => false,
            ],

            // Books & Education
            [
                'title' => 'مجموعة كتب طبية للطلاب',
                'description' => 'مجموعة كتب طبية شاملة لطلاب الطب: Anatomy, Physiology, Pathology, Pharmacology. حالة ممتازة، بدون أي تمزيق أو كتابة. مناسبة للسنوات الأولى.',
                'price' => 3500,
                'condition' => 'good',
                'is_negotiable' => true,
            ],

            // Baby & Kids
            [
                'title' => 'عربية أطفال Chicco',
                'description' => 'عربية أطفال شيكو إيطالية، قابلة للطي بسهولة. حالة ممتازة جداً، استخدام خفيف لمدة 6 أشهر. معاها غطاء المطر وشنطة الحفاضات.',
                'price' => 2800,
                'condition' => 'like_new',
                'is_negotiable' => true,
            ],
            [
                'title' => 'سرير أطفال خشب مع المرتبة',
                'description' => 'سرير أطفال خشبي قوي ومتين، لون أبيض. معاه مرتبة طبية مريحة. حالة جيدة جداً، نظيف ومعقم. مناسب من الولادة حتى 3 سنوات.',
                'price' => 1800,
                'condition' => 'good',
                'is_negotiable' => true,
            ],

            // Sports & Outdoor
            [
                'title' => 'جهاز مشي كهربائي',
                'description' => 'جهاز مشي كهربائي للتمارين المنزلية، شاشة LCD، سرعات متعددة، قابل للطي. حالة ممتازة، استخدام خفيف. مناسب لجميع أفراد الأسرة.',
                'price' => 7500,
                'condition' => 'like_new',
                'is_negotiable' => true,
            ],
            [
                'title' => 'خيمة تخييم عائلية 6 أشخاص',
                'description' => 'خيمة تخييم كبيرة تتسع لـ 6 أشخاص، مقاومة للماء والرياح. معاها حقيبة حمل وأوتاد وحبال. حالة جيدة، تم استخدامها 3 مرات فقط.',
                'price' => 2200,
                'condition' => 'good',
                'is_negotiable' => true,
            ],

            // Tools & Equipment
            [
                'title' => 'دريل بوش احترافي - شحن',
                'description' => 'دريل بوش احترافي يعمل بالبطارية، 18 فولت. معاه 2 بطارية وشاحن وحقيبة نقل. حالة ممتازة، استخدام منزلي بسيط.',
                'price' => 2500,
                'condition' => 'like_new',
                'is_negotiable' => true,
            ],

            // Musical Instruments
            [
                'title' => 'جيتار كلاسيكي Yamaha',
                'description' => 'جيتار كلاسيكي ياماها احترافي، صوت نقي وواضح. مناسب للمبتدئين والمحترفين. حالة ممتازة جداً. معاه شنطة حمل وريش إضافية.',
                'price' => 3800,
                'condition' => 'like_new',
                'is_negotiable' => true,
            ],

            // Gaming
            [
                'title' => 'Xbox Series X مع 3 ألعاب',
                'description' => 'إكس بوكس سيريز X، حالة ممتازة، استخدام خفيف. معاه 3 ألعاب (Halo Infinite, Forza Horizon 5, FIFA 24) ويد إضافي. كل شيء أصلي.',
                'price' => 13500,
                'condition' => 'good',
                'is_negotiable' => true,
            ],

            // Pets Accessories
            [
                'title' => 'قفص قطط كبير متعدد الطوابق',
                'description' => 'قفص قطط كبير 3 طوابق، مساحة واسعة للعب والراحة. معاه أماكن للنوم وألعاب معلقة. حالة ممتازة، سهل التنظيف.',
                'price' => 1200,
                'condition' => 'good',
                'is_negotiable' => true,
            ],

            // Office Supplies
            [
                'title' => 'مكتب خشبي مع كرسي دوار',
                'description' => 'مكتب خشبي للدراسة أو العمل من المنزل، مع أدراج للتخزين. معاه كرسي دوار مريح للظهر. حالة جيدة جداً.',
                'price' => 2500,
                'condition' => 'good',
                'is_negotiable' => true,
            ],
        ];

        // Create marketplace items
        foreach ($items as $index => $itemData) {
            $item = MarketplaceItem::create([
                'user_id' => $users->random()->id,
                'category_id' => $categories->random()->id,
                'city_id' => $cities->random()->id,
                'title' => $itemData['title'],
                'description' => $itemData['description'],
                'price' => $itemData['price'],
                'condition' => $itemData['condition'],
                'is_negotiable' => $itemData['is_negotiable'],
                'contact_phone' => '01' . rand(000000000, 999999999),
                'contact_whatsapp' => '01' . rand(000000000, 999999999),
                'status' => 'active',
                'view_count' => rand(10, 500),
                'contact_count' => rand(0, 50),
                'max_views' => 100,
                'images' => [], // You can add image URLs here if you have them
                'approved_at' => now(),
                'created_at' => now()->subDays(rand(1, 30)),
            ]);

            // Make some items sponsored (20% chance)
            if (rand(1, 5) === 1) {
                $item->update([
                    'is_sponsored' => true,
                    'sponsored_until' => now()->addDays(rand(7, 30)),
                    'sponsored_priority' => rand(1, 5),
                    'sponsored_views_boost' => rand(50, 200),
                ]);
            }
        }

        $this->command->info('✅ Marketplace seeded successfully with ' . count($items) . ' items!');
    }
}
