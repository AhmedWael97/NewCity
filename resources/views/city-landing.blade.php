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
                                    <img src="{{ asset('storage/' . $selectedCity->image) }}"
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

        {{-- Featured and Popular Shops --}}
        <section class="featured-shops py-5 bg-light">
            <div class="container">
                <div class="row">
                    {{-- Main Content --}}
                    <div class="col-lg-8">
                        @if($categoriesWithShops && $categoriesWithShops->count() > 0)
                            @foreach($categoriesWithShops->take(3) as $index => $category)
                                <div class="category-section-modern mb-5">
                                    {{-- Enhanced Category Header --}}
                                    <div class="category-header-modern bg-white rounded-3 p-4 mb-4 shadow-sm">
                                        <div class="d-flex align-items-center justify-content-between">
                                            <div class="d-flex align-items-center">
                                                <div class="category-icon-large bg-primary bg-opacity-10 rounded-circle p-3 me-3">
                                                    <i class="{{ $category->icon ?? 'fas fa-store' }} text-primary"
                                                        style="font-size: 1.5rem;"></i>
                                                </div>
                                                <div>
                                                    <h3 class="h4 mb-1 fw-bold">{{ $category->name }}</h3>
                                                    <p class="text-muted mb-0">
                                                        <i class="fas fa-store me-1"></i>
                                                        {{ $category->shops_count }} متجر متاح
                                                        @if($category->description)
                                                            • {{ Str::limit($category->description, 50) }}
                                                        @endif
                                                    </p>
                                                </div>
                                            </div>
                                            <a href="{{ route('city.category.shops', ['city' => $selectedCity->slug ?? 'all', 'category' => $category->slug]) }}"
                                                class="btn btn-primary rounded-pill px-4">
                                                <i class="fas fa-arrow-left me-2"></i>
                                                عرض الكل
                                            </a>
                                        </div>
                                    </div>

                                    {{-- Enhanced Shops Grid --}}
                                    <div class="row g-4">
                                        @forelse($category->shops->take(4) as $shop)
                                            <div class="col-lg-6 col-md-6">
                                                <x-shop-card :shop="$shop" :city-name="$cityContext['selected_city_name']" />
                                            </div>

                                        @empty
                                            <div class="col-12">
                                                <div class="text-center py-5">
                                                    <i class="fas fa-store-slash text-muted mb-3" style="font-size: 3rem;"></i>
                                                    <h5 class="text-muted mb-2">لا توجد متاجر في هذه الفئة</h5>
                                                    <p class="text-muted">سيتم إضافة المزيد من المتاجر قريباً</p>
                                                </div>
                                            </div>
                                        @endforelse
                                    </div>
                                </div>

                                {{-- Enhanced Ad Placement --}}
                                @if(($index + 1) % 2 === 0 && !$loop->last)
                                    <div class="my-5">
                                        <x-ad-display type="banner" placement="city_landing" :city-id="$selectedCity->id ?? null"
                                            class="rounded-3 overflow-hidden shadow-sm" />
                                    </div>
                                @endif
                            @endforeach
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

                    {{-- Enhanced Sidebar --}}
                    <div class="col-lg-4">
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
            });
        </script>
    @endpush
@endsection