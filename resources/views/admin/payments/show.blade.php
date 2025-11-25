@extends('layouts.admin')

@section('title', 'تفاصيل الدفع')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2 text-gray-800">
                <i class="fas fa-receipt me-2"></i>
                تفاصيل الدفع #{{ $subscription->id }}
            </h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.payments.index') }}">المدفوعات</a></li>
                    <li class="breadcrumb-item active">تفاصيل الدفع</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.payments.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right me-1"></i>
                العودة للقائمة
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Payment Details -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">معلومات الدفع</h6>
                    @if($subscription->status === 'pending')
                        <span class="badge bg-warning text-dark">قيد الانتظار</span>
                    @elseif($subscription->status === 'active')
                        <span class="badge bg-success">نشط</span>
                    @elseif($subscription->status === 'expired')
                        <span class="badge bg-secondary">منتهي</span>
                    @elseif($subscription->status === 'cancelled')
                        <span class="badge bg-danger">ملغي</span>
                    @endif
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">رقم الدفع</h6>
                            <p class="mb-0 fw-bold">#{{ $subscription->id }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">تاريخ الطلب</h6>
                            <p class="mb-0">{{ $subscription->created_at->format('Y-m-d H:i') }}</p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">خطة الاشتراك</h6>
                            <p class="mb-0 fw-bold">{{ $subscription->subscriptionPlan->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">دورة الفوترة</h6>
                            <p class="mb-0">
                                @if($subscription->billing_cycle === 'monthly')
                                    شهري
                                @elseif($subscription->billing_cycle === 'yearly')
                                    سنوي
                                @else
                                    {{ $subscription->billing_cycle }}
                                @endif
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">المبلغ المدفوع</h6>
                            <p class="mb-0 fw-bold text-success">{{ number_format($subscription->amount_paid, 2) }} جنيه</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">طريقة الدفع</h6>
                            <p class="mb-0">
                                @if($paymentMethodInfo)
                                    <i class="{{ $paymentMethodInfo['icon'] }} me-1"></i>
                                    {{ $paymentMethodInfo['name'] }}
                                @else
                                    {{ $subscription->payment_method }}
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($subscription->payment_details)
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">تفاصيل الدفع</h6>
                        <div class="bg-light p-3 rounded">
                            @if($subscription->payment_method === 'bank_transfer')
                                @if(isset($subscription->payment_details['bank_name']))
                                    <p class="mb-1"><strong>اسم البنك:</strong> {{ $subscription->payment_details['bank_name'] }}</p>
                                @endif
                                @if(isset($subscription->payment_details['account_number']))
                                    <p class="mb-1"><strong>رقم الحساب:</strong> {{ $subscription->payment_details['account_number'] }}</p>
                                @endif
                                @if(isset($subscription->payment_details['transaction_reference']))
                                    <p class="mb-1"><strong>رقم المرجع:</strong> {{ $subscription->payment_details['transaction_reference'] }}</p>
                                @endif
                                @if(isset($subscription->payment_details['transfer_date']))
                                    <p class="mb-0"><strong>تاريخ التحويل:</strong> {{ $subscription->payment_details['transfer_date'] }}</p>
                                @endif
                            @elseif($subscription->payment_method === 'cash')
                                <p class="mb-0">سيتم الدفع نقداً عند التسليم</p>
                            @else
                                <pre class="mb-0">{{ json_encode($subscription->payment_details, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if($subscription->payment_receipt)
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">إيصال الدفع</h6>
                        <a href="{{ $subscription->payment_receipt }}" target="_blank" class="btn btn-sm btn-outline-primary">
                            <i class="fas fa-file-download me-1"></i>
                            عرض الإيصال
                        </a>
                    </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">تاريخ البداية</h6>
                            <p class="mb-0">{{ $subscription->start_date ? $subscription->start_date->format('Y-m-d') : '-' }}</p>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-1">تاريخ الانتهاء</h6>
                            <p class="mb-0">{{ $subscription->end_date ? $subscription->end_date->format('Y-m-d') : '-' }}</p>
                        </div>
                    </div>

                    @if($subscription->verification_notes)
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">ملاحظات التحقق</h6>
                        <div class="alert alert-info mb-0">
                            {{ $subscription->verification_notes }}
                        </div>
                    </div>
                    @endif

                    @if($subscription->cancellation_reason)
                    <div class="mb-3">
                        <h6 class="text-muted mb-2">سبب الإلغاء</h6>
                        <div class="alert alert-danger mb-0">
                            {{ $subscription->cancellation_reason }}
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Shop and User Info -->
        <div class="col-lg-4">
            <!-- Shop Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">معلومات المتجر</h6>
                </div>
                <div class="card-body">
                    <h6 class="fw-bold mb-2">{{ $subscription->shop->name }}</h6>
                    <p class="text-muted small mb-2">
                        <i class="fas fa-map-marker-alt me-1"></i>
                        {{ $subscription->shop->city->name ?? 'غير محدد' }}
                    </p>
                    <p class="text-muted small mb-3">
                        <i class="fas fa-tag me-1"></i>
                        {{ $subscription->shop->category->name ?? 'غير محدد' }}
                    </p>
                    <a href="{{ route('admin.shops.show', $subscription->shop) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-eye me-1"></i>
                        عرض المتجر
                    </a>
                </div>
            </div>

            <!-- User Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">معلومات صاحب المتجر</h6>
                </div>
                <div class="card-body">
                    <h6 class="fw-bold mb-2">{{ $subscription->shop->user->name }}</h6>
                    <p class="text-muted small mb-2">
                        <i class="fas fa-envelope me-1"></i>
                        {{ $subscription->shop->user->email }}
                    </p>
                    @if($subscription->shop->user->phone)
                    <p class="text-muted small mb-3">
                        <i class="fas fa-phone me-1"></i>
                        {{ $subscription->shop->user->phone }}
                    </p>
                    @endif
                    <a href="{{ route('admin.users.show', $subscription->shop->user) }}" class="btn btn-sm btn-outline-primary">
                        <i class="fas fa-user me-1"></i>
                        عرض الملف
                    </a>
                </div>
            </div>

            <!-- Actions Card -->
            @if($subscription->status === 'pending')
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">الإجراءات</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.payments.verify', $subscription) }}" method="POST" class="mb-3">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">ملاحظات التحقق</label>
                            <textarea name="verification_notes" class="form-control" rows="3" placeholder="أضف أي ملاحظات..."></textarea>
                        </div>
                        <button type="submit" class="btn btn-success w-100">
                            <i class="fas fa-check me-1"></i>
                            تأكيد الدفع
                        </button>
                    </form>

                    <form action="{{ route('admin.payments.reject', $subscription) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">سبب الرفض</label>
                            <textarea name="rejection_reason" class="form-control" rows="3" placeholder="أضف سبب الرفض..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-times me-1"></i>
                            رفض الدفع
                        </button>
                    </form>
                </div>
            </div>
            @elseif($subscription->status === 'active')
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">الإجراءات</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.payments.refund', $subscription) }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">سبب الاسترداد</label>
                            <textarea name="refund_reason" class="form-control" rows="3" placeholder="أضف سبب الاسترداد..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-warning w-100" onclick="return confirm('هل أنت متأكد من استرداد هذا الدفع؟')">
                            <i class="fas fa-undo me-1"></i>
                            استرداد المبلغ
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
