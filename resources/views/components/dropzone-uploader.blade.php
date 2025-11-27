@props([
    'name' => 'images',
    'currentImages' => [],
    'label' => 'صور المتجر',
    'maxSize' => 2, // MB
    'maxFiles' => 10,
    'acceptedFiles' => 'image/*',
    'helpText' => 'يمكنك رفع عدة صور. PNG, JPG, JPEG (حد أقصى 2MB لكل صورة)',
    'showCurrentImages' => true,
    'deleteInputName' => 'delete_images',
    'dropzoneId' => 'dropzone-uploader'
])

@once
@push('styles')
<link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
<style>
.dropzone {
    border: 2px dashed #0087F7;
    border-radius: 5px;
    background: #f8f9fa;
    min-height: 150px;
}
.dropzone .dz-message {
    font-size: 1.2rem;
    color: #6c757d;
}
.current-image-item {
    position: relative;
    margin-bottom: 15px;
}
.current-image-item img {
    width: 100%;
    height: 150px;
    object-fit: cover;
    border-radius: 5px;
}
.current-image-item .delete-btn {
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
    transition: all 0.3s;
}
.current-image-item .delete-btn:hover {
    background: rgba(220, 53, 69, 1);
    transform: scale(1.1);
}
.current-image-item .delete-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(220, 53, 69, 0.7);
    display: none;
    align-items: center;
    justify-content: center;
    border-radius: 5px;
}
.current-image-item.marked-for-deletion {
    opacity: 0.5;
}
.current-image-item.marked-for-deletion .delete-overlay {
    display: flex;
}
</style>
@endpush
@endonce

<div class="dropzone-uploader-component">
    <!-- Current Images -->
    @if($showCurrentImages && count($currentImages) > 0)
        <div class="form-group mb-4">
            <label class="font-weight-bold">
                <i class="fas fa-images"></i> الصور الحالية
            </label>
            <div class="row" id="current-images-grid-{{ $dropzoneId }}">
                @foreach($currentImages as $index => $image)
                    <div class="col-md-3 col-sm-4 col-6 mb-3">
                        <div class="current-image-item" data-image-index="{{ $index }}">
                            <img src="{{ is_string($image) ? (str_starts_with($image, 'http') ? $image : asset('storage/' . $image)) : $image }}" 
                                 alt="Image" 
                                 onerror="this.src='/images/placeholder.jpg'">
                            <button type="button" class="delete-btn" onclick="toggleDeleteCurrentImage('{{ $dropzoneId }}', {{ $index }}, this)">
                                <i class="fas fa-times"></i>
                            </button>
                            <input type="checkbox" 
                                   name="{{ $deleteInputName }}[]" 
                                   value="{{ $index }}" 
                                   id="delete-{{ $dropzoneId }}-{{ $index }}" 
                                   style="display: none;">
                            <div class="delete-overlay">
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
        <div id="{{ $dropzoneId }}" class="dropzone">
            <div class="dz-message" data-dz-message>
                <i class="fas fa-cloud-upload-alt fa-3x mb-3 d-block"></i>
                <span><strong>اسحب وأفلت الصور هنا</strong></span>
                <p class="small text-muted mt-2">أو انقر للاختيار من جهازك</p>
                <p class="small text-muted">{{ $helpText }}</p>
            </div>
        </div>
    </div>
</div>

@once
@push('scripts')
<script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
<script>
// Disable auto discover
Dropzone.autoDiscover = false;

// Toggle delete for current images
function toggleDeleteCurrentImage(dropzoneId, index, button) {
    const container = button.closest('.current-image-item');
    const checkbox = document.getElementById('delete-' + dropzoneId + '-' + index);
    
    checkbox.checked = !checkbox.checked;
    
    if (checkbox.checked) {
        container.classList.add('marked-for-deletion');
        button.innerHTML = '<i class="fas fa-undo"></i>';
    } else {
        container.classList.remove('marked-for-deletion');
        button.innerHTML = '<i class="fas fa-times"></i>';
    }
}
</script>
@endpush
@endonce

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const dropzoneElement = document.getElementById('{{ $dropzoneId }}');
    
    if (dropzoneElement && !dropzoneElement.dropzone) {
        const myDropzone = new Dropzone("#{{ $dropzoneId }}", {
            url: "#", // We'll use the form's action
            autoProcessQueue: false,
            uploadMultiple: true,
            parallelUploads: {{ $maxFiles }},
            maxFiles: {{ $maxFiles }},
            maxFilesize: {{ $maxSize }}, // MB
            acceptedFiles: "{{ $acceptedFiles }}",
            addRemoveLinks: true,
            dictDefaultMessage: "اسحب وأفلت الصور هنا أو انقر للاختيار",
            dictRemoveFile: "حذف",
            dictCancelUpload: "إلغاء",
            dictMaxFilesExceeded: "لا يمكن رفع أكثر من {{ $maxFiles }} صور",
            dictFileTooBig: "الملف كبير جداً (@{{filesize}}MB). الحد الأقصى: {{ $maxSize }}MB",
            dictInvalidFileType: "نوع الملف غير مدعوم",
            
            init: function() {
                const dz = this;
                const form = dropzoneElement.closest('form');
                
                // Handle form submission
                if (form) {
                    form.addEventListener('submit', function(e) {
                        e.preventDefault();
                        e.stopPropagation();
                        
                        const formData = new FormData(form);
                        
                        // Add dropzone files to form data
                        dz.files.forEach((file, index) => {
                            formData.append('{{ $name }}[]', file);
                        });
                        
                        // Submit form via AJAX or regular submission
                        if (dz.files.length > 0) {
                            // If there are files, we need to use FormData
                            fetch(form.action, {
                                method: form.method,
                                body: formData,
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'X-Requested-With': 'XMLHttpRequest'
                                }
                            }).then(response => {
                                if (response.ok) {
                                    window.location.href = response.url;
                                } else {
                                    return response.json();
                                }
                            }).then(data => {
                                if (data && data.errors) {
                                    // Handle validation errors
                                    Object.keys(data.errors).forEach(key => {
                                        alert(data.errors[key].join('\n'));
                                    });
                                }
                            }).catch(error => {
                                console.error('Error:', error);
                                alert('حدث خطأ أثناء إرسال النموذج');
                            });
                        } else {
                            // No files, submit normally
                            form.submit();
                        }
                    });
                }
            }
        });
    }
});
</script>
@endpush
