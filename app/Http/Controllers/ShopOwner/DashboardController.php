<?php

namespace App\Http\Controllers\ShopOwner;

use App\Http\Controllers\Controller;
use App\Models\Shop;
use App\Models\City;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware(function ($request, $next) {
            if (!Auth::user()->isShopOwner()) {
                abort(403, 'غير مصرح لك بالوصول لهذه الصفحة');
            }
            return $next($request);
        });
    }

    /**
     * Show shop owner dashboard
     */
    public function index()
    {
        $user = Auth::user();
        $shops = $user->shops()->with(['city', 'category'])->latest()->get();
        
        return view('shop-owner.dashboard', compact('shops'));
    }

    /**
     * Show form to create new shop
     */
    public function createShop()
    {
        $cities = City::where('is_active', true)->orderBy('name')->get();
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        
        return view('shop-owner.create-shop', compact('cities', 'categories'));
    }

    /**
     * Store new shop
     */
    public function storeShop(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'city_id' => 'required|exists:cities,id',
            'category_id' => 'required|exists:categories,id',
            'address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'opening_hours' => 'nullable|array',
        ]);

        // Handle image uploads
        $images = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('shops', 'public');
                $images[] = $path;
            }
        }

        $shop = Shop::create([
            'user_id' => Auth::id(),
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'city_id' => $request->city_id,
            'category_id' => $request->category_id,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'website' => $request->website,
            'images' => $images,
            'opening_hours' => $request->opening_hours,
            'status' => Shop::STATUS_PENDING,
            'is_verified' => false,
            'is_active' => false,
        ]);

        return redirect()->route('shop-owner.dashboard')
                        ->with('success', 'تم إرسال طلب إضافة المتجر بنجاح! سيتم مراجعته من قبل الإدارة.');
    }

    /**
     * Show shop edit form
     */
    public function editShop(Shop $shop)
    {
        // Check if user owns this shop
        if ($shop->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بتعديل هذا المتجر');
        }

        $cities = City::where('is_active', true)->orderBy('name')->get();
        $categories = Category::where('is_active', true)->orderBy('name')->get();
        
        return view('shop-owner.edit-shop', compact('shop', 'cities', 'categories'));
    }

    /**
     * Update shop
     */
    public function updateShop(Request $request, Shop $shop)
    {
        // Check if user owns this shop
        if ($shop->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بتعديل هذا المتجر');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:1000',
            'city_id' => 'required|exists:cities,id',
            'category_id' => 'required|exists:categories,id',
            'address' => 'required|string|max:500',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'website' => 'nullable|url|max:255',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'opening_hours' => 'nullable|array',
        ]);

        // Handle image uploads
        $images = $shop->images ?? [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('shops', 'public');
                $images[] = $path;
            }
        }

        $shop->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'description' => $request->description,
            'city_id' => $request->city_id,
            'category_id' => $request->category_id,
            'address' => $request->address,
            'phone' => $request->phone,
            'email' => $request->email,
            'website' => $request->website,
            'images' => $images,
            'opening_hours' => $request->opening_hours,
        ]);

        return back()->with('success', 'تم تحديث بيانات المتجر بنجاح.');
    }
}
