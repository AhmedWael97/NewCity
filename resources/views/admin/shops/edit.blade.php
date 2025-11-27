@extends('layouts.admin')

@section('title', 'تعديل المتجر - ' . $shop->name)

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-edit"></i> تعديل المتجر: {{ $shop->name }}
        </h1>
        <div>
            <a href="{{ route('admin.shops.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-right"></i> العودة للقائمة
            </a>
            <a href="{{ route('admin.shops.show', $shop) }}" class="btn btn-info btn-sm">
                <i class="fas fa-eye"></i> عرض التفاصيل
            </a>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">معلومات المتجر</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.shops.update', $shop) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">اسم المتجر <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $shop->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="slug">الرابط (Slug)</label>
                                    <input type="text" class="form-control @error('slug') is-invalid @enderror" 
                                           id="slug" name="slug" value="{{ old('slug', $shop->slug) }}">
                                    @error('slug')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="user_id">المالك <span class="text-danger">*</span></label>
                                    <select class="form-control @error('user_id') is-invalid @enderror" id="user_id" name="user_id" required>
                                        <option value="">اختر المالك</option>
                                        @foreach($users ?? [] as $user)
                                            <option value="{{ $user->id }}" {{ old('user_id', $shop->user_id) == $user->id ? 'selected' : '' }}>
                                                {{ $user->name }} ({{ $user->email }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('user_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="city_id">المدينة <span class="text-danger">*</span></label>
                                    <select class="form-control @error('city_id') is-invalid @enderror" id="city_id" name="city_id" required>
                                        <option value="">اختر المدينة</option>
                                        @foreach($cities as $city)
                                            <option value="{{ $city->id }}" {{ old('city_id', $shop->city_id) == $city->id ? 'selected' : '' }}>
                                                {{ $city->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('city_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="category_id">التصنيف <span class="text-danger">*</span></label>
                                    <select class="form-control @error('category_id') is-invalid @enderror" id="category_id" name="category_id" required>
                                        <option value="">اختر التصنيف</option>
                                        @foreach($categories as $category)
                                            <option value="{{ $category->id }}" {{ old('category_id', $shop->category_id) == $category->id ? 'selected' : '' }}>
                                                {{ $category->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('category_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="description">الوصف</label>
                            <textarea class="form-control @error('description') is-invalid @enderror" 
                                      id="description" name="description" rows="4">{{ old('description', $shop->description) }}</textarea>
                            @error('description')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <x-google-maps-picker 
                            addressId="address"
                            latitudeId="latitude"
                            longitudeId="longitude"
                            :addressValue="old('address', $shop->address ?? '')"
                            :latitudeValue="old('latitude', $shop->latitude ?? '')"
                            :longitudeValue="old('longitude', $shop->longitude ?? '')"
                            height="450px"
                            :defaultLat="$shop->latitude ?? 24.774265"
                            :defaultLng="$shop->longitude ?? 46.738586"
                        />

                        <div class="form-group">
                            <label for="address">العنوان</label>
                            <input type="text" class="form-control @error('address') is-invalid @enderror" 
                                   id="address" name="address" value="{{ old('address', $shop->address) }}">
                            <small class="form-text text-muted">سيتم ملؤه تلقائياً عند تحديد الموقع على الخريطة</small>
                            @error('address')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="latitude">خط العرض</label>
                                    <input type="number" step="any" class="form-control @error('latitude') is-invalid @enderror" 
                                           id="latitude" name="latitude" value="{{ old('latitude', $shop->latitude) }}" readonly>
                                    <small class="form-text text-muted">يتم ملؤه تلقائياً من الخريطة</small>
                                    @error('latitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="longitude">خط الطول</label>
                                    <input type="number" step="any" class="form-control @error('longitude') is-invalid @enderror" 
                                           id="longitude" name="longitude" value="{{ old('longitude', $shop->longitude) }}" readonly>
                                    <small class="form-text text-muted">يتم ملؤه تلقائياً من الخريطة</small>
                                    @error('longitude')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="phone">رقم الهاتف</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone', $shop->phone) }}">
                                    @error('phone')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="email">البريد الإلكتروني</label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $shop->email) }}">
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="website">الموقع الإلكتروني</label>
                                    <input type="url" class="form-control @error('website') is-invalid @enderror" 
                                           id="website" name="website" value="{{ old('website', $shop->website) }}">
                                    @error('website')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <!-- Current Images -->
                        @if($shop->images && count($shop->images) > 0)
                            <div class="form-group">
                                <label class="font-weight-bold"><i class="fas fa-images"></i> الصور الحالية</label>
                                <div class="row" id="current-images-grid">
                                    @foreach($shop->images as $index => $image)
                                        <div class="col-md-3 col-sm-4 col-6 mb-3" data-image-index="{{ $index }}">
                                            <div class="image-preview-item">
                                                <img src="{{ asset('storage/' . $image) }}" alt="Shop Image">
                                                <button type="button" class="remove-image" onclick="toggleDeleteImage({{ $index }}, this)">
                                                    <i class="fas fa-times"></i>
                                                </button>
                                                <input type="checkbox" name="delete_images[]" value="{{ $index }}" 
                                                       id="delete_{{ $index }}" style="display: none;">
                                                <div class="delete-overlay" style="display: none;">
                                                    <span class="badge badge-danger">سيتم الحذف</span>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif

                        <!-- Add New Images with Drag & Drop -->
                        <div class="form-group">
                            <label class="font-weight-bold">
                                <i class="fas fa-plus-circle"></i> إضافة صور جديدة
                            </label>
                            <div id="image-upload-container" class="border rounded p-4 bg-light text-center" 
                                 style="min-height: 200px; cursor: pointer;">
                                <input type="file" 
                                       id="images-input" 
                                       name="images[]" 
                                       accept="image/*" 
                                       multiple 
                                       style="display: none;">
                                
                                <div id="upload-prompt" class="text-muted">
                                    <i class="fas fa-cloud-upload-alt fa-3x mb-3"></i>
                                    <p class="mb-2"><strong>اسحب وأفلت الصور هنا</strong></p>
                                    <p class="mb-2">أو <span class="text-primary" style="cursor: pointer; text-decoration: underline;">انقر للاختيار</span></p>
                                    <p class="small">أو استخدم Ctrl+V للصق الصور من الحافظة</p>
                                    <small class="text-muted">PNG, JPG, JPEG - حجم أقصى 2MB لكل صورة</small>
                                </div>
                                
                                <div id="image-preview-grid" class="row g-3 mt-2" style="display: none;"></div>
                            </div>
                            <small class="form-text text-muted">الصور الجديدة ستضاف إلى الصور الحالية</small>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <div class="form-group">
                                    <label for="status">الحالة</label>
                                    <select class="form-control @error('status') is-invalid @enderror" id="status" name="status">
                                        <option value="pending" {{ old('status', $shop->status) == 'pending' ? 'selected' : '' }}>في الانتظار</option>
                                        <option value="approved" {{ old('status', $shop->status) == 'approved' ? 'selected' : '' }}>مقبول</option>
                                        <option value="rejected" {{ old('status', $shop->status) == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                                        <option value="suspended" {{ old('status', $shop->status) == 'suspended' ? 'selected' : '' }}>معلق</option>
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="form-group">
                                    <label>الخيارات</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="is_verified" name="is_verified" value="1" {{ old('is_verified', $shop->is_verified) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_verified">محقق</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="is_featured" name="is_featured" value="1" {{ old('is_featured', $shop->is_featured) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_featured">مميز</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', $shop->is_active) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">نشط</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="verification_notes">ملاحظات التحقق</label>
                            <textarea class="form-control @error('verification_notes') is-invalid @enderror" 
                                      id="verification_notes" name="verification_notes" rows="3">{{ old('verification_notes', $shop->verification_notes) }}</textarea>
                            @error('verification_notes')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> حفظ التعديلات
                            </button>
                            <a href="{{ route('admin.shops.show', $shop) }}" class="btn btn-info">
                                <i class="fas fa-eye"></i> عرض التفاصيل
                            </a>
                            <a href="{{ route('admin.shops.index') }}" class="btn btn-secondary">
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
#image-upload-container {
    transition: all 0.3s ease;
}
#image-upload-container:hover {
    border-color: #4e73df !important;
    background-color: #f8f9fc !important;
}
#image-upload-container.drag-over {
    border: 2px dashed #4e73df !important;
    background-color: #e3ebff !important;
}
.image-preview-item {
    position: relative;
    border: 2px solid #dee2e6;
    border-radius: 8px;
    overflow: hidden;
    background: white;
}
.image-preview-item img {
    width: 100%;
    height: 150px;
    object-fit: cover;
}
.image-preview-item .remove-image {
    position: absolute;
    top: 5px;
    right: 5px;
    background: rgba(220, 53, 69, 0.9);
    color: white;
    border: none;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    cursor: pointer;
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 10;
    font-size: 14px;
}
.image-preview-item .remove-image:hover {
    background: rgba(220, 53, 69, 1);
}
.image-preview-item .delete-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(220, 53, 69, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    pointer-events: none;
}
.image-preview-item.marked-for-deletion {
    opacity: 0.5;
    border-color: #dc3545;
}
</style>

<script>
// Auto-generate slug from name
document.getElementById('name').addEventListener('input', function() {
    let name = this.value;
    let slug = name.toLowerCase()
                   .replace(/[^\w\s-]/g, '') // Remove special characters
                   .replace(/[\s_-]+/g, '-') // Replace spaces and underscores with -
                   .replace(/^-+|-+$/g, ''); // Remove leading/trailing dashes
    document.getElementById('slug').value = slug;
});

// Toggle delete for existing images
function toggleDeleteImage(index, button) {
    const container = button.closest('[data-image-index]');
    const checkbox = document.getElementById('delete_' + index);
    const overlay = container.querySelector('.delete-overlay');
    const item = container.querySelector('.image-preview-item');
    
    checkbox.checked = !checkbox.checked;
    
    if (checkbox.checked) {
        overlay.style.display = 'flex';
        item.classList.add('marked-for-deletion');
        button.innerHTML = '<i class="fas fa-undo"></i>';
    } else {
        overlay.style.display = 'none';
        item.classList.remove('marked-for-deletion');
        button.innerHTML = '<i class="fas fa-times"></i>';
    }
}

// Image Upload with Drag & Drop and Paste for new images
(function() {
    const container = document.getElementById('image-upload-container');
    const input = document.getElementById('images-input');
    const prompt = document.getElementById('upload-prompt');
    const previewGrid = document.getElementById('image-preview-grid');
    let selectedFiles = [];

    // Click to open file picker
    container.addEventListener('click', function(e) {
        if (!e.target.closest('.remove-image')) {
            input.click();
        }
    });

    // File input change
    input.addEventListener('change', function(e) {
        handleFiles(Array.from(e.target.files));
    });

    // Drag and drop
    container.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        container.classList.add('drag-over');
    });

    container.addEventListener('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        container.classList.remove('drag-over');
    });

    container.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        container.classList.remove('drag-over');
        
        const files = Array.from(e.dataTransfer.files).filter(file => 
            file.type.startsWith('image/')
        );
        
        if (files.length > 0) {
            handleFiles(files);
        }
    });

    // Paste from clipboard
    document.addEventListener('paste', function(e) {
        const items = Array.from(e.clipboardData.items);
        const imageItems = items.filter(item => item.type.startsWith('image/'));
        
        if (imageItems.length > 0) {
            e.preventDefault();
            const files = imageItems.map(item => item.getAsFile());
            handleFiles(files);
        }
    });

    function handleFiles(files) {
        const validFiles = files.filter(file => {
            if (!file.type.startsWith('image/')) {
                alert('الرجاء اختيار ملفات صور فقط');
                return false;
            }
            if (file.size > 2048 * 1024) {
                alert(`الملف ${file.name} حجمه أكبر من 2MB`);
                return false;
            }
            return true;
        });

        if (validFiles.length === 0) return;

        selectedFiles = selectedFiles.concat(validFiles);

        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => dataTransfer.items.add(file));
        input.files = dataTransfer.files;

        updatePreview();
    }

    function updatePreview() {
        if (selectedFiles.length === 0) {
            prompt.style.display = 'block';
            previewGrid.style.display = 'none';
            return;
        }

        prompt.style.display = 'none';
        previewGrid.style.display = 'flex';
        previewGrid.innerHTML = '';

        selectedFiles.forEach((file, index) => {
            const reader = new FileReader();
            reader.onload = function(e) {
                const col = document.createElement('div');
                col.className = 'col-md-3 col-sm-4 col-6';
                col.innerHTML = `
                    <div class="image-preview-item">
                        <img src="${e.target.result}" alt="Preview">
                        <button type="button" class="remove-image" data-index="${index}">
                            <i class="fas fa-times"></i>
                        </button>
                    </div>
                `;
                previewGrid.appendChild(col);

                col.querySelector('.remove-image').addEventListener('click', function(e) {
                    e.stopPropagation();
                    removeFile(index);
                });
            };
            reader.readAsDataURL(file);
        });
    }

    function removeFile(index) {
        selectedFiles.splice(index, 1);
        
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => dataTransfer.items.add(file));
        input.files = dataTransfer.files;
        
        updatePreview();
    }
})();
</script>
@endsection