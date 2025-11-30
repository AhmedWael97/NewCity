@extends('layouts.app')

@section('title', $seoData['title'] ?? 'اكتشف المتاجر')
@section('description', $seoData['description'] ?? 'اكتشف أفضل المتاجر')
@section('keywords', $seoData['keywords'] ?? 'متاجر, مصر')
@section('og:image', $seoData['og_image'] ?? asset('images/og-discover-cities.jpg'))
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

{{-- City Hero Section --}}
<section class="relative bg-gradient-to-br from-blue-600 via-purple-600 to-blue-800 text-white">
    <div class="absolute inset-0 bg-black opacity-30"></div>
    
    {{-- City Background Image --}}
    @if($city->image)
        <div class="absolute inset-0">
            <img 
                src="{{ $city->image }}" 
                alt="{{ $city->name }}"
                class="w-full h-full object-cover"
            >
            <div class="absolute inset-0 bg-gradient-to-r from-blue-900 via-purple-900 to-transparent opacity-80"></div>
        </div>
    @endif
    
    <div class="relative container mx-auto px-4 py-20">
        <div class="max-w-4xl">
            <div class="mb-6 inline-flex items-center bg-white bg-opacity-20 rounded-full px-6 py-3 backdrop-blur-sm">
                <span class="text-2xl ml-3">📍</span>
                <span class="text-lg font-medium">{{ $city->name }}</span>
                @if($city->governorate)
                    <span class="text-blue-200 text-sm mr-3">• {{ $city->governorate }}</span>
                @endif
            </div>
            
            <h1 class="text-5xl md:text-6xl font-bold mb-6">
                اكتشف أفضل المتاجر في<br>
                <span class="text-yellow-400">{{ $city->name }}</span>
            </h1>
            
            <p class="text-xl md:text-2xl mb-8 text-blue-100 max-w-3xl">
                {{ number_format($stats['total_shops']) }} متجر معتمد 
                • {{ number_format($stats['total_products']) }} منتج متاح
                • تقييم {{ number_format($stats['average_rating'], 1) }} ⭐
            </p>
            
            {{-- Quick Actions --}}
            <div class="flex flex-wrap gap-4">
                <a 
                    href="{{ route('city.shops.index', $city->slug) }}" 
                    class="bg-yellow-500 hover:bg-yellow-600 text-gray-800 font-bold px-8 py-4 rounded-xl text-lg transition-colors duration-200"
                >
                    🏪 تصفح جميع المتاجر
                </a>
                
                <a 
                    href="{{ route('marketplace.index') }}" 
                    class="bg-green-500 hover:bg-green-600 text-white font-bold px-8 py-4 rounded-xl text-lg transition-colors duration-200"
                >
                    🛒 السوق المفتوح
                </a>
                
                @if($featuredShops->count() > 0)
                    <a 
                        href="{{ route('city.shops.featured', $city->slug) }}" 
                        class="bg-white bg-opacity-20 hover:bg-opacity-30 px-8 py-4 rounded-xl text-lg font-medium transition-all duration-200 backdrop-blur-sm"
                    >
                        ⭐ المتاجر المميزة
                    </a>
                @endif
                
                <a 
                    href="{{ route('city.categories.index', $city->slug) }}" 
                    class="bg-white bg-opacity-20 hover:bg-opacity-30 px-8 py-4 rounded-xl text-lg font-medium transition-all duration-200 backdrop-blur-sm"
                >
                    📂 تصفح الفئات
                </a>
            </div>
        </div>
    </div>
    
    {{-- Statistics Cards --}}
    <div class="relative -mt-10">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4 max-w-4xl mx-auto">
                <div class="bg-white rounded-xl p-6 text-center shadow-xl">
                    <div class="text-3xl font-bold text-blue-600">{{ number_format($stats['total_shops']) }}</div>
                    <div class="text-gray-600 text-sm">متجر معتمد</div>
                </div>
                
                <div class="bg-white rounded-xl p-6 text-center shadow-xl">
                    <div class="text-3xl font-bold text-green-600">{{ number_format($stats['total_products']) }}</div>
                    <div class="text-gray-600 text-sm">منتج متاح</div>
                </div>
                
                <div class="bg-white rounded-xl p-6 text-center shadow-xl">
                    <div class="text-3xl font-bold text-purple-600">{{ number_format($stats['total_categories']) }}</div>
                    <div class="text-gray-600 text-sm">فئة متنوعة</div>
                </div>
                
                <div class="bg-white rounded-xl p-6 text-center shadow-xl">
                    <div class="text-3xl font-bold text-orange-600">{{ number_format($stats['average_rating'], 1) }}</div>
                    <div class="text-gray-600 text-sm">متوسط التقييم</div>
                </div>
            </div>
        </div>
    </div>
</section>

{{-- Search Section --}}
<section class="py-8 bg-white border-b border-gray-200">
    <div class="container mx-auto px-4">
        <form action="{{ route('city.search', $city->slug) }}" method="GET" class="max-w-2xl mx-auto">
            <div class="relative">
                <input 
                    type="text" 
                    name="q" 
                    placeholder="ابحث في متاجر {{ $city->name }}..."
                    class="w-full py-4 px-6 pr-16 text-lg border border-gray-300 rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-200 focus:border-blue-500"
                    autocomplete="off"
                >
                <button 
                    type="submit" 
                    class="absolute right-2 top-1/2 transform -translate-y-1/2 bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 px-6 rounded-xl transition-colors duration-200"
                >
                    بحث
                </button>
            </div>
        </form>
    </div>
</section>

{{-- Popular Categories --}}
@if($popularCategories->count() > 0)
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">الفئات الشائعة في {{ $city->name }}</h2>
            <p class="text-xl text-gray-600">اكتشف أنواع المتاجر والخدمات المتاحة في مدينتك</p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-6">
            @foreach($popularCategories as $category)
                <a 
                    href="{{ route('city.category.shops', [$city->slug, $category->slug]) }}" 
                    class="group bg-white rounded-xl p-6 text-center shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2"
                >
                    @if($category->icon)
                        <div class="text-4xl mb-4">{{ $category->icon }}</div>
                    @else
                        <div class="w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-2xl font-bold mx-auto mb-4">
                            {{ mb_substr($category->name, 0, 1) }}
                        </div>
                    @endif
                    
                    <h3 class="font-bold text-gray-800 mb-2 group-hover:text-blue-600 transition-colors">
                        {{ $category->name }}
                    </h3>
                    <p class="text-sm text-gray-500">{{ number_format($category->shops_count) }} متجر</p>
                </a>
            @endforeach
        </div>
        
        <div class="text-center mt-12">
            <a 
                href="{{ route('city.categories.index', $city->slug) }}" 
                class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-xl text-lg font-medium transition-colors duration-200"
            >
                عرض جميع الفئات
            </a>
        </div>
    </div>
</section>
@endif

{{-- Featured Shops --}}
@if($featuredShops->count() > 0)
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-4xl font-bold text-gray-800 mb-4">المتاجر المميزة في {{ $city->name }}</h2>
            <p class="text-xl text-gray-600">اكتشف أفضل المتاجر المعتمدة والموثوقة</p>
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @foreach($featuredShops as $shop)
                <div class="group bg-white rounded-xl shadow-lg hover:shadow-2xl transition-all duration-300 overflow-hidden hover:-translate-y-2">
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
                                <h3 class="text-xl font-bold mb-2">{{ $shop->name }}</h3>
                                <div class="flex items-center justify-center text-yellow-400">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($shop->rating))
                                            ⭐
                                        @endif
                                    @endfor
                                    <span class="text-white mr-2">({{ $shop->review_count }})</span>
                                </div>
                            </div>
                        </div>
                        
                        <div class="absolute top-4 right-4 bg-yellow-500 text-gray-800 px-3 py-1 rounded-full text-sm font-bold">
                            مميز
                        </div>
                    </div>
                    
                    {{-- Shop Info --}}
                    <div class="p-6">
                        <p class="text-gray-600 mb-4 line-clamp-2">{{ $shop->description }}</p>
                        
                        <div class="flex items-center text-gray-500 text-sm mb-4">
                            <span class="ml-2">📍</span>
                            <span>{{ $shop->address }}</span>
                        </div>
                        
                        <div class="flex items-center justify-between">
                            <span class="text-blue-600 font-medium">{{ $shop->category->name }}</span>
                            <a 
                                href="{{ route('shop.show', ['slug' => $shop->slug]) }}" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg transition-colors duration-200"
                            >
                                عرض المتجر
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <div class="text-center mt-12">
            <a 
                href="{{ route('city.shops.featured', $city->slug) }}" 
                class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-xl text-lg font-medium transition-colors duration-200"
            >
                عرض جميع المتاجر المميزة
            </a>
        </div>
    </div>
</section>
@endif

{{-- Nearby Cities --}}
@if($nearbyCities->count() > 0)
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12">
            <h2 class="text-3xl font-bold text-gray-800 mb-4">مدن قريبة من {{ $city->name }}</h2>
            <p class="text-lg text-gray-600">اكتشف المتاجر في المدن المجاورة</p>
        </div>
        
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-5 gap-6">
            @foreach($nearbyCities as $nearbyCity)
                <a 
                    href="{{ route('city.show', $nearbyCity->slug) }}" 
                    class="group bg-white rounded-xl p-6 text-center shadow-lg hover:shadow-xl transition-all duration-300 hover:-translate-y-2"
                >
                    <div class="text-3xl mb-3">🏙️</div>
                    <h3 class="font-bold text-gray-800 group-hover:text-blue-600 transition-colors">
                        {{ $nearbyCity->name }}
                    </h3>
                    @if(isset($nearbyCity->distance))
                        <p class="text-sm text-gray-500 mt-2">{{ number_format($nearbyCity->distance, 0) }} كم</p>
                    @endif
                </a>
            @endforeach
        </div>
    </div>
</section>
@endif
@endsection

@push('scripts')
<script>
// Track city page views
if (typeof gtag !== 'undefined') {
    gtag('event', 'page_view', {
        'page_title': '{{ $city->name }} - City Page',
        'page_location': window.location.href,
        'city_name': '{{ $city->name }}',
        'city_id': {{ $city->id }}
    });
}
</script>
@endpush