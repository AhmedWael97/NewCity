@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="text-center mb-5">
        <h2><i class="fas fa-rocket"></i> باقات رعاية الإعلانات</h2>
        <p class="text-muted">زد من ظهور إعلانك واحصل على المزيد من المشاهدات والاتصالات</p>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Item Info -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-2">
                    @if($item->images && count($item->images) > 0)
                    <img src="{{ $item->images[0] }}" alt="{{ $item->title }}" 
                         class="img-fluid rounded" style="height: 100px; object-fit: cover; width: 100%;">
                    @endif
                </div>
                <div class="col-md-6">
                    <h5>{{ $item->title }}</h5>
                    <p class="text-muted mb-0">
                        <i class="fas fa-map-marker-alt"></i> {{ $item->city->name }} •
                        <i class="fas fa-tag"></i> {{ $item->category->name }}
                    </p>
                </div>
                <div class="col-md-4 text-end">
                    <div class="mb-2">
                        <strong>المشاهدات الحالية:</strong> {{ $item->view_count }} / {{ $item->max_views + $item->sponsored_views_boost }}
                    </div>
                    @if($item->remainingViews() > 0)
                    <small class="text-{{ $item->remainingViews() < 10 ? 'danger' : 'success' }}">
                        <i class="fas fa-eye"></i> {{ $item->remainingViews() }} مشاهدة متبقية
                    </small>
                    @else
                    <small class="text-danger">
                        <i class="fas fa-eye-slash"></i> نفذت المشاهدات
                    </small>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Sponsorship Benefits -->
    <div class="alert alert-info">
        <h5><i class="fas fa-star"></i> مميزات الإعلانات المميزة:</h5>
        <ul class="mb-0">
            <li><i class="fas fa-check-circle text-success"></i> ظهور في أعلى نتائج البحث</li>
            <li><i class="fas fa-check-circle text-success"></i> علامة "مميز" على إعلانك</li>
            <li><i class="fas fa-check-circle text-success"></i> زيادة كبيرة في عدد المشاهدات</li>
            <li><i class="fas fa-check-circle text-success"></i> احتمالية أكبر للبيع السريع</li>
        </ul>
    </div>

    <!-- Sponsorship Packages -->
    <div class="row g-4 mb-5">
        @foreach($packages as $package)
        <div class="col-md-4">
            <div class="card h-100 shadow-sm {{ $package['featured'] ? 'border-warning border-3' : '' }}">
                @if($package['featured'])
                <div class="card-header bg-warning text-dark text-center">
                    <i class="fas fa-crown"></i> <strong>الأكثر شعبية</strong>
                </div>
                @endif
                <div class="card-body text-center">
                    <h3 class="mb-3">
                        @switch($package['type'])
                            @case('basic')
                                <i class="fas fa-box text-primary"></i> الباقة الأساسية
                                @break
                            @case('standard')
                                <i class="fas fa-box-open text-success"></i> الباقة القياسية
                                @break
                            @case('premium')
                                <i class="fas fa-gem text-warning"></i> الباقة الماسية
                                @break
                        @endswitch
                    </h3>
                    
                    <div class="display-4 text-primary mb-3">
                        {{ number_format($package['price'], 0) }} <small class="fs-6 text-muted">جنيه</small>
                    </div>

                    <ul class="list-unstyled mb-4">
                        <li class="mb-2">
                            <i class="fas fa-clock text-info"></i> 
                            <strong>{{ $package['duration'] }} يوم</strong> رعاية
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-eye text-success"></i> 
                            <strong>+{{ $package['views_boost'] }}</strong> مشاهدة إضافية
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-arrow-up text-warning"></i> 
                            أولوية <strong>{{ $package['priority'] }}</strong> في الترتيب
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-star text-warning"></i> 
                            علامة "مميز"
                        </li>
                    </ul>

                    <form action="{{ route('marketplace.sponsor.purchase', $item->id) }}" method="POST">
                        @csrf
                        <input type="hidden" name="package_type" value="{{ $package['type'] }}">
                        <button type="submit" class="btn btn-{{ $package['featured'] ? 'warning' : 'primary' }} w-100 btn-lg"
                                onclick="return confirm('هل تريد شراء باقة {{ $package['name'] }} بسعر {{ $package['price'] }} جنيه؟')">
                            <i class="fas fa-shopping-cart"></i> اشترك الآن
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @endforeach
    </div>

    <!-- Current Active Sponsorships -->
    @if($item->activeSponsorship)
    <div class="card border-success shadow">
        <div class="card-header bg-success text-white">
            <h5 class="mb-0"><i class="fas fa-check-circle"></i> الرعاية النشطة حالياً</h5>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-3">
                    <strong>الباقة:</strong> 
                    @switch($item->activeSponsorship->package_type)
                        @case('basic')
                            الباقة الأساسية
                            @break
                        @case('standard')
                            الباقة القياسية
                            @break
                        @case('premium')
                            الباقة الماسية
                            @break
                    @endswitch
                </div>
                <div class="col-md-3">
                    <strong>تاريخ البداية:</strong> {{ $item->activeSponsorship->starts_at->format('Y-m-d') }}
                </div>
                <div class="col-md-3">
                    <strong>تاريخ الانتهاء:</strong> {{ $item->sponsored_until->format('Y-m-d') }}
                </div>
                <div class="col-md-3">
                    <strong>الأيام المتبقية:</strong> 
                    <span class="badge bg-success">{{ now()->diffInDays($item->sponsored_until) }} يوم</span>
                </div>
            </div>
            <div class="row mt-3">
                <div class="col-md-4">
                    <strong>المشاهدات الإضافية:</strong> {{ $item->sponsored_views_boost }}
                </div>
                <div class="col-md-4">
                    <strong>المشاهدات المكتسبة:</strong> {{ $item->activeSponsorship->views_gained }}
                </div>
                <div class="col-md-4">
                    <strong>الاتصالات المكتسبة:</strong> {{ $item->activeSponsorship->contacts_gained }}
                </div>
            </div>
        </div>
    </div>
    @endif

    <!-- FAQ -->
    <div class="card mt-4">
        <div class="card-header">
            <h5><i class="fas fa-question-circle"></i> أسئلة شائعة</h5>
        </div>
        <div class="card-body">
            <div class="accordion" id="faqAccordion">
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                            كيف تعمل الرعاية؟
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse show" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            عند شراء باقة رعاية، يتم نقل إعلانك إلى أعلى نتائج البحث وإضافة علامة "مميز" عليه، مما يزيد من فرص مشاهدته والاتصال بك.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                            ماذا يحدث عند انتهاء الرعاية؟
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            بعد انتهاء مدة الرعاية، يعود إعلانك إلى الترتيب الطبيعي في نتائج البحث. يمكنك تجديد الرعاية في أي وقت.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq3">
                            هل يمكنني تجديد الرعاية قبل انتهائها؟
                        </button>
                    </h2>
                    <div id="faq3" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            نعم، يمكنك شراء باقة جديدة في أي وقت، وستبدأ فترة الرعاية الجديدة بعد انتهاء الفترة الحالية.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#faq4">
                            ماذا لو نفذت المشاهدات المجانية؟
                        </button>
                    </h2>
                    <div id="faq4" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body">
                            عند نفاد المشاهدات المجانية، يتم إخفاء إعلانك من نتائج البحث. الرعاية تمنحك مشاهدات إضافية وتحافظ على ظهور إعلانك.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="text-center mt-4">
        <a href="{{ route('marketplace.my-items') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> العودة إلى إعلاناتي
        </a>
    </div>
</div>

<style>
.card.border-3 {
    transform: scale(1.05);
    box-shadow: 0 0 30px rgba(255, 193, 7, 0.3) !important;
}
</style>
@endsection
