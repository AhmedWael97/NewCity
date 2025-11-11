@extends('layouts.app')

@section('content')
<div class="create-shop-container">
    <div class="container">
        <!-- Header -->
        <div class="page-header">
            <div class="header-content">
                <a href="{{ route('shop-owner.dashboard') }}" class="back-btn">
                    <i class="back-icon">←</i>
                    <span>العودة للوحة التحكم</span>
                </a>
                <h1 class="page-title">
                    <i class="title-icon">🏪</i>
                    إضافة متجر جديد
                </h1>
                <p class="page-subtitle">أضف متجرك وابدأ في جذب العملاء</p>
            </div>
        </div>

        <!-- Form Container -->
        <div class="form-container">
            <form action="{{ route('shop-owner.shops.store') }}" method="POST" enctype="multipart/form-data" class="shop-form">
                @csrf
                
                <!-- Basic Information Section -->
                <div class="form-section">
                    <div class="section-header">
                        <h3 class="section-title">
                            <i class="section-icon">ℹ️</i>
                            المعلومات الأساسية
                        </h3>
                        <p class="section-subtitle">أدخل المعلومات الأساسية لمتجرك</p>
                    </div>

                    <div class="form-grid">
                        <!-- Shop Name -->
                        <div class="form-group">
                            <label for="name" class="form-label">
                                <span class="label-text">اسم المتجر</span>
                                <span class="required">*</span>
                            </label>
                            <input type="text" 
                                   id="name" 
                                   name="name" 
                                   value="{{ old('name') }}"
                                   class="form-input @error('name') error @enderror"
                                   placeholder="مثال: مخبز الأصالة"
                                   required>
                            @error('name')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Category -->
                        <div class="form-group">
                            <label for="category_id" class="form-label">
                                <span class="label-text">فئة المتجر</span>
                                <span class="required">*</span>
                            </label>
                            <select id="category_id" 
                                    name="category_id" 
                                    class="form-select @error('category_id') error @enderror"
                                    required>
                                <option value="">اختر فئة المتجر</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- City -->
                        <div class="form-group">
                            <label for="city_id" class="form-label">
                                <span class="label-text">المدينة</span>
                                <span class="required">*</span>
                            </label>
                            <select id="city_id" 
                                    name="city_id" 
                                    class="form-select @error('city_id') error @enderror"
                                    required>
                                <option value="">اختر المدينة</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>
                                        {{ $city->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('city_id')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Phone -->
                        <div class="form-group">
                            <label for="phone" class="form-label">
                                <span class="label-text">رقم الهاتف</span>
                                <span class="required">*</span>
                            </label>
                            <input type="tel" 
                                   id="phone" 
                                   name="phone" 
                                   value="{{ old('phone') }}"
                                   class="form-input @error('phone') error @enderror"
                                   placeholder="مثال: 0501234567"
                                   required>
                            @error('phone')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Description -->
                    <div class="form-group full-width">
                        <label for="description" class="form-label">
                            <span class="label-text">وصف المتجر</span>
                        </label>
                        <textarea id="description" 
                                  name="description" 
                                  rows="4"
                                  class="form-textarea @error('description') error @enderror"
                                  placeholder="اكتب وصفاً مختصراً عن متجرك وما يميزه...">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Location Section -->
                <div class="form-section">
                    <div class="section-header">
                        <h3 class="section-title">
                            <i class="section-icon">📍</i>
                            معلومات الموقع
                        </h3>
                        <p class="section-subtitle">حدد موقع متجرك لسهولة الوصول إليه</p>
                    </div>

                    <div class="form-grid">
                        <!-- Address -->
                        <div class="form-group full-width">
                            <label for="address" class="form-label">
                                <span class="label-text">العنوان التفصيلي</span>
                                <span class="required">*</span>
                            </label>
                            <input type="text" 
                                   id="address" 
                                   name="address" 
                                   value="{{ old('address') }}"
                                   class="form-input @error('address') error @enderror"
                                   placeholder="مثال: طريق الملك فهد، حي الورود، مقابل مجمع العثيم"
                                   required>
                            @error('address')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <!-- Location Coordinates -->
                        <div class="form-group">
                            <label for="latitude" class="form-label">
                                <span class="label-text">خط العرض (Latitude)</span>
                            </label>
                            <input type="number" 
                                   id="latitude" 
                                   name="latitude" 
                                   value="{{ old('latitude') }}"
                                   step="any"
                                   class="form-input @error('latitude') error @enderror"
                                   placeholder="مثال: 24.7136">
                            @error('latitude')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="longitude" class="form-label">
                                <span class="label-text">خط الطول (Longitude)</span>
                            </label>
                            <input type="number" 
                                   id="longitude" 
                                   name="longitude" 
                                   value="{{ old('longitude') }}"
                                   step="any"
                                   class="form-input @error('longitude') error @enderror"
                                   placeholder="مثال: 46.6753">
                            @error('longitude')
                                <div class="error-message">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Map Helper -->
                    <div class="map-helper">
                        <p class="helper-text">
                            <i class="helper-icon">💡</i>
                            يمكنك الحصول على إحداثيات موقعك من خرائط جوجل أو أي تطبيق خرائط آخر
                        </p>
                    </div>
                </div>

                <!-- Images Section -->
                <div class="form-section">
                    <div class="section-header">
                        <h3 class="section-title">
                            <i class="section-icon">📸</i>
                            صور المتجر
                        </h3>
                        <p class="section-subtitle">أضف صوراً جذابة لمتجرك (اختياري)</p>
                    </div>

                    <div class="form-group full-width">
                        <label for="images" class="form-label">
                            <span class="label-text">صور المتجر</span>
                        </label>
                        <div class="file-upload-container">
                            <input type="file" 
                                   id="images" 
                                   name="images[]" 
                                   multiple
                                   accept="image/*"
                                   class="file-input @error('images') error @enderror">
                            <label for="images" class="file-upload-label">
                                <div class="upload-content">
                                    <i class="upload-icon">📷</i>
                                    <p class="upload-text">اضغط لاختيار الصور أو اسحبها هنا</p>
                                    <p class="upload-hint">يمكنك اختيار أكثر من صورة (JPG, PNG, GIF)</p>
                                </div>
                            </label>
                        </div>
                        @error('images')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                        
                        <!-- Image Preview -->
                        <div id="imagePreview" class="image-preview-container"></div>
                    </div>
                </div>

                <!-- Working Hours Section -->
                <div class="form-section">
                    <div class="section-header">
                        <h3 class="section-title">
                            <i class="section-icon">🕒</i>
                            أوقات العمل
                        </h3>
                        <p class="section-subtitle">حدد أوقات عمل متجرك (اختياري)</p>
                    </div>

                    <div class="form-group full-width">
                        <label for="working_hours" class="form-label">
                            <span class="label-text">أوقات العمل</span>
                        </label>
                        <textarea id="working_hours" 
                                  name="working_hours" 
                                  rows="3"
                                  class="form-textarea @error('working_hours') error @enderror"
                                  placeholder="مثال: من السبت إلى الخميس: 8:00 ص - 11:00 م&#10;الجمعة: 2:00 م - 11:00 م">{{ old('working_hours') }}</textarea>
                        @error('working_hours')
                            <div class="error-message">{{ $message }}</div>
                        @enderror
                    </div>
                </div>

                <!-- Submit Section -->
                <div class="form-section">
                    <div class="submit-section">
                        <div class="submit-info">
                            <h4 class="submit-title">جاهز لإرسال الطلب؟</h4>
                            <p class="submit-description">
                                سيتم مراجعة طلبك من قبل الإدارة وستصلك رسالة بالنتيجة خلال 24-48 ساعة
                            </p>
                        </div>
                        <div class="submit-actions">
                            <button type="button" class="btn btn-outline" onclick="history.back()">
                                <i class="btn-icon">❌</i>
                                <span>إلغاء</span>
                            </button>
                            <button type="submit" class="btn btn-primary">
                                <i class="btn-icon">📨</i>
                                <span>إرسال طلب الموافقة</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageInput = document.getElementById('images');
    const imagePreview = document.getElementById('imagePreview');
    
    imageInput.addEventListener('change', function(e) {
        imagePreview.innerHTML = '';
        
        if (e.target.files.length > 0) {
            Array.from(e.target.files).forEach((file, index) => {
                if (file.type.startsWith('image/')) {
                    const reader = new FileReader();
                    reader.onload = function(e) {
                        const previewItem = document.createElement('div');
                        previewItem.className = 'preview-item';
                        previewItem.innerHTML = `
                            <img src="${e.target.result}" alt="Preview ${index + 1}">
                            <div class="preview-overlay">
                                <span class="preview-name">${file.name}</span>
                            </div>
                        `;
                        imagePreview.appendChild(previewItem);
                    };
                    reader.readAsDataURL(file);
                }
            });
        }
    });
});
</script>
@endpush

@push('styles')
<style>
.create-shop-container {
    min-height: 100vh;
    background: linear-gradient(135deg, #f5f7fa 0%, #c3cfe2 100%);
    padding: 20px 0;
}

.page-header {
    margin-bottom: 30px;
}

.header-content {
    background: white;
    padding: 30px;
    border-radius: 16px;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.back-btn {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    color: #666;
    text-decoration: none;
    font-weight: 500;
    margin-bottom: 16px;
    transition: color 0.3s ease;
}

.back-btn:hover {
    color: #333;
}

.back-icon {
    font-size: 1.2rem;
}

.page-title {
    display: flex;
    align-items: center;
    gap: 12px;
    font-size: 2rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 8px;
}

.title-icon {
    font-size: 2.2rem;
}

.page-subtitle {
    color: #666;
    font-size: 1.1rem;
    margin: 0;
}

.form-container {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
}

.shop-form {
    padding: 40px;
}

.form-section {
    margin-bottom: 40px;
    padding-bottom: 30px;
    border-bottom: 1px solid #e9ecef;
}

.form-section:last-child {
    border-bottom: none;
    margin-bottom: 0;
}

.section-header {
    margin-bottom: 24px;
}

.section-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1.4rem;
    font-weight: bold;
    color: #333;
    margin-bottom: 6px;
}

.section-icon {
    font-size: 1.5rem;
}

.section-subtitle {
    color: #666;
    font-size: 1rem;
    margin: 0;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
    gap: 20px;
}

.form-group {
    display: flex;
    flex-direction: column;
}

.form-group.full-width {
    grid-column: 1 / -1;
}

.form-label {
    display: flex;
    align-items: center;
    gap: 4px;
    margin-bottom: 8px;
    font-weight: 600;
    color: #333;
}

.required {
    color: #e74c3c;
    font-weight: bold;
}

.form-input,
.form-select,
.form-textarea {
    padding: 14px 16px;
    border: 2px solid #e9ecef;
    border-radius: 12px;
    font-size: 1rem;
    transition: all 0.3s ease;
    background: #fff;
}

.form-input:focus,
.form-select:focus,
.form-textarea:focus {
    outline: none;
    border-color: #3498db;
    box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
}

.form-input.error,
.form-select.error,
.form-textarea.error {
    border-color: #e74c3c;
}

.error-message {
    color: #e74c3c;
    font-size: 0.875rem;
    margin-top: 6px;
    font-weight: 500;
}

.file-upload-container {
    position: relative;
}

.file-input {
    position: absolute;
    opacity: 0;
    width: 100%;
    height: 100%;
    cursor: pointer;
}

.file-upload-label {
    display: block;
    border: 2px dashed #cbd5e0;
    border-radius: 12px;
    padding: 40px 20px;
    text-align: center;
    cursor: pointer;
    transition: all 0.3s ease;
    background: #f8f9fa;
}

.file-upload-label:hover {
    border-color: #3498db;
    background: #e3f2fd;
}

.upload-content {
    display: flex;
    flex-direction: column;
    align-items: center;
}

.upload-icon {
    font-size: 3rem;
    margin-bottom: 12px;
    opacity: 0.6;
}

.upload-text {
    font-size: 1.1rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 6px;
}

.upload-hint {
    font-size: 0.9rem;
    color: #666;
    margin: 0;
}

.image-preview-container {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
    gap: 16px;
    margin-top: 16px;
}

.preview-item {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    background: #f8f9fa;
    aspect-ratio: 1;
}

.preview-item img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.preview-overlay {
    position: absolute;
    bottom: 0;
    left: 0;
    right: 0;
    background: linear-gradient(transparent, rgba(0,0,0,0.8));
    padding: 12px 8px 8px;
}

.preview-name {
    color: white;
    font-size: 0.8rem;
    font-weight: 500;
    display: block;
    text-overflow: ellipsis;
    overflow: hidden;
    white-space: nowrap;
}

.map-helper {
    background: #e3f2fd;
    border: 1px solid #bbdefb;
    border-radius: 12px;
    padding: 16px;
    margin-top: 16px;
}

.helper-text {
    display: flex;
    align-items: center;
    gap: 8px;
    color: #1976d2;
    font-size: 0.95rem;
    margin: 0;
}

.helper-icon {
    font-size: 1.1rem;
}

.submit-section {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 30px;
    border-radius: 16px;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.submit-info {
    flex: 1;
}

.submit-title {
    font-size: 1.3rem;
    font-weight: bold;
    margin-bottom: 8px;
}

.submit-description {
    opacity: 0.9;
    margin: 0;
    line-height: 1.5;
}

.submit-actions {
    display: flex;
    gap: 16px;
}

.btn {
    padding: 14px 24px;
    border-radius: 12px;
    text-decoration: none;
    font-weight: 600;
    font-size: 1rem;
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    transition: all 0.3s ease;
    border: none;
    cursor: pointer;
    min-width: 140px;
}

.btn-primary {
    background: linear-gradient(135deg, #27ae60, #2ecc71);
    color: white;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #219a52, #27ae60);
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(39, 174, 96, 0.3);
}

.btn-outline {
    background: rgba(255, 255, 255, 0.1);
    color: white;
    border: 2px solid rgba(255, 255, 255, 0.3);
}

.btn-outline:hover {
    background: rgba(255, 255, 255, 0.2);
    border-color: rgba(255, 255, 255, 0.5);
}

.btn-icon {
    font-size: 1.1rem;
}

@media (max-width: 768px) {
    .create-shop-container {
        padding: 10px 0;
    }
    
    .shop-form {
        padding: 20px;
    }
    
    .form-grid {
        grid-template-columns: 1fr;
    }
    
    .submit-section {
        flex-direction: column;
        gap: 20px;
        text-align: center;
    }
    
    .submit-actions {
        flex-direction: column;
        width: 100%;
    }
    
    .btn {
        width: 100%;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
    
    .section-title {
        font-size: 1.2rem;
    }
}

@media (max-width: 480px) {
    .header-content,
    .shop-form {
        padding: 16px;
    }
    
    .form-section {
        margin-bottom: 30px;
        padding-bottom: 20px;
    }
    
    .submit-section {
        padding: 20px;
    }
    
    .image-preview-container {
        grid-template-columns: repeat(auto-fill, minmax(120px, 1fr));
    }
}
</style>
@endpush
@endsection