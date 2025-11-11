<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlansSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $plans = [
            [
                'name' => 'الخطة المجانية',
                'slug' => 'free',
                'description' => 'خطة أساسية مجانية للمتاجر الصغيرة',
                'monthly_price' => 0,
                'yearly_price' => 0,
                'max_shops' => 1,
                'max_products_per_shop' => 10,
                'max_services_per_shop' => 5,
                'max_images_per_shop' => 5,
                'analytics_access' => false,
                'priority_listing' => false,
                'verified_badge' => false,
                'custom_branding' => false,
                'social_media_integration' => false,
                'email_marketing' => false,
                'advanced_seo' => false,
                'customer_support' => false,
                'features' => [
                    'إنشاء متجر واحد',
                    'حتى 10 منتجات',
                    'حتى 5 خدمات',
                    'حتى 5 صور',
                    'عرض أساسي'
                ],
                'is_active' => true,
                'is_popular' => false,
                'sort_order' => 1
            ],
            [
                'name' => 'الخطة الأساسية',
                'slug' => 'basic',
                'description' => 'خطة متقدمة للمتاجر المتوسطة مع مميزات إضافية',
                'monthly_price' => 99,
                'yearly_price' => 990, // 2 months free
                'max_shops' => 3,
                'max_products_per_shop' => 50,
                'max_services_per_shop' => 25,
                'max_images_per_shop' => 20,
                'analytics_access' => true,
                'priority_listing' => true,
                'verified_badge' => true,
                'custom_branding' => false,
                'social_media_integration' => true,
                'email_marketing' => false,
                'advanced_seo' => true,
                'customer_support' => true,
                'features' => [
                    'حتى 3 متاجر',
                    'حتى 50 منتج لكل متجر',
                    'حتى 25 خدمة لكل متجر',
                    'حتى 20 صورة لكل متجر',
                    'تحليلات متقدمة',
                    'أولوية في النتائج',
                    'شارة التحقق',
                    'تكامل وسائل التواصل',
                    'تحسين محركات البحث',
                    'دعم فني متقدم'
                ],
                'is_active' => true,
                'is_popular' => true,
                'sort_order' => 2
            ],
            [
                'name' => 'الخطة المتقدمة',
                'slug' => 'premium',
                'description' => 'خطة شاملة للمتاجر الكبيرة مع جميع المميزات',
                'monthly_price' => 299,
                'yearly_price' => 2990, // 3 months free
                'max_shops' => 10,
                'max_products_per_shop' => 999,
                'max_services_per_shop' => 999,
                'max_images_per_shop' => 100,
                'analytics_access' => true,
                'priority_listing' => true,
                'verified_badge' => true,
                'custom_branding' => true,
                'social_media_integration' => true,
                'email_marketing' => true,
                'advanced_seo' => true,
                'customer_support' => true,
                'features' => [
                    'حتى 10 متاجر',
                    'منتجات وخدمات غير محدودة',
                    'حتى 100 صورة لكل متجر',
                    'تحليلات شاملة ومتقدمة',
                    'أولوية قصوى في النتائج',
                    'شارة التحقق الذهبية',
                    'تخصيص كامل للعلامة التجارية',
                    'تكامل كامل وسائل التواصل',
                    'تسويق عبر البريد الإلكتروني',
                    'تحسين متقدم لمحركات البحث',
                    'دعم فني مخصص 24/7',
                    'تقارير مفصلة',
                    'واجهة برمجة تطبيقات API'
                ],
                'is_active' => true,
                'is_popular' => false,
                'sort_order' => 3
            ]
        ];

        foreach ($plans as $planData) {
            SubscriptionPlan::updateOrCreate(
                ['slug' => $planData['slug']],
                $planData
            );
        }
    }
}
