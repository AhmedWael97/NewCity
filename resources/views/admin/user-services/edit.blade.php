@extends('layouts.admin')

@section('title', 'تعديل الخدمة')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2">تعديل الخدمة: {{ $service->title }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.user-services.index') }}">الخدمات</a></li>
                    <li class="breadcrumb-item active">تعديل</li>
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

    <form action="{{ route('admin.user-services.update', $service) }}" method="POST" enctype="multipart/form-data">
        @csrf
        @method('PUT')

        <div class="row">
            <!-- User Information (Display Only) -->
            <div class="col-lg-12 mb-4">
                <div class="card shadow-sm">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0">
                            <i class="fas fa-user me-2"></i>معلومات صاحب الخدمة
                        </h5>
                    </div>
                    <div class="card-body bg-light">
                        <div class="row">
                            <div class="col-md-4">
                                <strong>الاسم:</strong> {{ $service->user->name }}
                            </div>
                            <div class="col-md-4">
                                <strong>البريد الإلكتروني:</strong> {{ $service->user->email }}
                            </div>
                            <div class="col-md-4">
                                <strong>رقم الجوال:</strong> {{ $service->user->phone }}
                            </div>
                        </div>
                        <div class="alert alert-info mb-0 mt-3">
                            <i class="fas fa-info-circle me-2"></i>
                            <strong>ملاحظة:</strong> لا يمكن تعديل معلومات المستخدم من هنا. يمكنك تعديلها من قسم إدارة المستخدمين.
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
                                   id="title" name="title" value="{{ old('title', $service->title) }}" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label required">وصف الخدمة</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="5" required>{{ old('description', $service->description) }}</textarea>
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
                                        <option value="{{ $category->id }}" 
                                            {{ old('service_category_id', $service->service_category_id) == $category->id ? 'selected' : '' }}>
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
                                        <option value="{{ $city->id }}" 
                                            {{ old('city_id', $service->city_id) == $city->id ? 'selected' : '' }}>
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
                                    <option value="fixed" {{ old('pricing_type', $service->pricing_type) == 'fixed' ? 'selected' : '' }}>سعر ثابت</option>
                                    <option value="hourly" {{ old('pricing_type', $service->pricing_type) == 'hourly' ? 'selected' : '' }}>بالساعة</option>
                                    <option value="per_km" {{ old('pricing_type', $service->pricing_type) == 'per_km' ? 'selected' : '' }}>بالكيلومتر</option>
                                    <option value="negotiable" {{ old('pricing_type', $service->pricing_type) == 'negotiable' ? 'selected' : '' }}>قابل للتفاوض</option>
                                </select>
                                @error('pricing_type')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="price_from" class="form-label">السعر من (ريال)</label>
                                <input type="number" class="form-control @error('price_from') is-invalid @enderror" 
                                       id="price_from" name="price_from" value="{{ old('price_from', $service->price_from) }}" 
                                       step="0.01" min="0">
                                @error('price_from')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="price_to" class="form-label">السعر إلى (ريال)</label>
                                <input type="number" class="form-control @error('price_to') is-invalid @enderror" 
                                       id="price_to" name="price_to" value="{{ old('price_to', $service->price_to) }}" 
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
                                       id="phone" name="phone" value="{{ old('phone', $service->phone) }}" 
                                       placeholder="05xxxxxxxx" required>
                                @error('phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="whatsapp" class="form-label">واتساب</label>
                                <input type="text" class="form-control @error('whatsapp') is-invalid @enderror" 
                                       id="whatsapp" name="whatsapp" value="{{ old('whatsapp', $service->whatsapp) }}" 
                                       placeholder="05xxxxxxxx">
                                @error('whatsapp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">العنوان</label>
                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                      id="address" name="address" rows="2">{{ old('address', $service->address) }}</textarea>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Current Images -->
                        @if($service->images && count($service->images) > 0)
                            <div class="mb-3">
                                <label class="form-label">الصور الحالية</label>
                                <div class="row g-2" id="currentImages">
                                    @foreach($service->images as $index => $image)
                                        <div class="col-md-3 col-6" id="image-{{ $index }}">
                                            <div class="position-relative">
                                                <img src="{{ asset('storage/' . $image) }}" class="img-fluid w-100 rounded" style="height: 150px; object-fit: cover;">
                                                <button type="button" class="btn btn-sm btn-danger position-absolute top-0 end-0 m-2" 
                                                        onclick="removeImage({{ $index }})">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                                <input type="hidden" name="existing_images[]" value="{{ $image }}" id="existing-image-{{ $index }}">
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <div class="mb-3">
                            <label for="images" class="form-label">إضافة صور جديدة</label>
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
                                   {{ old('is_active', $service->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">
                                <strong>نشط</strong>
                                <small class="d-block text-muted">الخدمة ظاهرة للجمهور</small>
                            </label>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="is_verified" name="is_verified" 
                                   {{ old('is_verified', $service->is_verified) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_verified">
                                <strong>موثق</strong>
                                <small class="d-block text-muted">علامة التوثيق الأزرق</small>
                            </label>
                        </div>

                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" 
                                   {{ old('is_featured', $service->is_featured) ? 'checked' : '' }}>
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
                            <i class="fas fa-info-circle me-2"></i>معلومات الخدمة
                        </h6>
                        <ul class="small mb-0">
                            <li><strong>تاريخ الإنشاء:</strong> {{ $service->created_at->format('Y-m-d') }}</li>
                            <li><strong>آخر تحديث:</strong> {{ $service->updated_at->diffForHumans() }}</li>
                            <li><strong>عدد المشاهدات:</strong> {{ $service->views ?? 0 }}</li>
                            <li><strong>الحالة:</strong> 
                                @if($service->is_active)
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-secondary">غير نشط</span>
                                @endif
                            </li>
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
                        <i class="fas fa-save me-2"></i>حفظ التعديلات
                    </button>
                    <div>
                        <a href="{{ route('admin.user-services.show', $service) }}" class="btn btn-outline-info btn-lg me-2">
                            <i class="fas fa-eye me-2"></i>عرض
                        </a>
                        <a href="{{ route('admin.user-services.index') }}" class="btn btn-outline-secondary btn-lg">
                            <i class="fas fa-times me-2"></i>إلغاء
                        </a>
                    </div>
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
    // Auto-fill phone number in whatsapp field
    const phoneInput = document.getElementById('phone');
    const whatsappInput = document.getElementById('whatsapp');
    
    if (phoneInput && whatsappInput) {
        phoneInput.addEventListener('blur', function() {
            if (!whatsappInput.value) {
                whatsappInput.value = this.value;
            }
        });
    }

    // Image preview for new images
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
                                <img src="${e.target.result}" class="img-fluid w-100" style="height: 150px; object-fit: cover; border-radius: 8px;">
                                <span class="badge bg-primary position-absolute top-0 start-0 m-2">
                                    جديد ${index + 1}
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

// Remove existing image
function removeImage(index) {
    if (confirm('هل أنت متأكد من حذف هذه الصورة؟')) {
        document.getElementById('image-' + index).style.display = 'none';
        document.getElementById('existing-image-' + index).remove();
    }
}
</script>
@endpush
@endsection
