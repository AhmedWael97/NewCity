@props(['name' => 'rating', 'value' => 0, 'maxRating' => 5, 'required' => false, 'size' => 'md'])

@php
    $sizeClass = match($size) {
        'sm' => 'rating-input-sm',
        'lg' => 'rating-input-lg',
        'xl' => 'rating-input-xl',
        default => 'rating-input-md'
    };
@endphp

<div class="rating-input {{ $sizeClass }}" data-rating="{{ $value }}" data-max-rating="{{ $maxRating }}">
    <input type="hidden" name="{{ $name }}" value="{{ $value }}" @if($required) required @endif>
    
    <div class="rating-stars-input">
        @for ($i = 1; $i <= $maxRating; $i++)
            <button type="button" 
                    class="star-btn {{ $i <= $value ? 'active' : '' }}" 
                    data-rating="{{ $i }}"
                    title="{{ $i }} من {{ $maxRating }} نجوم">
                <span class="star">★</span>
            </button>
        @endfor
    </div>
    
    <div class="rating-feedback">
        <span class="rating-value">{{ $value > 0 ? $value : 'لم يتم التقييم' }}</span>
        <span class="rating-labels">
            <span data-rating="1" class="rating-label">سيء</span>
            <span data-rating="2" class="rating-label">مقبول</span>
            <span data-rating="3" class="rating-label">جيد</span>
            <span data-rating="4" class="rating-label">ممتاز</span>
            <span data-rating="5" class="rating-label">رائع</span>
        </span>
    </div>
</div>

<style>
.rating-input {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.rating-stars-input {
    display: flex;
    direction: ltr;
    gap: 4px;
}

.star-btn {
    background: none;
    border: none;
    cursor: pointer;
    padding: 4px;
    transition: all 0.2s ease;
    border-radius: 4px;
}

.star-btn:hover {
    background-color: rgba(255, 193, 7, 0.1);
    transform: scale(1.1);
}

.star-btn .star {
    color: #ddd;
    transition: color 0.2s ease;
}

.star-btn.active .star,
.star-btn:hover .star {
    color: #ffc107;
}

.rating-feedback {
    display: flex;
    align-items: center;
    gap: 12px;
    min-height: 24px;
}

.rating-value {
    font-weight: 600;
    color: #333;
}

.rating-labels {
    position: relative;
}

.rating-label {
    display: none;
    color: #666;
    font-size: 0.9rem;
    font-style: italic;
}

.rating-label.active {
    display: inline;
    color: #ffc107;
    font-weight: 500;
}

/* Size variations */
.rating-input-sm .star {
    font-size: 1.25rem;
}

.rating-input-md .star {
    font-size: 1.5rem;
}

.rating-input-lg .star {
    font-size: 1.75rem;
}

.rating-input-xl .star {
    font-size: 2rem;
}

.rating-input-sm .rating-value {
    font-size: 0.875rem;
}

.rating-input-md .rating-value {
    font-size: 1rem;
}

.rating-input-lg .rating-value {
    font-size: 1.125rem;
}

.rating-input-xl .rating-value {
    font-size: 1.25rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const ratingInputs = document.querySelectorAll('.rating-input');
    
    ratingInputs.forEach(ratingInput => {
        const hiddenInput = ratingInput.querySelector('input[type="hidden"]');
        const starBtns = ratingInput.querySelectorAll('.star-btn');
        const ratingValue = ratingInput.querySelector('.rating-value');
        const ratingLabels = ratingInput.querySelectorAll('.rating-label');
        
        let currentRating = parseInt(ratingInput.dataset.rating) || 0;
        
        // Initialize display
        updateDisplay(currentRating);
        
        starBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const rating = parseInt(this.dataset.rating);
                
                // Toggle rating - if clicking same star, set to 0
                if (rating === currentRating) {
                    currentRating = 0;
                } else {
                    currentRating = rating;
                }
                
                hiddenInput.value = currentRating;
                updateDisplay(currentRating);
                
                // Trigger change event
                hiddenInput.dispatchEvent(new Event('change'));
            });
            
            btn.addEventListener('mouseenter', function() {
                const rating = parseInt(this.dataset.rating);
                highlightStars(rating);
                showLabel(rating);
            });
        });
        
        ratingInput.addEventListener('mouseleave', function() {
            updateDisplay(currentRating);
            hideLabels();
        });
        
        function updateDisplay(rating) {
            highlightStars(rating);
            ratingValue.textContent = rating > 0 ? `${rating} من ${ratingInput.dataset.maxRating}` : 'لم يتم التقييم';
            showLabel(rating);
        }
        
        function highlightStars(rating) {
            starBtns.forEach((btn, index) => {
                btn.classList.toggle('active', index < rating);
            });
        }
        
        function showLabel(rating) {
            ratingLabels.forEach(label => {
                label.classList.toggle('active', parseInt(label.dataset.rating) === rating);
            });
        }
        
        function hideLabels() {
            ratingLabels.forEach(label => {
                label.classList.remove('active');
            });
        }
    });
});
</script>