@extends('layouts.admin')

@section('title', 'إضافة خدمة جديدة')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2">إضافة خدمة جديدة</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.user-services.index') }}">الخدمات</a></li>
                    <li class="breadcrumb-item active">إضافة خدمة</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.user-services.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-right me-2"></i>رجوع
            </a>
        </div>
    </div>

    @if($errors->any())
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <strong>يوجد أخطاء في النموذج:</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <form action="{{ route('admin.user-services.store') }}" method="POST" enctype="multipart/form-data">
        @csrf

        <div class="row">
            <!-- User Account Information -->
            <div class="col-lg-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-user me-2"></i>معلومات صاحب الخدمة (سيتم إنشاء حساب جديد)
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="user_name" class="form-label required">اسم صاحب الخدمة</label>
                                <input type="text" class="form-control @error('user_name') is-invalid @enderror" 
                                       id="user_name" name="user_name" value="{{ old('user_name') }}" required>
                                @error('user_name')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="user_email" class="form-label required">البريد الإلكتروني</label>
                                <input type="email" class="form-control @error('user_email') is-invalid @enderror" 
                                       id="user_email" name="user_email" value="{{ old('user_email') }}" required>
                                <small class="text-muted">سيتم استخدامه لتسجيل الدخول</small>
                                @error('user_email')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="user_phone" class="form-label required">رقم الجوال</label>
                                <input type="text" class="form-control @error('user_phone') is-invalid @enderror" 
                                       id="user_phone" name="user_phone" value="{{ old('user_phone') }}" 
                                       placeholder="05xxxxxxxx" required>
                                @error('user_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="user_password" class="form-label required">كلمة المرور</label>
                                <div class="input-group">
                                    <input type="password" class="form-control @error('user_password') is-invalid @enderror" 
                                           id="user_password" name="user_password" required>
                                    <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                </div>
                                <small class="text-muted">8 أحرف على الأقل</small>
                                @error('user_password')
                                    <div class="invalid-feedback d-block">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="alert alert-info mb-0">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>ملاحظة:</strong> سيتم إنشاء حساب مستخدم جديد تلقائياً باستخدام هذه المعلومات. يمكن للمستخدم تسجيل الدخول باستخدام البريد الإلكتروني وكلمة المرور.
                        </div>
                    </div>
                </div>
            </div>

            <!-- Service Information -->
            <div class="col-lg-8">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-briefcase me-2"></i>معلومات الخدمة
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <label for="title" class="form-label required">عنوان الخدمة</label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label required">وصف الخدمة</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="5" required>{{ old('description') }}</textarea>
                            <small class="text-muted">اشرح الخدمة بالتفصيل لجذب المزيد من العملاء</small>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="service_category_id" class="form-label required">الفئة</label>
                                <select class="form-select @error('service_category_id') is-invalid @enderror" 
                                        id="service_category_id" name="service_category_id" required>
                                    <option value="">اختر الفئة</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('service_category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name_ar ?? $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('service_category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="city_id" class="form-label required">المدينة</label>
                                <select class="form-select @error('city_id') is-invalid @enderror" 
                                        id="city_id" name="city_id" required>
                                    <option value="">اختر المدينة</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>
                                            {{ $city->name_ar ?? $city->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('city_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="pricing_type" class="form-label required">نوع التسعير</label>
                                <select class="form-select @error('pricing_type') is-invalid @enderror" 
                                        id="pricing_type" name="pricing_type" required>
                                    <option value="fixed" {{ old('pricing_type') == 'fixed' ? 'selected' : '' }}>سعر ثابت</option>
                                    <option value="hourly" {{ old('pricing_type') == 'hourly' ? 'selected' : '' }}>بالساعة</option>
                                    <option value="per_km" {{ old('pricing_type') == 'per_km' ? 'selected' : '' }}>بالكيلومتر</option>
                                    <option value="negotiable" {{ old('pricing_type') == 'negotiable' ? 'selected' : '' }}>قابل للتفاوض</option>
                                </select>
                                @error('pricing_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="price_from" class="form-label">السعر من (ريال)</label>
                                <input type="number" class="form-control @error('price_from') is-invalid @enderror" 
                                       id="price_from" name="price_from" value="{{ old('price_from') }}" 
                                       step="0.01" min="0">
                                @error('price_from')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="price_to" class="form-label">السعر إلى (ريال)</label>
                                <input type="number" class="form-control @error('price_to') is-invalid @enderror" 
                                       id="price_to" name="price_to" value="{{ old('price_to') }}" 
                                       step="0.01" min="0">
                                @error('price_to')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="phone" class="form-label required">رقم التواصل</label>
                                <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                       id="phone" name="phone" value="{{ old('phone') }}" 
                                       placeholder="05xxxxxxxx" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="whatsapp" class="form-label">واتساب</label>
                                <input type="text" class="form-control @error('whatsapp') is-invalid @enderror" 
                                       id="whatsapp" name="whatsapp" value="{{ old('whatsapp') }}" 
                                       placeholder="05xxxxxxxx">
                                @error('whatsapp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">العنوان</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" name="address" rows="2">{{ old('address') }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="images" class="form-label">صور الخدمة</label>
                            <input type="file" class="form-control @error('images.*') is-invalid @enderror" 
                                   id="images" name="images[]" multiple accept="image/*">
                            <small class="text-muted">يمكنك اختيار عدة صور (الحد الأقصى 5 ميجابايت لكل صورة)</small>
                            @error('images.*')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                            <div id="imagePreview" class="mt-3 row g-2"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Sidebar -->
            <div class="col-lg-4">
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-cog me-2"></i>الإعدادات
                        </h5>
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" 
                                   {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                <strong>نشط</strong>
                                <small class="d-block text-muted">الخدمة ظاهرة للجمهور</small>
                            </label>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="is_verified" name="is_verified" 
                                   {{ old('is_verified', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_verified">
                                <strong>موثق</strong>
                                <small class="d-block text-muted">علامة التوثيق الأزرق</small>
                            </label>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" 
                                   {{ old('is_featured') ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_featured">
                                <strong>مميز</strong>
                                <small class="d-block text-muted">يظهر في القائمة المميزة</small>
                            </label>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm bg-light">
                    <div class="card-body">
                        <h6 class="card-title">
                            <i class="fas fa-info-circle me-2"></i>معلومات مهمة
                        </h6>
                        <ul class="small mb-0">
                            <li>سيتم إنشاء حساب مستخدم جديد</li>
                            <li>سيتم ربط الخدمة بالمستخدم تلقائياً</li>
                            <li>يمكن للمستخدم إدارة خدمته بعد تسجيل الدخول</li>
                            <li>البريد الإلكتروني يجب أن يكون فريداً</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="card shadow-sm">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="fas fa-save me-2"></i>إنشاء الخدمة والحساب
                    </button>
                    <a href="{{ route('admin.user-services.index') }}" class="btn btn-outline-secondary btn-lg">
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
    
    .card {
        border: none;
        border-radius: 10px;
    }
    
    .card-header {
        border-radius: 10px 10px 0 0 !important;
        border: none;
    }
    
    .form-check-input:checked {
        background-color: #28a745;
        border-color: #28a745;
    }
    
    #imagePreview img {
        border-radius: 8px;
        object-fit: cover;
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Toggle password visibility
    const togglePassword = document.getElementById('togglePassword');
    const passwordInput = document.getElementById('user_password');
    
    if (togglePassword) {
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            this.querySelector('i').classList.toggle('fa-eye');
            this.querySelector('i').classList.toggle('fa-eye-slash');
        });
    }

    // Auto-fill phone number in whatsapp field
    const phoneInput = document.getElementById('phone');
    const whatsappInput = document.getElementById('whatsapp');
    const userPhoneInput = document.getElementById('user_phone');
    
    if (phoneInput && whatsappInput) {
        phoneInput.addEventListener('blur', function() {
            if (!whatsappInput.value) {
                whatsappInput.value = this.value;
            }
        });
    }

    // Auto-fill service phone from user phone
    if (userPhoneInput && phoneInput) {
        userPhoneInput.addEventListener('blur', function() {
            if (!phoneInput.value) {
                phoneInput.value = this.value;
            }
        });
    }

    // Image preview
    const imageInput = document.getElementById('images');
    const imagePreview = document.getElementById('imagePreview');
    
    if (imageInput) {
        imageInput.addEventListener('change', function(e) {
            imagePreview.innerHTML = '';
            const files = Array.from(e.target.files);
            
            files.forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const col = document.createElement('div');
                        col.className = 'col-md-3 col-6';
                        col.innerHTML = `
                            <div class="position-relative">
                                <img src="${e.target.result}" class="img-fluid w-100" style="height: 150px;">
                                <span class="badge bg-primary position-absolute top-0 start-0 m-2">
                                    ${index + 1}
                                </span>
                            </div>
                        `;
                        imagePreview.appendChild(col);
                    };
                    reader.readAsDataURL(file);
                }
            });
        });
    }

    // Form validation
    const form = document.querySelector('form');
    if (form) {
        form.addEventListener('submit', function(e) {
            const priceFrom = parseFloat(document.getElementById('price_from').value) || 0;
            const priceTo = parseFloat(document.getElementById('price_to').value) || 0;
            
            if (priceFrom > 0 && priceTo > 0 && priceFrom > priceTo) {
                e.preventDefault();
                alert('السعر "من" يجب أن يكون أقل من السعر "إلى"');
                document.getElementById('price_from').focus();
                return false;
            }
        });
    }
});
</script>
@endpush
@endsection
