@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h2><i class="fas fa-comments"></i> المنتدى</h2>
                @auth
                <a href="{{ route('forum.category', $categories->first()) }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> موضوع جديد
                </a>
                @endauth
            </div>
            <p class="text-muted">شارك أفكارك وتجاربك مع المجتمع</p>
        </div>
    </div>

    @foreach($categories as $category)
    <div class="card shadow-sm mb-3">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-auto">
                    <div class="forum-icon" style="background: {{ $category->color }}20;">
                        <i class="{{ $category->icon }} fa-2x" style="color: {{ $category->color }};"></i>
                    </div>
                </div>
                <div class="col">
                    <a href="{{ route('forum.category', $category) }}" class="text-decoration-none">
                        <h5 class="mb-1">{{ $category->name }}</h5>
                    </a>
                    <p class="text-muted mb-1 small">{{ $category->description }}</p>
                    @if($category->city)
                    <span class="badge bg-info text-dark">
                        <i class="fas fa-map-marker-alt"></i> {{ $category->city->name }}
                    </span>
                    @endif
                </div>
                <div class="col-auto text-center">
                    <div class="mb-1">
                        <strong>{{ $category->threads_count }}</strong>
                        <small class="text-muted d-block">موضوع</small>
                    </div>
                </div>
                <div class="col-auto text-center">
                    <div class="mb-1">
                        <strong>{{ $category->posts_count }}</strong>
                        <small class="text-muted d-block">رد</small>
                    </div>
                </div>
                <div class="col-md-3">
                    @if($category->latestThread)
                    <small class="text-muted">
                        <i class="fas fa-comment"></i> آخر نشاط:<br>
                        <a href="{{ route('forum.thread', $category->latestThread) }}">
                            {{ Str::limit($category->latestThread->title, 40) }}
                        </a>
                        <br>
                        <span class="text-muted">{{ $category->latestThread->last_activity_at?->diffForHumans() }}</span>
                    </small>
                    @else
                    <small class="text-muted">لا توجد مواضيع بعد</small>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<style>
.forum-icon {
    width: 60px;
    height: 60px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
}
.card {
    transition: all 0.3s ease;
}
.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(0,0,0,0.1) !important;
}
</style>
@endsection
