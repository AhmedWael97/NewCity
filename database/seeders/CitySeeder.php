<?php

namespace Database\Seeders;

use App\Models\City;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class CitySeeder extends Seeder
{
    /**
     * Run the database seeder.
     */
    public function run()
    {
        $cities = [
            [
                'name' => 'الرياض',
                'slug' => 'riyadh',
                'state' => 'الرياض',
                'country' => 'السعودية',
                'latitude' => 24.7136,
                'longitude' => 46.6753,
                'description' => 'عاصمة المملكة العربية السعودية',
                'is_active' => true,
            ],
            [
                'name' => 'جدة',
                'slug' => 'jeddah',
                'state' => 'مكة المكرمة',
                'country' => 'السعودية',
                'latitude' => 21.4858,
                'longitude' => 39.1925,
                'description' => 'عروس البحر الأحمر',
                'is_active' => true,
            ],
            [
                'name' => 'الدمام',
                'slug' => 'dammam',
                'state' => 'المنطقة الشرقية',
                'country' => 'السعودية',
                'latitude' => 26.4207,
                'longitude' => 50.0888,
                'description' => 'عاصمة المنطقة الشرقية',
                'is_active' => true,
            ],
            [
                'name' => 'مكة المكرمة',
                'slug' => 'makkah',
                'state' => 'مكة المكرمة',
                'country' => 'السعودية',
                'latitude' => 21.3891,
                'longitude' => 39.8579,
                'description' => 'أم القرى',
                'is_active' => true,
            ],
            [
                'name' => 'المدينة المنورة',
                'slug' => 'madinah',
                'state' => 'المدينة المنورة',
                'country' => 'السعودية',
                'latitude' => 24.5247,
                'longitude' => 39.5692,
                'description' => 'المدينة المنورة',
                'is_active' => true,
            ],
            [
                'name' => 'تبوك',
                'slug' => 'tabuk',
                'state' => 'تبوك',
                'country' => 'السعودية',
                'latitude' => 28.3998,
                'longitude' => 36.5700,
                'description' => 'مدينة تبوك',
                'is_active' => true,
            ],
        ];

        foreach ($cities as $cityData) {
            City::firstOrCreate(
                ['slug' => $cityData['slug']], // Check by slug
                $cityData
            );
        }

        $this->command->info('Cities seeded successfully!');
    }
}