@extends('layouts.admin')

@section('title', 'إضافة متجر جديد')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plus"></i> إضافة متجر جديد
        </h1>
        <a href="{{ route('admin.shops.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-right"></i> العودة للقائمة
        </a>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">معلومات المتجر</h6>
                </div>
                <div class="card-body">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <h5 class="alert-heading"><i class="fas fa-exclamation-triangle"></i> يوجد أخطاء في النموذج:</h5>
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="close" data-dismiss="alert">
                                <span>&times;</span>
                            </button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.shops.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <div class="row">
                            <div class="col-md-6">
                                <x-form.input 
                                    name="name" 
                                    label="اسم المتجر" 
                                    :value="old('name')"
                                    icon="fas fa-store"
                                    :required="true"
                                    placeholder="أدخل اسم المتجر"
                                />
                            </div>
                            
                            <div class="col-md-6">
                                <x-form.input 
                                    name="slug" 
                                    label="الرابط (Slug)" 
                                    :value="old('slug')"
                                    icon="fas fa-link"
                                    placeholder="سيتم إنشاؤه تلقائياً من الاسم"
                                    helpText="اتركه فارغاً للإنشاء التلقائي"
                                />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <x-form.select 
                                    name="user_id" 
                                    label="المالك" 
                                    :options="$users->pluck('name', 'id')->map(fn($name, $id) => $name . ' (' . $users->find($id)->email . ')')->toArray()"
                                    :value="old('user_id')"
                                    icon="fas fa-user"
                                    :required="true"
                                    placeholder="اختر المالك"
                                />
                            </div>

                            <div class="col-md-3">
                                <x-form.select 
                                    name="city_id" 
                                    label="المدينة" 
                                    :options="$cities->pluck('name', 'id')->toArray()"
                                    :value="old('city_id')"
                                    icon="fas fa-city"
                                    :required="true"
                                    placeholder="اختر المدينة"
                                />
                            </div>

                            <div class="col-md-3">
                                <x-form.select 
                                    name="category_id" 
                                    label="التصنيف" 
                                    :options="$categories->pluck('name', 'id')->toArray()"
                                    :value="old('category_id')"
                                    icon="fas fa-tags"
                                    :required="true"
                                    placeholder="اختر التصنيف"
                                />
                            </div>
                        </div>

                        <x-form.textarea 
                            name="description" 
                            label="الوصف" 
                            :value="old('description')"
                            icon="fas fa-align-right"
                            rows="4"
                            placeholder="وصف تفصيلي عن المتجر"
                        />

                        <x-google-maps-picker 
                            addressId="address"
                            latitudeId="latitude"
                            longitudeId="longitude"
                            :addressValue="old('address', '')"
                            :latitudeValue="old('latitude', '')"
                            :longitudeValue="old('longitude', '')"
                            height="450px"
                        />

                        <div class="row">
                            <div class="col-md-12">
                                <x-form.input 
                                    name="address" 
                                    label="العنوان" 
                                    :value="old('address')"
                                    icon="fas fa-map-marker-alt"
                                    placeholder="عنوان المتجر الكامل"
                                    helpText="سيتم ملؤه تلقائياً عند تحديد الموقع على الخريطة"
                                />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <x-form.input 
                                    name="latitude" 
                                    type="number"
                                    label="خط العرض" 
                                    :value="old('latitude')"
                                    icon="fas fa-map-pin"
                                    placeholder="24.774265"
                                    helpText="يتم ملؤه تلقائياً من الخريطة"
                                    step="any"
                                    readonly
                                />
                            </div>
                            <div class="col-md-6">
                                <x-form.input 
                                    name="longitude" 
                                    type="number"
                                    label="خط الطول" 
                                    :value="old('longitude')"
                                    icon="fas fa-map-pin"
                                    placeholder="46.738586"
                                    helpText="يتم ملؤه تلقائياً من الخريطة"
                                    step="any"
                                    readonly
                                />
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <x-form.input 
                                    name="phone" 
                                    type="tel"
                                    label="رقم الهاتف" 
                                    :value="old('phone')"
                                    icon="fas fa-phone"
                                    placeholder="+966 50 123 4567"
                                />
                            </div>
                            <div class="col-md-4">
                                <x-form.input 
                                    name="email" 
                                    type="email"
                                    label="البريد الإلكتروني" 
                                    :value="old('email')"
                                    icon="fas fa-envelope"
                                    placeholder="shop@example.com"
                                />
                            </div>
                            <div class="col-md-4">
                                <x-form.input 
                                    name="website" 
                                    type="url"
                                    label="الموقع الإلكتروني" 
                                    :value="old('website')"
                                    icon="fas fa-globe"
                                    placeholder="https://example.com"
                                />
                            </div>
                        </div>

                        <!-- Image Upload with Drag & Drop and Paste -->
                        <div class="form-group">
                            <label class="font-weight-bold">
                                <i class="fas fa-images"></i> صور المتجر
                            </label>
                            <div id="image-upload-container" class="border rounded p-4 bg-light text-center" 
                                 style="min-height: 200px; cursor: pointer; position: relative;">
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
                            <small class="form-text text-muted">يمكنك رفع عدة صور. اترك الحقل فارغاً لإنشاء صور افتراضية تلقائياً.</small>
                        </div>

                        <div class="row">
                            <div class="col-md-4">
                                <x-form.select 
                                    name="status" 
                                    label="الحالة" 
                                    :options="[
                                        'pending' => 'في الانتظار',
                                        'approved' => 'مقبول',
                                        'rejected' => 'مرفوض',
                                        'suspended' => 'معلق'
                                    ]"
                                    :value="old('status', 'pending')"
                                    icon="fas fa-flag"
                                />
                            </div>
                            <div class="col-md-8">
                                <label class="d-block mb-2">الخيارات</label>
                                <div class="d-flex align-items-center" style="gap: 2rem; flex-wrap: wrap;">
                                    <x-form.checkbox 
                                        name="is_verified" 
                                        label="محقق" 
                                        :checked="old('is_verified', false)"
                                    />
                                    <x-form.checkbox 
                                        name="is_featured" 
                                        label="مميز" 
                                        :checked="old('is_featured', false)"
                                    />
                                    <x-form.checkbox 
                                        name="is_active" 
                                        label="نشط" 
                                        :checked="old('is_active', true)"
                                    />
                                </div>
                            </div>
                        </div>

                        <x-form.textarea 
                            name="verification_notes" 
                            label="ملاحظات التحقق" 
                            :value="old('verification_notes')"
                            icon="fas fa-sticky-note"
                            rows="3"
                            placeholder="ملاحظات خاصة بعملية التحقق من المتجر"
                            helpText="ملاحظات داخلية للإدارة"
                        />

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> حفظ المتجر
                            </button>
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
}
.image-preview-item .remove-image:hover {
    background: rgba(220, 53, 69, 1);
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

// Image Upload with Drag & Drop and Paste
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
        // Validate files
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

        // Add to selected files
        selectedFiles = selectedFiles.concat(validFiles);

        // Update file input
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => dataTransfer.items.add(file));
        input.files = dataTransfer.files;

        // Update UI
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

                // Add remove handler
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
        
        // Update file input
        const dataTransfer = new DataTransfer();
        selectedFiles.forEach(file => dataTransfer.items.add(file));
        input.files = dataTransfer.files;
        
        updatePreview();
    }
})();
</script>
@endsection