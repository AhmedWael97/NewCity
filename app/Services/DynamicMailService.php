<?php

namespace App\Services;

use App\Models\SmtpSettings;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;

class DynamicMailService
{
    /**
     * Configure mail settings from database
     */
    public static function configure()
    {
        $settings = SmtpSettings::getActive();

        if (!$settings) {
            return false;
        }

        Config::set('mail.mailers.smtp', [
            'transport' => 'smtp',
            'host' => $settings->host,
            'port' => $settings->port,
            'encryption' => $settings->encryption,
            'username' => $settings->username,
            'password' => $settings->getDecryptedPassword(),
            'timeout' => null,
        ]);

        Config::set('mail.from', [
            'address' => $settings->from_address,
            'name' => $settings->from_name,
        ]);

        return true;
    }

    /**
     * Test email configuration
     */
    public static function testConnection(SmtpSettings $settings, string $testEmail)
    {
        try {
            // Temporarily set mail configuration
            Config::set('mail.mailers.smtp', [
                'transport' => 'smtp',
                'host' => $settings->host,
                'port' => $settings->port,
                'encryption' => $settings->encryption,
                'username' => $settings->username,
                'password' => $settings->getDecryptedPassword(),
                'timeout' => null,
            ]);

            Config::set('mail.from', [
                'address' => $settings->from_address,
                'name' => $settings->from_name,
            ]);

            // Send test email
            Mail::raw('This is a test email from your City App. If you received this, your SMTP configuration is working correctly!', function ($message) use ($testEmail, $settings) {
                $message->to($testEmail)
                    ->subject('SMTP Configuration Test - City App')
                    ->from($settings->from_address, $settings->from_name);
            });

            // Update settings
            $settings->update([
                'last_tested_at' => now(),
                'test_successful' => true,
                'test_error' => null,
            ]);

            return [
                'success' => true,
                'message' => 'Test email sent successfully! Please check your inbox at ' . $testEmail,
            ];
        } catch (\Exception $e) {
            // Update settings with error
            $settings->update([
                'last_tested_at' => now(),
                'test_successful' => false,
                'test_error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message' => 'Failed to send test email: ' . $e->getMessage(),
            ];
        }
    }
}
