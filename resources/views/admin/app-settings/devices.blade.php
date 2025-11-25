@extends('layouts.admin')

@section('title', 'Registered Devices')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">الأجهزة المسجلة</h1>
        <a href="{{ route('admin.app-settings.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> العودة للإعدادات
        </a>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.app-settings.devices') }}" class="form-inline">
                <div class="form-group mr-3">
                    <label for="device_type" class="mr-2">نوع الجهاز:</label>
                    <select name="device_type" id="device_type" class="form-control">
                        <option value="">الكل</option>
                        <option value="ios" {{ request('device_type') === 'ios' ? 'selected' : '' }}>iOS</option>
                        <option value="android" {{ request('device_type') === 'android' ? 'selected' : '' }}>Android</option>
                    </select>
                </div>

                <div class="form-group mr-3">
                    <label for="status" class="mr-2">الحالة:</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">الكل</option>
                        <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="inactive" {{ request('status') === 'inactive' ? 'selected' : '' }}>غير نشط</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> تصفية
                </button>
                
                @if(request()->hasAny(['device_type', 'status']))
                    <a href="{{ route('admin.app-settings.devices') }}" class="btn btn-secondary mr-2">
                        <i class="fas fa-times"></i> إعادة تعيين
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Devices Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">قائمة الأجهزة</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>المستخدم</th>
                            <th>نوع الجهاز</th>
                            <th>اسم الجهاز</th>
                            <th>إصدار التطبيق</th>
                            <th>الحالة</th>
                            <th>آخر استخدام</th>
                            <th>تاريخ التسجيل</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($devices as $device)
                            <tr>
                                <td>{{ $device->id }}</td>
                                <td>
                                    @if($device->user)
                                        <a href="{{ route('admin.users.show', $device->user) }}">
                                            {{ $device->user->name }}
                                        </a>
                                        <br>
                                        <small class="text-muted">{{ $device->user->email }}</small>
                                    @else
                                        <span class="text-muted">مستخدم ضيف</span>
                                    @endif
                                </td>
                                <td>
                                    @if($device->device_type === 'ios')
                                        <i class="fab fa-apple fa-lg text-dark"></i> iOS
                                    @elseif($device->device_type === 'android')
                                        <i class="fab fa-android fa-lg text-success"></i> Android
                                    @else
                                        <span class="text-muted">غير محدد</span>
                                    @endif
                                </td>
                                <td>{{ $device->device_name ?? '-' }}</td>
                                <td>
                                    @if($device->app_version)
                                        <span class="badge bg-info text-white">v{{ $device->app_version }}</span>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($device->is_active)
                                        <span class="badge bg-success text-white">نشط</span>
                                    @else
                                        <span class="badge bg-secondary text-white">غير نشط</span>
                                    @endif
                                </td>
                                <td>
                                    @if($device->last_used_at)
                                        <small>{{ $device->last_used_at->diffForHumans() }}</small>
                                        <br>
                                        <small class="text-muted">{{ $device->last_used_at->format('Y-m-d H:i') }}</small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    <small>{{ $device->created_at->format('Y-m-d H:i') }}</small>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">لا توجد أجهزة مسجلة</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $devices->links() }}
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="row">
        <div class="col-md-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">إحصائيات الأجهزة</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-3 text-center">
                            <div class="mb-3">
                                <i class="fas fa-mobile-alt fa-2x text-primary mb-2"></i>
                                <h4 class="font-weight-bold">{{ $devices->total() }}</h4>
                                <p class="text-muted mb-0">إجمالي الأجهزة</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="mb-3">
                                <i class="fas fa-check-circle fa-2x text-success mb-2"></i>
                                <h4 class="font-weight-bold">{{ $devices->where('is_active', true)->count() }}</h4>
                                <p class="text-muted mb-0">أجهزة نشطة</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="mb-3">
                                <i class="fab fa-apple fa-2x text-dark mb-2"></i>
                                <h4 class="font-weight-bold">{{ $devices->where('device_type', 'ios')->count() }}</h4>
                                <p class="text-muted mb-0">أجهزة iOS</p>
                            </div>
                        </div>
                        <div class="col-md-3 text-center">
                            <div class="mb-3">
                                <i class="fab fa-android fa-2x text-success mb-2"></i>
                                <h4 class="font-weight-bold">{{ $devices->where('device_type', 'android')->count() }}</h4>
                                <p class="text-muted mb-0">أجهزة Android</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
