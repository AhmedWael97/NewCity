<?php

namespace App\Http\Controllers;

use App\Models\UserVerification;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class UserVerificationController extends Controller
{
    /**
     * Check if current session needs verification
     */
    public function checkVerification(Request $request)
    {
        $sessionId = $request->session()->getId();
        $isVerified = UserVerification::isSessionVerified($sessionId);

        return response()->json([
            'needs_verification' => !$isVerified,
            'session_id' => $sessionId,
        ]);
    }

    /**
     * Submit verification message
     */
    public function verify(Request $request)
    {
        $request->validate([
            'message' => 'required|string|min:3|max:500',
            'email' => 'nullable|email|max:255',
            'name' => 'nullable|string|max:255',
        ], [
            'message.required' => 'الرجاء كتابة رسالة للتحقق',
            'message.min' => 'الرسالة يجب أن تكون 3 أحرف على الأقل',
            'message.max' => 'الرسالة يجب ألا تزيد عن 500 حرف',
            'email.email' => 'البريد الإلكتروني غير صحيح',
        ]);

        $sessionId = $request->session()->getId();

        // Check if already verified
        if (UserVerification::isSessionVerified($sessionId)) {
            return response()->json([
                'success' => true,
                'message' => 'تم التحقق بالفعل',
            ]);
        }

        // Parse user agent for browser and device info
        $userAgent = $request->userAgent();
        $browser = $this->getBrowser($userAgent);
        $device = $this->getDevice($userAgent);

        // Create verification record
        $verification = UserVerification::create([
            'session_id' => $sessionId,
            'ip_address' => $request->ip(),
            'user_agent' => $userAgent,
            'message' => $request->message,
            'email' => $request->email,
            'name' => $request->name,
            'is_verified' => true,
            'verified_at' => now(),
            'browser' => $browser,
            'device' => $device,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'شكراً لك! تم التحقق بنجاح',
            'verification_id' => $verification->id,
        ]);
    }

    /**
     * Get browser name from user agent
     */
    private function getBrowser($userAgent): string
    {
        if (preg_match('/MSIE/i', $userAgent)) {
            return 'Internet Explorer';
        } elseif (preg_match('/Firefox/i', $userAgent)) {
            return 'Firefox';
        } elseif (preg_match('/Chrome/i', $userAgent) && !preg_match('/Edge/i', $userAgent)) {
            return 'Chrome';
        } elseif (preg_match('/Safari/i', $userAgent) && !preg_match('/Chrome/i', $userAgent)) {
            return 'Safari';
        } elseif (preg_match('/Edge/i', $userAgent)) {
            return 'Edge';
        } elseif (preg_match('/Opera|OPR/i', $userAgent)) {
            return 'Opera';
        }
        return 'Unknown';
    }

    /**
     * Get device type from user agent
     */
    private function getDevice($userAgent): string
    {
        if (preg_match('/mobile/i', $userAgent)) {
            return 'Mobile';
        } elseif (preg_match('/tablet/i', $userAgent)) {
            return 'Tablet';
        }
        return 'Desktop';
    }
}
