@extends('layouts.app')

@section('content')
<div class="category-page">
    <!-- Hero Section -->
    <div class="category-hero">
        <div class="container">
            <div class="hero-content">
                <div class="breadcrumb">
                    <a href="{{url('/') }}">الرئيسية</a>
                    <span class="divider">←</span>
                    <a href="{{ route('categories.index') }}">الفئات</a>
                    <span class="divider">←</span>
                    <span class="current">{{ $category->name }}</span>
                </div>
                
                <div class="category-header">
                    <div class="category-icon">
                        {{ $category->icon }}
                    </div>
                    <div class="category-info">
                        <h1>{{ $category->name }}</h1>
                        <p class="category-description">{{ $category->description }}</p>
                        <div class="category-stats">
                            <span class="shop-count">
                                <i class="icon">🏪</i>
                                {{ $shops->total() }} متجر
                            </span>
                            @if($category->children->count() > 0)
                                <span class="subcategory-count">
                                    <i class="icon">📂</i>
                                    {{ $category->children->count() }} فئة فرعية
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Subcategories Section -->
    @if($category->children->count() > 0)
    <div class="subcategories-section">
        <div class="container">
            <h2 class="section-title">
                <span class="icon">📂</span>
                الفئات الفرعية
            </h2>
            <div class="subcategories-grid">
                @foreach($category->children as $subcategory)
                    <a href="{{ route('category.shops', $subcategory->slug) }}" class="subcategory-card">
                        <div class="subcategory-icon">{{ $subcategory->icon }}</div>
                        <h3 class="subcategory-name">{{ $subcategory->name }}</h3>
                        <span class="subcategory-count">
                            {{ $subcategory->shops()->where('is_active', true)->count() }} متجر
                        </span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    <!-- Filters Section -->
    <div class="filters-section">
        <div class="container">
            <div class="filters-header">
                <h2 class="section-title">
                    <span class="icon">🏪</span>
                    المتاجر المتاحة
                </h2>
                <button class="filter-toggle" onclick="toggleFilters()">
                    <span class="icon">⚙️</span>
                    تصفية النتائج
                </button>
            </div>
            
            <div class="filters-panel" id="filtersPanel">
                <form method="GET" action="{{ route('category.shops', $category->slug) }}" class="filters-form">
                    <div class="filter-row">
                        <!-- Search -->
                        <div class="filter-group">
                            <label for="search">البحث</label>
                            <input type="text" 
                                   id="search" 
                                   name="q" 
                                   placeholder="ابحث في المتاجر..." 
                                   value="{{ request('q') }}"
                                   class="filter-input">
                        </div>

                        <!-- City Filter -->
                        <div class="filter-group">
                            <label for="city">المدينة</label>
                            <select name="city" id="city" class="filter-select">
                                <option value="">كل المدن</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->slug }}" {{ request('city') == $city->slug ? 'selected' : '' }}>
                                        {{ $city->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Rating Filter -->
                        <div class="filter-group">
                            <label for="rating">التقييم</label>
                            <select name="rating" id="rating" class="filter-select">
                                <option value="">كل التقييمات</option>
                                <option value="4" {{ request('rating') == '4' ? 'selected' : '' }}>⭐ 4+ نجوم</option>
                                <option value="4.5" {{ request('rating') == '4.5' ? 'selected' : '' }}>⭐ 4.5+ نجوم</option>
                                <option value="5" {{ request('rating') == '5' ? 'selected' : '' }}>⭐ 5 نجوم</option>
                            </select>
                        </div>

                        <!-- Filter Buttons -->
                        <div class="filter-actions">
                            <button type="submit" class="btn btn-primary">
                                <span class="icon">🔍</span>
                                تطبيق الفلاتر
                            </button>
                            <a href="{{ route('category.shops', $category->slug) }}" class="btn btn-secondary">
                                <span class="icon">🔄</span>
                                إعادة تعيين
                            </a>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Shops Grid -->
    <div class="shops-section">
        <div class="container">
            @if($shops->count() > 0)
                <!-- Results Info -->
                <div class="results-info">
                    <span class="results-count">
                        عرض {{ $shops->firstItem() }}-{{ $shops->lastItem() }} من أصل {{ $shops->total() }} متجر
                    </span>
                    @if(request()->hasAny(['q', 'city', 'rating']))
                        <div class="active-filters">
                            <span class="filters-label">الفلاتر النشطة:</span>
                            @if(request('q'))
                                <span class="filter-tag">
                                    🔍 {{ request('q') }}
                                    <a href="{{ request()->url() }}?{{ http_build_query(request()->except('q')) }}" class="remove-filter">×</a>
                                </span>
                            @endif
                            @if(request('city'))
                                @php $selectedCity = $cities->where('slug', request('city'))->first(); @endphp
                                <span class="filter-tag">
                                    📍 {{ $selectedCity->name ?? request('city') }}
                                    <a href="{{ request()->url() }}?{{ http_build_query(request()->except('city')) }}" class="remove-filter">×</a>
                                </span>
                            @endif
                            @if(request('rating'))
                                <span class="filter-tag">
                                    ⭐ {{ request('rating') }}+ نجوم
                                    <a href="{{ request()->url() }}?{{ http_build_query(request()->except('rating')) }}" class="remove-filter">×</a>
                                </span>
                            @endif
                        </div>
                    @endif
                </div>

                <!-- Shops Grid -->
                <div class="shops-grid">
                    @foreach($shops as $shop)
                        <x-shop-card :shop="$shop" :loop="$loop" />
                    @endforeach
                </div>

                <!-- Pagination -->
                <x-pagination :paginator="$shops" />
            @else
                <!-- No Results -->
                <div class="no-results">
                    <div class="no-results-icon">🔍</div>
                    <h3>لا توجد متاجر في هذه الفئة</h3>
                    <p>عذراً، لم نجد أي متاجر تطابق معايير البحث الخاصة بك.</p>
                    <div class="no-results-actions">
                        <a href="{{ route('category.shops', $category->slug) }}" class="btn btn-primary">
                            عرض جميع المتاجر
                        </a>
                        <a href="{{ route('categories.index') }}" class="btn btn-secondary">
                            تصفح الفئات الأخرى
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<style>
/* Category Page Styles */
.category-page {
    min-height: 100vh;
    background: #f8f9fa;
}

/* Hero Section */
.category-hero {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white;
    padding: 40px 0;
    margin-bottom: 30px;
}

.breadcrumb {
    margin-bottom: 20px;
    font-size: 14px;
    opacity: 0.9;
}

.breadcrumb a {
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    transition: color 0.3s ease;
}

.breadcrumb a:hover {
    color: white;
}

.divider {
    margin: 0 10px;
    opacity: 0.6;
}

.current {
    color: white;
    font-weight: 500;
}

.category-header {
    display: flex;
    align-items: center;
    gap: 20px;
}

.category-icon {
    font-size: 4rem;
    background: rgba(255, 255, 255, 0.2);
    padding: 20px;
    border-radius: 20px;
    backdrop-filter: blur(10px);
}

.category-info h1 {
    font-size: 2.5rem;
    margin-bottom: 10px;
    font-weight: 700;
}

.category-description {
    font-size: 1.1rem;
    opacity: 0.9;
    margin-bottom: 15px;
    line-height: 1.6;
}

.category-stats {
    display: flex;
    gap: 20px;
    align-items: center;
}

.category-stats span {
    display: flex;
    align-items: center;
    gap: 5px;
    background: rgba(255, 255, 255, 0.2);
    padding: 8px 15px;
    border-radius: 20px;
    font-size: 14px;
    font-weight: 500;
}

/* Subcategories Section */
.subcategories-section {
    margin-bottom: 40px;
}

.section-title {
    display: flex;
    align-items: center;
    gap: 10px;
    font-size: 1.8rem;
    font-weight: 600;
    color: var(--primary);
    margin-bottom: 20px;
}

.subcategories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
    gap: 20px;
}

.subcategory-card {
    background: white;
    padding: 20px;
    border-radius: 15px;
    text-align: center;
    text-decoration: none;
    color: inherit;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.subcategory-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    border-color: var(--primary);
    text-decoration: none;
    color: inherit;
}

.subcategory-icon {
    font-size: 2.5rem;
    margin-bottom: 15px;
}

.subcategory-name {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--primary);
    margin-bottom: 10px;
}

.subcategory-count {
    font-size: 14px;
    color: #666;
    background: #f8f9fa;
    padding: 5px 10px;
    border-radius: 15px;
    display: inline-block;
}

/* Filters Section */
.filters-section {
    margin-bottom: 30px;
}

.filters-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
}

.filter-toggle {
    background: var(--primary);
    color: white;
    border: none;
    padding: 10px 20px;
    border-radius: 10px;
    cursor: pointer;
    font-weight: 500;
    display: flex;
    align-items: center;
    gap: 8px;
    transition: all 0.3s ease;
}

.filter-toggle:hover {
    background: #014a42;
    transform: translateY(-2px);
}

.filters-panel {
    background: white;
    padding: 25px;
    border-radius: 15px;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    display: none;
}

.filters-panel.show {
    display: block;
}

.filter-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 20px;
    align-items: end;
}

.filter-group {
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.filter-group label {
    font-weight: 600;
    color: var(--primary);
    font-size: 14px;
}

.filter-input,
.filter-select {
    padding: 12px 15px;
    border: 2px solid #e9ecef;
    border-radius: 10px;
    font-family: 'Cairo', sans-serif;
    font-size: 14px;
    transition: border-color 0.3s ease;
}

.filter-input:focus,
.filter-select:focus {
    outline: none;
    border-color: var(--primary);
}

.filter-actions {
    display: flex;
    gap: 10px;
}

.btn {
    padding: 12px 20px;
    border-radius: 10px;
    font-weight: 500;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 8px;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    font-family: 'Cairo', sans-serif;
}

.btn-primary {
    background: var(--primary);
    color: white;
}

.btn-primary:hover {
    background: #014a42;
    transform: translateY(-2px);
    color: white;
    text-decoration: none;
}

.btn-secondary {
    background: #6c757d;
    color: white;
}

.btn-secondary:hover {
    background: #545b62;
    transform: translateY(-2px);
    color: white;
    text-decoration: none;
}

/* Results Info */
.results-info {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 25px;
    flex-wrap: wrap;
    gap: 15px;
}

.results-count {
    font-weight: 500;
    color: var(--primary);
}

.active-filters {
    display: flex;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
}

.filters-label {
    font-weight: 500;
    color: #666;
}

.filter-tag {
    background: var(--primary);
    color: white;
    padding: 5px 10px;
    border-radius: 15px;
    font-size: 12px;
    display: flex;
    align-items: center;
    gap: 5px;
}

.remove-filter {
    color: rgba(255, 255, 255, 0.8);
    text-decoration: none;
    font-weight: bold;
    margin-left: 5px;
}

.remove-filter:hover {
    color: white;
}

/* Shops Grid */
.shops-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
    gap: 25px;
    margin-bottom: 40px;
}

.shop-card {
    background: white;
    border-radius: 15px;
    overflow: hidden;
    box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.shop-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.15);
    border-color: var(--primary);
}

.shop-image {
    position: relative;
    height: 200px;
    overflow: hidden;
}

.shop-image img {
    width: 100%;
    height: 100%;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.shop-card:hover .shop-image img {
    transform: scale(1.05);
}

.shop-placeholder {
    width: 100%;
    height: 100%;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    display: flex;
    align-items: center;
    justify-content: center;
}

.placeholder-icon {
    font-size: 3rem;
    filter: brightness(1.2);
}

.shop-badges {
    position: absolute;
    top: 15px;
    right: 15px;
    display: flex;
    flex-direction: column;
    gap: 5px;
}

.badge {
    padding: 4px 8px;
    border-radius: 10px;
    font-size: 11px;
    font-weight: 600;
    color: white;
}

.badge.featured {
    background: #ffc107;
    color: #000;
}

.badge.verified {
    background: #28a745;
}

.shop-content {
    padding: 20px;
}

.shop-header {
    margin-bottom: 15px;
}

.shop-name {
    margin-bottom: 8px;
}

.shop-name a {
    color: var(--primary);
    text-decoration: none;
    font-weight: 600;
    font-size: 1.1rem;
}

.shop-name a:hover {
    color: #014a42;
}

.shop-rating {
    display: flex;
    align-items: center;
    gap: 5px;
    font-size: 14px;
}

.rating-value {
    font-weight: 600;
    color: var(--primary);
}

.review-count {
    color: #666;
}

.shop-info {
    display: flex;
    justify-content: space-between;
    margin-bottom: 15px;
    font-size: 14px;
    color: #666;
}

.shop-info > div {
    display: flex;
    align-items: center;
    gap: 5px;
}

.shop-description {
    color: #666;
    line-height: 1.5;
    margin-bottom: 20px;
}

.shop-actions {
    display: flex;
    gap: 10px;
}

.shop-actions .btn {
    flex: 1;
    justify-content: center;
    font-size: 14px;
    padding: 10px 15px;
}

/* No Results */
.no-results {
    text-align: center;
    padding: 60px 20px;
}

.no-results-icon {
    font-size: 4rem;
    margin-bottom: 20px;
    opacity: 0.5;
}

.no-results h3 {
    color: var(--primary);
    margin-bottom: 15px;
    font-size: 1.5rem;
}

.no-results p {
    color: #666;
    margin-bottom: 30px;
    font-size: 1.1rem;
}

.no-results-actions {
    display: flex;
    justify-content: center;
    gap: 15px;
    flex-wrap: wrap;
}

/* Responsive Design */
@media (max-width: 768px) {
    .category-header {
        flex-direction: column;
        text-align: center;
        gap: 15px;
    }
    
    .category-info h1 {
        font-size: 2rem;
    }
    
    .category-stats {
        justify-content: center;
    }
    
    .subcategories-grid {
        grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
        gap: 15px;
    }
    
    .filters-header {
        flex-direction: column;
        gap: 15px;
    }
    
    .filter-row {
        grid-template-columns: 1fr;
        gap: 15px;
    }
    
    .filter-actions {
        flex-direction: column;
    }
    
    .shops-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .results-info {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .shop-actions {
        flex-direction: column;
    }
    
    .no-results-actions {
        flex-direction: column;
        align-items: center;
    }
}
</style>

<script>
function toggleFilters() {
    const panel = document.getElementById('filtersPanel');
    panel.classList.toggle('show');
}

// Auto-submit form when select changes (for better UX)
document.addEventListener('DOMContentLoaded', function() {
    const selects = document.querySelectorAll('.filter-select');
    selects.forEach(select => {
        select.addEventListener('change', function() {
            // Optional: Auto-submit on change
            // this.closest('form').submit();
        });
    });
});
</script>
@endsection