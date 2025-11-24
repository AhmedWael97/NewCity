@extends('layouts.app')

@section('content')
<div class="upgrade-account-page">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-10">
                <!-- Alert Card -->
                <div class="card shadow-lg border-0 mb-4">
                    <div class="card-body text-center py-5">
                        <div class="upgrade-icon mb-4">
                            <i class="fas fa-store-alt" style="font-size: 5rem; color: #4e73df;"></i>
                        </div>
                        
                        <h1 class="display-5 fw-bold mb-3 text-primary">
                            قم بترقية حسابك إلى صاحب متجر
                        </h1>
                        
                        <p class="lead text-muted mb-4">
                            حسابك الحالي من نوع "مستخدم عادي" ولا يمكنك إضافة متاجر
                        </p>
                        
                        <div class="alert alert-info d-inline-block mb-4" role="alert">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>للاستمرار في إضافة متجر:</strong>
                            <br>
                            يجب ترقية حسابك إلى "صاحب متجر" واختيار خطة اشتراك مناسبة
                        </div>
                        
                        <div class="upgrade-benefits mb-4">
                            <h4 class="mb-3">مميزات حساب صاحب المتجر:</h4>
                            <div class="row g-3">
                                <div class="col-md-4">
                                    <div class="benefit-item p-3 bg-light rounded">
                                        <i class="fas fa-check-circle text-success fs-3 mb-2"></i>
                                        <p class="mb-0">إضافة وإدارة متاجرك</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="benefit-item p-3 bg-light rounded">
                                        <i class="fas fa-chart-line text-success fs-3 mb-2"></i>
                                        <p class="mb-0">تحليلات وإحصائيات</p>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div class="benefit-item p-3 bg-light rounded">
                                        <i class="fas fa-star text-success fs-3 mb-2"></i>
                                        <p class="mb-0">ميزات مميزة للترويج</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <form action="{{ route('shop-owner.upgrade') }}" method="POST" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-lg px-5 py-3 shadow-sm">
                                <i class="fas fa-arrow-up me-2"></i>
                                ترقية الحساب الآن
                            </button>
                        </form>
                        
                        <div class="mt-3">
                            <a href="{{ url()->previous() }}" class="btn btn-link text-muted">
                                <i class="fas fa-arrow-right me-1"></i>
                                العودة للصفحة السابقة
                            </a>
                        </div>
                    </div>
                </div>
                
                <!-- Subscription Plans Preview -->
                @if($subscriptionPlans->count() > 0)
                <div class="card shadow border-0">
                    <div class="card-header bg-gradient-primary text-white py-3">
                        <h3 class="mb-0 text-center">
                            <i class="fas fa-crown me-2"></i>
                            خطط الاشتراك المتاحة
                        </h3>
                    </div>
                    <div class="card-body p-4">
                        <div class="row g-4">
                            @foreach($subscriptionPlans as $plan)
                            <div class="col-md-4">
                                <div class="subscription-plan-card h-100 border rounded p-4 text-center hover-shadow">
                                    <div class="plan-icon mb-3">
                                        <i class="fas fa-box text-primary" style="font-size: 2.5rem;"></i>
                                    </div>
                                    <h4 class="fw-bold mb-2">{{ $plan->name }}</h4>
                                    <p class="text-muted small mb-3">{{ $plan->description }}</p>
                                    
                                    <div class="plan-price mb-3">
                                        <div class="mb-2">
                                            <span class="h3 fw-bold text-primary">{{ number_format($plan->monthly_price, 0) }}</span>
                                            <span class="text-muted">جنيه / شهر</span>
                                        </div>
                                        @if($plan->yearly_price > 0)
                                        <div class="text-muted small">
                                            أو {{ number_format($plan->yearly_price, 0) }} جنيه / سنة
                                        </div>
                                        @endif
                                    </div>
                                    
                                    <div class="plan-features text-start mb-3">
                                        @if($plan->features && is_array($plan->features))
                                            @foreach($plan->features as $feature)
                                            <div class="feature-item mb-2">
                                                <i class="fas fa-check text-success me-2"></i>
                                                <span class="small">{{ $feature }}</span>
                                            </div>
                                            @endforeach
                                        @endif
                                    </div>
                                    
                                    <div class="plan-limits small text-muted">
                                        <div class="mb-1">
                                            <i class="fas fa-store me-1"></i>
                                            {{ $plan->max_shops }} متجر
                                        </div>
                                        <div>
                                            <i class="fas fa-images me-1"></i>
                                            {{ $plan->max_images_per_shop }} صورة لكل متجر
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                        
                        <div class="text-center mt-4">
                            <p class="text-muted mb-0">
                                <i class="fas fa-info-circle me-1"></i>
                                بعد ترقية حسابك، ستتمكن من اختيار الخطة المناسبة لك
                            </p>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .upgrade-account-page {
        background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
        min-height: 100vh;
    }
    
    .bg-gradient-primary {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
    }
    
    .hover-shadow {
        transition: all 0.3s ease;
    }
    
    .hover-shadow:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.15) !important;
    }
    
    .subscription-plan-card {
        background: white;
        transition: all 0.3s ease;
    }
    
    .subscription-plan-card:hover {
        border-color: #4e73df !important;
        box-shadow: 0 5px 15px rgba(78, 115, 223, 0.3);
    }
    
    .benefit-item {
        transition: all 0.2s ease;
    }
    
    .benefit-item:hover {
        background-color: #e9ecef !important;
        transform: translateY(-2px);
    }
    
    .btn-primary {
        background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        border: none;
        transition: all 0.3s ease;
    }
    
    .btn-primary:hover {
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(78, 115, 223, 0.4);
    }
</style>
@endsection
