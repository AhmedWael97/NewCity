@extends('layouts.app')

@section('content')
<div class="container my-5">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('forum.index') }}">المنتدى</a></li>
            <li class="breadcrumb-item"><a href="{{ route('forum.category', $thread->category) }}">{{ $thread->category->name }}</a></li>
            <li class="breadcrumb-item active">{{ Str::limit($thread->title, 50) }}</li>
        </ol>
    </nav>

    <div class="card shadow-sm mb-4">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div class="flex-grow-1">
                    <div class="d-flex align-items-center mb-2">
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
                        <h3 class="mb-0">{{ $thread->title }}</h3>
                    </div>
                    <div class="text-muted small">
                        <i class="fas fa-eye"></i> {{ $thread->views_count }} مشاهدة
                        • <i class="fas fa-comments"></i> {{ $thread->replies_count }} رد
                        • {{ $thread->created_at->diffForHumans() }}
                    </div>
                </div>
                @auth
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                        <i class="fas fa-ellipsis-v"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-end">
                        @if($thread->user_id === auth()->id())
                        <li><a class="dropdown-item" href="{{ route('forum.thread.edit', $thread) }}">
                            <i class="fas fa-edit"></i> تعديل
                        </a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form action="{{ route('forum.thread.destroy', $thread) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الموضوع؟')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="fas fa-trash"></i> حذف
                                </button>
                            </form>
                        </li>
                        @endif
                        <li>
                            @if($thread->isSubscribedBy(auth()->user()))
                            <form action="{{ route('forum.thread.unsubscribe', $thread) }}" method="POST" class="subscribe-form">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-bell-slash"></i> إلغاء المتابعة
                                </button>
                            </form>
                            @else
                            <form action="{{ route('forum.thread.subscribe', $thread) }}" method="POST" class="subscribe-form">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-bell"></i> متابعة الموضوع
                                </button>
                            </form>
                            @endif
                        </li>
                    </ul>
                </div>
                @endauth
            </div>

            <div class="row">
                <div class="col-auto">
                    <img src="{{ $thread->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($thread->user->name) }}" 
                         alt="{{ $thread->user->name }}" 
                         class="rounded-circle" 
                         width="60" 
                         height="60">
                </div>
                <div class="col">
                    <div class="mb-2">
                        <strong>{{ $thread->user->name }}</strong>
                        <small class="text-muted">
                            @if($thread->user->role === 'shop_owner')
                            <span class="badge bg-success">صاحب متجر</span>
                            @endif
                        </small>
                    </div>
                    <div class="thread-body">
                        {!! nl2br(e($thread->body)) !!}
                    </div>
                </div>
            </div>
        </div>
    </div>

    <h5 class="mb-3">الردود ({{ $posts->total() }})</h5>

    @foreach($posts as $post)
    <div class="card shadow-sm mb-3" id="post-{{ $post->id }}">
        <div class="card-body">
            <div class="row">
                <div class="col-auto">
                    <img src="{{ $post->user->avatar ?? 'https://ui-avatars.com/api/?name=' . urlencode($post->user->name) }}" 
                         alt="{{ $post->user->name }}" 
                         class="rounded-circle" 
                         width="50" 
                         height="50">
                </div>
                <div class="col">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div>
                            <strong>{{ $post->user->name }}</strong>
                            <small class="text-muted">• {{ $post->created_at->diffForHumans() }}</small>
                        </div>
                        @auth
                        <div class="dropdown">
                            <button class="btn btn-sm btn-link text-muted" type="button" data-bs-toggle="dropdown">
                                <i class="fas fa-ellipsis-v"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                @if($post->user_id === auth()->id())
                                <li><a class="dropdown-item" href="{{ route('forum.post.edit', $post) }}">
                                    <i class="fas fa-edit"></i> تعديل
                                </a></li>
                                <li>
                                    <form action="{{ route('forum.post.destroy', $post) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الرد؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="fas fa-trash"></i> حذف
                                        </button>
                                    </form>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                @endif
                                <li>
                                    <form action="{{ route('forum.post.report', $post) }}" method="POST" onsubmit="return confirm('هل تريد الإبلاغ عن هذا الرد؟')">
                                        @csrf
                                        <input type="hidden" name="reason" value="spam">
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-flag"></i> إبلاغ
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                        @endauth
                    </div>
                    <div class="post-body mb-2">
                        {!! nl2br(e($post->body)) !!}
                    </div>
                    @auth
                    <div class="d-flex align-items-center">
                        <button class="btn btn-sm btn-outline-success vote-btn {{ $post->isVotedByUser(auth()->user()) ? 'active' : '' }}" 
                                data-post-id="{{ $post->id }}">
                            <i class="fas fa-thumbs-up"></i> مفيد (<span class="vote-count">{{ $post->helpful_count }}</span>)
                        </button>
                    </div>
                    @else
                    <div class="text-muted small">
                        <i class="fas fa-thumbs-up"></i> {{ $post->helpful_count }} مفيد
                    </div>
                    @endauth
                </div>
            </div>
        </div>
    </div>
    @endforeach

    @if($posts->hasPages())
    <div class="d-flex justify-content-center mb-4">
        {{ $posts->links() }}
    </div>
    @endif

    @auth
    @if(!$thread->is_locked)
    <div class="card shadow-sm">
        <div class="card-body">
            <h5 class="mb-3">إضافة رد</h5>
            <form action="{{ route('forum.thread.storePost', $thread) }}" method="POST">
                @csrf
                <div class="mb-3">
                    <textarea name="body" class="form-control @error('body') is-invalid @enderror" 
                              rows="5" placeholder="اكتب ردك هنا..." required>{{ old('body') }}</textarea>
                    @error('body')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-paper-plane"></i> إرسال الرد
                </button>
            </form>
        </div>
    </div>
    @else
    <div class="alert alert-warning">
        <i class="fas fa-lock"></i> هذا الموضوع مغلق ولا يمكن إضافة ردود جديدة.
    </div>
    @endif
    @else
    <div class="alert alert-info">
        <i class="fas fa-info-circle"></i> يجب <a href="{{ route('login') }}">تسجيل الدخول</a> لإضافة رد.
    </div>
    @endauth
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Vote functionality
    $('.vote-btn').click(function(e) {
        e.preventDefault();
        const btn = $(this);
        const postId = btn.data('post-id');
        
        $.ajax({
            url: `/forum/post/${postId}/vote`,
            type: 'POST',
            data: {
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if(response.success) {
                    btn.toggleClass('active');
                    btn.find('.vote-count').text(response.helpful_count);
                }
            }
        });
    });

    // Subscribe functionality
    $('.subscribe-form').submit(function(e) {
        e.preventDefault();
        const form = $(this);
        
        $.ajax({
            url: form.attr('action'),
            type: form.find('input[name="_method"]').length ? 'DELETE' : 'POST',
            data: form.serialize(),
            success: function(response) {
                if(response.success) {
                    location.reload();
                }
            }
        });
    });
});
</script>
@endpush
@endsection
