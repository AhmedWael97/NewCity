@extends('layouts.admin')

@section('title', 'إضافة مدينة جديدة')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plus"></i> إضافة مدينة جديدة
        </h1>
        <a href="{{ route('admin.cities.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-right"></i> العودة للقائمة
        </a>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">معلومات المدينة</h6>
                </div>
                <div class="card-body">
                    {{-- Display validation errors --}}
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h5 class="alert-heading">
                                <i class="fas fa-exclamation-triangle"></i> يوجد أخطاء في البيانات المدخلة
                            </h5>
                            <hr>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.cities.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-4">
                                <x-form.input
                                    name="name"
                                    label="اسم المدينة"
                                    icon="city"
                                    :required="true"
                                    placeholder="مثال: القاهرة"
                                />
                            </div>
                            
                            <div class="col-md-4">
                                <x-form.input
                                    name="slug"
                                    label="الرابط (Slug)"
                                    icon="link"
                                    :required="true"
                                    placeholder="cairo"
                                    help-text="يُستخدم في الروابط"
                                />
                            </div>

                            <div class="col-md-4">
                                <x-form.input
                                    name="country"
                                    label="الدولة"
                                    icon="flag"
                                    :required="true"
                                    placeholder="مثال: مصر"
                                />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <x-form.input
                                    name="state"
                                    label="الولاية / المحافظة"
                                    icon="map-marker-alt"
                                    placeholder="مثال: محافظة القاهرة"
                                />
                            </div>

                            <div class="col-md-6">
                                <x-form.checkbox
                                    name="is_active"
                                    label="المدينة نشطة"
                                    :checked="true"
                                    help-text="تفعيل/تعطيل ظهور المدينة"
                                />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-12">
                                <x-form.textarea
                                    name="description"
                                    label="وصف المدينة"
                                    :rows="4"
                                    placeholder="أدخل وصفاً تفصيلياً للمدينة..."
                                />
                            </div>
                        </div>

                        <!-- Location Information -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-map-marked-alt"></i> المعلومات الجغرافية
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <x-form.input
                                            name="latitude"
                                            type="number"
                                            label="خط العرض (Latitude)"
                                            icon="compass"
                                            placeholder="30.0444"
                                            step="0.000001"
                                            help-text="قيمة من -90 إلى 90"
                                        />
                                    </div>

                                    <div class="col-md-6">
                                        <x-form.input
                                            name="longitude"
                                            type="number"
                                            label="خط الطول (Longitude)"
                                            icon="compass"
                                            placeholder="31.2357"
                                            step="0.000001"
                                            help-text="قيمة من -180 إلى 180"
                                        />
                                    </div>
                                </div>


                            </div>
                        </div>

                        <!-- Media Section -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">
                                    <i class="fas fa-images"></i> الصور والوسائط
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <x-form.file
                                            name="image"
                                            label="صورة المدينة"
                                            accept="image/*"
                                            :preview="true"
                                            help-text="اختر صورة بصيغة JPG, PNG, GIF (حد أقصى 2 ميجابايت)"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Contact Information -->
                        <div class="card mt-4">
                            <div class="card-header bg-info text-white">
                                <h6 class="m-0 font-weight-bold">
                                    <i class="fas fa-address-book"></i> معلومات الاتصال (خاصة بكل مدينة)
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <x-form.input
                                            name="contact_phone"
                                            label="رقم الهاتف"
                                            icon="phone"
                                            placeholder="+20 123 456 7890"
                                        />
                                    </div>
                                    <div class="col-md-6">
                                        <x-form.input
                                            name="contact_whatsapp"
                                            label="رقم واتساب"
                                            icon="whatsapp"
                                            placeholder="+20 123 456 7890"
                                        />
                                    </div>
                                    <div class="col-md-6">
                                        <x-form.input
                                            name="contact_email"
                                            type="email"
                                            label="البريد الإلكتروني"
                                            icon="envelope"
                                            placeholder="info@city.com"
                                        />
                                    </div>
                                    <div class="col-md-6">
                                        <x-form.textarea
                                            name="contact_address"
                                            label="العنوان"
                                            :rows="2"
                                            placeholder="العنوان الكامل للمدينة..."
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Branding -->
                        <div class="card mt-4">
                            <div class="card-header bg-warning text-white">
                                <h6 class="m-0 font-weight-bold">
                                    <i class="fas fa-palette"></i> العلامة التجارية (Logo & Favicon)
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <label>شعار المدينة (Logo)</label>
                                        <input type="file" name="logo" class="form-control" accept="image/*">
                                        <small class="text-muted">PNG, JPG, SVG (Max 2MB)</small>
                                    </div>
                                    <div class="col-md-4">
                                        <label>أيقونة المدينة (Favicon)</label>
                                        <input type="file" name="favicon" class="form-control" accept=".png,.ico">
                                        <small class="text-muted">PNG, ICO (Max 1MB)</small>
                                    </div>
                                    <div class="col-md-4">
                                        <label>صورة المشاركة (OG Image)</label>
                                        <input type="file" name="og_image" class="form-control" accept="image/*">
                                        <small class="text-muted">1200x630px (Max 2MB)</small>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- SEO Settings -->
                        <div class="card mt-4">
                            <div class="card-header bg-primary text-white">
                                <h6 class="m-0 font-weight-bold">
                                    <i class="fas fa-search"></i> إعدادات SEO (خاصة بكل مدينة)
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <x-form.input
                                            name="meta_title"
                                            label="Meta Title (English)"
                                            icon="heading"
                                            placeholder="Best City Services"
                                            maxlength="60"
                                        />
                                    </div>
                                    <div class="col-md-6">
                                        <x-form.input
                                            name="meta_title_ar"
                                            label="Meta Title (عربي)"
                                            icon="heading"
                                            placeholder="أفضل خدمات المدينة"
                                            maxlength="60"
                                        />
                                    </div>
                                    <div class="col-md-6">
                                        <x-form.textarea
                                            name="meta_description"
                                            label="Meta Description (English)"
                                            :rows="3"
                                            placeholder="City description for search engines..."
                                            maxlength="160"
                                        />
                                    </div>
                                    <div class="col-md-6">
                                        <x-form.textarea
                                            name="meta_description_ar"
                                            label="Meta Description (عربي)"
                                            :rows="3"
                                            placeholder="وصف المدينة لمحركات البحث..."
                                            maxlength="160"
                                        />
                                    </div>
                                    <div class="col-md-6">
                                        <x-form.input
                                            name="meta_keywords"
                                            label="Meta Keywords (English)"
                                            icon="tags"
                                            placeholder="city, services, local business"
                                        />
                                    </div>
                                    <div class="col-md-6">
                                        <x-form.input
                                            name="meta_keywords_ar"
                                            label="Meta Keywords (عربي)"
                                            icon="tags"
                                            placeholder="مدينة، خدمات، أعمال محلية"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Social Media -->
                        <div class="card mt-4">
                            <div class="card-header bg-success text-white">
                                <h6 class="m-0 font-weight-bold">
                                    <i class="fas fa-share-alt"></i> روابط التواصل الاجتماعي
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <x-form.input
                                            name="facebook_url"
                                            label="Facebook"
                                            icon="facebook"
                                            placeholder="https://facebook.com/citypage"
                                        />
                                    </div>
                                    <div class="col-md-6">
                                        <x-form.input
                                            name="twitter_url"
                                            label="Twitter"
                                            icon="twitter"
                                            placeholder="https://twitter.com/cityaccount"
                                        />
                                    </div>
                                    <div class="col-md-6">
                                        <x-form.input
                                            name="instagram_url"
                                            label="Instagram"
                                            icon="instagram"
                                            placeholder="https://instagram.com/cityaccount"
                                        />
                                    </div>
                                    <div class="col-md-6">
                                        <x-form.input
                                            name="youtube_url"
                                            label="YouTube"
                                            icon="youtube"
                                            placeholder="https://youtube.com/citychannel"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Analytics -->
                        <div class="card mt-4">
                            <div class="card-header bg-secondary text-white">
                                <h6 class="m-0 font-weight-bold">
                                    <i class="fas fa-chart-line"></i> أدوات التحليل والتتبع
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <x-form.input
                                            name="google_analytics_id"
                                            label="Google Analytics ID"
                                            icon="google"
                                            placeholder="G-XXXXXXXXXX"
                                        />
                                    </div>
                                    <div class="col-md-6">
                                        <x-form.input
                                            name="facebook_pixel_id"
                                            label="Facebook Pixel ID"
                                            icon="facebook"
                                            placeholder="1234567890"
                                        />
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> حفظ المدينة
                            </button>
                            <a href="{{ route('admin.cities.index') }}" class="btn btn-secondary ml-2">
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
    // Auto-generate slug from name
    $('#name').on('input', function() {
        var name = $(this).val();
        var slug = name.toLowerCase()
                      .replace(/[^\w\s-]/g, '') // Remove special characters
                      .replace(/\s+/g, '-');    // Replace spaces with hyphens
        $('#slug').val(slug);
    });

    // Auto-generate English name from Arabic (basic transliteration)
    $('#name').on('input', function() {
        var arabicName = $(this).val();
        // This is a basic example - you might want to use a proper transliteration library
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
        
        if ($('#name_en').val() === '') {
            $('#name_en').val(englishName);
        }
    });
});
</script>
@endpush
@endsection