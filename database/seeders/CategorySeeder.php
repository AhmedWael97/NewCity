<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Restaurants',
                'slug' => 'restaurants',
                'description' => 'Food and dining establishments',
                'icon' => 'restaurant',
                'color' => '#FF6B6B',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'name' => 'Shopping',
                'slug' => 'shopping',
                'description' => 'Retail stores and shopping centers',
                'icon' => 'shopping_bag',
                'color' => '#4ECDC4',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'name' => 'Health & Medical',
                'slug' => 'health-medical',
                'description' => 'Healthcare services and medical facilities',
                'icon' => 'local_hospital',
                'color' => '#45B7D1',
                'sort_order' => 3,
                'is_active' => true,
            ],
            [
                'name' => 'Beauty & Spa',
                'slug' => 'beauty-spa',
                'description' => 'Beauty salons, spas, and wellness centers',
                'icon' => 'spa',
                'color' => '#F7931E',
                'sort_order' => 4,
                'is_active' => true,
            ],
            [
                'name' => 'Automotive',
                'slug' => 'automotive',
                'description' => 'Car services, repairs, and automotive shops',
                'icon' => 'directions_car',
                'color' => '#6C5CE7',
                'sort_order' => 5,
                'is_active' => true,
            ],
            [
                'name' => 'Entertainment',
                'slug' => 'entertainment',
                'description' => 'Entertainment venues, cinemas, and recreational facilities',
                'icon' => 'local_movies',
                'color' => '#FD79A8',
                'sort_order' => 6,
                'is_active' => true,
            ],
            [
                'name' => 'Education',
                'slug' => 'education',
                'description' => 'Schools, training centers, and educational institutions',
                'icon' => 'school',
                'color' => '#00B894',
                'sort_order' => 7,
                'is_active' => true,
            ],
            [
                'name' => 'Professional Services',
                'slug' => 'professional-services',
                'description' => 'Legal, financial, and business services',
                'icon' => 'business',
                'color' => '#2D3436',
                'sort_order' => 8,
                'is_active' => true,
            ],
        ];

        foreach ($categories as $category) {
            \App\Models\Category::create($category);
        }
    }
}
