# 🚀 Quick Start Guide - Google Maps Scraper

## Step-by-Step Instructions

### 1️⃣ Install Requirements
```bash
pip install -r scraper_requirements.txt
```

### 2️⃣ Run Basic Scraper
```bash
python google_maps_scraper.py
```

**Edit Configuration in the script:**
```python
SEARCH_QUERY = "restaurants"   # What to search
LOCATION = "Cairo, Egypt"      # Where to search
MAX_RESULTS = 50               # How many results
```

### 3️⃣ Output Files
After scraping, you'll get:
- `shops_data_TIMESTAMP.csv` - CSV format
- `shops_data_TIMESTAMP.xlsx` - Excel format
- `shops_data_TIMESTAMP.json` - JSON format

### 4️⃣ Import to Database (Optional)
```bash
python import_to_database.py shops_data_TIMESTAMP.json
```

---

## 📋 Common Search Queries

### Food & Dining
```python
SEARCH_QUERY = "restaurants"
SEARCH_QUERY = "cafes"
SEARCH_QUERY = "bakeries"
SEARCH_QUERY = "fast food"
```

### Retail
```python
SEARCH_QUERY = "clothing stores"
SEARCH_QUERY = "supermarkets"
SEARCH_QUERY = "electronics shops"
SEARCH_QUERY = "pharmacies"
```

### Services
```python
SEARCH_QUERY = "banks"
SEARCH_QUERY = "hospitals"
SEARCH_QUERY = "hotels"
SEARCH_QUERY = "gyms"
```

---

## 🎯 Extracted Data Fields

✅ **Shop Information:**
- Name
- Category
- Address
- Description

✅ **Contact Details:**
- Phone number
- Website
- Email (if available)

✅ **Location:**
- Latitude
- Longitude
- Google Maps URL

✅ **Ratings & Reviews:**
- Average rating (0-5)
- Number of reviews

✅ **Additional:**
- Opening hours (JSON)
- Images (URLs)
- Google Place ID
- Business types

---

## 💾 Database Import Process

### Manual Method:
1. Open the CSV/Excel file
2. Review the data
3. Import using Laravel seeder or SQL

### Automatic Method:
```bash
python import_to_database.py shops_data.json
```

The importer will:
- ✅ Match cities automatically
- ✅ Match categories automatically
- ✅ Generate unique slugs
- ✅ Skip duplicates
- ✅ Validate data

---

## 🔧 Troubleshooting

### Chrome Driver Error
```bash
pip install --upgrade webdriver-manager
```

### Slow Scraping
- Reduce `MAX_RESULTS`
- Check internet connection
- Try with `HEADLESS = True`

### No Data Extracted
- Verify search query returns results on Google Maps
- Check if location is correct
- Try with `HEADLESS = False` to see browser

### Import Errors
- Check `.env` database credentials
- Ensure tables exist (run migrations)
- Verify cities and categories exist in database

---

## 📊 Example Workflow

### Scrape Restaurants in Cairo
```python
# Edit google_maps_scraper.py
SEARCH_QUERY = "restaurants"
LOCATION = "Cairo, Egypt"
MAX_RESULTS = 100
```

```bash
# Run scraper
python google_maps_scraper.py

# Wait for completion...
# Output: shops_data_20250101_120000.json

# Import to database
python import_to_database.py shops_data_20250101_120000.json
```

---

## 🎨 Batch Scraping Multiple Categories

Edit `batch_scraper.py`:
```python
tasks = [
    {'query': 'restaurants', 'location': 'Cairo, Egypt', 'max_results': 50},
    {'query': 'cafes', 'location': 'Cairo, Egypt', 'max_results': 30},
    {'query': 'pharmacies', 'location': 'Alexandria, Egypt', 'max_results': 40},
]
```

Run:
```bash
python batch_scraper.py
```

---

## ⚡ Pro Tips

1. **Start Small**: Test with 10 shops first
2. **Review Data**: Check CSV before importing
3. **Match Categories**: Ensure your categories table has matching entries
4. **Add Cities First**: Make sure target cities exist in database
5. **Backup**: Always backup database before bulk import

---

## 📞 Need Help?

Check these files for detailed information:
- `SCRAPER_GUIDE.md` - Complete documentation
- `google_maps_scraper.py` - Main scraper code
- `import_to_database.py` - Database import code

---

## ⚠️ Important Notes

1. **Legal**: Ensure compliance with Google's Terms of Service
2. **Rate Limits**: Don't scrape too aggressively
3. **Data Quality**: Always review scraped data
4. **Updates**: Re-scrape periodically to keep data fresh
5. **Ethics**: Use responsibly

---

## 🎉 Success!

You should now have:
- ✅ Scraped shop data in multiple formats
- ✅ Data imported to database (optional)
- ✅ Ready to display on your website

Happy scraping! 🚀
