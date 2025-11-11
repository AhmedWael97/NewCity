<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\File;

class AdminLogsController extends Controller
{
    /**
     * Display system logs
     */
    public function index(Request $request)
    {
        $logPath = storage_path('logs');
        $logFiles = [];
        
        if (File::exists($logPath)) {
            $files = File::files($logPath);
            foreach ($files as $file) {
                if (pathinfo($file, PATHINFO_EXTENSION) === 'log') {
                    $logFiles[] = [
                        'name' => $file->getFilename(),
                        'path' => $file->getPathname(),
                        'size' => $file->getSize(),
                        'modified' => $file->getMTime(),
                    ];
                }
            }
        }

        // Sort by modification time (newest first)
        usort($logFiles, function($a, $b) {
            return $b['modified'] - $a['modified'];
        });

        $selectedLog = $request->get('log', 'laravel.log');
        $logContent = '';
        $logLines = [];

        if ($selectedLog && File::exists($logPath . '/' . $selectedLog)) {
            $logContent = File::get($logPath . '/' . $selectedLog);
            $logLines = array_reverse(explode("\n", $logContent));
            $logLines = array_filter($logLines); // Remove empty lines
            
            // Limit to last 100 lines for performance
            $logLines = array_slice($logLines, 0, 100);
        }

        return view('admin.logs.index', compact('logFiles', 'selectedLog', 'logLines'));
    }

    /**
     * Download a log file
     */
    public function download(Request $request)
    {
        $logFile = $request->get('file');
        $logPath = storage_path('logs/' . $logFile);

        if (File::exists($logPath)) {
            return response()->download($logPath);
        }

        return back()->with('error', 'Log file not found.');
    }

    /**
     * Clear a log file
     */
    public function clear(Request $request)
    {
        $logFile = $request->get('file');
        $logPath = storage_path('logs/' . $logFile);

        if (File::exists($logPath)) {
            File::put($logPath, '');
            return back()->with('success', 'Log file cleared successfully.');
        }

        return back()->with('error', 'Log file not found.');
    }

    /**
     * Delete a log file
     */
    public function delete(Request $request)
    {
        $logFile = $request->get('file');
        $logPath = storage_path('logs/' . $logFile);

        if (File::exists($logPath)) {
            File::delete($logPath);
            return back()->with('success', 'Log file deleted successfully.');
        }

        return back()->with('error', 'Log file not found.');
    }
}