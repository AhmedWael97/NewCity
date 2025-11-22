@extends('layouts.app')

@section('title', $seoData['title'] ?? 'المتاجر')
@section('description', $seoData['description'] ?? 'اكتشف أفضل المتاجر')
@section('keywords', $seoData['keywords'] ?? 'متاجر, مصر')
@section('canonical', $seoData['canonical'] ?? url()->current())

@section('content')
{{-- Breadcrumbs --}}
<nav class="bg-gray-50 border-b border-gray-200 py-3">
    <div class="container mx-auto px-4">
        <ol class="flex items-center space-x-2 text-sm">
            @foreach($seoData['breadcrumbs'] as $index => $crumb)
                @if($loop->last)
                    <li class="text-gray-600">{{ $crumb['name'] }}</li>
                @else
                    <li>
                        <a href="{{ $crumb['url'] }}" class="text-blue-600 hover:text-blue-800">{{ $crumb['name'] }}</a>
                        <span class="text-gray-400 mx-2">/</span>
                    </li>
                @endif
            @endforeach
        </ol>
    </div>
</nav>

{{-- Header --}}
<section class="bg-white border-b border-gray-200 py-8">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto text-center">
            <h1 class="text-4xl font-bold text-gray-800 mb-4">
                جميع المتاجر في {{ $city->name }}
            </h1>
            <p class="text-xl text-gray-600 mb-6">
                اكتشف {{ number_format($shops->total()) }} متجر معتمد في {{ $city->name }}
            </p>
            
            {{-- Search and Filters --}}
            <form method="GET" class="max-w-2xl mx-auto mb-8">
                <div class="flex flex-col md:flex-row gap-4">
                    <div class="flex-1">
                        <input 
                            type="text" 
                            name="q" 
                            value="{{ $filters['search'] }}"
                            placeholder="ابحث في المتاجر..."
                            class="w-full py-3 px-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                    </div>
                    
                    <div class="md:w-48">
                        <select 
                            name="category" 
                            class="w-full py-3 px-4 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                        >
                            <option value="">جميع الفئات</option>
                            @foreach($categories as $category)
                                <option 
                                    value="{{ $category->id }}" 
                                    {{ $filters['category_id'] == $category->id ? 'selected' : '' }}
                                >
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <button 
                        type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200"
                    >
                        بحث
                    </button>
                </div>
            </form>
        </div>
    </div>
</section>

{{-- Shops Grid --}}
<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4">
        @if($shops->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                @foreach($shops as $shop)
                    <div class="bg-white rounded-xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden hover:-translate-y-2">
                        {{-- Shop Image --}}
                        <div class="relative h-48 bg-gradient-to-br from-blue-500 to-purple-600">
                            @php
                                $images = $shop->images_array;
                                $hasImages = is_array($images) && count($images) > 0;
                            @endphp
                            @if($hasImages)
                                <img 
                                    src="{{ $images[0] }}" 
                                    alt="{{ $shop->name }}"
                                    class="w-full h-full object-cover"
                                    loading="lazy"
                                >
                            @endif
                            
                            <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                                <div class="text-white text-center">
                                    <h3 class="text-lg font-bold mb-2">{{ $shop->name }}</h3>
                                    @if($shop->rating > 0)
                                        <div class="flex items-center justify-center text-yellow-400">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= floor($shop->rating))
                                                    ⭐
                                                @endif
                                            @endfor
                                            <span class="text-white mr-2">({{ $shop->review_count }})</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            
                            @if($shop->is_featured)
                                <div class="absolute top-4 right-4 bg-yellow-500 text-gray-800 px-3 py-1 rounded-full text-sm font-bold">
                                    مميز
                                </div>
                            @endif
                        </div>
                        
                        {{-- Shop Info --}}
                        <div class="p-6">
                            <p class="text-gray-600 mb-4 line-clamp-2">{{ $shop->description }}</p>
                            
                            <div class="flex items-center text-gray-500 text-sm mb-4">
                                <span class="ml-2">📍</span>
                                <span class="truncate">{{ $shop->address }}</span>
                            </div>
                            
                            <div class="flex items-center justify-between">
                                <span class="text-blue-600 font-medium text-sm">{{ $shop->category->name }}</span>
                                <a 
                                    href="{{ route('shop.show', ['slug' => $shop->slug]) }}" 
                                    class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm transition-colors duration-200"
                                >
                                    عرض المتجر
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            
            {{-- Pagination --}}
            <div class="mt-12">
                {{ $shops->links() }}
            </div>
        @else
            {{-- No Shops Found --}}
            <div class="text-center py-16">
                <div class="text-6xl mb-6">🏪</div>
                <h3 class="text-2xl font-bold text-gray-800 mb-4">لم نجد متاجر مطابقة</h3>
                <p class="text-gray-600 mb-8">جرب تغيير معايير البحث أو تصفح جميع المتاجر</p>
                
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <a 
                        href="{{ route('city.shops.index', $city->slug) }}" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200"
                    >
                        عرض جميع المتاجر
                    </a>
                    
                    <a 
                        href="{{ route('city.show', $city->slug) }}" 
                        class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg font-medium transition-colors duration-200"
                    >
                        العودة لصفحة المدينة
                    </a>
                </div>
            </div>
        @endif
    </div>
</section>

{{-- Quick Links --}}
<section class="py-12 bg-white border-t border-gray-200">
    <div class="container mx-auto px-4">
        <div class="max-w-4xl mx-auto text-center">
            <h3 class="text-2xl font-bold text-gray-800 mb-8">روابط سريعة</h3>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <a 
                    href="{{ route('city.show', $city->slug) }}" 
                    class="bg-blue-50 hover:bg-blue-100 text-blue-600 p-4 rounded-lg text-center transition-colors duration-200"
                >
                    <div class="text-2xl mb-2">🏙️</div>
                    <div class="font-medium">صفحة المدينة</div>
                </a>
                
                <a 
                    href="{{ route('city.shops.featured', $city->slug) }}" 
                    class="bg-yellow-50 hover:bg-yellow-100 text-yellow-600 p-4 rounded-lg text-center transition-colors duration-200"
                >
                    <div class="text-2xl mb-2">⭐</div>
                    <div class="font-medium">المتاجر المميزة</div>
                </a>
                
                <a 
                    href="{{ route('city.categories.index', $city->slug) }}" 
                    class="bg-green-50 hover:bg-green-100 text-green-600 p-4 rounded-lg text-center transition-colors duration-200"
                >
                    <div class="text-2xl mb-2">📂</div>
                    <div class="font-medium">تصفح الفئات</div>
                </a>
                
                <a 
                    href="{{ route('cities.index') }}" 
                    class="bg-purple-50 hover:bg-purple-100 text-purple-600 p-4 rounded-lg text-center transition-colors duration-200"
                >
                    <div class="text-2xl mb-2">🗺️</div>
                    <div class="font-medium">مدن أخرى</div>
                </a>
            </div>
        </div>
    </div>
</section>
@endsection