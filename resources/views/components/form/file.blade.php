{{-- File Input Component --}}
@props([
    'name',
    'label',
    'required' => false,
    'helpText' => '',
    'accept' => 'image/*',
    'preview' => false,
    'currentFile' => null,
])

<div class="form-group" style="margin-bottom: 30px;">
    @if($label)
        <label for="{{ $name }}" class="form-label">
            {{ $label }}
            @if($required)
                <span class="text-danger">*</span>
            @endif
        </label>
    @endif
    
    <div class="custom-file">
        <input 
            type="file" 
            class="custom-file-input @error($name) is-invalid @enderror" 
            id="{{ $name }}" 
            name="{{ $name }}"
            accept="{{ $accept }}"
            {{ $required ? 'required' : '' }}
            {{ $attributes }}
            @if($preview) onchange="previewFile(this, '{{ $name }}_preview')" @endif
        >
        <label class="custom-file-label" for="{{ $name }}">اختر ملف...</label>
        
        @error($name)
            <div class="invalid-feedback d-block">
                <i class="fas fa-exclamation-circle"></i> {{ $message }}
            </div>
        @enderror
    </div>
    
    @if($helpText)
        <small class="form-text text-muted d-block mt-1">
            <i class="fas fa-info-circle"></i> {{ $helpText }}
        </small>
    @endif
    
    @if($preview)
        <div class="mt-2">
            @if($currentFile)
                <div class="current-file mb-2">
                    <img src="{{ asset('storage/' . $currentFile) }}" 
                         alt="Current" 
                         class="img-thumbnail" 
                         style="max-height: 150px;">
                    <p class="text-muted small mt-1">الملف الحالي</p>
                </div>
            @endif
            <img id="{{ $name }}_preview" 
                 src="" 
                 alt="Preview" 
                 class="img-thumbnail d-none" 
                 style="max-height: 150px;">
        </div>
    @endif
</div>

@if($preview)
    @push('scripts')
    <script>
        function previewFile(input, previewId) {
            const preview = document.getElementById(previewId);
            const file = input.files[0];
            const reader = new FileReader();

            reader.onloadend = function() {
                preview.src = reader.result;
                preview.classList.remove('d-none');
            }

            if (file) {
                reader.readAsDataURL(file);
            } else {
                preview.src = "";
                preview.classList.add('d-none');
            }
        }
    </script>
    @endpush
@endif
