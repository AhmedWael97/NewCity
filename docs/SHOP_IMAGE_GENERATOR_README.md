# Automatic Shop Image Generation

## Overview
When creating a shop without uploading images, the system automatically generates professional-looking images with the shop name and category using GD library (built into PHP).

## Features
- ✅ Generates beautiful gradient backgrounds with unique colors per shop
- ✅ Displays shop name prominently
- ✅ Shows category name and icon
- ✅ Creates 3 images per shop automatically
- ✅ Professional decorative elements and branding
- ✅ No external API needed (uses PHP GD library)

## How It Works

### 1. Image Generation Service
File: `app/Services/ShopImageGenerator.php`

The service creates 800x600px PNG images with:
- Gradient background (unique color per shop based on name hash)
- Shop name (centered, white text)
- Category name (subtitle)
- Category icon/emoji
- Decorative circles for visual appeal
- SENU Market branding

### 2. Automatic Integration

#### API Controller
File: `app/Http/Controllers/Api/MyShopController.php`

When creating a shop via API (`POST /api/v1/my-shops`):
```php
// If no images provided
if (empty($data['images'])) {
    $category = \App\Models\Category::find($data['category_id']);
    $imageGenerator = new ShopImageGenerator();
    $data['images'] = $imageGenerator->generateMultipleImages(
        $data['name'],
        $category->name ?? 'Shop',
        $category->icon ?? null,
        3
    );
}
```

#### Web Controller
File: `app/Http/Controllers/DashboardController.php`

When creating a shop via web form (`POST /shop-owner/shops`):
```php
// If no images uploaded
if (!$request->hasFile('images')) {
    $category = Category::find($shopData['category_id']);
    $imageGenerator = new ShopImageGenerator();
    $shopData['images'] = $imageGenerator->generateMultipleImages(
        $shopData['name'],
        $category->name ?? 'Shop',
        $category->icon ?? null,
        3
    );
}
```

## Testing

### Test Command
```bash
php artisan test:shop-image
```

This creates a test image for "مطعم الفخامة" (Restaurant).

### Manual Test via API
Create a shop without images:
```bash
POST /api/v1/my-shops
{
    "name": "مطعم البركة",
    "description": "مطعم شعبي متميز",
    "city_id": 4,
    "category_id": 1,
    "address": "شارع الملك فهد، حي النزهة",
    "latitude": "24.7136",
    "longitude": "46.6753",
    "phone": "0501234567"
    // Note: No "images" field
}
```

The system will automatically generate 3 images and store them in:
```
storage/app/public/shops/generated/
```

## Image Specifications

- **Format**: PNG
- **Size**: 800x600 pixels
- **Location**: `storage/app/public/shops/generated/`
- **Naming**: `shop_{slug}_{timestamp}.png`
- **Colors**: 8 predefined gradient schemes (randomly assigned based on shop name)

## Color Schemes
1. Blue to Purple
2. Pink to Red
3. Green to Blue
4. Orange to Red
5. Purple to Pink
6. Cyan to Blue
7. Amber to Red
8. Teal gradient

## Generated Image Structure

```
┌────────────────────────────┐
│   [Gradient Background]    │
│                            │
│     [Decorative Circles]   │
│                            │
│         🍽️ (Icon)         │
│                            │
│      مطعم الفخامة          │
│     (Shop Name - Large)    │
│                            │
│    مطاعم وكافيهات          │
│   (Category - Smaller)     │
│                            │
│                            │
│     SENU Market            │
│      (Branding)            │
└────────────────────────────┘
```

## Benefits

1. **No Manual Work**: Shop owners don't need to upload images immediately
2. **Professional Appearance**: All shops have visuals, even without custom images
3. **Consistent Branding**: Maintains SENU Market brand identity
4. **Fast**: Generated in milliseconds using built-in PHP GD
5. **No Cost**: No external API calls or services needed
6. **Arabic Support**: Works perfectly with Arabic text

## Future Enhancements (Optional)

If you want to use OpenAI DALL-E for even better images:
1. Add `OPENAI_API_KEY` to `.env`
2. Install: `composer require openai-php/client`
3. Update `ShopImageGenerator` to use DALL-E API for realistic shop photos

Example:
```php
$client = OpenAI::client(env('OPENAI_API_KEY'));
$response = $client->images()->create([
    'prompt' => "Professional storefront photo of {$shopName}, {$categoryName} style",
    'n' => 3,
    'size' => '1024x1024',
]);
```

## Files Created
1. `app/Services/ShopImageGenerator.php` - Image generation service
2. `app/Console/Commands/TestShopImageGenerator.php` - Test command
3. Updates to controllers for automatic integration

## Storage Requirements
- Generated images are ~50-100KB each
- 3 images per shop = ~150-300KB per shop
- Stored in `storage/app/public/shops/generated/`
