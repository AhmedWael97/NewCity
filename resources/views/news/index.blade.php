@extends('layouts.app')

@section('title', 'الأخبار والمقالات')
@section('description', 'اطلع على آخر الأخبار والمقالات')

@push('styles')
<style>
    .news-hero {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 60px 0 40px;
    }
    
    .news-card {
        border: none;
        border-radius: 12px;
        overflow: hidden;
        transition: transform 0.3s, box-shadow 0.3s;
        height: 100%;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    
    .news-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
    }
    
    .news-thumbnail {
        width: 100%;
        height: 220px;
        object-fit: cover;
    }
    
    .news-meta {
        font-size: 0.875rem;
        color: #6c757d;
    }
    
    .news-category-badge {
        position: absolute;
        top: 15px;
        right: 15px;
        background: rgba(255,255,255,0.95);
        padding: 5px 15px;
        border-radius: 20px;
        font-size: 0.875rem;
        font-weight: 600;
        color: #667eea;
        box-shadow: 0 2px 8px rgba(0,0,0,0.1);
    }
    
    .category-filter {
        background: white;
        border-radius: 50px;
        padding: 8px 20px;
        border: 2px solid #e9ecef;
        transition: all 0.3s;
        cursor: pointer;
        text-decoration: none;
        color: #495057;
        display: inline-block;
        margin: 5px;
    }
    
    .category-filter:hover,
    .category-filter.active {
        background: #667eea;
        color: white;
        border-color: #667eea;
        text-decoration: none;
    }
    
    .search-box {
        max-width: 500px;
        margin: 0 auto;
    }
</style>
@endpush

@section('content')
<main class="news-page">
    {{-- Hero Section --}}
    <section class="news-hero text-white">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-8 mx-auto text-center">
                    <h1 class="display-4 fw-bold mb-3">
                        <i class="fas fa-newspaper me-2"></i>
                        آخر الأخبار
                    </h1>
                    <p class="lead mb-4">ابقَ على اطلاع بأحدث الأخبار والمقالات</p>
                    
                    {{-- Search Box --}}
                    <form action="{{ route('news.index') }}" method="GET" class="search-box">
                        <div class="input-group input-group-lg shadow-sm">
                            <input type="text" name="search" class="form-control border-0" 
                                   placeholder="ابحث في الأخبار..." 
                                   value="{{ request('search') }}">
                            <button class="btn btn-light" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    {{-- Categories Filter --}}
    @if($categories->count() > 0)
    <section class="py-4 bg-light">
        <div class="container">
            <div class="text-center">
                <a href="{{ route('news.index') }}" 
                   class="category-filter {{ !request('category') ? 'active' : '' }}">
                    <i class="fas fa-th me-1"></i> الكل
                </a>
                @foreach($categories as $category)
                <a href="{{ route('news.index', ['category' => $category->id]) }}" 
                   class="category-filter {{ request('category') == $category->id ? 'active' : '' }}">
                    {{ $category->name }}
                    @if($category->active_news_count > 0)
                        <span class="badge bg-primary ms-1">{{ $category->active_news_count }}</span>
                    @endif
                </a>
                @endforeach
            </div>
        </div>
    </section>
    @endif

    {{-- News Grid --}}
    <section class="py-5">
        <div class="container">
            @if(request('search'))
            <div class="alert alert-info">
                <i class="fas fa-search me-2"></i>
                نتائج البحث عن: <strong>{{ request('search') }}</strong>
                <a href="{{ route('news.index') }}" class="btn btn-sm btn-outline-info ms-2">
                    <i class="fas fa-times"></i> مسح البحث
                </a>
            </div>
            @endif

            <div class="row g-4">
                @forelse($news as $item)
                <div class="col-md-6 col-lg-4">
                    <article class="news-card card h-100">
                        <div class="position-relative">
                            <img src="{{ $item->thumbnail_url }}" 
                                 alt="{{ $item->title }}" 
                                 class="news-thumbnail">
                            @if($item->category)
                            <span class="news-category-badge">
                                {{ $item->category->name }}
                            </span>
                            @endif
                        </div>
                        <div class="card-body d-flex flex-column">
                            <div class="news-meta mb-2">
                                <i class="far fa-calendar me-1"></i>
                                {{ $item->published_at->format('Y-m-d') }}
                                @if($item->city)
                                    <span class="mx-2">•</span>
                                    <i class="fas fa-map-marker-alt me-1"></i>
                                    {{ $item->city->name }}
                                @endif
                                <span class="mx-2">•</span>
                                <i class="far fa-eye me-1"></i>
                                {{ number_format($item->views_count) }}
                            </div>
                            
                            <h3 class="h5 card-title mb-3">
                                <a href="{{ route('news.show', $item->slug) }}" 
                                   class="text-dark text-decoration-none stretched-link">
                                    {{ $item->title }}
                                </a>
                            </h3>
                            
                            <p class="card-text text-muted mb-3">
                                {{ Str::limit($item->description, 120) }}
                            </p>
                            
                            <div class="mt-auto">
                                <span class="text-primary small">
                                    <i class="far fa-clock me-1"></i>
                                    {{ $item->reading_time }} دقائق قراءة
                                </span>
                            </div>
                        </div>
                    </article>
                </div>
                @empty
                <div class="col-12">
                    <div class="text-center py-5">
                        <i class="fas fa-newspaper text-muted" style="font-size: 4rem;"></i>
                        <h3 class="mt-3 text-muted">لا توجد أخبار حالياً</h3>
                        <p class="text-muted">ستتوفر الأخبار قريباً</p>
                    </div>
                </div>
                @endforelse
            </div>

            {{-- Pagination --}}
            @if($news->hasPages())
            <div class="mt-5 d-flex justify-content-center">
                {{ $news->links() }}
            </div>
            @endif
        </div>
    </section>
</main>
@endsection
