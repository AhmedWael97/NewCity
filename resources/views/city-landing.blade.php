@extends('layouts.app')

@php
    $seoData = $seoData ?? [];
    $cityContext = $cityContext ?? ['selected_city_name' => 'مدينة'];
    $selectedCity = $cityContext['selected_city'] ?? null;
@endphp

@section('title', $seoData['title'] ?? "اكتشف المتاجر في {$cityContext['selected_city_name']}")
@section('description', $seoData['description'] ?? "استعرض أفضل المتاجر والخدمات في {$cityContext['selected_city_name']}")

@section('content')
    <main class="city-landing">
        {{-- Modern City Header Section --}}
        <section class="city-hero-modern bg-gradient-primary text-white py-5">
            <div class="container">
                {{-- City Navigation Bar --}}
                <div class="city-nav-bar bg-white bg-opacity-10 rounded-3 p-3 mb-4 backdrop-blur-lg">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            @if($selectedCity && $selectedCity->image)
                                <div class="city-icon-modern rounded-circle overflow-hidden me-3 shadow"
                                    style="width: 60px; height: 60px;">
                                    <img src="{{ $selectedCity->image }}"
                                        alt="{{ $cityContext['selected_city_name'] }}" class="w-100 h-100 object-fit-cover"
                                        onerror="this.parentElement.innerHTML='<div class=\'bg-white bg-opacity-20 rounded-circle p-2 d-flex align-items-center justify-content-center\' style=\'width: 60px; height: 60px;\'><i class=\'fas fa-map-marked-alt text-white\' style=\'font-size: 1.5rem;\'></i></div>'">
                                </div>
                            @else
                                <div class="city-icon-modern bg-white bg-opacity-20 rounded-circle p-2 me-3">
                                    <i class="fas fa-map-marked-alt text-white" style="font-size: 1.5rem;"></i>
                                </div>
                            @endif
                            <div>
                                <h1 class="city-name-modern h3 mb-1 fw-bold">
                                    {{ $cityContext['selected_city_name'] ?? 'مدينتك' }}</h1>
                                <div class="city-meta d-flex align-items-center text-white-75">
                                    <span class="me-3">
                                        <i class="fas fa-store me-1"></i>
                                        {{ number_format($stats['total_shops'] ?? 0) }} متجر
                                    </span>
                                    <span>
                                        <i class="fas fa-th-large me-1"></i>
                                        {{ $stats['total_categories'] ?? 0 }} فئة
                                    </span>
                                </div>
                            </div>
                        </div>
                        <button onclick="showCityModal()" class="btn btn-light btn-lg rounded-pill px-4 shadow-sm">
                            <i class="fas fa-exchange-alt me-2"></i>
                            تغيير المدينة
                        </button>
                    </div>
                </div>

                {{-- Enhanced Search Bar --}}
                <div class="search-section-modern mb-4">
                    <form class="search-form-modern"
                        action="{{ route('city.search', ['city' => $selectedCity->slug ?? 'all']) }}" method="GET">
                        <div class="search-container-modern position-relative">
                            <div class="search-icon-modern position-absolute">
                                <i class="fas fa-search text-muted"></i>
                            </div>
                            <input type="text" name="q" id="city-search-modern"
                                placeholder="ابحث في متاجر {{ $cityContext['selected_city_name'] ?? 'المدينة' }}... (مطاعم، ملابس، إلكترونيات)"
                                class="form-control form-control-lg shadow-lg border-0 ps-5 pe-5 py-4 rounded-pill"
                                autocomplete="off" style="background: rgba(255,255,255,0.95); backdrop-filter: blur(10px);">
                            <button type="submit" class="btn btn-primary btn-lg position-absolute rounded-pill px-4 shadow">
                                <i class="fas fa-search me-2"></i>
                                بحث
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </section>

        

        {{-- User Services Section (Hidden - will be on separate page) --}}
        @if(false && isset($serviceCategoriesWithServices) && $serviceCategoriesWithServices->count() > 0)
        <section class="services-section py-5">
            <div class="container">
                <div class="section-header-modern bg-white rounded-3 p-4 mb-4 shadow-sm">
                    <div class="d-flex align-items-center justify-content-between">
                        <div>
                            <h2 class="h3 mb-2 fw-bold">
                                <i class="fas fa-tools text-primary me-2"></i>
                                خدمات مقدمة من المستخدمين
                            </h2>
                            <p class="text-muted mb-0">اكتشف أفضل الخدمات المقدمة من أهل {{ $cityContext['selected_city_name'] }}</p>
                        </div>
                        @auth
                        <a href="{{ route('user.services.create') }}" class="btn btn-primary rounded-pill px-4">
                            <i class="fas fa-plus me-2"></i>
                            أضف خدمتك
                        </a>
                        @endauth
                    </div>
                </div>

                @foreach($serviceCategoriesWithServices as $serviceCategory)
                    <div class="service-category-section mb-5">
                        <div class="category-header-modern bg-white rounded-3 p-4 mb-4 shadow-sm">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center">
                                    <div class="category-icon-large bg-success bg-opacity-10 rounded-circle p-3 me-3">
                                        <i class="{{ $serviceCategory->icon ?? 'fas fa-wrench' }} text-success" style="font-size: 1.5rem;"></i>
                                    </div>
                                    <div>
                                        <h3 class="h5 mb-1 fw-bold">{{ $serviceCategory->name }}</h3>
                                        <p class="text-muted mb-0">
                                            <i class="fas fa-concierge-bell me-1"></i>
                                            {{ $serviceCategory->services_count }} خدمة متاحة
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row g-4">
                            @foreach($serviceCategory->userServices as $service)
                                <div class="col-lg-3 col-md-6">
                                    <div class="service-card bg-white rounded-3 shadow-sm h-100 overflow-hidden">
                                        @if($service->images && is_array($service->images) && count($service->images) > 0)
                                            <div class="service-image" style="height: 180px; overflow: hidden;">
                                                <img src="{{ $service->images[0] }}" 
                                                     alt="{{ $service->title }}" 
                                                     class="w-100 h-100 object-fit-cover"
                                                     onerror="this.src='/images/placeholder-service.jpg'">
                                            </div>
                                        @else
                                            <div class="service-image bg-light d-flex align-items-center justify-content-center" style="height: 180px;">
                                                <i class="{{ $serviceCategory->icon ?? 'fas fa-wrench' }} text-muted" style="font-size: 3rem;"></i>
                                            </div>
                                        @endif
                                        
                                        <div class="p-3">
                                            <div class="d-flex align-items-center mb-2">
                                                <div class="service-provider-avatar bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                                    <i class="fas fa-user text-primary"></i>
                                                </div>
                                                <small class="text-muted">{{ $service->user->name }}</small>
                                            </div>
                                            
                                            <h5 class="service-title h6 mb-2 fw-bold">
                                                <a href="{{ route('user.services.show', $service->slug) }}" class="text-decoration-none text-dark">
                                                    {{ Str::limit($service->title, 40) }}
                                                </a>
                                            </h5>
                                            
                                            <p class="service-description text-muted small mb-3">
                                                {{ Str::limit($service->description, 60) }}
                                            </p>
                                            
                                            <div class="d-flex align-items-center justify-content-between">
                                                <div class="service-price">
                                                    @if($service->price_type === 'fixed')
                                                        <span class="text-success fw-bold">{{ number_format($service->price) }} جنيه</span>
                                                    @else
                                                        <span class="text-muted"><i class="fas fa-handshake me-1"></i>تفاوض</span>
                                                    @endif
                                                </div>
                                                <a href="{{ route('user.services.show', $service->slug) }}" class="btn btn-sm btn-outline-primary rounded-pill">
                                                    التفاصيل
                                                    <i class="fas fa-arrow-left ms-1"></i>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>
        </section>
        @endif

        {{-- All Shops Section --}}
        <section class="all-shops py-5 bg-light">
            <div class="container-fluid">
                <div class="row">
                    {{-- Enhanced Sidebar - Now on the Right --}}
                    <div class="col-lg-4 order-lg-1">
                        <div class="sidebar-modern">
                            {{-- City Quick Info Widget --}}
                            <div class="sidebar-widget-modern bg-white rounded-3 p-4 mb-4 shadow-sm">
                                <h5 class="widget-title-modern h6 mb-3 fw-bold d-flex align-items-center">
                                    <i class="fas fa-info-circle text-primary me-2"></i>
                                    معلومات {{ $cityContext['selected_city_name'] }}
                                </h5>
                                <div class="city-info-grid">
                                    <div
                                        class="info-item d-flex justify-content-between align-items-center mb-3 p-2 bg-light rounded">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-store text-primary me-2"></i>
                                            <span class="text-muted">المتاجر النشطة</span>
                                        </div>
                                        <strong
                                            class="text-primary">{{ number_format($stats['total_shops'] ?? 0) }}</strong>
                                    </div>
                                    <div
                                        class="info-item d-flex justify-content-between align-items-center mb-3 p-2 bg-light rounded">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-th-large text-success me-2"></i>
                                            <span class="text-muted">الفئات المتاحة</span>
                                        </div>
                                        <strong class="text-success">{{ $stats['total_categories'] ?? 0 }}</strong>
                                    </div>
                                    <div
                                        class="info-item d-flex justify-content-between align-items-center mb-3 p-2 bg-light rounded">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-star text-warning me-2"></i>
                                            <span class="text-muted">متوسط التقييم</span>
                                        </div>
                                        <strong class="text-warning">{{ number_format($stats['avg_rating'] ?? 4.5, 1) }}
                                            ⭐</strong>
                                    </div>
                                    <div
                                        class="info-item d-flex justify-content-between align-items-center p-2 bg-light rounded">
                                        <div class="d-flex align-items-center">
                                            <i class="fas fa-comments text-info me-2"></i>
                                            <span class="text-muted">إجمالي التقييمات</span>
                                        </div>
                                        <strong class="text-info">{{ number_format($stats['total_reviews'] ?? 0) }}</strong>
                                    </div>
                                </div>
                            </div>

                            {{-- Popular Categories Widget --}}
                            @if($categoriesWithShops && $categoriesWithShops->count() > 0)
                                <div class="sidebar-widget-modern bg-white rounded-3 p-4 mb-4 shadow-sm">
                                    <h5 class="widget-title-modern h6 mb-3 fw-bold d-flex align-items-center">
                                        <i class="fas fa-fire text-danger me-2"></i>
                                        الفئات الأكثر شعبية
                                    </h5>
                                    <div class="popular-categories-list">
                                        @foreach($categoriesWithShops->take(5) as $category)
                                            <a href="{{ route('city.category.shops', ['city' => $selectedCity->slug ?? 'all', 'category' => $category->slug]) }}"
                                                class="category-link-modern d-flex align-items-center justify-content-between mb-3 p-3 rounded-2 text-decoration-none border">
                                                <div class="d-flex align-items-center">
                                                    <div
                                                        class="category-icon-small bg-primary bg-opacity-10 rounded-circle p-2 me-3">
                                                        <i class="{{ $category->icon ?? 'fas fa-store' }} text-primary"></i>
                                                    </div>
                                                    <div>
                                                        <h6 class="mb-0 text-dark">{{ $category->name }}</h6>
                                                        <small class="text-muted">{{ $category->shops_count }} متجر</small>
                                                    </div>
                                                </div>
                                                <i class="fas fa-chevron-left text-muted"></i>
                                            </a>
                                        @endforeach
                                    </div>
                                    <div class="text-center mt-3">
                                        <a href="{{ route('categories.index') }}"
                                            class="btn btn-outline-primary btn-sm rounded-pill">
                                            <i class="fas fa-th-large me-1"></i>
                                            عرض جميع الفئات
                                        </a>
                                    </div>
                                </div>
                            @endif

                            {{-- Quick Actions Widget --}}
                            <div class="sidebar-widget-modern bg-white rounded-3 p-4 mb-4 shadow-sm">
                                <h5 class="widget-title-modern h6 mb-3 fw-bold d-flex align-items-center">
                                    <i class="fas fa-bolt text-warning me-2"></i>
                                    إجراءات سريعة
                                </h5>
                                <div class="quick-actions-grid d-grid gap-2">
                                    <button type="button" class="btn btn-success btn-sm rounded-pill mb-2" 
                                            data-bs-toggle="modal" data-bs-target="#suggestShopModal">
                                        <i class="fas fa-plus-circle me-2"></i>
                                        اقترح متجر
                                    </button>
                                    <a href="{{ route('city.shops.featured', ['city' => $selectedCity->slug ?? 'all']) }}"
                                        class="btn btn-warning btn-sm rounded-pill mb-2">
                                        <i class="fas fa-star me-2"></i>
                                        المتاجر المميزة
                                    </a>
                                    <a href="{{ route('city.search', ['city' => $selectedCity->slug ?? 'all']) }}"
                                        class="btn btn-info btn-sm rounded-pill mb-2">
                                        <i class="fas fa-search me-2"></i>
                                        البحث المتقدم
                                    </a>
                                    <button onclick="showCityModal()" class="btn btn-outline-secondary btn-sm rounded-pill">
                                        <i class="fas fa-exchange-alt me-2"></i>
                                        تغيير المدينة
                                    </button>
                                </div>
                            </div>

                            {{-- Advertisement Widget --}}
                            <div class="sidebar-widget-modern">
                                <x-ad-display type="sidebar" placement="city_landing" :city-id="$selectedCity->id ?? null"
                                    class="rounded-3 overflow-hidden shadow-sm" />
                            </div>
                        </div>
                    </div>

                    {{-- Main Content - Shops on the Left --}}
                    <div class="col-lg-8 order-lg-2">
                        {{-- Mobile Info Carousel (Hidden on Desktop) --}}
                        <div class="mobile-info-carousel shadow-sm d-lg-none" style="display: none;">
                            <div class="mobile-info-slide active">
                                <i class="fas fa-store text-primary me-2"></i>
                                <strong>{{ number_format($stats['total_shops'] ?? 0) }}</strong>
                                <span class="text-muted ms-1">متجر نشط</span>
                            </div>
                            <div class="mobile-info-slide">
                                <i class="fas fa-th-large text-success me-2"></i>
                                <strong>{{ $stats['total_categories'] ?? 0 }}</strong>
                                <span class="text-muted ms-1">فئة متاحة</span>
                            </div>
                            <div class="mobile-info-slide">
                                <i class="fas fa-star text-warning me-2"></i>
                                <strong>{{ number_format($stats['avg_rating'] ?? 4.5, 1) }} ⭐</strong>
                                <span class="text-muted ms-1">متوسط التقييم</span>
                            </div>
                            @if($categoriesWithShops && $categoriesWithShops->count() > 0)
                                @foreach($categoriesWithShops->take(3) as $category)
                                    <div class="mobile-info-slide">
                                        <i class="{{ $category->icon ?? 'fas fa-store' }} text-primary me-2"></i>
                                        <strong>{{ $category->name }}</strong>
                                        <span class="text-muted ms-1">({{ $category->shops_count }} متجر)</span>
                                    </div>
                                @endforeach
                            @endif
                        </div>

                        {{-- Mobile Quick Actions (4 columns on mobile) --}}
                        <div class="d-lg-none mb-4">
                            <div class="quick-actions-grid mobile-quick-actions">
                                <button type="button" class="btn btn-success btn-sm rounded-pill" 
                                        data-bs-toggle="modal" data-bs-target="#suggestShopModal">
                                    <i class="fas fa-plus-circle me-1"></i>
                                    اقترح متجر
                                </button>
                                <a href="{{ route('city.shops.featured', ['city' => $selectedCity->slug ?? 'all']) }}"
                                    class="btn btn-warning btn-sm rounded-pill">
                                    <i class="fas fa-star me-1"></i>
                                    المميزة
                                </a>
                                <a href="{{ route('city.search', ['city' => $selectedCity->slug ?? 'all']) }}"
                                    class="btn btn-info btn-sm rounded-pill">
                                    <i class="fas fa-search me-1"></i>
                                    بحث
                                </a>
                                <button onclick="showCityModal()" class="btn btn-outline-secondary btn-sm rounded-pill">
                                    <i class="fas fa-exchange-alt me-1"></i>
                                    المدينة
                                </button>
                            </div>
                        </div>

                        <div class="d-flex align-items-center justify-content-between mb-4">
                            <h2 class="h4 mb-0 fw-bold">جميع المتاجر في {{ $cityContext['selected_city_name'] }}</h2>
                            <span class="badge bg-primary rounded-pill px-3 py-2">
                                {{ $shops->count() }} متجر
                            </span>
                        </div>

                        @if($shops && $shops->count() > 0)
                            {{-- Shops Grid - 3 cards per row on large screens --}}
                            <div class="row g-4">
                                @foreach($shops as $shop)
                                    <div class="col-lg-4 col-md-6">
                                        <x-shop-card :shop="$shop" :city-name="$cityContext['selected_city_name']" />
                                    </div>
                                @endforeach
                            </div>
                        @else
                            {{-- Enhanced Empty State --}}
                            <div class="empty-state-modern bg-white rounded-3 p-5 text-center shadow-sm">
                                <div class="empty-icon-modern mb-4">
                                    <i class="fas fa-city text-muted" style="font-size: 4rem;"></i>
                                </div>
                                <h3 class="h4 mb-3 fw-bold">قريباً في {{ $cityContext['selected_city_name'] }}</h3>
                                <p class="text-muted mb-4">نعمل حالياً على إضافة أفضل المتاجر والخدمات في مدينتك</p>
                                <div class="d-flex justify-content-center gap-3">
                                    <button onclick="showCityModal()" class="btn btn-primary btn-lg rounded-pill px-4">
                                        <i class="fas fa-exchange-alt me-2"></i>
                                        جرب مدينة أخرى
                                    </button>
                                    <a href="{{ route('categories.index') }}"
                                        class="btn btn-outline-primary btn-lg rounded-pill px-4">
                                        <i class="fas fa-th-large me-2"></i>
                                        تصفح الفئات
                                    </a>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </section>


        {{-- City Statistics Dashboard --}}
        <section class="city-stats-dashboard py-4 bg-light">
            <div class="container">
                <div class="stats-grid-modern row g-4">
                    <div class="col-lg-3 col-md-6">
                        <div class="stat-card-modern bg-white rounded-3 p-4 shadow-sm h-100 text-center">
                            <div class="stat-icon-modern mb-3">
                                <i class="fas fa-users text-primary" style="font-size: 2.5rem;"></i>
                            </div>
                            <div class="stat-number-modern h2 mb-1 text-primary fw-bold">
                                {{ number_format($stats['total_users'] ?? 0) }}</div>
                            <div class="stat-label-modern text-muted">مستخدم نشط</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="stat-card-modern bg-white rounded-3 p-4 shadow-sm h-100 text-center">
                            <div class="stat-icon-modern mb-3">
                                <i class="fas fa-star text-warning" style="font-size: 2.5rem;"></i>
                            </div>
                            <div class="stat-number-modern h2 mb-1 text-warning fw-bold">
                                {{ number_format($stats['avg_rating'] ?? 4.5, 1) }}</div>
                            <div class="stat-label-modern text-muted">متوسط التقييم</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="stat-card-modern bg-white rounded-3 p-4 shadow-sm h-100 text-center">
                            <div class="stat-icon-modern mb-3">
                                <i class="fas fa-th-large text-success" style="font-size: 2.5rem;"></i>
                            </div>
                            <div class="stat-number-modern h2 mb-1 text-success fw-bold">
                                {{ number_format($stats['total_categories'] ?? 0) }}</div>
                            <div class="stat-label-modern text-muted">فئة متنوعة</div>
                        </div>
                    </div>
                    <div class="col-lg-3 col-md-6">
                        <div class="stat-card-modern bg-white rounded-3 p-4 shadow-sm h-100 text-center">
                            <div class="stat-icon-modern mb-3">
                                <i class="fas fa-store text-info" style="font-size: 2.5rem;"></i>
                            </div>
                            <div class="stat-number-modern h2 mb-1 text-info fw-bold">
                                {{ number_format($stats['total_shops'] ?? 0) }}</div>
                            <div class="stat-label-modern text-muted">متجر مفعل</div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        {{-- Quick Categories Navigation --}}
        <section class="quick-categories py-4">
            <div class="container">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <h2 class="h4 mb-0 fw-bold">استكشف الفئات</h2>
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-primary">
                        عرض جميع الفئات
                        <i class="fas fa-arrow-left ms-1"></i>
                    </a>
                </div>

                @if($categoriesWithShops && $categoriesWithShops->count() > 0)
                    <div class="categories-slider-modern">
                        <div class="row g-3">
                            @foreach($categoriesWithShops->take(6) as $category)
                                <div class="col-lg-2 col-md-3 col-4">
                                    <a href="{{ route('city.category.shops', ['city' => $selectedCity->slug ?? 'all', 'category' => $category->slug]) }}"
                                        class="category-card-modern bg-white rounded-3 p-3 shadow-sm text-center text-decoration-none h-100 d-block">
                                        <div class="category-icon-modern mb-2">
                                            <i class="{{ $category->icon ?? 'fas fa-store' }} text-primary"
                                                style="font-size: 2rem;"></i>
                                        </div>
                                        <h6 class="category-name-modern mb-1 text-dark">{{ $category->name }}</h6>
                                        <small class="text-muted">{{ $category->shops_count }} متجر</small>
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>
        </section>

        {{-- Services Discovery Banner --}}
        <section class="services-discovery-banner py-5">
            <div class="container">
                <div class="discovery-card bg-gradient position-relative overflow-hidden rounded-4 shadow-lg">
                    <div class="row align-items-center">
                        <div class="col-lg-7 p-5">
                            <div class="discovery-content">
                                <h2 class="display-6 fw-bold mb-3 text-white">
                                    <i class="fas fa-tools me-2"></i>
                                    اكتشف خدمات محلية من أهل {{ $cityContext['selected_city_name'] }}
                                </h2>
                                <p class="lead mb-4 text-white" style="opacity: 0.95;">
                                    سباكة، كهرباء، نجارة، تصليح أجهزة، وأكثر من 30 خدمة مقدمة من متخصصين موثوقين في منطقتك
                                </p>
                                <div class="d-flex flex-wrap gap-3 mb-4">
                                    <div class="feature-badge bg-white bg-opacity-20 rounded-pill px-4 py-2 text-white">
                                        <i class="fas fa-check-circle me-2"></i>
                                        خدمات متنوعة
                                    </div>
                                    <div class="feature-badge bg-white bg-opacity-20 rounded-pill px-4 py-2 text-white">
                                        <i class="fas fa-map-marker-alt me-2"></i>
                                        قريبة منك
                                    </div>
                                    <div class="feature-badge bg-white bg-opacity-20 rounded-pill px-4 py-2 text-white">
                                        <i class="fas fa-star me-2"></i>
                                        موثوقة
                                    </div>
                                </div>
                                <a href="{{ route('city.services', ['city' => $selectedCity->slug ?? 'all']) }}" 
                                   class="btn btn-light btn-lg rounded-pill px-5 shadow-lg text-dark fw-bold">
                                    <i class="fas fa-search me-2"></i>
                                    استكشف الخدمات الآن
                                    <i class="fas fa-arrow-left ms-2"></i>
                                </a>
                            </div>
                        </div>
                        <div class="col-lg-5 d-none d-lg-block">
                            <div class="discovery-illustration p-4">
                                <div class="services-icons-grid">
                                    <div class="service-icon-float">
                                        <div class="icon-bubble bg-white shadow-lg rounded-circle p-4">
                                            <i class="fas fa-wrench text-primary" style="font-size: 2rem;"></i>
                                        </div>
                                    </div>
                                    <div class="service-icon-float" style="animation-delay: 0.5s;">
                                        <div class="icon-bubble bg-white shadow-lg rounded-circle p-4">
                                            <i class="fas fa-paint-roller text-success" style="font-size: 2rem;"></i>
                                        </div>
                                    </div>
                                    <div class="service-icon-float" style="animation-delay: 1s;">
                                        <div class="icon-bubble bg-white shadow-lg rounded-circle p-4">
                                            <i class="fas fa-bolt text-warning" style="font-size: 2rem;"></i>
                                        </div>
                                    </div>
                                    <div class="service-icon-float" style="animation-delay: 1.5s;">
                                        <div class="icon-bubble bg-white shadow-lg rounded-circle p-4">
                                            <i class="fas fa-hammer text-danger" style="font-size: 2rem;"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Background Decoration --}}
                    <div class="position-absolute top-0 end-0 opacity-10">
                        <i class="fas fa-tools" style="font-size: 15rem;"></i>
                    </div>
                </div>
            </div>
        </section>

        {{-- Call to Action Section --}}
        <section class="cta-section py-5 bg-gradient-primary text-white">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h3 class="h4 mb-2 fw-bold">هل تملك متجر في {{ $cityContext['selected_city_name'] }}؟</h3>
                        <p class="mb-0 opacity-75">انضم إلى منصتنا واعرض متجرك لآلاف العملاء المحتملين</p>
                    </div>
                    <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                        <a href="{{ route('shop-owner.create-shop') }}" class="btn btn-light btn-lg rounded-pill px-4">
                            <i class="fas fa-plus-circle me-2"></i>
                            إضافة متجر
                        </a>
                    </div>
                </div>
            </div>
        </section>

        {{-- Quick Actions Section --}}
        <section class="quick-actions bg-light py-5">
            <div class="container">
                <div class="row g-4">
                    <div class="col-md-4">
                        <div class="action-card bg-white rounded-lg p-4 h-100 text-center">
                            <div class="action-icon mb-3">
                                <i class="fas fa-plus-circle text-primary" style="font-size: 3rem;"></i>
                            </div>
                            <h3 class="h5 mb-2">أضف متجرك</h3>
                            <p class="text-muted mb-3">انضم إلى منصتنا وارفع مبيعاتك</p>
                            <a href="{{ route('shop-owner.create-shop') }}" class="btn btn-primary">ابدأ الآن</a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="action-card bg-white rounded-lg p-4 h-100 text-center">
                            <div class="action-icon mb-3">
                                <i class="fas fa-star text-warning" style="font-size: 3rem;"></i>
                            </div>
                            <h3 class="h5 mb-2">أفضل المتاجر</h3>
                            <p class="text-muted mb-3">اكتشف المتاجر الأعلى تقييماً</p>
                            <a href="{{ route('city.shops.featured', ['city' => $selectedCity->slug ?? 'all']) }}"
                                class="btn btn-warning">استكشف</a>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="action-card bg-white rounded-lg p-4 h-100 text-center">
                            <div class="action-icon mb-3">
                                <i class="fas fa-mobile-alt text-success" style="font-size: 3rem;"></i>
                            </div>
                            <h3 class="h5 mb-2">تطبيق الجوال</h3>
                            <p class="text-muted mb-3">حمّل التطبيق للوصول السريع</p>
                            <button class="btn btn-success" onclick="alert('قريباً على متاجر التطبيقات')">قريباً</button>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </main>

    {{-- City Selection Modal (Simple & Working) --}}
    <x-city-modal-simple :show-modal="!session('selected_city')" />

    {{-- Shop Suggestion Modal --}}
    <div class="modal fade" id="suggestShopModal" tabindex="-1" aria-labelledby="suggestShopModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-scrollable">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="suggestShopModalLabel">
                        <i class="fas fa-plus-circle me-2"></i>
                        اقترح متجر جديد
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        <strong>ساعدنا في تحسين الخدمة!</strong> اقترح متجر تعرفه وسنقوم بمراجعته وإضافته قريباً.
                    </div>

                    <form id="suggestShopForm">
                        @csrf
                        <input type="hidden" name="city_id" value="{{ $selectedCity->id ?? '' }}">

                        {{-- Suggested By Info --}}
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-user me-2"></i>معلوماتك</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">اسمك</label>
                                        <input type="text" class="form-control" name="suggested_by_name" required 
                                               value="{{ auth()->user()->name ?? '' }}">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">رقم الهاتف</label>
                                        <input type="text" class="form-control" name="suggested_by_phone" required 
                                               value="{{ auth()->user()->phone ?? '' }}">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">البريد الإلكتروني</label>
                                        <input type="email" class="form-control" name="suggested_by_email" 
                                               value="{{ auth()->user()->email ?? '' }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Shop Basic Info --}}
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-store me-2"></i>معلومات المتجر الأساسية</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label required">اسم المتجر</label>
                                        <input type="text" class="form-control" name="shop_name" required 
                                               placeholder="مثال: متجر الإلكترونيات الحديثة">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">المدينة</label>
                                        <select class="form-select" name="city_id" id="citySelect" required>
                                            <option value="">اختر المدينة</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">الفئة</label>
                                        <select class="form-select" name="category_id" id="categorySelect">
                                            <option value="">اختر الفئة</option>
                                        </select>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">وصف المتجر</label>
                                        <textarea class="form-control" name="description" rows="3" 
                                                  placeholder="اكتب وصفاً مختصراً عن المتجر ونشاطه"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Contact Info --}}
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-phone me-2"></i>معلومات الاتصال</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">رقم الهاتف</label>
                                        <input type="text" class="form-control" name="phone" 
                                               placeholder="01xxxxxxxxx">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">واتساب</label>
                                        <input type="text" class="form-control" name="whatsapp" 
                                               placeholder="01xxxxxxxxx">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">البريد الإلكتروني</label>
                                        <input type="email" class="form-control" name="email" 
                                               placeholder="shop@example.com">
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Location Info --}}
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>معلومات الموقع</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">العنوان</label>
                                        <textarea class="form-control" name="address" rows="2" 
                                                  placeholder="مثال: شارع الجلاء، أمام مسجد النور"></textarea>
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">رابط خرائط جوجل</label>
                                        <input type="url" class="form-control" name="google_maps_url" 
                                               placeholder="https://goo.gl/maps/...">
                                        <small class="text-muted">يمكنك الحصول عليه من خرائط جوجل</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Additional Info --}}
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h6 class="mb-0"><i class="fas fa-info-circle me-2"></i>معلومات إضافية</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">الموقع الإلكتروني</label>
                                        <input type="url" class="form-control" name="website" 
                                               placeholder="https://www.example.com">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">صفحة الفيسبوك</label>
                                        <input type="url" class="form-control" name="facebook" 
                                               placeholder="https://facebook.com/...">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">صفحة الإنستجرام</label>
                                        <input type="url" class="form-control" name="instagram" 
                                               placeholder="https://instagram.com/...">
                                    </div>
                                    <div class="col-md-12 mb-3">
                                        <label class="form-label">أوقات العمل</label>
                                        <textarea class="form-control" name="opening_hours" rows="2" 
                                                  placeholder="مثال: من السبت إلى الخميس من 9 صباحاً إلى 10 مساءً"></textarea>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fas fa-times me-2"></i>إلغاء
                    </button>
                    <button type="button" class="btn btn-primary" id="submitSuggestion">
                        <i class="fas fa-paper-plane me-2"></i>إرسال الاقتراح
                    </button>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <style>
            /* Modern City Landing Page Styles */
            .city-landing {
                font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
                direction: rtl;
                background: #f8f9fa;
            }

            /* Enhanced Hero Section */
            .city-hero-modern {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                position: relative;
                overflow: hidden;
            }

            .city-hero-modern::before {
                content: '';
                position: absolute;
                top: 0;
                left: 0;
                right: 0;
                bottom: 0;
                background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
                opacity: 0.5;
            }

            .city-nav-bar {
                border: 1px solid rgba(255, 255, 255, 0.2);
                backdrop-filter: blur(10px);
            }

            .city-icon-modern {
                width: 50px;
                height: 50px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            .city-name-modern {
                color: white;
                text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            }

            /* Enhanced Search */
            .search-container-modern {
                max-width: 600px;
                margin: 0 auto;
            }

            .search-icon-modern {
                right: 20px;
                top: 50%;
                transform: translateY(-50%);
                z-index: 3;
            }

            #city-search-modern {
                padding-right: 120px;
                padding-left: 60px;
                font-size: 16px;
                border: 1px solid rgba(255, 255, 255, 0.2);
            }

            #city-search-modern:focus {
                box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.3);
                border-color: rgba(255, 255, 255, 0.4);
            }

            .search-container-modern button {
                left: 8px;
                top: 8px;
                height: calc(100% - 16px);
            }

            /* Modern Stats Dashboard */
            .city-stats-dashboard {
                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            }

            .stat-card-modern {
                border: 1px solid rgba(0, 0, 0, 0.05);
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }

            .stat-card-modern:hover {
                transform: translateY(-5px);
                box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
            }

            .stat-number-modern {
                font-size: 2.5rem;
                font-weight: 700;
            }

            /* Category Cards */
            .category-card-modern {
                border: 1px solid rgba(0, 0, 0, 0.08);
                transition: all 0.3s ease;
            }

            .category-card-modern:hover {
                transform: translateY(-3px);
                box-shadow: 0 8px 25px rgba(102, 126, 234, 0.15);
                border-color: rgba(102, 126, 234, 0.2);
                text-decoration: none;
            }

            /* Enhanced Shop Cards */
            .shop-card-modern {
                border: 1px solid rgba(0, 0, 0, 0.05);
                transition: all 0.3s ease;
                overflow: hidden;
            }

            .shop-card-modern:hover {
                transform: translateY(-5px);
                box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            }

            .shop-card-modern:hover .shop-overlay {
                opacity: 1 !important;
            }

            .shop-image-modern img {
                object-fit: cover;
                transition: transform 0.3s ease;
            }

            .shop-card-modern:hover .shop-image-modern img {
                transform: scale(1.05);
            }

            /* Category Section Headers */
            .category-header-modern {
                border: 1px solid rgba(0, 0, 0, 0.05);
            }

            .category-icon-large {
                width: 60px;
                height: 60px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            /* Sidebar Enhancements */
            .sidebar-widget-modern {
                border: 1px solid rgba(0, 0, 0, 0.05);
                transition: box-shadow 0.3s ease;
            }

            .sidebar-widget-modern:hover {
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.08);
            }

            .category-link-modern {
                border: 1px solid rgba(0, 0, 0, 0.08) !important;
                transition: all 0.3s ease;
            }

            .category-link-modern:hover {
                background-color: rgba(102, 126, 234, 0.05) !important;
                border-color: rgba(102, 126, 234, 0.2) !important;
                text-decoration: none;
                transform: translateX(-3px);
            }

            .category-icon-small {
                width: 35px;
                height: 35px;
                display: flex;
                align-items: center;
                justify-content: center;
            }

            /* Enhanced Empty State */
            .empty-state-modern {
                border: 1px solid rgba(0, 0, 0, 0.05);
            }

            .empty-icon-modern {
                opacity: 0.6;
            }

            /* CTA Section */
            .cta-section {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            }

            /* Mobile Auto-Rotating Info */
            .mobile-info-carousel {
                overflow: hidden;
                position: relative;
                height: 60px;
                background: white;
                border-radius: 8px;
                padding: 10px;
                margin-bottom: 15px;
            }

            .mobile-info-slide {
                display: flex;
                align-items: center;
                justify-content: center;
                height: 100%;
                font-size: 0.85rem;
                opacity: 0;
                position: absolute;
                width: 100%;
                transition: opacity 0.5s ease;
            }

            .mobile-info-slide.active {
                opacity: 1;
                position: relative;
            }

            /* Responsive Design */
            @media (max-width: 768px) {
                .stat-number-modern {
                    font-size: 2rem;
                }

                .city-name-modern {
                    font-size: 1.5rem;
                }

                #city-search-modern {
                    padding-left: 50px;
                    padding-right: 100px;
                }

                .search-container-modern button {
                    left: 5px;
                    padding: 0 15px;
                }

                .category-card-modern {
                    margin-bottom: 1rem;
                }

                .shop-card-modern {
                    margin-bottom: 1.5rem;
                }

                /* Hide desktop sidebar on mobile */
                .sidebar-modern .sidebar-widget-modern {
                    display: none !important;
                }

                /* Show mobile carousel instead */
                .mobile-info-carousel {
                    display: block !important;
                }

                /* Quick Actions: 4 columns */
                .quick-actions-grid.mobile-quick-actions {
                    display: grid !important;
                    grid-template-columns: repeat(2, 1fr);
                    gap: 8px;
                }

                .quick-actions-grid.mobile-quick-actions .btn {
                    font-size: 0.75rem;
                    padding: 8px 10px;
                    white-space: nowrap;
                }

                .quick-actions-grid.mobile-quick-actions .btn i {
                    font-size: 0.85rem;
                }

                /* Shop Cards: 2 columns */
                .all-shops .row .col-lg-4 {
                    flex: 0 0 50%;
                    max-width: 50%;
                }
            }

            @media (max-width: 576px) {
                .stat-number-modern {
                    font-size: 1.8rem;
                }

                .city-hero-modern {
                    padding: 2rem 0;
                }

                .categories-slider-modern .col-4 {
                    flex: 0 0 50%;
                    max-width: 50%;
                }

                .search-container-modern button {
                    position: relative !important;
                    width: 100%;
                    margin-top: 10px;
                    left: auto;
                    top: auto;
                    height: auto;
                }

                #city-search-modern {
                    padding-left: 20px;
                    padding-right: 50px;
                    margin-bottom: 10px;
                }

                /* Mobile carousel smaller text */
                .mobile-info-slide {
                    font-size: 0.75rem;
                }

                .mobile-info-slide i {
                    font-size: 0.85rem;
                }

                /* Quick Actions: smaller on very small screens */
                .quick-actions-grid.mobile-quick-actions .btn {
                    font-size: 0.7rem;
                    padding: 6px 8px;
                }

                .quick-actions-grid.mobile-quick-actions .btn i {
                    font-size: 0.75rem;
                    margin-left: 3px;
                }
            }

            /* Hide mobile carousel on desktop */
            @media (min-width: 769px) {
                .mobile-info-carousel {
                    display: none !important;
                }
            }

            /* Animation Utilities */
            @keyframes fadeInUp {
                from {
                    opacity: 0;
                    transform: translateY(30px);
                }

                to {
                    opacity: 1;
                    transform: translateY(0);
                }
            }

            .category-section-modern {
                animation: fadeInUp 0.6s ease-out;
            }

            .category-section-modern:nth-child(even) {
                animation-delay: 0.1s;
            }

            .category-section-modern:nth-child(odd) {
                animation-delay: 0.2s;
            }

            /* Focus and Accessibility */
            .btn:focus,
            .form-control:focus {
                box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.2);
            }

            /* Badge Enhancements */
            .badge {
                font-weight: 500;
                padding: 0.5em 0.75em;
            }

            /* Shop Suggestion Modal */
            #suggestShopModal .card-header {
                border-bottom: 2px solid #e9ecef;
            }
            
            #suggestShopModal .form-label.required::after {
                content: " *";
                color: #dc3545;
            }
            
            #suggestShopModal .modal-body {
                max-height: 70vh;
            }
            
            #suggestShopModal .card {
                border: 1px solid #e9ecef;
                transition: box-shadow 0.3s ease;
            }
            
            #suggestShopModal .card:hover {
                box-shadow: 0 4px 15px rgba(0,0,0,0.08);
            }

            /* Loading and Performance */
            .shop-image-modern {
                background: linear-gradient(45deg, #f1f3f4 25%, transparent 25%),
                    linear-gradient(-45deg, #f1f3f4 25%, transparent 25%),
                    linear-gradient(45deg, transparent 75%, #f1f3f4 75%),
                    linear-gradient(-45deg, transparent 75%, #f1f3f4 75%);
                background-size: 20px 20px;
                background-position: 0 0, 0 10px, 10px -10px, -10px 0px;
            }

            /* Print Styles */
            @media print {

                .sidebar-modern,
                .cta-section {
                    display: none;
                }

                .shop-card-modern {
                    break-inside: avoid;
                    page-break-inside: avoid;
                }

                /* Additional Styles */
                .fa-star {
                    font-size: 1.5rem;
                }

                .search-icon {
                    top: 50%;
                    left: 1rem;
                    transform: translateY(-50%);
                    z-index: 3;
                }

                .shop-card {
                    border: 1px solid #e9ecef;
                    transition: all 0.3s ease;
                }

                .shop-card:hover {
                    transform: translateY(-5px);
                    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1) !important;
                }

                .object-cover {
                    object-fit: cover;
                }

                .category-section {
                    border-bottom: 1px solid #e9ecef;
                    padding-bottom: 2rem;
                }

                .category-section:last-child {
                    border-bottom: none;
                }

                .action-card {
                    border: 1px solid #e9ecef;
                    transition: transform 0.2s ease;
                }

                .action-card:hover {
                    transform: translateY(-3px);
                }

                .sidebar-widget {
                    border: 1px solid #e9ecef;
                }

                .category-link {
                    color: inherit;
                    transition: background-color 0.2s ease;
                }

                .category-link:hover {
                    background-color: #f8f9fa !important;
                    text-decoration: none;
                    color: inherit;
                }
            }

            /* Services Discovery Banner */
            .services-discovery-banner {
                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%);
            }

            .discovery-card {
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                min-height: 400px;
            }

            .discovery-content {
                position: relative;
                z-index: 2;
            }

            .discovery-content h2,
            .discovery-content p {
                color: black !important;
                opacity: 0.8;
                text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
            }

            .feature-badge {
                backdrop-filter: blur(10px);
                color: black !important;
                opacity: 0.8;
                font-weight: 500;
            }

            .btn-light {
                background: #ffffff !important;
                color: #333 !important;
                font-weight: 600;
                border: none;
                transition: transform 0.3s ease, box-shadow 0.3s ease;
            }

            .btn-light:hover {
                transform: translateY(-2px);
                box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2) !important;
                color: #667eea !important;
            }

            .services-icons-grid {
                display: grid;
                grid-template-columns: repeat(2, 1fr);
                gap: 30px;
                padding: 40px;
            }

            .service-icon-float {
                animation: float 3s ease-in-out infinite;
            }

            .icon-bubble {
                width: 100px;
                height: 100px;
                display: flex;
                align-items: center;
                justify-content: center;
                transition: transform 0.3s ease;
            }

            .icon-bubble:hover {
                transform: scale(1.1);
            }

            @keyframes float {
                0%, 100% {
                    transform: translateY(0px);
                }
                50% {
                    transform: translateY(-20px);
                }
            }

            @media (max-width: 768px) {
                .discovery-card {
                    min-height: 300px;
                }
                
                .discovery-content h2 {
                    font-size: 1.5rem;
                }
                
                .discovery-content p {
                    font-size: 1rem;
                }
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Enhanced search functionality
                const searchInput = document.getElementById('city-search-modern');
                if (searchInput) {
                    // Focus management for better UX
                    searchInput.addEventListener('focus', function () {
                        this.parentElement.classList.add('focused');
                        this.placeholder = 'اكتب اسم المتجر أو الخدمة...';
                    });

                    searchInput.addEventListener('blur', function () {
                        this.parentElement.classList.remove('focused');
                        this.placeholder = `ابحث في متاجر {{ $cityContext['selected_city_name'] ?? 'المدينة' }}... (مطاعم، ملابس، إلكترونيات)`;
                    });

                    // Search suggestions (if implemented)
                    let searchTimeout;
                    searchInput.addEventListener('input', function () {
                        clearTimeout(searchTimeout);
                        const query = this.value.trim();

                        if (query.length > 2) {
                            searchTimeout = setTimeout(() => {
                                // Add search suggestions functionality here
                                console.log('Searching for:', query);
                            }, 300);
                        }
                    });
                }

                // Enhanced shop card interactions
                const shopCards = document.querySelectorAll('.shop-card-modern');
                shopCards.forEach(card => {
                    card.addEventListener('mouseenter', function () {
                        this.style.boxShadow = '0 20px 40px rgba(0,0,0,0.15)';
                    });

                    card.addEventListener('mouseleave', function () {
                        this.style.boxShadow = '';
                    });
                });

                // Category card hover effects
                const categoryCards = document.querySelectorAll('.category-card-modern');
                categoryCards.forEach(card => {
                    card.addEventListener('mouseenter', function () {
                        const icon = this.querySelector('i');
                        if (icon) {
                            icon.style.transform = 'scale(1.2)';
                            icon.style.transition = 'transform 0.3s ease';
                        }
                    });

                    card.addEventListener('mouseleave', function () {
                        const icon = this.querySelector('i');
                        if (icon) {
                            icon.style.transform = 'scale(1)';
                        }
                    });
                });

                // Stats counter animation
                const observerOptions = {
                    threshold: 0.5,
                    rootMargin: '0px 0px -50px 0px'
                };

                const observer = new IntersectionObserver(function (entries) {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            const counters = entry.target.querySelectorAll('.stat-number-modern');
                            counters.forEach(counter => {
                                animateCounter(counter);
                            });
                            observer.unobserve(entry.target);
                        }
                    });
                }, observerOptions);

                const statsSection = document.querySelector('.city-stats-dashboard');
                if (statsSection) {
                    observer.observe(statsSection);
                }

                // Counter animation function
                function animateCounter(element) {
                    const target = parseInt(element.textContent.replace(/,/g, ''));
                    const duration = 2000;
                    const step = target / (duration / 16);
                    let current = 0;

                    const timer = setInterval(() => {
                        current += step;
                        if (current >= target) {
                            current = target;
                            clearInterval(timer);
                        }

                        // Format number with commas
                        element.textContent = Math.floor(current).toLocaleString('ar-EG');
                    }, 16);
                }

                // Smooth scroll for internal links
                document.querySelectorAll('a[href^="#"]').forEach(anchor => {
                    anchor.addEventListener('click', function (e) {
                        e.preventDefault();
                        const target = document.querySelector(this.getAttribute('href'));
                        if (target) {
                            target.scrollIntoView({
                                behavior: 'smooth',
                                block: 'start'
                            });
                        }
                    });
                });

                // Enhanced loading states for action buttons
                const actionButtons = document.querySelectorAll('.btn[href]');
                actionButtons.forEach(button => {
                    button.addEventListener('click', function (e) {
                        // Add loading state for external links
                        if (this.hostname !== window.location.hostname) {
                            const originalText = this.innerHTML;
                            this.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري التحميل...';
                            this.disabled = true;

                            setTimeout(() => {
                                this.innerHTML = originalText;
                                this.disabled = false;
                            }, 3000);
                        }
                    });
                });

                // Search form enhancement
                const searchForm = document.querySelector('.search-form-modern');
                if (searchForm) {
                    searchForm.addEventListener('submit', function (e) {
                        const input = this.querySelector('input[name="q"]');
                        if (!input.value.trim()) {
                            e.preventDefault();
                            input.focus();
                            input.classList.add('is-invalid');
                            setTimeout(() => {
                                input.classList.remove('is-invalid');
                            }, 2000);
                        }
                    });
                }

                // Lazy loading for shop images
                if ('IntersectionObserver' in window) {
                    const imageObserver = new IntersectionObserver((entries, observer) => {
                        entries.forEach(entry => {
                            if (entry.isIntersecting) {
                                const img = entry.target;
                                if (img.dataset.src) {
                                    img.src = img.dataset.src;
                                    img.removeAttribute('data-src');
                                    observer.unobserve(img);
                                }
                            }
                        });
                    });

                    document.querySelectorAll('img[data-src]').forEach(img => {
                        imageObserver.observe(img);
                    });
                }

                // Enhanced keyboard navigation
                document.addEventListener('keydown', function (e) {
                    // Quick search with Ctrl+K or Cmd+K
                    if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                        e.preventDefault();
                        const searchInput = document.getElementById('city-search-modern');
                        if (searchInput) {
                            searchInput.focus();
                            searchInput.select();
                        }
                    }
                });

                // Performance optimization: Debounced resize handler
                let resizeTimeout;
                window.addEventListener('resize', function () {
                    clearTimeout(resizeTimeout);
                    resizeTimeout = setTimeout(() => {
                        // Recalculate layouts if needed
                        console.log('Layout recalculated');
                    }, 250);
                });

                // Enhanced error handling for images
                document.querySelectorAll('.shop-image-modern img').forEach(img => {
                    img.addEventListener('error', function () {
                        this.parentElement.innerHTML = `
                        <div class="w-100 h-100 d-flex align-items-center justify-content-center" 
                             style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                            <i class="fas fa-store text-white" style="font-size: 3rem;"></i>
                        </div>
                    `;
                    });
                });

                console.log('🏙️ City Landing Page Enhanced - Ready!');
            });

            // Utility function to show notifications
            function showNotification(message, type = 'info') {
                const notification = document.createElement('div');
                notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
                notification.style.cssText = 'top: 20px; right: 20px; z-index: 9999; max-width: 300px;';
                notification.innerHTML = `
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            `;

                document.body.appendChild(notification);

                setTimeout(() => {
                    notification.remove();
                }, 5000);
            }

            // Function to track user interactions (for analytics)
            function trackInteraction(action, category = 'City Landing') {
                // Add your analytics tracking code here
                console.log(`Analytics: ${category} - ${action}`);
            }

            // Get Directions using coordinates or address
            function getDirections(latitude, longitude, address) {
                event.preventDefault();
                event.stopPropagation();
                
                if (latitude && longitude) {
                    // Use coordinates for precise location
                    window.open(`https://www.google.com/maps/dir/?api=1&destination=${latitude},${longitude}`, '_blank');
                } else if (address) {
                    // Fallback to address search
                    const encodedAddress = encodeURIComponent(address);
                    window.open(`https://www.google.com/maps/search/${encodedAddress}`, '_blank');
                } else {
                    alert('عذراً، الموقع غير متوفر');
                }
            }

            // Toggle Favorite Shop
            function toggleFavoriteShop(shopId) {
                event.preventDefault();
                event.stopPropagation();
                
                // Check if user is logged in
                @auth
                    // Add your favorite toggle logic here
                    showNotification('تم إضافة المتجر للمفضلة', 'success');
                    console.log('Toggle favorite for shop:', shopId);
                @else
                    showNotification('يرجى تسجيل الدخول أولاً', 'warning');
                    setTimeout(() => {
                        window.location.href = '{{ route("login") }}';
                    }, 1500);
                @endauth
            }

            // Save current city to localStorage and cookie for persistence
            document.addEventListener('DOMContentLoaded', function () {
                const citySlug = '{{ $selectedCity->slug ?? "" }}';
                const cityName = '{{ $cityContext["selected_city_name"] ?? "" }}';

                if (citySlug) {
                    // Save to localStorage
                    localStorage.setItem('selectedCity', citySlug);
                    localStorage.setItem('selectedCityName', cityName);
                    localStorage.setItem('citySelectedAt', new Date().toISOString());

                    // Save to cookie (30 days)
                    const expires = new Date();
                    expires.setTime(expires.getTime() + (30 * 24 * 60 * 60 * 1000));
                    document.cookie = 'selected_city_slug=' + citySlug + ';expires=' + expires.toUTCString() + ';path=/';
                }

                // Shop Suggestion Modal Handler
                loadCitiesAndCategories();

                // Mobile Info Carousel Auto-Rotation
                initMobileCarousel();
            });

            // Mobile Carousel Auto-Rotation
            function initMobileCarousel() {
                const carousel = document.querySelector('.mobile-info-carousel');
                if (!carousel) return;

                const slides = carousel.querySelectorAll('.mobile-info-slide');
                if (slides.length <= 1) return;

                let currentIndex = 0;

                function showNextSlide() {
                    slides[currentIndex].classList.remove('active');
                    currentIndex = (currentIndex + 1) % slides.length;
                    slides[currentIndex].classList.add('active');
                }

                // Auto-rotate every 3 seconds
                setInterval(showNextSlide, 3000);

                // Pause on hover
                carousel.addEventListener('mouseenter', function() {
                    clearInterval(window.carouselInterval);
                });

                carousel.addEventListener('mouseleave', function() {
                    window.carouselInterval = setInterval(showNextSlide, 3000);
                });

                window.carouselInterval = setInterval(showNextSlide, 3000);
            }

            // Load Cities and Categories for Suggestion Modal
            function loadCitiesAndCategories() {
                fetch('{{ route("suggest-shop.data") }}')
                    .then(response => response.json())
                    .then(data => {
                        const citySelect = document.getElementById('citySelect');
                        const categorySelect = document.getElementById('categorySelect');
                        
                        // Populate cities
                        data.cities.forEach(city => {
                            const option = document.createElement('option');
                            option.value = city.id;
                            option.textContent = city.name;
                            if (city.id == {{ $selectedCity->id ?? 'null' }}) {
                                option.selected = true;
                            }
                            citySelect.appendChild(option);
                        });

                        // Populate categories
                        data.categories.forEach(category => {
                            const option = document.createElement('option');
                            option.value = category.id;
                            option.textContent = category.name;
                            categorySelect.appendChild(option);
                        });
                    })
                    .catch(error => {
                        console.error('Error loading data:', error);
                    });
            }

            // Submit Shop Suggestion
            document.getElementById('submitSuggestion').addEventListener('click', function() {
                const form = document.getElementById('suggestShopForm');
                const formData = new FormData(form);
                const button = this;
                
                // Disable button and show loading
                button.disabled = true;
                button.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>جاري الإرسال...';

                fetch('{{ route("suggest-shop.store") }}', {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        showNotification(data.message, 'success');
                        
                        // Close modal and reset form
                        const modal = bootstrap.Modal.getInstance(document.getElementById('suggestShopModal'));
                        modal.hide();
                        form.reset();
                    } else {
                        showNotification(data.message || 'حدث خطأ أثناء الإرسال', 'danger');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    showNotification('حدث خطأ أثناء الإرسال. يرجى المحاولة مرة أخرى', 'danger');
                })
                .finally(() => {
                    // Re-enable button
                    button.disabled = false;
                    button.innerHTML = '<i class="fas fa-paper-plane me-2"></i>إرسال الاقتراح';
                });
            });
        </script>
    @endpush
@endsection