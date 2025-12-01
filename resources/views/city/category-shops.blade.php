@extends('layouts.app')

@section('title', $seoData['title'] ?? $category->name . ' في ' . $city->name)
@section('description', $seoData['description'] ?? 'اكتشف أفضل متاجر ' . $category->name . ' في ' . $city->name)
@section('keywords', $seoData['keywords'] ?? $category->name . ', ' . $city->name)
@section('canonical', $seoData['canonical'] ?? url()->current())

@section('content')
    <!-- City Hero Section -->
    <section class="city-hero">
        <div class="container">
            <div class="city-hero-content"> 
                <div class="city-main-info">
                    <h1 class="city-title">{{ $category->icon }} {{ $category->name }} في {{ $city->name }}</h1>
                    <p class="city-description">اكتشف أفضل متاجر {{ $category->name }} في {{ $city->name }}</p>
                    
                    <div class="city-stats">
                        <div class="stat-item">
                            <span class="stat-number">{{ $shops->total() }}</span>
                            <span class="stat-label text-white">متجر</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">{{ $category->icon }}</span>
                            <span class="stat-label text-white">{{ $category->name }}</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">🏙️</span>
                            <span class="stat-label text-white">{{ $city->name }}</span>
                        </div>
                    </div>
                </div>
                
                <div class="city-hero-visual">
                    <div class="city-icon">{{ $category->icon }}</div>
                    <div class="floating-elements">
                        <div class="floating-element" style="top: 20%; left: 10%;">🏪</div>
                        <div class="floating-element" style="top: 60%; right: 15%;">⭐</div>
                        <div class="floating-element" style="bottom: 30%; left: 20%;">{{ $category->icon }}</div>
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
                                           placeholder="ابحث في {{ $category->name }}..." />
                                    <i class="search-icon">🔍</i>
                                </div>
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

                            <!-- Verified Filter -->
                            <div class="filter-group">
                                <label class="filter-checkbox">
                                    <input type="checkbox" name="verified" value="1" {{ request('verified') ? 'checked' : '' }}>
                                    <span>متاجر موثقة فقط</span>
                                </label>
                            </div>

                            <!-- Featured Filter -->
                            <div class="filter-group">
                                <label class="filter-checkbox">
                                    <input type="checkbox" name="featured" value="1" {{ request('featured') ? 'checked' : '' }}>
                                    <span>متاجر مميزة فقط</span>
                                </label>
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
                            <a href="{{ route('city.shops.featured', $city->slug) }}" class="quick-link">
                                ⭐ متاجر مميزة
                            </a>
                            <a href="{{ route('city.categories.index', $city->slug) }}" class="quick-link">
                                📂 تصفح الفئات
                            </a>
                        </div>
                    </div>
                </aside>

                <!-- Main Content -->
                <main class="city-main">
                    <!-- Results Header -->
                    <div class="results-header">
                        <div class="results-info">
                            <h2 class="results-title">
                                {{ $category->icon }} {{ $category->name }}
                            </h2>
                            <p class="results-count">
                                عرض {{ $shops->count() }} من {{ $shops->total() }} متجر
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
                    @if($shops->count())
                        <div class="shops-grid" id="shops-container">
                            @foreach($shops as $shop)
                                <x-shop-card :shop="$shop" :loop="$loop" :city="$city" />
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <x-pagination :paginator="$shops" />
                    @else
                        <div class="no-results">
                            <div class="no-results-icon">🔍</div>
                            <h3>لا توجد نتائج</h3>
                            <p>لم يتم العثور على متاجر في فئة {{ $category->name }}.</p>
                            <a href="{{ route('city.landing', $city->slug) }}" class="btn btn-primary">
                                عرض جميع المتاجر
                            </a>
                        </div>
                    @endif
                </main>
            </div>
        </div>
    </section>

    <script>
    // Get Directions using coordinates or address
    function getDirections(latitude, longitude, address) {
        event.preventDefault();
        event.stopPropagation();
        
        if (latitude && longitude) {
            window.open(`https://www.google.com/maps/dir/?api=1&destination=${latitude},${longitude}`, '_blank');
        } else if (address) {
            const encodedAddress = encodeURIComponent(address);
            window.open(`https://www.google.com/maps/search/${encodedAddress}`, '_blank');
        } else {
            alert('عذراً، الموقع غير متوفر');
        }
    }

    async function toggleFavoriteShop(shopId) {
        event.preventDefault();
        event.stopPropagation();
        
        @guest
            alert('يجب تسجيل الدخول لإضافة المتاجر للمفضلة');
            window.location.href = '{{ route("login") }}';
            return;
        @endguest
        
        const btn = event.target.closest('.favorite-btn, .favorite-btn-small');
        const icon = btn ? btn.querySelector('.heart-icon, i') : event.target;
        const isFavorite = icon.textContent.includes('❤️');
        
        if (btn) btn.disabled = true;
        
        try {
            const response = await fetch(`/favorites/shops/${shopId}`, {
                method: isFavorite ? 'DELETE' : 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            const data = await response.json();
            
            if (response.ok && data.success) {
                icon.textContent = isFavorite ? '🤍' : '❤️';
                if (window.showToast) {
                    showToast(data.message, 'success');
                } else {
                    alert(data.message);
                }
            } else {
                if (window.showToast) {
                    showToast(data.message || 'حدث خطأ ما', 'error');
                } else {
                    alert(data.message || 'حدث خطأ ما');
                }
            }
        } catch (error) {
            console.error('Favorite toggle error:', error);
            if (window.showToast) {
                showToast('حدث خطأ في الاتصال', 'error');
            } else {
                alert('حدث خطأ في الاتصال');
            }
        } finally {
            if (btn) btn.disabled = false;
        }
    }
    </script>
@endsection
