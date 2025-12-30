"""
Google Maps Scraper for Shop Data Extraction
============================================
This script scrapes shop data from Google Maps based on location and search query.
It extracts comprehensive information to populate the shops table in the database.

Requirements:
    pip install selenium webdriver-manager pandas openpyxl python-dotenv

Usage:
    python google_maps_scraper.py
"""

import time
import json
import csv
import re
from datetime import datetime
from typing import List, Dict, Optional
from selenium import webdriver
from selenium.webdriver.common.by import By
from selenium.webdriver.support.ui import WebDriverWait
from selenium.webdriver.support import expected_conditions as EC
from selenium.webdriver.chrome.service import Service
from selenium.webdriver.chrome.options import Options
from selenium.common.exceptions import TimeoutException, NoSuchElementException
from webdriver_manager.chrome import ChromeDriverManager
import pandas as pd


class GoogleMapsScraper:
    """Scraper class for extracting shop data from Google Maps"""
    
    def __init__(self, headless: bool = False):
        """
        Initialize the scraper with Chrome webdriver
        
        Args:
            headless: Run browser in headless mode (without GUI)
        """
        self.driver = self._setup_driver(headless)
        self.wait = WebDriverWait(self.driver, 10)
        self.shops_data = []
        
    def _setup_driver(self, headless: bool) -> webdriver.Chrome:
        """Setup Chrome driver with appropriate options"""
        chrome_options = Options()
        
        if headless:
            chrome_options.add_argument('--headless')
        
        chrome_options.add_argument('--no-sandbox')
        chrome_options.add_argument('--disable-dev-shm-usage')
        chrome_options.add_argument('--disable-blink-features=AutomationControlled')
        chrome_options.add_argument('--user-agent=Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36')
        chrome_options.add_experimental_option('excludeSwitches', ['enable-automation'])
        chrome_options.add_experimental_option('useAutomationExtension', False)
        
        service = Service(ChromeDriverManager().install())
        driver = webdriver.Chrome(service=service, options=chrome_options)
        driver.maximize_window()
        
        return driver
    
    def search_location(self, query: str, location: str = None, latitude: float = None, 
                       longitude: float = None, radius_km: float = None):
        """
        Search for businesses in a specific location
        
        Args:
            query: Search term (e.g., "restaurants", "pharmacies", "shops")
            location: Location to search (e.g., "Cairo, Egypt", "Alexandria")
            latitude: Latitude coordinate for center point
            longitude: Longitude coordinate for center point
            radius_km: Search radius in kilometers (optional, Google Maps decides default)
        """
        # Build search URL based on provided parameters
        if latitude is not None and longitude is not None:
            # Use coordinates-based search
            zoom = self._calculate_zoom_from_radius(radius_km) if radius_km else 13
            search_url = f"https://www.google.com/maps/search/{query.replace(' ', '+')}/@{latitude},{longitude},{zoom}z"
            print(f"🔍 Searching: {query} near ({latitude}, {longitude})")
            if radius_km:
                print(f"📏 Search radius: ~{radius_km} km")
        else:
            # Use location name-based search
            search_url = f"https://www.google.com/maps/search/{query}"
            if location:
                search_url += f"+in+{location.replace(' ', '+')}"
            print(f"🔍 Searching: {query} in {location}")
        
        self.driver.get(search_url)
        time.sleep(3)
        
        # Close any popups
        self._close_popups()
        
        # Scroll to load all results
        self._scroll_results()
    
    def _calculate_zoom_from_radius(self, radius_km: float) -> int:
        """
        Calculate appropriate zoom level based on radius
        
        Args:
            radius_km: Radius in kilometers
            
        Returns:
            Zoom level (higher = closer view)
        """
        # Approximate zoom levels for different radii
        # These are rough estimates
        if radius_km <= 1:
            return 15  # ~1 km
        elif radius_km <= 2:
            return 14  # ~2 km
        elif radius_km <= 5:
            return 13  # ~5 km
        elif radius_km <= 10:
            return 12  # ~10 km
        elif radius_km <= 20:
            return 11  # ~20 km
        elif radius_km <= 50:
            return 10  # ~50 km
        else:
            return 9   # ~100+ km
    
    def _close_popups(self):
        """Close any popups or consent dialogs"""
        try:
            # Try to close cookie consent
            consent_buttons = [
                "//button[contains(text(), 'Accept')]",
                "//button[contains(text(), 'Reject')]",
                "//button[contains(text(), 'قبول')]",
                "//button[contains(text(), 'رفض')]"
            ]
            
            for xpath in consent_buttons:
                try:
                    button = self.driver.find_element(By.XPATH, xpath)
                    button.click()
                    time.sleep(1)
                    break
                except:
                    continue
        except:
            pass
    
    def _scroll_results(self):
        """Scroll through results panel to load all businesses"""
        print("📜 Loading all results...")
        
        try:
            # Find the scrollable results panel
            scrollable_div = self.driver.find_element(By.CSS_SELECTOR, "div[role='feed']")
            
            last_height = self.driver.execute_script("return arguments[0].scrollHeight", scrollable_div)
            
            while True:
                # Scroll down
                self.driver.execute_script("arguments[0].scrollTop = arguments[0].scrollHeight", scrollable_div)
                time.sleep(2)
                
                # Calculate new scroll height
                new_height = self.driver.execute_script("return arguments[0].scrollHeight", scrollable_div)
                
                # Check if we've reached the end
                if new_height == last_height:
                    break
                    
                last_height = new_height
            
            print("✅ All results loaded")
        except Exception as e:
            print(f"⚠️ Scroll warning: {str(e)}")
    
    def extract_shop_links(self) -> List[str]:
        """Extract all shop links from the search results"""
        print("🔗 Extracting shop links...")
        
        links = []
        try:
            # Find all result items
            results = self.driver.find_elements(By.CSS_SELECTOR, "a[href*='/maps/place/']")
            
            for result in results:
                href = result.get_attribute('href')
                if href and '/maps/place/' in href and href not in links:
                    links.append(href)
            
            print(f"✅ Found {len(links)} shops")
        except Exception as e:
            print(f"❌ Error extracting links: {str(e)}")
        
        return links
    
    def extract_shop_details(self, url: str) -> Optional[Dict]:
        """
        Extract detailed information from a shop page
        
        Args:
            url: Google Maps URL of the shop
            
        Returns:
            Dictionary containing shop details
        """
        try:
            self.driver.get(url)
            time.sleep(3)
            
            shop_data = {
                'google_maps_url': url,
                'google_place_id': self._extract_place_id(url),
                'name': self._extract_name(),
                'category': self._extract_category(),
                'address': self._extract_address(),
                'phone': self._extract_phone(),
                'website': self._extract_website(),
                'rating': self._extract_rating(),
                'review_count': self._extract_review_count(),
                'latitude': None,
                'longitude': None,
                'opening_hours': self._extract_opening_hours(),
                'images': self._extract_images(),
                'description': self._extract_description(),
                'google_types': self._extract_types(),
                'scraped_at': datetime.now().isoformat()
            }
            
            # Extract coordinates from URL
            coords = self._extract_coordinates(url)
            if coords:
                shop_data['latitude'] = coords['lat']
                shop_data['longitude'] = coords['lng']
            
            print(f"✅ Extracted: {shop_data['name']}")
            return shop_data
            
        except Exception as e:
            print(f"❌ Error extracting shop details: {str(e)}")
            return None
    
    def _extract_place_id(self, url: str) -> Optional[str]:
        """Extract Google Place ID from URL"""
        try:
            match = re.search(r'!1s(0x[0-9a-f]+:[0-9a-fx]+)', url)
            if match:
                return match.group(1)
        except:
            pass
        return None
    
    def _extract_name(self) -> Optional[str]:
        """Extract shop name"""
        try:
            name = self.driver.find_element(By.CSS_SELECTOR, "h1.DUwDvf").text
            return name.strip() if name else None
        except:
            return None
    
    def _extract_category(self) -> Optional[str]:
        """Extract shop category/type"""
        try:
            category = self.driver.find_element(By.CSS_SELECTOR, "button[jsaction*='category']").text
            return category.strip() if category else None
        except:
            return None
    
    def _extract_address(self) -> Optional[str]:
        """Extract shop address"""
        try:
            # Try multiple selectors
            selectors = [
                "button[data-item-id='address']",
                "button[aria-label*='Address']",
                "button[data-tooltip*='Copy address']"
            ]
            
            for selector in selectors:
                try:
                    address_elem = self.driver.find_element(By.CSS_SELECTOR, selector)
                    address = address_elem.get_attribute('aria-label')
                    if address:
                        # Clean the address
                        address = address.replace('Address: ', '').replace('العنوان: ', '')
                        return address.strip()
                except:
                    continue
            
            # Alternative method
            address = self.driver.find_element(By.CSS_SELECTOR, "div[data-item-id='address'] div.fontBodyMedium").text
            return address.strip() if address else None
        except:
            return None
    
    def _extract_phone(self) -> Optional[str]:
        """Extract phone number"""
        try:
            selectors = [
                "button[data-item-id*='phone']",
                "button[aria-label*='Phone']",
                "button[data-tooltip*='Copy phone number']"
            ]
            
            for selector in selectors:
                try:
                    phone_elem = self.driver.find_element(By.CSS_SELECTOR, selector)
                    phone = phone_elem.get_attribute('aria-label')
                    if phone:
                        # Extract just the number
                        phone = re.sub(r'[^0-9+\-() ]', '', phone)
                        return phone.strip()
                except:
                    continue
        except:
            pass
        return None
    
    def _extract_website(self) -> Optional[str]:
        """Extract website URL"""
        try:
            website_elem = self.driver.find_element(By.CSS_SELECTOR, "a[data-item-id='authority']")
            website = website_elem.get_attribute('href')
            return website.strip() if website else None
        except:
            return None
    
    def _extract_rating(self) -> Optional[float]:
        """Extract rating score"""
        try:
            rating_elem = self.driver.find_element(By.CSS_SELECTOR, "div.F7nice span[aria-hidden='true']")
            rating_text = rating_elem.text.strip()
            rating = float(rating_text.replace(',', '.'))
            return round(rating, 2)
        except:
            return None
    
    def _extract_review_count(self) -> int:
        """Extract number of reviews"""
        try:
            review_elem = self.driver.find_element(By.CSS_SELECTOR, "div.F7nice span[aria-label*='reviews']")
            review_text = review_elem.text.strip()
            # Extract number from text like "(1,234)"
            review_count = re.sub(r'[^0-9]', '', review_text)
            return int(review_count) if review_count else 0
        except:
            return 0
    
    def _extract_opening_hours(self) -> Optional[Dict]:
        """Extract opening hours"""
        try:
            # Click to expand hours if needed
            try:
                hours_button = self.driver.find_element(By.CSS_SELECTOR, "button[data-item-id='oh']")
                hours_button.click()
                time.sleep(1)
            except:
                pass
            
            hours_dict = {}
            hours_elements = self.driver.find_elements(By.CSS_SELECTOR, "table[aria-label*='hours'] tr")
            
            for row in hours_elements:
                try:
                    day = row.find_element(By.CSS_SELECTOR, "td:first-child").text.strip()
                    hours = row.find_element(By.CSS_SELECTOR, "td:last-child").text.strip()
                    
                    # Convert Arabic days to English
                    day_mapping = {
                        'الأحد': 'sunday',
                        'الاثنين': 'monday',
                        'الثلاثاء': 'tuesday',
                        'الأربعاء': 'wednesday',
                        'الخميس': 'thursday',
                        'الجمعة': 'friday',
                        'السبت': 'saturday',
                        'Sunday': 'sunday',
                        'Monday': 'monday',
                        'Tuesday': 'tuesday',
                        'Wednesday': 'wednesday',
                        'Thursday': 'thursday',
                        'Friday': 'friday',
                        'Saturday': 'saturday'
                    }
                    
                    day_key = day_mapping.get(day, day.lower())
                    hours_dict[day_key] = hours
                except:
                    continue
            
            return hours_dict if hours_dict else None
        except:
            return None
    
    def _extract_images(self) -> List[str]:
        """Extract shop images URLs"""
        try:
            # Click on photos to see them
            try:
                photos_button = self.driver.find_element(By.CSS_SELECTOR, "button[aria-label*='Photo']")
                photos_button.click()
                time.sleep(2)
            except:
                pass
            
            image_urls = []
            images = self.driver.find_elements(By.CSS_SELECTOR, "img[src*='googleusercontent']")
            
            for img in images[:10]:  # Limit to first 10 images
                src = img.get_attribute('src')
                if src and 'googleusercontent' in src:
                    # Get high quality version
                    src = src.split('=')[0] + '=w1000'
                    if src not in image_urls:
                        image_urls.append(src)
            
            # Go back
            try:
                back_button = self.driver.find_element(By.CSS_SELECTOR, "button[aria-label*='Back']")
                back_button.click()
                time.sleep(1)
            except:
                pass
            
            return image_urls[:5]  # Return max 5 images
        except:
            return []
    
    def _extract_description(self) -> Optional[str]:
        """Extract shop description/about"""
        try:
            # Try to find description in various places
            selectors = [
                "div[data-section-id='description'] div.fontBodyMedium",
                "div.WeS02d div.fontBodyMedium"
            ]
            
            for selector in selectors:
                try:
                    desc_elem = self.driver.find_element(By.CSS_SELECTOR, selector)
                    description = desc_elem.text.strip()
                    if description and len(description) > 10:
                        return description
                except:
                    continue
        except:
            pass
        return None
    
    def _extract_types(self) -> List[str]:
        """Extract business types/categories"""
        try:
            types = []
            # Try to find all type elements
            type_elements = self.driver.find_elements(By.CSS_SELECTOR, "button[jsaction*='category']")
            
            for elem in type_elements:
                type_text = elem.text.strip()
                if type_text and type_text not in types:
                    types.append(type_text)
            
            return types
        except:
            return []
    
    def _extract_coordinates(self, url: str) -> Optional[Dict]:
        """Extract latitude and longitude from URL"""
        try:
            # Pattern: @lat,lng,zoom
            match = re.search(r'@(-?\d+\.\d+),(-?\d+\.\d+)', url)
            if match:
                return {
                    'lat': float(match.group(1)),
                    'lng': float(match.group(2))
                }
        except:
            pass
        return None
    
    def scrape(self, query: str, location: str = None, max_results: int = 50,
               latitude: float = None, longitude: float = None, radius_km: float = None) -> List[Dict]:
        """
        Main scraping method
        
        Args:
            query: Search term (e.g., "restaurants", "shops")
            location: Location to search (e.g., "Cairo", "Alexandria")
            max_results: Maximum number of results to scrape
            latitude: Latitude coordinate for center point (alternative to location)
            longitude: Longitude coordinate for center point (alternative to location)
            radius_km: Search radius in kilometers (optional)
            
        Returns:
            List of dictionaries containing shop data
        """
        print(f"\n{'='*60}")
        print(f"🚀 Starting Google Maps Scraper")
        print(f"{'='*60}\n")
        
        # Search for locations
        self.search_location(query, location, latitude, longitude, radius_km)
        
        # Extract shop links
        shop_links = self.extract_shop_links()
        
        if not shop_links:
            print("❌ No shops found")
            return []
        
        # Limit results
        shop_links = shop_links[:max_results]
        
        print(f"\n📊 Processing {len(shop_links)} shops...\n")
        
        # Extract details for each shop
        for i, link in enumerate(shop_links, 1):
            print(f"[{i}/{len(shop_links)}] Processing...")
            
            shop_data = self.extract_shop_details(link)
            if shop_data:
                # Add search parameters to shop data
                if latitude is not None and longitude is not None:
                    shop_data['search_center_lat'] = latitude
                    shop_data['search_center_lng'] = longitude
                    shop_data['search_radius_km'] = radius_km
                    
                    # Calculate distance from center point
                    if shop_data.get('latitude') and shop_data.get('longitude'):
                        distance = self._calculate_distance(
                            latitude, longitude,
                            shop_data['latitude'], shop_data['longitude']
                        )
                        shop_data['distance_from_center_km'] = round(distance, 2)
                
                self.shops_data.append(shop_data)
            
            # Small delay to avoid detection
            time.sleep(1)
        
        print(f"\n{'='*60}")
        print(f"✅ Scraping completed! Found {len(self.shops_data)} shops")
        print(f"{'='*60}\n")
        
        return self.shops_data
    
    def _calculate_distance(self, lat1: float, lon1: float, lat2: float, lon2: float) -> float:
        """
        Calculate distance between two coordinates using Haversine formula
        
        Args:
            lat1: Latitude of first point
            lon1: Longitude of first point
            lat2: Latitude of second point
            lon2: Longitude of second point
            
        Returns:
            Distance in kilometers
        """
        from math import radians, cos, sin, asin, sqrt
        
        # Convert to radians
        lon1, lat1, lon2, lat2 = map(radians, [lon1, lat1, lon2, lat2])
        
        # Haversine formula
        dlon = lon2 - lon1
        dlat = lat2 - lat1
        a = sin(dlat/2)**2 + cos(lat1) * cos(lat2) * sin(dlon/2)**2
        c = 2 * asin(sqrt(a))
        
        # Radius of earth in kilometers
        r = 6371
        
        return c * r
    
    def save_to_csv(self, filename: str = None):
        """Save scraped data to CSV file"""
        if not self.shops_data:
            print("❌ No data to save")
            return
        
        if not filename:
            timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
            filename = f"google_maps_shops_{timestamp}.csv"
        
        # Convert to DataFrame
        df = pd.DataFrame(self.shops_data)
        
        # Save to CSV
        df.to_csv(filename, index=False, encoding='utf-8-sig')
        print(f"💾 Data saved to: {filename}")
    
    def save_to_excel(self, filename: str = None):
        """Save scraped data to Excel file"""
        if not self.shops_data:
            print("❌ No data to save")
            return
        
        if not filename:
            timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
            filename = f"google_maps_shops_{timestamp}.xlsx"
        
        # Convert to DataFrame
        df = pd.DataFrame(self.shops_data)
        
        # Save to Excel
        df.to_excel(filename, index=False, engine='openpyxl')
        print(f"💾 Data saved to: {filename}")
    
    def save_to_json(self, filename: str = None):
        """Save scraped data to JSON file"""
        if not self.shops_data:
            print("❌ No data to save")
            return
        
        if not filename:
            timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
            filename = f"google_maps_shops_{timestamp}.json"
        
        with open(filename, 'w', encoding='utf-8') as f:
            json.dump(self.shops_data, f, ensure_ascii=False, indent=2)
        
        print(f"💾 Data saved to: {filename}")
    
    def close(self):
        """Close the browser"""
        self.driver.quit()
        print("🔒 Browser closed")


def main():
    """Main execution function"""
    
    # ========== CONFIGURATION ==========
    # Choose one of two search methods:
    
    # METHOD 1: Search by location name
    SEARCH_QUERY = "restaurants"           # What to search for
    LOCATION = "Cairo, Egypt"              # Where to search
    
    # METHOD 2: Search by coordinates and radius
    USE_COORDINATES = True                 # Set to True for coordinate search
    LATITUDE = 30.1573387                  # Center point latitude
    LONGITUDE = 31.8369255                 # Center point longitude
    RADIUS_KM = 2                          # Search radius in kilometers
    
    # Common settings
    MAX_RESULTS = 500                      # Maximum number of shops to scrape
    HEADLESS = False                       # Set to True to run without GUI
    
    # ===================================
    
    # Examples for Egyptian cities coordinates:
    # Cairo Downtown: 30.0444, 31.2357
    # Nasr City: 30.0626, 31.3549
    # Maadi: 29.9602, 31.2569
    # Alexandria: 31.2001, 29.9187
    # Giza Pyramids: 29.9773, 31.1325
    # New Cairo: 30.0290, 31.4885
    # 6th October: 29.9602, 31.0117
    # Sharm El Sheikh: 27.9158, 34.3300
    
    # Available search queries examples:
    # - "restaurants"
    # - "cafes"
    # - "pharmacies"
    # - "supermarkets"
    # - "clothing stores"
    # - "electronics shops"
    # - "jewelry stores"
    # - "bookstores"
    # - "banks"
    # - "hotels"
    
    print("""
    ╔══════════════════════════════════════════════════════════╗
    ║         Google Maps Scraper for Shop Data               ║
    ║                                                          ║
    ║  Extracts comprehensive shop information including:      ║
    ║  • Name, Category, Address                               ║
    ║  • Phone, Website, Coordinates                           ║
    ║  • Rating, Reviews, Opening Hours                        ║
    ║  • Images, Description, Google Place ID                  ║
    ║  • Distance from center (if using coordinates)           ║
    ╚══════════════════════════════════════════════════════════╝
    """)
    
    # Initialize scraper
    scraper = GoogleMapsScraper(headless=HEADLESS)
    
    try:
        # Scrape data based on search method
        if USE_COORDINATES:
            # Coordinate-based search
            shops = scraper.scrape(
                query=SEARCH_QUERY,
                latitude=LATITUDE,
                longitude=LONGITUDE,
                radius_km=RADIUS_KM,
                max_results=MAX_RESULTS
            )
        else:
            # Location name-based search
            shops = scraper.scrape(
                query=SEARCH_QUERY,
                location=LOCATION,
            max_results=MAX_RESULTS
        )
        
        if shops:
            # Save to different formats
            timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
            
            print("\n💾 Saving data...")
            scraper.save_to_csv(f"shops_data_{timestamp}.csv")
            scraper.save_to_excel(f"shops_data_{timestamp}.xlsx")
            scraper.save_to_json(f"shops_data_{timestamp}.json")
            
            print(f"\n📊 Summary:")
            print(f"   • Total shops scraped: {len(shops)}")
            print(f"   • Shops with phone: {sum(1 for s in shops if s.get('phone'))}")
            print(f"   • Shops with website: {sum(1 for s in shops if s.get('website'))}")
            
            shops_with_rating = [s for s in shops if s.get('rating')]
            print(f"   • Shops with rating: {len(shops_with_rating)}")
            if shops_with_rating:
                avg_rating = sum(s.get('rating') for s in shops_with_rating) / len(shops_with_rating)
                print(f"   • Average rating: {avg_rating:.2f}")
            
            # Show distance info if coordinate search was used
            if shops and shops[0].get('distance_from_center_km') is not None:
                shops_with_distance = [s for s in shops if s.get('distance_from_center_km') is not None]
                if shops_with_distance:
                    distances = [s['distance_from_center_km'] for s in shops_with_distance]
                    print(f"   • Closest shop: {min(distances):.2f} km")
                    print(f"   • Farthest shop: {max(distances):.2f} km")
                    print(f"   • Average distance: {sum(distances) / len(distances):.2f} km")
            
    except KeyboardInterrupt:
        print("\n\n⚠️ Scraping interrupted by user")
    except Exception as e:
        print(f"\n❌ Error: {str(e)}")
    finally:
        scraper.close()


if __name__ == "__main__":
    main()
