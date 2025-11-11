<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Shop;

class UpdateShopRatings extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'shops:update-ratings';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all shop ratings based on their reviews';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating shop ratings...');
        
        $shops = Shop::all();
        $progressBar = $this->output->createProgressBar($shops->count());
        
        foreach ($shops as $shop) {
            $shop->updateRating();
            $progressBar->advance();
        }
        
        $progressBar->finish();
        $this->info("\nShop ratings updated successfully!");
        
        // Show statistics
        $totalShops = $shops->count();
        $shopsWithRatings = Shop::where('review_count', '>', 0)->count();
        
        $this->table(['Metric', 'Value'], [
            ['Total Shops', $totalShops],
            ['Shops with Ratings', $shopsWithRatings],
            ['Average Rating', round(Shop::where('review_count', '>', 0)->avg('rating'), 2)],
        ]);
    }
}
