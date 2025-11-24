@extends('layouts.app')

@section('content')
<div class="subscriptions-container">
    <div class="container py-5">
        <!-- Page Header -->
        <div class="text-center mb-5">
            <h1 class="display-4 fw-bold mb-3">باقات الاشتراك</h1>
            <p class="lead text-muted">اختر الباقة المناسبة لمتجرك وابدأ في جذب المزيد من العملاء</p>
        </div>

        @if(session('info'))
            <div class="alert alert-info alert-dismissible fade show" role="alert">
                {{ session('info') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <!-- Current Subscriptions -->
        @if($userShops->count() > 0)
            <div class="mb-5">
                <h3 class="mb-4">متاجرك الحالية</h3>
                <div class="row g-4">
                    @foreach($userShops as $shop)
                        <div class="col-md-6">
                            <div class="card shadow-sm">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start">
                                        <div>
                                            <h5 class="card-title">{{ $shop->name }}</h5>
                                            <p class="text-muted mb-2">
                                                <i class="fas fa-map-marker-alt"></i> {{ $shop->city->name ?? 'غير محدد' }}
                                            </p>
                                        </div>
                                        @if($shop->activeSubscription)
                                            <span class="badge bg-success">نشط</span>
                                        @else
                                            <span class="badge bg-warning text-dark">بحاجة لاشتراك</span>
                                        @endif
                                    </div>
                                    
                                    @if($shop->activeSubscription)
                                        <div class="mt-3 p-3 bg-light rounded">
                                            <p class="mb-1"><strong>الباقة الحالية:</strong> {{ $shop->activeSubscription->subscriptionPlan->name ?? 'غير محددة' }}</p>
                                            <p class="mb-1"><strong>تنتهي في:</strong> {{ $shop->activeSubscription->ends_at->format('Y-m-d') }}</p>
                                            <p class="mb-0"><strong>الحالة:</strong> {!! $shop->activeSubscription->status_badge !!}</p>
                                        </div>
                                    @else
                                        <div class="mt-3">
                                            <p class="text-danger mb-0">
                                                <i class="fas fa-exclamation-circle"></i> يجب اختيار باقة اشتراك لهذا المتجر
                                            </p>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        <!-- Available Plans -->
        <div class="mb-5">
            <h3 class="text-center mb-4">الباقات المتاحة</h3>
            <div class="row g-4">
                @forelse($plans as $plan)
                    <div class="col-md-4">
                        <div class="card h-100 shadow-sm {{ $plan->is_popular ? 'border-primary' : '' }}">
                            @if($plan->is_popular)
                                <div class="card-header bg-primary text-white text-center">
                                    <strong>الأكثر شعبية</strong>
                                </div>
                            @endif
                            
                            <div class="card-body d-flex flex-column">
                                <div class="text-center mb-4">
                                    <h4 class="card-title fw-bold">{{ $plan->name }}</h4>
                                    <p class="text-muted">{{ $plan->description }}</p>
                                    
                                    <div class="pricing mt-3">
                                        <h2 class="text-primary mb-0">
                                            {{ number_format($plan->monthly_price, 2) }} جنيه
                                        </h2>
                                        <small class="text-muted">شهرياً</small>
                                    </div>
                                    
                                    @if($plan->yearly_price)
                                        <div class="mt-2">
                                            <small class="text-success">
                                                وفر {{ $plan->yearly_savings_percentage }}% مع الاشتراك السنوي
                                                ({{ number_format($plan->yearly_price, 2) }} جنيه/سنة)
                                            </small>
                                        </div>
                                    @endif
                                </div>

                                <!-- Features List -->
                                <div class="features-list mb-4">
                                    <ul class="list-unstyled">
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success"></i>
                                            {{ $plan->max_shops }} {{ $plan->max_shops == 1 ? 'متجر' : 'متاجر' }}
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success"></i>
                                            {{ $plan->max_products_per_shop }} منتج لكل متجر
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success"></i>
                                            {{ $plan->max_services_per_shop }} خدمة لكل متجر
                                        </li>
                                        <li class="mb-2">
                                            <i class="fas fa-check text-success"></i>
                                            {{ $plan->max_images_per_shop }} صورة لكل متجر
                                        </li>
                                        
                                        @if($plan->analytics_access)
                                            <li class="mb-2">
                                                <i class="fas fa-check text-success"></i>
                                                الوصول إلى التحليلات
                                            </li>
                                        @endif
                                        
                                        @if($plan->priority_listing)
                                            <li class="mb-2">
                                                <i class="fas fa-check text-success"></i>
                                                إدراج ذو أولوية
                                            </li>
                                        @endif
                                        
                                        @if($plan->verified_badge)
                                            <li class="mb-2">
                                                <i class="fas fa-check text-success"></i>
                                                شارة التحقق
                                            </li>
                                        @endif
                                        
                                        @if($plan->custom_branding)
                                            <li class="mb-2">
                                                <i class="fas fa-check text-success"></i>
                                                علامة تجارية مخصصة
                                            </li>
                                        @endif
                                    </ul>
                                </div>

                                <!-- Action Button -->
                                <div class="mt-auto text-center">
                                    <a href="{{ route('shop-owner.payment', ['plan' => $plan->id, 'cycle' => 'monthly']) }}" 
                                       class="btn {{ $plan->is_popular ? 'btn-primary' : 'btn-outline-primary' }} w-100">
                                        اختر هذه الباقة
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-warning text-center">
                            <i class="fas fa-exclamation-triangle"></i>
                            لا توجد باقات متاحة حالياً. يرجى التواصل مع الإدارة.
                        </div>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Help Section -->
        <div class="text-center mt-5">
            <p class="text-muted mb-2">هل تحتاج إلى مساعدة في اختيار الباقة المناسبة؟</p>
            <a href="#" class="btn btn-link">تواصل مع فريق الدعم</a>
        </div>
    </div>
</div>

<style>


.card {
    transition: transform 0.3s ease, box-shadow 0.3s ease;
}

.card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
}

.border-primary {
    border-width: 2px !important;
}

.pricing h2 {
    font-size: 2.5rem;
    font-weight: bold;
}

.features-list li {
    padding: 0.5rem 0;
}

.features-list i {
    margin-left: 0.5rem;
}
</style>
@endsection
