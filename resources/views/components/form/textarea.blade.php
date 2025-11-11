{{-- Textarea Component --}}
@props([
    'name',
    'label',
    'value' => '',
    'required' => false,
    'placeholder' => '',
    'helpText' => '',
    'rows' => 3,
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
    
    <textarea 
        class="form-control @error($name) is-invalid @enderror" 
        id="{{ $name }}" 
        name="{{ $name }}" 
        rows="{{ $rows }}"
        placeholder="{{ $placeholder }}"
        {{ $required ? 'required' : '' }}
        {{ $disabled ? 'disabled' : '' }}
        {{ $readonly ? 'readonly' : '' }}
        {{ $attributes }}
    >{{ old($name, $value) }}</textarea>
    
    @error($name)
        <div class="invalid-feedback d-block">
            <i class="fas fa-exclamation-circle"></i> {{ $message }}
        </div>
    @enderror
    
    @if($helpText)
        <small class="form-text text-muted">
            <i class="fas fa-info-circle"></i> {{ $helpText }}
        </small>
    @endif
</div>
