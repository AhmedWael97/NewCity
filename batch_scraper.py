"""
Batch Google Maps Scraper
=========================
Run multiple searches at once for different categories and locations

Usage:
    python batch_scraper.py
"""

from google_maps_scraper import GoogleMapsScraper
from datetime import datetime
import time


def batch_scrape():
    """Run multiple scraping tasks"""
    
    # Define your scraping tasks
    tasks = [
        {
            'query': 'restaurants',
            'location': 'Cairo, Egypt',
            'max_results': 50
        },
        {
            'query': 'cafes',
            'location': 'Cairo, Egypt',
            'max_results': 30
        },
        {
            'query': 'pharmacies',
            'location': 'Alexandria, Egypt',
            'max_results': 40
        },
        {
            'query': 'supermarkets',
            'location': 'Giza, Egypt',
            'max_results': 25
        },
        # Add more tasks as needed
    ]
    
    print(f"""
    ╔══════════════════════════════════════════════════════════╗
    ║              Batch Google Maps Scraper                   ║
    ║                                                          ║
    ║  Running {len(tasks)} scraping tasks                           ║
    ╚══════════════════════════════════════════════════════════╝
    """)
    
    all_shops = []
    
    for i, task in enumerate(tasks, 1):
        print(f"\n{'='*60}")
        print(f"📍 Task {i}/{len(tasks)}: {task['query']} in {task['location']}")
        print(f"{'='*60}\n")
        
        scraper = GoogleMapsScraper(headless=False)
        
        try:
            shops = scraper.scrape(
                query=task['query'],
                location=task['location'],
                max_results=task['max_results']
            )
            
            # Add task info to each shop
            for shop in shops:
                shop['search_query'] = task['query']
                shop['search_location'] = task['location']
            
            all_shops.extend(shops)
            
        except Exception as e:
            print(f"❌ Error in task {i}: {str(e)}")
        finally:
            scraper.close()
        
        # Delay between tasks
        if i < len(tasks):
            print(f"\n⏳ Waiting 5 seconds before next task...")
            time.sleep(5)
    
    # Save combined results
    if all_shops:
        timestamp = datetime.now().strftime("%Y%m%d_%H%M%S")
        
        scraper_final = GoogleMapsScraper(headless=True)
        scraper_final.shops_data = all_shops
        
        print(f"\n{'='*60}")
        print(f"💾 Saving combined results...")
        print(f"{'='*60}\n")
        
        scraper_final.save_to_csv(f"batch_shops_data_{timestamp}.csv")
        scraper_final.save_to_excel(f"batch_shops_data_{timestamp}.xlsx")
        scraper_final.save_to_json(f"batch_shops_data_{timestamp}.json")
        
        print(f"\n📊 Final Summary:")
        print(f"   • Total shops scraped: {len(all_shops)}")
        print(f"   • Tasks completed: {len(tasks)}")
        
        scraper_final.close()


if __name__ == "__main__":
    batch_scrape()
