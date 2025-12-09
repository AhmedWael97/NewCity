@extends('layouts.admin')

@section('title', 'تفاصيل الدور: ' . $role->name)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-shield"></i> تفاصيل الدور: {{ $role->name }}
        </h1>
        <div>
            @can('edit-roles')
            <a href="{{ route('admin.roles.edit', $role) }}" class="btn btn-warning">
                <i class="fas fa-edit"></i> تعديل
            </a>
            @endcan
            <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i> رجوع
            </a>
        </div>
    </div>

    <!-- Role Info Card -->
    <div class="row mb-4">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">معلومات الدور</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>اسم الدور:</strong> {{ $role->name }}</p>
                            <p><strong>Guard:</strong> {{ $role->guard_name }}</p>
                            @if(in_array($role->name, ['super_admin', 'admin']))
                                <span class="badge bg-danger">دور نظامي</span>
                            @endif
                            @if($role->name === 'city_manager')
                                <span class="badge bg-info">مدير مدينة</span>
                            @endif
                        </div>
                        <div class="col-md-6">
                            <p><strong>تاريخ الإنشاء:</strong> {{ $role->created_at->format('Y-m-d H:i') }}</p>
                            <p><strong>آخر تحديث:</strong> {{ $role->updated_at->format('Y-m-d H:i') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card shadow bg-primary text-white">
                <div class="card-body text-center">
                    <h2>{{ $role->users->count() }}</h2>
                    <p class="mb-0"><i class="fas fa-users"></i> عدد المستخدمين</p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card shadow bg-success text-white">
                <div class="card-body text-center">
                    <h2>{{ $role->permissions->count() }}</h2>
                    <p class="mb-0"><i class="fas fa-shield-alt"></i> عدد الصلاحيات</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Permissions Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-shield-alt"></i> الصلاحيات ({{ $role->permissions->count() }})
            </h6>
        </div>
        <div class="card-body">
            @if($role->permissions->count() > 0)
                @php
                    $groupedPermissions = $role->permissions->groupBy(function($permission) {
                        $parts = explode('-', $permission->name);
                        return count($parts) > 1 ? $parts[1] : 'other';
                    });
                @endphp
                
                <div class="row">
                    @foreach($groupedPermissions as $module => $permissions)
                    <div class="col-md-6 mb-3">
                        <div class="card border-left-primary">
                            <div class="card-header bg-light">
                                <strong><i class="fas fa-folder"></i> {{ ucfirst($module) }}</strong>
                                <span class="badge bg-primary float-end">{{ $permissions->count() }}</span>
                            </div>
                            <div class="card-body">
                                @foreach($permissions as $permission)
                                    <span class="badge bg-secondary mb-1">{{ $permission->name }}</span>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            @else
                <div class="text-center text-muted py-4">
                    <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                    لا توجد صلاحيات مرتبطة بهذا الدور
                </div>
            @endif
        </div>
    </div>

    <!-- Users Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-users"></i> المستخدمون ({{ $role->users->count() }})
            </h6>
        </div>
        <div class="card-body">
            @if($role->users->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead class="table-light">
                            <tr>
                                <th>الاسم</th>
                                <th>البريد الإلكتروني</th>
                                <th>تاريخ التسجيل</th>
                                @can('view-users')
                                <th>الإجراءات</th>
                                @endcan
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($role->users as $user)
                            <tr>
                                <td>{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td>{{ $user->created_at->format('Y-m-d') }}</td>
                                @can('view-users')
                                <td>
                                    <a href="{{ route('admin.users.edit', $user) }}" 
                                       class="btn btn-sm btn-info">
                                        <i class="fas fa-eye"></i> عرض
                                    </a>
                                </td>
                                @endcan
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="text-center text-muted py-4">
                    <i class="fas fa-user-slash fa-3x mb-3 d-block"></i>
                    لا يوجد مستخدمون لهذا الدور
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
