<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Rating;
use App\Models\Shop;

class UpdateRatingsStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'ratings:update-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update all existing ratings to have active status and recalculate shop ratings';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Updating ratings status...');
        
        // Update all existing ratings to active status
        $updated = Rating::where('status', 'pending')->update(['status' => 'active']);
        $this->info("Updated {$updated} ratings to active status");
        
        // Recalculate all shop ratings
        $this->info('Recalculating shop ratings...');
        $shops = Shop::all();
        $count = 0;
        
        foreach ($shops as $shop) {
            $shop->updateRating();
            $count++;
        }
        
        $this->info("Recalculated ratings for {$count} shops");
        $this->info('Done!');
        
        return 0;
    }
}
