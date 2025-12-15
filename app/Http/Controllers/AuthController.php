<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\City;
use App\Services\AdminEmailQueueService;

class AuthController extends Controller
{
    public function showLoginForm()
    {
        $seoData = [
            'title' => 'تسجيل الدخول - SENÚ سنو',
            'description' => 'سجل دخولك للوصول إلى حسابك واكتشف أفضل المتاجر والخدمات في مدينتك',
            'keywords' => 'تسجيل دخول, حساب, متاجر, خدمات, مدن',
            'canonical' => route('login')
        ];

        return view('auth.login', compact('seoData'));
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->filled('remember'))) {
            $request->session()->regenerate();
            
            $user = Auth::user();
            
            // Redirect based on user type
            if ($user->isAdmin()) {
                return redirect()->intended('/dashboard');
            } elseif ($user->isShopOwner()) {
                return redirect()->intended(route('shop-owner.dashboard'));
            } else {
                return redirect()->intended('/');
            }
        }

        return back()->withErrors([
            'email' => 'البيانات المدخلة غير صحيحة.',
        ])->onlyInput('email');
    }

    public function showRegistrationForm()
    {
        $seoData = [
            'title' => 'إنشاء حساب جديد - SENÚ سنو',
            'description' => 'أنشئ حسابك الآن وابدأ في اكتشاف المتاجر والخدمات أو أضف متجرك الخاص',
            'keywords' => 'تسجيل, حساب جديد, صاحب متجر, متاجر, خدمات',
            'canonical' => route('register')
        ];

        $cities = City::all();
        return view('auth.register', compact('cities', 'seoData'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'user_type' => 'required|in:regular,shop_owner',
            'city_id' => 'required|exists:cities,id',
            'phone' => 'nullable|string|max:20',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_type' => $request->user_type,
            'city_id' => $request->city_id,
            'phone' => $request->phone,
            'is_verified' => $request->user_type === 'regular' ? true : false,
        ]);

        // Queue email notification to admins
        AdminEmailQueueService::queueNewUser($user);

        Auth::login($user);

        // Redirect based on user type
        if ($user->isShopOwner()) {
            return redirect()->route('shop-owner.dashboard')
                ->with('success', 'تم إنشاء حسابك بنجاح! يمكنك الآن إضافة متجرك.');
        } else {
            return redirect('/')
                ->with('success', 'تم إنشاء حسابك بنجاح! مرحباً بك معنا.');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'تم تسجيل الخروج بنجاح.');
    }

    public function showProfile()
    {
        $seoData = [
            'title' => 'الملف الشخصي - SENÚ سنو',
            'description' => 'إدارة معلوماتك الشخصية وإعدادات حسابك',
            'keywords' => 'ملف شخصي, إعدادات, حساب, معلومات شخصية',
            'canonical' => route('profile')
        ];

        $cities = City::all();
        return view('profile', compact('cities', 'seoData'));
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'city_id' => 'required|exists:cities,id',
            'phone' => 'nullable|string|max:20',
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
            'city_id' => $request->city_id,
            'phone' => $request->phone,
        ]);

        return back()->with('success', 'تم تحديث ملفك الشخصي بنجاح.');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors([
                'current_password' => 'كلمة المرور الحالية غير صحيحة.',
            ]);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return back()->with('success', 'تم تغيير كلمة المرور بنجاح.');
    }
}
