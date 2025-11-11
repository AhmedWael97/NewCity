{{-- Text Input Component --}}
@props([
    'name',
    'label',
    'type' => 'text',
    'value' => '',
    'required' => false,
    'placeholder' => '',
    'helpText' => '',
    'icon' => null,
    'disabled' => false,
    'readonly' => false,
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
    
    <div class="{{ $icon ? 'input-group' : '' }}">
        @if($icon)
            <div class="input-group-prepend">
                <span class="input-group-text" style="display: flex; align-items: center; justify-content: center;">
                    <i class="fas fa-{{ $icon }}"></i>
                </span>
            </div>
        @endif
        
        <input 
            type="{{ $type }}" 
            class="form-control @error($name) is-invalid @enderror" 
            id="{{ $name }}" 
            name="{{ $name }}" 
            value="{{ old($name, $value) }}"
            placeholder="{{ $placeholder }}"
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $readonly ? 'readonly' : '' }}
            {{ $attributes }}
        >
        
        @error($name)
            <div class="invalid-feedback d-block">
                <i class="fas fa-exclamation-circle"></i> {{ $message }}
            </div>
        @enderror
    </div>
    
    @if($helpText)
        <small class="form-text text-muted">
            <i class="fas fa-info-circle"></i> {{ $helpText }}
        </small>
    @endif
</div>
