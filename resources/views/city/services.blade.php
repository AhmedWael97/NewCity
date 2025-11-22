@extends('layouts.app')

@php
    $seoData = $seoData ?? [];
    $cityContext = $cityContext ?? ['selected_city_name' => 'مدينة'];
    $selectedCity = $cityContext['selected_city'] ?? null;
@endphp

@section('title', $seoData['title'] ?? "خدمات محلية في {$cityContext['selected_city_name']}")
@section('description', $seoData['description'] ?? "اكتشف أفضل الخدمات المحلية في {$cityContext['selected_city_name']}")

@section('content')
    <main class="city-services-page">
        {{-- Page Header --}}
        <section class="services-hero bg-gradient-primary text-white py-5">
            <div class="container">
                {{-- Breadcrumb --}}
                <nav aria-label="breadcrumb" class="mb-4">
                    <ol class="breadcrumb bg-transparent mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ url('/') }}" class="text-white text-decoration-none">
                                <i class="fas fa-home me-1"></i>الرئيسية
                            </a>
                        </li>
                        <li class="breadcrumb-item">
                            <a href="{{ route('city.landing', $selectedCity->slug ?? 'all') }}" class="text-white text-decoration-none">
                                {{ $cityContext['selected_city_name'] }}
                            </a>
                        </li>
                        <li class="breadcrumb-item active text-white" aria-current="page">الخدمات</li>
                    </ol>
                </nav>

                {{-- Page Title --}}
                <div class="text-center">
                    <h1 class="display-4 fw-bold mb-3">
                        <i class="fas fa-tools me-2"></i>
                        خدمات محلية في {{ $cityContext['selected_city_name'] }}
                    </h1>
                    <p class="lead mb-4">اكتشف أفضل مقدمي الخدمات في منطقتك</p>
                    
                    @auth
                    <a href="{{ route('user.services.create') }}" class="btn btn-light btn-lg rounded-pill px-5">
                        <i class="fas fa-plus me-2"></i>
                        أضف خدمتك
                    </a>
                    @endauth
                </div>
            </div>
        </section>

        {{-- Services Content --}}
        <section class="services-content py-5">
            <div class="container">
                @if($serviceCategoriesWithServices && $serviceCategoriesWithServices->count() > 0)
                    @foreach($serviceCategoriesWithServices as $serviceCategory)
                        <div class="service-category-section mb-5">
                            {{-- Category Header --}}
                            <div class="category-header bg-white rounded-3 p-4 mb-4 shadow-sm">
                                <div class="d-flex align-items-center justify-content-between">
                                    <div class="d-flex align-items-center">
                                        <div class="category-icon-large bg-success bg-opacity-10 rounded-circle p-3 me-3">
                                            <i class="{{ $serviceCategory->icon ?? 'fas fa-wrench' }} text-success" style="font-size: 1.8rem;"></i>
                                        </div>
                                        <div>
                                            <h2 class="h4 mb-1 fw-bold">{{ $serviceCategory->name_ar }}</h2>
                                            <p class="text-muted mb-0">
                                                <i class="fas fa-concierge-bell me-1"></i>
                                                {{ $serviceCategory->services_count }} خدمة متاحة
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Services Grid --}}
                            <div class="row g-4">
                                @forelse($serviceCategory->userServices as $service)
                                    <div class="col-lg-3 col-md-6">
                                        <div class="service-card bg-white rounded-3 shadow-sm h-100 overflow-hidden">
                                            {{-- Service Image --}}
                                            @if($service->images && is_array($service->images) && count($service->images) > 0)
                                                <div class="service-image" style="height: 200px; overflow: hidden;">
                                                    <img src="{{ $service->images[0] }}" 
                                                         alt="{{ $service->title }}" 
                                                         class="w-100 h-100 object-fit-cover"
                                                         onerror="this.style.display='none'; this.parentElement.innerHTML='<div class=&quot;bg-light d-flex align-items-center justify-content-center h-100&quot;><i class=&quot;fas fa-wrench text-muted&quot; style=&quot;font-size: 3rem;&quot;></i></div>'">
                                                </div>
                                            @else
                                                <div class="service-image bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                                    <i class="{{ $serviceCategory->icon ?? 'fas fa-wrench' }} text-muted" style="font-size: 3rem;"></i>
                                                </div>
                                            @endif
                                            
                                            {{-- Service Info --}}
                                            <div class="p-3">
                                                {{-- Provider Info --}}
                                                <div class="d-flex align-items-center mb-2">
                                                    <div class="service-provider-avatar bg-primary bg-opacity-10 rounded-circle p-2 me-2">
                                                        <i class="fas fa-user text-primary"></i>
                                                    </div>
                                                    <small class="text-muted fw-medium">{{ $service->user->name }}</small>
                                                </div>
                                                
                                                {{-- Service Title --}}
                                                <h5 class="service-title h6 mb-2 fw-bold">
                                                    <a href="{{ route('user.services.show', $service->slug) }}" class="text-decoration-none text-dark stretched-link">
                                                        {{ Str::limit($service->title, 45) }}
                                                    </a>
                                                </h5>
                                                
                                                {{-- Service Description --}}
                                                <p class="service-description text-muted small mb-3">
                                                    {{ Str::limit($service->description, 70) }}
                                                </p>
                                                
                                                {{-- Service Footer --}}
                                                <div class="d-flex align-items-center justify-content-between border-top pt-3">
                                                    <div class="service-price">
                                                        @if($service->price_type === 'fixed')
                                                            <span class="text-success fw-bold">{{ number_format($service->price) }} جنيه</span>
                                                        @elseif($service->price_type === 'hourly')
                                                            <span class="text-success fw-bold">{{ number_format($service->price) }} جنيه/ساعة</span>
                                                        @else
                                                            <span class="text-muted"><i class="fas fa-handshake me-1"></i>تفاوض</span>
                                                        @endif
                                                    </div>
                                                    <div class="service-rating">
                                                        @if($service->average_rating > 0)
                                                            <span class="text-warning">
                                                                <i class="fas fa-star"></i>
                                                                {{ number_format($service->average_rating, 1) }}
                                                            </span>
                                                        @else
                                                            <span class="text-muted small">جديد</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="col-12">
                                        <div class="text-center py-5">
                                            <i class="fas fa-tools text-muted mb-3" style="font-size: 3rem;"></i>
                                            <h5 class="text-muted mb-2">لا توجد خدمات في هذه الفئة</h5>
                                            <p class="text-muted">سيتم إضافة المزيد من الخدمات قريباً</p>
                                        </div>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    @endforeach
                @else
                    {{-- Empty State --}}
                    <div class="empty-state bg-white rounded-3 p-5 text-center shadow-sm">
                        <div class="empty-icon mb-4">
                            <i class="fas fa-tools text-muted" style="font-size: 4rem;"></i>
                        </div>
                        <h3 class="h4 mb-3 fw-bold">لا توجد خدمات متاحة حالياً</h3>
                        <p class="text-muted mb-4">كن أول من يضيف خدمة في {{ $cityContext['selected_city_name'] }}</p>
                        @auth
                        <a href="{{ route('user.services.create') }}" class="btn btn-primary btn-lg rounded-pill px-5">
                            <i class="fas fa-plus me-2"></i>
                            أضف خدمتك الآن
                        </a>
                        @else
                        <a href="{{ route('login') }}" class="btn btn-primary btn-lg rounded-pill px-5">
                            <i class="fas fa-sign-in-alt me-2"></i>
                            سجل دخول لإضافة خدمتك
                        </a>
                        @endauth
                    </div>
                @endif
            </div>
        </section>

        {{-- CTA Section --}}
        <section class="cta-section bg-light py-5">
            <div class="container">
                <div class="row align-items-center">
                    <div class="col-lg-8">
                        <h3 class="h4 mb-2 fw-bold">هل تقدم خدمة في {{ $cityContext['selected_city_name'] }}؟</h3>
                        <p class="text-muted mb-0">انضم الآن وابدأ في الوصول إلى عملاء جدد في منطقتك</p>
                    </div>
                    <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
                        @auth
                        <a href="{{ route('user.services.create') }}" class="btn btn-success btn-lg rounded-pill px-5">
                            <i class="fas fa-plus me-2"></i>
                            أضف خدمتك مجاناً
                        </a>
                        @else
                        <a href="{{ route('register') }}" class="btn btn-success btn-lg rounded-pill px-5">
                            <i class="fas fa-user-plus me-2"></i>
                            سجل الآن
                        </a>
                        @endauth
                    </div>
                </div>
            </div>
        </section>
    </main>

    {{-- City Selection Modal --}}
    <x-city-selection-modal-enhanced :cities="$cities" :selected-city="$selectedCity" />
@endsection

@push('styles')
    <style>
        .city-services-page {
            font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            direction: rtl;
            background: #f8f9fa;
        }

        .services-hero {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            position: relative;
            overflow: hidden;
        }

        .services-hero::before {
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

        .service-card {
            border: 1px solid rgba(0, 0, 0, 0.08);
            transition: all 0.3s ease;
            position: relative;
        }

        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
        }

        .service-image img {
            transition: transform 0.3s ease;
        }

        .service-card:hover .service-image img {
            transform: scale(1.05);
        }

        .service-provider-avatar {
            width: 32px;
            height: 32px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .category-header {
            border: 1px solid rgba(0, 0, 0, 0.05);
        }

        .category-icon-large {
            width: 70px;
            height: 70px;
            display: flex;
            align-items: center;
            justify-content: center;
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

        @media (max-width: 768px) {
            .services-hero h1 {
                font-size: 1.8rem;
            }
            
            .services-hero .lead {
                font-size: 1rem;
            }
        }
    </style>
@endpush
