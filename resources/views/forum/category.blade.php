@extends('layouts.app')

@section('content')
<div class="container my-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('forum.index') }}">المنتدى</a></li>
            <li class="breadcrumb-item active">{{ $category->name }}</li>
        </ol>
    </nav>

    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow-sm">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <div class="d-flex align-items-center mb-2">
                                <i class="{{ $category->icon }} fa-2x me-3" style="color: {{ $category->color }};"></i>
                                <div>
                                    <h3 class="mb-0">{{ $category->name }}</h3>
                                    <p class="text-muted mb-0">{{ $category->description }}</p>
                                </div>
                            </div>
                        </div>
                        @auth
                        <a href="{{ route('forum.createThread', $category) }}" class="btn btn-primary">
                            <i class="fas fa-plus"></i> موضوع جديد
                        </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row mb-3">
        <div class="col-12">
            <div class="btn-group" role="group">
                <a href="{{ route('forum.category', ['category' => $category, 'sort' => 'recent']) }}" 
                   class="btn btn-sm {{ request('sort', 'recent') == 'recent' ? 'btn-primary' : 'btn-outline-primary' }}">
                    <i class="fas fa-clock"></i> الأحدث
                </a>
                <a href="{{ route('forum.category', ['category' => $category, 'sort' => 'popular']) }}" 
                   class="btn btn-sm {{ request('sort') == 'popular' ? 'btn-primary' : 'btn-outline-primary' }}">
                    <i class="fas fa-fire"></i> الأكثر نشاطاً
                </a>
            </div>
        </div>
    </div>

    @forelse($threads as $thread)
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-auto">
                    <img src="{{ $thread->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($thread->user->name) }}" 
                         alt="{{ $thread->user->name }}" 
                         class="rounded-circle" 
                         width="50" 
                         height="50">
                </div>
                <div class="col">
                    <div class="d-flex align-items-center mb-1">
                        @if($thread->is_pinned)
                        <span class="badge bg-warning text-dark me-2">
                            <i class="fas fa-thumbtack"></i> مثبت
                        </span>
                        @endif
                        @if($thread->is_locked)
                        <span class="badge bg-secondary me-2">
                            <i class="fas fa-lock"></i> مغلق
                        </span>
                        @endif
                        <a href="{{ route('forum.thread', $thread) }}" class="text-decoration-none">
                            <h5 class="mb-0">{{ $thread->title }}</h5>
                        </a>
                    </div>
                    <p class="text-muted mb-1 small">{{ Str::limit($thread->body, 150) }}</p>
                    <small class="text-muted">
                        بواسطة <strong>{{ $thread->user->name }}</strong>
                        • {{ $thread->created_at->diffForHumans() }}
                        @if($thread->city)
                        • <i class="fas fa-map-marker-alt"></i> {{ $thread->city->name }}
                        @endif
                    </small>
                </div>
                <div class="col-auto text-center">
                    <div class="mb-1">
                        <strong>{{ $thread->replies_count }}</strong>
                        <small class="text-muted d-block">رد</small>
                    </div>
                </div>
                <div class="col-auto text-center">
                    <div class="mb-1">
                        <strong>{{ $thread->views_count }}</strong>
                        <small class="text-muted d-block">مشاهدة</small>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="card shadow-sm">
        <div class="card-body text-center py-5">
            <i class="fas fa-comments fa-3x text-muted mb-3"></i>
            <h5>لا توجد مواضيع في هذا القسم</h5>
            <p class="text-muted">كن أول من يبدأ نقاشاً جديداً!</p>
            @auth
            <a href="{{ route('forum.createThread', $category) }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> إنشاء موضوع جديد
            </a>
            @endauth
        </div>
    </div>
    @endforelse

    @if($threads->hasPages())
    <div class="d-flex justify-content-center mt-4">
        {{ $threads->links() }}
    </div>
    @endif
</div>
@endsection
