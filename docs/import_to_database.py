"""
Database Importer for Google Maps Data
======================================
Imports scraped shop data directly into Laravel database

Requirements:
    pip install pymysql python-dotenv sqlalchemy

Usage:
    python import_to_database.py shops_data.json
"""

import json
import sys
import os
from datetime import datetime
from typing import Dict, List, Optional
import re
from dotenv import load_dotenv
import pymysql
from slugify import slugify


class DatabaseImporter:
    """Import Google Maps data into Laravel database"""
    
    def __init__(self):
        """Initialize database connection from .env file"""
        load_dotenv()
        
        self.connection = pymysql.connect(
            host=os.getenv('DB_HOST', '127.0.0.1'),
            port=int(os.getenv('DB_PORT', 3306)),
            user=os.getenv('DB_USERNAME', 'root'),
            password=os.getenv('DB_PASSWORD', ''),
            database=os.getenv('DB_DATABASE', 'city_db'),
            charset='utf8mb4',
            cursorclass=pymysql.cursors.DictCursor
        )
        
        print("✅ Database connected successfully")
    
    def load_json_data(self, filename: str) -> List[Dict]:
        """Load shop data from JSON file"""
        try:
            with open(filename, 'r', encoding='utf-8') as f:
                data = json.load(f)
            print(f"📄 Loaded {len(data)} shops from {filename}")
            return data
        except FileNotFoundError:
            print(f"❌ File not found: {filename}")
            return []
        except json.JSONDecodeError:
            print(f"❌ Invalid JSON file: {filename}")
            return []
    
    def find_city_id(self, address: str) -> Optional[int]:
        """Find city ID based on address"""
        if not address:
            return None
        
        # Egyptian cities mapping
        city_keywords = {
            'cairo': ['cairo', 'القاهرة', 'nasr city', 'maadi', 'heliopolis', 'المعادي', 'مدينة نصر', 'هليوبوليس'],
            'alexandria': ['alexandria', 'الإسكندرية', 'alex'],
            'giza': ['giza', 'الجيزة', 'dokki', 'mohandessin', 'الدقي', 'المهندسين'],
            'sharm el sheikh': ['sharm', 'شرم الشيخ'],
            'hurghada': ['hurghada', 'الغردقة'],
            'luxor': ['luxor', 'الأقصر'],
            'aswan': ['aswan', 'أسوان'],
            'port said': ['port said', 'بورسعيد'],
            'suez': ['suez', 'السويس'],
            'tanta': ['tanta', 'طنطا'],
            'mansoura': ['mansoura', 'المنصورة'],
            'ismailia': ['ismailia', 'الإسماعيلية'],
        }
        
        address_lower = address.lower()
        
        # Try to match with database cities
        with self.connection.cursor() as cursor:
            cursor.execute("SELECT id, name, slug FROM cities")
            cities = cursor.fetchall()
            
            # First try exact match
            for city in cities:
                if city['slug'] in address_lower or city['name'].lower() in address_lower:
                    return city['id']
            
            # Then try keyword matching
            for city in cities:
                city_slug = city['slug']
                if city_slug in city_keywords:
                    for keyword in city_keywords[city_slug]:
                        if keyword in address_lower:
                            return city['id']
        
        # Default to first city if no match
        with self.connection.cursor() as cursor:
            cursor.execute("SELECT id FROM cities LIMIT 1")
            result = cursor.fetchone()
            return result['id'] if result else None
    
    def find_category_id(self, category: str, google_types: List[str] = None) -> Optional[int]:
        """Find category ID based on shop category"""
        if not category and not google_types:
            return None
        
        # Category mapping
        category_keywords = {
            'restaurants': ['restaurant', 'مطعم', 'food', 'dining', 'eatery'],
            'cafes': ['cafe', 'coffee', 'قهوة', 'كافيه', 'كوفي'],
            'pharmacies': ['pharmacy', 'صيدلية', 'drug store'],
            'supermarkets': ['supermarket', 'grocery', 'سوبر ماركت', 'بقالة', 'store'],
            'clothing': ['clothing', 'fashion', 'ملابس', 'apparel', 'boutique'],
            'electronics': ['electronics', 'إلكترونيات', 'computer', 'mobile', 'موبايل'],
            'jewelry': ['jewelry', 'مجوهرات', 'gold', 'ذهب'],
            'banks': ['bank', 'بنك', 'atm'],
            'hotels': ['hotel', 'فندق', 'lodge', 'resort'],
            'hospitals': ['hospital', 'مستشفى', 'clinic', 'عيادة'],
            'gyms': ['gym', 'fitness', 'نادي', 'رياضة'],
            'salons': ['salon', 'صالون', 'barber', 'حلاق', 'spa'],
            'bakeries': ['bakery', 'مخبز', 'pastry', 'حلويات'],
        }
        
        search_text = (category or '').lower()
        if google_types:
            search_text += ' ' + ' '.join(google_types).lower()
        
        # Try to match with database categories
        with self.connection.cursor() as cursor:
            cursor.execute("SELECT id, name, slug FROM categories")
            categories = cursor.fetchall()
            
            # First try exact match
            for cat in categories:
                if cat['slug'] in search_text or cat['name'].lower() in search_text:
                    return cat['id']
            
            # Then try keyword matching
            for cat in categories:
                cat_slug = cat['slug']
                if cat_slug in category_keywords:
                    for keyword in category_keywords[cat_slug]:
                        if keyword in search_text:
                            return cat['id']
        
        # Default to general category
        with self.connection.cursor() as cursor:
            cursor.execute("SELECT id FROM categories WHERE slug = 'general' OR slug = 'other' LIMIT 1")
            result = cursor.fetchone()
            if result:
                return result['id']
            
            # If no general category, return first one
            cursor.execute("SELECT id FROM categories LIMIT 1")
            result = cursor.fetchone()
            return result['id'] if result else None
    
    def generate_unique_slug(self, name: str) -> str:
        """Generate unique slug for shop name"""
        base_slug = slugify(name)
        slug = base_slug
        counter = 1
        
        with self.connection.cursor() as cursor:
            while True:
                cursor.execute("SELECT id FROM shops WHERE slug = %s", (slug,))
                if not cursor.fetchone():
                    return slug
                slug = f"{base_slug}-{counter}"
                counter += 1
    
    def check_duplicate(self, google_place_id: str, name: str, address: str) -> bool:
        """Check if shop already exists in database"""
        with self.connection.cursor() as cursor:
            # Check by Google Place ID
            if google_place_id:
                cursor.execute("SELECT id FROM shops WHERE google_place_id = %s", (google_place_id,))
                if cursor.fetchone():
                    return True
            
            # Check by name and similar address
            cursor.execute(
                "SELECT id FROM shops WHERE name = %s AND address LIKE %s",
                (name, f"%{address[:30]}%")
            )
            if cursor.fetchone():
                return True
        
        return False
    
    def import_shop(self, shop_data: Dict, default_user_id: int = 1) -> bool:
        """Import single shop into database"""
        try:
            # Check for duplicates
            if self.check_duplicate(
                shop_data.get('google_place_id'),
                shop_data.get('name'),
                shop_data.get('address', '')
            ):
                print(f"⚠️  Duplicate skipped: {shop_data.get('name')}")
                return False
            
            # Find city and category
            city_id = self.find_city_id(shop_data.get('address', ''))
            category_id = self.find_category_id(
                shop_data.get('category'),
                shop_data.get('google_types', [])
            )
            
            if not city_id or not category_id:
                print(f"⚠️  Missing city or category: {shop_data.get('name')}")
                return False
            
            # Generate slug
            slug = self.generate_unique_slug(shop_data.get('name', 'shop'))
            
            # Prepare data
            insert_data = {
                'user_id': default_user_id,
                'city_id': city_id,
                'category_id': category_id,
                'name': shop_data.get('name'),
                'slug': slug,
                'google_place_id': shop_data.get('google_place_id'),
                'description': shop_data.get('description') or f"متجر {shop_data.get('name')} في {shop_data.get('address', '')}",
                'address': shop_data.get('address'),
                'latitude': shop_data.get('latitude'),
                'longitude': shop_data.get('longitude'),
                'phone': shop_data.get('phone'),
                'website': shop_data.get('website'),
                'email': None,  # Usually not available
                'images': json.dumps(shop_data.get('images', [])) if shop_data.get('images') else None,
                'opening_hours': json.dumps(shop_data.get('opening_hours', {})) if shop_data.get('opening_hours') else None,
                'rating': shop_data.get('rating') or 0.00,
                'review_count': shop_data.get('review_count') or 0,
                'google_types': json.dumps(shop_data.get('google_types', [])) if shop_data.get('google_types') else None,
                'is_featured': False,
                'is_verified': False,
                'is_active': True,
                'created_at': datetime.now(),
                'updated_at': datetime.now()
            }
            
            # Insert into database
            with self.connection.cursor() as cursor:
                columns = ', '.join(insert_data.keys())
                placeholders = ', '.join(['%s'] * len(insert_data))
                sql = f"INSERT INTO shops ({columns}) VALUES ({placeholders})"
                
                cursor.execute(sql, list(insert_data.values()))
                self.connection.commit()
            
            print(f"✅ Imported: {shop_data.get('name')}")
            return True
            
        except Exception as e:
            print(f"❌ Error importing {shop_data.get('name')}: {str(e)}")
            self.connection.rollback()
            return False
    
    def import_all(self, shops_data: List[Dict], default_user_id: int = 1) -> Dict:
        """Import all shops from data"""
        stats = {
            'total': len(shops_data),
            'imported': 0,
            'skipped': 0,
            'errors': 0
        }
        
        print(f"\n{'='*60}")
        print(f"📥 Starting import of {stats['total']} shops")
        print(f"{'='*60}\n")
        
        for i, shop in enumerate(shops_data, 1):
            print(f"[{i}/{stats['total']}] ", end='')
            
            if self.import_shop(shop, default_user_id):
                stats['imported'] += 1
            else:
                stats['skipped'] += 1
        
        print(f"\n{'='*60}")
        print(f"📊 Import Summary:")
        print(f"   • Total shops: {stats['total']}")
        print(f"   • Successfully imported: {stats['imported']}")
        print(f"   • Skipped (duplicates): {stats['skipped']}")
        print(f"   • Errors: {stats['errors']}")
        print(f"{'='*60}\n")
        
        return stats
    
    def close(self):
        """Close database connection"""
        self.connection.close()
        print("🔒 Database connection closed")


def main():
    """Main execution function"""
    
    if len(sys.argv) < 2:
        print("""
Usage: python import_to_database.py <json_file> [user_id]

Arguments:
    json_file    Path to JSON file with scraped data
    user_id      Optional: User ID to assign shops to (default: 1)

Example:
    python import_to_database.py shops_data.json
    python import_to_database.py shops_data.json 2
        """)
        sys.exit(1)
    
    json_file = sys.argv[1]
    user_id = int(sys.argv[2]) if len(sys.argv) > 2 else 1
    
    print("""
    ╔══════════════════════════════════════════════════════════╗
    ║         Database Importer for Google Maps Data           ║
    ║                                                          ║
    ║  Imports scraped shop data directly into database        ║
    ╚══════════════════════════════════════════════════════════╝
    """)
    
    # Initialize importer
    importer = DatabaseImporter()
    
    try:
        # Load data
        shops_data = importer.load_json_data(json_file)
        
        if not shops_data:
            print("❌ No data to import")
            return
        
        # Import data
        stats = importer.import_all(shops_data, user_id)
        
        if stats['imported'] > 0:
            print(f"✅ Import completed successfully!")
            print(f"   Check your database for the new shops.")
        
    except KeyboardInterrupt:
        print("\n\n⚠️ Import interrupted by user")
    except Exception as e:
        print(f"\n❌ Error: {str(e)}")
    finally:
        importer.close()


if __name__ == "__main__":
    main()
