# Google Maps Scraper - Usage Guide

## 📋 Overview
This powerful Google Maps scraper extracts comprehensive shop data including:
- Shop name, category, address
- Phone number, website, email
- Coordinates (latitude, longitude)
- Rating and review count
- Opening hours
- Images
- Google Place ID
- And more...

## 🚀 Installation

### 1. Install Python Requirements
```bash
pip install -r scraper_requirements.txt
```

### 2. Required Packages
- **selenium**: Web automation
- **webdriver-manager**: Automatic ChromeDriver management
- **pandas**: Data processing
- **openpyxl**: Excel file support

## 💻 Usage

### Basic Usage
```python
from google_maps_scraper import GoogleMapsScraper

# Initialize scraper
scraper = GoogleMapsScraper(headless=False)

# Scrape shops
shops = scraper.scrape(
    query="restaurants",      # What to search for
    location="Cairo, Egypt",  # Where to search
    max_results=50            # How many results
)

# Save results
scraper.save_to_csv("shops.csv")
scraper.save_to_excel("shops.xlsx")
scraper.save_to_json("shops.json")

# Close browser
scraper.close()
```

### Run the Script Directly
```bash
python google_maps_scraper.py
```

Edit the configuration in the `main()` function:
```python
SEARCH_QUERY = "restaurants"   # Change to your category
LOCATION = "Cairo, Egypt"      # Change to your city
MAX_RESULTS = 50               # Change max results
HEADLESS = False               # True = no browser window
```

### Batch Scraping Multiple Locations
```bash
python batch_scraper.py
```

Edit `batch_scraper.py` to add your tasks:
```python
tasks = [
    {'query': 'restaurants', 'location': 'Cairo, Egypt', 'max_results': 50},
    {'query': 'cafes', 'location': 'Alexandria, Egypt', 'max_results': 30},
    {'query': 'pharmacies', 'location': 'Giza, Egypt', 'max_results': 40},
]
```

## 📊 Output Formats

### CSV Format
```csv
name,category,address,phone,website,rating,review_count,latitude,longitude,...
```

### Excel Format
Formatted spreadsheet with all columns

### JSON Format
```json
[
  {
    "name": "Shop Name",
    "category": "Restaurant",
    "address": "123 Street, City",
    "phone": "+20 123 456 789",
    "website": "https://example.com",
    "rating": 4.5,
    "review_count": 150,
    "latitude": 30.0444,
    "longitude": 31.2357,
    "opening_hours": {
      "monday": "9:00 AM - 10:00 PM",
      "tuesday": "9:00 AM - 10:00 PM"
    },
    "images": [
      "https://example.com/image1.jpg"
    ],
    "google_place_id": "0x14583:0x1234",
    "google_maps_url": "https://maps.google.com/..."
  }
]
```

## 🗄️ Database Import

### Extracted Fields Matching Shop Table:
- ✅ `name` - Shop name
- ✅ `category` - Business category
- ✅ `address` - Full address
- ✅ `latitude` - Coordinate
- ✅ `longitude` - Coordinate
- ✅ `phone` - Phone number
- ✅ `website` - Website URL
- ✅ `rating` - Average rating (0-5)
- ✅ `review_count` - Number of reviews
- ✅ `images` - Array of image URLs
- ✅ `opening_hours` - JSON object
- ✅ `google_place_id` - Unique Google identifier
- ✅ `google_types` - Array of business types

### Manual Fields (to add manually):
- `user_id` - Shop owner (set default admin user)
- `city_id` - Match from your cities table
- `category_id` - Match from your categories table
- `slug` - Generate from name
- `description` - Optional description
- `email` - Usually not public on Google Maps
- `is_featured` - Set to false
- `is_verified` - Set to false
- `is_active` - Set to true

## 🔧 Search Query Examples

```python
# Food & Dining
"restaurants"
"cafes"
"bakeries"
"fast food"
"pizza places"
"seafood restaurants"

# Retail
"clothing stores"
"electronics shops"
"bookstores"
"jewelry stores"
"supermarkets"
"malls"

# Services
"pharmacies"
"hospitals"
"banks"
"hotels"
"gyms"
"salons"

# Entertainment
"cinemas"
"theaters"
"parks"
"museums"

# Combined searches
"italian restaurants in Cairo"
"pharmacies near downtown"
```

## ⚙️ Configuration Options

### Headless Mode
```python
scraper = GoogleMapsScraper(headless=True)  # No browser window
```

### Custom Wait Time
```python
# Modify in the scraper class
self.wait = WebDriverWait(self.driver, 15)  # Wait up to 15 seconds
```

### Image Limit
```python
# In _extract_images() method
return image_urls[:5]  # Change 5 to desired number
```

## 🛡️ Best Practices

1. **Respect Rate Limits**: Add delays between requests
2. **Use Headless Mode**: For production scraping
3. **Handle Errors**: Wrap in try-except blocks
4. **Save Frequently**: Save data periodically
5. **Verify Data**: Check extracted data quality
6. **Legal Compliance**: Ensure scraping complies with Google's ToS

## 🐛 Troubleshooting

### Chrome Driver Issues
```bash
# Manual installation
pip install --upgrade webdriver-manager
```

### Selenium Errors
- Update Chrome browser to latest version
- Clear browser cache
- Run with `headless=False` to see what's happening

### No Data Extracted
- Check if search query returns results
- Verify location is correct
- Try with `headless=False` to debug
- Increase wait times for slow connections

### Popup/Consent Issues
- Script handles common popups automatically
- Add custom selectors in `_close_popups()` if needed

## 📝 Example Workflow

### 1. Scrape Data
```bash
python google_maps_scraper.py
```

### 2. Review Output
Check generated CSV/Excel files

### 3. Prepare for Database Import
Create SQL import script or use Laravel seeder:

```php
// database/seeders/GoogleMapsShopsSeeder.php
$jsonData = file_get_contents('shops_data.json');
$shops = json_decode($jsonData, true);

foreach ($shops as $shop) {
    Shop::create([
        'user_id' => 1, // Admin user
        'city_id' => $this->findCityId($shop['address']),
        'category_id' => $this->findCategoryId($shop['category']),
        'name' => $shop['name'],
        'slug' => Str::slug($shop['name']),
        'address' => $shop['address'],
        'latitude' => $shop['latitude'],
        'longitude' => $shop['longitude'],
        'phone' => $shop['phone'],
        'website' => $shop['website'],
        'rating' => $shop['rating'] ?? 0,
        'review_count' => $shop['review_count'] ?? 0,
        'images' => json_encode($shop['images']),
        'opening_hours' => json_encode($shop['opening_hours']),
        'google_place_id' => $shop['google_place_id'],
        'google_types' => json_encode($shop['google_types']),
        'is_active' => true,
    ]);
}
```

### 4. Run Seeder
```bash
php artisan db:seed --class=GoogleMapsShopsSeeder
```

## 🎯 Pro Tips

1. **Start Small**: Test with 10-20 results first
2. **Multiple Sessions**: Split large scrapes into batches
3. **Rotate IPs**: Use proxies for large-scale scraping
4. **Data Validation**: Always validate extracted data
5. **Backup Data**: Keep raw JSON backups
6. **Update Regularly**: Re-scrape to keep data fresh

## 📧 Support

For issues or questions:
1. Check the troubleshooting section
2. Review error messages carefully
3. Test with `headless=False` to debug visually
4. Verify Chrome and ChromeDriver versions match

## ⚖️ Legal Notice

This scraper is for educational purposes. Ensure compliance with:
- Google Maps Terms of Service
- Local data protection laws
- robots.txt guidelines
- Rate limiting best practices

Use responsibly and ethically! 🙏
