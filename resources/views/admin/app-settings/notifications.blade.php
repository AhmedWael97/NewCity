@extends('layouts.admin')

@section('title', 'Push Notifications Management')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">إدارة الإشعارات</h1>
        <div>
            <a href="{{ route('admin.app-settings.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i> العودة للإعدادات
            </a>
            <a href="{{ route('admin.app-settings.notifications.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> إرسال إشعار جديد
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show" role="alert">
            {{ session('error') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                إجمالي الإشعارات</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bell fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                قيد الانتظار</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                تم الإرسال</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['sent'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                فشل</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['failed'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-times-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.app-settings.notifications') }}" class="form-inline">
                <div class="form-group mr-3">
                    <label for="status" class="mr-2">الحالة:</label>
                    <select name="status" id="status" class="form-control">
                        <option value="">الكل</option>
                        <option value="pending" {{ request('status') === 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                        <option value="sending" {{ request('status') === 'sending' ? 'selected' : '' }}>جاري الإرسال</option>
                        <option value="sent" {{ request('status') === 'sent' ? 'selected' : '' }}>تم الإرسال</option>
                        <option value="failed" {{ request('status') === 'failed' ? 'selected' : '' }}>فشل</option>
                    </select>
                </div>

                <div class="form-group mr-3">
                    <label for="type" class="mr-2">النوع:</label>
                    <select name="type" id="type" class="form-control">
                        <option value="">الكل</option>
                        <option value="general" {{ request('type') === 'general' ? 'selected' : '' }}>عام</option>
                        <option value="alert" {{ request('type') === 'alert' ? 'selected' : '' }}>تنبيه</option>
                        <option value="promo" {{ request('type') === 'promo' ? 'selected' : '' }}>عرض ترويجي</option>
                        <option value="update" {{ request('type') === 'update' ? 'selected' : '' }}>تحديث</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-filter"></i> تصفية
                </button>
                
                @if(request()->hasAny(['status', 'type']))
                    <a href="{{ route('admin.app-settings.notifications') }}" class="btn btn-secondary mr-2">
                        <i class="fas fa-times"></i> إعادة تعيين
                    </a>
                @endif
            </form>
        </div>
    </div>

    <!-- Notifications Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">قائمة الإشعارات</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" id="dataTable">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>العنوان</th>
                            <th>النوع</th>
                            <th>الهدف</th>
                            <th>الحالة</th>
                            <th>الإحصائيات</th>
                            <th>تاريخ الإرسال</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($notifications as $notification)
                            <tr>
                                <td>{{ $notification->id }}</td>
                                <td>
                                    <strong>{{ $notification->title }}</strong>
                                    <br>
                                    <small class="text-muted">{{ Str::limit($notification->body, 50) }}</small>
                                </td>
                                <td>
                                    @php
                                        $typeLabels = [
                                            'general' => ['عام', 'info'],
                                            'alert' => ['تنبيه', 'warning'],
                                            'promo' => ['ترويجي', 'success'],
                                            'update' => ['تحديث', 'primary'],
                                        ];
                                        $typeInfo = $typeLabels[$notification->type] ?? ['غير محدد', 'secondary'];
                                    @endphp
                                    <span class="badge badge-{{ $typeInfo[1] }}">{{ $typeInfo[0] }}</span>
                                </td>
                                <td>
                                    @php
                                        $targetLabels = [
                                            'all' => 'الكل',
                                            'specific_users' => 'مستخدمون محددون',
                                            'city' => 'مدينة محددة',
                                        ];
                                    @endphp
                                    {{ $targetLabels[$notification->target] ?? $notification->target }}
                                </td>
                                <td>
                                    @php
                                        $statusLabels = [
                                            'pending' => ['قيد الانتظار', 'warning'],
                                            'sending' => ['جاري الإرسال', 'info'],
                                            'sent' => ['تم الإرسال', 'success'],
                                            'failed' => ['فشل', 'danger'],
                                        ];
                                        $statusInfo = $statusLabels[$notification->status] ?? ['غير محدد', 'secondary'];
                                    @endphp
                                    <span class="badge badge-{{ $statusInfo[1] }}">{{ $statusInfo[0] }}</span>
                                </td>
                                <td>
                                    @if($notification->status === 'sent')
                                        <small>
                                            <i class="fas fa-paper-plane text-primary"></i> {{ $notification->sent_count }}<br>
                                            <i class="fas fa-check text-success"></i> {{ $notification->success_count }}<br>
                                            <i class="fas fa-times text-danger"></i> {{ $notification->failure_count }}
                                        </small>
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($notification->scheduled_at)
                                        <small>مجدول: {{ $notification->scheduled_at->format('Y-m-d H:i') }}</small>
                                    @elseif($notification->sent_at)
                                        {{ $notification->sent_at->format('Y-m-d H:i') }}
                                    @else
                                        <span class="text-muted">-</span>
                                    @endif
                                </td>
                                <td>
                                    @if($notification->status === 'pending')
                                        <form action="{{ route('admin.app-settings.notifications.send', $notification) }}" 
                                              method="POST" class="d-inline" 
                                              onsubmit="return confirm('هل أنت متأكد من إرسال هذا الإشعار؟')">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-success" title="إرسال الآن">
                                                <i class="fas fa-paper-plane"></i>
                                            </button>
                                        </form>
                                    @endif
                                    
                                    @if($notification->status !== 'sending')
                                        <form action="{{ route('admin.app-settings.notifications.delete', $notification) }}" 
                                              method="POST" class="d-inline" 
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذا الإشعار؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center">لا توجد إشعارات</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $notifications->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
