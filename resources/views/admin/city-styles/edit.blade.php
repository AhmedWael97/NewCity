@extends('layouts.admin')

@section('title', 'تعديل تصميم المدينة - ' . $city->name_ar)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-palette"></i> تعديل تصميم: {{ $city->name_ar }}
        </h1>
        <div>
            <a href="{{ route('admin.city-styles.show', $city) }}" class="btn btn-info btn-sm">
                <i class="fas fa-eye"></i> معاينة
            </a>
            <a href="{{ route('admin.city-styles.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-right"></i> العودة
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show">
            <strong>خطأ!</strong> يرجى تصحيح الأخطاء التالية:
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">&times;</button>
        </div>
    @endif

    <form action="{{ route('admin.city-styles.update', $city) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- Main Styling Options -->
            <div class="col-lg-8">
                <!-- Enable Custom Styling -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-primary text-white">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-toggle-on"></i> إعدادات التصميم
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="custom-control custom-switch mb-3">
                            <input type="checkbox" 
                                   class="custom-control-input" 
                                   id="enable_custom_styling" 
                                   name="enable_custom_styling" 
                                   value="1"
                                   {{ old('enable_custom_styling', $city->enable_custom_styling) ? 'checked' : '' }}>
                            <label class="custom-control-label" for="enable_custom_styling">
                                <strong>تفعيل التصميم المخصص</strong>
                                <br><small class="text-muted">عند التفعيل، سيتم استخدام الألوان والتصميم المخصص لهذه المدينة</small>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- Color Scheme -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-gradient-primary text-white">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-fill-drip"></i> نظام الألوان
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="primary_color">اللون الأساسي</label>
                                <div class="input-group">
                                    <input type="color" 
                                           class="form-control form-control-color" 
                                           id="primary_color" 
                                           name="primary_color" 
                                           value="{{ old('primary_color', $city->getThemeConfig('colors.primary', '#3b82f6')) }}">
                                    <input type="text" 
                                           class="form-control" 
                                           id="primary_color_text"
                                           value="{{ old('primary_color', $city->getThemeConfig('colors.primary', '#3b82f6')) }}"
                                           readonly>
                                </div>
                                <small class="form-text text-muted">للعناوين والأزرار الرئيسية</small>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="secondary_color">اللون الثانوي</label>
                                <div class="input-group">
                                    <input type="color" 
                                           class="form-control form-control-color" 
                                           id="secondary_color" 
                                           name="secondary_color" 
                                           value="{{ old('secondary_color', $city->getThemeConfig('colors.secondary', '#64748b')) }}">
                                    <input type="text" 
                                           class="form-control"
                                           id="secondary_color_text"
                                           value="{{ old('secondary_color', $city->getThemeConfig('colors.secondary', '#64748b')) }}"
                                           readonly>
                                </div>
                                <small class="form-text text-muted">للنصوص الثانوية</small>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="accent_color">اللون المميز</label>
                                <div class="input-group">
                                    <input type="color" 
                                           class="form-control form-control-color" 
                                           id="accent_color" 
                                           name="accent_color" 
                                           value="{{ old('accent_color', $city->getThemeConfig('colors.accent', '#f59e0b')) }}">
                                    <input type="text" 
                                           class="form-control"
                                           id="accent_color_text"
                                           value="{{ old('accent_color', $city->getThemeConfig('colors.accent', '#f59e0b')) }}"
                                           readonly>
                                </div>
                                <small class="form-text text-muted">للعناصر المميزة</small>
                            </div>
                        </div>

                        <!-- Live Preview -->
                        <div class="alert alert-light border mt-3">
                            <strong>معاينة سريعة:</strong>
                            <div class="d-flex gap-2 mt-2">
                                <button type="button" class="btn btn-sm" id="preview_primary" style="background-color: {{ $city->getThemeConfig('colors.primary', '#3b82f6') }}; color: white;">زر أساسي</button>
                                <button type="button" class="btn btn-sm" id="preview_secondary" style="background-color: {{ $city->getThemeConfig('colors.secondary', '#64748b') }}; color: white;">زر ثانوي</button>
                                <button type="button" class="btn btn-sm" id="preview_accent" style="background-color: {{ $city->getThemeConfig('colors.accent', '#f59e0b') }}; color: white;">زر مميز</button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Background Settings -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-info text-white">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-image"></i> إعدادات الخلفية
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="background_style">نوع الخلفية</label>
                            <select class="form-control" id="background_style" name="background_style">
                                <option value="color" {{ old('background_style', $city->getThemeConfig('background.style', 'color')) == 'color' ? 'selected' : '' }}>لون واحد</option>
                                <option value="gradient" {{ old('background_style', $city->getThemeConfig('background.style', 'color')) == 'gradient' ? 'selected' : '' }}>تدرج لوني</option>
                                <option value="image" {{ old('background_style', $city->getThemeConfig('background.style', 'color')) == 'image' ? 'selected' : '' }}>صورة</option>
                            </select>
                        </div>

                        <!-- Single Color -->
                        <div id="background_color_section" class="bg-section">
                            <div class="form-group">
                                <label for="background_color">لون الخلفية</label>
                                <input type="color" 
                                       class="form-control form-control-color" 
                                       id="background_color" 
                                       name="background_color" 
                                       value="{{ old('background_color', $city->getThemeConfig('background.color', '#ffffff')) }}">
                            </div>
                        </div>

                        <!-- Gradient -->
                        <div id="background_gradient_section" class="bg-section" style="display: none;">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="background_gradient_start">بداية التدرج</label>
                                        <input type="color" 
                                               class="form-control form-control-color" 
                                               id="background_gradient_start" 
                                               name="background_gradient_start" 
                                               value="{{ old('background_gradient_start', $city->getThemeConfig('background.gradient.start', '#f8fafc')) }}">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="background_gradient_end">نهاية التدرج</label>
                                        <input type="color" 
                                               class="form-control form-control-color" 
                                               id="background_gradient_end" 
                                               name="background_gradient_end" 
                                               value="{{ old('background_gradient_end', $city->getThemeConfig('background.gradient.end', '#e2e8f0')) }}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Image -->
                        <div id="background_image_section" class="bg-section" style="display: none;">
                            <div class="form-group">
                                <label for="hero_image">صورة الخلفية</label>
                                <input type="file" 
                                       class="form-control-file" 
                                       id="hero_image" 
                                       name="hero_image" 
                                       accept="image/*">
                                <small class="form-text text-muted">الحد الأقصى: 2MB | الصيغ المدعومة: JPG, PNG, WebP</small>
                                
                                @if($city->hero_image)
                                    <div class="mt-2">
                                        <img src="{{ asset('storage/' . $city->hero_image) }}" 
                                             alt="Current Hero" 
                                             class="img-thumbnail" 
                                             style="max-height: 150px;">
                                        <p class="text-muted small mt-1">الصورة الحالية</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Typography -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-success text-white">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-font"></i> الخطوط
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="font_family">عائلة الخط</label>
                            <select class="form-control" id="font_family" name="font_family">
                                <option value="default" {{ old('font_family', $city->getThemeConfig('typography.font_family', 'cairo')) == 'default' ? 'selected' : '' }}>افتراضي</option>
                                <option value="cairo" {{ old('font_family', $city->getThemeConfig('typography.font_family', 'cairo')) == 'cairo' ? 'selected' : '' }}>Cairo (القاهرة)</option>
                                <option value="tajawal" {{ old('font_family', $city->getThemeConfig('typography.font_family', 'cairo')) == 'tajawal' ? 'selected' : '' }}>Tajawal (تجوال)</option>
                                <option value="amiri" {{ old('font_family', $city->getThemeConfig('typography.font_family', 'cairo')) == 'amiri' ? 'selected' : '' }}>Amiri (أميري)</option>
                                <option value="noto_sans_arabic" {{ old('font_family', $city->getThemeConfig('typography.font_family', 'cairo')) == 'noto_sans_arabic' ? 'selected' : '' }}>Noto Sans Arabic</option>
                            </select>
                        </div>
                    </div>
                </div>

                <!-- Custom CSS -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3 bg-warning text-white">
                        <h6 class="m-0 font-weight-bold">
                            <i class="fas fa-code"></i> CSS مخصص (متقدم)
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="custom_css">كود CSS إضافي</label>
                            <textarea class="form-control font-monospace" 
                                      id="custom_css" 
                                      name="custom_css" 
                                      rows="8" 
                                      style="font-size: 12px;"
                                      placeholder="/* أدخل كود CSS مخصص هنا */&#10;.custom-class {&#10;    color: #000;&#10;}">{{ old('custom_css', $city->custom_css) }}</textarea>
                            <small class="form-text text-muted">احذر: استخدم هذا فقط إذا كنت تعرف CSS</small>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Quick Actions -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-cog"></i> إجراءات سريعة
                        </h6>
                    </div>
                    <div class="card-body">
                        <button type="submit" class="btn btn-primary btn-block mb-2">
                            <i class="fas fa-save"></i> حفظ التغييرات
                        </button>

                        <a href="{{ route('admin.city-styles.css', $city) }}" 
                           class="btn btn-info btn-block mb-2" 
                           target="_blank">
                            <i class="fas fa-file-code"></i> عرض CSS المولد
                        </a>

                        <button type="button" 
                                class="btn btn-secondary btn-block mb-2"
                                onclick="if(confirm('هل تريد إعادة تعيين التصميم للإعدادات الافتراضية؟')) { document.getElementById('reset-form').submit(); }">
                            <i class="fas fa-undo"></i> إعادة تعيين
                        </button>
                    </div>
                </div>

                <!-- Color Presets -->
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">
                            <i class="fas fa-swatchbook"></i> قوالب جاهزة
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="preset-theme mb-2 p-2 border rounded cursor-pointer" data-preset="blue">
                            <strong>الأزرق الكلاسيكي</strong>
                            <div class="d-flex gap-1 mt-1">
                                <span class="badge" style="background: #3b82f6; width: 30px;">&nbsp;</span>
                                <span class="badge" style="background: #64748b; width: 30px;">&nbsp;</span>
                                <span class="badge" style="background: #f59e0b; width: 30px;">&nbsp;</span>
                            </div>
                        </div>
                        
                        <div class="preset-theme mb-2 p-2 border rounded cursor-pointer" data-preset="green">
                            <strong>الأخضر الطبيعي</strong>
                            <div class="d-flex gap-1 mt-1">
                                <span class="badge" style="background: #10b981; width: 30px;">&nbsp;</span>
                                <span class="badge" style="background: #059669; width: 30px;">&nbsp;</span>
                                <span class="badge" style="background: #84cc16; width: 30px;">&nbsp;</span>
                            </div>
                        </div>

                        <div class="preset-theme mb-2 p-2 border rounded cursor-pointer" data-preset="purple">
                            <strong>البنفسجي الملكي</strong>
                            <div class="d-flex gap-1 mt-1">
                                <span class="badge" style="background: #8b5cf6; width: 30px;">&nbsp;</span>
                                <span class="badge" style="background: #6d28d9; width: 30px;">&nbsp;</span>
                                <span class="badge" style="background: #ec4899; width: 30px;">&nbsp;</span>
                            </div>
                        </div>

                        <div class="preset-theme mb-2 p-2 border rounded cursor-pointer" data-preset="orange">
                            <strong>البرتقالي الدافئ</strong>
                            <div class="d-flex gap-1 mt-1">
                                <span class="badge" style="background: #f97316; width: 30px;">&nbsp;</span>
                                <span class="badge" style="background: #ea580c; width: 30px;">&nbsp;</span>
                                <span class="badge" style="background: #fbbf24; width: 30px;">&nbsp;</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Help -->
                <div class="card shadow">
                    <div class="card-header py-3 bg-light">
                        <h6 class="m-0 font-weight-bold text-secondary">
                            <i class="fas fa-question-circle"></i> مساعدة
                        </h6>
                    </div>
                    <div class="card-body">
                        <ul class="small mb-0">
                            <li>استخدم الألوان المتناسقة لتجربة أفضل</li>
                            <li>اختبر التصميم على أجهزة مختلفة</li>
                            <li>يمكنك استخدام القوالب الجاهزة</li>
                            <li>التغييرات تنعكس فوراً في التطبيق</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </form>

    <!-- Reset Form (Hidden) -->
    <form id="reset-form" 
          action="{{ route('admin.city-styles.reset', $city) }}" 
          method="POST" 
          style="display: none;">
        @csrf
        @method('PATCH')
    </form>
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Background style switcher
    function toggleBackgroundSections() {
        const style = $('#background_style').val();
        $('.bg-section').hide();
        
        if (style === 'color') {
            $('#background_color_section').show();
        } else if (style === 'gradient') {
            $('#background_gradient_section').show();
        } else if (style === 'image') {
            $('#background_image_section').show();
        }
    }
    
    $('#background_style').on('change', toggleBackgroundSections);
    toggleBackgroundSections();

    // Color picker sync with text input
    $('input[type="color"]').on('input', function() {
        const textInputId = $(this).attr('id') + '_text';
        $('#' + textInputId).val($(this).val());
        updatePreview();
    });

    // Update live preview
    function updatePreview() {
        $('#preview_primary').css('background-color', $('#primary_color').val());
        $('#preview_secondary').css('background-color', $('#secondary_color').val());
        $('#preview_accent').css('background-color', $('#accent_color').val());
    }

    // Color presets
    const presets = {
        blue: { primary: '#3b82f6', secondary: '#64748b', accent: '#f59e0b' },
        green: { primary: '#10b981', secondary: '#059669', accent: '#84cc16' },
        purple: { primary: '#8b5cf6', secondary: '#6d28d9', accent: '#ec4899' },
        orange: { primary: '#f97316', secondary: '#ea580c', accent: '#fbbf24' }
    };

    $('.preset-theme').on('click', function() {
        const presetName = $(this).data('preset');
        const preset = presets[presetName];
        
        if (preset) {
            $('#primary_color').val(preset.primary);
            $('#primary_color_text').val(preset.primary);
            $('#secondary_color').val(preset.secondary);
            $('#secondary_color_text').val(preset.secondary);
            $('#accent_color').val(preset.accent);
            $('#accent_color_text').val(preset.accent);
            updatePreview();
            
            // Visual feedback
            $('.preset-theme').removeClass('border-primary');
            $(this).addClass('border-primary');
        }
    });

    // Initial preview update
    updatePreview();
});
</script>
@endpush

@push('styles')
<style>
.cursor-pointer {
    cursor: pointer;
}
.preset-theme:hover {
    background-color: #f8f9fa;
}
.form-control-color {
    width: 60px;
    height: 38px;
    padding: 2px;
}
</style>
@endpush
