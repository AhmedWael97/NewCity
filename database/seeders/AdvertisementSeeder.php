<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Advertisement;
use App\Models\City;
use App\Models\Category;

class AdvertisementSeeder extends Seeder
{
    public function run()
    {
        $cities = City::all();
        $categories = Category::all();

        $advertisements = [
            [
                'title' => 'اكتشف أفضل مطاعم القاهرة',
                'description' => 'جرب أشهى الأطباق من أفضل المطاعم في العاصمة مع خصم 20%',
                'placement' => 'city_landing',
                'click_url' => 'https://example.com/restaurants',
                'type' => 'hero',
                'scope' => 'city_specific',
                'city_id' => $cities->where('name_ar', 'القاهرة')->first()?->id,
                'target_categories' => [$categories->where('name_ar', 'مطاعم')->first()?->id],
                'pricing_model' => 'cpm',
                'price_amount' => 5.00,
                'budget_total' => 1000,
                'budget_daily' => 100,
                'start_date' => now(),
                'status' => 'active',
                'impressions' => 15420,
                'clicks' => 820,
                'conversions' => 45,
                'spent_amount' => 245.30,
                'advertiser_name' => 'شركة المطاعم المميزة',
                'advertiser_email' => 'contact@restaurants.com'
            ],
            [
                'title' => 'تسوق أونلاين - توصيل مجاني',
                'description' => 'تسوق من أفضل المتاجر مع توصيل مجاني لجميع أنحاء المدينة',
                'placement' => 'homepage',
                'click_url' => 'https://example.com/shopping',
                'type' => 'banner',
                'scope' => 'global',
                'pricing_model' => 'cpc',
                'price_amount' => 0.50,
                'budget_total' => 500,
                'start_date' => now(),
                'status' => 'active',
                'impressions' => 8750,
                'clicks' => 425,
                'conversions' => 28,
                'spent_amount' => 212.50,
                'advertiser_name' => 'متجر التسوق الإلكتروني',
                'advertiser_email' => 'info@shopping.com'
            ],
            [
                'title' => 'صيدلية النهار - خدمة 24 ساعة',
                'description' => 'أدوية وعلاجات متوفرة على مدار الساعة',
                'placement' => 'shop_page',
                'click_url' => 'https://example.com/pharmacy',
                'type' => 'sidebar',
                'scope' => 'city_specific',
                'city_id' => $cities->where('name_ar', 'الإسكندرية')->first()?->id,
                'target_categories' => [$categories->where('name_ar', 'صيدليات')->first()?->id],
                'pricing_model' => 'cpa',
                'price_amount' => 2.00,
                'budget_total' => 300,
                'start_date' => now(),
                'status' => 'active',
                'impressions' => 5200,
                'clicks' => 180,
                'conversions' => 12,
                'spent_amount' => 24.00,
                'advertiser_name' => 'صيدلية النهار',
                'advertiser_email' => 'pharmacy@nahar.com'
            ],
            [
                'title' => 'معرض الأزياء الجديد',
                'description' => 'أحدث صيحات الموضة بأسعار مناسبة',
                'placement' => 'category_page',
                'click_url' => 'https://example.com/fashion',
                'type' => 'sponsored_listing',
                'scope' => 'global',
                'target_categories' => [$categories->where('name_ar', 'ملابس')->first()?->id],
                'pricing_model' => 'cpm',
                'price_amount' => 3.50,
                'budget_total' => 750,
                'start_date' => now(),
                'status' => 'active',
                'impressions' => 12300,
                'clicks' => 680,
                'conversions' => 35,
                'spent_amount' => 430.50,
                'advertiser_name' => 'معرض الأزياء الحديثة',
                'advertiser_email' => 'fashion@modern.com'
            ],
            [
                'title' => 'متجر الإلكترونيات الذكية',
                'description' => 'أحدث الأجهزة الذكية بضمان شامل',
                'placement' => 'homepage',
                'click_url' => 'https://example.com/electronics',
                'type' => 'banner',
                'scope' => 'city_specific',
                'city_id' => $cities->where('name_ar', 'الجيزة')->first()?->id,
                'target_categories' => [$categories->where('name_ar', 'إلكترونيات')->first()?->id],
                'pricing_model' => 'cpc',
                'price_amount' => 0.75,
                'budget_total' => 400,
                'start_date' => now(),
                'status' => 'active',
                'impressions' => 6800,
                'clicks' => 340,
                'conversions' => 22,
                'spent_amount' => 255.00,
                'advertiser_name' => 'متجر الذكاء التقني',
                'advertiser_email' => 'tech@smart.com'
            ],
            [
                'title' => 'مول التسوق الجديد',
                'description' => 'افتتاح جديد مع عروض حصرية لأول 1000 زائر',
                'placement' => 'city_landing',
                'click_url' => 'https://example.com/mall',
                'type' => 'hero',
                'scope' => 'global',
                'pricing_model' => 'cpm',
                'price_amount' => 8.00,
                'budget_total' => 2000,
                'budget_daily' => 200,
                'start_date' => now(),
                'status' => 'pending_review',
                'impressions' => 0,
                'clicks' => 0,
                'conversions' => 0,
                'spent_amount' => 0,
                'advertiser_name' => 'مول المدينة الجديد',
                'advertiser_email' => 'mall@newcity.com'
            ]
        ];

        foreach ($advertisements as $ad) {
            // Only create if city exists (for city-specific ads)
            if ($ad['scope'] === 'city_specific' && !$ad['city_id']) {
                continue;
            }
            
            // Only create if categories exist
            if (!empty($ad['target_categories']) && in_array(null, $ad['target_categories'])) {
                continue;
            }
            
            // Calculate CTR
            $ctr = $ad['impressions'] > 0 ? ($ad['clicks'] / $ad['impressions']) * 100 : 0;
            $ad['ctr'] = round($ctr, 2);
            
            Advertisement::create($ad);
        }
    }
}