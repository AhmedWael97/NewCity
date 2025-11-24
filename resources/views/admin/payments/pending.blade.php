@extends('admin.layouts.app')

@section('title', 'المدفوعات المعلقة')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">المدفوعات المعلقة</h1>
        <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> كل المدفوعات
        </a>
    </div>

    <!-- Statistics -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted mb-2">عدد المدفوعات المعلقة</h6>
                    <h2 class="mb-0 text-warning">{{ $stats['pending_count'] }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-muted mb-2">الإيرادات المعلقة</h6>
                    <h2 class="mb-0 text-info">{{ number_format($stats['pending_revenue'], 2) }} جنيه</h2>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Payments Table -->
    <div class="card">
        <div class="card-header bg-warning text-white">
            <h5 class="mb-0">
                <i class="fas fa-clock"></i> المدفوعات التي تحتاج للمراجعة
            </h5>
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
                            <th>التاريخ</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($payments as $payment)
                            <tr>
                                <td><small class="font-monospace">{{ $payment->transaction_id }}</small></td>
                                <td>
                                    <strong>{{ $payment->shop->name }}</strong><br>
                                    <small class="text-muted">{{ $payment->shop->city->name ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    {{ $payment->shop->user->name }}<br>
                                    <small class="text-muted">{{ $payment->shop->user->email }}</small><br>
                                    <small class="text-muted">{{ $payment->shop->user->phone ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    {{ $payment->subscriptionPlan->name ?? 'N/A' }}<br>
                                    <small class="text-muted">{{ $payment->billing_cycle === 'yearly' ? 'سنوي' : 'شهري' }}</small>
                                </td>
                                <td>
                                    <strong class="text-primary">{{ number_format($payment->amount_paid, 2) }} جنيه</strong>
                                </td>
                                <td>
                                    @php
                                        $methods = \App\Services\PaymentService::getAvailablePaymentMethods();
                                        $method = $methods[$payment->payment_method] ?? null;
                                    @endphp
                                    @if($method)
                                        <div>{{ $method['icon'] }} {{ $method['name'] }}</div>
                                        @if($method['requires_verification'])
                                            <small class="text-warning">
                                                <i class="fas fa-exclamation-triangle"></i> يتطلب التحقق
                                            </small>
                                        @endif
                                    @else
                                        {{ $payment->payment_method }}
                                    @endif
                                </td>
                                <td>
                                    {{ $payment->created_at->diffForHumans() }}<br>
                                    <small class="text-muted">{{ $payment->created_at->format('Y-m-d H:i') }}</small>
                                </td>
                                <td>
                                    <a href="{{ route('admin.payments.show', $payment) }}" 
                                       class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> مراجعة
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="8" class="text-center py-5">
                                    <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
                                    <p class="text-muted mb-0">لا توجد مدفوعات معلقة</p>
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
