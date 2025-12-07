@extends('layouts.admin')

@section('title', 'اقتراحات المدن')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2">اقتراحات المدن</h1>
            <p class="text-muted mb-0">إدارة ومراجعة اقتراحات المدن من المستخدمين</p>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3">
                                <i class="fas fa-list fa-lg"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">إجمالي الاقتراحات</h6>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-3">
                                <i class="fas fa-clock fa-lg"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">قيد المراجعة</h6>
                            <h3 class="mb-0">{{ $stats['pending'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-success bg-opacity-10 text-success rounded-circle p-3">
                                <i class="fas fa-check-circle fa-lg"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">موافق عليها</h6>
                            <h3 class="mb-0">{{ $stats['approved'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <div class="flex-shrink-0">
                            <div class="bg-danger bg-opacity-10 text-danger rounded-circle p-3">
                                <i class="fas fa-times-circle fa-lg"></i>
                            </div>
                        </div>
                        <div class="flex-grow-1 ms-3">
                            <h6 class="text-muted mb-1">مرفوضة</h6>
                            <h3 class="mb-0">{{ $stats['rejected'] }}</h3>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Message -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle me-2"></i>
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Filters -->
    <div class="card border-0 shadow-sm mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.city-suggestions.index') }}">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label">بحث</label>
                        <input type="text" name="search" class="form-control" 
                               placeholder="ابحث عن اسم المدينة، رقم الهاتف..."
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-select">
                            <option value="">الكل</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>موافق عليها</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوضة</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search me-1"></i> بحث
                        </button>
                        <a href="{{ route('admin.city-suggestions.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo me-1"></i> إعادة تعيين
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Suggestions Table -->
    <div class="card border-0 shadow-sm">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>المدينة</th>
                            <th>رقم الهاتف</th>
                            <th>رابط المجموعة</th>
                            <th>الحالة</th>
                            <th>تاريخ الإرسال</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($suggestions as $suggestion)
                            <tr>
                                <td>
                                    <strong>{{ $suggestion->city_name }}</strong>
                                </td>
                                <td>
                                    <a href="tel:{{ $suggestion->phone }}" class="text-decoration-none">
                                        <i class="fas fa-phone me-1"></i>
                                        {{ $suggestion->phone }}
                                    </a>
                                </td>
                                <td>
                                    <a href="{{ $suggestion->group_url }}" target="_blank" class="text-decoration-none" title="{{ $suggestion->group_url }}">
                                        <i class="fas fa-external-link-alt me-1"></i>
                                        رابط المجموعة
                                    </a>
                                </td>
                                <td>
                                    @if($suggestion->status == 'pending')
                                        <span class="badge bg-warning">
                                            <i class="fas fa-clock me-1"></i>قيد المراجعة
                                        </span>
                                    @elseif($suggestion->status == 'approved')
                                        <span class="badge bg-success">
                                            <i class="fas fa-check-circle me-1"></i>موافق عليها
                                        </span>
                                    @else
                                        <span class="badge bg-danger">
                                            <i class="fas fa-times-circle me-1"></i>مرفوضة
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    <small class="text-muted">
                                        {{ $suggestion->created_at->diffForHumans() }}
                                    </small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.city-suggestions.show', $suggestion) }}" 
                                           class="btn btn-sm btn-outline-primary" title="عرض التفاصيل">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        
                                        @if($suggestion->status == 'pending')
                                            <button type="button" class="btn btn-sm btn-outline-success" 
                                                    onclick="updateStatus({{ $suggestion->id }}, 'approved')" 
                                                    title="موافقة">
                                                <i class="fas fa-check"></i>
                                            </button>
                                            <button type="button" class="btn btn-sm btn-outline-danger" 
                                                    onclick="updateStatus({{ $suggestion->id }}, 'rejected')" 
                                                    title="رفض">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        @endif
                                        
                                        <button type="button" class="btn btn-sm btn-outline-danger" 
                                                onclick="deleteSuggestion({{ $suggestion->id }})" 
                                                title="حذف">
                                            <i class="fas fa-trash"></i>
                                        </button>
                                    </div>

                                    <!-- Hidden forms -->
                                    <form id="status-form-{{ $suggestion->id }}" 
                                          action="{{ route('admin.city-suggestions.update-status', $suggestion) }}" 
                                          method="POST" style="display: none;">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" id="status-input-{{ $suggestion->id }}">
                                    </form>

                                    <form id="delete-form-{{ $suggestion->id }}" 
                                          action="{{ route('admin.city-suggestions.destroy', $suggestion) }}" 
                                          method="POST" style="display: none;">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5">
                                    <i class="fas fa-inbox fa-3x text-muted mb-3"></i>
                                    <p class="text-muted mb-0">لا توجد اقتراحات</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($suggestions->hasPages())
                <div class="mt-4">
                    {{ $suggestions->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
function updateStatus(id, status) {
    if (confirm('هل أنت متأكد من تغيير حالة هذا الاقتراح؟')) {
        document.getElementById('status-input-' + id).value = status;
        document.getElementById('status-form-' + id).submit();
    }
}

function deleteSuggestion(id) {
    if (confirm('هل أنت متأكد من حذف هذا الاقتراح؟ لا يمكن التراجع عن هذا الإجراء.')) {
        document.getElementById('delete-form-' + id).submit();
    }
}
</script>
@endpush
@endsection
