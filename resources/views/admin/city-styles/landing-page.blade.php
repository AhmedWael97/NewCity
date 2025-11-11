@extends('layouts.admin')

@section('title', 'إعدادات الصفحة الرئيسية - ' . $city->name)

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-palette"></i> إعدادات الصفحة الرئيسية: {{ $city->name }}
        </h1>
        <div>
            <a href="{{ route('admin.city-styles.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> العودة للقائمة
            </a>
            <a href="{{ route('admin.city-banners.index', ['city_id' => $city->id]) }}" class="btn btn-info btn-sm">
                <i class="fas fa-image"></i> إدارة الإعلانات
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">إعدادات المظهر والألوان</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.city-styles.landing-page.update', $city) }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i>
                            <strong>ملاحظة:</strong> هذه الإعدادات تتحكم في مظهر الصفحة الرئيسية للمدينة في تطبيق الموبايل.
                        </div>

                        <h6 class="font-weight-bold mb-3">
                            <i class="fas fa-paint-brush"></i> الألوان الأساسية
                        </h6>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="primary_color">اللون الأساسي</label>
                                    <div class="input-group">
                                        <input type="color" name="primary_color" id="primary_color" 
                                               class="form-control" style="height: 50px;"
                                               value="{{ old('primary_color', $city->theme_config['primary_color'] ?? '#FF5733') }}">
                                        <input type="text" id="primary_color_text" class="form-control" 
                                               value="{{ old('primary_color', $city->theme_config['primary_color'] ?? '#FF5733') }}"
                                               readonly>
                                    </div>
                                    <small class="form-text text-muted">يستخدم في العناوين والأزرار الرئيسية</small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="secondary_color">اللون الثانوي</label>
                                    <div class="input-group">
                                        <input type="color" name="secondary_color" id="secondary_color" 
                                               class="form-control" style="height: 50px;"
                                               value="{{ old('secondary_color', $city->theme_config['secondary_color'] ?? '#33FF57') }}">
                                        <input type="text" id="secondary_color_text" class="form-control" 
                                               value="{{ old('secondary_color', $city->theme_config['secondary_color'] ?? '#33FF57') }}"
                                               readonly>
                                    </div>
                                    <small class="form-text text-muted">يستخدم في النصوص والخلفيات الثانوية</small>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="accent_color">اللون التكميلي</label>
                                    <div class="input-group">
                                        <input type="color" name="accent_color" id="accent_color" 
                                               class="form-control" style="height: 50px;"
                                               value="{{ old('accent_color', $city->theme_config['accent_color'] ?? '#FFC300') }}">
                                        <input type="text" id="accent_color_text" class="form-control" 
                                               value="{{ old('accent_color', $city->theme_config['accent_color'] ?? '#FFC300') }}"
                                               readonly>
                                    </div>
                                    <small class="form-text text-muted">يستخدم في الإبرازات والأيقونات</small>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="font-weight-bold mb-3">
                            <i class="fas fa-sliders-h"></i> إعدادات الأقسام
                        </h6>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="custom-control custom-switch mb-3">
                                    <input type="checkbox" class="custom-control-input" id="show_featured_section" 
                                           name="show_featured_section"
                                           {{ old('show_featured_section', $city->theme_config['show_featured_section'] ?? true) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="show_featured_section">
                                        عرض قسم المتاجر المميزة
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="custom-control custom-switch mb-3">
                                    <input type="checkbox" class="custom-control-input" id="show_latest_section" 
                                           name="show_latest_section"
                                           {{ old('show_latest_section', $city->theme_config['show_latest_section'] ?? true) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="show_latest_section">
                                        عرض قسم المتاجر الجديدة
                                    </label>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="custom-control custom-switch mb-3">
                                    <input type="checkbox" class="custom-control-input" id="show_statistics" 
                                           name="show_statistics"
                                           {{ old('show_statistics', $city->theme_config['show_statistics'] ?? true) ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="show_statistics">
                                        عرض الإحصائيات
                                    </label>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <h6 class="font-weight-bold mb-3">
                            <i class="fas fa-cog"></i> إعدادات العرض
                        </h6>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="banner_style">نمط عرض الإعلانات</label>
                                    <select name="banner_style" id="banner_style" class="form-control">
                                        <option value="carousel" {{ old('banner_style', $city->theme_config['banner_style'] ?? 'carousel') == 'carousel' ? 'selected' : '' }}>
                                            Carousel (منزلق)
                                        </option>
                                        <option value="slider" {{ old('banner_style', $city->theme_config['banner_style'] ?? 'carousel') == 'slider' ? 'selected' : '' }}>
                                            Slider (مربعات)
                                        </option>
                                        <option value="grid" {{ old('banner_style', $city->theme_config['banner_style'] ?? 'carousel') == 'grid' ? 'selected' : '' }}>
                                            Grid (شبكة)
                                        </option>
                                    </select>
                                </div>
                            </div>

                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="category_display_style">نمط عرض التصنيفات</label>
                                    <select name="category_display_style" id="category_display_style" class="form-control">
                                        <option value="grid" {{ old('category_display_style', $city->theme_config['category_display_style'] ?? 'grid') == 'grid' ? 'selected' : '' }}>
                                            Grid (شبكة)
                                        </option>
                                        <option value="list" {{ old('category_display_style', $city->theme_config['category_display_style'] ?? 'grid') == 'list' ? 'selected' : '' }}>
                                            List (قائمة)
                                        </option>
                                        <option value="carousel" {{ old('category_display_style', $city->theme_config['category_display_style'] ?? 'grid') == 'carousel' ? 'selected' : '' }}>
                                            Carousel (منزلق)
                                        </option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="featured_shops_limit">عدد المتاجر المميزة</label>
                                    <input type="number" name="featured_shops_limit" id="featured_shops_limit" 
                                           class="form-control" min="3" max="20"
                                           value="{{ old('featured_shops_limit', $city->theme_config['featured_shops_limit'] ?? 10) }}">
                                    <small class="form-text text-muted">الحد الأقصى للمتاجر المميزة المعروضة (3-20)</small>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="latest_shops_limit">عدد المتاجر الجديدة</label>
                                    <input type="number" name="latest_shops_limit" id="latest_shops_limit" 
                                           class="form-control" min="5" max="30"
                                           value="{{ old('latest_shops_limit', $city->theme_config['latest_shops_limit'] ?? 15) }}">
                                    <small class="form-text text-muted">الحد الأقصى للمتاجر الجديدة المعروضة (5-30)</small>
                                </div>
                            </div>
                        </div>

                        <hr class="my-4">

                        <div class="form-group mb-0">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> حفظ الإعدادات
                            </button>
                            <a href="{{ route('admin.city-styles.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Preview Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">
                        <i class="fas fa-eye"></i> معاينة الألوان
                    </h6>
                </div>
                <div class="card-body">
                    <div id="colorPreview" class="p-3 rounded" style="border: 1px solid #ddd;">
                        <div class="mb-3 p-2 rounded" id="primaryPreview" style="background-color: #FF5733; color: white;">
                            <strong>اللون الأساسي</strong><br>
                            <small>يستخدم في العناوين الرئيسية</small>
                        </div>
                        <div class="mb-3 p-2 rounded" id="secondaryPreview" style="background-color: #33FF57; color: white;">
                            <strong>اللون الثانوي</strong><br>
                            <small>يستخدم في الخلفيات</small>
                        </div>
                        <div class="mb-0 p-2 rounded" id="accentPreview" style="background-color: #FFC300; color: white;">
                            <strong>اللون التكميلي</strong><br>
                            <small>يستخدم في الإبرازات</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Info Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">
                        <i class="fas fa-info-circle"></i> معلومات
                    </h6>
                </div>
                <div class="card-body">
                    <h6 class="font-weight-bold">الإحصائيات الحالية:</h6>
                    <ul class="small">
                        <li><strong>المتاجر المميزة:</strong> {{ $city->shops()->where('is_featured', true)->count() }}</li>
                        <li><strong>المتاجر الجديدة (30 يوم):</strong> {{ $city->shops()->where('created_at', '>=', now()->subDays(30))->count() }}</li>
                        <li><strong>إعلانات نشطة:</strong> {{ $city->activeBanners()->count() }}</li>
                    </ul>

                    <hr>

                    <h6 class="font-weight-bold">روابط سريعة:</h6>
                    <a href="{{ route('admin.city-banners.index', ['city_id' => $city->id]) }}" class="btn btn-sm btn-info btn-block mb-2">
                        <i class="fas fa-image"></i> إدارة الإعلانات
                    </a>
                    <a href="{{ route('admin.shops.index', ['city_id' => $city->id, 'is_featured' => 1]) }}" class="btn btn-sm btn-warning btn-block">
                        <i class="fas fa-star"></i> المتاجر المميزة
                    </a>
                </div>
            </div>

            <!-- API Info -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-code"></i> معلومات API
                    </h6>
                </div>
                <div class="card-body">
                    <p class="small"><strong>API Endpoint:</strong></p>
                    <code class="small d-block bg-light p-2 rounded mb-2">
                        GET /api/v1/cities/{{ $city->id }}
                    </code>
                    <p class="small text-muted mb-0">
                        تطبيق الموبايل سيحصل على هذه الإعدادات تلقائياً عند تحميل الصفحة الرئيسية للمدينة.
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Color picker sync
    function syncColorPicker(pickerId, textId, previewId) {
        const picker = document.getElementById(pickerId);
        const text = document.getElementById(textId);
        const preview = document.getElementById(previewId);
        
        picker.addEventListener('input', function() {
            text.value = this.value;
            preview.style.backgroundColor = this.value;
        });
    }

    syncColorPicker('primary_color', 'primary_color_text', 'primaryPreview');
    syncColorPicker('secondary_color', 'secondary_color_text', 'secondaryPreview');
    syncColorPicker('accent_color', 'accent_color_text', 'accentPreview');
</script>
@endpush
@endsection
