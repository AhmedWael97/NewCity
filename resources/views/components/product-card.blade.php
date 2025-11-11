@props(['product', 'size' => 'normal'])

<div class="product-card {{ $size === 'small' ? 'product-card-small' : '' }}" 
     data-category="{{ $product->category }}"
     data-price="{{ $product->final_price }}"
     data-name="{{ $product->name }}"
     data-featured="{{ $product->is_featured ? 'true' : 'false' }}">
    
    <div class="product-image-container">
        @if($product->images && is_array($product->images) && count($product->images) > 0)
            <img src="{{ $product->images[0] }}" 
                 alt="{{ $product->name }}" 
                 class="product-image"
                 loading="lazy"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="product-image-placeholder" style="display: none;">
                <div class="placeholder-icon">📦</div>
                @if($size !== 'small')
                    <span class="placeholder-text">{{ Str::limit($product->name, 20) }}</span>
                @endif
            </div>
        @else
            <div class="product-image-placeholder">
                <div class="placeholder-icon">📦</div>
                @if($size !== 'small')
                    <span class="placeholder-text">{{ Str::limit($product->name, 20) }}</span>
                @endif
            </div>
        @endif
        
        @if($product->has_discount)
            <div class="product-discount-badge">
                {{ $product->discount_percentage }}%-
            </div>
        @endif
        
        @if($product->is_featured)
            <div class="product-featured-badge">⭐</div>
        @endif
        
        @if($product->stock_quantity == 0)
            <div class="product-status-badge out-of-stock">نفدت</div>
        @elseif($product->stock_quantity <= 5)
            <div class="product-status-badge low-stock">{{ $product->stock_quantity }} متبقي</div>
        @endif
    </div>
    
    <div class="product-content">
        <h3 class="product-name" title="{{ $product->name }}">
            {{ Str::limit($product->name, $size === 'small' ? 30 : 45) }}
        </h3>
        
        @if($size !== 'small' && $product->category)
            <div class="product-category">{{ $product->category }}</div>
        @endif
        
        <div class="product-price-section">
            @if($product->has_discount)
                <div class="price-with-discount">
                    <span class="current-price">{{ number_format($product->final_price, 0) }} ج.م</span>
                    <span class="original-price">{{ number_format($product->price, 0) }} ج.م</span>
                </div>
                @if($size !== 'small')
                    <div class="savings-badge">
                        وفر {{ number_format($product->price - $product->final_price, 0) }} ج.م
                    </div>
                @endif
            @else
                <span class="current-price">{{ number_format($product->price, 0) }} ج.م</span>
            @endif
        </div>
        
        @if($size !== 'small')
            <div class="product-description">
                {{ Str::limit($product->description, 60) }}
            </div>
            
            <div class="product-meta">
                <span class="stock-info {{ $product->stock_quantity <= 5 ? 'low-stock' : '' }}">
                    <i class="stock-icon">📦</i>
                    {{ $product->stock_quantity }} متوفر
                </span>
            </div>
        @endif
        
        <div class="product-actions">
            <button class="btn-add-cart {{ $product->stock_quantity == 0 ? 'disabled' : '' }}" 
                    data-product-id="{{ $product->id }}"
                    {{ $product->stock_quantity == 0 ? 'disabled' : '' }}>
                @if($product->stock_quantity == 0)
                    <span class="btn-icon">🚫</span>
                    @if($size !== 'small')<span class="btn-text">نفدت</span>@endif
                @else
                    <span class="btn-icon">🛒</span>
                    @if($size !== 'small')<span class="btn-text">أضف للسلة</span>@endif
                @endif
            </button>
            
            <button class="btn-wishlist" data-product-id="{{ $product->id }}">
                <span class="wishlist-icon">🤍</span>
            </button>
        </div>
    </div>
</div>

