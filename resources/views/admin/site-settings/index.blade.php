@extends('layouts.admin')

@section('title', 'إعدادات الموقع')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-cog"></i> إعدادات الموقع
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
                    <a class="nav-link active" href="{{ route('admin.site-settings.index') }}">
                        <i class="fas fa-info-circle me-1"></i> معلومات الموقع
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="{{ route('admin.site-settings.seo') }}">
                        <i class="fas fa-search me-1"></i> إعدادات SEO
                    </a>
                </li>
            </ul>
        </div>
    </div>

    <form action="{{ route('admin.site-settings.update') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <!-- Basic Info -->
            <div class="col-lg-8">
                <!-- Site Identity -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-id-card me-2"></i>هوية الموقع
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="site_name" class="form-label required">اسم الموقع (English)</label>
                                <input type="text" class="form-control @error('site_name') is-invalid @enderror" 
                                       id="site_name" name="site_name" value="{{ old('site_name', $settings['site_name']) }}" required>
                                @error('site_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="site_name_ar" class="form-label required">اسم الموقع (عربي)</label>
                                <input type="text" class="form-control @error('site_name_ar') is-invalid @enderror" 
                                       id="site_name_ar" name="site_name_ar" value="{{ old('site_name_ar', $settings['site_name_ar']) }}" required>
                                @error('site_name_ar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="site_tagline" class="form-label">الشعار (English)</label>
                                <input type="text" class="form-control @error('site_tagline') is-invalid @enderror" 
                                       id="site_tagline" name="site_tagline" value="{{ old('site_tagline', $settings['site_tagline']) }}">
                                @error('site_tagline')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="site_tagline_ar" class="form-label">الشعار (عربي)</label>
                                <input type="text" class="form-control @error('site_tagline_ar') is-invalid @enderror" 
                                       id="site_tagline_ar" name="site_tagline_ar" value="{{ old('site_tagline_ar', $settings['site_tagline_ar']) }}">
                                @error('site_tagline_ar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="site_description" class="form-label">وصف الموقع (English)</label>
                                <textarea class="form-control @error('site_description') is-invalid @enderror" 
                                          id="site_description" name="site_description" rows="3">{{ old('site_description', $settings['site_description']) }}</textarea>
                                @error('site_description')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="site_description_ar" class="form-label">وصف الموقع (عربي)</label>
                                <textarea class="form-control @error('site_description_ar') is-invalid @enderror" 
                                          id="site_description_ar" name="site_description_ar" rows="3">{{ old('site_description_ar', $settings['site_description_ar']) }}</textarea>
                                @error('site_description_ar')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-address-book me-2"></i>معلومات الاتصال
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="contact_email" class="form-label">البريد الإلكتروني</label>
                                <input type="email" class="form-control @error('contact_email') is-invalid @enderror" 
                                       id="contact_email" name="contact_email" value="{{ old('contact_email', $settings['contact_email']) }}">
                                @error('contact_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="contact_phone" class="form-label">رقم الهاتف</label>
                                <input type="text" class="form-control @error('contact_phone') is-invalid @enderror" 
                                       id="contact_phone" name="contact_phone" value="{{ old('contact_phone', $settings['contact_phone']) }}">
                                @error('contact_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-12 mb-3">
                                <label for="contact_address" class="form-label">العنوان</label>
                                <textarea class="form-control @error('contact_address') is-invalid @enderror" 
                                          id="contact_address" name="contact_address" rows="2">{{ old('contact_address', $settings['contact_address']) }}</textarea>
                                @error('contact_address')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Social Media -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-share-alt me-2"></i>روابط التواصل الاجتماعي
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="facebook_url" class="form-label">
                                    <i class="fab fa-facebook text-primary me-1"></i> Facebook
                                </label>
                                <input type="url" class="form-control @error('facebook_url') is-invalid @enderror" 
                                       id="facebook_url" name="facebook_url" value="{{ old('facebook_url', $settings['facebook_url']) }}"
                                       placeholder="https://facebook.com/yourpage">
                                @error('facebook_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="twitter_url" class="form-label">
                                    <i class="fab fa-twitter text-info me-1"></i> Twitter
                                </label>
                                <input type="url" class="form-control @error('twitter_url') is-invalid @enderror" 
                                       id="twitter_url" name="twitter_url" value="{{ old('twitter_url', $settings['twitter_url']) }}"
                                       placeholder="https://twitter.com/yourprofile">
                                @error('twitter_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="instagram_url" class="form-label">
                                    <i class="fab fa-instagram text-danger me-1"></i> Instagram
                                </label>
                                <input type="url" class="form-control @error('instagram_url') is-invalid @enderror" 
                                       id="instagram_url" name="instagram_url" value="{{ old('instagram_url', $settings['instagram_url']) }}"
                                       placeholder="https://instagram.com/yourprofile">
                                @error('instagram_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="youtube_url" class="form-label">
                                    <i class="fab fa-youtube text-danger me-1"></i> YouTube
                                </label>
                                <input type="url" class="form-control @error('youtube_url') is-invalid @enderror" 
                                       id="youtube_url" name="youtube_url" value="{{ old('youtube_url', $settings['youtube_url']) }}"
                                       placeholder="https://youtube.com/yourchannel">
                                @error('youtube_url')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Media Sidebar -->
            <div class="col-lg-4">
                <!-- Logo -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-warning text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-image me-2"></i>شعار الموقع (Logo)
                        </h5>
                    </div>
                    <div class="card-body text-center">
                        @if($settings['site_logo'])
                            <div class="mb-3">
                                <img src="{{ asset('storage/' . $settings['site_logo']) }}" 
                                     alt="Logo" 
                                     class="img-fluid rounded shadow-sm"
                                     style="max-height: 150px;">
                            </div>
                        @else
                            <div class="mb-3 bg-light p-4 rounded">
                                <i class="fas fa-image fa-3x text-muted"></i>
                                <p class="text-muted mt-2">لم يتم تحميل شعار بعد</p>
                            </div>
                        @endif
                        
                        <div class="mb-3">
                            <label for="site_logo" class="form-label">تحميل شعار جديد</label>
                            <input type="file" class="form-control @error('site_logo') is-invalid @enderror" 
                                   id="site_logo" name="site_logo" accept="image/*">
                            <small class="text-muted">PNG, JPG, SVG (Max 2MB)</small>
                            @error('site_logo')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Favicon -->
                <div class="card shadow mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-bookmark me-2"></i>أيقونة الموقع (Favicon)
                        </h5>
                    </div>
                    <div class="card-body text-center">
                        @if($settings['site_favicon'])
                            <div class="mb-3">
                                <img src="{{ asset('storage/' . $settings['site_favicon']) }}" 
                                     alt="Favicon" 
                                     class="rounded"
                                     style="width: 64px; height: 64px;">
                            </div>
                        @else
                            <div class="mb-3 bg-light p-4 rounded">
                                <i class="fas fa-bookmark fa-3x text-muted"></i>
                                <p class="text-muted mt-2">لم يتم تحميل أيقونة بعد</p>
                            </div>
                        @endif
                        
                        <div class="mb-3">
                            <label for="site_favicon" class="form-label">تحميل أيقونة جديدة</label>
                            <input type="file" class="form-control @error('site_favicon') is-invalid @enderror" 
                                   id="site_favicon" name="site_favicon" accept=".ico,.png">
                            <small class="text-muted">ICO, PNG (Max 1MB)</small>
                            @error('site_favicon')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Info Card -->
                <div class="card shadow bg-light">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="fas fa-info-circle me-2"></i>معلومات مهمة
                        </h6>
                        <ul class="small mb-0">
                            <li>الشعار (Logo) يظهر في الرأسية وجميع الصفحات</li>
                            <li>الأيقونة (Favicon) تظهر في تبويب المتصفح</li>
                            <li>تأكد من استخدام صور بجودة عالية</li>
                            <li>يفضل أن يكون الشعار بخلفية شفافة (PNG)</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="card shadow">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-2"></i>حفظ التغييرات
                    </button>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline-secondary btn-lg">
                        <i class="fas fa-times me-2"></i>إلغاء
                    </a>
                </div>
            </div>
        </div>
    </form>
</div>

@push('styles')
<style>
    .required::after {
        content: " *";
        color: #dc3545;
    }
</style>
@endpush
@endsection
