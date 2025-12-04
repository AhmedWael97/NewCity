@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0"><i class="fas fa-edit"></i> تعديل الإعلان</h4>
        </div>
        <div class="card-body">
            <form action="{{ route('marketplace.update', $item->slug) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- Title -->
                <div class="mb-3">
                    <label for="title" class="form-label">عنوان الإعلان <span class="text-danger">*</span></label>
                    <input type="text" class="form-control @error('title') is-invalid @enderror" 
                           id="title" name="title" value="{{ old('title', $item->title) }}" required>
                    @error('title')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Description -->
                <div class="mb-3">
                    <label for="description" class="form-label">الوصف <span class="text-danger">*</span></label>
                    <textarea class="form-control @error('description') is-invalid @enderror" 
                              id="description" name="description" rows="5" required 
                              minlength="20">{{ old('description', $item->description) }}</textarea>
                    <small class="text-muted">يجب أن يحتوي الوصف على 20 حرفاً على الأقل</small>
                    @error('description')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Price and Negotiable -->
                <div class="row mb-3">
                    <div class="col-md-8">
                        <label for="price" class="form-label">السعر (بالجنيه المصري) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('price') is-invalid @enderror" 
                               id="price" name="price" value="{{ old('price', $item->price) }}" 
                               min="0" step="0.01" required>
                        @error('price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">&nbsp;</label>
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="is_negotiable" 
                                   name="is_negotiable" value="1" 
                                   {{ old('is_negotiable', $item->is_negotiable) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_negotiable">
                                السعر قابل للتفاوض
                            </label>
                        </div>
                    </div>
                </div>

                <!-- City and Category -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="city_id" class="form-label">المدينة <span class="text-danger">*</span></label>
                        <select class="form-select @error('city_id') is-invalid @enderror" 
                                id="city_id" name="city_id" required>
                            <option value="">اختر المدينة</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" 
                                        {{ old('city_id', $item->city_id) == $city->id ? 'selected' : '' }}>
                                    {{ $city->name }}
                                </option>
                            @endforeach
                        </select>
                        @error('city_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="category_id" class="form-label">التصنيف <span class="text-danger">*</span></label>
                        <select class="form-select @error('category_id') is-invalid @enderror" 
                                id="category_id" name="category_id" required>
                            <option value="">اختر التصنيف</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" 
                                        {{ old('category_id', $item->category_id) == $category->id ? 'selected' : '' }}>
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
                    <label class="form-label">حالة المنتج <span class="text-danger">*</span></label>
                    <div class="row g-2">
                        <div class="col-6 col-md-3">
                            <input type="radio" class="btn-check" name="condition" id="new" 
                                   value="new" {{ old('condition', $item->condition) == 'new' ? 'checked' : '' }} required>
                            <label class="btn btn-outline-success w-100" for="new">
                                <i class="fas fa-gift"></i><br>جديد
                            </label>
                        </div>
                        <div class="col-6 col-md-3">
                            <input type="radio" class="btn-check" name="condition" id="like_new" 
                                   value="like_new" {{ old('condition', $item->condition) == 'like_new' ? 'checked' : '' }}>
                            <label class="btn btn-outline-info w-100" for="like_new">
                                <i class="fas fa-star"></i><br>شبه جديد
                            </label>
                        </div>
                        <div class="col-6 col-md-3">
                            <input type="radio" class="btn-check" name="condition" id="good" 
                                   value="good" {{ old('condition', $item->condition) == 'good' ? 'checked' : '' }}>
                            <label class="btn btn-outline-primary w-100" for="good">
                                <i class="fas fa-thumbs-up"></i><br>جيد
                            </label>
                        </div>
                        <div class="col-6 col-md-3">
                            <input type="radio" class="btn-check" name="condition" id="fair" 
                                   value="fair" {{ old('condition', $item->condition) == 'fair' ? 'checked' : '' }}>
                            <label class="btn btn-outline-warning w-100" for="fair">
                                <i class="fas fa-check"></i><br>مقبول
                            </label>
                        </div>
                    </div>
                    @error('condition')
                        <div class="text-danger small mt-1">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Contact Information -->
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="contact_phone" class="form-label">رقم الهاتف <span class="text-danger">*</span></label>
                        <input type="tel" class="form-control @error('contact_phone') is-invalid @enderror" 
                               id="contact_phone" name="contact_phone" 
                               value="{{ old('contact_phone', $item->contact_phone) }}" required>
                        @error('contact_phone')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="col-md-6">
                        <label for="contact_whatsapp" class="form-label">رقم واتساب (اختياري)</label>
                        <input type="tel" class="form-control @error('contact_whatsapp') is-invalid @enderror" 
                               id="contact_whatsapp" name="contact_whatsapp" 
                               value="{{ old('contact_whatsapp', $item->contact_whatsapp) }}">
                        @error('contact_whatsapp')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Current Images -->
                @if($item->images && count($item->images) > 0)
                <div class="mb-3">
                    <label class="form-label">الصور الحالية</label>
                    <div class="row g-2 mb-2">
                        @foreach($item->images as $index => $image)
                        <div class="col-3 col-md-2">
                            <div style="position: relative;">
                                <img src="{{ $image }}" class="img-thumbnail" style="height: 100px; width: 100%; object-fit: cover;">
                                <div class="form-check position-absolute top-0 end-0 m-1 bg-white rounded">
                                    <input class="form-check-input" type="checkbox" 
                                           name="delete_images[]" value="{{ $index }}" 
                                           id="delete_image_{{ $index }}">
                                    <label class="form-check-label small text-danger" for="delete_image_{{ $index }}">
                                        حذف
                                    </label>
                                </div>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    <small class="text-muted">ضع علامة على الصور التي تريد حذفها</small>
                </div>
                @endif

                <!-- New Images -->
                <div class="mb-3">
                    <label for="images" class="form-label">
                        {{ $item->images && count($item->images) > 0 ? 'إضافة صور جديدة (اختياري)' : 'الصور (1-5 صور)' }}
                    </label>
                    <input type="file" class="form-control @error('images') is-invalid @enderror @error('images.*') is-invalid @enderror" 
                           id="images" name="images[]" accept="image/*" multiple 
                           onchange="previewImages(event)">
                    <small class="text-muted">
                        يمكنك رفع من 1 إلى 5 صور. الحد الأقصى لحجم كل صورة: 5 ميجابايت
                    </small>
                    @error('images')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    @error('images.*')
                        <div class="invalid-feedback d-block">{{ $message }}</div>
                    @enderror
                    
                    <!-- Image Preview -->
                    <div id="imagePreview" class="row g-2 mt-2"></div>
                </div>

                <!-- Submit Buttons -->
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> حفظ التعديلات
                    </button>
                    <a href="{{ route('marketplace.my-items') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function previewImages(event) {
    const preview = document.getElementById('imagePreview');
    preview.innerHTML = '';
    
    const files = event.target.files;
    if (files.length > 5) {
        alert('يمكنك رفع 5 صور كحد أقصى');
        event.target.value = '';
        return;
    }
    
    for (let i = 0; i < files.length; i++) {
        const file = files[i];
        
        // Check file size (5MB)
        if (file.size > 5 * 1024 * 1024) {
            alert(`الصورة ${file.name} أكبر من 5 ميجابايت`);
            continue;
        }
        
        const reader = new FileReader();
        reader.onload = function(e) {
            const col = document.createElement('div');
            col.className = 'col-3 col-md-2';
            col.innerHTML = `
                <img src="${e.target.result}" class="img-thumbnail" 
                     style="height: 100px; width: 100%; object-fit: cover;">
            `;
            preview.appendChild(col);
        };
        reader.readAsDataURL(file);
    }
}
</script>
@endsection
