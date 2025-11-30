@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-plus-circle"></i> إضافة إعلان جديد</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>ملاحظة:</strong> سيتم مراجعة إعلانك من قبل الإدارة قبل نشره. سيتم إشعارك عند الموافقة عليه.
                    </div>

                    @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <form action="{{ route('marketplace.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <!-- Title -->
                        <div class="mb-3">
                            <label class="form-label required">عنوان الإعلان</label>
                            <input type="text" name="title" class="form-control @error('title') is-invalid @enderror" 
                                   value="{{ old('title') }}" placeholder="مثال: iPhone 14 Pro Max" required>
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Description -->
                        <div class="mb-3">
                            <label class="form-label required">الوصف</label>
                            <textarea name="description" rows="5" class="form-control @error('description') is-invalid @enderror" 
                                      placeholder="اكتب وصفاً تفصيلياً للمنتج..." required>{{ old('description') }}</textarea>
                            <small class="text-muted">الحد الأدنى 20 حرفاً</small>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- Price -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">السعر (جنيه)</label>
                                <input type="number" name="price" class="form-control @error('price') is-invalid @enderror" 
                                       value="{{ old('price') }}" placeholder="0.00" step="0.01" min="0" required>
                                @error('price')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Negotiable -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">السعر قابل للتفاوض؟</label>
                                <div class="form-check form-switch mt-2">
                                    <input class="form-check-input" type="checkbox" name="is_negotiable" id="is_negotiable" 
                                           value="1" {{ old('is_negotiable', true) ? 'checked' : '' }}>
                                    <label class="form-check-label" for="is_negotiable">نعم، قابل للتفاوض</label>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <!-- City -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">المدينة</label>
                                <select name="city_id" class="form-select @error('city_id') is-invalid @enderror" required>
                                    <option value="">اختر المدينة</option>
                                    @foreach($cities as $city)
                                        <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>
                                            {{ $city->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('city_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Category -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label required">الفئة</label>
                                <select name="category_id" class="form-select @error('category_id') is-invalid @enderror" required>
                                    <option value="">اختر الفئة</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                            {{ $category->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('category_id')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Condition -->
                        <div class="mb-3">
                            <label class="form-label required">حالة المنتج</label>
                            <div class="row g-2">
                                <div class="col-6 col-md-3">
                                    <input type="radio" class="btn-check" name="condition" id="condition-new" value="new" 
                                           {{ old('condition') == 'new' ? 'checked' : '' }} required>
                                    <label class="btn btn-outline-success w-100" for="condition-new">
                                        <i class="fas fa-certificate"></i><br>جديد
                                    </label>
                                </div>
                                <div class="col-6 col-md-3">
                                    <input type="radio" class="btn-check" name="condition" id="condition-like_new" value="like_new"
                                           {{ old('condition') == 'like_new' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-info w-100" for="condition-like_new">
                                        <i class="fas fa-star"></i><br>مثل الجديد
                                    </label>
                                </div>
                                <div class="col-6 col-md-3">
                                    <input type="radio" class="btn-check" name="condition" id="condition-good" value="good"
                                           {{ old('condition') == 'good' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-warning w-100" for="condition-good">
                                        <i class="fas fa-thumbs-up"></i><br>جيد
                                    </label>
                                </div>
                                <div class="col-6 col-md-3">
                                    <input type="radio" class="btn-check" name="condition" id="condition-fair" value="fair"
                                           {{ old('condition') == 'fair' ? 'checked' : '' }}>
                                    <label class="btn btn-outline-secondary w-100" for="condition-fair">
                                        <i class="fas fa-check"></i><br>مقبول
                                    </label>
                                </div>
                            </div>
                            @error('condition')
                                <div class="invalid-feedback d-block">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <!-- Contact Phone -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">رقم الهاتف للتواصل</label>
                                <input type="text" name="contact_phone" class="form-control @error('contact_phone') is-invalid @enderror" 
                                       value="{{ old('contact_phone', auth()->user()->phone) }}" placeholder="+201234567890">
                                @error('contact_phone')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <!-- Contact WhatsApp -->
                            <div class="col-md-6 mb-3">
                                <label class="form-label">رقم الواتساب</label>
                                <input type="text" name="contact_whatsapp" class="form-control @error('contact_whatsapp') is-invalid @enderror" 
                                       value="{{ old('contact_whatsapp') }}" placeholder="+201234567890">
                                @error('contact_whatsapp')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <!-- Images -->
                        <div class="mb-3">
                            <label class="form-label required">صور المنتج</label>
                            <input type="file" name="images[]" class="form-control @error('images') is-invalid @enderror" 
                                   multiple accept="image/*" id="imageInput" required>
                            <small class="text-muted">يمكنك رفع من 1 إلى 5 صور (الحد الأقصى 5 ميجا لكل صورة)</small>
                            @error('images')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            
                            <!-- Image Preview -->
                            <div id="imagePreview" class="mt-3 row g-2"></div>
                        </div>

                        <div class="alert alert-warning">
                            <i class="fas fa-exclamation-triangle"></i>
                            <strong>ملحوظة هامة:</strong>
                            <ul class="mb-0 mt-2">
                                <li>كل إعلان يحصل على <strong>50 مشاهدة مجانية</strong></li>
                                <li>بعد انتهاء المشاهدات، سيتم إخفاء الإعلان تلقائياً</li>
                                <li>يمكنك رعاية الإعلان للحصول على مشاهدات إضافية وظهور أفضل</li>
                            </ul>
                        </div>

                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-check"></i> نشر الإعلان
                            </button>
                            <a href="{{ route('marketplace.index') }}" class="btn btn-outline-secondary">
                                <i class="fas fa-times"></i> إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.required::after {
    content: " *";
    color: red;
}

.image-preview-item {
    position: relative;
}

.image-preview-item img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 8px;
}

.image-preview-item .remove-image {
    position: absolute;
    top: 5px;
    right: 5px;
    background: rgba(255, 0, 0, 0.8);
    color: white;
    border: none;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    cursor: pointer;
}
</style>

<script>
document.getElementById('imageInput').addEventListener('change', function(e) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    
    const files = Array.from(e.target.files);
    
    if (files.length > 5) {
        alert('يمكنك رفع 5 صور كحد أقصى');
        e.target.value = '';
        return;
    }
    
    files.forEach((file, index) => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-6 col-md-4 image-preview-item';
                col.innerHTML = `
                    <img src="${e.target.result}" alt="Preview ${index + 1}">
                    <span class="badge bg-primary position-absolute bottom-0 start-0 m-2">صورة ${index + 1}</span>
                `;
                preview.appendChild(col);
            };
            reader.readAsDataURL(file);
        }
    });
});
</script>
@endsection
