@props([
    'name' => 'images',
    'currentImages' => [],
    'label' => 'صور المتجر',
    'maxSize' => 2048,
    'accept' => 'image/*',
    'helpText' => 'يمكنك رفع عدة صور. PNG, JPG, JPEG',
    'showCurrentImages' => true,
    'deleteInputName' => 'delete_images',
    'uploadContainerId' => 'image-upload-container',
    'inputId' => 'images-input',
    'previewGridId' => 'image-preview-grid',
    'currentImagesGridId' => 'current-images-grid'
])

<div class="image-uploader-component">
    <!-- Current Images -->
    @if($showCurrentImages && count($currentImages) > 0)
        <div class="form-group mb-4">
            <label class="font-weight-bold">
                <i class="fas fa-images"></i> الصور الحالية
            </label>
            <div class="row" id="{{ $currentImagesGridId }}">
                @foreach($currentImages as $index => $image)
                    <div class="col-md-3 col-sm-4 col-6 mb-3" data-image-index="{{ $index }}">
                        <div class="image-preview-item">
                            <img src="{{ is_string($image) ? (str_starts_with($image, 'http') ? $image : asset('storage/' . $image)) : $image }}" 
                                 alt="Image" 
                                 onerror="this.src='/images/placeholder.jpg'">
                            <button type="button" class="remove-image" onclick="toggleDeleteImage{{ $uploadContainerId }}({{ $index }}, this)">
                                <i class="fas fa-times"></i>
                            </button>
                            <input type="checkbox" 
                                   name="{{ $deleteInputName }}[]" 
                                   value="{{ $index }}" 
                                   id="delete_{{ $uploadContainerId }}_{{ $index }}" 
                                   style="display: none;">
                            <div class="delete-overlay" style="display: none;">
                                <span class="badge badge-danger">سيتم الحذف</span>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif

    <!-- Add New Images -->
    <div class="form-group">
        <label class="font-weight-bold">
            <i class="fas fa-{{ $showCurrentImages && count($currentImages) > 0 ? 'plus-circle' : 'images' }}"></i> 
            {{ $showCurrentImages && count($currentImages) > 0 ? 'إضافة صور جديدة' : $label }}
        </label>
        <div id="{{ $uploadContainerId }}" class="image-upload-zone border rounded p-4 bg-light text-center" 
             style="min-height: 200px; cursor: pointer;">
            <input type="file" 
                   id="{{ $inputId }}" 
                   name="{{ $name }}[]" 
                   accept="{{ $accept }}" 
                   multiple 
                   style="display: none;">
            
            <div id="upload-prompt-{{ $uploadContainerId }}" class="text-muted">
                <i class="fas fa-cloud-upload-alt fa-3x mb-3"></i>
                <p class="mb-2"><strong>اسحب وأفلت الصور هنا</strong></p>
                <p class="mb-2">أو <span class="text-primary" style="cursor: pointer; text-decoration: underline;">انقر للاختيار</span></p>
                <p class="small">أو استخدم Ctrl+V للصق الصور من الحافظة</p>
                <small class="text-muted">{{ $helpText }} - حجم أقصى {{ $maxSize }}KB لكل صورة</small>
            </div>
            
            <div id="{{ $previewGridId }}" class="row g-3 mt-2" style="display: none;"></div>
        </div>
        @if($showCurrentImages && count($currentImages) > 0)
            <small class="form-text text-muted">الصور الجديدة ستضاف إلى الصور الحالية</small>
        @else
            <small class="form-text text-muted">يمكنك ترك الحقل فارغاً لإنشاء صور افتراضية تلقائياً</small>
        @endif
    </div>
</div>

@once
@push('styles')
<style>
.image-upload-zone {
    transition: all 0.3s ease;
}
.image-upload-zone:hover {
    border-color: #4e73df !important;
    background-color: #f8f9fc !important;
}
.image-upload-zone.drag-over {
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
@endpush

@push('scripts')
<script>
// Toggle delete for existing images - {{ $uploadContainerId }}
function toggleDeleteImage{{ $uploadContainerId }}(index, button) {
    const container = button.closest('[data-image-index]');
    const checkbox = document.getElementById('delete_{{ $uploadContainerId }}_' + index);
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

// Image Upload Handler - {{ $uploadContainerId }}
(function() {
    const container = document.getElementById('{{ $uploadContainerId }}');
    const input = document.getElementById('{{ $inputId }}');
    const prompt = document.getElementById('upload-prompt-{{ $uploadContainerId }}');
    const previewGrid = document.getElementById('{{ $previewGridId }}');
    let selectedFiles = [];
    const maxSize = {{ $maxSize }} * 1024; // Convert KB to bytes

    if (!container || !input) return; // Exit if elements don't exist

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
            if (file.size > maxSize) {
                alert(`الملف ${file.name} حجمه أكبر من {{ $maxSize }}KB`);
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
@endpush
@endonce
