@extends('layouts.app')

@section('content')
<!-- Hero Section -->
<div class="marketplace-hero">
    <div class="container">
        <div class="hero-content">
            <h1 class="hero-title">
                <i class="fas fa-store-alt"></i>
                السوق المفتوح
            </h1>
            
        </div>
    </div>
</div>

<div class="container-fluid my-5">
    <!-- Mobile Filter Toggle Button -->
    <button class="mobile-filter-toggle" id="mobileFilterBtn">
        <i class="fas fa-filter"></i>
        <span class="filter-badge">{{ collect(request()->only(['search', 'city_id', 'category_id', 'min_price', 'max_price', 'condition']))->filter()->count() }}</span>
    </button>

    <div class="row">
        <div class="col-lg-3">
            <!-- Filters Sidebar -->
            <div class="filter-sidebar" id="filterSidebar">
                <div class="filter-header">
                    <div>
                        <i class="fas fa-filter"></i> البحث والتصفية
                    </div>
                    <button class="filter-close-btn" id="closeFilterBtn">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
                <div class="filter-body">
                    <form method="GET" action="{{ route('marketplace.index') }}" class="filter-form">
                        <!-- Search -->
                        <div class="filter-group">
                            <label class="filter-label"><i class="fas fa-search"></i> بحث</label>
                            <input type="text" name="search" class="filter-input" placeholder="ابحث عن منتج..." value="{{ request('search') }}">
                        </div>

                        <!-- City -->
                        <div class="filter-group">
                            <label class="filter-label"><i class="fas fa-map-marker-alt"></i> المدينة</label>
                            <select name="city_id" class="filter-select">
                                <option value="">كل المدن</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>
                                        {{ $city->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Category -->
                        <div class="filter-group">
                            <label class="filter-label"><i class="fas fa-tag"></i> الفئة</label>
                            <select name="category_id" class="filter-select">
                                <option value="">كل الفئات</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Price Range -->
                        <div class="filter-group">
                            <label class="filter-label"><i class="fas fa-dollar-sign"></i> السعر</label>
                            <div class="price-inputs">
                                <input type="number" name="min_price" class="filter-input" placeholder="من" value="{{ request('min_price') }}">
                                <span class="price-separator">-</span>
                                <input type="number" name="max_price" class="filter-input" placeholder="إلى" value="{{ request('max_price') }}">
                            </div>
                        </div>

                        <!-- Condition -->
                        <div class="filter-group">
                            <label class="filter-label"><i class="fas fa-box"></i> الحالة</label>
                            <select name="condition" class="filter-select">
                                <option value="">كل الحالات</option>
                                <option value="new" {{ request('condition') == 'new' ? 'selected' : '' }}>🆕 جديد</option>
                                <option value="like_new" {{ request('condition') == 'like_new' ? 'selected' : '' }}>✨ مثل الجديد</option>
                                <option value="good" {{ request('condition') == 'good' ? 'selected' : '' }}>👍 جيد</option>
                                <option value="fair" {{ request('condition') == 'fair' ? 'selected' : '' }}>⭐ مقبول</option>
                            </select>
                        </div>

                        <button type="submit" class="filter-submit-btn">
                            <i class="fas fa-search"></i> بحث
                        </button>
                        <a href="{{ route('marketplace.index') }}" class="filter-reset-btn">
                            <i class="fas fa-redo"></i> إعادة تعيين
                        </a>
                    </form>
                </div>
            </div>

            @auth
            <div class="add-item-card">
                <div class="add-item-icon">
                    <i class="fas fa-plus-circle"></i>
                </div>
                <h5 class="add-item-title">لديك شيء للبيع؟</h5>
                <p class="add-item-text">أضف إعلانك الآن وابدأ البيع</p>
                <a href="{{ route('marketplace.create') }}" class="add-item-btn">
                    <i class="fas fa-plus"></i> إضافة إعلان
                </a>
            </div>
            @endauth
        </div>

        <div class="col-lg-9">
            <!-- Header -->
            <div class="marketplace-header">
                <div class="header-left">
                    <h2 class="header-title">
                        <i class="fas fa-boxes"></i> جميع الإعلانات
                    </h2>
                    <p class="header-count">وجدنا <strong>{{ $items->total() }}</strong> إعلان</p>
                </div>
                <div class="header-actions">
                    @auth
                        <a href="{{ route('marketplace.my-items') }}" class="action-btn secondary">
                            <i class="fas fa-list"></i>
                            <span>إعلاناتي</span>
                        </a>
                        <a href="{{ route('marketplace.create') }}" class="action-btn primary">
                            <i class="fas fa-plus"></i>
                            <span>إضافة إعلان</span>
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="action-btn primary">
                            <i class="fas fa-sign-in-alt"></i>
                            <span>تسجيل الدخول</span>
                        </a>
                    @endauth
                </div>
            </div>

            @if(session('success'))
            <div class="success-alert">
                <i class="fas fa-check-circle"></i>
                <span>{{ session('success') }}</span>
                <button type="button" class="alert-close" onclick="this.parentElement.remove()">×</button>
            </div>
            @endif

            <!-- Items Grid -->
            @if($items->count() > 0)
            <div class="marketplace-grid">
                @foreach($items as $item)
                <div class="marketplace-item">
                    <a href="{{ route('marketplace.show', $item->slug) }}" class="item-link">
                        @if($item->is_sponsored && $item->sponsored_until > now())
                        <div class="sponsored-badge">
                            <i class="fas fa-star"></i> مميز
                        </div>
                        @endif

                        <!-- Image -->
                        <div class="item-image">
                            @if($item->images && count($item->images) > 0)
                            <img src="{{ $item->images[0] }}" alt="{{ $item->title }}" loading="lazy">
                            @else
                            <div class="no-image">
                                <i class="fas fa-image"></i>
                            </div>
                            @endif
                            
                            <div class="condition-badge condition-{{ $item->condition }}">
                                @switch($item->condition)
                                    @case('new') 🆕 جديد @break
                                    @case('like_new') ✨ شبه جديد @break
                                    @case('good') 👍 جيد @break
                                    @case('fair') ⭐ مقبول @break
                                @endswitch
                            </div>
                        </div>

                        <div class="item-body">
                            <h3 class="item-title">{{ Str::limit($item->title, 45) }}</h3>
                            <p class="item-description">
                                {{ Str::limit($item->description, 70) }}
                            </p>
                            
                            <div class="item-price-row">
                                <div class="item-price">
                                    <span class="price-amount">{{ number_format($item->price, 0) }}</span>
                                    <span class="price-currency">جنيه</span>
                                    @if($item->is_negotiable)
                                    <span class="price-negotiable">قابل للتفاوض</span>
                                    @endif
                                </div>
                            </div>

                            <div class="item-meta">
                                <div class="meta-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>{{ $item->city->name }}</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-tag"></i>
                                    <span>{{ $item->category->name }}</span>
                                </div>
                                <div class="meta-item">
                                    <i class="fas fa-eye"></i>
                                    <span>{{ $item->view_count }}</span>
                                </div>
                            </div>

                            <button class="item-view-btn">
                                <i class="fas fa-arrow-left"></i>
                                <span>عرض التفاصيل</span>
                            </button>
                        </div>
                    </a>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="marketplace-pagination">
                {{ $items->links() }}
            </div>
            @else
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-inbox"></i>
                </div>
                <h3 class="empty-title">لا توجد إعلانات</h3>
                <p class="empty-text">جرب تغيير معايير البحث أو تصفح جميع الفئات</p>
                <a href="{{ route('marketplace.index') }}" class="empty-btn">
                    <i class="fas fa-refresh"></i> تصفح جميع الإعلانات
                </a>
            </div>
            @endif
        </div>
    </div>
</div>

<style>
/* Hero Section */
.marketplace-hero {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 60px 0;
    margin-bottom: 40px;
    position: relative;
    overflow: hidden;
}

.marketplace-hero::before {
    content: '';
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: url("data:image/svg+xml,%3Csvg width='60' height='60' viewBox='0 0 60 60' xmlns='http://www.w3.org/2000/svg'%3E%3Cg fill='none' fill-rule='evenodd'%3E%3Cg fill='%23ffffff' fill-opacity='0.05'%3E%3Cpath d='M36 34v-4h-2v4h-4v2h4v4h2v-4h4v-2h-4zm0-30V0h-2v4h-4v2h4v4h2V6h4V4h-4zM6 34v-4H4v4H0v2h4v4h2v-4h4v-2H6zM6 4V0H4v4H0v2h4v4h2V6h4V4H6z'/%3E%3C/g%3E%3C/g%3E%3C/svg%3E");
    opacity: 0.1;
}

.hero-content {
    position: relative;
    z-index: 1;
    text-align: center;
}

.hero-title {
    font-size: 3rem;
    font-weight: 800;
    margin-bottom: 15px;
    text-shadow: 0 2px 20px rgba(0, 0, 0, 0.2);
}

.hero-title i {
    margin-left: 15px;
}

.hero-subtitle {
    font-size: 1.3rem;
    opacity: 0.95;
    margin-bottom: 40px;
    font-weight: 300;
}

.hero-stats {
    display: flex;
    justify-content: center;
    gap: 60px;
    flex-wrap: wrap;
}

.stat-item {
    text-align: center;
}

.stat-number {
    font-size: 2.5rem;
    font-weight: 800;
    margin-bottom: 5px;
    text-shadow: 0 2px 10px rgba(0, 0, 0, 0.2);
}

.stat-label {
    font-size: 1rem;
    opacity: 0.9;
    font-weight: 500;
}

/* Mobile Filter Toggle Button */
.mobile-filter-toggle {
    display: none;
    position: fixed;
    bottom: 90px;
    right: 30px;
    width: 60px;
    height: 60px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border: none;
    border-radius: 50%;
    color: white;
    font-size: 1.5rem;
    box-shadow: 0 8px 25px rgba(102, 126, 234, 0.5);
    cursor: pointer;
    z-index: 999;
    transition: all 0.3s ease;
}

.mobile-filter-toggle:hover {
    transform: scale(1.1);
    box-shadow: 0 12px 35px rgba(102, 126, 234, 0.6);
}

.mobile-filter-toggle:active {
    transform: scale(0.95);
}

.filter-badge {
    position: absolute;
    top: -5px;
    left: -5px;
    background: #f5576c;
    color: white;
    width: 24px;
    height: 24px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 0.75rem;
    font-weight: 700;
    border: 3px solid white;
}

.filter-badge:empty {
    display: none;
}

/* Filter Sidebar */
.filter-sidebar {
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 20px rgba(0, 0, 0, 0.08);
    overflow: hidden;
    margin-bottom: 30px;
    position: sticky;
    top: 20px;
}

.filter-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 20px;
    font-size: 1.1rem;
    font-weight: 700;
    display: flex;
    align-items: center;
    justify-content: space-between;
}

.filter-header i {
    margin-left: 10px;
}

.filter-close-btn {
    display: none;
    background: rgba(255, 255, 255, 0.2);
    border: none;
    color: white;
    width: 36px;
    height: 36px;
    border-radius: 50%;
    cursor: pointer;
    font-size: 1.2rem;
    transition: all 0.3s ease;
}

.filter-close-btn:hover {
    background: rgba(255, 255, 255, 0.3);
    transform: rotate(90deg);
}

.filter-body {
    padding: 25px;
}

.filter-group {
    margin-bottom: 20px;
}

.filter-label {
    display: block;
    font-weight: 600;
    color: #333;
    margin-bottom: 8px;
    font-size: 0.95rem;
}

.filter-label i {
    margin-left: 8px;
    color: #667eea;
}

.filter-input,
.filter-select {
    width: 100%;
    padding: 12px 16px;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    font-size: 0.95rem;
    transition: all 0.3s ease;
    background: white;
}

.filter-input:focus,
.filter-select:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
}

.price-inputs {
    display: flex;
    align-items: center;
    gap: 10px;
}

.price-separator {
    color: #999;
    font-weight: 600;
}

.filter-submit-btn,
.filter-reset-btn {
    width: 100%;
    padding: 14px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 1rem;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    text-decoration: none;
}

.filter-submit-btn {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    margin-bottom: 12px;
}

.filter-submit-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.filter-reset-btn {
    background: white;
    color: #666;
    border: 2px solid #e9ecef;
}

.filter-reset-btn:hover {
    background: #f8f9fa;
    border-color: #dee2e6;
}

/* Add Item Card */
.add-item-card {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    border-radius: 16px;
    padding: 30px;
    text-align: center;
    color: white;
    box-shadow: 0 4px 20px rgba(245, 87, 108, 0.3);
}

.add-item-icon {
    font-size: 3.5rem;
    margin-bottom: 15px;
    animation: bounce 2s infinite;
}

.add-item-title {
    font-weight: 700;
    margin-bottom: 10px;
    font-size: 1.3rem;
}

.add-item-text {
    opacity: 0.95;
    margin-bottom: 20px;
    font-size: 0.95rem;
}

.add-item-btn {
    background: white;
    color: #f5576c;
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 700;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.add-item-btn:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 20px rgba(0, 0, 0, 0.2);
}

@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-10px); }
}

/* Marketplace Header */
.marketplace-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 30px;
    padding: 25px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.06);
}

.header-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 5px;
    display: flex;
    align-items: center;
    gap: 12px;
}

.header-title i {
    color: #667eea;
}

.header-count {
    color: #666;
    font-size: 1rem;
    margin: 0;
}

.header-count strong {
    color: #667eea;
    font-weight: 700;
}

.header-actions {
    display: flex;
    gap: 12px;
}

.action-btn {
    padding: 12px 24px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 0.95rem;
    text-decoration: none;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.action-btn.primary {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
}

.action-btn.primary:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.action-btn.secondary {
    background: white;
    color: #667eea;
    border-color: #667eea;
}

.action-btn.secondary:hover {
    background: #667eea;
    color: white;
}

/* Success Alert */
.success-alert {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    padding: 16px 20px;
    border-radius: 12px;
    margin-bottom: 25px;
    display: flex;
    align-items: center;
    gap: 12px;
    box-shadow: 0 4px 15px rgba(17, 153, 142, 0.3);
    animation: slideDown 0.4s ease;
}

.success-alert i {
    font-size: 1.5rem;
}

.alert-close {
    margin-right: auto;
    background: none;
    border: none;
    color: white;
    font-size: 1.8rem;
    cursor: pointer;
    padding: 0 8px;
    line-height: 1;
    opacity: 0.8;
    transition: opacity 0.3s;
}

.alert-close:hover {
    opacity: 1;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Marketplace Grid */
.marketplace-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 25px;
    margin-bottom: 40px;
}

.marketplace-item {
    background: white;
    border-radius: 16px;
    overflow: hidden;
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.08);
    transition: all 0.3s ease;
    position: relative;
}

.marketplace-item:hover {
    transform: translateY(-8px);
    box-shadow: 0 12px 30px rgba(0, 0, 0, 0.15);
}

.item-link {
    text-decoration: none;
    color: inherit;
    display: block;
}

.item-image {
    position: relative;
    height: 240px;
    overflow: hidden;
    background: #f8f9fa;
}

.item-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.4s ease;
}

.marketplace-item:hover .item-image img {
    transform: scale(1.08);
}

.no-image {
    width: 100%;
    height: 100%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 4rem;
    color: #dee2e6;
}

.sponsored-badge {
    position: absolute;
    top: 12px;
    right: 12px;
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
    padding: 8px 16px;
    border-radius: 25px;
    font-size: 0.85rem;
    font-weight: 700;
    z-index: 2;
    box-shadow: 0 4px 15px rgba(245, 87, 108, 0.4);
    display: flex;
    align-items: center;
    gap: 6px;
}

.condition-badge {
    position: absolute;
    bottom: 12px;
    left: 12px;
    padding: 6px 14px;
    border-radius: 8px;
    font-size: 0.85rem;
    font-weight: 700;
    z-index: 2;
    backdrop-filter: blur(10px);
}

.condition-new {
    background: rgba(40, 167, 69, 0.95);
    color: white;
}

.condition-like_new {
    background: rgba(23, 162, 184, 0.95);
    color: white;
}

.condition-good {
    background: rgba(255, 193, 7, 0.95);
    color: #000;
}

.condition-fair {
    background: rgba(108, 117, 125, 0.95);
    color: white;
}

.item-body {
    padding: 20px;
}

.item-title {
    font-size: 1.15rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 10px;
    line-height: 1.4;
    min-height: 50px;
}

.item-description {
    color: #666;
    font-size: 0.9rem;
    line-height: 1.5;
    margin-bottom: 15px;
    min-height: 42px;
}

.item-price-row {
    margin-bottom: 15px;
    padding-bottom: 15px;
    border-bottom: 2px solid #f1f3f5;
}

.item-price {
    display: flex;
    align-items: baseline;
    gap: 6px;
    flex-wrap: wrap;
}

.price-amount {
    font-size: 1.8rem;
    font-weight: 800;
    color: #667eea;
}

.price-currency {
    font-size: 1rem;
    font-weight: 600;
    color: #764ba2;
}

.price-negotiable {
    font-size: 0.8rem;
    color: #28a745;
    font-weight: 600;
    background: rgba(40, 167, 69, 0.1);
    padding: 4px 10px;
    border-radius: 6px;
}

.item-meta {
    display: flex;
    justify-content: space-between;
    margin-bottom: 16px;
    gap: 8px;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 6px;
    font-size: 0.85rem;
    color: #666;
}

.meta-item i {
    color: #667eea;
    font-size: 0.9rem;
}

.item-view-btn {
    width: 100%;
    padding: 12px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    border: none;
    border-radius: 10px;
    font-weight: 700;
    font-size: 0.95rem;
    cursor: pointer;
    transition: all 0.3s ease;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.item-view-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 80px 20px;
    background: white;
    border-radius: 16px;
    box-shadow: 0 2px 15px rgba(0, 0, 0, 0.06);
}

.empty-icon {
    font-size: 6rem;
    color: #dee2e6;
    margin-bottom: 20px;
}

.empty-title {
    font-size: 1.8rem;
    font-weight: 700;
    color: #333;
    margin-bottom: 12px;
}

.empty-text {
    color: #666;
    font-size: 1.1rem;
    margin-bottom: 30px;
}

.empty-btn {
    display: inline-flex;
    align-items: center;
    gap: 10px;
    padding: 14px 28px;
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    text-decoration: none;
    border-radius: 10px;
    font-weight: 700;
    transition: all 0.3s ease;
}

.empty-btn:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

/* Pagination */
.marketplace-pagination {
    margin-top: 40px;
    display: flex;
    justify-content: center;
}

/* Responsive */
@media (max-width: 991px) {
    /* Show mobile filter button */
    .mobile-filter-toggle {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    /* Hide filter sidebar by default on mobile */
    .filter-sidebar {
        position: fixed;
        top: 0;
        right: -100%;
        width: 85%;
        max-width: 400px;
        height: 100vh;
        margin: 0;
        z-index: 1000;
        transition: right 0.3s ease;
        overflow-y: auto;
    }
    
    .filter-sidebar.active {
        right: 0;
        box-shadow: -5px 0 25px rgba(0, 0, 0, 0.3);
    }
    
    /* Show close button on mobile */
    .filter-close-btn {
        display: flex;
        align-items: center;
        justify-content: center;
    }
    
    .marketplace-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 20px;
        padding: 20px;
    }
    
    .header-actions {
        width: 100%;
        flex-direction: column;
    }
    
    .action-btn {
        width: 100%;
        justify-content: center;
    }
    
    .marketplace-grid {
        grid-template-columns: repeat(2, 1fr);
        gap: 15px;
    }
}

/* Filter Overlay for mobile */
.filter-overlay {
    display: none;
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.5);
    z-index: 999;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.filter-overlay.active {
    display: block;
    opacity: 1;
}

@media (max-width: 768px) {
    /* Hero Section Mobile */
    .marketplace-hero {
        padding: 40px 0;
        margin-bottom: 25px;
    }
    
    .hero-title {
        font-size: 1.8rem;
        margin-bottom: 10px;
    }
    
    .hero-title i {
        margin-left: 8px;
    }
    
    .hero-subtitle {
        font-size: 1rem;
        margin-bottom: 25px;
    }
    
    .hero-stats {
        gap: 20px;
    }
    
    .stat-number {
        font-size: 1.8rem;
    }
    
    .stat-label {
        font-size: 0.85rem;
    }
    
    /* Filter Sidebar Mobile */
    .filter-header {
        padding: 15px;
        font-size: 1rem;
    }
    
    .filter-body {
        padding: 20px;
    }
    
    .filter-group {
        margin-bottom: 15px;
    }
    
    .filter-label {
        font-size: 0.9rem;
    }
    
    .filter-input,
    .filter-select {
        padding: 10px 14px;
        font-size: 0.9rem;
    }
    
    .filter-submit-btn,
    .filter-reset-btn {
        padding: 12px;
        font-size: 0.95rem;
    }
    
    /* Add Item Card Mobile */
    .add-item-card {
        padding: 25px;
    }
    
    .add-item-icon {
        font-size: 3rem;
        margin-bottom: 12px;
    }
    
    .add-item-title {
        font-size: 1.2rem;
    }
    
    .add-item-text {
        font-size: 0.9rem;
    }
    
    /* Header Mobile */
    .header-title {
        font-size: 1.4rem;
    }
    
    .header-title i {
        font-size: 1.2rem;
    }
    
    .header-count {
        font-size: 0.9rem;
    }
    
    /* Grid Mobile */
    .marketplace-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    /* Item Cards Mobile */
    .marketplace-item {
        box-shadow: 0 1px 10px rgba(0, 0, 0, 0.08);
    }
    
    .item-image {
        height: 220px;
    }
    
    .sponsored-badge {
        top: 10px;
        right: 10px;
        padding: 6px 12px;
        font-size: 0.8rem;
    }
    
    .condition-badge {
        bottom: 10px;
        left: 10px;
        padding: 5px 12px;
        font-size: 0.8rem;
    }
    
    .item-body {
        padding: 16px;
    }
    
    .item-title {
        font-size: 1.05rem;
        min-height: auto;
        margin-bottom: 8px;
    }
    
    .item-description {
        font-size: 0.85rem;
        min-height: auto;
        margin-bottom: 12px;
    }
    
    .item-price-row {
        margin-bottom: 12px;
        padding-bottom: 12px;
    }
    
    .price-amount {
        font-size: 1.5rem;
    }
    
    .price-currency {
        font-size: 0.9rem;
    }
    
    .price-negotiable {
        font-size: 0.75rem;
        padding: 3px 8px;
    }
    
    .item-meta {
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 14px;
    }
    
    .meta-item {
        font-size: 0.8rem;
    }
    
    .meta-item i {
        font-size: 0.85rem;
    }
    
    .item-view-btn {
        padding: 10px;
        font-size: 0.9rem;
    }
    
    /* Empty State Mobile */
    .empty-state {
        padding: 60px 20px;
    }
    
    .empty-icon {
        font-size: 4rem;
    }
    
    .empty-title {
        font-size: 1.5rem;
    }
    
    .empty-text {
        font-size: 1rem;
    }
    
    .empty-btn {
        padding: 12px 24px;
        font-size: 0.95rem;
    }
    
    /* Pagination Mobile */
    .marketplace-pagination {
        margin-top: 30px;
    }
    
    .marketplace-pagination .pagination {
        flex-wrap: wrap;
        gap: 5px;
    }
}

@media (max-width: 480px) {
    /* Extra Small Mobile */
    .marketplace-hero {
        padding: 30px 0;
    }
    
    .hero-title {
        font-size: 1.5rem;
    }
    
    .hero-subtitle {
        font-size: 0.9rem;
    }
    
    .hero-stats {
        flex-direction: column;
        gap: 15px;
    }
    
    .stat-number {
        font-size: 1.5rem;
    }
    
    .filter-body {
        padding: 15px;
    }
    
    .add-item-card {
        padding: 20px;
    }
    
    .marketplace-header {
        padding: 15px;
    }
    
    .header-title {
        font-size: 1.2rem;
    }
    
    .action-btn {
        padding: 10px 20px;
        font-size: 0.9rem;
    }
    
    .item-image {
        height: 200px;
    }
    
    .item-body {
        padding: 14px;
    }
    
    .item-title {
        font-size: 1rem;
    }
    
    .price-amount {
        font-size: 1.3rem;
    }
    
    .item-meta {
        justify-content: space-between;
        gap: 8px;
    }
    
    .meta-item span {
        font-size: 0.75rem;
    }
}
</style>

<!-- Filter Overlay -->
<div class="filter-overlay" id="filterOverlay"></div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mobileFilterBtn = document.getElementById('mobileFilterBtn');
    const closeFilterBtn = document.getElementById('closeFilterBtn');
    const filterSidebar = document.getElementById('filterSidebar');
    const filterOverlay = document.getElementById('filterOverlay');
    
    // Open filter
    if (mobileFilterBtn) {
        mobileFilterBtn.addEventListener('click', function() {
            filterSidebar.classList.add('active');
            filterOverlay.classList.add('active');
            document.body.style.overflow = 'hidden';
        });
    }
    
    // Close filter
    function closeFilter() {
        filterSidebar.classList.remove('active');
        filterOverlay.classList.remove('active');
        document.body.style.overflow = '';
    }
    
    if (closeFilterBtn) {
        closeFilterBtn.addEventListener('click', closeFilter);
    }
    
    if (filterOverlay) {
        filterOverlay.addEventListener('click', closeFilter);
    }
    
    // Close on ESC key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && filterSidebar.classList.contains('active')) {
            closeFilter();
        }
    });
});
</script>

@endsection
