@extends('layouts.app')

@section('title', $seoData['title'] ?? 'المتاجر المميزة في ' . $city->name)
@section('description', $seoData['description'] ?? 'اكتشف أفضل المتاجر المميزة في ' . $city->name)
@section('keywords', $seoData['keywords'] ?? 'متاجر مميزة, ' . $city->name)
@section('canonical', $seoData['canonical'] ?? url()->current())

@section('content')
    <!-- City Hero Section -->
    <section class="city-hero">
        <div class="container">
            <div class="city-hero-content"> 
                <div class="city-main-info">
                    <h1 class="city-title">⭐ المتاجر المميزة في {{ $city->name }}</h1>
                    <p class="city-description">اكتشف أفضل المتاجر المميزة والموثوقة في {{ $city->name }}</p>
                    
                    <div class="city-stats">
                        <div class="stat-item">
                            <span class="stat-number">{{ $featuredShops->count() }}</span>
                            <span class="stat-label text-white">متجر مميز</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">⭐</span>
                            <span class="stat-label text-white">متاجر موثقة</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">🏙️</span>
                            <span class="stat-label text-white">{{ $city->name }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="city-hero-visual">
                    <div class="city-icon">⭐</div>
                    <div class="floating-elements">
                        <div class="floating-element" style="top: 20%; left: 10%;">🏪</div>
                        <div class="floating-element" style="top: 60%; right: 15%;">✨</div>
                        <div class="floating-element" style="bottom: 30%; left: 20%;">🌟</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- City Content -->
    <section class="city-content">
        <div class="container">
            <div class="city-layout">
                <!-- Filters Sidebar -->
                <aside class="city-sidebar">
                    <div class="filter-card">
                        <h3 class="filter-title">تصفية النتائج</h3>
                        
                        <form method="GET" class="filter-form">
                            <!-- Search -->
                            <div class="filter-group">
                                <label class="filter-label">البحث</label>
                                <div class="search-input-wrapper">
                                    <input type="text" name="q" value="{{ request('q') }}" 
                                           class="filter-input search-input" 
                                           placeholder="ابحث في المتاجر المميزة..." />
                                    <i class="search-icon">🔍</i>
                                </div>
                            </div>

                            <!-- Category Filter -->
                            <div class="filter-group">
                                <label class="filter-label">الفئة</label>
                                <select name="category" class="filter-select">
                                    <option value="">جميع الفئات</option>
                                    @if(isset($city) && $city->categories)
                                        @foreach($city->categories()->where('categories.is_active', true)->get() as $category)
                                            <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                                {{ $category->icon }} {{ $category->name }}
                                            </option>
                                        @endforeach
                                    @endif
                                </select>
                            </div>

                            <!-- Rating Filter -->
                            <div class="filter-group">
                                <label class="filter-label">التقييم</label>
                                <select name="rating" class="filter-select">
                                    <option value="">جميع التقييمات</option>
                                    <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>⭐⭐⭐⭐⭐</option>
                                    <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>⭐⭐⭐⭐ فأكثر</option>
                                    <option value="3" {{ request('rating') == '3' ? 'selected' : '' }}>⭐⭐⭐ فأكثر</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary w-100">تطبيق التصفية</button>
                        </form>
                    </div>

                    <!-- Quick Links -->
                    <div class="filter-card" style="margin-top: 20px;">
                        <h3 class="filter-title">روابط سريعة</h3>
                        <div class="quick-links">
                            <a href="{{ route('city.landing', $city->slug) }}" class="quick-link">
                                🏙️ جميع المتاجر
                            </a>
                            <a href="{{ route('city.categories.index', $city->slug) }}" class="quick-link">
                                📂 تصفح الفئات
                            </a>
                            <a href="{{ route('cities.index') }}" class="quick-link">
                                🗺️ مدن أخرى
                            </a>
                        </div>
                    </div>

                    <!-- Featured Badge Info -->
                    <div class="filter-card" style="margin-top: 20px; background: linear-gradient(135deg, #FFD700 0%, #FFA500 100%); color: white;">
                        <h3 class="filter-title" style="color: white;">ما هي المتاجر المميزة؟</h3>
                        <p style="font-size: 14px; line-height: 1.6; margin-top: 10px;">
                            ⭐ متاجر موثقة ومعتمدة<br>
                            ✅ تقييمات عالية من العملاء<br>
                            🏆 خدمة عملاء ممتازة<br>
                            💎 جودة منتجات مضمونة
                        </p>
                    </div>
                </aside>

                <!-- Main Content -->
                <main class="city-main">
                    <!-- Results Header -->
                    <div class="results-header">
                        <div class="results-info">
                            <h2 class="results-title">
                                ⭐ المتاجر المميزة
                            </h2>
                            <p class="results-count">
                                عرض {{ $featuredShops->count() }} متجر مميز
                            </p>
                        </div>
                        
                        <div class="view-options">
                            <button class="view-btn active" data-view="grid" title="عرض شبكي">
                                <i class="icon">⊞</i>
                            </button>
                            <button class="view-btn" data-view="list" title="عرض قائمة">
                                <i class="icon">☰</i>
                            </button>
                        </div>
                    </div>

                    <!-- Shops Grid -->
                    @if($featuredShops->count())
                        <div class="shops-grid" id="shops-container">
                            @foreach($featuredShops as $shop)
                                <x-shop-card :shop="$shop" :loop="$loop" :city="$city" />
                            @endforeach
                        </div>
                    @else
                        <div class="no-results">
                            <div class="no-results-icon">⭐</div>
                            <h3>لا توجد متاجر مميزة</h3>
                            <p>لا توجد متاجر مميزة متاحة حالياً في {{ $city->name }}.</p>
                            <a href="{{ route('city.landing', $city->slug) }}" class="btn btn-primary">
                                عرض جميع المتاجر
                            </a>
                        </div>
                    @endif
                </main>
            </div>
        </div>
    </section>
@endsection
