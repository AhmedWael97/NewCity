@extends('admin.layouts.app')

@section('title', 'إدارة المدفوعات')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">إدارة المدفوعات</h1>
        <a href="{{ route('admin.payments.pending') }}" class="btn btn-warning">
            <i class="fas fa-clock"></i> المدفوعات المعلقة ({{ $stats['pending'] }})
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">إجمالي المدفوعات</h6>
                            <h3 class="mb-0">{{ $stats['total'] }}</h3>
                        </div>
                        <div class="bg-primary text-white rounded p-3">
                            <i class="fas fa-money-bill-wave fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">معلقة</h6>
                            <h3 class="mb-0 text-warning">{{ $stats['pending'] }}</h3>
                        </div>
                        <div class="bg-warning text-white rounded p-3">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">نشطة</h6>
                            <h3 class="mb-0 text-success">{{ $stats['active'] }}</h3>
                        </div>
                        <div class="bg-success text-white rounded p-3">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-md-3">
            <div class="card">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">الإيرادات الكلية</h6>
                            <h3 class="mb-0 text-primary">{{ number_format($stats['total_revenue'], 2) }} جنيه</h3>
                        </div>
                        <div class="bg-info text-white rounded p-3">
                            <i class="fas fa-chart-line fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.payments.index') }}">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label class="form-label">الحالة</label>
                        <select name="status" class="form-select">
                            <option value="">الكل</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>معلقة</option>
                            <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشطة</option>
                            <option value="expired" {{ request('status') == 'expired' ? 'selected' : '' }}>منتهية</option>
                            <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ملغاة</option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوضة</option>
                        </select>
                    </div>

                    <div class="col-md-3">
                        <label class="form-label">طريقة الدفع</label>
                        <select name="payment_method" class="form-select">
                            <option value="">الكل</option>
                            @foreach($paymentMethods as $key => $method)
                                <option value="{{ $key }}" {{ request('payment_method') == $key ? 'selected' : '' }}>
                                    {{ $method['name'] }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">من تاريخ</label>
                        <input type="date" name="date_from" class="form-control" value="{{ request('date_from') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label">إلى تاريخ</label>
                        <input type="date" name="date_to" class="form-control" value="{{ request('date_to') }}">
                    </div>

                    <div class="col-md-2">
                        <label class="form-label d-block">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-filter"></i> تصفية
                        </button>
                    </div>
                </div>

                <div class="row mt-3">
                    <div class="col-md-10">
                        <input type="text" name="search" class="form-control" 
                               placeholder="بحث بالمتجر، الصاحب، أو البريد الإلكتروني..."
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary w-100">
                            <i class="fas fa-redo"></i> إعادة تعيين
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Payments Table -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">قائمة المدفوعات</h5>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>رقم المعاملة</th>
                            <th>المتجر</th>
                            <th>الصاحب</th>
                            <th>الباقة</th>
                            <th>المبلغ</th>
                            <th>طريقة الدفع</th>
                            <th>الحالة</th>
                            <th>التاريخ</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td>
                                    <small class="font-monospace">{{ $payment->transaction_id }}</small>
                                </td>
                                <td>
                                    <strong>{{ $payment->shop->name }}</strong><br>
                                    <small class="text-muted">{{ $payment->shop->city->name ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    {{ $payment->shop->user->name }}<br>
                                    <small class="text-muted">{{ $payment->shop->user->email }}</small>
                                </td>
                                <td>{{ $payment->subscriptionPlan->name ?? 'N/A' }}</td>
                                <td>
                                    <strong>{{ number_format($payment->amount_paid, 2) }} جنيه</strong><br>
                                    <small class="text-muted">{{ $payment->billing_cycle === 'yearly' ? 'سنوي' : 'شهري' }}</small>
                                </td>
                                <td>
                                    @php
                                        $methods = \App\Services\PaymentService::getAvailablePaymentMethods();
                                        $method = $methods[$payment->payment_method] ?? null;
                                    @endphp
                                    @if($method)
                                        {{ $method['icon'] }} {{ $method['name'] }}
                                    @else
                                        {{ $payment->payment_method }}
                                    @endif
                                </td>
                                <td>
                                    @switch($payment->status)
                                        @case('pending')
                                            <span class="badge bg-warning">معلقة</span>
                                            @break
                                        @case('active')
                                            <span class="badge bg-success">نشطة</span>
                                            @break
                                        @case('expired')
                                            <span class="badge bg-danger">منتهية</span>
                                            @break
                                        @case('cancelled')
                                            <span class="badge bg-secondary">ملغاة</span>
                                            @break
                                        @case('rejected')
                                            <span class="badge bg-dark">مرفوضة</span>
                                            @break
                                        @default
                                            <span class="badge bg-info">{{ $payment->status }}</span>
                                    @endswitch
                                </td>
                                <td>
                                    {{ $payment->created_at->format('Y-m-d') }}<br>
                                    <small class="text-muted">{{ $payment->created_at->format('H:i') }}</small>
                                </td>
                                <td>
                                    <a href="{{ route('admin.payments.show', $payment) }}" 
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> عرض
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4 text-muted">
                                    لا توجد مدفوعات
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($payments->hasPages())
            <div class="card-footer">
                {{ $payments->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
