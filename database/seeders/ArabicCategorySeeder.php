<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Category;

class ArabicCategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Main Categories with Sub-categories
        $categories = [
            // 1. المطاعم والمأكولات
            [
                'name' => 'مطاعم ومأكولات',
                'slug' => 'restaurants-food',
                'description' => 'جميع أنواع المطاعم والمأكولات',
                'icon' => '🍽️',
                'color' => '#FF6B6B',
                'sort_order' => 1,
                'is_active' => true,
                'parent_id' => null,
                'children' => [
                    ['name' => 'مطاعم شعبية', 'slug' => 'local-restaurants', 'icon' => '🏠', 'color' => '#FF5722'],
                    ['name' => 'مطاعم عالمية', 'slug' => 'international-restaurants', 'icon' => '🌍', 'color' => '#2196F3'],
                    ['name' => 'مطاعم أسماك', 'slug' => 'seafood-restaurants', 'icon' => '🐟', 'color' => '#00BCD4'],
                    ['name' => 'مطاعم فراخ', 'slug' => 'chicken-restaurants', 'icon' => '🐔', 'color' => '#FF9800'],
                    ['name' => 'مطاعم مشويات', 'slug' => 'grilled-restaurants', 'icon' => '🔥', 'color' => '#F44336'],
                    ['name' => 'مأكولات سريعة', 'slug' => 'fast-food', 'icon' => '🍔', 'color' => '#FFC107'],
                    ['name' => 'بيتزا', 'slug' => 'pizza', 'icon' => '🍕', 'color' => '#FF5722'],
                    ['name' => 'مأكولات شرقية', 'slug' => 'oriental-food', 'icon' => '🥙', 'color' => '#8BC34A'],
                    ['name' => 'حلويات شرقية', 'slug' => 'oriental-sweets', 'icon' => '🧁', 'color' => '#E91E63'],
                    ['name' => 'آيس كريم', 'slug' => 'ice-cream', 'icon' => '🍦', 'color' => '#9C27B0'],
                ]
            ],

            // 2. مقاهي ومشروبات
            [
                'name' => 'مقاهي ومشروبات',
                'slug' => 'cafes-beverages',
                'description' => 'مقاهي وجميع أنواع المشروبات',
                'icon' => '☕',
                'color' => '#8E44AD',
                'sort_order' => 2,
                'is_active' => true,
                'parent_id' => null,
                'children' => [
                    ['name' => 'مقاهي شعبية', 'slug' => 'traditional-cafes', 'icon' => '🫖', 'color' => '#795548'],
                    ['name' => 'كوفي شوب', 'slug' => 'coffee-shops', 'icon' => '☕', 'color' => '#6D4C41'],
                    ['name' => 'عصائر طبيعية', 'slug' => 'fresh-juices', 'icon' => '🥤', 'color' => '#FF9800'],
                    ['name' => 'كافيهات حديثة', 'slug' => 'modern-cafes', 'icon' => '🏢', 'color' => '#607D8B'],
                    ['name' => 'شاي ونسكافيه', 'slug' => 'tea-coffee', 'icon' => '🍵', 'color' => '#4CAF50'],
                    ['name' => 'عصير قصب', 'slug' => 'sugarcane-juice', 'icon' => '🌾', 'color' => '#8BC34A'],
                ]
            ],

            // 3. ملابس وأزياء
            [
                'name' => 'ملابس وأزياء',
                'slug' => 'clothing-fashion',
                'description' => 'جميع أنواع الملابس والأزياء',
                'icon' => '👕',
                'color' => '#4ECDC4',
                'sort_order' => 3,
                'is_active' => true,
                'parent_id' => null,
                'children' => [
                    ['name' => 'ملابس رجالي', 'slug' => 'mens-clothing', 'icon' => '👔', 'color' => '#2196F3'],
                    ['name' => 'ملابس حريمي', 'slug' => 'womens-clothing', 'icon' => '👗', 'color' => '#E91E63'],
                    ['name' => 'ملابس أطفال', 'slug' => 'kids-clothing', 'icon' => '👶', 'color' => '#4CAF50'],
                    ['name' => 'ملابس رياضية', 'slug' => 'sportswear', 'icon' => '🏃', 'color' => '#FF9800'],
                    ['name' => 'ملابس داخلية', 'slug' => 'underwear', 'icon' => '👙', 'color' => '#9C27B0'],
                    ['name' => 'ملابس نوم', 'slug' => 'sleepwear', 'icon' => '🌙', 'color' => '#3F51B5'],
                    ['name' => 'ملابس محجبات', 'slug' => 'hijab-clothing', 'icon' => '🧕', 'color' => '#673AB7'],
                    ['name' => 'ملابس عمل', 'slug' => 'work-clothing', 'icon' => '👷', 'color' => '#FF5722'],
                ]
            ],

            // 4. أحذية وإكسسوارات
            [
                'name' => 'أحذية وإكسسوارات',
                'slug' => 'shoes-accessories',
                'description' => 'أحذية وجميع الإكسسوارات',
                'icon' => '👟',
                'color' => '#795548',
                'sort_order' => 4,
                'is_active' => true,
                'parent_id' => null,
                'children' => [
                    ['name' => 'أحذية رجالي', 'slug' => 'mens-shoes', 'icon' => '👞', 'color' => '#5D4037'],
                    ['name' => 'أحذية حريمي', 'slug' => 'womens-shoes', 'icon' => '👠', 'color' => '#E91E63'],
                    ['name' => 'أحذية أطفال', 'slug' => 'kids-shoes', 'icon' => '👟', 'color' => '#4CAF50'],
                    ['name' => 'أحذية رياضية', 'slug' => 'sports-shoes', 'icon' => '⚽', 'color' => '#FF9800'],
                    ['name' => 'شباشب وصنادل', 'slug' => 'sandals-slippers', 'icon' => '🩴', 'color' => '#00BCD4'],
                    ['name' => 'حقائب يد', 'slug' => 'handbags', 'icon' => '👜', 'color' => '#9C27B0'],
                    ['name' => 'محافظ', 'slug' => 'wallets', 'icon' => '👛', 'color' => '#607D8B'],
                    ['name' => 'ساعات', 'slug' => 'watches', 'icon' => '⌚', 'color' => '#37474F'],
                    ['name' => 'نظارات', 'slug' => 'eyewear', 'icon' => '👓', 'color' => '#3F51B5'],
                ]
            ],

            // 5. إلكترونيات وتكنولوجيا
            [
                'name' => 'إلكترونيات وتكنولوجيا',
                'slug' => 'electronics-technology',
                'description' => 'جميع الأجهزة الإلكترونية والتكنولوجية',
                'icon' => '📱',
                'color' => '#45B7D1',
                'sort_order' => 5,
                'is_active' => true,
                'parent_id' => null,
                'children' => [
                    ['name' => 'موبايلات وتابلت', 'slug' => 'mobile-tablets', 'icon' => '📱', 'color' => '#607D8B'],
                    ['name' => 'لابتوب وكمبيوتر', 'slug' => 'computers-laptops', 'icon' => '💻', 'color' => '#37474F'],
                    ['name' => 'تلفزيونات', 'slug' => 'televisions', 'icon' => '📺', 'color' => '#424242'],
                    ['name' => 'أجهزة صوت', 'slug' => 'audio-systems', 'icon' => '🎵', 'color' => '#9C27B0'],
                    ['name' => 'كاميرات', 'slug' => 'cameras', 'icon' => '📷', 'color' => '#FF5722'],
                    ['name' => 'أجهزة منزلية', 'slug' => 'home-appliances', 'icon' => '🏠', 'color' => '#4CAF50'],
                    ['name' => 'إكسسوارات إلكترونية', 'slug' => 'electronics-accessories', 'icon' => '🔌', 'color' => '#FF9800'],
                    ['name' => 'ألعاب فيديو', 'slug' => 'video-games', 'icon' => '🎮', 'color' => '#3F51B5'],
                ]
            ],

            // 6. صحة وجمال
            [
                'name' => 'صحة وجمال',
                'slug' => 'health-beauty',
                'description' => 'منتجات الصحة والجمال والعناية',
                'icon' => '💊',
                'color' => '#2ECC71',
                'sort_order' => 6,
                'is_active' => true,
                'parent_id' => null,
                'children' => [
                    ['name' => 'صيدليات', 'slug' => 'pharmacies', 'icon' => '💊', 'color' => '#4CAF50'],
                    ['name' => 'مستحضرات تجميل', 'slug' => 'cosmetics', 'icon' => '💄', 'color' => '#E91E63'],
                    ['name' => 'عطور', 'slug' => 'perfumes', 'icon' => '🌸', 'color' => '#9C27B0'],
                    ['name' => 'كوافير رجالي', 'slug' => 'mens-salon', 'icon' => '💇‍♂️', 'color' => '#795548'],
                    ['name' => 'كوافير حريمي', 'slug' => 'womens-salon', 'icon' => '💇‍♀️', 'color' => '#E91E63'],
                    ['name' => 'منتجات طبيعية', 'slug' => 'natural-products', 'icon' => '🌿', 'color' => '#8BC34A'],
                    ['name' => 'أجهزة طبية', 'slug' => 'medical-equipment', 'icon' => '🩺', 'color' => '#00BCD4'],
                    ['name' => 'منتجات أطفال', 'slug' => 'baby-products', 'icon' => '🍼', 'color' => '#FF9800'],
                ]
            ],

            // 7. مواد غذائية ومنزلية
            [
                'name' => 'مواد غذائية ومنزلية',
                'slug' => 'grocery-household',
                'description' => 'جميع المواد الغذائية والمنزلية',
                'icon' => '🛒',
                'color' => '#F39C12',
                'sort_order' => 7,
                'is_active' => true,
                'parent_id' => null,
                'children' => [
                    ['name' => 'سوبر ماركت', 'slug' => 'supermarkets', 'icon' => '🏪', 'color' => '#4CAF50'],
                    ['name' => 'بقالة', 'slug' => 'grocery-stores', 'icon' => '🏪', 'color' => '#8BC34A'],
                    ['name' => 'جزارة', 'slug' => 'butcher-shops', 'icon' => '🥩', 'color' => '#F44336'],
                    ['name' => 'فراخ ودواجن', 'slug' => 'poultry', 'icon' => '🐔', 'color' => '#FF9800'],
                    ['name' => 'أسماك وثمار بحر', 'slug' => 'seafood', 'icon' => '🐟', 'color' => '#2196F3'],
                    ['name' => 'خضار وفاكهة', 'slug' => 'fruits-vegetables', 'icon' => '🥬', 'color' => '#4CAF50'],
                    ['name' => 'ألبان وأجبان', 'slug' => 'dairy-products', 'icon' => '🥛', 'color' => '#FFFFFF'],
                    ['name' => 'مخبوزات', 'slug' => 'bakery-products', 'icon' => '🥖', 'color' => '#8D6E63'],
                    ['name' => 'مواد تنظيف', 'slug' => 'cleaning-supplies', 'icon' => '🧽', 'color' => '#00BCD4'],
                ]
            ],

            // 8. منزل وديكور
            [
                'name' => 'منزل وديكور',
                'slug' => 'home-decor',
                'description' => 'أثاث ومستلزمات منزلية وديكور',
                'icon' => '🏠',
                'color' => '#8D6E63',
                'sort_order' => 8,
                'is_active' => true,
                'parent_id' => null,
                'children' => [
                    ['name' => 'أثاث منزلي', 'slug' => 'home-furniture', 'icon' => '🛋️', 'color' => '#6D4C41'],
                    ['name' => 'أثاث مكتبي', 'slug' => 'office-furniture', 'icon' => '🪑', 'color' => '#5D4037'],
                    ['name' => 'مفروشات', 'slug' => 'home-textiles', 'icon' => '🛏️', 'color' => '#795548'],
                    ['name' => 'ستائر', 'slug' => 'curtains', 'icon' => '🪟', 'color' => '#8BC34A'],
                    ['name' => 'إضاءة', 'slug' => 'lighting', 'icon' => '💡', 'color' => '#FFC107'],
                    ['name' => 'أدوات مطبخ', 'slug' => 'kitchen-tools', 'icon' => '🍴', 'color' => '#FF5722'],
                    ['name' => 'ديكورات', 'slug' => 'decorations', 'icon' => '🖼️', 'color' => '#9C27B0'],
                    ['name' => 'سجاد وموكيت', 'slug' => 'carpets-rugs', 'icon' => '🪄', 'color' => '#673AB7'],
                ]
            ],

            // 9. سيارات ومواصلات
            [
                'name' => 'سيارات ومواصلات',
                'slug' => 'automotive-transport',
                'description' => 'سيارات وقطع غيار وخدمات مواصلات',
                'icon' => '🚗',
                'color' => '#37474F',
                'sort_order' => 9,
                'is_active' => true,
                'parent_id' => null,
                'children' => [
                    ['name' => 'قطع غيار سيارات', 'slug' => 'auto-parts', 'icon' => '🔧', 'color' => '#FF5722'],
                    ['name' => 'ورش سيارات', 'slug' => 'auto-repair', 'icon' => '🔧', 'color' => '#F44336'],
                    ['name' => 'محطات وقود', 'slug' => 'gas-stations', 'icon' => '⛽', 'color' => '#4CAF50'],
                    ['name' => 'غسيل سيارات', 'slug' => 'car-wash', 'icon' => '🚿', 'color' => '#00BCD4'],
                    ['name' => 'إطارات', 'slug' => 'tires', 'icon' => '⚫', 'color' => '#424242'],
                    ['name' => 'زيوت ومواد تشحيم', 'slug' => 'oils-lubricants', 'icon' => '🛢️', 'color' => '#795548'],
                    ['name' => 'معدات صوتية للسيارات', 'slug' => 'car-audio', 'icon' => '🎵', 'color' => '#9C27B0'],
                ]
            ],

            // 10. رياضة وترفيه
            [
                'name' => 'رياضة وترفيه',
                'slug' => 'sports-entertainment',
                'description' => 'مستلزمات رياضية وألعاب وترفيه',
                'icon' => '⚽',
                'color' => '#4CAF50',
                'sort_order' => 10,
                'is_active' => true,
                'parent_id' => null,
                'children' => [
                    ['name' => 'معدات رياضية', 'slug' => 'sports-equipment', 'icon' => '🏋️', 'color' => '#FF9800'],
                    ['name' => 'ألعاب أطفال', 'slug' => 'toys', 'icon' => '🧸', 'color' => '#E91E63'],
                    ['name' => 'كتب ومجلات', 'slug' => 'books-magazines', 'icon' => '📚', 'color' => '#795548'],
                    ['name' => 'قرطاسية', 'slug' => 'stationery', 'icon' => '✏️', 'color' => '#2196F3'],
                    ['name' => 'آلات موسيقية', 'slug' => 'musical-instruments', 'icon' => '🎸', 'color' => '#9C27B0'],
                    ['name' => 'ألعاب إلكترونية', 'slug' => 'electronic-games', 'icon' => '🕹️', 'color' => '#3F51B5'],
                ]
            ],

            // 11. خدمات
            [
                'name' => 'خدمات',
                'slug' => 'services',
                'description' => 'جميع أنواع الخدمات',
                'icon' => '🛎️',
                'color' => '#2196F3',
                'sort_order' => 11,
                'is_active' => true,
                'parent_id' => null,
                'children' => [
                    ['name' => 'خدمات مالية', 'slug' => 'financial-services', 'icon' => '🏦', 'color' => '#1976D2'],
                    ['name' => 'اتصالات وإنترنت', 'slug' => 'telecommunications', 'icon' => '📞', 'color' => '#9C27B0'],
                    ['name' => 'خدمات توصيل', 'slug' => 'delivery-services', 'icon' => '🚚', 'color' => '#FF9800'],
                    ['name' => 'خدمات تنظيف', 'slug' => 'cleaning-services', 'icon' => '🧽', 'color' => '#4CAF50'],
                    ['name' => 'خدمات منزلية', 'slug' => 'home-services', 'icon' => '🔨', 'color' => '#FF5722'],
                    ['name' => 'خدمات طباعة', 'slug' => 'printing-services', 'icon' => '🖨️', 'color' => '#607D8B'],
                    ['name' => 'خدمات قانونية', 'slug' => 'legal-services', 'icon' => '⚖️', 'color' => '#795548'],
                ]
            ],

            // 12. تعليم وصحة
            [
                'name' => 'تعليم وصحة',
                'slug' => 'education-healthcare',
                'description' => 'خدمات تعليمية وصحية',
                'icon' => '🎓',
                'color' => '#F44336',
                'sort_order' => 12,
                'is_active' => true,
                'parent_id' => null,
                'children' => [
                    ['name' => 'مراكز تعليمية', 'slug' => 'educational-centers', 'icon' => '📖', 'color' => '#2196F3'],
                    ['name' => 'حضانات', 'slug' => 'nurseries', 'icon' => '👶', 'color' => '#FF9800'],
                    ['name' => 'عيادات طبية', 'slug' => 'medical-clinics', 'icon' => '🏥', 'color' => '#F44336'],
                    ['name' => 'مختبرات طبية', 'slug' => 'medical-labs', 'icon' => '🧪', 'color' => '#00BCD4'],
                    ['name' => 'عيادات أسنان', 'slug' => 'dental-clinics', 'icon' => '🦷', 'color' => '#FFFFFF'],
                    ['name' => 'مراكز علاج طبيعي', 'slug' => 'physiotherapy', 'icon' => '🏃', 'color' => '#4CAF50'],
                    ['name' => 'مراكز لياقة', 'slug' => 'fitness-centers', 'icon' => '💪', 'color' => '#FF9800'],
                ]
            ],

            // 13. تراث وحرف
            [
                'name' => 'تراث وحرف',
                'slug' => 'heritage-crafts',
                'description' => 'منتجات تراثية وحرف يدوية',
                'icon' => '🏺',
                'color' => '#8D6E63',
                'sort_order' => 13,
                'is_active' => true,
                'parent_id' => null,
                'children' => [
                    ['name' => 'عطارة وأعشاب', 'slug' => 'spices-herbs', 'icon' => '🌿', 'color' => '#4CAF50'],
                    ['name' => 'تحف وهدايا', 'slug' => 'gifts-souvenirs', 'icon' => '🎁', 'color' => '#E91E63'],
                    ['name' => 'حرف يدوية', 'slug' => 'handicrafts', 'icon' => '🖐️', 'color' => '#795548'],
                    ['name' => 'منتجات فخار', 'slug' => 'pottery', 'icon' => '🏺', 'color' => '#8D6E63'],
                    ['name' => 'منسوجات تراثية', 'slug' => 'traditional-textiles', 'icon' => '🧵', 'color' => '#9C27B0'],
                    ['name' => 'منتجات جلدية', 'slug' => 'leather-products', 'icon' => '🎒', 'color' => '#6D4C41'],
                ]
            ],

            // 14. حيوانات أليفة
            [
                'name' => 'حيوانات أليفة',
                'slug' => 'pets',
                'description' => 'مستلزمات وخدمات الحيوانات الأليفة',
                'icon' => '🐕',
                'color' => '#FF9800',
                'sort_order' => 14,
                'is_active' => true,
                'parent_id' => null,
                'children' => [
                    ['name' => 'طعام حيوانات', 'slug' => 'pet-food', 'icon' => '🍖', 'color' => '#F44336'],
                    ['name' => 'إكسسوارات حيوانات', 'slug' => 'pet-accessories', 'icon' => '🦴', 'color' => '#8BC34A'],
                    ['name' => 'عيادات بيطرية', 'slug' => 'veterinary-clinics', 'icon' => '🐾', 'color' => '#4CAF50'],
                    ['name' => 'فندقة حيوانات', 'slug' => 'pet-hotels', 'icon' => '🏠', 'color' => '#2196F3'],
                    ['name' => 'تدريب حيوانات', 'slug' => 'pet-training', 'icon' => '🎯', 'color' => '#FF9800'],
                ]
            ],

            // 15. عام
            [
                'name' => 'عام',
                'slug' => 'general',
                'description' => 'فئة عامة لجميع الأنشطة الأخرى',
                'icon' => '🏪',
                'color' => '#95A5A6',
                'sort_order' => 15,
                'is_active' => true,
                'parent_id' => null,
                'children' => []
            ],
        ];

        foreach ($categories as $categoryData) {
            $children = $categoryData['children'] ?? [];
            unset($categoryData['children']);
            
            // Create or update main category
            $mainCategory = Category::updateOrCreate(
                ['name' => $categoryData['name']],
                $categoryData
            );

            // Create sub-categories
            foreach ($children as $index => $childData) {
                $childData['parent_id'] = $mainCategory->id;
                $childData['description'] = $childData['description'] ?? $childData['name'];
                $childData['sort_order'] = $index + 1;
                $childData['is_active'] = true;

                Category::updateOrCreate(
                    ['name' => $childData['name'], 'parent_id' => $mainCategory->id],
                    $childData
                );
            }
        }
    }
}
