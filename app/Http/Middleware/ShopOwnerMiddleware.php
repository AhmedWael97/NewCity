<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class ShopOwnerMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is authenticated on shop_owner guard
        if (!Auth::guard('shop_owner')->check()) {
            return redirect()->route('shop-owner.login');
        }

        // Check if the authenticated user has shop_owner or admin role
        $user = Auth::guard('shop_owner')->user();
        if (!in_array($user->user_type, ['shop_owner', 'admin'])) {
            Auth::guard('shop_owner')->logout();
            return redirect()->route('shop-owner.login')->with('error', 'غير مصرح لك بالوصول إلى هذه الصفحة');
        }

        // Check if user is active
        if (!$user->is_active) {
            Auth::guard('shop_owner')->logout();
            return redirect()->route('shop-owner.login')->with('error', 'حسابك غير نشط');
        }

        return $next($request);
    }
}