@props(['service', 'size' => 'normal'])

<div class="service-card {{ $size === 'small' ? 'service-card-small' : '' }}"
     data-category="{{ $service->category }}"
     data-price="{{ $service->final_price }}"
     data-name="{{ $service->name }}"
     data-duration="{{ $service->duration_minutes }}"
     data-appointment="{{ $service->requires_appointment ? 'true' : 'false' }}">
    
    <div class="service-image-container">
        @if($service->images && is_array($service->images) && count($service->images) > 0)
            <img src="{{ $service->images[0] }}" 
                 alt="{{ $service->name }}" 
                 class="service-image"
                 loading="lazy"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="service-image-placeholder" style="display: none;">
                <div class="placeholder-icon">🔧</div>
                @if($size !== 'small')
                    <span class="placeholder-text">{{ Str::limit($service->name, 20) }}</span>
                @endif
            </div>
        @else
            <div class="service-image-placeholder">
                <div class="placeholder-icon">🔧</div>
                @if($size !== 'small')
                    <span class="placeholder-text">{{ Str::limit($service->name, 20) }}</span>
                @endif
            </div>
        @endif
        
        @if($service->has_discount)
            <div class="service-discount-badge">
                {{ $service->discount_percentage }}%-
            </div>
        @endif
        
        @if($service->is_featured)
            <div class="service-featured-badge">⭐</div>
        @endif
        
        @if($service->requires_appointment)
            <div class="service-appointment-badge">📅 موعد</div>
        @endif
    </div>
    
    <div class="service-content">
        <h3 class="service-name" title="{{ $service->name }}">
            {{ Str::limit($service->name, $size === 'small' ? 30 : 45) }}
        </h3>
        
        @if($size !== 'small' && $service->category)
            <div class="service-category">{{ $service->category }}</div>
        @endif
        
        <div class="service-price-section">
            @if($service->has_discount)
                <div class="price-with-discount">
                    <span class="current-price">{{ number_format($service->final_price, 0) }} ج.م</span>
                    <span class="original-price">{{ number_format($service->price, 0) }} ج.م</span>
                </div>
                @if($size !== 'small')
                    <div class="savings-badge">
                        وفر {{ number_format($service->price - $service->final_price, 0) }} ج.م
                    </div>
                @endif
            @else
                <span class="current-price">{{ number_format($service->price, 0) }} ج.م</span>
            @endif
        </div>
        
        @if($size !== 'small')
            <div class="service-description">
                {{ Str::limit($service->description, 60) }}
            </div>
            
            <div class="service-meta">
                @if($service->formatted_duration)
                    <span class="duration-info">
                        <i class="duration-icon">⏱️</i>
                        {{ $service->formatted_duration }}
                    </span>
                @endif
                
                @if($service->requires_appointment)
                    <span class="appointment-info">
                        <i class="appointment-icon">📅</i>
                        يتطلب موعد
                    </span>
                @endif
            </div>
        @endif
        
        <div class="service-actions">
            <button class="btn-book-service" 
                    data-service-id="{{ $service->id }}"
                    data-requires-appointment="{{ $service->requires_appointment ? 'true' : 'false' }}">
                @if($service->requires_appointment)
                    <span class="btn-icon">📅</span>
                    @if($size !== 'small')<span class="btn-text">احجز موعد</span>@endif
                @else
                    <span class="btn-icon">🛒</span>
                    @if($size !== 'small')<span class="btn-text">اطلب الآن</span>@endif
                @endif
            </button>
            
            <button class="btn-wishlist" data-service-id="{{ $service->id }}">
                <span class="wishlist-icon">🤍</span>
            </button>
        </div>
    </div>
</div>

