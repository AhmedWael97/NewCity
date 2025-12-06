@extends('layouts.admin')

@section('title', 'إعدادات SEO')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-search"></i> إعدادات تحسين محركات البحث (SEO)
        </h1>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Settings Navigation -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <ul class="nav nav-pills">
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.site-settings.index') }}">
                        <i class="fas fa-info-circle me-1"></i> معلومات الموقع
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="{{ route('admin.site-settings.seo') }}">
                        <i class="fas fa-search me-1"></i> إعدادات SEO
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <form action="{{ route('admin.site-settings.seo.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <!-- SEO Settings -->
            <div class="col-lg-8">
                <!-- Meta Tags -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-tags me-2"></i>عناوين ووصف الميتا تاج
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="meta_title" class="form-label">Meta Title (English)</label>
                                <input type="text" class="form-control @error('meta_title') is-invalid @enderror" 
                                       id="meta_title" name="meta_title" value="{{ old('meta_title', $settings['meta_title']) }}"
                                       maxlength="60">
                                <small class="text-muted">الطول الأمثل: 50-60 حرف</small>
                                @error('meta_title')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="meta_title_ar" class="form-label">Meta Title (عربي)</label>
                                <input type="text" class="form-control @error('meta_title_ar') is-invalid @enderror" 
                                       id="meta_title_ar" name="meta_title_ar" value="{{ old('meta_title_ar', $settings['meta_title_ar']) }}"
                                       maxlength="60">
                                <small class="text-muted">الطول الأمثل: 50-60 حرف</small>
                                @error('meta_title_ar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="meta_description" class="form-label">Meta Description (English)</label>
                                <textarea class="form-control @error('meta_description') is-invalid @enderror" 
                                          id="meta_description" name="meta_description" rows="3" maxlength="160">{{ old('meta_description', $settings['meta_description']) }}</textarea>
                                <small class="text-muted">الطول الأمثل: 150-160 حرف</small>
                                @error('meta_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="meta_description_ar" class="form-label">Meta Description (عربي)</label>
                                <textarea class="form-control @error('meta_description_ar') is-invalid @enderror" 
                                          id="meta_description_ar" name="meta_description_ar" rows="3" maxlength="160">{{ old('meta_description_ar', $settings['meta_description_ar']) }}</textarea>
                                <small class="text-muted">الطول الأمثل: 150-160 حرف</small>
                                @error('meta_description_ar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="meta_keywords" class="form-label">Meta Keywords (English)</label>
                                <input type="text" class="form-control @error('meta_keywords') is-invalid @enderror" 
                                       id="meta_keywords" name="meta_keywords" value="{{ old('meta_keywords', $settings['meta_keywords']) }}"
                                       placeholder="keyword1, keyword2, keyword3">
                                <small class="text-muted">افصل بين الكلمات بفاصلة</small>
                                @error('meta_keywords')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="meta_keywords_ar" class="form-label">Meta Keywords (عربي)</label>
                                <input type="text" class="form-control @error('meta_keywords_ar') is-invalid @enderror" 
                                       id="meta_keywords_ar" name="meta_keywords_ar" value="{{ old('meta_keywords_ar', $settings['meta_keywords_ar']) }}"
                                       placeholder="كلمة1، كلمة2، كلمة3">
                                <small class="text-muted">افصل بين الكلمات بفاصلة</small>
                                @error('meta_keywords_ar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Analytics & Tracking -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-chart-line me-2"></i>أدوات التحليل والتتبع
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="google_analytics_id" class="form-label">
                                    <i class="fab fa-google text-danger me-1"></i> Google Analytics ID
                                </label>
                                <input type="text" class="form-control @error('google_analytics_id') is-invalid @enderror" 
                                       id="google_analytics_id" name="google_analytics_id" 
                                       value="{{ old('google_analytics_id', $settings['google_analytics_id']) }}"
                                       placeholder="G-XXXXXXXXXX or UA-XXXXXXXXX-X">
                                @error('google_analytics_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="facebook_pixel_id" class="form-label">
                                    <i class="fab fa-facebook text-primary me-1"></i> Facebook Pixel ID
                                </label>
                                <input type="text" class="form-control @error('facebook_pixel_id') is-invalid @enderror" 
                                       id="facebook_pixel_id" name="facebook_pixel_id" 
                                       value="{{ old('facebook_pixel_id', $settings['facebook_pixel_id']) }}"
                                       placeholder="1234567890">
                                @error('facebook_pixel_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="google_site_verification" class="form-label">
                                    <i class="fab fa-google text-danger me-1"></i> Google Site Verification Code
                                </label>
                                <input type="text" class="form-control @error('google_site_verification') is-invalid @enderror" 
                                       id="google_site_verification" name="google_site_verification" 
                                       value="{{ old('google_site_verification', $settings['google_site_verification']) }}"
                                       placeholder="xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx">
                                <small class="text-muted">الكود من Google Search Console</small>
                                @error('google_site_verification')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Open Graph Image Sidebar -->
            <div class="col-lg-4">
                <!-- OG Image -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-share-alt me-2"></i>صورة المشاركة (OG Image)
                        </h5>
                    </div>
                    <div class="card-body text-center">
                        @if($settings['og_image'])
                            <div class="mb-3">
                                <img src="{{ asset('storage/' . $settings['og_image']) }}" 
                                     alt="OG Image" 
                                     class="img-fluid rounded shadow-sm"
                                     style="max-height: 200px;">
                            </div>
                        @else
                            <div class="mb-3 bg-light p-4 rounded">
                                <i class="fas fa-image fa-3x text-muted"></i>
                                <p class="text-muted mt-2">لم يتم تحميل صورة بعد</p>
                            </div>
                        @endif
                        
                        <div class="mb-3">
                            <label for="og_image" class="form-label">تحميل صورة جديدة</label>
                            <input type="file" class="form-control @error('og_image') is-invalid @enderror" 
                                   id="og_image" name="og_image" accept="image/*">
                            <small class="text-muted">الحجم الأمثل: 1200x630 بكسل (Max 2MB)</small>
                            @error('og_image')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- SEO Tips Card -->
                <div class="card shadow bg-light">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="fas fa-lightbulb me-2 text-warning"></i>نصائح SEO
                        </h6>
                        <ul class="small mb-0">
                            <li><strong>Meta Title:</strong> 50-60 حرف</li>
                            <li><strong>Meta Description:</strong> 150-160 حرف</li>
                            <li><strong>OG Image:</strong> 1200x630 بكسل</li>
                            <li>استخدم كلمات مفتاحية ذات صلة</li>
                            <li>اجعل العنوان والوصف جذابين</li>
                            <li>صورة OG تظهر عند المشاركة</li>
                            <li>Google Analytics يتتبع الزوار</li>
                            <li>Facebook Pixel يتتبع التحويلات</li>
                        </ul>
                    </div>
                </div>

                <!-- SEO Preview Card -->
                <div class="card shadow mt-3">
                    <div class="card-header bg-secondary text-white">
                        <h6 class="mb-0">
                            <i class="fas fa-eye me-2"></i>معاينة في Google
                        </h6>
                    </div>
                    <div class="card-body">
                        <div class="seo-preview">
                            <h6 class="text-primary mb-0" id="preview-title">
                                {{ $settings['meta_title'] ?: 'عنوان الموقع' }}
                            </h6>
                            <small class="text-success d-block mb-1">{{ url('/') }}</small>
                            <p class="small text-muted mb-0" id="preview-description">
                                {{ $settings['meta_description'] ?: 'وصف الموقع سيظهر هنا...' }}
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="card shadow">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-2"></i>حفظ إعدادات SEO
                    </button>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-times me-2"></i>إلغاء
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
    // Live SEO Preview
    document.getElementById('meta_title').addEventListener('input', function() {
        document.getElementById('preview-title').textContent = this.value || 'عنوان الموقع';
    });
    
    document.getElementById('meta_description').addEventListener('input', function() {
        document.getElementById('preview-description').textContent = this.value || 'وصف الموقع سيظهر هنا...';
    });
    
    // Character counter for meta fields
    function updateCharCount(inputId, maxLength) {
        const input = document.getElementById(inputId);
        const counter = document.createElement('small');
        counter.className = 'text-muted float-end';
        input.parentElement.appendChild(counter);
        
        function update() {
            const count = input.value.length;
            counter.textContent = `${count}/${maxLength}`;
            counter.className = count > maxLength ? 'text-danger float-end' : 'text-muted float-end';
        }
        
        input.addEventListener('input', update);
        update();
    }
    
    updateCharCount('meta_title', 60);
    updateCharCount('meta_title_ar', 60);
    updateCharCount('meta_description', 160);
    updateCharCount('meta_description_ar', 160);
</script>
@endpush

@push('styles')
<style>
    .seo-preview {
        padding: 10px;
        background: #f8f9fa;
        border-radius: 8px;
    }
    .seo-preview h6 {
        font-size: 18px;
        line-height: 1.3;
        cursor: pointer;
    }
    .seo-preview h6:hover {
        text-decoration: underline;
    }
</style>
@endpush
@endsection
