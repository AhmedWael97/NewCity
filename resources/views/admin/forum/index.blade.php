@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <h2 class="mb-4"><i class="fas fa-comments"></i> إدارة المنتدى</h2>

    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card text-white bg-primary">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $stats['total_categories'] }}</h3>
                            <small>الأقسام</small>
                        </div>
                        <i class="fas fa-folder fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-success">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $stats['total_threads'] }}</h3>
                            <small>المواضيع</small>
                        </div>
                        <i class="fas fa-comments fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-info">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $stats['total_posts'] }}</h3>
                            <small>الردود</small>
                        </div>
                        <i class="fas fa-reply fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card text-white bg-warning">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h3 class="mb-0">{{ $stats['pending_threads'] }}</h3>
                            <small>مواضيع بانتظار الموافقة</small>
                        </div>
                        <i class="fas fa-clock fa-2x opacity-50"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-fire"></i> أكثر المواضيع نشاطاً</h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>الموضوع</th>
                                    <th class="text-center">الردود</th>
                                    <th class="text-center">المشاهدات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($popular_threads as $thread)
                                <tr>
                                    <td>
                                        <a href="{{ route('forum.thread', $thread) }}" target="_blank">
                                            {{ Str::limit($thread->title, 50) }}
                                        </a>
                                        <br>
                                        <small class="text-muted">{{ $thread->category->name }}</small>
                                    </td>
                                    <td class="text-center">{{ $thread->replies_count }}</td>
                                    <td class="text-center">{{ $thread->views_count }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-exclamation-triangle"></i> البلاغات المعلقة</h5>
                    <a href="{{ route('admin.forum.reports') }}" class="btn btn-sm btn-primary">عرض الكل</a>
                </div>
                <div class="card-body">
                    @if($pending_reports->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>النوع</th>
                                    <th>السبب</th>
                                    <th>التاريخ</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pending_reports as $report)
                                <tr>
                                    <td>
                                        @if($report->reportable_type === 'App\\Models\\ForumThread')
                                        <span class="badge bg-info">موضوع</span>
                                        @else
                                        <span class="badge bg-secondary">رد</span>
                                        @endif
                                    </td>
                                    <td>{{ $report->reason_label }}</td>
                                    <td>{{ $report->created_at->diffForHumans() }}</td>
                                    <td>
                                        <a href="{{ route('admin.forum.reports.show', $report) }}" 
                                           class="btn btn-sm btn-outline-primary">
                                            مراجعة
                                        </a>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <p class="text-muted text-center mb-0">لا توجد بلاغات معلقة</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-3">
        <div class="col-12">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="fas fa-clock"></i> آخر المواضيع</h5>
                    <a href="{{ route('admin.forum.threads') }}" class="btn btn-sm btn-primary">عرض الكل</a>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>الموضوع</th>
                                    <th>القسم</th>
                                    <th>الكاتب</th>
                                    <th>الحالة</th>
                                    <th>التاريخ</th>
                                    <th>الإجراءات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent_threads as $thread)
                                <tr>
                                    <td>
                                        <a href="{{ route('forum.thread', $thread) }}" target="_blank">
                                            {{ Str::limit($thread->title, 60) }}
                                        </a>
                                    </td>
                                    <td>{{ $thread->category->name }}</td>
                                    <td>{{ $thread->user->name }}</td>
                                    <td>
                                        @if($thread->is_approved)
                                        <span class="badge bg-success">منشور</span>
                                        @else
                                        <span class="badge bg-warning">معلق</span>
                                        @endif
                                        @if($thread->is_pinned)
                                        <span class="badge bg-primary">مثبت</span>
                                        @endif
                                        @if($thread->is_locked)
                                        <span class="badge bg-secondary">مغلق</span>
                                        @endif
                                    </td>
                                    <td>{{ $thread->created_at->diffForHumans() }}</td>
                                    <td>
                                        <div class="btn-group btn-group-sm">
                                            @if(!$thread->is_approved)
                                            <form action="{{ route('admin.forum.threads.approve', $thread) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-success" title="الموافقة">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                            @endif
                                            <form action="{{ route('admin.forum.threads.pin', $thread) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-primary" title="{{ $thread->is_pinned ? 'إلغاء التثبيت' : 'تثبيت' }}">
                                                    <i class="fas fa-thumbtack"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.forum.threads.lock', $thread) }}" method="POST" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-warning" title="{{ $thread->is_locked ? 'فتح' : 'إغلاق' }}">
                                                    <i class="fas fa-lock"></i>
                                                </button>
                                            </form>
                                            <form action="{{ route('admin.forum.threads.destroy', $thread) }}" method="POST" class="d-inline" onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger" title="حذف">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
