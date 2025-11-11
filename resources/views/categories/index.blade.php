@extends('layouts.app')

@section('content')
<div class="categories-index-page">
    <!-- Hero Section -->
    <div class="categories-hero">
        <div class="container">
            <div class="hero-content">
                <div class="breadcrumb">
                    <a href="{{url('/') }}">الرئيسية</a>
                    <span class="divider">←</span>
                    <span class="current">جميع الفئات</span>
                </div>
                
                <h1>🏷️ فئات المتاجر والخدمات</h1>
                <p class="hero-description">استعرض جميع فئات المتاجر والخدمات المتاحة في مصر واختر ما يناسبك</p>
                
                <div class="hero-stats">
                    <div class="stat-item">
                        <span class="stat-number">{{ $categories->count() }}</span>
                        <span class="stat-label">فئة رئيسية</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $categories->sum(function($cat) { return $cat->children->count(); }) }}</span>
                        <span class="stat-label">فئة فرعية</span>
                    </div>
                    <div class="stat-item">
                        <span class="stat-number">{{ $categories->sum('shops_count') }}</span>
                        <span class="stat-label">متجر متاح</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Categories Grid -->
    <div class="categories-section">
        <div class="container">
            <div class="categories-grid">
                @foreach($categories as $category)
                    <div class="category-main-card">
                        <!-- Main Category Header -->
                        <div class="category-card-header">
                            <a href="{{ route('category.shops', $category->slug) }}" class="category-link">
                                <div class="category-icon">{{ $category->icon }}</div>
                                <div class="category-details">
                                    <h2 class="category-name">{{ $category->name }}</h2>
                                    <p class="category-description">{{ $category->description }}</p>
                                    <div class="category-meta">
                                        <span class="shop-count">
                                            <i class="icon">🏪</i>
                                            {{ $category->shops_count }} متجر
                                        </span>
                                        @if($category->children->count() > 0)
                                            <span class="subcategory-count">
                                                <i class="icon">📂</i>
                                                {{ $category->children->count() }} فئة فرعية
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </a>
                        </div>

                        <!-- Subcategories -->
                        @if($category->children->count() > 0)
                            <div class="subcategories-container">
                                <h3 class="subcategories-title">الفئات الفرعية:</h3>
                                <div class="subcategories-list">
                                    @foreach($category->children->take(6) as $subcategory)
                                        <a href="{{ route('category.shops', $subcategory->slug) }}" class="subcategory-item">
                                            <span class="subcategory-icon">{{ $subcategory->icon }}</span>
                                            <span class="subcategory-name">{{ $subcategory->name }}</span>
                                            <span class="subcategory-shops">
                                                ({{ $subcategory->shops()->where('is_active', true)->count() }})
                                            </span>
                                        </a>
                                    @endforeach
                                    
                                    @if($category->children->count() > 6)
                                        <a href="{{ route('category.shops', $category->slug) }}" class="view-all-subcategories">
                                            <span class="icon">👁️</span>
                                            عرض جميع الفئات الفرعية ({{ $category->children->count() }})
                                        </a>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Quick Actions -->
                        <div class="category-actions">
                            <a href="{{ route('category.shops', $category->slug) }}" class="btn btn-primary">
                                <span class="icon">👁️</span>
                                عرض المتاجر
                            </a>
                            @if($category->children->count() > 0)
                                <span class="btn btn-secondary" onclick="toggleSubcategories('{{ $category->slug }}')">
                                    <span class="icon">📂</span>
                                    الفئات الفرعية
                                </span>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

            @if($categories->isEmpty())
                <div class="no-categories">
                    <div class="no-categories-icon">📂</div>
                    <h3>لا توجد فئات متاحة حالياً</h3>
                    <p>نعمل على إضافة المزيد من الفئات قريباً</p>
                    <a href="{{url('/') }}" class="btn btn-primary">
                        العودة للرئيسية
                    </a>
                </div>
            @endif
        </div>
    </div>

    <!-- Categories Quick Navigation -->
    <div class="quick-nav-section">
        <div class="container">
            <h2 class="section-title">
                <span class="icon">🔗</span>
                تصفح سريع للفئات
            </h2>
            <div class="quick-nav-grid">
                @foreach($categories->take(8) as $category)
                    <a href="{{ route('category.shops', $category->slug) }}" class="quick-nav-item">
                        <span class="quick-nav-icon">{{ $category->icon }}</span>
                        <span class="quick-nav-name">{{ $category->name }}</span>
                        <span class="quick-nav-count">{{ $category->shops_count }}</span>
                    </a>
                @endforeach
            </div>
        </div>
    </div>
</div>

<style>
/* Categories Index Page Styles */
.categories-index-page {
    min-height: 100vh;
    background: #f8f9fa;
}

/* Hero Section */
.categories-hero {
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    color: white;
    padding: 50px 0;
    margin-bottom: 40px;
}

.hero-content {
    text-align: center;
}

.breadcrumb {
    margin-bottom: 25px;
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

.hero-content h1 {
    font-size: 3rem;
    margin-bottom: 20px;
    font-weight: 700;
}

.hero-description {
    font-size: 1.3rem;
    opacity: 0.9;
    margin-bottom: 30px;
    line-height: 1.6;
    max-width: 600px;
    margin-left: auto;
    margin-right: auto;
}

.hero-stats {
    display: flex;
    justify-content: center;
    gap: 40px;
    flex-wrap: wrap;
}

.stat-item {
    text-align: center;
    background: rgba(255, 255, 255, 0.2);
    padding: 20px;
    border-radius: 15px;
    backdrop-filter: blur(10px);
    min-width: 120px;
}

.stat-number {
    display: block;
    font-size: 2.5rem;
    font-weight: 700;
    margin-bottom: 5px;
}

.stat-label {
    font-size: 14px;
    opacity: 0.9;
}

/* Categories Grid */
.categories-section {
    margin-bottom: 50px;
}

.categories-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 30px;
}

.category-main-card {
    background: white;
    border-radius: 20px;
    overflow: hidden;
    box-shadow: 0 8px 25px rgba(0, 0, 0, 0.1);
    transition: all 0.3s ease;
    border: 3px solid transparent;
}

.category-main-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
    border-color: var(--primary);
}

/* Category Header */
.category-card-header {
    padding: 25px;
    border-bottom: 2px solid #f8f9fa;
}

.category-link {
    display: flex;
    align-items: center;
    gap: 20px;
    text-decoration: none;
    color: inherit;
}

.category-icon {
    font-size: 4rem;
    background: linear-gradient(135deg, var(--primary), var(--secondary));
    padding: 20px;
    border-radius: 15px;
    color: white;
    display: flex;
    align-items: center;
    justify-content: center;
    min-width: 80px;
    min-height: 80px;
}

.category-details {
    flex: 1;
}

.category-name {
    font-size: 1.6rem;
    font-weight: 700;
    color: var(--primary);
    margin-bottom: 8px;
}

.category-description {
    color: #666;
    margin-bottom: 15px;
    line-height: 1.5;
}

.category-meta {
    display: flex;
    gap: 20px;
    flex-wrap: wrap;
}

.category-meta span {
    display: flex;
    align-items: center;
    gap: 5px;
    background: #f8f9fa;
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 13px;
    font-weight: 500;
    color: #666;
}

/* Subcategories */
.subcategories-container {
    padding: 20px 25px;
    background: #fafbfc;
}

.subcategories-title {
    font-size: 1.1rem;
    font-weight: 600;
    color: var(--primary);
    margin-bottom: 15px;
}

.subcategories-list {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 10px;
}

.subcategory-item {
    display: flex;
    align-items: center;
    gap: 8px;
    padding: 10px 15px;
    background: white;
    border-radius: 10px;
    text-decoration: none;
    color: #333;
    transition: all 0.3s ease;
    border: 1px solid #e9ecef;
    font-size: 14px;
}

.subcategory-item:hover {
    background: var(--primary);
    color: white;
    transform: translateX(5px);
    text-decoration: none;
}

.subcategory-icon {
    font-size: 16px;
}

.subcategory-name {
    flex: 1;
    font-weight: 500;
}

.subcategory-shops {
    font-size: 12px;
    opacity: 0.7;
}

.view-all-subcategories {
    grid-column: 1 / -1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px;
    background: var(--primary);
    color: white;
    border-radius: 10px;
    text-decoration: none;
    font-weight: 500;
    margin-top: 10px;
    transition: all 0.3s ease;
}

.view-all-subcategories:hover {
    background: #014a42;
    text-decoration: none;
    color: white;
}

/* Category Actions */
.category-actions {
    padding: 20px 25px;
    display: flex;
    gap: 15px;
    border-top: 1px solid #f1f3f4;
}

.btn {
    flex: 1;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
    padding: 12px 20px;
    border-radius: 10px;
    font-weight: 500;
    text-decoration: none;
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

/* Quick Navigation */
.quick-nav-section {
    background: white;
    padding: 40px 0;
    border-top: 3px solid var(--primary);
}

.section-title {
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    font-size: 2rem;
    font-weight: 600;
    color: var(--primary);
    margin-bottom: 30px;
    text-align: center;
}

.quick-nav-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
    gap: 20px;
}

.quick-nav-item {
    display: flex;
    flex-direction: column;
    align-items: center;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 15px;
    text-decoration: none;
    color: inherit;
    transition: all 0.3s ease;
    border: 2px solid transparent;
}

.quick-nav-item:hover {
    background: var(--primary);
    color: white;
    transform: translateY(-5px);
    border-color: var(--primary);
    text-decoration: none;
}

.quick-nav-icon {
    font-size: 2.5rem;
    margin-bottom: 10px;
}

.quick-nav-name {
    font-weight: 600;
    margin-bottom: 5px;
    text-align: center;
}

.quick-nav-count {
    font-size: 12px;
    opacity: 0.7;
    background: rgba(0, 0, 0, 0.1);
    padding: 4px 8px;
    border-radius: 10px;
}

.quick-nav-item:hover .quick-nav-count {
    background: rgba(255, 255, 255, 0.2);
}

/* No Categories */
.no-categories {
    text-align: center;
    padding: 80px 20px;
}

.no-categories-icon {
    font-size: 5rem;
    margin-bottom: 20px;
    opacity: 0.5;
}

.no-categories h3 {
    color: var(--primary);
    margin-bottom: 15px;
    font-size: 1.8rem;
}

.no-categories p {
    color: #666;
    margin-bottom: 30px;
    font-size: 1.1rem;
}

/* Responsive Design */
@media (max-width: 768px) {
    .hero-content h1 {
        font-size: 2.2rem;
    }
    
    .hero-description {
        font-size: 1.1rem;
    }
    
    .hero-stats {
        gap: 20px;
    }
    
    .stat-item {
        min-width: 100px;
        padding: 15px;
    }
    
    .stat-number {
        font-size: 2rem;
    }
    
    .categories-grid {
        grid-template-columns: 1fr;
        gap: 20px;
    }
    
    .category-link {
        flex-direction: column;
        text-align: center;
        gap: 15px;
    }
    
    .category-icon {
        font-size: 3rem;
        min-width: 70px;
        min-height: 70px;
        padding: 15px;
    }
    
    .category-meta {
        justify-content: center;
    }
    
    .subcategories-list {
        grid-template-columns: 1fr;
    }
    
    .category-actions {
        flex-direction: column;
    }
    
    .quick-nav-grid {
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 15px;
    }
}

@media (max-width: 480px) {
    .hero-stats {
        flex-direction: column;
        align-items: center;
    }
    
    .quick-nav-icon {
        font-size: 2rem;
    }
}
</style>

<script>
function toggleSubcategories(categorySlug) {
    const container = document.querySelector(`[data-category="${categorySlug}"] .subcategories-container`);
    if (container) {
        container.style.display = container.style.display === 'none' ? 'block' : 'none';
    }
}
</script>
@endsection