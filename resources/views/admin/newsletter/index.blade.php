@extends('layouts.admin')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">إدارة النشرة البريدية</h1>
        <a href="{{ route('admin.newsletter.export') }}" class="btn btn-success">
            <i class="fas fa-download me-2"></i>
            تصدير المشتركين (CSV)
        </a>
    </div>

    {{-- Statistics Cards --}}
    <div class="row mb-4">
        <div class="col-md-2">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h5 class="card-title">إجمالي المشتركين</h5>
                    <h2>{{ number_format($stats['total']) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h5 class="card-title">نشط</h5>
                    <h2>{{ number_format($stats['active']) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-secondary text-white">
                <div class="card-body">
                    <h5 class="card-title">غير نشط</h5>
                    <h2>{{ number_format($stats['inactive']) }}</h2>
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
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h5 class="card-title">هذا الأسبوع</h5>
                    <h2>{{ number_format($stats['this_week']) }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card bg-dark text-white">
                <div class="card-body">
                    <h5 class="card-title">هذا الشهر</h5>
                    <h2>{{ number_format($stats['this_month']) }}</h2>
                </div>
            </div>
        </div>
    </div>

    {{-- Filters --}}
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.newsletter.index') }}" class="row g-3">
                <div class="col-md-4">
                    <label class="form-label">البحث</label>
                    <input type="text" name="search" class="form-control" 
                           placeholder="البحث بالبريد أو الاسم..." 
                           value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label">الحالة</label>
                    <select name="status" class="form-select">
                        <option value="">الكل</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>غير نشط</option>
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
                    <a href="{{ route('admin.newsletter.index') }}" class="btn btn-secondary w-100">
                        <i class="fas fa-redo me-2"></i>إعادة تعيين
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Subscribers Table --}}
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>البريد الإلكتروني</th>
                            <th>الاسم</th>
                            <th>تاريخ الاشتراك</th>
                            <th>الحالة</th>
                            <th>IP</th>
                            <th>إجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($subscribers as $subscriber)
                        <tr>
                            <td>{{ $subscriber->id }}</td>
                            <td>
                                <i class="fas fa-envelope text-primary me-2"></i>
                                {{ $subscriber->email }}
                            </td>
                            <td>{{ $subscriber->name ?: '-' }}</td>
                            <td>
                                <small class="text-muted">
                                    {{ $subscriber->subscribed_at->format('Y-m-d H:i') }}
                                    <br>
                                    <i class="fas fa-clock me-1"></i>{{ $subscriber->subscribed_at->diffForHumans() }}
                                </small>
                            </td>
                            <td>
                                @if($subscriber->is_active)
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-secondary">ملغي</span>
                                    <br>
                                    <small class="text-muted">{{ $subscriber->unsubscribed_at?->format('Y-m-d') }}</small>
                                @endif
                            </td>
                            <td>
                                <small class="text-muted">{{ $subscriber->ip_address }}</small>
                            </td>
                            <td>
                                @can('delete-newsletter')
                                <form action="{{ route('admin.newsletter.destroy', $subscriber) }}" 
                                      method="POST" 
                                      onsubmit="return confirm('هل أنت متأكد من حذف هذا المشترك؟')"
                                      class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-5">
                                <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                <p class="text-muted">لا توجد اشتراكات</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $subscribers->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
