@props(['rating' => 0, 'maxRating' => 5, 'size' => 'md', 'showText' => true, 'readonly' => true])

@php
    $fullStars = floor($rating);
    $hasHalfStar = ($rating - $fullStars) >= 0.5;
    $emptyStars = $maxRating - $fullStars - ($hasHalfStar ? 1 : 0);
    
    $sizeClass = match($size) {
        'sm' => 'rating-sm',
        'lg' => 'rating-lg',
        'xl' => 'rating-xl',
        default => 'rating-md'
    };
@endphp

<div class="rating-display {{ $sizeClass }}" {{ $attributes }}>
    <div class="rating-stars">
        {{-- Full stars --}}
        @for ($i = 0; $i < $fullStars; $i++)
            <span class="star star-full">★</span>
        @endfor
        
        {{-- Half star --}}
        @if ($hasHalfStar)
            <span class="star star-half">★</span>
        @endif
        
        {{-- Empty stars --}}
        @for ($i = 0; $i < $emptyStars; $i++)
            <span class="star star-empty">☆</span>
        @endfor
    </div>
    
    @if ($showText)
        <span class="rating-text">
            {{ number_format($rating, 1) }} من {{ $maxRating }}
        </span>
    @endif
</div>

<style>
.rating-display {
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.rating-stars {
    display: inline-flex;
    direction: ltr;
}

.star {
    color: #ddd;
    transition: color 0.2s ease;
}

.star-full {
    color: #ffc107;
}

.star-half {
    background: linear-gradient(90deg, #ffc107 50%, #ddd 50%);
    -webkit-background-clip: text;
    -webkit-text-fill-color: transparent;
    background-clip: text;
}

.star-empty {
    color: #ddd;
}

/* Size variations */
.rating-sm .star {
    font-size: 1rem;
}

.rating-md .star {
    font-size: 1.25rem;
}

.rating-lg .star {
    font-size: 1.5rem;
}

.rating-xl .star {
    font-size: 2rem;
}

.rating-sm .rating-text {
    font-size: 0.875rem;
}

.rating-md .rating-text {
    font-size: 1rem;
}

.rating-lg .rating-text {
    font-size: 1.125rem;
}

.rating-xl .rating-text {
    font-size: 1.25rem;
}

.rating-text {
    color: #666;
    font-weight: 500;
}
</style>