@extends('layouts.app')

@section('title', $seoData['title'] ?? "نتائج البحث في {$city->name}")
@section('description', $seoData['description'] ?? "نتائج البحث في {$city->name}")

@section('content')
<div class="container py-5">
    <!-- Search Header -->
    <div class="mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item"><a href="{{ route('home') }}">الرئيسية</a></li>
                <li class="breadcrumb-item"><a href="{{ route('city.landing', $city->slug) }}">{{ $city->name }}</a></li>
                <li class="breadcrumb-item active">نتائج البحث</li>
            </ol>
        </nav>

        <div class="d-flex align-items-center justify-content-between mb-3">
            <div>
                <h1 class="h3 mb-2">نتائج البحث عن: "{{ $query }}"</h1>
                <p class="text-muted mb-0">
                    في {{ $city->name }} - 
                    <span class="fw-bold">{{ $shops->total() }}</span> نتيجة
                </p>
            </div>
        </div>

        <!-- Search Form -->
        <form action="{{ route('city.search', $city->slug) }}" method="GET" class="mb-4">
            <div class="input-group input-group-lg">
                <input type="text" 
                       name="q" 
                       class="form-control" 
                       placeholder="ابحث عن متاجر، منتجات، خدمات..."
                       value="{{ $query }}"
                       required>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> بحث
                </button>
            </div>
        </form>
    </div>

    @if($shops->isEmpty())
        <!-- No Results -->
        <div class="text-center py-5">
            <i class="fas fa-search fa-4x text-muted mb-4"></i>
            <h3 class="mb-3">لا توجد نتائج</h3>
            <p class="text-muted mb-4">
                لم نتمكن من العثور على نتائج تطابق بحثك "{{ $query }}" في {{ $city->name }}
            </p>
            <a href="{{ route('city.landing', $city->slug) }}" class="btn btn-primary">
                <i class="fas fa-arrow-right"></i> العودة إلى {{ $city->name }}
            </a>
        </div>
    @else
        <!-- Search Results -->
        <div class="row g-4">
            @foreach($shops as $shop)
                <div class="col-md-6 col-lg-4">
                    <x-shop-card :shop="$shop" :cityName="$city->name" />
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="d-flex justify-content-center mt-5">
            {{ $shops->appends(['q' => $query])->links() }}
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
            </ul>
        </div>
    </div>
</div>

<style>
    .breadcrumb {
        background: transparent;
        padding: 0;
    }
    
    .input-group-lg .form-control {
        border-radius: 0.5rem 0 0 0.5rem;
    }
    
    .input-group-lg .btn {
        border-radius: 0 0.5rem 0.5rem 0;
        padding: 0.5rem 2rem;
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
        window.open(`https://www.google.com/maps/dir/?api=1&destination=${latitude},${longitude}`, '_blank');
    } else if (address) {
        const encodedAddress = encodeURIComponent(address);
        window.open(`https://www.google.com/maps/search/${encodedAddress}`, '_blank');
    } else {
        alert('عذراً، الموقع غير متوفر');
    }
}

async function toggleFavoriteShop(shopId) {
    event.preventDefault();
    event.stopPropagation();
    
    @guest
        alert('يجب تسجيل الدخول لإضافة المتاجر للمفضلة');
        window.location.href = '{{ route("login") }}';
        return;
    @endguest
    
    const btn = event.target.closest('.favorite-btn, .favorite-btn-small');
    const icon = btn ? btn.querySelector('.heart-icon') : event.target;
    const isFavorite = icon.textContent.includes('❤️');
    
    if (btn) btn.disabled = true;
    
    try {
        const response = await fetch(`/favorites/shops/${shopId}`, {
            method: isFavorite ? 'DELETE' : 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        });
        
        const data = await response.json();
        
        if (response.ok && data.success) {
            icon.textContent = isFavorite ? '🤍' : '❤️';
            alert(data.message);
        } else {
            alert(data.message || 'حدث خطأ ما');
        }
    } catch (error) {
        console.error('Favorite toggle error:', error);
        alert('حدث خطأ في الاتصال');
    } finally {
        if (btn) btn.disabled = false;
    }
}
</script>
@endsection
