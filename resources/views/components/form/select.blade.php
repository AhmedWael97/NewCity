{{-- Select Component --}}
@props([
    'name',
    'label',
    'options' => [],
    'value' => '',
    'required' => false,
    'placeholder' => 'اختر...',
    'helpText' => '',
    'icon' => null,
    'disabled' => false,
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
        
        <select 
            class="form-control @error($name) is-invalid @enderror" 
            id="{{ $name }}" 
            name="{{ $name }}"
            {{ $required ? 'required' : '' }}
            {{ $disabled ? 'disabled' : '' }}
            {{ $attributes }}
        >
            @if($placeholder)
                <option value="">{{ $placeholder }}</option>
            @endif
            
            @foreach($options as $optionValue => $optionLabel)
                <option value="{{ $optionValue }}" 
                    {{ old($name, $value) == $optionValue ? 'selected' : '' }}>
                    {{ $optionLabel }}
                </option>
            @endforeach
        </select>
        
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
