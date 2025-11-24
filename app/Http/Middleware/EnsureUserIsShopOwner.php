<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsShopOwner
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!Auth::check()) {
            return redirect()->route('login')
                ->with('error', 'يجب تسجيل الدخول أولاً.');
        }

        if (!Auth::user()->isShopOwner()) {
            return redirect()->route('profile')
                ->with('error', 'هذه الصفحة مخصصة لأصحاب المتاجر فقط. يرجى ترقية حسابك إلى حساب صاحب متجر.');
        }

        return $next($request);
    }
}
