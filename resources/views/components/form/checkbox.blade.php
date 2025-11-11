{{-- Checkbox Component --}}
@props([
    'name',
    'label',
    'value' => '1',
    'checked' => false,
    'helpText' => '',
    'disabled' => false,
])

<div class="form-group" style="margin-bottom: 30px;">
    <div class="custom-control custom-checkbox">
        <input 
            type="checkbox" 
            class="custom-control-input @error($name) is-invalid @enderror" 
            id="{{ $name }}" 
            name="{{ $name }}" 
            value="{{ $value }}"
            {{ old($name, $checked) ? 'checked' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $attributes }}
        >
        <label class="custom-control-label" for="{{ $name }}">
            {{ $label }}
        </label>
        
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
</div>
