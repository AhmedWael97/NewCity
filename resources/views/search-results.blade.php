@extends('layouts.app')

@section('title', $seoData['title'] ?? "نتائج البحث")
@section('description', $seoData['description'] ?? "نتائج البحث")

@section('content')
<div class="container py-5">
    <!-- Search Header -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a></li>
                <li class="breadcrumb-item active">نتائج البحث</li>
            </ol>
        </nav>

        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h1 class="h3 mb-2">
                    @if(!empty($query))
                        نتائج البحث عن: "{{ $query }}"
                    @else
                        البحث في المتاجر
                    @endif
                </h1>
                <p class="text-muted mb-0">
                    @if($stats['city_filter'])
                        في {{ $stats['city_filter'] }} - 
                    @endif
                    <span class="fw-bold">{{ number_format($stats['total_results']) }}</span> نتيجة
                    @if($stats['category_filter'])
                        - فئة: {{ $stats['category_filter'] }}
                    @endif
                </p>
            </div>
        </div>

        <!-- Search Form -->
        <form action="{{ route('search') }}" method="GET" class="mb-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <input type="text" 
                           name="q" 
                           class="form-control form-control-lg" 
                           placeholder="ابحث عن متاجر، منتجات، خدمات..."
                           value="{{ $query }}"
                           required>
                </div>
                <div class="col-md-3">
                    <select name="city" class="form-select form-select-lg">
                        <option value="">كل المدن</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ $cityId == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <select name="category" class="form-select form-select-lg">
                        <option value="">كل الفئات</option>
                        @foreach(['مطاعم', 'ملابس', 'إلكترونيات', 'صيدليات', 'سوبر ماركت', 'مقاهي'] as $cat)
                            <option value="{{ $cat }}" {{ $category == $cat ? 'selected' : '' }}>
                                {{ $cat }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-1">
                    <button type="submit" class="btn btn-primary btn-lg w-100">
                        🔍
                    </button>
                </div>
            </div>
        </form>
    </div>

    @if($results->isEmpty())
        <!-- No Results -->
        <div class="text-center py-5">
            <i class="fas fa-search fa-4x text-muted mb-4"></i>
            <h3 class="mb-3">لا توجد نتائج</h3>
            <p class="text-muted mb-4">
                @if(!empty($query))
                    لم نتمكن من العثور على نتائج تطابق بحثك "{{ $query }}"
                @else
                    لم نتمكن من العثور على أي نتائج
                @endif
            </p>
            <a href="{{ route('home') }}" class="btn btn-primary">
                <i class="fas fa-arrow-right"></i> العودة إلى الرئيسية
            </a>
        </div>
    @else
        <!-- Search Results -->
        <div class="row g-4">
            @foreach($results as $shop)
                <div class="col-md-6 col-lg-4">
                    <x-shop-card :shop="$shop" />
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-5">
            {{ $results->appends(['q' => $query, 'city' => $cityId, 'category' => $category])->links() }}
        </div>
    @endif

    <!-- Search Tips -->
    <div class="card mt-5 border-0 bg-light">
        <div class="card-body">
            <h5 class="card-title">
                <i class="fas fa-lightbulb text-warning"></i> نصائح للبحث
            </h5>
            <ul class="mb-0">
                <li>استخدم كلمات مفتاحية بسيطة وواضحة</li>
                <li>جرب كلمات مختلفة أو مرادفات</li>
                <li>تحقق من التهجئة الصحيحة للكلمات</li>
                <li>استخدم كلمات عامة للحصول على نتائج أكثر</li>
                <li>استخدم فلاتر المدينة والفئة لتحسين النتائج</li>
            </ul>
        </div>
    </div>
</div>

<style>
    .breadcrumb {
        background: transparent;
        padding: 0;
    }
    
    .shop-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    
    .shop-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
    }
</style>

<script>
// Get Directions using coordinates or address
function getDirections(latitude, longitude, address) {
    event.preventDefault();
    event.stopPropagation();
    
    if (latitude && longitude) {
        // Use coordinates for precise location
        window.open(`https://www.google.com/maps/dir/?api=1&destination=${latitude},${longitude}`, '_blank');
    } else if (address) {
        // Fallback to address search
        const encodedAddress = encodeURIComponent(address);
        window.open(`https://www.google.com/maps/search/${encodedAddress}`, '_blank');
    } else {
        alert('عذراً، الموقع غير متوفر');
    }
}

// Toggle Favorite Shop
function toggleFavoriteShop(shopId) {
    event.preventDefault();
    event.stopPropagation();
    console.log('Toggle favorite for shop:', shopId);
    // Add your favorite toggle logic here
}
</script>
@endsection
