<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ShopOwnerLoginController extends Controller
{
    /**
     * Show the shop owner login form.
     */
    public function showLoginForm()
    {
        return view('auth.shop-owner.login');
    }

    /**
     * Handle a shop owner login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $credentials = $request->only('email', 'password');
        $credentials['user_type'] = 'shop_owner'; // Only allow shop_owner role
        $credentials['is_active'] = true; // Only allow active users

        if (Auth::guard('shop_owner')->attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('shop-owner.dashboard'));
        }

        throw ValidationException::withMessages([
            'email' => [trans('auth.failed')],
        ]);
    }

    /**
     * Log the shop owner out.
     */
    public function logout(Request $request)
    {
        Auth::guard('shop_owner')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('shop-owner.login');
    }
}