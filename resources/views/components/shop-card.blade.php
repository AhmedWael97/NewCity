@props(['shop', 'loop' => null, 'city' => null, 'cityName' => ''])

<div class="enhanced-shop-card">
    <div class="shop-image-container">
        @php
            $images = $shop->images_array ?? [];
            $hasImages = is_array($images) && count($images) > 0;
        @endphp
        
        @if($hasImages)
            <img src="{{ $images[0] }}" 
                 alt="{{ $shop->name }}" 
                 class="shop-image"
                 style="object-fit: cover; width: 100%; height: 100%;"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            <div class="shop-image-placeholder style-{{ ($loop?->index ?? rand(1, 4)) % 4 + 1 }}" style="display: none;">
                <div class="placeholder-icon">
                    @switch($shop->category->name ?? 'عام')
                        @case('مطاعم')
                            🍽️
                            @break
                        @case('ملابس')
                            👕
                            @break
                        @case('إلكترونيات')
                            📱
                            @break
                        @case('صيدليات')
                            💊
                            @break
                        @case('سوبر ماركت')
                            🛒
                            @break
                        @case('مجوهرات')
                            💎
                            @break
                        @case('مخابز')
                            🍞
                            @break
                        @case('كافيهات')
                            ☕
                            @break
                        @case('مكتبات')
                            📚
                            @break
                        @case('صالونات')
                            ✂️
                            @break
                        @case('ورش سيارات')
                            🔧
                            @break
                        @case('أدوات منزلية')
                            🏠
                            @break
                        @case('ألعاب أطفال')
                            🧸
                            @break
                        @case('حدائق ونباتات')
                            🌱
                            @break
                        @case('أدوات رياضية')
                            ⚽
                            @break
                        @case('مراكز طبية')
                            🏥
                            @break
                        @case('مراكز تعليمية')
                            📖
                            @break
                        @case('خدمات مالية')
                            💰
                            @break
                        @case('عقارات')
                            🏢
                            @break
                        @case('خدمات أمان')
                            🛡️
                            @break
                        @case('خدمات صحية')
                            🩺
                            @break
                        @case('مواد غذائية')
                            🍎
                            @break
                        @case('لحوم ودواجن')
                            🥩
                            @break
                        @case('حلويات')
                            🍰
                            @break
                        @case('عصائر وآيس كريم')
                            🧊
                            @break
                        @case('أسماك وبحريات')
                            🐟
                            @break
                        @case('توابل ومخللات')
                            🌶️
                            @break
                        @case('فواكه وخضروات')
                            🥬
                            @break
                        @case('ألبان ومنتجاتها')
                            🥛
                            @break
                        @case('أدوات مطبخ')
                            🍳
                            @break
                        @case('أثاث ومفروشات')
                            🪑
                            @break
                        @case('ديكورات')
                            🖼️
                            @break
                        @case('إضاءة')
                            💡
                            @break
                        @case('ستائر ومفارش')
                            🪟
                            @break
                        @case('أدوات تنظيف')
                            🧽
                            @break
                        @case('حيوانات أليفة')
                            🐾
                            @break
                        @case('أدوات كهربائية')
                            🔌
                            @break
                        @case('هواتف واكسسوارات')
                            📱
                            @break
                        @case('كمبيوتر ولابتوب')
                            💻
                            @break
                        @case('ألعاب إلكترونية')
                            🎮
                            @break
                        @case('كاميرات وتصوير')
                            📷
                            @break
                        @case('أجهزة صوتية')
                            🎧
                            @break
                        @case('ساعات')
                            ⌚
                            @break
                        @case('أحذية')
                            👞
                            @break
                        @case('حقائب')
                            👜
                            @break
                        @case('نظارات')
                            👓
                            @break
                        @case('عطور ومكياج')
                            💄
                            @break
                        @case('منتجات شعر')
                            💇‍♀️
                            @break
                        @case('منتجات عناية')
                            🧴
                            @break
                        @case('أدوية')
                            💊
                            @break
                        @case('معدات طبية')
                            🩹
                            @break
                        @case('مكملات غذائية')
                            💉
                            @break
                        @case('وسائل نقل')
                            🚗
                            @break
                        @case('قطع غيار')
                            🔩
                            @break
                        @case('زيوت ومواد تشحيم')
                            🛢️
                            @break
                        @case('إطارات')
                            🛞
                            @break
                        @case('اكسسوارات سيارات')
                            🚙
                            @break
                        @case('خدمات سفر')
                            ✈️
                            @break
                        @case('فنادق وإقامة')
                            🏨
                            @break
                        @case('مطاعم فاخرة')
                            🍷
                            @break
                        @case('مطاعم شعبية')
                            🍲
                            @break
                        @case('وجبات سريعة')
                            🍔
                            @break
                        @case('مأكولات بحرية')
                            🦐
                            @break
                        @case('حلال')
                            🥩
                            @break
                        @case('نباتي')
                            🥗
                            @break
                        @default
                            🏪
                    @endswitch
                </div>
                <span class="placeholder-text">{{ $shop->name }}</span>
            </div>
        @else
            <div class="shop-image-placeholder style-{{ ($loop?->index ?? rand(1, 4)) % 4 + 1 }}">
                <div class="placeholder-icon">
                    @switch($shop->category->name ?? 'عام')
                        @case('مطاعم')
                            🍽️
                            @break
                        @case('ملابس')
                            👕
                            @break
                        @case('إلكترونيات')
                            📱
                            @break
                        @case('صيدليات')
                            💊
                            @break
                        @case('سوبر ماركت')
                            🛒
                            @break
                        @case('مجوهرات')
                            💎
                            @break
                        @case('مخابز')
                            🍞
                            @break
                        @case('كافيهات')
                            ☕
                            @break
                        @case('مكتبات')
                            📚
                            @break
                        @case('صالونات')
                            ✂️
                            @break
                        @case('ورش سيارات')
                            🔧
                            @break
                        @case('أدوات منزلية')
                            🏠
                            @break
                        @case('ألعاب أطفال')
                            🧸
                            @break
                        @case('حدائق ونباتات')
                            🌱
                            @break
                        @case('أدوات رياضية')
                            ⚽
                            @break
                        @case('مراكز طبية')
                            🏥
                            @break
                        @case('مراكز تعليمية')
                            📖
                            @break
                        @case('خدمات مالية')
                            💰
                            @break
                        @case('عقارات')
                            🏢
                            @break
                        @case('خدمات أمان')
                            🛡️
                            @break
                        @case('خدمات صحية')
                            🩺
                            @break
                        @case('مواد غذائية')
                            🍎
                            @break
                        @case('لحوم ودواجن')
                            🥩
                            @break
                        @case('حلويات')
                            🍰
                            @break
                        @case('عصائر وآيس كريم')
                            🧊
                            @break
                        @case('أسماك وبحريات')
                            🐟
                            @break
                        @case('توابل ومخللات')
                            🌶️
                            @break
                        @case('فواكه وخضروات')
                            🥬
                            @break
                        @case('ألبان ومنتجاتها')
                            🥛
                            @break
                        @case('أدوات مطبخ')
                            🍳
                            @break
                        @case('أثاث ومفروشات')
                            🪑
                            @break
                        @case('ديكورات')
                            🖼️
                            @break
                        @case('إضاءة')
                            💡
                            @break
                        @case('ستائر ومفارش')
                            🪟
                            @break
                        @case('أدوات تنظيف')
                            🧽
                            @break
                        @case('حيوانات أليفة')
                            🐾
                            @break
                        @case('أدوات كهربائية')
                            🔌
                            @break
                        @case('هواتف واكسسوارات')
                            📱
                            @break
                        @case('كمبيوتر ولابتوب')
                            💻
                            @break
                        @case('ألعاب إلكترونية')
                            🎮
                            @break
                        @case('كاميرات وتصوير')
                            📷
                            @break
                        @case('أجهزة صوتية')
                            🎧
                            @break
                        @case('ساعات')
                            ⌚
                            @break
                        @case('أحذية')
                            👞
                            @break
                        @case('حقائب')
                            👜
                            @break
                        @case('نظارات')
                            👓
                            @break
                        @case('عطور ومكياج')
                            💄
                            @break
                        @case('منتجات شعر')
                            💇‍♀️
                            @break
                        @case('منتجات عناية')
                            🧴
                            @break
                        @case('أدوية')
                            💊
                            @break
                        @case('معدات طبية')
                            🩹
                            @break
                        @case('مكملات غذائية')
                            💉
                            @break
                        @case('وسائل نقل')
                            🚗
                            @break
                        @case('قطع غيار')
                            🔩
                            @break
                        @case('زيوت ومواد تشحيم')
                            🛢️
                            @break
                        @case('إطارات')
                            🛞
                            @break
                        @case('اكسسوارات سيارات')
                            🚙
                            @break
                        @case('خدمات سفر')
                            ✈️
                            @break
                        @case('فنادق وإقامة')
                            🏨
                            @break
                        @case('مطاعم فاخرة')
                            🍷
                            @break
                        @case('مطاعم شعبية')
                            🍲
                            @break
                        @case('وجبات سريعة')
                            🍔
                            @break
                        @case('مأكولات بحرية')
                            🦐
                            @break
                        @case('حلال')
                            🥩
                            @break
                        @case('نباتي')
                            🥗
                            @break
                        @default
                            🏪
                    @endswitch
                </div>
                <span class="placeholder-text">{{ $shop->name }}</span>
            </div>
        @endif
        
        <div class="shop-badge">
            <span class="badge-text">{{ $shop->category?->name ?? 'عam' }}</span>
        </div>
        
    </div>
    
    <div class="shop-card-content">
        <div class="shop-header">
            <h3 class="shop-name" style="font-size:14px; font-weight: bold">
                {{ $shop->name }}
               
            </h3>
           
        </div>
        
        <div class="shop-details shop-details-desktop">
            <div class="detail-item" style="margin-bottom: 15px">  
                <div class="shop-rating" >
                <x-rating 
                    :rating="$shop->rating ?? 4.5" 
                    :review-count="$shop->review_count ?? rand(10, 150)"
                    :show-count="true"
                    size="sm"
                />
            </div></div>
            <div class="detail-item">
                <i class="detail-icon">📍</i>
                <span>{{ Str::limit($shop->address ?? ($city?->name ?? $shop->city?->name ?? 'غير محدد'), 30) }}</span>
            </div>
            
            @if($shop->phone)
            <div class="detail-item">
                <i class="detail-icon">📞</i>
                <span dir="ltr">{{ $shop->phone }}</span>
            </div>
            @endif
        </div>
        
        <div class="shop-footer">
            <div class="shop-actions-mini">
                @if($shop->phone)
                <a href="tel:{{ $shop->phone }}" class="action-mini call" title="اتصال">
                    <i class="icon">📞</i>
                </a>
                @endif
                <button class="action-mini directions" onclick="getDirections({{ $shop->latitude ?? 'null' }}, {{ $shop->longitude ?? 'null' }}, {{ json_encode(str_replace(["\r", "\n"], ' ', $shop->address ?? '')) }})" title="الاتجاهات">
                    <i class="icon">🧭</i>
                </button>
                <a href="{{ route('shop.show', $shop->slug) }}" class="action-mini view" title="عرض التفاصيل">
                    <i class="icon">👁️</i>
                </a>
            </div>
        </div>
        
        <a href="{{ route('shop.show', $shop->slug) }}" class="card-overlay-link"></a>
    </div>
</div>