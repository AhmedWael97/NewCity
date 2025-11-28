<?php

namespace App\Console\Commands;

use App\Models\PushNotification;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendScheduledNotifications extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:send-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Send scheduled push notifications';

    protected $notificationService;

    /**
     * Create a new command instance.
     */
    public function __construct(NotificationService $notificationService)
    {
        parent::__construct();
        $this->notificationService = $notificationService;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Checking for scheduled notifications...');

        // Get pending notifications that are scheduled for now or earlier
        $notifications = PushNotification::where('status', 'pending')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now())
            ->get();

        if ($notifications->isEmpty()) {
            $this->info('No scheduled notifications to send.');
            return 0;
        }

        $this->info("Found {$notifications->count()} scheduled notification(s) to send.");

        foreach ($notifications as $notification) {
            try {
                $this->info("Sending notification: {$notification->title}");
                
                $result = $this->notificationService->sendPushNotification($notification);

                if ($result['success']) {
                    $this->info("✓ Sent to {$result['success_count']} device(s)");
                } else {
                    $this->error("✗ Failed: {$result['message']}");
                }
            } catch (\Exception $e) {
                $this->error("✗ Error sending notification {$notification->id}: {$e->getMessage()}");
                Log::error('Scheduled notification send failed', [
                    'notification_id' => $notification->id,
                    'error' => $e->getMessage()
                ]);
            }
        }

        $this->info('Scheduled notifications processing complete.');
        return 0;
    }
}
