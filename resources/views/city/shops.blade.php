@extends('layouts.app')

@php
    $seoData = $seoData ?? [];
@endphp

@section('title', $seoData['title'] ?? 'المتاجر')
@section('description', $seoData['description'] ?? 'اكتشف أفضل المتاجر')

@section('content')
    <main class="city-shops-page">
        {{-- Page Header --}}
        <section class="shops-hero bg-gradient-primary text-white py-5">
            <div class="container">
                {{-- Breadcrumb --}}
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb bg-transparent mb-0">
                        @foreach($seoData['breadcrumbs'] ?? [] as $crumb)
                            @if($loop->last)
                                <li class="breadcrumb-item active text-white" aria-current="page">{{ $crumb['name'] }}</li>
                            @else
                                <li class="breadcrumb-item">
                                    <a href="{{ $crumb['url'] }}" class="text-white text-decoration-none">
                                        @if($loop->first)<i class="fas fa-home me-1"></i>@endif{{ $crumb['name'] }}
                                    </a>
                                </li>
                            @endif
                        @endforeach
                    </ol>
                </nav>

                {{-- Page Title --}}
                <div class="text-center">
                    <h1 class="display-4 fw-bold mb-3">
                        <i class="fas fa-store me-2"></i>
                        جميع المتاجر في {{ $city->name }}
                    </h1>
                    <p class="lead mb-4">اكتشف {{ number_format($shops->total()) }} متجر معتمد في {{ $city->name }}</p>
                </div>
            </div>
        </section>

        {{-- Search and Filters Section --}}
        <section class="filters-section py-4 bg-light">
            <div class="container">
                {{-- Enhanced Search and Filters --}}
                <form method="GET">
                    <div class="bg-white rounded-3 shadow-sm p-4">
                        <div class="row g-3">
                            {{-- Search Input --}}
                            <div class="col-md-5">
                                <label class="form-label fw-medium">
                                    <i class="fas fa-search me-1"></i> البحث
                                </label>
                                <input 
                                    type="text" 
                                    name="q" 
                                    value="{{ $filters['search'] ?? '' }}"
                                    placeholder="ابحث عن متجر، منتج، أو خدمة..."
                                    class="form-control form-control-lg"
                                >
                            </div>
                            
                            {{-- Category Filter --}}
                            <div class="col-md-3">
                                <label class="form-label fw-medium">
                                    <i class="fas fa-th-large me-1"></i> الفئة
                                </label>
                                <select name="category" class="form-select form-select-lg">
                                    <option value="">جميع الفئات</option>
                                    @foreach($categories as $category)
                                        <option 
                                            value="{{ $category->id }}" 
                                            {{ ($filters['category_id'] ?? '') == $category->id ? 'selected' : '' }}
                                        >
                                            {{ $category->name }} ({{ $category->shops_count ?? 0 }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            
                            {{-- Rating Filter --}}
                            <div class="col-md-2">
                                <label class="form-label fw-medium">
                                    <i class="fas fa-star me-1"></i> التقييم
                                </label>
                                <select name="rating" class="form-select form-select-lg">
                                    <option value="">الكل</option>
                                    <option value="4" {{ ($filters['min_rating'] ?? '') == 4 ? 'selected' : '' }}>4+ نجوم</option>
                                    <option value="3" {{ ($filters['min_rating'] ?? '') == 3 ? 'selected' : '' }}>3+ نجوم</option>
                                    <option value="2" {{ ($filters['min_rating'] ?? '') == 2 ? 'selected' : '' }}>2+ نجوم</option>
                                </select>
                            </div>
                            
                            {{-- Search Button --}}
                            <div class="col-md-2 d-flex align-items-end">
                                <button type="submit" class="btn btn-primary btn-lg w-100">
                                    <i class="fas fa-search me-1"></i> بحث
                                </button>
                            </div>
                        </div>
                        
                        {{-- Active Filters Display --}}
                        @if(($filters['search'] ?? '') || ($filters['category_id'] ?? '') || ($filters['min_rating'] ?? ''))
                            <div class="mt-3 pt-3 border-top">
                                <div class="d-flex flex-wrap align-items-center gap-2">
                                    <span class="small fw-medium text-muted">الفلاتر النشطة:</span>
                                    
                                    @if($filters['search'] ?? '')
                                        <span class="badge bg-primary">
                                            بحث: "{{ $filters['search'] }}"
                                            <a href="{{ route('city.shops.index', array_merge(request()->except('q'), ['city' => $city->slug])) }}" class="text-white ms-1">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        </span>
                                    @endif
                                    
                                    @if($filters['category_id'] ?? '')
                                        @php
                                            $selectedCategory = $categories->find($filters['category_id']);
                                        @endphp
                                        @if($selectedCategory)
                                            <span class="badge bg-success">
                                                فئة: {{ $selectedCategory->name }}
                                                <a href="{{ route('city.shops.index', array_merge(request()->except('category'), ['city' => $city->slug])) }}" class="text-white ms-1">
                                                    <i class="fas fa-times"></i>
                                                </a>
                                            </span>
                                        @endif
                                    @endif
                                    
                                    @if($filters['min_rating'] ?? '')
                                        <span class="badge bg-warning text-dark">
                                            تقييم: {{ $filters['min_rating'] }}+ نجوم
                                            <a href="{{ route('city.shops.index', array_merge(request()->except('rating'), ['city' => $city->slug])) }}" class="text-dark ms-1">
                                                <i class="fas fa-times"></i>
                                            </a>
                                        </span>
                                    @endif
                                    
                                    <a href="{{ route('city.shops.index', ['city' => $city->slug]) }}" class="small text-danger fw-medium">
                                        <i class="fas fa-redo me-1"></i> إعادة تعيين الكل
                                    </a>
                                </div>
                            </div>
                        @endif
                        
                        {{-- Sort Options --}}
                        <div class="mt-3 pt-3 border-top">
                            <div class="d-flex flex-wrap align-items-center gap-2">
                                <span class="small fw-medium text-muted">ترتيب حسب:</span>
                                <div class="d-flex flex-wrap gap-2">
                                    <a href="{{ route('city.shops.index', array_merge(request()->except('sort'), ['city' => $city->slug, 'sort' => 'featured'])) }}" 
                                       class="badge {{ request('sort') == 'featured' || !request('sort') ? 'bg-primary' : 'bg-light text-dark' }} text-decoration-none">
                                        <i class="fas fa-star"></i> المميزة
                                    </a>
                                    <a href="{{ route('city.shops.index', array_merge(request()->except('sort'), ['city' => $city->slug, 'sort' => 'rating'])) }}" 
                                       class="badge {{ request('sort') == 'rating' ? 'bg-primary' : 'bg-light text-dark' }} text-decoration-none">
                                        <i class="fas fa-thumbs-up"></i> الأعلى تقييماً
                                    </a>
                                    <a href="{{ route('city.shops.index', array_merge(request()->except('sort'), ['city' => $city->slug, 'sort' => 'newest'])) }}" 
                                       class="badge {{ request('sort') == 'newest' ? 'bg-primary' : 'bg-light text-dark' }} text-decoration-none">
                                        <i class="fas fa-clock"></i> الأحدث
                                    </a>
                                    <a href="{{ route('city.shops.index', array_merge(request()->except('sort'), ['city' => $city->slug, 'sort' => 'name'])) }}" 
                                       class="badge {{ request('sort') == 'name' ? 'bg-primary' : 'bg-light text-dark' }} text-decoration-none">
                                        <i class="fas fa-sort-alpha-down"></i> الاسم
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </section>
        </section>

        {{-- Shops Grid --}}
        <section class="shops-content py-5">
            <div class="container">
                @if($shops->count() > 0)
                    <div class="row g-4">
                        @foreach($shops as $shop)
                            <div class="col-lg-4 col-md-6">
                                <x-shop-card :shop="$shop" :city-name="$city->name" />
                            </div>
                        @endforeach
                    </div>
                    
                    {{-- Pagination --}}
                    <div class="mt-5">
                        <div class="d-flex justify-content-center">
                            {{ $shops->onEachSide(1)->links('pagination::bootstrap-5') }}
                        </div>
                    </div>
                @else
                    {{-- Empty State --}}
                    <div class="empty-state bg-white rounded-3 p-5 text-center shadow-sm">
                        <div class="empty-icon mb-4">
                            <i class="fas fa-store text-muted" style="font-size: 4rem;"></i>
                        </div>
                        <h3 class="h4 mb-3 fw-bold">لم نجد متاجر مطابقة</h3>
                        <p class="text-muted mb-4">جرب تغيير معايير البحث أو تصفح جميع المتاجر</p>
                        <div class="d-flex flex-wrap gap-3 justify-content-center">
                            <a href="{{ route('city.shops.index', $city->slug) }}" class="btn btn-primary btn-lg rounded-pill px-5">
                                <i class="fas fa-store me-2"></i>عرض جميع المتاجر
                            </a>
                            <a href="{{ route('city.landing', $city->slug) }}" class="btn btn-outline-secondary btn-lg rounded-pill px-5">
                                <i class="fas fa-arrow-right me-2"></i>العودة لصفحة المدينة
                            </a>
                        </div>
                    </div>
                @endif
            </div>
        </section>

        {{-- Quick Links --}}
        <section class="quick-links py-5 bg-light border-top">
            <div class="container">
                <div class="text-center mb-4">
                    <h3 class="h4 fw-bold">روابط سريعة</h3>
                </div>
                
                <div class="row g-3">
                    <div class="col-6 col-md-3">
                        <a href="{{ route('city.landing', $city->slug) }}" 
                           class="d-block bg-white hover-shadow rounded-3 p-4 text-center text-decoration-none border">
                            <div class="fs-2 mb-2">🏙️</div>
                            <div class="fw-medium text-dark">صفحة المدينة</div>
                        </a>
                    </div>
                    
                    <div class="col-6 col-md-3">
                        <a href="{{ route('city.shops.featured', $city->slug) }}" 
                           class="d-block bg-white hover-shadow rounded-3 p-4 text-center text-decoration-none border">
                            <div class="fs-2 mb-2">⭐</div>
                            <div class="fw-medium text-dark">المتاجر المميزة</div>
                        </a>
                    </div>
                    
                    <div class="col-6 col-md-3">
                        <a href="{{ route('city.categories.index', $city->slug) }}" 
                           class="d-block bg-white hover-shadow rounded-3 p-4 text-center text-decoration-none border">
                            <div class="fs-2 mb-2">📂</div>
                            <div class="fw-medium text-dark">تصفح الفئات</div>
                        </a>
                    </div>
                    
                    <div class="col-6 col-md-3">
                        <a href="{{ route('cities.index') }}" 
                           class="d-block bg-white hover-shadow rounded-3 p-4 text-center text-decoration-none border">
                            <div class="fs-2 mb-2">🗺️</div>
                            <div class="fw-medium text-dark">مدن أخرى</div>
                        </a>
                    </div>
                </div>
            </div>
        </section>
    </main>
@endsection

@push('styles')
    <style>
        .small.text-muted {
            display: none;
        }
        .city-shops-page {
            font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            direction: rtl;
            background: #f8f9fa;
        }

        .shops-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
        }

        .shops-hero::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.03'%3E%3Ccircle cx='30' cy='30' r='2'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
            opacity: 0.5;
        }

        .breadcrumb-item + .breadcrumb-item::before {
            content: "‹";
            color: rgba(255, 255, 255, 0.7);
        }

        .shop-card {
            border: 1px solid rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            position: relative;
        }

        .shop-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
        }

        .shop-image img {
            transition: transform 0.3s ease;
        }

        .shop-card:hover .shop-image img {
            transform: scale(1.05);
        }

        .object-fit-cover {
            object-fit: cover;
        }

        .stretched-link::after {
            position: absolute;
            top: 0;
            right: 0;
            bottom: 0;
            left: 0;
            z-index: 1;
            content: "";
        }

        .hover-shadow {
            transition: all 0.3s ease;
        }

        .hover-shadow:hover {
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1) !important;
            transform: translateY(-3px);
        }

        .empty-state {
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        /* Modern Pagination Styles */
        .pagination {
            gap: 0.5rem;
        }

        /* Hide pagination text */
        .pagination + p {
            display: none;
        }

        .page-link {
            border-radius: 0.5rem !important;
            border: 1px solid #e9ecef;
            color: #667eea;
            font-weight: 500;
            padding: 0.5rem 1rem;
            transition: all 0.3s ease;
        }

        .page-link:hover {
            background-color: #667eea;
            border-color: #667eea;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.3);
        }

        .page-item.active .page-link {
            background-color: #667eea;
            border-color: #667eea;
            color: white;
            box-shadow: 0 4px 12px rgba(102, 126, 234, 0.4);
        }

        .page-item.disabled .page-link {
            background-color: #f8f9fa;
            border-color: #e9ecef;
            color: #6c757d;
        }

        .page-item:first-child .page-link,
        .page-item:last-child .page-link {
            border-radius: 0.5rem !important;
        }

        @media (max-width: 768px) {
            .shops-hero h1 {
                font-size: 1.8rem;
            }
            
            .shops-hero .lead {
                font-size: 1rem;
            }

            .page-link {
                padding: 0.4rem 0.7rem;
                font-size: 0.875rem;
            }
        }
    </style>
@endpush