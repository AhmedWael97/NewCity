<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\UserVerification;
use Illuminate\Http\Request;

class UserVerificationController extends Controller
{
    /**
     * Display user verifications dashboard
     */
    public function index()
    {
        $stats = UserVerification::getStats();
        
        $verifications = UserVerification::latest()
            ->paginate(50);

        return view('admin.verifications.index', compact('stats', 'verifications'));
    }

    /**
     * Show verification details
     */
    public function show(UserVerification $verification)
    {
        return view('admin.verifications.show', compact('verification'));
    }

    /**
     * Delete verification
     */
    public function destroy(UserVerification $verification)
    {
        $verification->delete();

        return redirect()->route('admin.verifications.index')
            ->with('success', 'تم حذف التحقق بنجاح');
    }

    /**
     * Export verifications to CSV
     */
    public function export()
    {
        $verifications = UserVerification::all();

        $filename = 'verifications_' . date('Y-m-d_H-i-s') . '.csv';
        
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"$filename\"",
        ];

        $callback = function() use ($verifications) {
            $file = fopen('php://output', 'w');
            
            // UTF-8 BOM for Arabic support in Excel
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));
            
            // Headers
            fputcsv($file, [
                'ID', 'Name', 'Email', 'Message', 'IP', 'Browser', 
                'Device', 'Verified At', 'Session ID'
            ]);

            // Data
            foreach ($verifications as $v) {
                fputcsv($file, [
                    $v->id,
                    $v->name,
                    $v->email,
                    $v->message,
                    $v->ip_address,
                    $v->browser,
                    $v->device,
                    $v->verified_at->format('Y-m-d H:i:s'),
                    $v->session_id,
                ]);
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
