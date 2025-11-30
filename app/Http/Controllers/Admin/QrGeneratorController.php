<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class QrGeneratorController extends Controller
{
    /**
     * Show QR generator form
     */
    public function index()
    {
        return view('admin.qr-generator.index');
    }

    /**
     * Generate QR code from input
     */
    public function generate(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
            'size' => 'nullable|integer|min:100|max:1000',
        ]);

        $content = $request->input('content');
        $size = $request->input('size', 300);

        // Generate QR code as SVG (doesn't require Imagick)
        $qrCode = QrCode::size($size)
            ->generate($content);

        // Return as data URI for display
        $dataUri = 'data:image/svg+xml;base64,' . base64_encode($qrCode);

        return response()->json([
            'success' => true,
            'qrCode' => $dataUri,
            'content' => $content,
            'size' => $size
        ]);
    }

    /**
     * Download QR code
     */
    public function download(Request $request)
    {
        $request->validate([
            'content' => 'required|string|max:2000',
            'size' => 'nullable|integer|min:100|max:1000',
        ]);

        $content = $request->input('content');
        $size = $request->input('size', 500);

        // Generate QR code as SVG
        $qrCode = QrCode::size($size)
            ->generate($content);

        $filename = 'qr-code-' . date('Y-m-d-H-i-s') . '.svg';

        return response($qrCode)
            ->header('Content-Type', 'image/svg+xml')
            ->header('Content-Disposition', 'attachment; filename="' . $filename . '"');
    }
}
