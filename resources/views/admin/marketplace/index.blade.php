@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-shopping-cart"></i> إدارة إعلانات السوق</h2>
        <div>
            <a href="{{ route('admin.marketplace.statistics') }}" class="btn btn-info">
                <i class="fas fa-chart-bar"></i> الإحصائيات
            </a>
        </div>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show">
        <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form action="{{ route('admin.marketplace.index') }}" method="GET">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">البحث</label>
                        <input type="text" name="search" class="form-control" placeholder="البحث..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-select">
                            <option value="">الكل</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                            <option value="sold" {{ request('status') == 'sold' ? 'selected' : '' }}>مباع</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">المدينة</label>
                        <select name="city_id" class="form-select">
                            <option value="">الكل</option>
                            @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">التصنيف</label>
                        <select name="category_id" class="form-select">
                            <option value="">الكل</option>
                            @foreach($categories as $category)
                            <option value="{{ $category->id }}" {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                {{ $category->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label class="form-label">مميز</label>
                        <select name="sponsored" class="form-select">
                            <option value="">الكل</option>
                            <option value="1" {{ request('sponsored') == '1' ? 'selected' : '' }}>نعم</option>
                            <option value="0" {{ request('sponsored') == '0' ? 'selected' : '' }}>لا</option>
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-search"></i> بحث
                        </button>
                        <a href="{{ route('admin.marketplace.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> إعادة
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Bulk Actions -->
    <div class="card mb-3">
        <div class="card-body">
            <form action="{{ route('admin.marketplace.bulk-action') }}" method="POST" id="bulkActionForm">
                @csrf
                <div class="row align-items-end">
                    <div class="col-md-3">
                        <label class="form-label">الإجراء الجماعي</label>
                        <select name="action" class="form-select" id="bulkAction" required>
                            <option value="">اختر إجراء...</option>
                            <option value="approve">الموافقة</option>
                            <option value="reject">الرفض</option>
                            <option value="delete">الحذف</option>
                        </select>
                    </div>
                    <div class="col-md-6" id="rejectionReasonField" style="display: none;">
                        <label class="form-label">سبب الرفض</label>
                        <input type="text" name="rejection_reason" class="form-control" 
                               placeholder="أدخل سبب الرفض...">
                    </div>
                    <div class="col-md-3">
                        <button type="submit" class="btn btn-warning" onclick="return confirmBulkAction()">
                            <i class="fas fa-bolt"></i> تنفيذ الإجراء
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Items Table -->
    <div class="card shadow">
        <div class="card-body">
            @if($items->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th width="30">
                                <input type="checkbox" id="selectAll" onclick="toggleSelectAll()">
                            </th>
                            <th>الإعلان</th>
                            <th>المعلن</th>
                            <th>السعر</th>
                            <th>الحالة</th>
                            <th>المشاهدات</th>
                            <th>التاريخ</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $item)
                        <tr>
                            <td>
                                <input type="checkbox" name="item_ids[]" value="{{ $item->id }}" 
                                       class="item-checkbox" form="bulkActionForm">
                            </td>
                            <td>
                                <div class="d-flex align-items-center">
                                    @if($item->images && count($item->images) > 0)
                                    <img src="{{ $item->images[0] }}" alt="{{ $item->title }}" 
                                         style="width: 60px; height: 60px; object-fit: cover; border-radius: 8px;" class="me-3">
                                    @endif
                                    <div>
                                        <strong>{{ Str::limit($item->title, 40) }}</strong>
                                        @if($item->is_sponsored && $item->sponsored_until > now())
                                        <span class="badge bg-warning text-dark ms-1">
                                            <i class="fas fa-star"></i> مميز
                                        </span>
                                        @endif
                                        <br>
                                        <small class="text-muted">
                                            <i class="fas fa-map-marker-alt"></i> {{ $item->city->name }} •
                                            <i class="fas fa-tag"></i> {{ $item->category->name }}
                                        </small>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <strong>{{ $item->user->name }}</strong><br>
                                <small class="text-muted">{{ $item->user->phone }}</small>
                            </td>
                            <td>
                                <strong class="text-primary">{{ number_format($item->price, 0) }}</strong>
                            </td>
                            <td>
                                @switch($item->status)
                                    @case('active')
                                        <span class="badge bg-success">نشط</span>
                                        @break
                                    @case('pending')
                                        <span class="badge bg-warning">قيد المراجعة</span>
                                        @break
                                    @case('rejected')
                                        <span class="badge bg-danger">مرفوض</span>
                                        @if($item->rejection_reason)
                                        <br><small class="text-danger">{{ $item->rejection_reason }}</small>
                                        @endif
                                        @break
                                    @case('sold')
                                        <span class="badge bg-secondary">مباع</span>
                                        @break
                                @endswitch
                            </td>
                            <td>
                                <strong>{{ $item->view_count }}</strong> / {{ $item->max_views + $item->sponsored_views_boost }}
                            </td>
                            <td>
                                <small>{{ $item->created_at->format('Y-m-d') }}</small>
                            </td>
                            <td>
                                <div class="btn-group btn-group-sm">
                                    <a href="{{ route('admin.marketplace.show', $item->id) }}" class="btn btn-info" title="عرض">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @if($item->status === 'pending')
                                    <form action="{{ route('admin.marketplace.approve', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="btn btn-success" title="الموافقة">
                                            <i class="fas fa-check"></i>
                                        </button>
                                    </form>
                                    <button type="button" class="btn btn-warning" title="الرفض" 
                                            onclick="showRejectModal({{ $item->id }}, '{{ $item->title }}')">
                                        <i class="fas fa-times"></i>
                                    </button>
                                    @endif
                                    <form action="{{ route('admin.marketplace.destroy', $item->id) }}" method="POST" class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" title="حذف"
                                                onclick="return confirm('هل أنت متأكد من حذف هذا الإعلان؟')">
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

            <!-- Pagination -->
            <div class="mt-3">
                {{ $items->links() }}
            </div>
            @else
            <div class="text-center py-5">
                <i class="fas fa-inbox fa-5x text-muted mb-3"></i>
                <h4>لا توجد إعلانات</h4>
                <p class="text-muted">لا توجد إعلانات تطابق معايير البحث</p>
            </div>
            @endif
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form id="rejectForm" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">رفض الإعلان</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>رفض الإعلان: <strong id="rejectItemTitle"></strong></p>
                    <div class="mb-3">
                        <label class="form-label">سبب الرفض <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" class="form-control" rows="3" 
                                  placeholder="أدخل سبب الرفض..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i> رفض
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
// Toggle select all checkboxes
function toggleSelectAll() {
    const selectAll = document.getElementById('selectAll');
    const checkboxes = document.querySelectorAll('.item-checkbox');
    checkboxes.forEach(cb => cb.checked = selectAll.checked);
}

// Show/hide rejection reason field based on bulk action
document.getElementById('bulkAction')?.addEventListener('change', function() {
    const rejectionField = document.getElementById('rejectionReasonField');
    rejectionField.style.display = this.value === 'reject' ? 'block' : 'none';
});

// Confirm bulk action
function confirmBulkAction() {
    const action = document.getElementById('bulkAction').value;
    const checked = document.querySelectorAll('.item-checkbox:checked').length;
    
    if (checked === 0) {
        alert('يرجى اختيار إعلان واحد على الأقل');
        return false;
    }
    
    if (action === 'reject') {
        const reason = document.querySelector('input[name="rejection_reason"]').value;
        if (!reason) {
            alert('يرجى إدخال سبب الرفض');
            return false;
        }
    }
    
    const actionText = action === 'approve' ? 'الموافقة على' : (action === 'reject' ? 'رفض' : 'حذف');
    return confirm(`هل أنت متأكد من ${actionText} ${checked} إعلان؟`);
}

// Show reject modal
function showRejectModal(itemId, itemTitle) {
    document.getElementById('rejectItemTitle').textContent = itemTitle;
    document.getElementById('rejectForm').action = `/admin/marketplace/${itemId}/reject`;
    new bootstrap.Modal(document.getElementById('rejectModal')).show();
}
</script>
@endsection
