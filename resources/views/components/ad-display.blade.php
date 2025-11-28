@props([
    'type' => 'banner',
    'placement' => 'homepage',
    'cityId' => null,
    'limit' => 1,
    'class' => ''
])

@php
use App\Services\AdService;

$adService = app(AdService::class);

switch ($type) {
    case 'hero':
        $ads = $adService->getHeroAds($cityId);
        break;
    case 'sidebar':
        $ads = $adService->getSidebarAds($placement, $cityId);
        break;
    case 'sponsored':
        $ads = $adService->getSponsoredListings($cityId);
        break;
    default:
        $ads = $adService->getBannerAds($placement, $cityId);
}

$ads = $ads->take($limit);
@endphp

@if($ads->count() > 0)
    <div class="ads-container ads-{{ $type }} {{ $class }}" data-placement="{{ $placement }}">
        @foreach($ads as $ad)
            <div class="ad-item" 
                 data-ad-id="{{ $ad->id }}" 
                 data-type="{{ $ad->type }}"
                 data-pricing-model="{{ $ad->pricing_model }}">
                
                @if($type === 'hero')
                    {{-- Hero Ad Layout --}}
                    <div class="hero-ad bg-linear-to-r from-blue-600 to-purple-600 text-white rounded-lg overflow-hidden shadow-lg">
                        @if($ad->image_path)
                            <div class="relative h-64 md:h-80">
                                <img src="{{ Storage::url($ad->image_path) }}" 
                                     alt="{{ $ad->title }}" 
                                     class="w-full h-full object-cover">
                                <div class="absolute inset-0 bg-black bg-opacity-40 flex items-center justify-center">
                                    <div class="text-center p-6">
                                        <h2 class="text-3xl md:text-4xl font-bold mb-4">{{ $ad->title }}</h2>
                                        @if($ad->description)
                                            <p class="text-lg mb-6 opacity-90">{{ $ad->description }}</p>
                                        @endif
                                        <button class="ad-click-btn btn btn-primary btn-lg rounded-pill px-4 py-2 shadow-sm fw-bold">
                                            <i class="fas fa-external-link-alt me-2"></i>
                                            اكتشف المزيد
                                        </button>
                                    </div>
                                </div>
                            </div>
                        @else
                            <div class="p-8 text-center">
                                <h2 class="text-3xl md:text-4xl font-bold mb-4">{{ $ad->title }}</h2>
                                @if($ad->description)
                                    <p class="text-lg mb-6 opacity-90">{{ $ad->description }}</p>
                                @endif
                                <button class="ad-click-btn btn btn-primary btn-lg rounded-pill px-4 py-2 shadow-sm fw-bold">
                                    <i class="fas fa-external-link-alt me-2"></i>
                                    اكتشف المزيد
                                </button>
                            </div>
                        @endif
                    </div>

                @elseif($type === 'banner')
                    {{-- Banner Ad Layout --}}
                    <div class="banner-ad bg-white rounded-3 shadow-sm border border-light-subtle overflow-hidden mb-3 hover-shadow-lg transition-all">
                        <div class="d-flex align-items-center">
                            @if($ad->image_path)
                                <div class="flex-shrink-0" style="width: 33.333%;">
                                    <img src="{{ Storage::url($ad->image_path) }}" 
                                         alt="{{ $ad->title }}" 
                                         class="w-100 object-fit-cover"
                                         style="height: 120px;">
                                </div>
                            @endif
                            <div class="flex-fill p-3">
                                <h5 class="fw-bold text-dark mb-1 fs-6">{{ $ad->title }}</h5>
                                @if($ad->description)
                                    <p class="text-muted small mb-2 lh-sm">{{ Str::limit($ad->description, 100) }}</p>
                                @endif
                                <button class="ad-click-btn btn btn-outline-primary btn-sm rounded-pill px-3 py-1">
                                    <i class="fas fa-arrow-left me-1"></i>
                                    عرض التفاصيل
                                </button>
                            </div>
                        </div>
                    </div>

                @elseif($type === 'sidebar')
                    {{-- Sidebar Ad Layout --}}
                    <div class="sidebar-ad bg-light rounded-3 p-3 mb-3 text-center border border-light-subtle shadow-sm">
                        @if($ad->image_path)
                            <img src="{{ Storage::url($ad->image_path) }}" 
                                 alt="{{ $ad->title }}" 
                                 class="w-100 object-fit-cover rounded-2 mb-2"
                                 style="height: 120px;">
                        @endif
                        <h6 class="fw-bold text-dark mb-2 fs-6">{{ $ad->title }}</h6>
                        @if($ad->description)
                            <p class="text-muted small mb-3 lh-sm">{{ Str::limit($ad->description, 80) }}</p>
                        @endif
                        <button class="ad-click-btn btn btn-primary btn-sm rounded-pill px-3 py-2 w-100 fw-bold shadow-sm">
                            <i class="fas fa-external-link-alt me-1"></i>
                            اكتشف الآن
                        </button>
                    </div>

                @elseif($type === 'sponsored')
                    {{-- Sponsored Listing Layout --}}
                    <div class="sponsored-ad bg-warning bg-opacity-10 border border-warning rounded-3 p-3 mb-3 shadow-sm">
                        <div class="d-flex align-items-start justify-content-between mb-2">
                            <span class="badge bg-warning text-dark rounded-pill px-2 py-1 fw-bold">
                                <i class="fas fa-star me-1"></i>
                                إعلان مموّل
                            </span>
                        </div>
                        
                        <div class="d-flex align-items-center">
                            @if($ad->image_path)
                                <div class="flex-shrink-0 me-3">
                                    <img src="{{ Storage::url($ad->image_path) }}" 
                                         alt="{{ $ad->title }}" 
                                         class="rounded-3 object-fit-cover"
                                         style="width: 64px; height: 64px;">
                                </div>
                            @endif
                            <div class="flex-fill">
                                <h5 class="fw-bold text-dark mb-1 fs-6">{{ $ad->title }}</h5>
                                @if($ad->description)
                                    <p class="text-muted mb-2 small">{{ Str::limit($ad->description, 120) }}</p>
                                @endif
                                <button class="ad-click-btn btn btn-outline-primary btn-sm rounded-pill px-3 py-1">
                                    <i class="fas fa-external-link-alt me-1"></i>
                                    زيارة الموقع
                                </button>
                            </div>
                        </div>
                    </div>
                @endif

                {{-- Ad Attribution --}}
                <div class="ad-attribution text-muted small text-center mt-1">
                    <i class="fas fa-ad me-1"></i>
                    إعلان
                </div>
            </div>
        @endforeach
    </div>

    {{-- Ad Tracking Script --}}
    @push('scripts')
    <script>
    document.addEventListener('DOMContentLoaded', function() {
        // Track ad impressions
        document.querySelectorAll('.ad-item').forEach(function(adItem) {
            const adId = adItem.dataset.adId;
            const pricingModel = adItem.dataset.pricingModel;
            
            // Record impression when ad comes into view
            const observer = new IntersectionObserver(function(entries) {
                entries.forEach(function(entry) {
                    if (entry.isIntersecting) {
                        recordImpression(adId);
                        observer.unobserve(entry.target);
                    }
                });
            }, {
                threshold: 0.5 // Ad must be 50% visible
            });
            
            observer.observe(adItem);
            
            // Handle ad clicks
            const clickBtn = adItem.querySelector('.ad-click-btn');
            if (clickBtn) {
                clickBtn.addEventListener('click', function(e) {
                    e.preventDefault();
                    recordClick(adId);
                });
            }
        });
        
        function recordImpression(adId) {
            fetch('/api/ads/impression', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ ad_id: adId })
            });
        }
        
        function recordClick(adId) {
            fetch('/api/ads/click', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({ ad_id: adId })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.redirect_url) {
                    window.open(data.redirect_url, '_blank');
                }
            });
        }
    });
    </script>
    @endpush
@else
    {{-- No Ads Available - Show Placeholder --}}
    <div class="no-ads-placeholder bg-gradient-primary text-white rounded-3 p-4 text-center {{ $class }}" 
         style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="mb-3">
            <i class="fas fa-ad fa-3x opacity-75"></i>
        </div>
        <h6 class="fw-bold mb-2">يمكنك الآن حجز هذا الإعلان لك</h6>
        <p class="small mb-3 opacity-90">احجز مساحتك الإعلانية وصل لآلاف العملاء المحتملين</p>
        <a href="https://wa.me/201060863230?text=مرحباً، أريد الاستفسار عن حجز مساحة إعلانية" 
           target="_blank"
           class="btn btn-light btn-sm rounded-pill px-4 fw-bold">
            <i class="fab fa-whatsapp me-2"></i>
            تواصل واتساب
        </a>
    </div>
@endif