@props(['shop', 'showBreakdown' => true, 'showRecentReviews' => true, 'maxReviews' => 3])

@php
    $averageRating = $shop->averageRating();
    $totalRatings = $shop->totalRatings();
    $distribution = $shop->getRatingDistribution();
    $recentRatings = $showRecentReviews ? $shop->ratings()->with('user')->latest()->limit($maxReviews)->get() : collect();
@endphp

<div class="rating-summary" {{ $attributes }}>
    <!-- Overall Rating -->
    <div class="rating-overview">
        <div class="rating-score">
            <div class="score-number">{{ number_format($averageRating, 1) }}</div>
            <div class="score-stars">
                <x-rating.display :rating="$averageRating" :show-text="false" size="lg" />
            </div>
            <div class="score-count">{{ $totalRatings }} تقييم</div>
        </div>
        
        @if ($showBreakdown && $totalRatings > 0)
            <div class="rating-breakdown">
                @for ($i = 5; $i >= 1; $i--)
                    @php
                        $count = $distribution[$i] ?? 0;
                        $percentage = $totalRatings > 0 ? ($count / $totalRatings) * 100 : 0;
                    @endphp
                    <div class="breakdown-row">
                        <span class="star-label">{{ $i }} نجوم</span>
                        <div class="progress-bar">
                            <div class="progress-fill" style="width: {{ $percentage }}%"></div>
                        </div>
                        <span class="count-label">{{ $count }}</span>
                    </div>
                @endfor
            </div>
        @endif
    </div>
    
    <!-- Recent Reviews -->
    @if ($showRecentReviews && $recentRatings->isNotEmpty())
        <div class="recent-reviews">
            <h4 class="section-title">آخر التقييمات</h4>
            <div class="reviews-list">
                @foreach ($recentRatings as $rating)
                    <div class="review-item">
                        <div class="review-header">
                            <div class="reviewer-info">
                                @if ($rating->user->avatar)
                                    <img src="{{ $rating->user->avatar }}" alt="{{ $rating->user->name }}" class="reviewer-avatar">
                                @else
                                    <div class="reviewer-avatar-placeholder">{{ substr($rating->user->name, 0, 1) }}</div>
                                @endif
                                <div class="reviewer-details">
                                    <span class="reviewer-name">{{ $rating->user->name }}</span>
                                    <span class="review-date">{{ $rating->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                            <div class="review-rating">
                                <x-rating.display :rating="$rating->rating" :show-text="false" size="sm" />
                                @if ($rating->is_verified)
                                    <span class="verified-badge">تم التحقق</span>
                                @endif
                            </div>
                        </div>
                        
                        @if ($rating->comment)
                            <div class="review-comment">{{ $rating->comment }}</div>
                        @endif
                        
                        @if ($rating->helpful_count > 0)
                            <div class="review-helpful">
                                <span class="helpful-count">{{ $rating->helpful_count }} وجدوا هذا مفيد</span>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
            
            <div class="view-all-reviews">
                <a href="#" class="btn btn-outline-primary btn-sm" onclick="loadAllReviews({{ $shop->id }})">
                    عرض جميع التقييمات ({{ $totalRatings }})
                </a>
            </div>
        </div>
    @endif
</div>

<style>
.rating-summary {
    background: white;
    border-radius: 12px;
    padding: 24px;
    border: 1px solid #e5e7eb;
}

.rating-overview {
    display: grid;
    grid-template-columns: auto 1fr;
    gap: 24px;
    margin-bottom: 24px;
    align-items: center;
}

.rating-score {
    text-align: center;
    min-width: 120px;
}

.score-number {
    font-size: 3rem;
    font-weight: 700;
    color: #333;
    line-height: 1;
}

.score-stars {
    margin: 8px 0;
}

.score-count {
    color: #666;
    font-size: 0.9rem;
}

.rating-breakdown {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.breakdown-row {
    display: grid;
    grid-template-columns: 60px 1fr 40px;
    align-items: center;
    gap: 12px;
    font-size: 0.875rem;
}

.star-label {
    color: #666;
    font-weight: 500;
}

.progress-bar {
    height: 8px;
    background: #f3f4f6;
    border-radius: 4px;
    overflow: hidden;
}

.progress-fill {
    height: 100%;
    background: linear-gradient(90deg, #fbbf24, #f59e0b);
    transition: width 0.3s ease;
}

.count-label {
    color: #666;
    text-align: center;
    font-weight: 500;
}

.recent-reviews {
    border-top: 1px solid #e5e7eb;
    padding-top: 24px;
}

.section-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: #333;
    margin-bottom: 16px;
}

.reviews-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.review-item {
    padding: 16px;
    border: 1px solid #f3f4f6;
    border-radius: 8px;
    background: #fafafa;
}

.review-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 12px;
}

.reviewer-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.reviewer-avatar {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    object-fit: cover;
}

.reviewer-avatar-placeholder {
    width: 40px;
    height: 40px;
    border-radius: 50%;
    background: linear-gradient(135deg, #667eea, #764ba2);
    display: flex;
    align-items: center;
    justify-content: center;
    color: white;
    font-weight: 600;
    font-size: 1.2rem;
}

.reviewer-details {
    display: flex;
    flex-direction: column;
}

.reviewer-name {
    font-weight: 600;
    color: #333;
}

.review-date {
    font-size: 0.8rem;
    color: #666;
}

.review-rating {
    display: flex;
    align-items: center;
    gap: 8px;
}

.verified-badge {
    background: #10b981;
    color: white;
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 0.75rem;
    font-weight: 500;
}

.review-comment {
    color: #374151;
    line-height: 1.6;
    margin-bottom: 8px;
}

.review-helpful {
    font-size: 0.8rem;
    color: #6b7280;
}

.view-all-reviews {
    margin-top: 16px;
    text-align: center;
}

/* Responsive Design */
@media (max-width: 768px) {
    .rating-overview {
        grid-template-columns: 1fr;
        text-align: center;
    }
    
    .breakdown-row {
        grid-template-columns: 70px 1fr 30px;
        gap: 8px;
    }
    
    .review-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 8px;
    }
    
    .reviewer-info {
        width: 100%;
    }
}
</style>

<script>
function loadAllReviews(shopId) {
    // This function can be implemented to load all reviews in a modal or separate page
    console.log('Loading all reviews for shop:', shopId);
    // You can implement this with AJAX or redirect to a reviews page
}
</script>