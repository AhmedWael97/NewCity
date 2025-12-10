@extends('layouts.admin')

@section('title', 'Admin Dashboard')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">لوحة التحكم الرئيسية</h1>
        <div>
            <button type="button" class="btn btn-outline-primary" onclick="refreshSystemHealth()">
                <i class="fas fa-sync-alt"></i> تحديث حالة النظام
            </button>
        </div>
    </div>

    <!-- System Health Status -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-left-info">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-info">حالة النظام</h6>
                    <div id="system-health-indicator" class="badge bg-success text-white">صحي</div>
                </div>
                <div class="card-body">
                    <div class="row" id="system-health-details">
                        <div class="col-md-3 text-center">
                            <div class="health-item">
                                <i class="fas fa-database fa-2x text-success mb-2"></i>
                                <p class="mb-0 small">قاعدة البيانات</p>
                                <span class="badge bg-success text-white">متصل</span>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="health-item">
                                <i class="fas fa-hdd fa-2x text-success mb-2"></i>
                                <p class="mb-0 small">التخزين</p>
                                <span class="badge bg-success text-white">متاح</span>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="health-item">
                                <i class="fas fa-memory fa-2x text-success mb-2"></i>
                                <p class="mb-0 small">التخزين المؤقت</p>
                                <span class="badge bg-success text-white">يعمل</span>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="health-item">
                                <i class="fas fa-tasks fa-2x text-success mb-2"></i>
                                <p class="mb-0 small">المهام</p>
                                <span class="badge bg-success text-white">طبيعي</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        @can('view users')
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">إجمالي المستخدمين</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_users']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="text-success"><i class="fas fa-arrow-up"></i> +{{ $stats['new_users_this_month'] }}</span>
                        <span class="text-muted small">هذا الشهر</span>
                    </div>
                </div>
            </div>
        </div>
        @endcan

        @can('view shops')
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">إجمالي المتاجر</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_shops']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-store fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="text-success"><i class="fas fa-arrow-up"></i> +{{ $stats['new_shops_this_month'] }}</span>
                        <span class="text-muted small">هذا الشهر</span>
                    </div>
                </div>
            </div>
        </div>
        @endcan

        @can('verify shops')
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">المتاجر المتحققة</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['verified_shops']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="text-info">
                            {{ $stats['total_shops'] > 0 ? round(($stats['verified_shops'] / $stats['total_shops']) * 100, 1) : 0 }}%
                        </span>
                        <span class="text-muted small">من إجمالي المتاجر</span>
                    </div>
                </div>
            </div>
        </div>
        @endcan

        @can('verify shops')
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">المتاجر المعلقة</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['pending_shops']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    @if($stats['pending_shops'] > 0)
                        <div class="mt-2">
                            <a href="{{ route('admin.shops.pending') }}" class="text-warning small">
                                <i class="fas fa-arrow-right"></i> مراجعة المتاجر
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endcan
    </div>

    <!-- Additional Statistics Cards - New Business Features -->
    <div class="row mb-4">
        @can('view products')
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">المنتجات</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_products']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-box fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endcan

        @can('view services')
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">الخدمات</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_services']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tools fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endcan

        @can('view subscriptions')
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">الاشتراكات النشطة</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['active_subscriptions']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-credit-card fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endcan

        @can('view support tickets')
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">التذاكر المفتوحة</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['pending_tickets']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ticket-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    @if($stats['pending_tickets'] > 0)
                        <div class="mt-2">
                            <a href="{{ route('admin.tickets.index') }}" class="text-warning small">
                                <i class="fas fa-arrow-right"></i> مراجعة التذاكر
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
        @endcan

        @can('view cities')
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">المدن</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_cities']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-map-marker-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endcan

        @can('view categories')
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">الفئات</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_categories']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-tags fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endcan
    </div>

    <!-- Quick Access Cards - New Features -->
    <div class="row mb-4">
        <!-- City Banners Management -->
        @can('manage city banners')
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">إعلانات المدن</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\CityBanner::where('is_active', true)->count() }}
                            </div>
                            <div class="text-muted small mt-1">إعلان نشط</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-image fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.city-banners.index') }}" class="btn btn-warning btn-sm btn-block">
                            <i class="fas fa-cog"></i> إدارة الإعلانات
                        </a>
                        <a href="{{ route('admin.city-banners.create') }}" class="btn btn-outline-warning btn-sm btn-block mt-2">
                            <i class="fas fa-plus"></i> إضافة إعلان جديد
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endcan

        <!-- Featured Shops -->
        @can('manage featured shops')
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">المتاجر المميزة</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\Shop::where('is_featured', true)->count() }}
                            </div>
                            <div class="text-muted small mt-1">متجر مميز</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.shops.index', ['is_featured' => 1]) }}" class="btn btn-primary btn-sm btn-block">
                            <i class="fas fa-list"></i> عرض المتاجر المميزة
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endcan

        <!-- City Theme Configuration -->
        @can('manage city styles')
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">تصاميم المدن</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ \App\Models\City::whereNotNull('theme_config')->count() }}
                            </div>
                            <div class="text-muted small mt-1">مدينة مخصصة</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-palette fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.city-styles.index') }}" class="btn btn-success btn-sm btn-block">
                            <i class="fas fa-paint-brush"></i> إدارة التصاميم
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endcan

        <!-- Mobile App Settings -->
        @can('manage app settings')
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">إعدادات التطبيق</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                @php
                                    $appStatus = \App\Models\AppSetting::where('key', 'app_status')->first();
                                @endphp
                                @if($appStatus && $appStatus->value === 'active')
                                    <span class="badge bg-success text-white">نشط</span>
                                @else
                                    <span class="badge bg-danger text-white">غير نشط</span>
                                @endif
                            </div>
                            <div class="text-muted small mt-1">حالة التطبيق</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-mobile-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-3">
                        <a href="{{ route('admin.app-settings.index') }}" class="btn btn-info btn-sm btn-block">
                            <i class="fas fa-cog"></i> إعدادات التطبيق
                        </a>
                        <a href="{{ route('admin.app-settings.notifications') }}" class="btn btn-outline-info btn-sm btn-block mt-2">
                            <i class="fas fa-bell"></i> إرسال إشعار
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @endcan
    </div>

    <!-- Analytics Performance Section -->
    @can('view analytics')
    <div class="row mb-4">
        <!-- Top Performing Shops -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-success">أفضل المتاجر أداءً (آخر 30 يوم)</h6>
                    <a href="{{ route('admin.analytics.shops') }}" class="btn btn-success btn-sm">عرض التفاصيل</a>
                </div>
                <div class="card-body">
                    @forelse($topShops->take(5) as $shopAnalytic)
                        <div class="d-flex align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="avatar-sm rounded-circle bg-success text-white d-flex align-items-center justify-content-center mr-3">
                                {{ $loop->iteration }}
                            </div>
                            <div class="flex-grow-1">
                                <div class="font-weight-bold">{{ $shopAnalytic->shop->name ?? 'متجر محذوف' }}</div>
                                <div class="text-muted small">
                                    {{ $shopAnalytic->shop->city->name_ar ?? 'غير محدد' }} - {{ $shopAnalytic->shop->category->name_ar ?? 'غير محدد' }}
                                </div>
                            </div>
                            <div class="text-success font-weight-bold">
                                {{ number_format($shopAnalytic->total_views) }} مشاهدة
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-chart-line fa-3x mb-3"></i>
                            <p>لا توجد بيانات تحليلية متاحة</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Top Cities by Visitors -->
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-info">أكثر المدن زيارة (آخر 30 يوم)</h6>
                    <a href="{{ route('admin.analytics.cities') }}" class="btn btn-info btn-sm">عرض التفاصيل</a>
                </div>
                <div class="card-body">
                    @forelse($topCities->take(5) as $cityAnalytic)
                        <div class="d-flex align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="avatar-sm rounded-circle bg-info text-white d-flex align-items-center justify-content-center mr-3">
                                {{ $loop->iteration }}
                            </div>
                            <div class="flex-grow-1">
                                <div class="font-weight-bold">{{ $cityAnalytic->city->name_ar ?? 'مدينة محذوفة' }}</div>
                                <div class="text-muted small">
                                    {{ $cityAnalytic->city->governorate ?? 'غير محدد' }}
                                </div>
                            </div>
                            <div class="text-info font-weight-bold">
                                {{ number_format($cityAnalytic->unique_visitors) }} زائر
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-map-marked-alt fa-3x mb-3"></i>
                            <p>لا توجد بيانات زيارات متاحة</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
    @endcan

    
    @if(auth()->user()->hasRole('super_admin'))
    <!-- New Features Guide -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow border-left-success">
                <div class="card-header py-3 bg-gradient-success">
                    <h6 class="m-0 font-weight-bold text-white">
                        <i class="fas fa-rocket"></i> الميزات الجديدة - إدارة صفحات المدن والتطبيق
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <!-- City Banners Guide -->
                        <div class="col-md-3 mb-3">
                            <div class="card h-100 border-warning">
                                <div class="card-body text-center">
                                    <i class="fas fa-image fa-3x text-warning mb-3"></i>
                                    <h6 class="font-weight-bold text-warning">إعلانات المدن</h6>
                                    <p class="small text-muted">
                                        قم بإنشاء وإدارة الإعلانات الترويجية لكل مدينة مع جدولة زمنية وأولويات عرض.
                                    </p>
                                    <a href="{{ route('admin.city-banners.index') }}" class="btn btn-warning btn-sm btn-block">
                                        <i class="fas fa-arrow-right"></i> إدارة الإعلانات
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- Featured Shops Guide -->
                        <div class="col-md-3 mb-3">
                            <div class="card h-100 border-primary">
                                <div class="card-body text-center">
                                    <i class="fas fa-star fa-3x text-primary mb-3"></i>
                                    <h6 class="font-weight-bold text-primary">المتاجر المميزة</h6>
                                    <p class="small text-muted">
                                        حدد المتاجر المميزة مع تحديد الأولوية وتاريخ الانتهاء لعرضها في الصفحة الرئيسية.
                                    </p>
                                    <a href="{{ route('admin.shops.index') }}" class="btn btn-primary btn-sm btn-block">
                                        <i class="fas fa-arrow-right"></i> إدارة المتاجر
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- City Theme Guide -->
                        <div class="col-md-3 mb-3">
                            <div class="card h-100 border-success">
                                <div class="card-body text-center">
                                    <i class="fas fa-palette fa-3x text-success mb-3"></i>
                                    <h6 class="font-weight-bold text-success">تخصيص المظهر</h6>
                                    <p class="small text-muted">
                                        قم بتخصيص ألوان ومظهر الصفحة الرئيسية لكل مدينة بشكل مستقل.
                                    </p>
                                    <a href="{{ route('admin.city-styles.index') }}" class="btn btn-success btn-sm btn-block">
                                        <i class="fas fa-arrow-right"></i> تصاميم المدن
                                    </a>
                                </div>
                            </div>
                        </div>

                        <!-- App Settings Guide -->
                        <div class="col-md-3 mb-3">
                            <div class="card h-100 border-info">
                                <div class="card-body text-center">
                                    <i class="fas fa-mobile-alt fa-3x text-info mb-3"></i>
                                    <h6 class="font-weight-bold text-info">إعدادات التطبيق</h6>
                                    <p class="small text-muted">
                                        تحكم في التطبيق: تغيير الاسم، الإغلاق للصيانة، إرسال الإشعارات وغيرها.
                                    </p>
                                    <a href="{{ route('admin.app-settings.index') }}" class="btn btn-info btn-sm btn-block">
                                        <i class="fas fa-arrow-right"></i> إعدادات التطبيق
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Quick Tips -->
                    <div class="alert alert-success mt-3 mb-0">
                        <div class="row">
                            <div class="col-md-4">
                                <strong><i class="fas fa-lightbulb"></i> نصيحة سريعة:</strong>
                                <p class="small mb-0">استخدم الأولويات (0-100) لترتيب الإعلانات والمتاجر المميزة</p>
                            </div>
                            <div class="col-md-4">
                                <strong><i class="fas fa-info-circle"></i> معلومة:</strong>
                                <p class="small mb-0">التغييرات في المظهر تظهر فوراً في التطبيق عبر API</p>
                            </div>
                            <div class="col-md-4">
                                <strong><i class="fas fa-book"></i> الدليل:</strong>
                                <p class="small mb-0">راجع ملفات التوثيق في مجلد المشروع للتفاصيل الكاملة</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @endif

    <!-- Revenue and Growth Section -->
    @can('view subscriptions')
    <div class="row mb-4">
        <!-- Revenue Chart -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">الإيرادات اليومية (آخر 30 يوم)</h6>
                    <a href="{{ route('admin.subscriptions.analytics') }}" class="btn btn-primary btn-sm">تحليلات الاشتراكات</a>
                </div>
                <div class="card-body">
                    <canvas id="revenueChart" width="100%" height="50"></canvas>
                    <div class="mt-3 text-center">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center py-2">
                                        <div class="text-xs text-uppercase text-muted">إجمالي الإيرادات</div>
                                        <div class="h6 mb-0 text-success">
                                            {{ number_format($revenueData->sum('daily_revenue')) }} ج.م
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center py-2">
                                        <div class="text-xs text-uppercase text-muted">متوسط يومي</div>
                                        <div class="h6 mb-0 text-info">
                                            {{ number_format($revenueData->avg('daily_revenue')) }} ج.م
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="card bg-light">
                                    <div class="card-body text-center py-2">
                                        <div class="text-xs text-uppercase text-muted">أعلى يوم</div>
                                        <div class="h6 mb-0 text-warning">
                                            {{ number_format($revenueData->max('daily_revenue')) }} ج.م
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activities Summary -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">الأنشطة الحديثة</h6>
                </div>
                <div class="card-body">
                    <!-- Recent Subscriptions -->
                    @if(isset($recentSubscriptions) && $recentSubscriptions->count() > 0)
                        <div class="mb-3">
                            <h6 class="text-success">
                                <i class="fas fa-credit-card"></i> اشتراكات جديدة
                            </h6>
                            @foreach($recentSubscriptions->take(3) as $subscription)
                                <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                    <small>{{ $subscription->shop->name ?? 'متجر محذوف' }}</small>
                                    <small class="text-muted">{{ $subscription->created_at->diffForHumans() }}</small>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Recent Tickets -->
                    @if(isset($recentTickets) && $recentTickets->count() > 0)
                        <div class="mb-3">
                            <h6 class="text-warning">
                                <i class="fas fa-ticket-alt"></i> تذاكر جديدة
                            </h6>
                            @foreach($recentTickets->take(3) as $ticket)
                                <div class="d-flex justify-content-between align-items-center py-1 border-bottom">
                                    <small>{{ Str::limit($ticket->subject, 25) }}</small>
                                    <small class="text-muted">{{ $ticket->created_at->diffForHumans() }}</small>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    <!-- Quick Actions -->
                    @if(auth()->user()->hasRole('super_admin'))
                    <div class="mt-3">
                        <h6 class="text-dark">
                            <i class="fas fa-bolt"></i> إجراءات سريعة
                        </h6>
                        <div class="btn-group-vertical w-100">
                            <a href="{{ route('admin.subscriptions.create') }}" class="btn btn-outline-primary btn-sm mb-1">
                                <i class="fas fa-plus"></i> إضافة خطة اشتراك
                            </a>
                            <a href="{{ route('admin.tickets.index') }}" class="btn btn-outline-warning btn-sm mb-1">
                                <i class="fas fa-ticket-alt"></i> إدارة التذاكر
                            </a>
                            <a href="{{ route('admin.analytics.index') }}" class="btn btn-outline-info btn-sm">
                                <i class="fas fa-chart-pie"></i> التحليلات المتقدمة
                            </a>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endcan

    <!-- Charts Row -->
    @can('view analytics')
    <div class="row mb-4">
        <!-- Monthly Statistics Chart -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">إحصائيات شهرية</h6>
                </div>
                <div class="card-body">
                    <canvas id="monthlyStatsChart" width="100%" height="50"></canvas>
                </div>
            </div>
        </div>

        <!-- User Distribution Pie Chart -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">توزيع المستخدمين</h6>
                </div>
                <div class="card-body">
                    <canvas id="userDistributionChart" width="100%" height="100"></canvas>
                    <div class="mt-4 text-center small">
                        @foreach($user_distribution as $type)
                            <span class="mr-2">
                                <i class="fas fa-circle" style="color: {{ $loop->index == 0 ? '#4e73df' : ($loop->index == 1 ? '#1cc88a' : '#36b9cc') }}"></i>
                                {{ $type->user_type == 'regular' ? 'عادي' : ($type->user_type == 'shop_owner' ? 'صاحب متجر' : 'مدير') }} ({{ $type->count }})
                            </span>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endcan

    <!-- Recent Activities Row -->
    <div class="row">
        <!-- Recent Users -->
        @can('view users')
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">أحدث المستخدمين</h6>
                    <a href="{{ route('admin.users.index') }}" class="btn btn-primary btn-sm">عرض الكل</a>
                </div>
                <div class="card-body">
                    @forelse($recent_users as $user)
                        <div class="d-flex align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="avatar-sm rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-3">
                                {{ substr($user->name, 0, 1) }}
                            </div>
                            <div class="flex-grow-1">
                                <div class="font-weight-bold">{{ $user->name }}</div>
                                <div class="text-muted small">{{ $user->email }}</div>
                            </div>
                            <div class="text-right">
                                <span class="badge bg-{{ $user->user_type == 'admin' ? 'danger' : ($user->user_type == 'shop_owner' ? 'success' : 'secondary') }}">
                                    {{ $user->user_type == 'regular' ? 'عادي' : ($user->user_type == 'shop_owner' ? 'صاحب متجر' : 'مدير') }}
                                </span>
                                <div class="text-muted small">{{ $user->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-4">لا توجد مستخدمين حديثين</p>
                    @endforelse
                </div>
            </div>
        </div>
        @endcan

        <!-- Recent Shops -->
        @can('view shops')
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">أحدث المتاجر</h6>
                    <a href="{{ route('admin.shops.index') }}" class="btn btn-primary btn-sm">عرض الكل</a>
                </div>
                <div class="card-body">
                    @forelse($recent_shops as $shop)
                        <div class="d-flex align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="avatar-sm rounded-circle bg-success text-white d-flex align-items-center justify-content-center mr-3">
                                <i class="fas fa-store"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="font-weight-bold">{{ $shop->name }}</div>
                                <div class="text-muted small">{{ $shop->city->name ?? 'غير محدد' }}</div>
                            </div>
                            <div class="text-right">
                                <span class="badge bg-{{ $shop->status == 'active' ? 'success' : ($shop->status == 'pending' ? 'warning' : 'danger') }}">
                                    {{ $shop->status == 'active' ? 'نشط' : ($shop->status == 'pending' ? 'معلق' : 'غير نشط') }}
                                </span>
                                <div class="text-muted small">{{ $shop->created_at->diffForHumans() }}</div>
                            </div>
                        </div>
                    @empty
                        <p class="text-muted text-center py-4">لا توجد متاجر حديثة</p>
                    @endforelse
                </div>
            </div>
        </div>
        @endcan
    </div>

    <!-- Top Rated Shops -->
    @can('view shops')
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">أفضل المتاجر تقييماً</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>المتجر</th>
                                    <th>المالك</th>
                                    <th>المدينة</th>
                                    <th>التقييم</th>
                                    <th>عدد المراجعات</th>
                                    <th>الحالة</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($top_shops as $shop)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm rounded bg-success text-white d-flex align-items-center justify-content-center mr-2">
                                                    <i class="fas fa-store"></i>
                                                </div>
                                                <div>
                                                    <div class="font-weight-bold">{{ $shop->name }}</div>
                                                    <div class="text-muted small">{{ Str::limit($shop->description, 50) }}</div>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $shop->user->name ?? 'غير محدد' }}</td>
                                        <td>{{ $shop->city->name ?? 'غير محدد' }}</td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="font-weight-bold mr-1">{{ number_format($shop->rating, 1) }}</span>
                                                <div class="text-warning">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <i class="fas fa-star{{ $i <= $shop->rating ? '' : '-o' }}"></i>
                                                    @endfor
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ number_format($shop->total_reviews) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $shop->status == 'active' ? 'success' : ($shop->status == 'pending' ? 'warning' : 'danger') }}">
                                                {{ $shop->status == 'active' ? 'نشط' : ($shop->status == 'pending' ? 'معلق' : 'غير نشط') }}
                                            </span>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.shops.show', $shop) }}" class="btn btn-sm btn-outline-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center py-4 text-muted">لا توجد متاجر مقيمة</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @endcan
</div>

<style>
.avatar-sm {
    width: 40px;
    height: 40px;
    font-size: 0.875rem;
}

.health-item {
    padding: 1rem;
}

.card {
    border-radius: 0.5rem;
}

.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.text-gray-800 {
    color: #5a5c69 !important;
}

.text-gray-300 {
    color: #dddfeb !important;
}
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Monthly Statistics Chart
const monthlyStatsCtx = document.getElementById('monthlyStatsChart').getContext('2d');
const monthlyStatsChart = new Chart(monthlyStatsCtx, {
    type: 'line',
    data: {
        labels: @json($monthly_stats['months']),
        datasets: [{
            label: 'المستخدمين',
            data: @json($monthly_stats['users']),
            borderColor: '#4e73df',
            backgroundColor: 'rgba(78, 115, 223, 0.1)',
            tension: 0.3
        }, {
            label: 'المتاجر',
            data: @json($monthly_stats['shops']),
            borderColor: '#1cc88a',
            backgroundColor: 'rgba(28, 200, 138, 0.1)',
            tension: 0.3
        }, {
            label: 'التقييمات',
            data: @json($monthly_stats['ratings']),
            borderColor: '#36b9cc',
            backgroundColor: 'rgba(54, 185, 204, 0.1)',
            tension: 0.3
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
});

// User Distribution Chart
const userDistributionCtx = document.getElementById('userDistributionChart').getContext('2d');
const userDistributionChart = new Chart(userDistributionCtx, {
    type: 'doughnut',
    data: {
        labels: @json($user_distribution->pluck('user_type')->map(function($type) {
            return $type == 'regular' ? 'عادي' : ($type == 'shop_owner' ? 'صاحب متجر' : 'مدير');
        })),
        datasets: [{
            data: @json($user_distribution->pluck('count')),
            backgroundColor: ['#4e73df', '#1cc88a', '#36b9cc'],
            hoverBackgroundColor: ['#2e59d9', '#17a673', '#2c9faf'],
            hoverBorderColor: "rgba(234, 236, 244, 1)",
        }],
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

// Revenue Chart
const revenueCtx = document.getElementById('revenueChart').getContext('2d');
const revenueChart = new Chart(revenueCtx, {
    type: 'bar',
    data: {
        labels: @json($revenueData->pluck('date')),
        datasets: [{
            label: 'الإيرادات اليومية (ج.م)',
            data: @json($revenueData->pluck('daily_revenue')),
            backgroundColor: 'rgba(28, 200, 138, 0.8)',
            borderColor: '#1cc88a',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return value.toLocaleString() + ' ج.م';
                    }
                }
            }
        },
        plugins: {
            tooltip: {
                callbacks: {
                    label: function(context) {
                        return 'الإيرادات: ' + context.parsed.y.toLocaleString() + ' ج.م';
                    }
                }
            }
        }
    }
});

// System Health Check
function refreshSystemHealth() {
    fetch('{{ route("admin.system.health") }}')
        .then(response => response.json())
        .then(data => {
            updateSystemHealthUI(data);
        })
        .catch(error => {
            console.error('Error:', error);
        });
}

function updateSystemHealthUI(healthData) {
    const indicator = document.getElementById('system-health-indicator');
    const details = document.getElementById('system-health-details');
    
    let overallStatus = 'healthy';
    
    // Check if any system is in error state
    Object.values(healthData).forEach(system => {
        if (system.status === 'error') {
            overallStatus = 'error';
        } else if (system.status === 'warning' && overallStatus !== 'error') {
            overallStatus = 'warning';
        }
    });
    
    // Update overall indicator
    indicator.className = `badge bg-${overallStatus === 'healthy' ? 'success' : (overallStatus === 'warning' ? 'warning' : 'danger')}`;
    indicator.textContent = overallStatus === 'healthy' ? 'صحي' : (overallStatus === 'warning' ? 'تحذير' : 'خطأ');
    
    // Update details (you can expand this based on the actual health data structure)
    console.log('System health updated:', healthData);
}

// Auto-refresh system health every 5 minutes
setInterval(refreshSystemHealth, 5 * 60 * 1000);
</script>
@endpush