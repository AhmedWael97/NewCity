# Multi-City Settings Implementation Guide

## Overview
The system now supports **city-specific settings**, allowing each city to function as an independent site with its own branding, contact information, SEO settings, and design.

## Features Implemented

### 1. **City-Specific Fields**
Each city can now have:

#### Contact Information
- Phone number
- Email address
- Physical address
- WhatsApp number

#### SEO Settings
- Meta title (English & Arabic)
- Meta description (English & Arabic)
- Meta keywords (English & Arabic)
- Open Graph image

#### Branding
- Custom logo
- Custom favicon
- Primary color
- Secondary color
- Accent color

#### Social Media Links
- Facebook URL
- Twitter URL
- Instagram URL
- YouTube URL

#### Analytics
- Google Analytics ID
- Facebook Pixel ID

### 2. **Database Changes**
Migration added: `2025_12_06_171448_add_city_specific_settings_to_cities_table.php`

New columns in `cities` table:
- `contact_phone`, `contact_email`, `contact_address`, `contact_whatsapp`
- `meta_title`, `meta_title_ar`, `meta_description`, `meta_description_ar`
- `meta_keywords`, `meta_keywords_ar`
- `logo`, `favicon`, `og_image`
- `facebook_url`, `twitter_url`, `instagram_url`, `youtube_url`
- `google_analytics_id`, `facebook_pixel_id`

Note: Color fields (`primary_color`, `secondary_color`, `accent_color`) already existed from previous migrations.

### 3. **Admin Interface**
Update cities through: `/admin/cities/{id}/edit`

New sections in the city edit form:
1. **Contact Information Tab** - Manage contact details
2. **SEO Settings Tab** - Configure meta tags and keywords
3. **Branding Tab** - Upload logo, favicon, OG image
4. **Social Media Tab** - Add social media profile links
5. **Analytics Tab** - Configure Google Analytics and Facebook Pixel

### 4. **Frontend Integration**

#### Automatic City Detection
The `CitySettingsServiceProvider` automatically:
1. Detects current city from session
2. Falls back to request parameter `?city=slug`
3. Uses default first active city if none selected
4. Caches city data for performance (1 hour)

#### Layout Changes
All layouts now use city-specific settings:

**Header (Meta Tags):**
```blade
<title>{{ $citySettings['meta_title_ar'] }}</title>
<meta name="description" content="{{ $citySettings['meta_description_ar'] }}">
<meta name="keywords" content="{{ $citySettings['meta_keywords_ar'] }}">
<link rel="icon" href="{{ $citySettings['favicon'] }}">
```

**Navbar:**
- Shows city-specific logo if available
- Falls back to default SENÚ logo

**Footer:**
- Displays city name and description
- Shows social media icons (if configured)
- Shows contact information (email, phone, WhatsApp)

**Custom Colors:**
```css
:root {
    --city-primary-color: #3B82F6;
    --city-secondary-color: #10B981;
    --city-accent-color: #F59E0B;
}
```

**Analytics:**
- Google Analytics script loads automatically if ID is set
- Facebook Pixel script loads automatically if ID is set

## Usage

### For Administrators

1. **Configure City Settings:**
   - Go to `/admin/cities`
   - Click "Edit" on any city
   - Fill in the new tabs with city-specific information
   - Upload logo (PNG, JPG, SVG - max 2MB)
   - Upload favicon (PNG, ICO - max 1MB)
   - Upload OG image (PNG, JPG - max 2MB, recommended: 1200x630px)
   - Save changes

2. **Set Colors:**
   - Use color pickers for primary, secondary, and accent colors
   - These colors will automatically apply to buttons, links, and UI elements

3. **Configure SEO:**
   - Add unique meta titles and descriptions for each city
   - Use relevant keywords for better search engine visibility
   - Upload Open Graph image for social media sharing

4. **Add Analytics:**
   - Get Google Analytics ID from Google Analytics console
   - Get Facebook Pixel ID from Facebook Business Manager
   - Paste IDs in respective fields

### For Developers

#### Accessing City Settings in Views
City settings are automatically available in all views via `$citySettings`:

```blade
{{ $citySettings['name_ar'] }}
{{ $citySettings['logo'] }}
{{ $citySettings['contact_phone'] }}
{{ $citySettings['primary_color'] }}
```

#### Available Settings Array
```php
[
    'name' => 'City Name (EN)',
    'name_ar' => 'اسم المدينة',
    'logo' => 'full-url-to-logo',
    'favicon' => 'full-url-to-favicon',
    'primary_color' => '#3B82F6',
    'secondary_color' => '#10B981',
    'accent_color' => '#F59E0B',
    'contact_phone' => '+20...',
    'contact_email' => 'email@example.com',
    'contact_address' => 'Full address',
    'contact_whatsapp' => '+20...',
    'facebook_url' => 'https://...',
    'twitter_url' => 'https://...',
    'instagram_url' => 'https://...',
    'youtube_url' => 'https://...',
    'meta_title' => 'Page Title',
    'meta_title_ar' => 'عنوان الصفحة',
    'meta_description' => 'Description',
    'meta_description_ar' => 'الوصف',
    'meta_keywords' => 'keywords',
    'meta_keywords_ar' => 'كلمات مفتاحية',
    'og_image' => 'full-url-to-og-image',
    'google_analytics_id' => 'G-XXXXXXXXX',
    'facebook_pixel_id' => '1234567890',
]
```

#### Accessing Current City Object
```blade
{{ $currentCity->id }}
{{ $currentCity->name }}
{{ $currentCity->slug }}
```

#### Programmatically Change City
```php
// In controller
session(['current_city_id' => $cityId]);

// Or redirect with city parameter
return redirect()->route('home', ['city' => $citySlug]);
```

## Benefits

1. **Multi-Tenant System**: Each city functions as its own branded site
2. **SEO Optimization**: Unique meta tags per city improve search rankings
3. **Local Branding**: Cities can have their own logos and colors
4. **Better Analytics**: Track each city separately with dedicated analytics
5. **Social Media Integration**: Direct links to city-specific social profiles
6. **Contact Management**: Display relevant local contact information
7. **Performance**: Settings are cached for 1 hour, reducing database queries

## File Changes Summary

### Modified Files:
- `database/migrations/2025_12_06_171448_add_city_specific_settings_to_cities_table.php` (NEW)
- `app/Models/City.php` (Added fillable fields + accessor methods)
- `app/Http/Controllers/Admin/AdminCityController.php` (Added validation + file upload handling)
- `app/Providers/CitySettingsServiceProvider.php` (Shares city settings with all views)
- `resources/views/layouts/app.blade.php` (Dynamic meta tags, favicon, colors, analytics)
- `resources/views/partials/navbar.blade.php` (Dynamic logo)
- `resources/views/partials/footer.blade.php` (Dynamic contact info + social links)
- `resources/views/admin/cities/edit.blade.php` (Added new form sections)
- `resources/views/admin/cities/create.blade.php` (Added new form sections)

## Notes

- All images are stored in `storage/app/public/cities/logos|favicons|og-images/`
- Old images are automatically deleted when new ones are uploaded
- Settings are cached for 1 hour per city for performance
- Falls back to default values if city settings are empty
- Color customization uses CSS variables for easy overriding
- Analytics scripts only load when IDs are configured
