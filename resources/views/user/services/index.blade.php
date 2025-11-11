@extends('layouts.app')

@section('title', 'خدماتي')

@push('styles')
<style>
    .service-card {
        transition: all 0.3s ease;
        border: 1px solid #e9ecef;
        border-radius: 12px;
        overflow: hidden;
    }
    
    .service-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }
    
    .analytics-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .analytics-number {
        font-size: 2rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }
    
    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-size: 0.875rem;
        font-weight: 600;
    }
    
    .status-pending {
        background: #fff3cd;
        color: #856404;
    }
    
    .status-approved {
        background: #d1edff;
        color: #0c5460;
    }
    
    .status-rejected {
        background: #f8d7da;
        color: #721c24;
    }
    
    .pricing-info {
        background: #f8f9fa;
        padding: 0.75rem;
        border-radius: 8px;
        margin-top: 0.5rem;
    }
    
    .analytics-chart {
        height: 300px;
        background: white;
        border-radius: 8px;
        padding: 1rem;
        margin-top: 1rem;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">خدماتي</h1>
            <p class="text-muted">إدارة وتتبع خدماتك المقدمة</p>
        </div>
        <a href="{{ route('user.services.create') }}" class="btn btn-primary rounded-pill px-4">
            <i class="fas fa-plus me-2"></i>
            إضافة خدمة جديدة
        </a>
    </div>

    <!-- Analytics Overview -->
    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-3">
            <div class="analytics-card">
                <div class="analytics-number">{{ $analytics['total_services'] }}</div>
                <div class="h6 mb-0">إجمالي الخدمات</div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="analytics-card" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                <div class="analytics-number">{{ $analytics['active_services'] }}</div>
                <div class="h6 mb-0">الخدمات النشطة</div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="analytics-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="analytics-number">{{ number_format($analytics['monthly_views']) }}</div>
                <div class="h6 mb-0">مشاهدات هذا الشهر</div>
            </div>
        </div>
        <div class="col-md-3 col-6 mb-3">
            <div class="analytics-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="analytics-number">{{ number_format($analytics['monthly_contacts']) }}</div>
                <div class="h6 mb-0">تواصل هذا الشهر</div>
            </div>
        </div>
    </div>

    <!-- Services List -->
    <div class="card shadow-sm">
        <div class="card-header bg-white border-bottom">
            <h5 class="card-title mb-0">
                <i class="fas fa-list me-2 text-primary"></i>
                قائمة خدماتك
            </h5>
        </div>
        <div class="card-body p-0">
            @if($services->count() > 0)
                <div class="row g-0">
                    @foreach($services as $service)
                        <div class="col-12">
                            <div class="service-card m-3">
                                <div class="row g-0">
                                    <!-- Service Image -->
                                    <div class="col-md-3">
                                        <div class="position-relative">
                                            <img src="{{ $service->first_image }}" 
                                                 alt="{{ $service->title }}" 
                                                 class="w-100 h-100 object-fit-cover"
                                                 style="min-height: 200px;">
                                            
                                            @if($service->is_featured)
                                                <span class="position-absolute top-0 start-0 m-2">
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="fas fa-star me-1"></i>مميزة
                                                    </span>
                                                </span>
                                            @endif
                                            
                                            <!-- Status Badge -->
                                            <span class="position-absolute bottom-0 start-0 m-2">
                                                <span class="status-badge status-{{ $service->status }}">
                                                    @switch($service->status)
                                                        @case('pending')
                                                            <i class="fas fa-clock me-1"></i>قيد المراجعة
                                                            @break
                                                        @case('approved')
                                                            <i class="fas fa-check me-1"></i>معتمدة
                                                            @break
                                                        @case('rejected')
                                                            <i class="fas fa-times me-1"></i>مرفوضة
                                                            @break
                                                    @endswitch
                                                </span>
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <!-- Service Info -->
                                    <div class="col-md-6">
                                        <div class="p-3">
                                            <h5 class="card-title mb-2">{{ $service->title }}</h5>
                                            <p class="text-muted small mb-2">
                                                <i class="fas fa-map-marker-alt me-1"></i>{{ $service->city->name }}
                                                <span class="mx-2">•</span>
                                                <i class="fas fa-tag me-1"></i>{{ $service->serviceCategory->name }}
                                            </p>
                                            <p class="card-text text-muted">{{ Str::limit($service->description, 120) }}</p>
                                            
                                            <!-- Pricing Info -->
                                            <div class="pricing-info">
                                                @switch($service->pricing_type)
                                                    @case('fixed')
                                                        <strong>{{ number_format($service->base_price) }} جنيه - سعر ثابت</strong>
                                                        @break
                                                    @case('hourly')
                                                        <strong>{{ number_format($service->hourly_rate) }} جنيه/ساعة</strong>
                                                        @break
                                                    @case('distance')
                                                        <strong>{{ number_format($service->distance_rate) }} جنيه/كم</strong>
                                                        @break
                                                    @case('negotiable')
                                                        <strong>السعر قابل للتفاوض</strong>
                                                        @break
                                                @endswitch
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Analytics & Actions -->
                                    <div class="col-md-3">
                                        <div class="p-3 h-100 d-flex flex-column">
                                            <!-- Analytics Mini -->
                                            <div class="analytics-mini mb-3">
                                                <div class="row text-center">
                                                    <div class="col-6">
                                                        <div class="text-primary h5 mb-0">{{ number_format($service->monthly_views) }}</div>
                                                        <small class="text-muted">مشاهدات</small>
                                                    </div>
                                                    <div class="col-6">
                                                        <div class="text-success h5 mb-0">{{ number_format($service->monthly_contacts) }}</div>
                                                        <small class="text-muted">تواصل</small>
                                                    </div>
                                                </div>
                                                
                                                @if($service->average_rating > 0)
                                                    <div class="text-center mt-2">
                                                        <div class="text-warning">
                                                            @for($i = 1; $i <= 5; $i++)
                                                                @if($i <= $service->average_rating)
                                                                    <i class="fas fa-star"></i>
                                                                @else
                                                                    <i class="far fa-star"></i>
                                                                @endif
                                                            @endfor
                                                        </div>
                                                        <small class="text-muted">{{ $service->total_reviews }} تقييم</small>
                                                    </div>
                                                @endif
                                            </div>
                                            
                                            <!-- Actions -->
                                            <div class="mt-auto">
                                                <div class="d-grid gap-2">
                                                    <a href="{{ route('user.services.show', $service) }}" 
                                                       class="btn btn-outline-primary btn-sm">
                                                        <i class="fas fa-eye me-1"></i>عرض
                                                    </a>
                                                    
                                                    <a href="{{ route('user.services.edit', $service) }}" 
                                                       class="btn btn-outline-secondary btn-sm">
                                                        <i class="fas fa-edit me-1"></i>تعديل
                                                    </a>
                                                    
                                                    <a href="{{ route('user.services.analytics', $service) }}" 
                                                       class="btn btn-outline-info btn-sm">
                                                        <i class="fas fa-chart-line me-1"></i>تحليلات
                                                    </a>
                                                    
                                                    <form action="{{ route('user.services.toggle-status', $service) }}" 
                                                          method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" 
                                                                class="btn btn-outline-{{ $service->is_active ? 'warning' : 'success' }} btn-sm w-100">
                                                            <i class="fas fa-{{ $service->is_active ? 'pause' : 'play' }} me-1"></i>
                                                            {{ $service->is_active ? 'إيقاف' : 'تفعيل' }}
                                                        </button>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center p-3">
                    {{ $services->links() }}
                </div>
            @else
                <!-- Empty State -->
                <div class="text-center py-5">
                    <i class="fas fa-concierge-bell text-muted mb-3" style="font-size: 4rem;"></i>
                    <h4 class="text-muted mb-3">لا توجد خدمات بعد</h4>
                    <p class="text-muted mb-4">ابدأ بإضافة خدمتك الأولى لتصل إلى المزيد من العملاء</p>
                    <a href="{{ route('user.services.create') }}" class="btn btn-primary rounded-pill px-4">
                        <i class="fas fa-plus me-2"></i>
                        إضافة خدمة جديدة
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Auto-refresh analytics every 5 minutes
    setInterval(function() {
        // You can implement auto-refresh here if needed
    }, 300000);
    
    // Smooth scroll for analytics cards
    document.querySelectorAll('.analytics-card').forEach(card => {
        card.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-3px)';
        });
        
        card.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
        });
    });
});
</script>
@endpush