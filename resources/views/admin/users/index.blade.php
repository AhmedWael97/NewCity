@extends('layouts.admin')

@section('title', 'إدارة المستخدمين')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">إدارة المستخدمين</h1>
    <div class="d-flex gap-2">
        <a href="{{ route('admin.users.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> إضافة مستخدم جديد
        </a>
    </div>
</div>

<!-- Filters -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">البحث والتصفية</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('admin.users.index') }}">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="search">البحث</label>
                        <input type="text" class="form-control" name="search" id="search" 
                               value="{{ request('search') }}" placeholder="البحث بالاسم أو البريد الإلكتروني">
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="role">الدور</label>
                        <select class="form-control" name="role" id="role">
                            <option value="">جميع الأدوار</option>
                            <option value="admin" {{ request('role') == 'admin' ? 'selected' : '' }}>مدير</option>
                            <option value="shop_owner" {{ request('role') == 'shop_owner' ? 'selected' : '' }}>صاحب متجر</option>
                            <option value="user" {{ request('role') == 'user' ? 'selected' : '' }}>مستخدم</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="city_id">المدينة</label>
                        <select class="form-control" name="city_id" id="city_id">
                            <option value="">جميع المدن</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>
                                    {{ $city->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="status">الحالة</label>
                        <select class="form-control" name="status" id="status">
                            <option value="">جميع الحالات</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                            <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group">
                        <label for="is_verified">التحقق</label>
                        <select class="form-control" name="is_verified" id="is_verified">
                            <option value="">الكل</option>
                            <option value="1" {{ request('is_verified') == '1' ? 'selected' : '' }}>محقق</option>
                            <option value="0" {{ request('is_verified') == '0' ? 'selected' : '' }}>غير محقق</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-1">
                    <div class="form-group">
                        <label>&nbsp;</label>
                        <div>
                            <button type="submit" class="btn btn-primary btn-block">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Users Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3 d-flex justify-content-between align-items-center">
        <h6 class="m-0 font-weight-bold text-primary">قائمة المستخدمين ({{ $users->total() }})</h6>
        <div>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                <i class="fas fa-check-square"></i> تحديد الكل
            </button>
            <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAll()">
                <i class="fas fa-square"></i> إلغاء التحديد
            </button>
        </div>
    </div>
    <div class="card-body">
        <form id="bulkActionForm" method="POST" action="{{ route('admin.users.bulk-action') }}">
            @csrf
            <div class="row mb-3">
                <div class="col-md-6">
                    <select name="action" class="form-control" required>
                        <option value="">اختر العملية</option>
                        <option value="verify">تحقق</option>
                        <option value="unverify">إلغاء التحقق</option>
                        <option value="activate">تفعيل</option>
                        <option value="deactivate">إلغاء التفعيل</option>
                        <option value="delete">حذف</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <button type="submit" class="btn btn-warning">تطبيق على المحدد</button>
                </div>
            </div>

            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th width="30px">
                                <input type="checkbox" id="selectAllCheckbox">
                            </th>
                            <th>المستخدم</th>
                            <th>البريد الإلكتروني</th>
                            <th>الدور</th>
                            <th>المدينة</th>
                            <th>الحالة</th>
                            <th>التحقق</th>
                            <th>تاريخ التسجيل</th>
                            <th>العمليات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($users as $user)
                            <tr>
                                <td>
                                    <input type="checkbox" name="users[]" value="{{ $user->id }}" class="user-checkbox">
                                </td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center me-2" 
                                             style="width: 32px; height: 32px; font-size: 0.875rem;">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-weight-bold">{{ $user->name }}</div>
                                            @if($user->phone)
                                                <small class="text-muted">{{ $user->phone }}</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>{{ $user->email }}</td>
                                <td>
                                    @if($user->role == 'admin')
                                        <span class="badge badge-danger">مدير</span>
                                    @elseif($user->role == 'shop_owner')
                                        <span class="badge badge-warning">صاحب متجر</span>
                                    @else
                                        <span class="badge badge-info">مستخدم</span>
                                    @endif
                                </td>
                                <td>{{ $user->city->name ?? 'غير محدد' }}</td>
                                <td>
                                    @if($user->status == 'active')
                                        <span class="badge badge-success">نشط</span>
                                    @else
                                        <span class="badge badge-secondary">غير نشط</span>
                                    @endif
                                </td>
                                <td>
                                    @if($user->email_verified_at)
                                        <span class="badge badge-success">
                                            <i class="fas fa-check"></i> محقق
                                        </span>
                                    @else
                                        <span class="badge badge-warning">
                                            <i class="fas fa-clock"></i> غير محقق
                                        </span>
                                    @endif
                                </td>
                                <td>{{ $user->created_at->format('Y-m-d') }}</td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.users.show', $user) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.users.edit', $user) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @if(!$user->email_verified_at)
                                            <form method="POST" action="{{ route('admin.users.verify', $user) }}" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-success btn-sm" title="تحقق">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('admin.users.toggle-status', $user) }}" class="d-inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="btn btn-{{ $user->status == 'active' ? 'warning' : 'success' }} btn-sm" 
                                                    title="{{ $user->status == 'active' ? 'إلغاء التفعيل' : 'تفعيل' }}">
                                                <i class="fas fa-{{ $user->status == 'active' ? 'ban' : 'check' }}"></i>
                                            </button>
                                        </form>
                                        @if($user->id !== auth()->id())
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}" class="d-inline" 
                                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center">لا توجد مستخدمين</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </form>
    </div>
</div>

<!-- Pagination -->
<div class="d-flex justify-content-center">
    {{ $users->links() }}
</div>

@endsection

@push('scripts')
<script>
function selectAll() {
    document.querySelectorAll('.user-checkbox').forEach(checkbox => {
        checkbox.checked = true;
    });
    document.getElementById('selectAllCheckbox').checked = true;
}

function deselectAll() {
    document.querySelectorAll('.user-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    document.getElementById('selectAllCheckbox').checked = false;
}

// Select all checkbox functionality
document.getElementById('selectAllCheckbox').addEventListener('change', function() {
    const checkboxes = document.querySelectorAll('.user-checkbox');
    checkboxes.forEach(checkbox => {
        checkbox.checked = this.checked;
    });
});

// Bulk action form validation
document.getElementById('bulkActionForm').addEventListener('submit', function(e) {
    const selectedUsers = document.querySelectorAll('.user-checkbox:checked');
    const action = document.querySelector('select[name="action"]').value;
    
    if (selectedUsers.length === 0) {
        e.preventDefault();
        alert('يرجى تحديد مستخدم واحد على الأقل');
        return;
    }
    
    if (!action) {
        e.preventDefault();
        alert('يرجى اختيار عملية');
        return;
    }
    
    if (action === 'delete') {
        if (!confirm(`هل أنت متأكد من حذف ${selectedUsers.length} مستخدم؟`)) {
            e.preventDefault();
            return;
        }
    }
});
</script>
@endpush