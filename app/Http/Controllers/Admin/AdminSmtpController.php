<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SmtpSettings;
use App\Services\DynamicMailService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AdminSmtpController extends Controller
{
    /**
     * Display SMTP settings page
     */
    public function index()
    {
        $settings = SmtpSettings::getActive();
        return view('admin.smtp.index', compact('settings'));
    }

    /**
     * Store or update SMTP settings
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'host' => 'required|string',
            'port' => 'required|integer',
            'encryption' => 'required|in:tls,ssl,none',
            'username' => 'required|string',
            'password' => 'required|string',
            'from_address' => 'required|email',
            'from_name' => 'required|string',
            'notification_emails' => 'required|string',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        // Parse notification emails (one per line)
        $notificationEmails = array_filter(
            array_map('trim', explode("\n", $request->notification_emails)),
            function($email) {
                return filter_var($email, FILTER_VALIDATE_EMAIL);
            }
        );

        if (empty($notificationEmails)) {
            return redirect()->back()
                ->withErrors(['notification_emails' => 'Please provide at least one valid email address.'])
                ->withInput();
        }

        // Deactivate all existing settings
        SmtpSettings::where('is_active', true)->update(['is_active' => false]);

        // Create or update settings
        $settings = SmtpSettings::create([
            'host' => $request->host,
            'port' => $request->port,
            'encryption' => $request->encryption === 'none' ? null : $request->encryption,
            'username' => $request->username,
            'password' => $request->password,
            'from_address' => $request->from_address,
            'from_name' => $request->from_name,
            'notification_emails' => array_values($notificationEmails),
            'is_active' => true,
        ]);

        return redirect()->route('admin.smtp.index')
            ->with('success', 'SMTP settings saved successfully!');
    }

    /**
     * Test SMTP configuration
     */
    public function test(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'test_email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Please provide a valid email address',
            ], 422);
        }

        $settings = SmtpSettings::getActive();

        if (!$settings) {
            return response()->json([
                'success' => false,
                'message' => 'No SMTP settings configured. Please save settings first.',
            ], 404);
        }

        $result = DynamicMailService::testConnection($settings, $request->test_email);

        return response()->json($result);
    }

    /**
     * Delete SMTP settings
     */
    public function destroy($id)
    {
        $settings = SmtpSettings::findOrFail($id);
        $settings->delete();

        return redirect()->route('admin.smtp.index')
            ->with('success', 'SMTP settings deleted successfully!');
    }

    /**
     * Activate specific SMTP settings
     */
    public function activate($id)
    {
        // Deactivate all
        SmtpSettings::where('is_active', true)->update(['is_active' => false]);

        // Activate selected
        $settings = SmtpSettings::findOrFail($id);
        $settings->update(['is_active' => true]);

        return redirect()->route('admin.smtp.index')
            ->with('success', 'SMTP settings activated successfully!');
    }
}
