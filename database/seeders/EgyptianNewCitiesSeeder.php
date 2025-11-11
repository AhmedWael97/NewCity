<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\City;
use Illuminate\Support\Str;

class EgyptianNewCitiesSeeder extends Seeder
{
    /**
     * Run the database seeds for Egyptian New Cities.
     * These are the modern planned cities of Egypt, not traditional ones.
     */
    public function run(): void
    {
        $cities = [
            [
                'name' => 'العاصمة الإدارية الجديدة',
                'slug' => 'new-administrative-capital',
                'state' => 'القاهرة',
                'country' => 'مصر',
                'latitude' => 30.0219,
                'longitude' => 31.7547,
                'description' => 'العاصمة الجديدة المخططة لمصر، تضم المباني الحكومية والحي التجاري ووسائل الراحة الحديثة والمرافق الذكية.',
                'image' => 'https://images.unsplash.com/photo-1581833971358-2c8b550f87b3?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2071&q=80',
                'is_active' => true,
            ],
            [
                'name' => 'العلمين الجديدة',
                'slug' => 'new-alamein',
                'state' => 'مطروح',
                'country' => 'مصر',
                'latitude' => 30.8333,
                'longitude' => 28.9500,
                'description' => 'مدينة منتجع ساحلية كبرى على البحر المتوسط، مخططة لتكون عاصمة مصر الصيفية مع شواطئ خلابة ومرافق سياحية عالمية.',
                'image' => 'https://images.unsplash.com/photo-1544551763-46a013bb70d5?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80',
                'is_active' => true,
            ],
            [
                'name' => 'القاهرة الجديدة',
                'slug' => 'new-cairo',
                'state' => 'القاهرة',
                'country' => 'مصر',
                'latitude' => 30.0329,
                'longitude' => 31.4750,
                'description' => 'مدينة حديثة تابعة للقاهرة، موطن للجامعات والمجمعات السكنية والمراكز التجارية والأحياء الراقية.',
                'image' => 'https://images.unsplash.com/photo-1553913861-c0fddf2619ee?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2069&q=80',
                'is_active' => true,
            ],
            [
                'name' => 'مدينة الشيخ زايد',
                'slug' => 'sheikh-zayed-city',
                'state' => 'الجيزة',
                'country' => 'مصر',
                'latitude' => 30.0771,
                'longitude' => 30.9700,
                'description' => 'مدينة راقية تابعة للجيزة، معروفة بمجتمعاتها المخططة والبنية التحتية الحديثة والمولات التجارية الفاخرة.',
                'image' => 'https://images.unsplash.com/photo-1582555172866-f73bb12a2ab3?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2084&q=80',
                'is_active' => true,
            ],
            [
                'name' => 'مدينة السادس من أكتوبر',
                'slug' => '6th-october-city',
                'state' => 'الجيزة',
                'country' => 'مصر',
                'latitude' => 29.9097,
                'longitude' => 30.9467,
                'description' => 'مدينة جديدة كبرى في محافظة الجيزة، معروفة بجامعاتها ومناطقها الصناعية والمراكز التعليمية المتقدمة.',
                'image' => 'https://images.unsplash.com/photo-1449824913935-59a10b8d2000?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80',
                'is_active' => true,
            ],
            [
                'name' => 'مدينة العاشر من رمضان',
                'slug' => '10th-ramadan-city',
                'state' => 'الشرقية',
                'country' => 'مصر',
                'latitude' => 30.3127,
                'longitude' => 31.6975,
                'description' => 'مدينة صناعية جديدة في محافظة الشرقية، مركز رئيسي للتصنيع والنسيج والصناعات الحديثة.',
                'image' => 'https://images.unsplash.com/photo-1486406146926-c627a92ad1ab?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80',
                'is_active' => true,
            ],
            [
                'name' => 'المنصورة الجديدة',
                'slug' => 'new-mansoura',
                'state' => 'الدقهلية',
                'country' => 'مصر',
                'latitude' => 31.0409,
                'longitude' => 31.3785,
                'description' => 'مدينة جديدة مخططة بالقرب من المنصورة الأصلية، تضم مناطق سكنية وتجارية حديثة ومرافق متطورة.',
                'image' => 'https://images.unsplash.com/photo-1480714378408-67cf0d13bc1f?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2070&q=80',
                'is_active' => true,
            ],
            [
                'name' => 'مدينة الشروق',
                'slug' => 'el-shorouk-city',
                'state' => 'القاهرة',
                'country' => 'مصر',
                'latitude' => 30.1200,
                'longitude' => 31.6100,
                'description' => 'مدينة تابعة شمال شرق القاهرة، معروفة بمجمعاتها السكنية ومراكزها التجارية والمرافق الحديثة.',
                'image' => 'https://images.unsplash.com/photo-1519501025264-65ba15a82390?ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D&auto=format&fit=crop&w=2064&q=80',
                'is_active' => true,
            ]
        ];

        foreach ($cities as $cityData) {
            City::create($cityData);
        }

        $this->command->info('✅ Egyptian New Cities seeded successfully!');
        $this->command->line('📍 Created ' . count($cities) . ' new Egyptian cities with complete data including:');
        $this->command->line('   • Geographic coordinates');
        $this->command->line('   • Arabic names and descriptions');
        $this->command->line('   • Real image URLs from internet');
        $this->command->line('   • Complete city information');
        $this->command->line('   • Egyptian governorate data');
    }
}