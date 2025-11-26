<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\ShopImageGenerator;

class TestShopImageGenerator extends Command
{
    protected $signature = 'test:shop-image';
    protected $description = 'Test shop image generator';

    public function handle()
    {
        $this->info('Testing Shop Image Generator...');

        $generator = new ShopImageGenerator();
        
        try {
            $path = $generator->generateShopImage(
                'مطعم الفخامة',
                'مطاعم وكافيهات',
                '🍽️'
            );

            $this->info("✅ Image generated successfully!");
            $this->info("Path: storage/app/public/$path");
            $this->info("URL: " . url("storage/$path"));

        } catch (\Exception $e) {
            $this->error("❌ Error: " . $e->getMessage());
            $this->error($e->getTraceAsString());
        }

        return 0;
    }
}
