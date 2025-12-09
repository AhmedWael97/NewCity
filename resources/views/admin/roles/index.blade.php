@extends('layouts.admin')

@section('title', 'إدارة الأدوار والصلاحيات')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-shield"></i> إدارة الأدوار والصلاحيات
        </h1>
        @can('create-roles')
        <a href="{{ route('admin.roles.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة دور جديد
        </a>
        @endcan
    </div>

    <!-- Success/Error Messages -->
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Roles Table Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">قائمة الأدوار ({{ $roles->count() }})</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="rolesTable">
                    <thead class="table-light">
                        <tr>
                            <th style="width: 5%">#</th>
                            <th style="width: 20%">اسم الدور</th>
                            <th style="width: 15%">عدد المستخدمين</th>
                            <th style="width: 15%">عدد الصلاحيات</th>
                            <th style="width: 30%">الصلاحيات</th>
                            <th style="width: 15%">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $index => $role)
                        <tr>
                            <td>{{ $index + 1 }}</td>
                            <td>
                                <strong>{{ $role->name }}</strong>
                                @if(in_array($role->name, ['super_admin', 'admin']))
                                    <span class="badge bg-danger ms-2">نظام</span>
                                @endif
                                @if($role->name === 'city_manager')
                                    <span class="badge bg-info ms-2">مدير مدينة</span>
                                @endif
                            </td>
                            <td>
                                <span class="badge bg-primary">{{ $role->users_count }}</span>
                                مستخدم
                            </td>
                            <td>
                                <span class="badge bg-success">{{ $role->permissions->count() }}</span>
                                صلاحية
                            </td>
                            <td>
                                <div class="permissions-preview">
                                    @php
                                        $displayPermissions = $role->permissions->take(3);
                                        $remainingCount = $role->permissions->count() - 3;
                                    @endphp
                                    @foreach($displayPermissions as $permission)
                                        <span class="badge bg-secondary mb-1">{{ $permission->name }}</span>
                                    @endforeach
                                    @if($remainingCount > 0)
                                        <span class="badge bg-info mb-1">+{{ $remainingCount }} أخرى</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="btn-group" role="group">
                                    @can('view-roles')
                                    <a href="{{ route('admin.roles.show', $role) }}" 
                                       class="btn btn-sm btn-info"
                                       title="عرض التفاصيل">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    @endcan
                                    
                                    @can('edit-roles')
                                    <a href="{{ route('admin.roles.edit', $role) }}" 
                                       class="btn btn-sm btn-warning"
                                       title="تعديل">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    @endcan
                                    
                                    @can('delete-roles')
                                    @if(!in_array($role->name, ['super_admin', 'admin']))
                                        <form method="POST" 
                                              action="{{ route('admin.roles.destroy', $role) }}" 
                                              class="d-inline"
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذا الدور؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                    class="btn btn-sm btn-danger"
                                                    title="حذف"
                                                    {{ $role->users_count > 0 ? 'disabled' : '' }}>
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @else
                                        <button class="btn btn-sm btn-secondary" disabled title="لا يمكن حذف الأدوار النظامية">
                                            <i class="fas fa-lock"></i>
                                        </button>
                                    @endif
                                    @endcan
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                لا توجد أدوار مسجلة
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Role Information Card -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">معلومات عن الأدوار</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6><i class="fas fa-user-shield text-danger"></i> <strong>super_admin:</strong></h6>
                            <p class="text-muted">يمتلك جميع الصلاحيات في النظام بدون قيود</p>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-user-cog text-primary"></i> <strong>admin:</strong></h6>
                            <p class="text-muted">يمتلك جميع الصلاحيات ماعدا إدارة الأدوار والمستخدمين</p>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-city text-info"></i> <strong>city_manager:</strong></h6>
                            <p class="text-muted">يستطيع إدارة محتوى المدن المخصصة له فقط</p>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-edit text-warning"></i> <strong>editor:</strong></h6>
                            <p class="text-muted">يستطيع إنشاء وتعديل المحتوى ولكن لا يستطيع الحذف</p>
                        </div>
                        <div class="col-md-6">
                            <h6><i class="fas fa-eye text-success"></i> <strong>viewer:</strong></h6>
                            <p class="text-muted">صلاحيات القراءة فقط لجميع المحتوى</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTable
    $('#rolesTable').DataTable({
        "language": {
            "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Arabic.json"
        },
        "order": [[0, "asc"]],
        "pageLength": 25
    });
});
</script>
@endpush
@endsection
