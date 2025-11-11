@extends('layouts.admin')

@section('title', 'المتاجر المعلقة')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-clock"></i> المتاجر المعلقة
            <span class="badge badge-warning ms-2">{{ $shops->total() }}</span>
        </h1>
        <div>
            <a href="{{ route('admin.shops.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-list"></i> جميع المتاجر
            </a>
        </div>
    </div>

    <!-- Search -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">البحث في المتاجر المعلقة</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.shops.pending') }}">
                <div class="row">
                    <div class="col-md-8">
                        <label>البحث</label>
                        <input type="text" name="search" class="form-control" 
                               placeholder="اسم المتجر، الوصف، العنوان، اسم المالك..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-4">
                        <label>&nbsp;</label>
                        <div class="d-flex gap-2">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-search"></i> بحث
                            </button>
                            <a href="{{ route('admin.shops.pending') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> إعادة تعيين
                            </a>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Pending Shops Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">المتاجر في انتظار المراجعة</h6>
            @if($shops->count() > 0)
                <div class="dropdown">
                    <button class="btn btn-sm btn-outline-primary dropdown-toggle" type="button" 
                            data-bs-toggle="dropdown">
                        <i class="fas fa-cogs"></i> إجراءات متعددة
                    </button>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="#" onclick="bulkAction('approve')">
                            <i class="fas fa-check text-success"></i> الموافقة على الكل
                        </a></li>
                        <li><a class="dropdown-item" href="#" onclick="bulkAction('reject')">
                            <i class="fas fa-times text-danger"></i> رفض الكل
                        </a></li>
                    </ul>
                </div>
            @endif
        </div>
        <div class="card-body p-0">
            @if($shops->count() > 0)
                <div class="table-responsive">
                    <table class="table table-compact table-hover mb-0">
                        <thead class="table-dark">
                            <tr>
                                <th style="width: 40px;">
                                    <input type="checkbox" id="selectAll" class="form-check-input">
                                </th>
                                <th>المتجر</th>
                                <th class="d-none-md">المالك</th>
                                <th class="d-none-lg">المدينة</th>
                                <th class="d-none-lg">التصنيف</th>
                                <th class="d-none-sm">تاريخ التقديم</th>
                                <th style="width: 100px;">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($shops as $shop)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="shop_ids[]" value="{{ $shop->id }}" 
                                               class="form-check-input shop-checkbox">
                                    </td>
                                    <td>
                                        <div class="d-flex align-items-center">
                                            @if($shop->images && count($shop->images) > 0)
                                                <img src="{{ asset('storage/' . $shop->images[0]) }}" 
                                                     class="rounded me-2" style="width: 35px; height: 35px; object-fit: cover;">
                                            @else
                                                <div class="bg-light rounded me-2 d-flex align-items-center justify-content-center" 
                                                     style="width: 35px; height: 35px;">
                                                    <i class="fas fa-store text-muted"></i>
                                                </div>
                                            @endif
                                            <div class="grow">
                                                <div class="fw-bold text-truncate-md">{{ $shop->name }}</div>
                                                <small class="text-muted text-truncate-md">{{ Str::limit($shop->description, 40) }}</small>
                                                <!-- Mobile-only info -->
                                                <div class="d-block d-md-none">
                                                    <small class="text-muted">
                                                        {{ $shop->owner->name }} • {{ $shop->city->name }}
                                                    </small>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="d-none-md">
                                        <div class="text-truncate-sm">
                                            <div class="fw-bold">{{ $shop->owner->name }}</div>
                                            <small class="text-muted">{{ $shop->owner->email }}</small>
                                        </div>
                                    </td>
                                    <td class="d-none-lg">
                                        <span class="badge badge-info">{{ $shop->city->name }}</span>
                                    </td>
                                    <td class="d-none-lg">
                                        <span class="badge badge-secondary">{{ $shop->category->name }}</span>
                                    </td>
                                    <td class="d-none-sm">
                                        <div class="text-truncate-sm">
                                            <div>{{ $shop->created_at->format('Y-m-d') }}</div>
                                            <small class="text-muted">{{ $shop->created_at->diffForHumans() }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="dropdown action-dropdown">
                                            <button class="btn btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                                <i class="fas fa-ellipsis-v"></i>
                                            </button>
                                            <ul class="dropdown-menu dropdown-menu-end">
                                                <li>
                                                    <a class="dropdown-item" href="{{ route('admin.shops.show', $shop) }}">
                                                        <i class="fas fa-eye text-info"></i>
                                                        عرض التفاصيل
                                                    </a>
                                                </li>
                                                <li><hr class="dropdown-divider"></li>
                                                <li>
                                                    <form method="POST" action="{{ route('admin.shops.verify', $shop) }}" 
                                                          class="d-inline w-100"
                                                          onsubmit="return confirm('هل أنت متأكد من الموافقة على هذا المتجر؟')">
                                                        @csrf
                                                        <button type="submit" class="dropdown-item">
                                                            <i class="fas fa-check text-success"></i>
                                                            الموافقة على المتجر
                                                        </button>
                                                    </form>
                                                </li>
                                                <li>
                                                    <button type="button" class="dropdown-item text-danger" 
                                                            onclick="rejectShop({{ $shop->id }})">
                                                        <i class="fas fa-times text-danger"></i>
                                                        رفض المتجر
                                                    </button>
                                                </li>
                                            </ul>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="card-footer bg-light border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="pagination-info">
                            عرض {{ $shops->firstItem() }} إلى {{ $shops->lastItem() }} من {{ $shops->total() }} متجر
                        </div>
                        <div>
                            {{ $shops->links() }}
                        </div>
                    </div>
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-clock fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">لا توجد متاجر معلقة</h5>
                    <p class="text-muted">جميع المتاجر تم مراجعتها والموافقة عليها أو رفضها.</p>
                    <a href="{{ route('admin.shops.index') }}" class="btn btn-primary">
                        <i class="fas fa-list"></i> عرض جميع المتاجر
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Reject Shop Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">رفض المتجر</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="rejectForm" method="POST">
                @csrf
                @method('POST')
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="rejection_reason" class="form-label">سبب الرفض</label>
                        <textarea id="rejection_reason" name="rejection_reason" class="form-control" 
                                  rows="4" placeholder="اكتب سبب رفض المتجر..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger">رفض المتجر</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
// Select All functionality
document.getElementById('selectAll').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.shop-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Reject Shop functionality
function rejectShop(shopId) {
    const modal = new bootstrap.Modal(document.getElementById('rejectModal'));
    const form = document.getElementById('rejectForm');
    form.action = `/admin/shops/${shopId}/reject`;
    modal.show();
}

// Bulk Actions
function bulkAction(action) {
    const checkedBoxes = document.querySelectorAll('.shop-checkbox:checked');
    if (checkedBoxes.length === 0) {
        alert('يرجى اختيار متجر واحد على الأقل');
        return;
    }

    const shopIds = Array.from(checkedBoxes).map(cb => cb.value);
    const actionText = action === 'approve' ? 'الموافقة على' : 'رفض';
    
    if (confirm(`هل أنت متأكد من ${actionText} ${shopIds.length} متجر؟`)) {
        // Create form and submit
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = '{{ route("admin.shops.bulk-action") }}';
        
        // Add CSRF token
        const csrfInput = document.createElement('input');
        csrfInput.type = 'hidden';
        csrfInput.name = '_token';
        csrfInput.value = '{{ csrf_token() }}';
        form.appendChild(csrfInput);
        
        // Add action
        const actionInput = document.createElement('input');
        actionInput.type = 'hidden';
        actionInput.name = 'action';
        actionInput.value = action;
        form.appendChild(actionInput);
        
        // Add shop IDs
        shopIds.forEach(id => {
            const idInput = document.createElement('input');
            idInput.type = 'hidden';
            idInput.name = 'shop_ids[]';
            idInput.value = id;
            form.appendChild(idInput);
        });
        
        document.body.appendChild(form);
        form.submit();
    }
}
</script>
@endsection