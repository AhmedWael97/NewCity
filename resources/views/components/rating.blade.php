@props([
    'rating' => 0,
    'maxRating' => 5,
    'showNumber' => true,
    'showCount' => false,
    'reviewCount' => 0,
    'size' => 'md',
    'readonly' => true
])

@php
    // Ensure rating is a valid number and within bounds
    $ratingValue = is_numeric($rating) ? (float) $rating : 0;
    $ratingValue = max(0, min($maxRating, $ratingValue));
    
    // Calculate stars
    $fullStars = floor($ratingValue);
    $hasHalfStar = ($ratingValue - $fullStars) >= 0.5;
    $emptyStars = $maxRating - $fullStars - ($hasHalfStar ? 1 : 0);
    
    // Determine classes based on size
    $containerClass = 'rating-component flex items-center gap-2';
    $starClass = 'rating-star';
    $numberClass = 'rating-number font-semibold text-gray-700';
    $countClass = 'rating-count text-gray-500 text-sm';
    
    if ($size === 'sm') {
        $containerClass .= ' text-sm';
        $starClass .= ' text-sm';
    } elseif ($size === 'lg') {
        $containerClass .= ' text-lg';
        $starClass .= ' text-lg';
    } elseif ($size === 'xl') {
        $containerClass .= ' text-xl';
        $starClass .= ' text-xl';
    } else {
        $containerClass .= ' text-base';
        $starClass .= ' text-base';
    }
@endphp

<div {{ $attributes->merge(['class' => $containerClass]) }}>
    <!-- Stars Container -->
    <div class="rating-stars flex items-center" dir="ltr">
        @for ($i = 1; $i <= $fullStars; $i++)
            <span class="{{ $starClass }} text-yellow-400">★</span>
        @endfor
        
        @if ($hasHalfStar)
            <span class="{{ $starClass }} text-yellow-400">☆</span>
        @endif
        
        @for ($i = 1; $i <= $emptyStars; $i++)
            <span class="{{ $starClass }} text-gray-300">☆</span>
        @endfor
    </div>
    
    <!-- Rating Information -->
    @if ($showNumber)
        <span class="{{ $numberClass }}">
            {{ number_format($ratingValue, 1) }}
        </span>
    @endif
    
    @if ($showCount && $reviewCount > 0)
        <span class="{{ $countClass }}">
            ({{ number_format($reviewCount) }})
        </span>
    @endif
</div>