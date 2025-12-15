@extends('layouts.admin')

@section('title', 'Email Queue')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">قائمة انتظار البريد الإلكتروني</h1>
        <div>
            <a href="{{ route('admin.email-queue.preferences') }}" class="btn btn-info">
                <i class="fas fa-cog"></i> تفضيلات البريد
            </a>
            <a href="{{ route('admin.dashboard') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i> العودة
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-4 col-md-4 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">قيد الانتظار</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-4 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">تم الإرسال</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['sent'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-4 col-md-4 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">فشل</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['failed'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-triangle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-filter"></i> فلترة النتائج
            </h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.email-queue.index') }}" class="form-inline">
                <div class="form-group mr-3 mb-2">
                    <label for="status" class="mr-2">الحالة:</label>
                    <select name="status" id="status" class="form-control">
                        <option value="all" {{ $status == 'all' ? 'selected' : '' }}>الكل</option>
                        <option value="pending" {{ $status == 'pending' ? 'selected' : '' }}>قيد الانتظار</option>
                        <option value="sent" {{ $status == 'sent' ? 'selected' : '' }}>تم الإرسال</option>
                        <option value="failed" {{ $status == 'failed' ? 'selected' : '' }}>فشل</option>
                    </select>
                </div>

                <div class="form-group mr-3 mb-2">
                    <label for="event_type" class="mr-2">نوع الحدث:</label>
                    <select name="event_type" id="event_type" class="form-control">
                        <option value="all" {{ $eventType == 'all' ? 'selected' : '' }}>الكل</option>
                        <option value="shop_suggestion" {{ $eventType == 'shop_suggestion' ? 'selected' : '' }}>اقتراح متجر</option>
                        <option value="city_suggestion" {{ $eventType == 'city_suggestion' ? 'selected' : '' }}>اقتراح مدينة</option>
                        <option value="shop_rate" {{ $eventType == 'shop_rate' ? 'selected' : '' }}>تقييم متجر</option>
                        <option value="service_rate" {{ $eventType == 'service_rate' ? 'selected' : '' }}>تقييم خدمة</option>
                        <option value="new_service" {{ $eventType == 'new_service' ? 'selected' : '' }}>خدمة جديدة</option>
                        <option value="new_marketplace" {{ $eventType == 'new_marketplace' ? 'selected' : '' }}>منتج سوق جديد</option>
                        <option value="new_user" {{ $eventType == 'new_user' ? 'selected' : '' }}>مستخدم جديد</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-primary mb-2">
                    <i class="fas fa-search"></i> بحث
                </button>

                @if($status != 'all' || $eventType != 'all')
                    <a href="{{ route('admin.email-queue.index') }}" class="btn btn-secondary mb-2 mr-2">
                        <i class="fas fa-times"></i> إلغاء الفلتر
                    </a>
                @endif

                @if($stats['sent'] > 0)
                    <form method="POST" action="{{ route('admin.email-queue.clear-sent') }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف جميع الرسائل المرسلة؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger mb-2">
                            <i class="fas fa-trash"></i> حذف المرسلة
                        </button>
                    </form>
                @endif
            </form>
        </div>
    </div>

    <!-- Email Queue Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list"></i> قائمة الرسائل
            </h6>
        </div>
        <div class="card-body">
            @if($emails->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>المصدر</th>
                                <th>نوع الحدث</th>
                                <th>الموضوع</th>
                                <th>المستلمين</th>
                                <th>الحالة</th>
                                <th>المحاولات</th>
                                <th>التاريخ</th>
                                <th>الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($emails as $email)
                                <tr>
                                    <td>{{ $email->id }}</td>
                                    <td>
                                        @if($email->source === 'api')
                                            <span class="badge badge-primary"><i class="fas fa-code"></i> API</span>
                                        @elseif($email->source === 'web')
                                            <span class="badge badge-secondary"><i class="fas fa-globe"></i> Web</span>
                                        @else
                                            <span class="badge badge-dark"><i class="fas fa-cog"></i> System</span>
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $eventLabels = [
                                                'shop_suggestion' => 'اقتراح متجر',
                                                'city_suggestion' => 'اقتراح مدينة',
                                                'shop_rate' => 'تقييم متجر',
                                                'service_rate' => 'تقييم خدمة',
                                                'new_service' => 'خدمة جديدة',
                                                'new_marketplace' => 'منتج سوق جديد',
                                                'new_user' => 'مستخدم جديد',
                                            ];
                                        @endphp
                                        <span class="badge badge-info">{{ $eventLabels[$email->event_type] ?? $email->event_type }}</span>
                                    </td>
                                    <td>{{ Str::limit($email->subject, 40) }}</td>
                                    <td>
                                        <small>{{ count($email->recipients) }} مستلم</small>
                                    </td>
                                    <td>
                                        @if($email->status == 'pending')
                                            <span class="badge badge-warning">قيد الانتظار</span>
                                        @elseif($email->status == 'processing')
                                            <span class="badge badge-primary">جاري المعالجة</span>
                                        @elseif($email->status == 'sent')
                                            <span class="badge badge-success">تم الإرسال</span>
                                        @else
                                            <span class="badge badge-danger">فشل</span>
                                        @endif
                                    </td>
                                    <td>{{ $email->attempts }}</td>
                                    <td>{{ $email->created_at->format('Y-m-d H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.email-queue.show', $email->id) }}" class="btn btn-sm btn-info" title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($email->status == 'failed')
                                            <form method="POST" action="{{ route('admin.email-queue.retry', $email->id) }}" class="d-inline">
                                                @csrf
                                                <button type="submit" class="btn btn-sm btn-warning" title="إعادة المحاولة">
                                                    <i class="fas fa-redo"></i>
                                                </button>
                                            </form>
                                        @endif
                                        <form method="POST" action="{{ route('admin.email-queue.destroy', $email->id) }}" class="d-inline" onsubmit="return confirm('هل أنت متأكد من حذف هذا البريد؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                <div class="mt-3">
                    {{ $emails->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-inbox fa-3x text-gray-300 mb-3"></i>
                    <p class="text-muted">لا توجد رسائل بريد إلكتروني</p>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
