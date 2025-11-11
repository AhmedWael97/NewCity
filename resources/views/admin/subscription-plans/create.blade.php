@extends('layouts.admin')

@section('title', 'إضافة خطة اشتراك جديدة')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plus"></i> إضافة خطة اشتراك جديدة
        </h1>
        <a href="{{ route('admin.subscription-plans.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-right"></i> العودة للقائمة
        </a>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">معلومات خطة الاشتراك</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.subscription-plans.store') }}">
                        @csrf
                        
                        <!-- Basic Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">المعلومات الأساسية</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">اسم الخطة <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" name="name" value="{{ old('name') }}" required 
                                                   placeholder="مثال: الخطة الأساسية">
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name_en">اسم الخطة بالإنجليزية</label>
                                            <input type="text" class="form-control @error('name_en') is-invalid @enderror" 
                                                   id="name_en" name="name_en" value="{{ old('name_en') }}" 
                                                   placeholder="Basic Plan">
                                            @error('name_en')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="description">وصف الخطة</label>
                                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                                      id="description" name="description" rows="3" 
                                                      placeholder="اكتب وصفاً مفصلاً عن مزايا هذه الخطة...">{{ old('description') }}</textarea>
                                            @error('description')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Pricing Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-success">التسعير والمدة</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="price">السعر <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <input type="number" step="0.01" min="0" 
                                                       class="form-control @error('price') is-invalid @enderror" 
                                                       id="price" name="price" value="{{ old('price') }}" required>
                                                <div class="input-group-append">
                                                    <span class="input-group-text">جنيه</span>
                                                </div>
                                            </div>
                                            @error('price')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="duration_days">مدة الاشتراك (بالأيام) <span class="text-danger">*</span></label>
                                            <select class="form-control @error('duration_days') is-invalid @enderror" 
                                                    id="duration_days" name="duration_days" required>
                                                <option value="">-- اختر المدة --</option>
                                                <option value="7" {{ old('duration_days') == 7 ? 'selected' : '' }}>7 أيام (أسبوع)</option>
                                                <option value="30" {{ old('duration_days') == 30 ? 'selected' : '' }}>30 يوم (شهر)</option>
                                                <option value="90" {{ old('duration_days') == 90 ? 'selected' : '' }}>90 يوم (3 أشهر)</option>
                                                <option value="180" {{ old('duration_days') == 180 ? 'selected' : '' }}>180 يوم (6 أشهر)</option>
                                                <option value="365" {{ old('duration_days') == 365 ? 'selected' : '' }}>365 يوم (سنة)</option>
                                                <option value="custom">مدة مخصصة</option>
                                            </select>
                                            @error('duration_days')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-3" id="custom-duration" style="display: none;">
                                        <div class="form-group">
                                            <label for="custom_duration">المدة المخصصة (أيام)</label>
                                            <input type="number" min="1" class="form-control" 
                                                   id="custom_duration" name="custom_duration" value="{{ old('custom_duration') }}">
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="discount_percentage">نسبة الخصم (%)</label>
                                            <input type="number" step="0.01" min="0" max="100" 
                                                   class="form-control @error('discount_percentage') is-invalid @enderror" 
                                                   id="discount_percentage" name="discount_percentage" value="{{ old('discount_percentage', 0) }}">
                                            @error('discount_percentage')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="alert alert-info">
                                            <strong>معلومة:</strong> السعر النهائي بعد الخصم: 
                                            <span id="final-price" class="font-weight-bold">{{ old('price', 0) }} جنيه</span>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="alert alert-success">
                                            <strong>السعر لكل يوم:</strong> 
                                            <span id="daily-price" class="font-weight-bold">0 جنيه</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Features & Limits -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-info">المزايا والحدود</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="max_products">الحد الأقصى للمنتجات</label>
                                            <input type="number" min="0" class="form-control @error('max_products') is-invalid @enderror" 
                                                   id="max_products" name="max_products" value="{{ old('max_products') }}" 
                                                   placeholder="0 = غير محدود">
                                            @error('max_products')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">اتركه فارغاً أو 0 لعدد غير محدود</small>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="max_images">الحد الأقصى للصور</label>
                                            <input type="number" min="0" class="form-control @error('max_images') is-invalid @enderror" 
                                                   id="max_images" name="max_images" value="{{ old('max_images') }}" 
                                                   placeholder="0 = غير محدود">
                                            @error('max_images')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="max_categories">الحد الأقصى للفئات</label>
                                            <input type="number" min="0" class="form-control @error('max_categories') is-invalid @enderror" 
                                                   id="max_categories" name="max_categories" value="{{ old('max_categories') }}" 
                                                   placeholder="0 = غير محدود">
                                            @error('max_categories')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label>المزايا المتاحة</label>
                                            <div class="row">
                                                <div class="col-md-6">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" 
                                                               id="can_add_products" name="can_add_products" value="1" 
                                                               {{ old('can_add_products', true) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="can_add_products">
                                                            إضافة منتجات
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" 
                                                               id="can_edit_shop" name="can_edit_shop" value="1" 
                                                               {{ old('can_edit_shop', true) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="can_edit_shop">
                                                            تعديل المتجر
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" 
                                                               id="can_use_analytics" name="can_use_analytics" value="1" 
                                                               {{ old('can_use_analytics') ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="can_use_analytics">
                                                            التحليلات المتقدمة
                                                        </label>
                                                    </div>
                                                </div>
                                                <div class="col-md-6">
                                                    <div class="form-check">
                                                        <input class="form-check-input" type="checkbox" 
                                                               id="priority_support" name="priority_support" value="1" 
                                                               {{ old('priority_support') ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="priority_support">
                                                            دعم مميز
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="features">مزايا إضافية</label>
                                            <textarea class="form-control @error('features') is-invalid @enderror" 
                                                      id="features" name="features" rows="5" 
                                                      placeholder="أدخل كل ميزة في سطر منفصل...">{{ old('features') }}</textarea>
                                            @error('features')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">ميزة واحدة في كل سطر</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Display Settings -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-warning">إعدادات العرض</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="color">لون الخطة</label>
                                            <div class="input-group">
                                                <input type="color" class="form-control @error('color') is-invalid @enderror" 
                                                       id="color" name="color" value="{{ old('color', '#007bff') }}" 
                                                       style="height: 38px;">
                                                <div class="input-group-append">
                                                    <input type="text" class="form-control" id="color-text" 
                                                           value="{{ old('color', '#007bff') }}" readonly>
                                                </div>
                                            </div>
                                            @error('color')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="icon">أيقونة الخطة</label>
                                            <div class="input-group">
                                                <div class="input-group-prepend">
                                                    <span class="input-group-text">
                                                        <i id="icon-preview" class="fas fa-star"></i>
                                                    </span>
                                                </div>
                                                <input type="text" class="form-control @error('icon') is-invalid @enderror" 
                                                       id="icon" name="icon" value="{{ old('icon', 'fas fa-star') }}" 
                                                       placeholder="fas fa-star">
                                            </div>
                                            @error('icon')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="sort_order">ترتيب العرض</label>
                                            <input type="number" class="form-control @error('sort_order') is-invalid @enderror" 
                                                   id="sort_order" name="sort_order" value="{{ old('sort_order', 0) }}">
                                            @error('sort_order')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="badge">شارة الخطة</label>
                                            <input type="text" class="form-control @error('badge') is-invalid @enderror" 
                                                   id="badge" name="badge" value="{{ old('badge') }}" 
                                                   placeholder="مثال: الأكثر شعبية">
                                            @error('badge')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Status Settings -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-secondary">حالة الخطة</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                                   {{ old('is_active', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">
                                                الخطة نشطة
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" 
                                                   {{ old('is_featured') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_featured">
                                                خطة مميزة
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_popular" name="is_popular" value="1" 
                                                   {{ old('is_popular') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_popular">
                                                الأكثر شعبية
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> حفظ خطة الاشتراك
                            </button>
                            <a href="{{ route('admin.subscription-plans.index') }}" class="btn btn-secondary btn-lg ml-2">
                                <i class="fas fa-times"></i> إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Calculate final price and daily price
    function calculatePrices() {
        var price = parseFloat($('#price').val()) || 0;
        var discount = parseFloat($('#discount_percentage').val()) || 0;
        var duration = parseInt($('#duration_days').val()) || 1;
        
        // Calculate final price after discount
        var finalPrice = price - (price * discount / 100);
        $('#final-price').text(finalPrice.toFixed(2) + ' جنيه');
        
        // Calculate daily price
        var dailyPrice = finalPrice / duration;
        $('#daily-price').text(dailyPrice.toFixed(2) + ' جنيه');
    }

    // Update calculations when values change
    $('#price, #discount_percentage, #duration_days').on('input change', calculatePrices);

    // Handle custom duration
    $('#duration_days').on('change', function() {
        if ($(this).val() === 'custom') {
            $('#custom-duration').show();
            $('#custom_duration').attr('required', true);
        } else {
            $('#custom-duration').hide();
            $('#custom_duration').attr('required', false);
        }
        calculatePrices();
    });

    // Use custom duration for calculation
    $('#custom_duration').on('input', function() {
        if ($('#duration_days').val() === 'custom') {
            var customDuration = parseInt($(this).val()) || 1;
            // Temporarily set the duration for calculation
            var originalSelect = $('#duration_days');
            $('#duration_days').data('original', originalSelect.val());
            $('#duration_days').val(customDuration);
            calculatePrices();
            $('#duration_days').val('custom');
        }
    });

    // Auto-generate English name
    $('#name').on('input', function() {
        if ($('#name_en').val() === '') {
            var arabicName = $(this).val();
            // Basic transliteration
            var englishName = arabicName.replace(/ا/g, 'a')
                                       .replace(/ب/g, 'b')
                                       .replace(/ت/g, 't')
                                       .replace(/ث/g, 'th')
                                       .replace(/ج/g, 'j')
                                       .replace(/ح/g, 'h')
                                       .replace(/خ/g, 'kh')
                                       .replace(/د/g, 'd')
                                       .replace(/ذ/g, 'dh')
                                       .replace(/ر/g, 'r')
                                       .replace(/ز/g, 'z')
                                       .replace(/س/g, 's')
                                       .replace(/ش/g, 'sh')
                                       .replace(/ص/g, 's')
                                       .replace(/ض/g, 'd')
                                       .replace(/ط/g, 't')
                                       .replace(/ظ/g, 'z')
                                       .replace(/ع/g, 'a')
                                       .replace(/غ/g, 'gh')
                                       .replace(/ف/g, 'f')
                                       .replace(/ق/g, 'q')
                                       .replace(/ك/g, 'k')
                                       .replace(/ل/g, 'l')
                                       .replace(/م/g, 'm')
                                       .replace(/ن/g, 'n')
                                       .replace(/ه/g, 'h')
                                       .replace(/و/g, 'w')
                                       .replace(/ي/g, 'y');
            $('#name_en').val(englishName);
        }
    });

    // Update icon preview
    $('#icon').on('input', function() {
        var iconClass = $(this).val();
        $('#icon-preview').attr('class', iconClass);
    });

    // Update color text field
    $('#color').on('input', function() {
        $('#color-text').val($(this).val());
    });

    // Initial calculation
    calculatePrices();
});
</script>
@endpush
@endsection