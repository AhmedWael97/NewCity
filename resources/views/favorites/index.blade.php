@extends('layouts.app')

@section('title', 'المفضلة - متاجري المحفوظة')

@section('content')
<div class="container py-5">
    <div class="row">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h1 class="h3 mb-0">
                    <i class="fas fa-heart text-danger"></i>
                    متاجري المفضلة
                </h1>
                <span class="badge badge-primary badge-pill">{{ $favorites->total() }} متجر</span>
            </div>

            @if($favorites->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-heart-broken fa-5x text-muted mb-4"></i>
                    <h3 class="text-muted">لا توجد متاجر في المفضلة</h3>
                    <p class="text-muted">ابدأ بإضافة المتاجر التي تحبها إلى قائمة المفضلة</p>
                    <a href="{{ route('home') }}" class="btn btn-primary mt-3">
                        <i class="fas fa-search"></i> استكشف المتاجر
                    </a>
                </div>
            @else
                <div class="row">
                    @foreach($favorites as $shop)
                        <div class="col-md-6 col-lg-4 mb-4">
                            <div class="card h-100 shop-card">
                                @if($shop->images && count($shop->images) > 0)
                                    <img src="{{ $shop->images[0] }}" class="card-img-top" alt="{{ $shop->name }}" style="height: 200px; object-fit: cover;">
                                @else
                                    <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                        <i class="fas fa-store fa-3x text-muted"></i>
                                    </div>
                                @endif

                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="card-title mb-0">
                                            <a href="{{ route('city.shop.show', ['city' => $shop->city->slug ?? 'city', 'shop' => $shop->slug]) }}" class="text-dark text-decoration-none">
                                                {{ $shop->name }}
                                            </a>
                                        </h5>
                                        <button class="btn btn-sm btn-link text-danger p-0 favorite-btn" data-shop-id="{{ $shop->id }}" onclick="toggleFavoriteShop({{ $shop->id }}, event)">
                                            <span class="heart-icon">❤️</span>
                                        </button>
                                    </div>

                                    <p class="card-text text-muted small mb-2">
                                        <i class="fas fa-map-marker-alt"></i> {{ $shop->city->name ?? 'غير محدد' }}
                                        @if($shop->category)
                                            • <i class="fas fa-tag"></i> {{ $shop->category->name }}
                                        @endif
                                    </p>

                                    @if($shop->rating > 0)
                                        <div class="mb-2">
                                            <span class="text-warning">
                                                @for($i = 1; $i <= 5; $i++)
                                                    @if($i <= floor($shop->rating))
                                                        <i class="fas fa-star"></i>
                                                    @elseif($i - 0.5 <= $shop->rating)
                                                        <i class="fas fa-star-half-alt"></i>
                                                    @else
                                                        <i class="far fa-star"></i>
                                                    @endif
                                                @endfor
                                            </span>
                                            <span class="text-muted small">({{ $shop->ratings_count }} تقييم)</span>
                                        </div>
                                    @endif

                                    @if($shop->description)
                                        <p class="card-text text-muted small">{{ Str::limit($shop->description, 100) }}</p>
                                    @endif

                                    <div class="mt-3">
                                        <a href="{{ route('city.shop.show', ['city' => $shop->city->slug ?? 'city', 'shop' => $shop->slug]) }}" class="btn btn-primary btn-sm btn-block">
                                            <i class="fas fa-eye"></i> عرض التفاصيل
                                        </a>
                                    </div>
                                </div>

                                @if($shop->is_verified)
                                    <div class="card-footer bg-light">
                                        <small class="text-success">
                                            <i class="fas fa-check-circle"></i> متجر موثق
                                        </small>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $favorites->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<style>
.shop-card {
    transition: transform 0.2s, box-shadow 0.2s;
}

.shop-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

.favorite-btn .heart-icon {
    font-size: 1.5rem;
    cursor: pointer;
    transition: transform 0.2s;
}

.favorite-btn:hover .heart-icon {
    transform: scale(1.2);
}
</style>
@endsection
