<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Product;
use App\Models\Shop;
use App\Models\Category;
use Faker\Factory as Faker;
use Illuminate\Support\Str;

class ProductsSeeder extends Seeder
{
    public function run(): void
    {
        $faker = Faker::create('ar_SA');
        
        // Get all shops
        $shops = Shop::with('category')->get();
        
        $this->command->info('🏪 Starting to seed products for shops...');
        $this->command->getOutput()->progressStart($shops->count());
        
        foreach ($shops as $shop) {
            // Generate 3-15 products per shop
            $productsCount = $faker->numberBetween(3, 15);
            
            for ($i = 0; $i < $productsCount; $i++) {
                $this->createProductForShop($shop, $faker);
            }
            
            $this->command->getOutput()->progressAdvance();
        }
        
        $this->command->getOutput()->progressFinish();
        $this->command->info('✅ Successfully seeded products!');
        
        // Show statistics
        $totalProducts = Product::count();
        $this->command->line("📊 Total Products: {$totalProducts}");
        
        // Show distribution by category
        $categories = Category::withCount('shops')->get();
        foreach ($categories as $category) {
            $productsInCategory = Product::whereHas('shop', function($query) use ($category) {
                $query->where('category_id', $category->id);
            })->count();
            
            if ($productsInCategory > 0) {
                $this->command->line("   • {$category->name}: {$productsInCategory} products");
            }
        }
    }
    
    private function createProductForShop($shop, $faker)
    {
        $categoryName = $shop->category->name ?? 'عام';
        
        $productData = $this->generateProductByCategory($categoryName, $faker);
        
        // Generate price and discount
        $basePrice = $faker->randomFloat(2, 10, 2000);
        $hasDiscount = $faker->boolean(30); // 30% chance of discount
        
        if ($hasDiscount) {
            $discountPercentage = $faker->numberBetween(5, 50);
            $originalPrice = $basePrice;
            $finalPrice = $originalPrice - ($originalPrice * $discountPercentage / 100);
        } else {
            $originalPrice = null;
            $finalPrice = $basePrice;
            $discountPercentage = 0;
        }
        
        Product::create([
            'shop_id' => $shop->id,
            'name' => $productData['name'],
            'description' => $productData['description'],
            'price' => $finalPrice,
            'slug' => Str::slug($productData['name'] . '-' . Str::random(6)),
            'original_price' => $originalPrice,
            'discount_percentage' => $discountPercentage,
            'images' => $this->generateProductImages($categoryName, $faker),
            'sku' => $this->generateSKU($faker),
            'stock_quantity' => $faker->numberBetween(0, 100),
            'is_available' => $faker->boolean(95), // 95% available
            'is_featured' => $faker->boolean(20), // 20% featured
            'specifications' => $productData['specifications'],
            'unit' => $productData['unit'],
            'weight' => $productData['weight'],
            'brand' => $productData['brand'],
            'sort_order' => $faker->numberBetween(0, 100),
        ]);
    }
    
    private function generateProductByCategory($categoryName, $faker)
    {
        $products = [
            'مطاعم' => [
                'names' => [
                    'وجبة كباب مشوي', 'طبق كشري مخلوط', 'فراخ محشية بالأرز', 'سمك مقلي بالطحينة',
                    'فتة لحمة ضاني', 'مولوخية باللحمة', 'محشي ورق عنب', 'رقاق باللحمة',
                    'فراخ بانيه مقرمشة', 'بيتزا مارجريتا', 'برجر لحم مشوي', 'شاورما دجاج'
                ],
                'units' => ['طبق', 'وجبة', 'قطعة', 'حبة'],
                'brands' => ['مطعم الشيف', 'بيت الأكل', 'المذاق الأصيل', 'كنتاكي', 'ماكدونالدز']
            ],
            'ملابس' => [
                'names' => [
                    'قميص قطني رجالي', 'فستان صيفي نسائي', 'بنطلون جينز', 'جاكيت شتوي',
                    'فستان سهرة أنيق', 'قميص نوم حريري', 'بلوزة كاجوال', 'معطف صوف',
                    'تي شيرت رياضي', 'سويت شيرت قطني', 'فستان محجبات', 'بدلة رسمية'
                ],
                'units' => ['قطعة', 'زوج', 'طقم'],
                'brands' => ['زارا', 'H&M', 'LC Waikiki', 'مودانيسا', 'Nike', 'Adidas']
            ],
            'إلكترونيات' => [
                'names' => [
                    'هاتف ذكي', 'لابتوب محمول', 'تلفزيون LED', 'سماعات بلوتوث',
                    'ساعة ذكية', 'كاميرا رقمية', 'مكيف هواء', 'غسالة أتوماتيك',
                    'ثلاجة نوفروست', 'مكنسة كهربائية', 'مايكروويف', 'طابعة ليزر'
                ],
                'units' => ['جهاز', 'قطعة', 'وحدة'],
                'brands' => ['سامسونج', 'آبل', 'هواوي', 'LG', 'سوني', 'ديل', 'HP']
            ],
            'صيدليات' => [
                'names' => [
                    'فيتامين د 3', 'كريم واقي الشمس', 'شامبو طبي', 'مرهم مضاد حيوي',
                    'أقراص مسكن للألم', 'قطرة عين مرطبة', 'معجون أسنان طبي', 'غسول فم',
                    'كريم مرطب للبشرة', 'سيروم فيتامين سي', 'حبوب كالسيوم', 'شراب كحة'
                ],
                'units' => ['علبة', 'زجاجة', 'أنبوبة', 'قطعة'],
                'brands' => ['فايزر', 'سانوفي', 'نوفارتيس', 'جلاكسو', 'باير']
            ],
            'سوبر ماركت' => [
                'names' => [
                    'أرز مصري فاخر', 'زيت عباد الشمس', 'سكر أبيض ناعم', 'شاي أحمر',
                    'قهوة تركية', 'عسل نحل طبيعي', 'معكرونة إيطالية', 'صلصة طماطم',
                    'جبن أبيض طري', 'لبن طازج', 'خبز أسمر', 'بيض بلدي'
                ],
                'units' => ['كيلو', 'لتر', 'علبة', 'كيس', 'زجاجة'],
                'brands' => ['العلالي', 'هاينز', 'نستله', 'المراعي', 'جهينة', 'دومتي']
            ]
        ];
        
        $categoryProducts = $products[$categoryName] ?? $products['سوبر ماركت'];
        
        $name = $faker->randomElement($categoryProducts['names']);
        $unit = $faker->randomElement($categoryProducts['units']);
        $brand = $faker->randomElement($categoryProducts['brands']);
        
        return [
            'name' => $name,
            'description' => $this->generateProductDescription($name, $faker),
            'specifications' => $this->generateSpecifications($categoryName, $faker),
            'unit' => $unit,
            'weight' => $categoryName === 'ملابس' ? null : $faker->randomFloat(2, 0.1, 5),
            'brand' => $brand
        ];
    }
    
    private function generateProductDescription($productName, $faker)
    {
        $templates = [
            "منتج عالي الجودة - {product} بأفضل المواصفات والخامات الممتازة",
            "{product} أصلي ومضمون بضمان الشركة المصنعة لمدة عام كامل",
            "احصل على {product} بأعلى جودة وأفضل سعر في السوق",
            "{product} مناسب للاستخدام اليومي بتصميم عملي وأنيق",
            "تميز بـ {product} الفاخر والمصنوع من أجود الخامات"
        ];
        
        $template = $faker->randomElement($templates);
        return str_replace('{product}', $productName, $template);
    }
    
    private function generateSpecifications($categoryName, $faker)
    {
        $specs = [
            'مطاعم' => [
                'طريقة التحضير' => ['مشوي', 'مقلي', 'مسلوق', 'مطبوخ'],
                'الوقت المطلوب' => ['15 دقيقة', '30 دقيقة', '45 دقيقة'],
                'نوع اللحم' => ['دجاج', 'لحم بقري', 'لحم ضاني', 'سمك']
            ],
            'ملابس' => [
                'المقاس' => ['S', 'M', 'L', 'XL', 'XXL'],
                'اللون' => ['أبيض', 'أسود', 'أزرق', 'أحمر', 'بني'],
                'الخامة' => ['قطن', 'حرير', 'بوليستر', 'صوف']
            ],
            'إلكترونيات' => [
                'القدرة' => ['100 واط', '200 واط', '500 واط'],
                'الجهد' => ['220 فولت', '110 فولت'],
                'الضمان' => ['سنة واحدة', 'سنتين', '3 سنوات']
            ]
        ];
        
        $categorySpecs = $specs[$categoryName] ?? [];
        $result = [];
        
        foreach ($categorySpecs as $key => $values) {
            if ($faker->boolean(70)) { // 70% chance to include each spec
                $result[$key] = $faker->randomElement($values);
            }
        }
        
        return $result;
    }
    
    private function generateProductImages($categoryName, $faker)
    {
        // Sample product images based on category
        $imageCategories = [
            'مطاعم' => [
                'https://images.unsplash.com/photo-1555939594-58d7cb561ad1?w=400',
                'https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=400',
                'https://images.unsplash.com/photo-1565299624946-b28f40a0ca4b?w=400'
            ],
            'ملابس' => [
                'https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=400',
                'https://images.unsplash.com/photo-1445205170230-053b83016050?w=400',
                'https://images.unsplash.com/photo-1434389677669-e08b4cac3105?w=400'
            ],
            'إلكترونيات' => [
                'https://images.unsplash.com/photo-1560472354-b33ff0c44a43?w=400',
                'https://images.unsplash.com/photo-1526738549149-8e07eca6c147?w=400',
                'https://images.unsplash.com/photo-1550009158-9ebf69173e03?w=400'
            ],
            'صيدليات' => [
                'https://images.unsplash.com/photo-1556844962-6d1b156d9c3d?w=400',
                'https://images.unsplash.com/photo-1585435557343-3b092031edf0?w=400'
            ],
            'سوبر ماركت' => [
                'https://images.unsplash.com/photo-1550989460-0adf9ea622e2?w=400',
                'https://images.unsplash.com/photo-1563636619-e9143da7973b?w=400'
            ]
        ];
        
        $categoryImages = $imageCategories[$categoryName] ?? $imageCategories['سوبر ماركت'];
        
        // Return 1-3 random images
        $imageCount = $faker->numberBetween(1, min(3, count($categoryImages)));
        return $faker->randomElements($categoryImages, $imageCount);
    }
    
    private function generateSKU($faker)
    {
        return 'PRD-' . strtoupper($faker->lexify('???')) . '-' . $faker->numberBetween(100, 999);
    }
}
