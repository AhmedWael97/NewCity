@extends('layouts.app')

@section('title', $news->title)
@section('description', $news->description)

@push('styles')
<style>
    .news-header {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        padding: 80px 0 40px;
        color: white;
    }
    
    .news-content {
        max-width: 800px;
        margin: 0 auto;
    }
    
    .news-featured-image {
        width: 100%;
        max-height: 500px;
        object-fit: cover;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.1);
    }
    
    .news-body {
        font-size: 1.125rem;
        line-height: 1.8;
        color: #333;
    }
    
    .news-body img {
        max-width: 100%;
        height: auto;
        border-radius: 8px;
        margin: 20px 0;
    }
    
    .news-body p {
        margin-bottom: 1.5rem;
    }
    
    .news-body h2, .news-body h3, .news-body h4 {
        margin-top: 2rem;
        margin-bottom: 1rem;
        font-weight: 600;
    }
    
    .news-meta-info {
        display: flex;
        flex-wrap: wrap;
        gap: 20px;
        padding: 20px 0;
        border-top: 1px solid #dee2e6;
        border-bottom: 1px solid #dee2e6;
        margin: 30px 0;
    }
    
    .meta-item {
        display: flex;
        align-items: center;
        color: #6c757d;
    }
    
    .meta-item i {
        margin-left: 8px;
        color: #667eea;
    }
    
    .news-gallery {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
        gap: 15px;
        margin: 30px 0;
    }
    
    .news-gallery img {
        width: 100%;
        height: 250px;
        object-fit: cover;
        border-radius: 8px;
        cursor: pointer;
        transition: transform 0.3s;
    }
    
    .news-gallery img:hover {
        transform: scale(1.05);
    }
    
    .related-news-card {
        border: none;
        border-radius: 12px;
        overflow: hidden;
        transition: transform 0.3s, box-shadow 0.3s;
        box-shadow: 0 2px 8px rgba(0,0,0,0.08);
    }
    
    .related-news-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 8px 24px rgba(0,0,0,0.15);
    }
    
    .related-news-thumbnail {
        width: 100%;
        height: 180px;
        object-fit: cover;
    }
    
    .share-buttons {
        display: flex;
        gap: 10px;
        margin: 30px 0;
    }
    
    .share-btn {
        width: 40px;
        height: 40px;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        color: white;
        text-decoration: none;
        transition: transform 0.2s;
    }
    
    .share-btn:hover {
        transform: scale(1.1);
    }
    
    .share-btn.facebook { background: #3b5998; }
    .share-btn.twitter { background: #1da1f2; }
    .share-btn.whatsapp { background: #25d366; }
    .share-btn.telegram { background: #0088cc; }
</style>
@endpush

@section('content')
<main class="news-detail">
    {{-- Header --}}
    <section class="news-header">
        <div class="container">
            <div class="news-content">
                @if($news->category)
                <div class="mb-3">
                    <span class="badge bg-white text-primary px-3 py-2 rounded-pill">
                        {{ $news->category->name }}
                    </span>
                </div>
                @endif
                
                <h1 class="display-4 fw-bold mb-4">{{ $news->title }}</h1>
                
                <div class="d-flex flex-wrap gap-4 text-white-75">
                    <span>
                        <i class="far fa-calendar me-2"></i>
                        {{ $news->published_at->format('Y-m-d') }}
                    </span>
                    <span>
                        <i class="far fa-clock me-2"></i>
                        {{ $news->reading_time }} دقائق قراءة
                    </span>
                    <span>
                        <i class="far fa-eye me-2"></i>
                        {{ number_format($news->views_count) }} مشاهدة
                    </span>
                    @if($news->city)
                    <span>
                        <i class="fas fa-map-marker-alt me-2"></i>
                        {{ $news->city->name }}
                    </span>
                    @endif
                </div>
            </div>
        </div>
    </section>

    {{-- Content --}}
    <section class="py-5">
        <div class="container">
            <div class="news-content">
                {{-- Featured Image --}}
                @if($news->thumbnail)
                <div class="mb-5">
                    <img src="{{ $news->thumbnail_url }}" 
                         alt="{{ $news->title }}" 
                         class="news-featured-image">
                </div>
                @endif

                {{-- Description --}}
                <div class="lead mb-4 text-muted">
                    {!! $news->description !!}
                </div>

                {{-- Meta Info --}}
                <div class="news-meta-info">
                    <div class="meta-item">
                        <i class="fas fa-calendar-alt"></i>
                        <span>نُشر في {{ $news->published_at->format('d F Y') }}</span>
                    </div>
                    @if($news->category)
                    <div class="meta-item">
                        <i class="fas fa-tag"></i>
                        <span>{{ $news->category->name }}</span>
                    </div>
                    @endif
                    <div class="meta-item">
                        <i class="fas fa-clock"></i>
                        <span>{{ $news->reading_time }} دقائق</span>
                    </div>
                </div>

                {{-- Content --}}
                <div class="news-body">
                    {!! $news->content !!}
                </div>

                {{-- Gallery --}}
                @if($news->images && count($news->images) > 0)
                <div class="news-gallery">
                    @foreach($news->images_url as $image)
                    <img src="{{ $image }}" alt="صورة" onclick="openImageModal(this.src)">
                    @endforeach
                </div>
                @endif

                {{-- Share Buttons --}}
                <div class="share-buttons">
                    <h6 class="mb-0 me-3">مشاركة:</h6>
                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(route('news.show', $news->slug)) }}" 
                       target="_blank" class="share-btn facebook" title="مشاركة على فيسبوك">
                        <i class="fab fa-facebook-f"></i>
                    </a>
                    <a href="https://twitter.com/intent/tweet?url={{ urlencode(route('news.show', $news->slug)) }}&text={{ urlencode($news->title) }}" 
                       target="_blank" class="share-btn twitter" title="مشاركة على تويتر">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://wa.me/?text={{ urlencode($news->title . ' ' . route('news.show', $news->slug)) }}" 
                       target="_blank" class="share-btn whatsapp" title="مشاركة على واتساب">
                        <i class="fab fa-whatsapp"></i>
                    </a>
                    <a href="https://t.me/share/url?url={{ urlencode(route('news.show', $news->slug)) }}&text={{ urlencode($news->title) }}" 
                       target="_blank" class="share-btn telegram" title="مشاركة على تليجرام">
                        <i class="fab fa-telegram-plane"></i>
                    </a>
                </div>
            </div>
        </div>
    </section>

    {{-- Related News --}}
    @if($relatedNews->count() > 0)
    <section class="py-5 bg-light">
        <div class="container">
            <h2 class="h3 mb-4 text-center">أخبار ذات صلة</h2>
            <div class="row g-4">
                @foreach($relatedNews as $related)
                <div class="col-md-4">
                    <article class="related-news-card card h-100">
                        <img src="{{ $related->thumbnail_url }}" 
                             alt="{{ $related->title }}" 
                             class="related-news-thumbnail">
                        <div class="card-body">
                            @if($related->category)
                            <span class="badge bg-primary mb-2">{{ $related->category->name }}</span>
                            @endif
                            <h3 class="h6 mb-2">
                                <a href="{{ route('news.show', $related->slug) }}" 
                                   class="text-dark text-decoration-none stretched-link">
                                    {{ $related->title }}
                                </a>
                            </h3>
                            <p class="small text-muted mb-2">
                                {{ Str::limit($related->description, 80) }}
                            </p>
                            <div class="small text-muted">
                                <i class="far fa-calendar me-1"></i>
                                {{ $related->published_at->format('Y-m-d') }}
                            </div>
                        </div>
                    </article>
                </div>
                @endforeach
            </div>
        </div>
    </section>
    @endif
</main>

{{-- Image Modal --}}
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content bg-transparent border-0">
            <div class="modal-body p-0 position-relative">
                <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" 
                        data-bs-dismiss="modal"></button>
                <img id="modalImage" src="" alt="" class="w-100 rounded">
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function openImageModal(src) {
        document.getElementById('modalImage').src = src;
        new bootstrap.Modal(document.getElementById('imageModal')).show();
    }
</script>
@endpush
@endsection
