@extends('layouts.admin')

@section('title', 'تفاصيل خطة الاشتراك')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-crown"></i> {{ $subscription->name }}
            @if($subscription->is_popular)
                <span class="badge badge-warning">الأكثر شعبية</span>
            @endif
        </h1>
        <div>
            <a href="{{ route('admin.subscription-plans.edit', $subscription) }}" class="btn btn-warning btn-icon-split">
                <span class="icon text-white-50">
                    <i class="fas fa-edit"></i>
                </span>
                <span class="text">تعديل</span>
            </a>
            <a href="{{ route('admin.subscription-plans.index') }}" class="btn btn-secondary btn-icon-split">
                <span class="icon text-white-50">
                    <i class="fas fa-arrow-right"></i>
                </span>
                <span class="text">العودة</span>
            </a>
        </div>
    </div>

    <!-- Statistics Row -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">إجمالي المشتركين</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_subscribers'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">إيرادات الشهر</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['monthly_revenue'], 2) }} ج.م</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">إجمالي الإيرادات</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_revenue'], 2) }} ج.م</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">معدل الإلغاء</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['churn_rate'], 1) }}%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Plan Details -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-info-circle"></i> معلومات الخطة
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>الاسم:</strong><br>
                        {{ $subscription->name }}
                    </div>

                    <div class="mb-3">
                        <strong>الرمز:</strong><br>
                        <code>{{ $subscription->slug }}</code>
                    </div>

                    <div class="mb-3">
                        <strong>الوصف:</strong><br>
                        {{ $subscription->description }}
                    </div>

                    <div class="mb-3">
                        <strong>السعر الشهري:</strong><br>
                        <span class="text-success font-weight-bold">{{ number_format($subscription->monthly_price, 2) }} ج.م</span>
                    </div>

                    <div class="mb-3">
                        <strong>السعر السنوي:</strong><br>
                        <span class="text-info font-weight-bold">{{ number_format($subscription->yearly_price, 2) }} ج.م</span>
                        <small class="text-muted">(توفير {{ number_format(($subscription->monthly_price * 12 - $subscription->yearly_price), 2) }} ج.م)</small>
                    </div>

                    <div class="mb-3">
                        <strong>الحالة:</strong><br>
                        <span class="badge badge-{{ $subscription->is_active ? 'success' : 'secondary' }}">
                            {{ $subscription->is_active ? 'نشط' : 'معطل' }}
                        </span>
                    </div>

                    <div class="mb-3">
                        <strong>الترتيب:</strong><br>
                        {{ $subscription->sort_order }}
                    </div>
                </div>
            </div>
        </div>

        <!-- Limits & Features -->
        <div class="col-lg-8 mb-4">
            <!-- Usage Limits -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-sliders-h"></i> حدود الاستخدام
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 mb-3">
                            <div class="text-center">
                                <i class="fas fa-store fa-2x text-primary mb-2"></i>
                                <h4 class="mb-0">{{ $subscription->max_shops }}</h4>
                                <small class="text-muted">متجر</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center">
                                <i class="fas fa-box fa-2x text-success mb-2"></i>
                                <h4 class="mb-0">{{ $subscription->max_products_per_shop }}</h4>
                                <small class="text-muted">منتج/متجر</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center">
                                <i class="fas fa-concierge-bell fa-2x text-warning mb-2"></i>
                                <h4 class="mb-0">{{ $subscription->max_services_per_shop }}</h4>
                                <small class="text-muted">خدمة/متجر</small>
                            </div>
                        </div>
                        <div class="col-md-3 mb-3">
                            <div class="text-center">
                                <i class="fas fa-images fa-2x text-info mb-2"></i>
                                <h4 class="mb-0">{{ $subscription->max_images_per_shop }}</h4>
                                <small class="text-muted">صورة/متجر</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Premium Features -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-success text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-star"></i> الميزات المتقدمة
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-{{ $subscription->analytics_access ? 'check text-success' : 'times text-danger' }}"></i>
                                    الوصول للتحليلات
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-{{ $subscription->priority_listing ? 'check text-success' : 'times text-danger' }}"></i>
                                    الظهور المميز
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-{{ $subscription->verified_badge ? 'check text-success' : 'times text-danger' }}"></i>
                                    شارة التحقق
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-{{ $subscription->custom_branding ? 'check text-success' : 'times text-danger' }}"></i>
                                    العلامة التجارية المخصصة
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-{{ $subscription->social_media_integration ? 'check text-success' : 'times text-danger' }}"></i>
                                    ربط وسائل التواصل
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-{{ $subscription->email_marketing ? 'check text-success' : 'times text-danger' }}"></i>
                                    التسويق عبر البريد
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-{{ $subscription->advanced_seo ? 'check text-success' : 'times text-danger' }}"></i>
                                    SEO متقدم
                                </li>
                                <li class="mb-2">
                                    <i class="fas fa-{{ $subscription->customer_support ? 'check text-success' : 'times text-danger' }}"></i>
                                    دعم العملاء
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Feature List -->
            @if($subscription->features && count($subscription->features) > 0)
            <div class="card shadow">
                <div class="card-header py-3 bg-warning text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-list-check"></i> قائمة الميزات
                    </h6>
                </div>
                <div class="card-body">
                    <ul class="mb-0">
                        @foreach($subscription->features as $feature)
                            <li class="mb-1">{{ $feature }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Subscribers List -->
    <div class="card shadow">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-users"></i> المشتركون ({{ $subscriptions->total() }})
            </h6>
        </div>
        <div class="card-body">
            @if($subscriptions->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>المتجر</th>
                                <th>المالك</th>
                                <th>نوع الاشتراك</th>
                                <th>المبلغ المدفوع</th>
                                <th>تاريخ البداية</th>
                                <th>تاريخ الانتهاء</th>
                                <th>الحالة</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($subscriptions as $sub)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.shops.show', $sub->shop) }}">
                                            {{ $sub->shop->name }}
                                        </a>
                                    </td>
                                    <td>{{ $sub->shop->user->name }}</td>
                                    <td>
                                        <span class="badge badge-{{ $sub->billing_cycle == 'monthly' ? 'info' : 'primary' }}">
                                            {{ $sub->billing_cycle == 'monthly' ? 'شهري' : 'سنوي' }}
                                        </span>
                                    </td>
                                    <td>{{ number_format($sub->amount_paid, 2) }} ج.م</td>
                                    <td>{{ $sub->started_at->format('Y-m-d') }}</td>
                                    <td>{{ $sub->expires_at->format('Y-m-d') }}</td>
                                    <td>
                                        <span class="badge badge-{{ $sub->status == 'active' ? 'success' : 'secondary' }}">
                                            {{ $sub->status == 'active' ? 'نشط' : $sub->status }}
                                        </span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="mt-3">
                    {{ $subscriptions->links() }}
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> لا يوجد مشتركون في هذه الخطة حالياً.
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
