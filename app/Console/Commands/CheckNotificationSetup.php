<?php

namespace App\Console\Commands;

use App\Models\DeviceToken;
use Illuminate\Console\Command;

class CheckNotificationSetup extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:check';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check Firebase notification configuration and device token status';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking Firebase Notification Setup...');
        $this->newLine();
        
        // Check environment variables
        $this->info('Environment Configuration:');
        $this->checkConfig('FIREBASE_ENABLED', config('services.firebase.enabled'));
        $this->checkConfig('FIREBASE_SERVER_KEY', config('services.firebase.server_key'));
        $this->checkConfig('FIREBASE_API_KEY', config('services.firebase.web.api_key'));
        $this->checkConfig('FIREBASE_PROJECT_ID', config('services.firebase.web.project_id'));
        $this->checkConfig('FIREBASE_MESSAGING_SENDER_ID', config('services.firebase.web.messaging_sender_id'));
        $this->checkConfig('FIREBASE_APP_ID', config('services.firebase.web.app_id'));
        $this->checkConfig('FIREBASE_VAPID_KEY', config('services.firebase.web.vapid_key'));
        $this->newLine();
        
        // Check VAPID key validity
        $vapidKey = config('services.firebase.web.vapid_key');
        if ($vapidKey === 'your-vapid-key-here' || empty($vapidKey)) {
            $this->error('VAPID KEY IS NOT CONFIGURED!');
            $this->warn('   Please update FIREBASE_VAPID_KEY in your .env file');
            $this->warn('   Get it from: Firebase Console -> Project Settings -> Cloud Messaging -> Web Push certificates');
            $this->newLine();
        } else {
            $this->info('VAPID key is configured');
            $this->newLine();
        }
        
        // Check device tokens
        $this->info('Device Token Statistics:');
        $totalTokens = DeviceToken::count();
        $activeTokens = DeviceToken::where('is_active', true)->count();
        $guestTokens = DeviceToken::whereNull('user_id')->count();
        $userTokens = DeviceToken::whereNotNull('user_id')->count();
        $webTokens = DeviceToken::where('device_type', 'web')->count();
        $androidTokens = DeviceToken::where('device_type', 'android')->count();
        $iosTokens = DeviceToken::where('device_type', 'ios')->count();
        
        $this->table(
            ['Metric', 'Count'],
            [
                ['Total Tokens', $totalTokens],
                ['Active Tokens', $activeTokens],
                ['Guest Tokens', $guestTokens],
                ['User Tokens', $userTokens],
                ['Web Tokens', $webTokens],
                ['Android Tokens', $androidTokens],
                ['iOS Tokens', $iosTokens],
            ]
        );
        
        if ($totalTokens === 0) {
            $this->warn('No device tokens found!');
            $this->info('   Possible reasons:');
            $this->info('   1. Users haven\'t allowed notifications yet');
            $this->info('   2. VAPID key is missing or incorrect');
            $this->info('   3. Service worker not loading correctly');
            $this->info('   4. API endpoint errors (check logs)');
            $this->newLine();
        }
        
        // Show recent tokens
        if ($totalTokens > 0) {
            $this->info('Recent Device Tokens (last 5):');
            $recentTokens = DeviceToken::with('user')
                ->latest()
                ->limit(5)
                ->get();
            
            $this->table(
                ['ID', 'User', 'Device Type', 'Device Name', 'Active', 'Created'],
                $recentTokens->map(function ($token) {
                    return [
                        $token->id,
                        $token->user ? $token->user->name : 'Guest',
                        $token->device_type ?? 'N/A',
                        $token->device_name ?? 'Unknown',
                        $token->is_active ? 'Yes' : 'No',
                        $token->created_at->diffForHumans(),
                    ];
                })->toArray()
            );
        }
        
        $this->newLine();
        $this->info('Check complete!');
        
        // Provide next steps
        if ($vapidKey === 'your-vapid-key-here' || empty($vapidKey)) {
            $this->newLine();
            $this->warn('Next Steps:');
            $this->warn('1. Go to Firebase Console: https://console.firebase.google.com');
            $this->warn('2. Select your project: ' . config('services.firebase.web.project_id'));
            $this->warn('3. Go to Project Settings → Cloud Messaging');
            $this->warn('4. Under "Web Push certificates", click "Generate key pair" or copy existing key');
            $this->warn('5. Update .env file: FIREBASE_VAPID_KEY=<your-key>');
            $this->warn('6. Run: php artisan config:clear');
            $this->warn('7. Test by allowing notifications in browser');
        } elseif ($totalTokens === 0) {
            $this->newLine();
            $this->info('Troubleshooting:');
            $this->info('1. Clear browser cache and localStorage');
            $this->info('2. Reload page and check browser console for errors');
            $this->info('3. Verify service worker is registered (F12 → Application → Service Workers)');
            $this->info('4. Check Laravel logs: storage/logs/laravel.log');
            $this->info('5. Test API manually: POST /api/v1/guest-device-tokens');
        }
        
        return 0;
    }
    
    private function checkConfig($name, $value)
    {
        if (empty($value) || $value === 'your-vapid-key-here' || $value === 'your_server_key_here') {
            $this->line("  [X] $name: <fg=red>NOT SET</>");
        } else {
            $shortened = is_string($value) && strlen($value) > 30 
                ? substr($value, 0, 30) . '...' 
                : $value;
            $this->line("  [OK] $name: <fg=green>$shortened</>");
        }
    }
}
