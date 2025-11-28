<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\NewsCategory;
use App\Models\News;
use Illuminate\Support\Str;

class NewsSampleSeeder extends Seeder
{
    public function run(): void
    {
        // Create categories
        $categories = [
            ['name' => 'أخبار محلية',  'order' => 1],
            ['name' => 'فعاليات',  'order' => 2],
            ['name' => 'عروض وخصومات',  'order' => 3],
            ['name' => 'تحديثات',  'order' => 4],
        ];

        foreach ($categories as $categoryData) {
            NewsCategory::create([
                'name' => $categoryData['name'],
                'slug' => Str::slug($categoryData['name']),
                'is_active' => true,
                'order' => $categoryData['order'],
            ]);
        }

        // Create sample news
        $newsCategory = NewsCategory::first();
        
        $newsItems = [
            [
                'title' => 'افتتاح متجر جديد في المدينة',
                'description' => 'نرحب بافتتاح متجر جديد يقدم أفضل المنتجات والخدمات لسكان المدينة',
                'content' => 'نحن سعداء بالإعلان عن افتتاح متجر جديد في قلب المدينة. يقدم المتجر مجموعة واسعة من المنتجات عالية الجودة والخدمات المميزة التي تلبي احتياجات جميع أفراد الأسرة.

يتميز المتجر بتصميمه العصري وموقعه الاستراتيجي، مما يسهل على الزوار الوصول إليه والاستمتاع بتجربة تسوق مريحة وممتعة.

نتطلع إلى خدمتكم وتقديم أفضل تجربة تسوق لكم.',
            ],
            [
                'title' => 'عروض خاصة لنهاية الأسبوع',
                'description' => 'لا تفوت العروض الحصرية المتاحة فقط خلال نهاية هذا الأسبوع',
                'content' => 'بمناسبة نهاية الأسبوع، نقدم لكم مجموعة من العروض الحصرية على مختلف المنتجات والخدمات.

تشمل العروض خصومات تصل إلى 50% على منتجات مختارة، بالإضافة إلى هدايا مجانية مع كل عملية شراء.

العروض متاحة لفترة محدودة، فبادر بزيارتنا واستفد من هذه الفرصة الذهبية!',
            ],
            [
                'title' => 'فعالية مجتمعية يوم السبت القادم',
                'description' => 'انضم إلينا في فعالية مجتمعية ممتعة للعائلات والأطفال',
                'content' => 'ندعوكم للمشاركة في فعالية مجتمعية خاصة ننظمها يوم السبت القادم في الساحة الرئيسية.

ستتضمن الفعالية:
- أنشطة ترفيهية للأطفال
- مسابقات وجوائز قيمة
- عروض طعام وشراب
- موسيقى حية

الدخول مجاني للجميع، ونتطلع لرؤيتكم هناك!',
            ],
            [
                'title' => 'تطبيق جديد لتسهيل التسوق',
                'description' => 'أطلقنا تطبيقاً جديداً يجعل تجربة التسوق أسهل وأسرع',
                'content' => 'يسعدنا الإعلان عن إطلاق تطبيقنا الجديد المتاح الآن على أجهزة iOS و Android.

مميزات التطبيق:
- تصفح سهل وسريع للمنتجات
- إشعارات فورية بالعروض الجديدة
- نظام نقاط ومكافآت
- خدمة عملاء متاحة 24/7

حمّل التطبيق الآن واحصل على خصم 20% على أول طلب لك!',
            ],
        ];

        foreach ($newsItems as $newsData) {
            News::create([
                'title' => $newsData['title'],
                'slug' => Str::slug($newsData['title']),
                'description' => $newsData['description'],
                'content' => $newsData['content'],
                'category_id' => $newsCategory->id,
                'is_active' => true,
                'published_at' => now()->subDays(rand(1, 30)),
                'views_count' => rand(50, 500),
            ]);
        }

        $this->command->info('News sample data created successfully!');
    }
}
