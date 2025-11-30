@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-lg-3">
            <!-- Filters Sidebar -->
            <div class="card shadow-sm mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-filter"></i> البحث والتصفية</h5>
                </div>
                <div class="card-body">
                    <form method="GET" action="{{ route('marketplace.index') }}">
                        <!-- Search -->
                        <div class="mb-3">
                            <label class="form-label">بحث</label>
                            <input type="text" name="search" class="form-control" placeholder="ابحث عن منتج..." value="{{ request('search') }}">
                        </div>

                        <!-- City -->
                        <div class="mb-3">
                            <label class="form-label">المدينة</label>
                            <select name="city_id" class="form-select">
                                <option value="">كل المدن</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>
                                        {{ $city->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Category -->
                        <div class="mb-3">
                            <label class="form-label">الفئة</label>
                            <select name="category_id" class="form-select">
                                <option value="">كل الفئات</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Price Range -->
                        <div class="mb-3">
                            <label class="form-label">السعر</label>
                            <div class="row g-2">
                                <div class="col-6">
                                    <input type="number" name="min_price" class="form-control" placeholder="من" value="{{ request('min_price') }}">
                                </div>
                                <div class="col-6">
                                    <input type="number" name="max_price" class="form-control" placeholder="إلى" value="{{ request('max_price') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Condition -->
                        <div class="mb-3">
                            <label class="form-label">الحالة</label>
                            <select name="condition" class="form-select">
                                <option value="">كل الحالات</option>
                                <option value="new" {{ request('condition') == 'new' ? 'selected' : '' }}>جديد</option>
                                <option value="like_new" {{ request('condition') == 'like_new' ? 'selected' : '' }}>مثل الجديد</option>
                                <option value="good" {{ request('condition') == 'good' ? 'selected' : '' }}>جيد</option>
                                <option value="fair" {{ request('condition') == 'fair' ? 'selected' : '' }}>مقبول</option>
                            </select>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-search"></i> بحث
                        </button>
                        <a href="{{ route('marketplace.index') }}" class="btn btn-outline-secondary w-100 mt-2">
                            <i class="fas fa-redo"></i> إعادة تعيين
                        </a>
                    </form>
                </div>
            </div>

            @auth
            <div class="card shadow-sm">
                <div class="card-body text-center">
                    <i class="fas fa-plus-circle fa-3x text-primary mb-3"></i>
                    <h5>لديك شيء للبيع؟</h5>
                    <p class="text-muted">أضف إعلانك الآن وابدأ البيع</p>
                    <a href="{{ route('marketplace.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> إضافة إعلان
                    </a>
                </div>
            </div>
            @endauth
        </div>

        <div class="col-lg-9">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2><i class="fas fa-store"></i> السوق</h2>
                    <p class="text-muted mb-0">وجدنا {{ $items->total() }} إعلان</p>
                </div>
                @auth
                <div>
                    <a href="{{ route('marketplace.my-items') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list"></i> إعلاناتي
                    </a>
                    <a href="{{ route('marketplace.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> إضافة إعلان
                    </a>
                </div>
                @else
                <div>
                    <a href="{{ route('login') }}" class="btn btn-primary">
                        <i class="fas fa-sign-in-alt"></i> تسجيل الدخول للإضافة
                    </a>
                </div>
                @endauth
            </div>

            @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            <!-- Items Grid -->
            @if($items->count() > 0)
            <div class="row g-4">
                @foreach($items as $item)
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 shadow-sm marketplace-item-card">
                        @if($item->is_sponsored && $item->sponsored_until > now())
                        <div class="sponsored-badge">
                            <i class="fas fa-star"></i> مميز
                        </div>
                        @endif

                        <!-- Image -->
                        <div class="marketplace-item-image">
                            @if($item->images && count($item->images) > 0)
                            <img src="{{ $item->images[0] }}" alt="{{ $item->title }}" class="card-img-top">
                            @else
                            <div class="no-image d-flex align-items-center justify-content-center bg-light">
                                <i class="fas fa-image fa-3x text-muted"></i>
                            </div>
                            @endif
                            
                            <div class="condition-badge badge-{{ $item->condition }}">
                                @switch($item->condition)
                                    @case('new') جديد @break
                                    @case('like_new') مثل الجديد @break
                                    @case('good') جيد @break
                                    @case('fair') مقبول @break
                                @endswitch
                            </div>
                        </div>

                        <div class="card-body">
                            <h5 class="card-title">{{ Str::limit($item->title, 50) }}</h5>
                            <p class="card-text text-muted small">
                                {{ Str::limit($item->description, 80) }}
                            </p>
                            
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div class="price">
                                    <h4 class="text-primary mb-0">{{ number_format($item->price, 0) }} جنيه</h4>
                                    @if($item->is_negotiable)
                                    <small class="text-muted">قابل للتفاوض</small>
                                    @endif
                                </div>
                            </div>

                            <div class="item-meta small text-muted mb-3">
                                <div><i class="fas fa-map-marker-alt"></i> {{ $item->city->name }}</div>
                                <div><i class="fas fa-tag"></i> {{ $item->category->name }}</div>
                                <div><i class="fas fa-eye"></i> {{ $item->view_count }} مشاهدة</div>
                            </div>

                            <a href="{{ route('marketplace.show', $item->id) }}" class="btn btn-primary w-100">
                                <i class="fas fa-eye"></i> عرض التفاصيل
                            </a>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $items->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-5x text-muted mb-3"></i>
                <h4>لا توجد إعلانات</h4>
                <p class="text-muted">جرب تغيير معايير البحث</p>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
.marketplace-item-card {
    transition: transform 0.2s, box-shadow 0.2s;
    position: relative;
}

.marketplace-item-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
}

.marketplace-item-image {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.marketplace-item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.no-image {
    height: 100%;
}

.sponsored-badge {
    position: absolute;
    top: 10px;
    right: 10px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 5px 15px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: bold;
    z-index: 1;
    box-shadow: 0 2px 10px rgba(102, 126, 234, 0.5);
}

.condition-badge {
    position: absolute;
    bottom: 10px;
    left: 10px;
    padding: 5px 10px;
    border-radius: 5px;
    font-size: 11px;
    font-weight: bold;
    z-index: 1;
}

.badge-new {
    background-color: #28a745;
    color: white;
}

.badge-like_new {
    background-color: #17a2b8;
    color: white;
}

.badge-good {
    background-color: #ffc107;
    color: #000;
}

.badge-fair {
    background-color: #6c757d;
    color: white;
}

.item-meta > div {
    margin-bottom: 5px;
}

.item-meta i {
    width: 16px;
}
</style>
@endsection
