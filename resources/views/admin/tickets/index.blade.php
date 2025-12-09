@extends('layouts.admin')

@section('title', 'إدارة تذاكر الدعم')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">إدارة تذاكر الدعم</h1>
        <div>
            <a href="{{ route('admin.tickets.analytics') }}" class="btn btn-outline-info">
                <i class="fas fa-chart-bar"></i> التحليلات
            </a>
            <a href="{{ route('admin.tickets.export') }}" class="btn btn-outline-success">
                <i class="fas fa-download"></i> تصدير CSV
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">إجمالي التذاكر</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_tickets']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-ticket-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">مفتوحة</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['open_tickets']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-folder-open fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">قيد المعالجة</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['in_progress_tickets']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-cog fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">محلولة</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['resolved_tickets']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-secondary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">مغلقة</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['closed_tickets']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-archive fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-2 col-md-4 mb-4">
            <div class="card border-left-dark shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">متوسط الرد (ساعة)</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['avg_response_time'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">فلترة التذاكر</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.tickets.index') }}">
                <div class="row">
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="status">الحالة</label>
                            <select name="status" id="status" class="form-control">
                                <option value="">جميع الحالات</option>
                                <option value="open" {{ request('status') == 'open' ? 'selected' : '' }}>مفتوحة</option>
                                <option value="in_progress" {{ request('status') == 'in_progress' ? 'selected' : '' }}>قيد المعالجة</option>
                                <option value="resolved" {{ request('status') == 'resolved' ? 'selected' : '' }}>محلولة</option>
                                <option value="closed" {{ request('status') == 'closed' ? 'selected' : '' }}>مغلقة</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="priority">الأولوية</label>
                            <select name="priority" id="priority" class="form-control">
                                <option value="">جميع الأولويات</option>
                                <option value="low" {{ request('priority') == 'low' ? 'selected' : '' }}>منخفضة</option>
                                <option value="normal" {{ request('priority') == 'normal' ? 'selected' : '' }}>عادية</option>
                                <option value="high" {{ request('priority') == 'high' ? 'selected' : '' }}>عالية</option>
                                <option value="urgent" {{ request('priority') == 'urgent' ? 'selected' : '' }}>عاجلة</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="category">الفئة</label>
                            <select name="category" id="category" class="form-control">
                                <option value="">جميع الفئات</option>
                                <option value="technical" {{ request('category') == 'technical' ? 'selected' : '' }}>تقني</option>
                                <option value="billing" {{ request('category') == 'billing' ? 'selected' : '' }}>فوترة</option>
                                <option value="general" {{ request('category') == 'general' ? 'selected' : '' }}>عام</option>
                                <option value="complaint" {{ request('category') == 'complaint' ? 'selected' : '' }}>شكوى</option>
                                <option value="suggestion" {{ request('category') == 'suggestion' ? 'selected' : '' }}>اقتراح</option>
                            </select>
                        </div>
                    </div>

                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="city_id">المدينة</label>
                            <select name="city_id" id="city_id" class="form-control">
                                <option value="">جميع المدن</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>
                                        {{ $city->name_ar }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="search">البحث</label>
                            <input type="text" name="search" id="search" class="form-control" 
                                   placeholder="رقم التذكرة، الموضوع، أو الوصف..." 
                                   value="{{ request('search') }}">
                        </div>
                    </div>

                    <div class="col-md-1">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Tickets Table -->
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">التذاكر</h6>
            <div>
                <button type="button" class="btn btn-outline-secondary btn-sm" data-toggle="modal" data-target="#bulkActionModal">
                    <i class="fas fa-tasks"></i> إجراءات متعددة
                </button>
            </div>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="50">
                                <input type="checkbox" id="select-all">
                            </th>
                            <th>رقم التذكرة</th>
                            <th>الموضوع</th>
                            <th>المستخدم</th>
                            <th>المدينة</th>
                            <th>الفئة</th>
                            <th>الأولوية</th>
                            <th>الحالة</th>
                            <th>المعين إليه</th>
                            <th>تاريخ الإنشاء</th>
                            <th>آخر تحديث</th>
                            <th width="100">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($tickets as $ticket)
                            <tr>
                                <td>
                                    <input type="checkbox" class="ticket-checkbox" value="{{ $ticket->id }}">
                                </td>
                                <td>
                                    <span class="font-weight-bold text-primary">
                                        {{ $ticket->ticket_number }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.tickets.show', $ticket) }}" class="text-decoration-none">
                                        <strong>{{ Str::limit($ticket->subject, 40) }}</strong>
                                    </a>
                                    @if(!$ticket->admin_read_at)
                                        <span class="badge bg-warning text-dark badge-sm ml-1">جديد</span>
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mr-2">
                                            {{ substr($ticket->user->name ?? 'غ', 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-weight-bold">{{ $ticket->user->name ?? 'مستخدم محذوف' }}</div>
                                            <small class="text-muted">{{ $ticket->user->email ?? 'غير متاح' }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $ticket->city->name_ar ?? 'غير محدد' }}</td>
                                <td>
                                    <span class="badge bg-{{ $ticket->category == 'technical' ? 'info' : ($ticket->category == 'billing' ? 'warning' : 'secondary') }}">
                                        {{ 
                                            $ticket->category == 'technical' ? 'تقني' : 
                                            ($ticket->category == 'billing' ? 'فوترة' : 
                                            ($ticket->category == 'general' ? 'عام' : 
                                            ($ticket->category == 'complaint' ? 'شكوى' : 'اقتراح')))
                                        }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $ticket->priority == 'urgent' ? 'danger' : ($ticket->priority == 'high' ? 'warning' : ($ticket->priority == 'normal' ? 'info' : 'secondary')) }}">
                                        {{ 
                                            $ticket->priority == 'urgent' ? 'عاجلة' : 
                                            ($ticket->priority == 'high' ? 'عالية' : 
                                            ($ticket->priority == 'normal' ? 'عادية' : 'منخفضة'))
                                        }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-{{ $ticket->status == 'resolved' ? 'success' : ($ticket->status == 'in_progress' ? 'info' : ($ticket->status == 'closed' ? 'secondary' : 'warning')) }}">
                                        {{ 
                                            $ticket->status == 'open' ? 'مفتوحة' : 
                                            ($ticket->status == 'in_progress' ? 'قيد المعالجة' : 
                                            ($ticket->status == 'resolved' ? 'محلولة' : 'مغلقة'))
                                        }}
                                    </span>
                                </td>
                                <td>
                                    @if($ticket->assignedTo)
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-sm rounded-circle bg-success text-white d-flex align-items-center justify-content-center mr-1">
                                                {{ substr($ticket->assignedTo->name, 0, 1) }}
                                            </div>
                                            <small>{{ $ticket->assignedTo->name }}</small>
                                        </div>
                                    @else
                                        <span class="text-muted">غير معين</span>
                                    @endif
                                </td>
                                <td>
                                    <div>{{ $ticket->created_at->format('Y/m/d') }}</div>
                                    <small class="text-muted">{{ $ticket->created_at->format('H:i') }}</small>
                                </td>
                                <td>
                                    <div>{{ $ticket->updated_at->format('Y/m/d') }}</div>
                                    <small class="text-muted">{{ $ticket->updated_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <div class="btn-group">
                                        @can('view-tickets')
                                        <a href="{{ route('admin.tickets.show', $ticket) }}" class="btn btn-primary btn-sm" title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('edit-tickets')
                                        @if($ticket->status !== 'closed')
                                            <button type="button" class="btn btn-outline-info btn-sm" 
                                                    data-toggle="modal" data-target="#assignModal"
                                                    data-ticket-id="{{ $ticket->id }}"
                                                    data-ticket-number="{{ $ticket->ticket_number }}"
                                                    title="تعيين">
                                                <i class="fas fa-user-plus"></i>
                                            </button>
                                        @endif
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-center py-4">
                                    <i class="fas fa-ticket-alt fa-3x text-muted mb-3"></i>
                                    <h5 class="text-muted">لا توجد تذاكر</h5>
                                    <p class="text-muted">لم يتم العثور على تذاكر مطابقة للفلتر المحدد</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($tickets->hasPages())
                <div class="d-flex justify-content-center mt-3">
                    {{ $tickets->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Assign Modal -->
<div class="modal fade" id="assignModal" tabindex="-1" role="dialog" aria-labelledby="assignModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="assignModalLabel">تعيين التذكرة</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" id="assignForm">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="assigned_to">المشرف</label>
                        <select name="assigned_to" id="assigned_to" class="form-control" required>
                            <option value="">اختر المشرف</option>
                            @foreach($admins as $admin)
                                <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">تعيين</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Bulk Action Modal -->
<div class="modal fade" id="bulkActionModal" tabindex="-1" role="dialog" aria-labelledby="bulkActionModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="bulkActionModalLabel">إجراءات متعددة</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('admin.tickets.bulk.action') }}" id="bulkActionForm">
                @csrf
                <div class="modal-body">
                    <div id="selectedTicketsCount" class="alert alert-info">
                        لم يتم تحديد أي تذاكر
                    </div>
                    
                    <div class="form-group">
                        <label for="bulk_action">الإجراء</label>
                        <select name="action" id="bulk_action" class="form-control" required>
                            <option value="">اختر الإجراء</option>
                            <option value="assign">تعيين إلى مشرف</option>
                            <option value="status_change">تغيير الحالة</option>
                            <option value="priority_change">تغيير الأولوية</option>
                        </select>
                    </div>

                    <div class="form-group" id="bulk_assigned_to_group" style="display: none;">
                        <label for="bulk_assigned_to">المشرف</label>
                        <select name="assigned_to" id="bulk_assigned_to" class="form-control">
                            <option value="">اختر المشرف</option>
                            @foreach($admins as $admin)
                                <option value="{{ $admin->id }}">{{ $admin->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="form-group" id="bulk_status_group" style="display: none;">
                        <label for="bulk_status">الحالة</label>
                        <select name="status" id="bulk_status" class="form-control">
                            <option value="">اختر الحالة</option>
                            <option value="open">مفتوحة</option>
                            <option value="in_progress">قيد المعالجة</option>
                            <option value="resolved">محلولة</option>
                            <option value="closed">مغلقة</option>
                        </select>
                    </div>

                    <div class="form-group" id="bulk_priority_group" style="display: none;">
                        <label for="bulk_priority">الأولوية</label>
                        <select name="priority" id="bulk_priority" class="form-control">
                            <option value="">اختر الأولوية</option>
                            <option value="low">منخفضة</option>
                            <option value="normal">عادية</option>
                            <option value="high">عالية</option>
                            <option value="urgent">عاجلة</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">تطبيق</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 0.875rem;
}

.border-left-primary {
    border-left: 0.25rem solid #4e73df !important;
}

.border-left-success {
    border-left: 0.25rem solid #1cc88a !important;
}

.border-left-info {
    border-left: 0.25rem solid #36b9cc !important;
}

.border-left-warning {
    border-left: 0.25rem solid #f6c23e !important;
}

.border-left-secondary {
    border-left: 0.25rem solid #6c757d !important;
}

.border-left-dark {
    border-left: 0.25rem solid #343a40 !important;
}

.text-gray-800 {
    color: #5a5c69 !important;
}

.text-gray-300 {
    color: #dddfeb !important;
}

.badge-sm {
    font-size: 0.75rem;
}
</style>
@endsection

@push('scripts')
<script>
// Select all checkbox functionality
document.getElementById('select-all').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.ticket-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
    updateSelectedCount();
});

// Update selected tickets count
function updateSelectedCount() {
    const checked = document.querySelectorAll('.ticket-checkbox:checked');
    const count = checked.length;
    const countElement = document.getElementById('selectedTicketsCount');
    
    if (count > 0) {
        countElement.textContent = `تم تحديد ${count} تذكرة`;
        countElement.className = 'alert alert-success';
    } else {
        countElement.textContent = 'لم يتم تحديد أي تذاكر';
        countElement.className = 'alert alert-info';
    }
}

// Update count when individual checkboxes change
document.querySelectorAll('.ticket-checkbox').forEach(checkbox => {
    checkbox.addEventListener('change', updateSelectedCount);
});

// Assign modal functionality
document.getElementById('assignModal').addEventListener('show.bs.modal', function(event) {
    const button = event.relatedTarget;
    const ticketId = button.getAttribute('data-ticket-id');
    const ticketNumber = button.getAttribute('data-ticket-number');
    
    document.getElementById('assignModalLabel').textContent = `تعيين التذكرة ${ticketNumber}`;
    document.getElementById('assignForm').action = `/admin/tickets/${ticketId}/assign`;
});

// Bulk action form handling
document.getElementById('bulk_action').addEventListener('change', function() {
    const action = this.value;
    const assignGroup = document.getElementById('bulk_assigned_to_group');
    const statusGroup = document.getElementById('bulk_status_group');
    const priorityGroup = document.getElementById('bulk_priority_group');
    
    // Hide all groups
    assignGroup.style.display = 'none';
    statusGroup.style.display = 'none';
    priorityGroup.style.display = 'none';
    
    // Show relevant group
    if (action === 'assign') {
        assignGroup.style.display = 'block';
    } else if (action === 'status_change') {
        statusGroup.style.display = 'block';
    } else if (action === 'priority_change') {
        priorityGroup.style.display = 'block';
    }
});

// Bulk action form submission
document.getElementById('bulkActionForm').addEventListener('submit', function(e) {
    const checked = document.querySelectorAll('.ticket-checkbox:checked');
    
    if (checked.length === 0) {
        e.preventDefault();
        alert('يرجى تحديد تذكرة واحدة على الأقل');
        return;
    }
    
    // Add selected ticket IDs to form
    checked.forEach(checkbox => {
        const hiddenInput = document.createElement('input');
        hiddenInput.type = 'hidden';
        hiddenInput.name = 'tickets[]';
        hiddenInput.value = checkbox.value;
        this.appendChild(hiddenInput);
    });
});
</script>
@endpush