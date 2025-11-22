<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Shop;
use App\Models\Category;
use App\Models\City;
use Illuminate\Support\Facades\Storage;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        if ($user->isShopOwner()) {
            return $this->shopOwnerDashboard();
        }
        
        return $this->regularUserDashboard();
    }

    private function shopOwnerDashboard()
    {
        $seoData = [
            'title' => 'لوحة تحكم صاحب المتجر - اكتشف المدن',
            'description' => 'إدارة متاجرك ومراقبة أدائها من خلال لوحة التحكم الخاصة بأصحاب المتاجر',
            'keywords' => 'لوحة تحكم, صاحب متجر, إدارة متاجر, إحصائيات',
            'canonical' => route('shop-owner.dashboard')
        ];

        $shops = Auth::user()->shops()->with(['city', 'category'])->get();
        
        return view('shop-owner.dashboard', compact('shops', 'seoData'));
    }

    private function regularUserDashboard()
    {
        // Redirect regular users to their profile page
        return redirect()->route('profile');
    }

    public function createShop()
    {
        $seoData = [
            'title' => 'إضافة متجر جديد - اكتشف المدن',
            'description' => 'أضف متجرك الجديد وابدأ في جذب العملاء من خلال منصة اكتشف المدن',
            'keywords' => 'إضافة متجر, متجر جديد, تسجيل متجر, أصحاب المتاجر',
            'canonical' => route('shop-owner.create-shop')
        ];

        $categories = Category::all();
        $cities = City::all();
        
        return view('shop-owner.create-shop', compact('categories', 'cities', 'seoData'));
    }

    public function storeShop(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'city_id' => 'required|exists:cities,id',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'description' => 'nullable|string|max:1000',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'working_hours' => 'nullable|string|max:500',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048'
        ]);

        $shopData = $request->only([
            'name', 'category_id', 'city_id', 'phone', 'address', 
            'description', 'latitude', 'longitude', 'working_hours'
        ]);
        
        $shopData['user_id'] = Auth::id();
        $shopData['status'] = 'pending';
        
        // Handle images
        if ($request->hasFile('images')) {
            $images = [];
            foreach ($request->file('images') as $image) {
                $path = $image->store('shops', 'public');
                $images[] = $path;
            }
            $shopData['images'] = $images;
        }

        Shop::create($shopData);

        return redirect()->route('shop-owner.dashboard')
            ->with('success', 'تم إرسال طلب إضافة المتجر بنجاح! سيتم مراجعته خلال 24-48 ساعة.');
    }

    public function editShop(Shop $shop)
    {
        // Ensure user owns this shop
        if ($shop->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بتعديل هذا المتجر.');
        }

        $seoData = [
            'title' => 'تعديل متجر: ' . $shop->name . ' - اكتشف المدن',
            'description' => 'تعديل معلومات متجر ' . $shop->name . ' وتحديث البيانات والصور',
            'keywords' => 'تعديل متجر, ' . $shop->name . ', تحديث معلومات متجر',
            'canonical' => route('shop-owner.shops.edit', $shop)
        ];

        $categories = Category::all();
        $cities = City::all();
        
        return view('shop-owner.edit-shop', compact('shop', 'categories', 'cities', 'seoData'));
    }

    public function updateShop(Request $request, Shop $shop)
    {
        // Ensure user owns this shop
        if ($shop->user_id !== Auth::id()) {
            abort(403, 'غير مصرح لك بتعديل هذا المتجر.');
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'category_id' => 'required|exists:categories,id',
            'city_id' => 'required|exists:cities,id',
            'phone' => 'required|string|max:20',
            'address' => 'required|string|max:500',
            'description' => 'nullable|string|max:1000',
            'latitude' => 'nullable|numeric|between:-90,90',
            'longitude' => 'nullable|numeric|between:-180,180',
            'working_hours' => 'nullable|string|max:500',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'delete_images' => 'nullable|array',
            'delete_images.*' => 'integer'
        ]);

        $shopData = $request->only([
            'name', 'category_id', 'city_id', 'phone', 'address', 
            'description', 'latitude', 'longitude', 'working_hours'
        ]);

        // Handle image deletion
        $currentImages = $shop->images ?? [];
        if ($request->has('delete_images')) {
            foreach ($request->delete_images as $index) {
                if (isset($currentImages[$index])) {
                    // Delete file from storage
                    Storage::disk('public')->delete($currentImages[$index]);
                    unset($currentImages[$index]);
                }
            }
            $currentImages = array_values($currentImages); // Reindex array
        }

        // Handle new images
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                $path = $image->store('shops', 'public');
                $currentImages[] = $path;
            }
        }

        $shopData['images'] = $currentImages;
        
        // If shop was approved and user made changes, set back to pending
        if ($shop->status === 'approved') {
            $shopData['status'] = 'pending';
            $shopData['verification_notes'] = 'تم تحديث المتجر - في انتظار مراجعة التعديلات';
        }

        $shop->update($shopData);

        return redirect()->route('shop-owner.dashboard')
            ->with('success', 'تم تحديث المتجر بنجاح!' . 
                ($shop->status === 'pending' ? ' سيتم مراجعة التعديلات قريباً.' : ''));
    }
}