@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">تقييمات الموقع</h1>
    </div>

    {{-- Statistics Cards --}}
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">إجمالي التقييمات</h5>
                    <h2>{{ number_format($stats['total']) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">متوسط التقييم</h5>
                    <h2>{{ $stats['average_rating'] }} <i class="fas fa-star"></i></h2>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">إيجابي (4-5)</h5>
                    <h2>{{ number_format($stats['positive']) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <h5 class="card-title">سلبي (1-2)</h5>
                    <h2>{{ number_format($stats['negative']) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h5 class="card-title">اليوم</h5>
                    <h2>{{ number_format($stats['today']) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-dark text-white">
                <div class="card-body">
                    <h5 class="card-title">هذا الأسبوع</h5>
                    <h2>{{ number_format($stats['this_week']) }}</h2>
                </div>
            </div>
        </div>
    </div>

    {{-- Rating Distribution --}}
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">توزيع التقييمات</h5>
        </div>
        <div class="card-body">
            <div class="row">
                @for($i = 5; $i >= 1; $i--)
                <div class="col-md-12 mb-3">
                    <div class="d-flex align-items-center">
                        <div style="width: 80px;">
                            <strong>{{ $i }} <i class="fas fa-star text-warning"></i></strong>
                        </div>
                        <div class="flex-grow-1">
                            <div class="progress" style="height: 25px;">
                                @php
                                    $percentage = $stats['total'] > 0 ? ($stats['rating_distribution'][$i] / $stats['total']) * 100 : 0;
                                @endphp
                                <div class="progress-bar {{ $i >= 4 ? 'bg-success' : ($i == 3 ? 'bg-warning' : 'bg-danger') }}" 
                                     style="width: {{ $percentage }}%">
                                    {{ number_format($percentage, 1) }}%
                                </div>
                            </div>
                        </div>
                        <div style="width: 80px;" class="text-end">
                            <strong>{{ $stats['rating_distribution'][$i] }}</strong>
                        </div>
                    </div>
                </div>
                @endfor
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.feedback.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">البحث</label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="البحث في التعليقات..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <label class="form-label">التقييم</label>
                    <select name="rating" class="form-select">
                        <option value="">الكل</option>
                        @for($i = 5; $i >= 1; $i--)
                            <option value="{{ $i }}" {{ request('rating') == $i ? 'selected' : '' }}>
                                {{ $i }} <span class="fas fa-star"></span>
                            </option>
                        @endfor
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">الترتيب</label>
                    <select name="sort" class="form-select">
                        <option value="latest" {{ request('sort') === 'latest' ? 'selected' : '' }}>الأحدث</option>
                        <option value="oldest" {{ request('sort') === 'oldest' ? 'selected' : '' }}>الأقدم</option>
                        <option value="highest" {{ request('sort') === 'highest' ? 'selected' : '' }}>الأعلى تقييماً</option>
                        <option value="lowest" {{ request('sort') === 'lowest' ? 'selected' : '' }}>الأقل تقييماً</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-2"></i>بحث
                    </button>
                </div>
                <div class="col-md-2">
                    <label class="form-label">&nbsp;</label>
                    <a href="{{ route('admin.feedback.index') }}" class="btn btn-secondary w-100">
                        <i class="fas fa-redo me-2"></i>إعادة تعيين
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Feedback List --}}
    <div class="row">
        @forelse($feedbacks as $feedback)
        <div class="col-md-6 mb-4">
            <div class="card h-100">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <div>
                        @for($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $i <= $feedback->rating ? 'text-warning' : 'text-muted' }}"></i>
                        @endfor
                        <strong class="ms-2">{{ $feedback->rating }}/5</strong>
                    </div>
                    <small class="text-muted">
                        <i class="fas fa-clock me-1"></i>
                        {{ $feedback->submitted_at->diffForHumans() }}
                    </small>
                </div>
                <div class="card-body">
                    @if($feedback->message)
                        <p class="card-text">{{ $feedback->message }}</p>
                    @else
                        <p class="card-text text-muted"><em>لا يوجد تعليق</em></p>
                    @endif

                    <div class="mt-3">
                        <small class="text-muted d-block">
                            <i class="fas fa-link me-1"></i>
                            <a href="{{ $feedback->page_url }}" target="_blank" class="text-decoration-none">
                                {{ Str::limit($feedback->page_url, 50) }}
                            </a>
                        </small>

                        @if($feedback->user)
                            <small class="text-muted d-block mt-1">
                                <i class="fas fa-user me-1"></i>
                                {{ $feedback->user->name }}
                            </small>
                        @elseif($feedback->email)
                            <small class="text-muted d-block mt-1">
                                <i class="fas fa-envelope me-1"></i>
                                {{ $feedback->email }}
                            </small>
                        @else
                            <small class="text-muted d-block mt-1">
                                <i class="fas fa-user-secret me-1"></i>
                                مجهول
                            </small>
                        @endif

                        <small class="text-muted d-block mt-1">
                            <i class="fas fa-network-wired me-1"></i>
                            {{ $feedback->ip_address }}
                        </small>
                    </div>
                </div>
                <div class="card-footer d-flex justify-content-between align-items-center">
                    <span class="badge {{ $feedback->rating >= 4 ? 'bg-success' : ($feedback->rating == 3 ? 'bg-warning' : 'bg-danger') }}">
                        {{ $feedback->rating >= 4 ? 'إيجابي' : ($feedback->rating == 3 ? 'محايد' : 'سلبي') }}
                    </span>

                    <form action="{{ route('admin.feedback.destroy', $feedback) }}" 
                          method="POST" 
                          onsubmit="return confirm('هل أنت متأكد من حذف هذا التقييم؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger">
                            <i class="fas fa-trash me-1"></i>حذف
                        </button>
                    </form>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center py-5">
                    <i class="fas fa-comments fa-3x text-muted mb-3"></i>
                    <p class="text-muted">لا توجد تقييمات</p>
                </div>
            </div>
        </div>
        @endforelse
    </div>

    @if($feedbacks->hasPages())
    <div class="mt-3">
        {{ $feedbacks->links() }}
    </div>
    @endif
</div>
@endsection
