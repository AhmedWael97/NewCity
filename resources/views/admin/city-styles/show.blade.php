@extends('layouts.admin')

@section('title', 'عرض تصميم المدينة - ' . $city->name_ar)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-palette"></i> تصميم: {{ $city->name_ar }}
        </h1>
        <div>
            <a href="{{ route('admin.city-styles.edit', $city) }}" class="btn btn-warning btn-sm">
                <i class="fas fa-edit"></i> تعديل
            </a>
            <a href="{{ route('admin.city-styles.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-right"></i> العودة
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Theme Preview -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3" style="background-color: {{ $city->getThemeConfig('colors.primary', '#3b82f6') }}; color: white;">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-eye"></i> معاينة التصميم
                    </h6>
                </div>
                <div class="card-body" style="background: {{ $city->getThemeConfig('background.color', '#ffffff') }};">
                    @if($city->enable_custom_styling)
                        <!-- Hero Section Preview -->
                        @if($city->hero_image)
                            <div class="mb-4">
                                <img src="{{ asset('storage/' . $city->hero_image) }}" 
                                     alt="{{ $city->name_ar }}" 
                                     class="img-fluid rounded">
                            </div>
                        @endif

                        <!-- Sample Content -->
                        <div class="text-center mb-4">
                            <h2 style="color: {{ $city->getThemeConfig('colors.primary', '#3b82f6') }};">
                                مرحباً بكم في {{ $city->name_ar }}
                            </h2>
                            <p style="color: {{ $city->getThemeConfig('colors.secondary', '#64748b') }};">
                                اكتشف أفضل المتاجر والخدمات في مدينتك
                            </p>
                        </div>

                        <!-- Sample Buttons -->
                        <div class="text-center mb-4">
                            <button class="btn btn-lg m-2" 
                                    style="background-color: {{ $city->getThemeConfig('colors.primary', '#3b82f6') }}; color: white; border: none;">
                                <i class="fas fa-store"></i> تصفح المتاجر
                            </button>
                            <button class="btn btn-lg m-2" 
                                    style="background-color: {{ $city->getThemeConfig('colors.accent', '#f59e0b') }}; color: white; border: none;">
                                <i class="fas fa-star"></i> العروض المميزة
                            </button>
                        </div>

                        <!-- Sample Card -->
                        <div class="card border-0 shadow-sm">
                            <div class="card-header" 
                                 style="background-color: {{ $city->getThemeConfig('colors.primary', '#3b82f6') }}10; 
                                        border-bottom: 2px solid {{ $city->getThemeConfig('colors.primary', '#3b82f6') }};">
                                <h5 class="mb-0" style="color: {{ $city->getThemeConfig('colors.primary', '#3b82f6') }};">
                                    عنوان تجريبي
                                </h5>
                            </div>
                            <div class="card-body">
                                <p style="color: {{ $city->getThemeConfig('colors.secondary', '#64748b') }};">
                                    هذا مثال على كيفية ظهور المحتوى بالتصميم المخصص. الألوان والخطوط ستطبق على جميع عناصر التطبيق.
                                </p>
                                <span class="badge bg-lg" 
                                      style="background-color: {{ $city->getThemeConfig('colors.accent', '#f59e0b') }}; color: white;">
                                    علامة مميزة
                                </span>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>التصميم المخصص غير مفعل</strong>
                            <p class="mb-0 mt-2">سيتم استخدام التصميم الافتراضي للتطبيق. يمكنك تفعيل التصميم المخصص من صفحة التعديل.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Theme Details -->
        <div class="col-lg-4">
            <!-- Status Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-{{ $city->enable_custom_styling ? 'success' : 'secondary' }} text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-{{ $city->enable_custom_styling ? 'check-circle' : 'times-circle' }}"></i>
                        الحالة
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <strong>التصميم المخصص:</strong>
                        <span class="badge bg-{{ $city->enable_custom_styling ? 'success' : 'secondary' }}">
                            {{ $city->enable_custom_styling ? 'مفعل' : 'معطل' }}
                        </span>
                    </p>
                    @if($city->theme_config)
                        <p class="mb-0">
                            <i class="fas fa-check text-success"></i> يحتوي على تكوين مخصص
                        </p>
                    @endif
                </div>
            </div>

            <!-- Colors Card -->
            @if($city->enable_custom_styling)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-fill-drip"></i> لوحة الألوان
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>اللون الأساسي:</strong>
                        <div class="d-flex align-items-center mt-1">
                            <div class="rounded" 
                                 style="width: 40px; height: 40px; background-color: {{ $city->getThemeConfig('colors.primary', '#3b82f6') }}; border: 1px solid #dee2e6;">
                            </div>
                            <code class="ms-2">{{ $city->getThemeConfig('colors.primary', '#3b82f6') }}</code>
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>اللون الثانوي:</strong>
                        <div class="d-flex align-items-center mt-1">
                            <div class="rounded" 
                                 style="width: 40px; height: 40px; background-color: {{ $city->getThemeConfig('colors.secondary', '#64748b') }}; border: 1px solid #dee2e6;">
                            </div>
                            <code class="ms-2">{{ $city->getThemeConfig('colors.secondary', '#64748b') }}</code>
                        </div>
                    </div>

                    <div class="mb-0">
                        <strong>اللون المميز:</strong>
                        <div class="d-flex align-items-center mt-1">
                            <div class="rounded" 
                                 style="width: 40px; height: 40px; background-color: {{ $city->getThemeConfig('colors.accent', '#f59e0b') }}; border: 1px solid #dee2e6;">
                            </div>
                            <code class="ms-2">{{ $city->getThemeConfig('colors.accent', '#f59e0b') }}</code>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Typography Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-font"></i> الطباعة
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-0">
                        <strong>الخط:</strong> {{ ucfirst($city->getThemeConfig('typography.font_family', 'cairo')) }}
                    </p>
                </div>
            </div>

            <!-- Background Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-image"></i> الخلفية
                    </h6>
                </div>
                <div class="card-body">
                    <p>
                        <strong>النوع:</strong> 
                        @switch($city->getThemeConfig('background.style', 'color'))
                            @case('color')
                                لون واحد
                                @break
                            @case('gradient')
                                تدرج لوني
                                @break
                            @case('image')
                                صورة
                                @break
                        @endswitch
                    </p>

                    @if($city->hero_image)
                        <img src="{{ asset('storage/' . $city->hero_image) }}" 
                             alt="Hero" 
                             class="img-thumbnail" 
                             style="max-width: 100%;">
                    @endif
                </div>
            </div>
            @endif

            <!-- Actions Card -->
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-tools"></i> إجراءات
                    </h6>
                </div>
                <div class="card-body">
                    <a href="{{ route('admin.city-styles.edit', $city) }}" 
                       class="btn btn-warning btn-block mb-2">
                        <i class="fas fa-edit"></i> تعديل التصميم
                    </a>
                    
                    <a href="{{ route('admin.city-styles.css', $city) }}" 
                       class="btn btn-info btn-block mb-2" 
                       target="_blank">
                        <i class="fas fa-file-code"></i> عرض CSS
                    </a>
                    
                    <a href="{{ route('admin.city-styles.landing-page', $city) }}" 
                       class="btn btn-primary btn-block">
                        <i class="fas fa-rocket"></i> تخصيص الصفحة الرئيسية
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
