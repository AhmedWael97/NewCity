@extends('layouts.app')

@section('content')
    <!-- Shop Hero Section -->
    <section class="shop-hero">
        <div class="container">
            <div class="shop-hero-content">
                <div class="shop-hero-info">
                    <div class="shop-breadcrumb">
                        <a href="{{ url('/') }}">الرئيسية</a>
                        <span>•</span>
                        <a href="{{ route('city.shops', $shop->city->slug ?? '#') }}">{{ $shop->city->name ?? 'المدن' }}</a>
                        <span>•</span>
                        <span>{{ $shop->name }}</span>
                    </div>
                    
                    <div class="shop-main-info">
                        <h1 class="shop-title">{{ $shop->name }}</h1>
                        <div class="shop-meta">
                            <span class="shop-category">
                                <i class="icon">🏪</i>
                                {{ $shop->category->name ?? 'عام' }}
                            </span>
                            <span class="shop-location">
                                <i class="icon">📍</i>
                                {{ $shop->city->name ?? '' }}
                            </span>
                            <span class="shop-status {{ $shop->is_open_now ?? true ? 'open' : 'closed' }}">
                                <i class="icon">🕒</i>
                                {{ $shop->is_open_now ?? true ? 'مفتوح الآن' : 'مغلق حالياً' }}
                            </span>
                        </div>
                        
                        <div class="shop-rating">
                            <x-rating.display 
                                :rating="$shop->averageRating()" 
                                :show-text="true"
                                size="md"
                                class="hero-rating"
                            />
                            <span class="review-count">({{ $shop->totalRatings() }} تقييم)</span>
                        </div>
                    </div>
                </div>
                
                <div class="shop-hero-image">
                    @if($shop->images && is_array($shop->images) && count($shop->images) > 0)
                        <img src="{{ $shop->images[0] }}" 
                             alt="{{ $shop->name }}" 
                             class="hero-img"
                             onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                        <div class="hero-placeholder" style="display: none;">
                            <div class="placeholder-icon">
                                @switch($shop->category->name ?? 'عام')
                                    @case('مطاعم')
                                        �️
                                        @break
                                    @case('ملابس')
                                        👕
                                        @break
                                    @case('إلكترونيات')
                                        📱
                                        @break
                                    @case('صيدليات')
                                        💊
                                        @break
                                    @case('سوبر ماركت')
                                        🛒
                                        @break
                                    @case('مجوهرات')
                                        💎
                                        @break
                                    @default
                                        �🏪
                                @endswitch
                            </div>
                            <span>{{ $shop->name }}</span>
                            <p class="placeholder-subtitle">فشل في تحميل الصورة</p>
                        </div>
                    @else
                        <div class="hero-placeholder">
                            <div class="placeholder-icon">
                                @switch($shop->category->name ?? 'عام')
                                    @case('مطاعم')
                                        �️
                                        @break
                                    @case('ملابس')
                                        👕
                                        @break
                                    @case('إلكترونيات')
                                        📱
                                        @break
                                    @case('صيدليات')
                                        💊
                                        @break
                                    @case('سوبر ماركت')
                                        🛒
                                        @break
                                    @case('مجوهرات')
                                        💎
                                        @break
                                    @default
                                        �🏪
                                @endswitch
                            </div>
                            <span>{{ $shop->name }}</span>
                            <p class="placeholder-subtitle">صورة غير متوفرة</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Banner Advertisement after shop hero --}}
    <section class="py-3 bg-light">
        <div class="container">
            <x-ad-display type="banner" placement="shop_page" :city-id="$shop->city_id ?? null" />
        </div>
    </section>

    <!-- Shop Content -->
    <section class="shop-content">
        <div class="container">
            <div class="shop-layout">
                <!-- Main Content -->
                <main class="shop-main">
                    <!-- Quick Actions -->
                    <div class="shop-actions">
                        <a href="tel:{{ $shop->phone ?? '' }}" class="action-btn call-btn">
                            <i class="icon">📞</i>
                            <span>اتصال</span>
                        </a>
                        @if($shop->latitude && $shop->longitude)
                            <a href="https://www.google.com/maps/dir/?api=1&destination={{ $shop->latitude }},{{ $shop->longitude }}" 
                               target="_blank" 
                               class="action-btn directions-btn">
                                <i class="icon">🧭</i>
                                <span>الاتجاهات</span>
                            </a>
                        @else
                            <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($shop->address) }}" 
                               target="_blank" 
                               class="action-btn directions-btn">
                                <i class="icon">🧭</i>
                                <span>الاتجاهات</span>
                            </a>
                        @endif
                        <button class="action-btn share-btn" onclick="shareShop()">
                            <i class="icon">📤</i>
                            <span>مشاركة</span>
                        </button>
                        <button class="action-btn favorite-btn" onclick="toggleFavorite()">
                            <i class="icon">❤️</i>
                            <span>مفضلة</span>
                        </button>
                    </div>

                    <!-- Shop Description -->
                    <div class="shop-section">
                        <h3 class="section-title">نبذة عن المتجر</h3>
                        <div class="section-content">
                            <p class="shop-description">
                                {{ $shop->description ?? 'متجر متميز يقدم أفضل المنتجات والخدمات لعملائه الكرام. نحرص على تقديم تجربة تسوق ممتازة وخدمة عملاء استثنائية.' }}
                            </p>
                        </div>
                    </div>

                    <!-- Products and Services Section -->
                    @if(isset($products) && $products->count() > 0 || isset($services) && $services->count() > 0)
                    <div class="shop-section shop-products-services">
                        <div class="products-services-header">
                            <h3 class="section-title">المنتجات والخدمات</h3>
                            <div class="tab-navigation">
                                @if(isset($products) && $products->count() > 0)
                                    <button class="tab-btn active" data-tab="products">
                                        <span class="tab-icon">📦</span>
                                        <span class="tab-text">المنتجات ({{ $products->count() }})</span>
                                    </button>
                                @endif
                                @if(isset($services) && $services->count() > 0)
                                    <button class="tab-btn {{ !isset($products) || $products->count() == 0 ? 'active' : '' }}" data-tab="services">
                                        <span class="tab-icon">🔧</span>
                                        <span class="tab-text">الخدمات ({{ $services->count() }})</span>
                                    </button>
                                @endif
                            </div>
                        </div>

                        <!-- Products Tab Content -->
                        @if(isset($products) && $products->count() > 0)
                        <div class="tab-content {{ isset($products) && $products->count() > 0 ? 'active' : '' }}" id="products-tab">
                            <div class="products-filters">
                                <div class="filters-header">
                                    <h5 class="filters-title">
                                        <i class="filter-icon">🔍</i>
                                        البحث والفرز
                                    </h5>
                                    <button class="clear-filters-btn" onclick="clearProductFilters()">
                                        <i class="clear-icon">✖️</i>
                                        مسح المرشحات
                                    </button>
                                </div>
                                
                                <div class="filter-controls">
                                    <div class="search-box">
                                        <div class="search-input-wrapper">
                                            <i class="search-icon">🔍</i>
                                            <input type="text" 
                                                   id="product-search" 
                                                   class="search-input" 
                                                   placeholder="ابحث في المنتجات..."
                                                   autocomplete="off">
                                            <button class="search-clear" onclick="clearProductSearch()" style="display: none;">
                                                <i class="clear-icon">✖️</i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="filter-selects">
                                        <div class="select-wrapper">
                                            <label for="product-category-filter" class="select-label">الفئة</label>
                                            <select class="filter-select" id="product-category-filter">
                                                <option value="">جميع الفئات</option>
                                                @foreach($products->pluck('category')->unique()->filter() as $category)
                                                    <option value="{{ $category }}">{{ $category }}</option>
                                                @endforeach
                                            </select>
                                            <i class="select-arrow">▼</i>
                                        </div>
                                        
                                        <div class="select-wrapper">
                                            <label for="product-sort" class="select-label">الترتيب</label>
                                            <select class="filter-select" id="product-sort">
                                                <option value="name">الاسم (أ-ي)</option>
                                                <option value="name_desc">الاسم (ي-أ)</option>
                                                <option value="price_asc">السعر (من الأقل)</option>
                                                <option value="price_desc">السعر (من الأعلى)</option>
                                                <option value="featured">المميزة أولاً</option>
                                                <option value="newest">الأحدث</option>
                                            </select>
                                            <i class="select-arrow">▼</i>
                                        </div>
                                        
                                        <div class="select-wrapper">
                                            <label for="product-price-range" class="select-label">نطاق السعر</label>
                                            <select class="filter-select" id="product-price-range">
                                                <option value="">جميع الأسعار</option>
                                                <option value="0-50">أقل من 50 ج.م</option>
                                                <option value="50-100">50 - 100 ج.م</option>
                                                <option value="100-200">100 - 200 ج.م</option>
                                                <option value="200-500">200 - 500 ج.م</option>
                                                <option value="500+">أكثر من 500 ج.م</option>
                                            </select>
                                            <i class="select-arrow">▼</i>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="filter-results">
                                    <span class="results-count" id="products-count">عرض جميع المنتجات ({{ $products->count() }})</span>
                                    <div class="view-toggle">
                                        <button class="view-btn active" data-view="grid" title="عرض شبكي">
                                            <i class="view-icon">⊞</i>
                                        </button>
                                        <button class="view-btn" data-view="list" title="عرض قائمة">
                                            <i class="view-icon">☰</i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="swiper all-products-swiper">
                                <div class="swiper-wrapper">
                                    @foreach($products as $product)
                                        <div class="swiper-slide">
                                            <x-product-card :product="$product" size="small" :featured="$product->is_featured ?? false" />
                                        </div>
                                    @endforeach
                                </div>
                                <div class="swiper-pagination all-products-pagination"></div>
                            </div>
                            
                            @if($products->count() >= 12)
                            <div class="load-more-section">
                                <button class="btn btn-outline load-more-btn" data-type="products">
                                    عرض المزيد من المنتجات
                                </button>
                            </div>
                            @endif
                        </div>
                        @endif

                        <!-- Services Tab Content -->
                        @if(isset($services) && $services->count() > 0)
                        <div class="tab-content {{ !isset($products) || $products->count() == 0 ? 'active' : '' }}" id="services-tab">
                            <div class="services-filters">
                                <div class="filters-header">
                                    <h5 class="filters-title">
                                        <i class="filter-icon">🔍</i>
                                        البحث والفرز
                                    </h5>
                                    <button class="clear-filters-btn" onclick="clearServiceFilters()">
                                        <i class="clear-icon">✖️</i>
                                        مسح المرشحات
                                    </button>
                                </div>
                                
                                <div class="filter-controls">
                                    <div class="search-box">
                                        <div class="search-input-wrapper">
                                            <i class="search-icon">🔍</i>
                                            <input type="text" 
                                                   id="service-search" 
                                                   class="search-input" 
                                                   placeholder="ابحث في الخدمات..."
                                                   autocomplete="off">
                                            <button class="search-clear" onclick="clearServiceSearch()" style="display: none;">
                                                <i class="clear-icon">✖️</i>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <div class="filter-selects">
                                        <div class="select-wrapper">
                                            <label for="service-category-filter" class="select-label">الفئة</label>
                                            <select class="filter-select" id="service-category-filter">
                                                <option value="">جميع الفئات</option>
                                                @foreach($services->pluck('category')->unique()->filter() as $category)
                                                    <option value="{{ $category }}">{{ $category }}</option>
                                                @endforeach
                                            </select>
                                            <i class="select-arrow">▼</i>
                                        </div>
                                        
                                        <div class="select-wrapper">
                                            <label for="service-sort" class="select-label">الترتيب</label>
                                            <select class="filter-select" id="service-sort">
                                                <option value="name">الاسم (أ-ي)</option>
                                                <option value="name_desc">الاسم (ي-أ)</option>
                                                <option value="price_asc">السعر (من الأقل)</option>
                                                <option value="price_desc">السعر (من الأعلى)</option>
                                                <option value="duration_asc">المدة (من الأقصر)</option>
                                                <option value="duration_desc">المدة (من الأطول)</option>
                                                <option value="appointment">يتطلب موعد أولاً</option>
                                                <option value="featured">المميزة أولاً</option>
                                            </select>
                                            <i class="select-arrow">▼</i>
                                        </div>
                                        
                                        <div class="select-wrapper">
                                            <label for="service-price-range" class="select-label">نطاق السعر</label>
                                            <select class="filter-select" id="service-price-range">
                                                <option value="">جميع الأسعار</option>
                                                <option value="0-100">أقل من 100 ج.م</option>
                                                <option value="100-250">100 - 250 ج.م</option>
                                                <option value="250-500">250 - 500 ج.م</option>
                                                <option value="500-1000">500 - 1000 ج.م</option>
                                                <option value="1000+">أكثر من 1000 ج.م</option>
                                            </select>
                                            <i class="select-arrow">▼</i>
                                        </div>
                                        
                                        <div class="select-wrapper">
                                            <label for="service-appointment" class="select-label">نوع الحجز</label>
                                            <select class="filter-select" id="service-appointment">
                                                <option value="">جميع الخدمات</option>
                                                <option value="required">يتطلب موعد</option>
                                                <option value="not_required">لا يتطلب موعد</option>
                                                <option value="instant">فوري</option>
                                            </select>
                                            <i class="select-arrow">▼</i>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="filter-results">
                                    <span class="results-count" id="services-count">عرض جميع الخدمات ({{ $services->count() }})</span>
                                    <div class="view-toggle">
                                        <button class="view-btn active" data-view="grid" title="عرض شبكي">
                                            <i class="view-icon">⊞</i>
                                        </button>
                                        <button class="view-btn" data-view="list" title="عرض قائمة">
                                            <i class="view-icon">☰</i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="swiper all-services-swiper">
                                <div class="swiper-wrapper">
                                    @foreach($services as $service)
                                        <div class="swiper-slide">
                                            <x-service-card :service="$service" size="small" :featured="$service->is_featured ?? false" />
                                        </div>
                                    @endforeach
                                </div>
                                <div class="swiper-pagination all-services-pagination"></div>
                            </div>
                            
                            @if($services->count() >= 12)
                            <div class="load-more-section">
                                <button class="btn btn-outline load-more-btn" data-type="services">
                                    عرض المزيد من الخدمات
                                </button>
                            </div>
                            @endif
                        </div>
                        @endif
                    </div>
                    @endif

                    <!-- Shop Gallery -->
                    @if($shop->images && is_array($shop->images) && count($shop->images) > 1)
                    <div class="shop-section">
                        <h3 class="section-title">معرض الصور</h3>
                        <div class="shop-gallery">
                            @foreach($shop->images as $image)
                                <div class="gallery-item" onclick="openLightbox('{{ $image }}')">
                                    <img src="{{ $image }}" 
                                         alt="{{ $shop->name }}"
                                         onerror="this.parentElement.style.display='none';">
                                    <div class="gallery-overlay">
                                        <i class="icon">🔍</i>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Opening Hours -->
                    <div class="shop-section">
                        <h3 class="section-title">ساعات العمل</h3>
                        <div class="opening-hours">
                            @if(is_array($shop->opening_hours) && !empty($shop->opening_hours))
                                @foreach($shop->opening_hours as $day => $hours)
                                    @php
                                        $dayNames = [
                                            'sunday' => 'الأحد',
                                            'monday' => 'الاثنين',
                                            'tuesday' => 'الثلاثاء',
                                            'wednesday' => 'الأربعاء',
                                            'thursday' => 'الخميس',
                                            'friday' => 'الجمعة',
                                            'saturday' => 'السبت'
                                        ];
                                        $dayName = $dayNames[strtolower($day)] ?? ucfirst($day);
                                    @endphp
                                    <div class="hours-row">
                                        <span class="day">{{ $dayName }}</span>
                                        <span class="hours">
                                            @if(is_array($hours) && isset($hours['open']) && isset($hours['close']))
                                                {{ $hours['open'] }} - {{ $hours['close'] }}
                                            @elseif(is_string($hours))
                                                {{ $hours }}
                                            @else
                                                مغلق
                                            @endif
                                        </span>
                                    </div>
                                @endforeach
                            @elseif(is_string($shop->opening_hours))
                                <div class="hours-row">
                                    <span class="day">ساعات العمل</span>
                                    <span class="hours">{{ $shop->opening_hours }}</span>
                                </div>
                            @else
                                <div class="hours-row">
                                    <span class="day">ساعات العمل</span>
                                    <span class="hours">يرجى الاتصال للاستفسار</span>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Reviews Section -->
                    <div class="shop-section">
                        <h3 class="section-title">آراء العملاء</h3>
                        
                        <!-- Rating Summary Component -->
                        <x-rating.summary 
                            :shop="$shop" 
                            :show-breakdown="true" 
                            :show-recent-reviews="true" 
                            :max-reviews="5" 
                        />
                        
                        <!-- Rating Form Component -->
                        <x-rating.form :shop="$shop" :user-rating="$userRating" />
                    </div>
                </main>

                <!-- Sidebar -->
                <aside class="shop-sidebar">
                    {{-- Sidebar Ads --}}
                    <x-ad-display type="sidebar" placement="shop_page" :city-id="$shop->city_id ?? null" />
                    
                    <!-- Contact Info -->
                    <div class="sidebar-card">
                        <h4 class="card-title">معلومات الاتصال</h4>
                        <div class="contact-info">
                            <div class="contact-item">
                                <i class="icon">📍</i>
                                <div>
                                    <strong>العنوان</strong>
                                    <p>{{ $shop->address ?? 'غير متوفر' }}</p>
                                </div>
                            </div>
                            <div class="contact-item">
                                <i class="icon">📞</i>
                                <div>
                                    <strong>الهاتف</strong>
                                    <p>{{ $shop->phone ?? 'غير متوفر' }}</p>
                                </div>
                            </div>
                            <div class="contact-item">
                                <i class="icon">🌐</i>
                                <div>
                                    <strong>الموقع الإلكتروني</strong>
                                    <p>{{ $shop->website ?? 'غير متوفر' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Stats -->
                    <div class="sidebar-card">
                        <h4 class="card-title">إحصائيات سريعة</h4>
                        <div class="quick-stats">
                            <div class="stat-item">
                                <div class="stat-icon">👥</div>
                                <div class="stat-info">
                                    <span class="stat-number">1,234</span>
                                    <span class="stat-label">زائر شهرياً</span>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-icon">⭐</div>
                                <div class="stat-info">
                                    <span class="stat-number">4.8</span>
                                    <span class="stat-label">التقييم</span>
                                </div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-icon">📅</div>
                                <div class="stat-info">
                                    <span class="stat-number">{{ $shop->created_at ? $shop->created_at->diffForHumans() : 'غير معروف' }}</span>
                                    <span class="stat-label">منذ التسجيل</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Map Placeholder -->
                    <div class="sidebar-card">
                        <h4 class="card-title">الموقع على الخريطة</h4>
                        <div class="map-placeholder" style="height: 250px; background: linear-gradient(135deg, #e8f5f3, #d1ebe7); border-radius: 12px; display: flex; flex-direction: column; align-items: center; justify-content: center; text-align: center; padding: 30px;">
                            <div class="map-icon" style="font-size: 64px; margin-bottom: 20px; animation: bounce 2s infinite;">🗺️</div>
                            <h5 style="color: #016B61; margin-bottom: 10px; font-weight: bold;">{{ $shop->name }}</h5>
                            <p style="color: #666; margin-bottom: 20px; font-size: 14px;">
                                📍 {{ $shop->address ?? $shop->city->name ?? 'الموقع' }}
                            </p>
                            @if($shop->latitude && $shop->longitude)
                                <a href="https://www.google.com/maps/dir/?api=1&destination={{ $shop->latitude }},{{ $shop->longitude }}" 
                                   target="_blank"
                                   class="btn btn-primary"
                                   style="padding: 12px 30px; font-size: 14px; font-weight: bold; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px; margin-bottom: 15px;">
                                     احصل على الاتجاهات
                                </a>
                            @elseif($shop->address)
                                <a href="https://www.google.com/maps/search/?api=1&query={{ urlencode($shop->address) }}" 
                                   target="_blank"
                                   class="btn btn-primary"
                                   style="padding: 12px 30px; font-size: 15px; font-weight: bold; border-radius: 8px; text-decoration: none; display: inline-flex; align-items: center; gap: 8px;">
                                    🔍 ابحث عن الموقع
                                </a>
                            @endif
                        </div>
                    </div>

                    <!-- Similar Shops -->
                    @if(isset($similarShops) && $similarShops->count() > 0)
                    <div class="sidebar-card">
                        <h4 class="card-title">متاجر مشابهة</h4>
                        <div class="similar-shops">
                            @foreach($similarShops as $similarShop)
                                <a href="{{ route('shop.show', $similarShop->slug) }}" class="similar-shop">
                                    <div class="shop-thumb">
                                        @if($similarShop->images && is_array($similarShop->images) && count($similarShop->images) > 0)
                                            <img src="{{ $similarShop->images[0] }}" 
                                                 alt="{{ $similarShop->name }}"
                                                 style="width: 100%; height: 100%; object-fit: cover; border-radius: 8px;"
                                                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
                                            <span style="display: none;">{{ $similarShop->category->icon ?? '🏪' }}</span>
                                        @else
                                            {{ $similarShop->category->icon ?? '🏪' }}
                                        @endif
                                    </div>
                                    <div class="shop-info">
                                        <h5>{{ $similarShop->name }}</h5>
                                        <p>
                                            @if($similarShop->ratings_avg_rating)
                                                ⭐ {{ number_format($similarShop->ratings_avg_rating, 1) }}
                                            @else
                                                ⭐ جديد
                                            @endif
                                            • {{ $similarShop->city->name ?? '' }}
                                        </p>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </aside>
            </div>
        </div>
    </section>

    <!-- Lightbox Modal -->
    <div id="lightbox" class="lightbox" onclick="closeLightbox()">
        <div class="lightbox-content">
            <span class="lightbox-close">&times;</span>
            <img id="lightbox-img" src="" alt="">
        </div>
    </div>
@endsection

@push('scripts')
<!-- Swiper JS -->
<script src="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.js"></script>
<script>
    function shareShop() {
        const shopName = '{{ $shop->name }}';
        const cityName = '{{ $shop->city->name ?? "" }}';
        const shopUrl = window.location.href;
        const message = `اكتشف ${shopName} في ${cityName}\n\n${shopUrl}`;
        
        if (navigator.share) {
            navigator.share({
                title: shopName,
                text: `اكتشف ${shopName} في ${cityName}`,
                url: shopUrl
            }).catch(() => {
                // If share fails, copy to clipboard
                copyToClipboard(message);
            });
        } else {
            // Fallback - copy message and link to clipboard
            copyToClipboard(message);
        }
    }

    function copyToClipboard(text) {
        if (navigator.clipboard && navigator.clipboard.writeText) {
            navigator.clipboard.writeText(text).then(() => {
                showCopyNotification();
            }).catch(() => {
                // Fallback for older browsers
                fallbackCopyToClipboard(text);
            });
        } else {
            fallbackCopyToClipboard(text);
        }
    }

    function fallbackCopyToClipboard(text) {
        const textArea = document.createElement('textarea');
        textArea.value = text;
        textArea.style.position = 'fixed';
        textArea.style.left = '-999999px';
        document.body.appendChild(textArea);
        textArea.select();
        try {
            document.execCommand('copy');
            showCopyNotification();
        } catch (err) {
            alert('فشل نسخ الرابط');
        }
        document.body.removeChild(textArea);
    }

    function showCopyNotification() {
        const notification = document.createElement('div');
        notification.textContent = '✅ تم نسخ الرسالة والرابط!';
        notification.style.cssText = `
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: linear-gradient(135deg, #10b981, #059669);
            color: white;
            padding: 15px 25px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(16, 185, 129, 0.3);
            z-index: 10000;
            font-weight: bold;
            animation: slideIn 0.3s ease-out;
        `;
        document.body.appendChild(notification);
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => document.body.removeChild(notification), 300);
        }, 3000);
    }

    async function toggleFavorite() {
        // Check if user is authenticated
        @guest
            showToast('يجب تسجيل الدخول لاستخدام هذه الميزة', 'warning');
            setTimeout(() => {
                window.location.href = '{{ route("login") }}';
            }, 1500);
            return;
        @endguest

        const btn = document.querySelector('.favorite-btn');
        const icon = btn.querySelector('.icon');
        const shopId = {{ $shop->id }};
        const isFavorite = icon.textContent === '❤️';
        
        // Disable button while processing
        btn.disabled = true;
        
        try {
            const response = await fetch(`/api/v1/shops/${shopId}/favorite`, {
                method: isFavorite ? 'DELETE' : 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Authorization': 'Bearer ' + (localStorage.getItem('auth_token') || '')
                }
            });
            
            const data = await response.json();
            
            if (response.ok && data.success) {
                // Toggle icon
                icon.textContent = isFavorite ? '🤍' : '❤️';
                showToast(data.message, 'success');
            } else {
                // Handle error
                if (response.status === 401) {
                    showToast('يجب تسجيل الدخول لاستخدام هذه الميزة', 'warning');
                    setTimeout(() => {
                        window.location.href = '{{ route("login") }}';
                    }, 1500);
                } else {
                    showToast(data.message || 'حدث خطأ ما', 'error');
                }
            }
        } catch (error) {
            console.error('Error toggling favorite:', error);
            showToast('حدث خطأ في الاتصال', 'error');
        } finally {
            btn.disabled = false;
        }
    }

    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        const icons = {
            success: '✅',
            error: '❌',
            warning: '⚠️',
            info: 'ℹ️'
        };
        const colors = {
            success: 'linear-gradient(135deg, #10b981, #059669)',
            error: 'linear-gradient(135deg, #ef4444, #dc2626)',
            warning: 'linear-gradient(135deg, #f59e0b, #d97706)',
            info: 'linear-gradient(135deg, #3b82f6, #2563eb)'
        };
        
        toast.innerHTML = `${icons[type]} ${message}`;
        toast.style.cssText = `
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: ${colors[type]};
            color: white;
            padding: 15px 25px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            z-index: 10000;
            font-weight: bold;
            animation: slideIn 0.3s ease-out;
        `;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => document.body.removeChild(toast), 300);
        }, 3000);
    }

    function openLightbox(src) {
        document.getElementById('lightbox').style.display = 'flex';
        document.getElementById('lightbox-img').src = src;
        document.body.style.overflow = 'hidden';
    }

    function closeLightbox() {
        document.getElementById('lightbox').style.display = 'none';
        document.body.style.overflow = 'auto';
    }

    // Close lightbox with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeLightbox();
        }
    });

    // Image error handling for hero and gallery images
    function handleImageError(img) {
        if (img.classList.contains('hero-img')) {
            img.style.display = 'none';
            const placeholder = img.nextElementSibling;
            if (placeholder && placeholder.classList.contains('hero-placeholder')) {
                placeholder.style.display = 'flex';
            }
        } else if (img.closest('.gallery-item')) {
            img.closest('.gallery-item').style.display = 'none';
        }
    }

    // Add error handling to all images
    document.querySelectorAll('.hero-img, .shop-gallery img').forEach(img => {
        img.addEventListener('error', function() {
            handleImageError(this);
        });
        
        // Check if image is already broken
        if (!img.complete || img.naturalWidth === 0) {
            handleImageError(img);
        }
    });

   document.addEventListener('DOMContentLoaded', function() {
        // Initialize Swiper for All Products
        if (document.querySelector('.all-products-swiper')) {
            new Swiper('.all-products-swiper', {
                slidesPerView: 1,
                spaceBetween: 16,
                pagination: {
                    el: '.all-products-pagination',
                    clickable: true,
                    type: 'bullets',
                },
                breakpoints: {
                    768: { 
                        slidesPerView: 2, 
                        spaceBetween: 20 
                    },
                    1024: { 
                        slidesPerView: 3, 
                        spaceBetween: 24 
                    }
                }
            });
        }

        // Initialize Swiper for All Services
        if (document.querySelector('.all-services-swiper')) {
            new Swiper('.all-services-swiper', {
                slidesPerView: 1,
                spaceBetween: 16,
                pagination: {
                    el: '.all-services-pagination',
                    clickable: true,
                    type: 'bullets',
                },
                breakpoints: {
                    768: { 
                        slidesPerView: 2, 
                        spaceBetween: 20 
                    },
                    1024: { 
                        slidesPerView: 3, 
                        spaceBetween: 24 
                    }
                }
            });
        }

        // Tab switching
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const targetTab = this.dataset.tab;
                
                // Remove active class from all tabs
                tabBtns.forEach(b => b.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));
                
                // Add active class to clicked tab
                this.classList.add('active');
                const targetElement = document.getElementById(targetTab + '-tab');
                if (targetElement) {
                    targetElement.classList.add('active');
                }
            });
        });

        // Professional Search and Filter System
        
        // Global filter functions
        function clearProductFilters() {
            document.getElementById('product-search').value = '';
            document.getElementById('product-category-filter').value = '';
            document.getElementById('product-sort').value = 'name';
            document.getElementById('product-price-range').value = '';
            document.querySelector('#product-search + .search-clear').style.display = 'none';
            applyProductFilters();
        }
        
        function clearServiceFilters() {
            document.getElementById('service-search').value = '';
            document.getElementById('service-category-filter').value = '';
            document.getElementById('service-sort').value = 'name';
            document.getElementById('service-price-range').value = '';
            document.getElementById('service-appointment').value = '';
            document.querySelector('#service-search + .search-clear').style.display = 'none';
            applyServiceFilters();
        }
        
        function clearProductSearch() {
            document.getElementById('product-search').value = '';
            document.querySelector('#product-search + .search-clear').style.display = 'none';
            applyProductFilters();
        }
        
        function clearServiceSearch() {
            document.getElementById('service-search').value = '';
            document.querySelector('#service-search + .search-clear').style.display = 'none';
            applyServiceFilters();
        }
        
        // Products filtering and search
        function applyProductFilters() {
            const searchTerm = document.getElementById('product-search').value.toLowerCase().trim();
            const categoryFilter = document.getElementById('product-category-filter').value;
            const sortBy = document.getElementById('product-sort').value;
            const priceRange = document.getElementById('product-price-range').value;
            
            const productSlides = document.querySelectorAll('.all-products-swiper .swiper-slide');
            let visibleCount = 0;
            
            productSlides.forEach(slide => {
                const card = slide.querySelector('.product-card');
                if (!card) return;
                
                const productName = card.querySelector('.product-name')?.textContent.toLowerCase() || '';
                const productCategory = card.dataset.category || '';
                const productPrice = parseFloat(card.dataset.price || 0);
                
                let visible = true;
                
                // Search filter
                if (searchTerm && !productName.includes(searchTerm)) {
                    visible = false;
                }
                
                // Category filter
                if (categoryFilter && productCategory !== categoryFilter) {
                    visible = false;
                }
                
                // Price range filter
                if (priceRange) {
                    const [min, max] = priceRange.split('-');
                    if (max === '+') {
                        if (productPrice < parseFloat(min)) visible = false;
                    } else {
                        if (productPrice < parseFloat(min) || productPrice > parseFloat(max)) {
                            visible = false;
                        }
                    }
                }
                
                slide.style.display = visible ? 'block' : 'none';
                if (visible) visibleCount++;
            });
            
            // Update results count
            document.getElementById('products-count').textContent = `عرض ${visibleCount} من أصل {{ $products->count() }} منتج`;
        }
        
        // Services filtering and search
        function applyServiceFilters() {
            const searchTerm = document.getElementById('service-search').value.toLowerCase().trim();
            const categoryFilter = document.getElementById('service-category-filter').value;
            const sortBy = document.getElementById('service-sort').value;
            const priceRange = document.getElementById('service-price-range').value;
            const appointmentFilter = document.getElementById('service-appointment').value;
            
            const serviceSlides = document.querySelectorAll('.all-services-swiper .swiper-slide');
            let visibleCount = 0;
            
            serviceSlides.forEach(slide => {
                const card = slide.querySelector('.service-card');
                if (!card) return;
                
                const serviceName = card.querySelector('.service-name')?.textContent.toLowerCase() || '';
                const serviceCategory = card.dataset.category || '';
                const servicePrice = parseFloat(card.dataset.price || 0);
                const requiresAppointment = card.dataset.requiresAppointment === 'true';
                
                let visible = true;
                
                // Search filter
                if (searchTerm && !serviceName.includes(searchTerm)) {
                    visible = false;
                }
                
                // Category filter
                if (categoryFilter && serviceCategory !== categoryFilter) {
                    visible = false;
                }
                
                // Price range filter
                if (priceRange) {
                    const [min, max] = priceRange.split('-');
                    if (max === '+') {
                        if (servicePrice < parseFloat(min)) visible = false;
                    } else {
                        if (servicePrice < parseFloat(min) || servicePrice > parseFloat(max)) {
                            visible = false;
                        }
                    }
                }
                
                // Appointment filter
                if (appointmentFilter) {
                    if (appointmentFilter === 'required' && !requiresAppointment) visible = false;
                    if (appointmentFilter === 'not_required' && requiresAppointment) visible = false;
                }
                
                slide.style.display = visible ? 'block' : 'none';
                if (visible) visibleCount++;
            });
            
            // Update results count
            document.getElementById('services-count').textContent = `عرض ${visibleCount} من أصل {{ $services->count() }} خدمة`;
        }
        
        // Initialize search and filter events
        function initializeSearchAndFilters() {
            // Product search input
            const productSearch = document.getElementById('product-search');
            const productSearchClear = document.querySelector('.search-input-wrapper .search-clear');
            
            if (productSearch) {
                productSearch.addEventListener('input', function() {
                    const clearBtn = this.parentElement.querySelector('.search-clear');
                    if (this.value.length > 0) {
                        clearBtn.style.display = 'block';
                    } else {
                        clearBtn.style.display = 'none';
                    }
                    applyProductFilters();
                });
                
                productSearch.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        applyProductFilters();
                    }
                });
            }
            
            // Service search input
            const serviceSearch = document.getElementById('service-search');
            if (serviceSearch) {
                serviceSearch.addEventListener('input', function() {
                    const clearBtn = this.parentElement.querySelector('.search-clear');
                    if (this.value.length > 0) {
                        clearBtn.style.display = 'block';
                    } else {
                        clearBtn.style.display = 'none';
                    }
                    applyServiceFilters();
                });
                
                serviceSearch.addEventListener('keypress', function(e) {
                    if (e.key === 'Enter') {
                        e.preventDefault();
                        applyServiceFilters();
                    }
                });
            }
            
            // Product filter selects
            const productFilters = ['product-category-filter', 'product-sort', 'product-price-range'];
            productFilters.forEach(id => {
                const element = document.getElementById(id);
                if (element) {
                    element.addEventListener('change', applyProductFilters);
                }
            });
            
            // Service filter selects
            const serviceFilters = ['service-category-filter', 'service-sort', 'service-price-range', 'service-appointment'];
            serviceFilters.forEach(id => {
                const element = document.getElementById(id);
                if (element) {
                    element.addEventListener('change', applyServiceFilters);
                }
            });
            
            // View toggle functionality
            document.querySelectorAll('.view-btn').forEach(btn => {
                btn.addEventListener('click', function() {
                    const viewType = this.dataset.view;
                    const container = this.closest('.tab-content');
                    
                    // Update active state
                    container.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
                    this.classList.add('active');
                    
                    // Apply view class to swiper container
                    const swiperContainer = container.querySelector('.swiper');
                    if (swiperContainer) {
                        swiperContainer.classList.toggle('list-view', viewType === 'list');
                        swiperContainer.classList.toggle('grid-view', viewType === 'grid');
                    }
                });
            });
        }
        
        // Initialize search and filters when DOM is ready
        initializeSearchAndFilters();

        // Load more functionality
        const loadMoreBtns = document.querySelectorAll('.load-more-btn');
        loadMoreBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const type = this.dataset.type;
                const shopId = window.location.pathname.split('/').pop();
                
                // Add loading state
                this.innerHTML = 'جارٍ التحميل...';
                this.disabled = true;
                
                // Simulate loading more items (replace with actual AJAX call)
                setTimeout(() => {
                    this.innerHTML = `عرض المزيد من ${type === 'products' ? 'المنتجات' : 'الخدمات'}`;
                    this.disabled = false;
                    // Hide button if no more items
                    // this.style.display = 'none';
                }, 1000);
            });
        });

        // Product/Service action handlers
        document.addEventListener('click', function(e) {
            if (e.target.classList.contains('btn-add-cart') || e.target.closest('.btn-add-cart')) {
                const btn = e.target.classList.contains('btn-add-cart') ? e.target : e.target.closest('.btn-add-cart');
                const productId = btn.dataset.productId;
                
                if (!btn.disabled) {
                    // Add loading state
                    const originalContent = btn.innerHTML;
                    btn.innerHTML = '<span class="btn-icon">⏳</span><span class="btn-text">جارٍ الإضافة...</span>';
                    btn.disabled = true;
                    
                    // Simulate API call
                    setTimeout(() => {
                        btn.innerHTML = '<span class="btn-icon">✅</span><span class="btn-text">تم الإضافة</span>';
                        btn.style.background = 'linear-gradient(135deg, #27ae60, #229954)';
                        
                        setTimeout(() => {
                            btn.innerHTML = originalContent;
                            btn.style.background = '';
                            btn.disabled = false;
                        }, 2000);
                    }, 1000);
                }
            }
            
            if (e.target.classList.contains('btn-book-service') || e.target.closest('.btn-book-service')) {
                const btn = e.target.classList.contains('btn-book-service') ? e.target : e.target.closest('.btn-book-service');
                const serviceId = btn.dataset.serviceId;
                const requiresAppointment = btn.dataset.requiresAppointment === 'true';
                
                const originalContent = btn.innerHTML;
                
                if (requiresAppointment) {
                    btn.innerHTML = '<span class="btn-icon">📅</span><span class="btn-text">جارٍ الحجز...</span>';
                    // Simulate appointment booking
                    setTimeout(() => {
                        btn.innerHTML = '<span class="btn-icon">✅</span><span class="btn-text">تم الحجز</span>';
                        btn.style.background = 'linear-gradient(135deg, #27ae60, #229954)';
                        
                        setTimeout(() => {
                            btn.innerHTML = originalContent;
                            btn.style.background = '';
                        }, 2000);
                    }, 1000);
                } else {
                    btn.innerHTML = '<span class="btn-icon">⏳</span><span class="btn-text">جارٍ الطلب...</span>';
                    setTimeout(() => {
                        btn.innerHTML = '<span class="btn-icon">✅</span><span class="btn-text">تم الطلب</span>';
                        btn.style.background = 'linear-gradient(135deg, #27ae60, #229954)';
                        
                        setTimeout(() => {
                            btn.innerHTML = originalContent;
                            btn.style.background = '';
                        }, 2000);
                    }, 1000);
                }
            }
            
            if (e.target.classList.contains('btn-wishlist') || e.target.closest('.btn-wishlist')) {
                const btn = e.target.classList.contains('btn-wishlist') ? e.target : e.target.closest('.btn-wishlist');
                const icon = btn.querySelector('.wishlist-icon');
                
                if (icon) {
                    const isWishlisted = icon.textContent === '❤️';
                    icon.textContent = isWishlisted ? '🤍' : '❤️';
                    
                    // Add animation
                    btn.style.transform = 'scale(1.2)';
                    setTimeout(() => {
                        btn.style.transform = '';
                    }, 200);
                }
            }

            // Slider navigation
            if (e.target.classList.contains('slider-btn') || e.target.closest('.slider-btn')) {
                const btn = e.target.classList.contains('slider-btn') ? e.target : e.target.closest('.slider-btn');
                const sliderType = btn.dataset.slider;
                const slider = document.getElementById(sliderType + '-slider');
                
                if (slider) {
                    const scrollAmount = 240; // Width of card + gap
                    const direction = btn.classList.contains('slider-btn-prev') ? -1 : 1;
                    
                    slider.scrollBy({
                        left: scrollAmount * direction,
                        behavior: 'smooth'
                    });
                }
            }
        });

        // Auto-hide slider buttons based on scroll position
        function updateSliderButtons() {
            const sliders = document.querySelectorAll('.products-slider, .services-slider');
            
            sliders.forEach(slider => {
                const container = slider.closest('.products-slider-container, .services-slider-container');
                const prevBtn = container.querySelector('.slider-btn-prev');
                const nextBtn = container.querySelector('.slider-btn-next');
                
                if (prevBtn && nextBtn) {
                    const isAtStart = slider.scrollLeft <= 0;
                    const isAtEnd = slider.scrollLeft >= slider.scrollWidth - slider.clientWidth - 10;
                    
                    prevBtn.style.opacity = isAtStart ? '0.5' : '1';
                    nextBtn.style.opacity = isAtEnd ? '0.5' : '1';
                    prevBtn.style.pointerEvents = isAtStart ? 'none' : 'auto';
                    nextBtn.style.pointerEvents = isAtEnd ? 'none' : 'auto';
                }
            });
        }

        // Add scroll listeners to sliders
        document.querySelectorAll('.products-slider, .services-slider').forEach(slider => {
            slider.addEventListener('scroll', updateSliderButtons);
        });

        // Initial button state
        setTimeout(updateSliderButtons, 100);
    });

    // Add CSS animations for notification and map icon
    const style = document.createElement('style');
    style.textContent = `
        @keyframes slideIn {
            from {
                transform: translateX(400px);
                opacity: 0;
            }
            to {
                transform: translateX(0);
                opacity: 1;
            }
        }
        @keyframes slideOut {
            from {
                transform: translateX(0);
                opacity: 1;
            }
            to {
                transform: translateX(400px);
                opacity: 0;
            }
        }
        @keyframes bounce {
            0%, 100% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-10px);
            }
        }
    `;
    document.head.appendChild(style);
    
</script>

@endpush
