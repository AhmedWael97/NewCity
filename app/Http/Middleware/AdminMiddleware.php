<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Force admin guard for downstream auth() / gate checks
        Auth::shouldUse('admin');
        
        // Ensure session has the correct guard set
        if (!$request->session()->has('_guard') || $request->session()->get('_guard') !== 'admin') {
            $request->session()->put('_guard', 'admin');
        }

        // Check if user is authenticated on admin guard
        if (!Auth::guard('admin')->check()) {
            return redirect()->route('admin.login');
        }

        // Check if the authenticated user has admin role
        $user = Auth::guard('admin')->user();
        if ($user->user_type !== 'admin') {
            Auth::guard('admin')->logout();
            return redirect()->route('admin.login')->with('error', 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        // Check if user is active
        if (!$user->is_active) {
            Auth::guard('admin')->logout();
            return redirect()->route('admin.login')->with('error', 'حسابك غير نشط');
        }

        return $next($request);
    }
}