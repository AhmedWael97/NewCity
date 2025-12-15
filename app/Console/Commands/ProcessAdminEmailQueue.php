<?php

namespace App\Console\Commands;

use App\Mail\AdminNotificationMail;
use App\Models\AdminEmailQueue;
use App\Services\DynamicMailService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;

class ProcessAdminEmailQueue extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'email:process-admin-queue {--limit=10 : Maximum number of emails to process}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process admin email queue and send pending emails';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting admin email queue processing...');

        // Configure dynamic mail settings
        if (!DynamicMailService::configure()) {
            $this->error('No active SMTP settings found. Please configure SMTP settings first.');
            return Command::FAILURE;
        }

        // Get pending emails
        $limit = (int) $this->option('limit');
        $pendingEmails = AdminEmailQueue::pending()
            ->where('attempts', '<', 3) // Max 3 attempts
            ->orderBy('created_at', 'asc')
            ->limit($limit)
            ->get();

        if ($pendingEmails->isEmpty()) {
            $this->info('No pending emails to process.');
            return Command::SUCCESS;
        }

        $this->info("Found {$pendingEmails->count()} pending email(s).");

        $sent = 0;
        $failed = 0;

        foreach ($pendingEmails as $email) {
            try {
                $this->line("Processing email ID: {$email->id}...");

                // Mark as processing
                $email->markAsProcessing();

                // Send email to all recipients
                foreach ($email->recipients as $recipient) {
                    Mail::to($recipient)->send(
                        new AdminNotificationMail(
                            $email->subject,
                            $email->body,
                            $email->event_data
                        )
                    );
                }

                // Mark as sent
                $email->markAsSent();
                $sent++;
                $this->info("✓ Email ID {$email->id} sent successfully to " . count($email->recipients) . " recipient(s).");

            } catch (\Exception $e) {
                // Mark as failed
                $email->markAsFailed($e->getMessage());
                $failed++;
                $this->error("✗ Failed to send email ID {$email->id}: " . $e->getMessage());
            }
        }

        $this->info("\nProcessing complete:");
        $this->info("- Sent: {$sent}");
        $this->info("- Failed: {$failed}");

        return Command::SUCCESS;
    }
}
