@extends('layouts.admin')

@section('title', 'تحقق الزوار - User Verifications')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">
                <i class="fas fa-shield-check text-primary"></i>
                تحقق الزوار الحقيقيين
            </h1>
            <p class="text-muted mb-0">
                <i class="fas fa-info-circle"></i>
                تتبع وتحليل الزوار الذين تحققوا من أنهم أشخاص حقيقيون
            </p>
        </div>
        <div>
            <a href="{{ route('admin.verifications.export') }}" class="btn btn-success">
                <i class="fas fa-file-excel"></i>
                تصدير Excel
            </a>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                إجمالي التحققات
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['total_verifications']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                اليوم
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['today']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                هذا الأسبوع
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['this_week']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-week fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                عناوين IP فريدة
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($stats['unique_ips']) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-network-wired fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Stats -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            <div class="card shadow">
                <div class="card-body">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-chart-line"></i>
                        إحصائيات سريعة
                    </h6>
                    <ul class="list-unstyled mb-0">
                        <li class="mb-2">
                            <i class="fas fa-calendar-alt text-info"></i>
                            <strong>هذا الشهر:</strong> {{ number_format($stats['this_month']) }} تحقق
                        </li>
                        <li class="mb-2">
                            <i class="fas fa-envelope text-success"></i>
                            <strong>مع بريد إلكتروني:</strong> {{ number_format($stats['with_email']) }} زائر
                        </li>
                        <li>
                            <i class="fas fa-percentage text-warning"></i>
                            <strong>نسبة الإيميلات:</strong> 
                            {{ $stats['total_verifications'] > 0 ? number_format(($stats['with_email'] / $stats['total_verifications']) * 100, 1) : 0 }}%
                        </li>
                    </ul>
                </div>
            </div>
        </div>

        <div class="col-md-6 mb-3">
            <div class="card shadow">
                <div class="card-body">
                    <h6 class="text-primary mb-3">
                        <i class="fas fa-info-circle"></i>
                        معلومات مهمة
                    </h6>
                    <div class="alert alert-info mb-0">
                        <ul class="mb-0 small">
                            <li>يظهر البوب اب بعد ثانيتين من فتح الموقع</li>
                            <li>لا يمكن إغلاق البوب اب بدون كتابة رسالة</li>
                            <li>يتم حفظ التحقق في الجلسة - لا يظهر مرة أخرى</li>
                            <li>يتم تسجيل IP، المتصفح، والجهاز تلقائياً</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Verifications Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                <i class="fas fa-list"></i>
                جميع التحققات ({{ number_format($verifications->total()) }})
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead class="thead-light">
                        <tr>
                            <th>#</th>
                            <th>الاسم</th>
                            <th>البريد</th>
                            <th>الرسالة</th>
                            <th>IP</th>
                            <th>المتصفح</th>
                            <th>الجهاز</th>
                            <th>التاريخ</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($verifications as $verification)
                        <tr>
                            <td>{{ $verification->id }}</td>
                            <td>
                                @if($verification->name)
                                    <i class="fas fa-user text-primary"></i>
                                    {{ $verification->name }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                @if($verification->email)
                                    <i class="fas fa-envelope text-success"></i>
                                    <small>{{ $verification->email }}</small>
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                            <td>
                                <div style="max-width: 300px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                    {{ $verification->message }}
                                </div>
                            </td>
                            <td>
                                <code class="text-info">{{ $verification->ip_address }}</code>
                            </td>
                            <td>
                                <span class="badge badge-secondary">
                                    {{ $verification->browser ?? 'Unknown' }}
                                </span>
                            </td>
                            <td>
                                @if($verification->device === 'Mobile')
                                    <i class="fas fa-mobile-alt text-primary"></i>
                                @elseif($verification->device === 'Tablet')
                                    <i class="fas fa-tablet-alt text-info"></i>
                                @else
                                    <i class="fas fa-desktop text-secondary"></i>
                                @endif
                                {{ $verification->device }}
                            </td>
                            <td>
                                <small>{{ $verification->verified_at->diffForHumans() }}</small>
                                <br>
                                <small class="text-muted">{{ $verification->verified_at->format('Y-m-d H:i') }}</small>
                            </td>
                            <td>
                                @can('view-verifications')
                                <a href="{{ route('admin.verifications.show', $verification) }}" 
                                   class="btn btn-sm btn-info" 
                                   title="التفاصيل">
                                    <i class="fas fa-eye"></i>
                                </a>
                                @endcan
                                
                                @can('delete-verifications')
                                <form action="{{ route('admin.verifications.destroy', $verification) }}" 
                                      method="POST" 
                                      class="d-inline"
                                      onsubmit="return confirm('هل أنت متأكد من الحذف؟')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger" title="حذف">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                                @endcan
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center text-muted py-4">
                                <i class="fas fa-inbox fa-3x mb-3 d-block"></i>
                                لا توجد تحققات بعد
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $verifications->links() }}
            </div>
        </div>
    </div>
</div>

<style>
.border-left-primary { border-left: 4px solid #4e73df !important; }
.border-left-success { border-left: 4px solid #1cc88a !important; }
.border-left-info { border-left: 4px solid #36b9cc !important; }
.border-left-warning { border-left: 4px solid #f6c23e !important; }
</style>
@endsection
