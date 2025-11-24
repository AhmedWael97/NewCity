@extends('layouts.app')

@section('content')
<div class="payment-container">
    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <!-- Header -->
                <div class="text-center mb-5">
                    <h1 class="h2 fw-bold mb-3">اختر طريقة الدفع</h1>
                    <p class="text-muted">اختر طريقة الدفع المناسبة لإتمام الاشتراك</p>
                </div>

                <!-- Selected Plan Summary -->
                <div class="card shadow-sm mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">ملخص الطلب</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <p class="mb-2"><strong>الباقة:</strong> {{ $plan->name }}</p>
                                <p class="mb-2"><strong>الفترة:</strong> {{ $billingCycle === 'yearly' ? 'سنوياً' : 'شهرياً' }}</p>
                            </div>
                            <div class="col-md-6 text-md-end">
                                <h3 class="text-primary mb-0">
                                    {{ number_format($amount, 2) }} جنيه
                                </h3>
                                <small class="text-muted">{{ $billingCycle === 'yearly' ? 'سنوياً' : 'شهرياً' }}</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Billing Cycle Selection -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-3">دورة الفوترة</h5>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <div class="billing-option {{ $billingCycle === 'monthly' ? 'active' : '' }}" 
                                     onclick="window.location.href='{{ route('shop-owner.payment', ['plan' => $plan->id, 'cycle' => 'monthly']) }}'">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">اشتراك شهري</h6>
                                            <p class="text-muted mb-0 small">يتجدد كل شهر</p>
                                        </div>
                                        <div class="text-end">
                                            <h5 class="mb-0">{{ number_format($plan->monthly_price, 2) }}</h5>
                                            <small class="text-muted">جنيه/شهر</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @if($plan->yearly_price)
                            <div class="col-md-6">
                                <div class="billing-option {{ $billingCycle === 'yearly' ? 'active' : '' }}" 
                                     onclick="window.location.href='{{ route('shop-owner.payment', ['plan' => $plan->id, 'cycle' => 'yearly']) }}'">
                                    <span class="badge bg-success position-absolute top-0 end-0 m-2">
                                        وفر {{ $plan->yearly_savings_percentage }}%
                                    </span>
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div>
                                            <h6 class="mb-1">اشتراك سنوي</h6>
                                            <p class="text-muted mb-0 small">يتجدد كل سنة</p>
                                        </div>
                                        <div class="text-end">
                                            <h5 class="mb-0">{{ number_format($plan->yearly_price, 2) }}</h5>
                                            <small class="text-muted">جنيه/سنة</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Payment Methods -->
                <div class="card shadow-sm mb-4">
                    <div class="card-body">
                        <h5 class="card-title mb-4">طرق الدفع المتاحة</h5>
                        
                        <form action="{{ route('shop-owner.process-payment') }}" method="POST" id="paymentForm">
                            @csrf
                            <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                            <input type="hidden" name="billing_cycle" value="{{ $billingCycle }}">
                            
                            <div class="payment-methods">
                                @foreach($paymentMethods as $key => $method)
                                <div class="payment-method-card mb-3">
                                    <input type="radio" 
                                           name="payment_method" 
                                           id="payment_{{ $key }}" 
                                           value="{{ $key }}"
                                           class="payment-radio"
                                           required>
                                    <label for="payment_{{ $key }}" class="payment-label">
                                        <div class="d-flex align-items-center">
                                            <span class="payment-icon">{{ $method['icon'] }}</span>
                                            <div class="flex-grow-1">
                                                <h6 class="mb-1">{{ $method['name'] }}</h6>
                                                <p class="text-muted mb-0 small">{{ $method['description'] }}</p>
                                                @if($method['requires_verification'])
                                                    <small class="text-warning">
                                                        <i class="fas fa-clock"></i> يتطلب التحقق - {{ $method['processing_time'] }}
                                                    </small>
                                                @endif
                                            </div>
                                            @if(!$method['is_active'])
                                                <span class="badge bg-secondary">قريباً</span>
                                            @endif
                                        </div>
                                        
                                    </label>
                                    
                                    <!-- Bank Transfer Details (outside label) -->
                                    @if($key === 'bank_transfer' && isset($method['bank_details']))
                                    <div class="bank-details mt-3 p-3 bg-light rounded d-none" id="bank_details_{{ $key }}">
                                        <h6 class="mb-2">تفاصيل الحساب البنكي:</h6>
                                        <p class="mb-1"><strong>البنك:</strong> {{ $method['bank_details']['bank_name'] }}</p>
                                        <p class="mb-1"><strong>اسم الحساب:</strong> {{ $method['bank_details']['account_name'] }}</p>
                                        <p class="mb-1"><strong>رقم الحساب:</strong> {{ $method['bank_details']['account_number'] }}</p>
                                        <p class="mb-0"><strong>IBAN:</strong> {{ $method['bank_details']['iban'] }}</p>
                                        <div class="alert alert-info mt-2 mb-0">
                                            <small>يرجى إرفاق صورة من إيصال التحويل بعد إتمام العملية</small>
                                        </div>
                                    </div>
                                    @endif
                                </div>
                                @endforeach
                            </div>

                            @error('payment_method')
                                <div class="alert alert-danger mt-3">{{ $message }}</div>
                            @enderror

                            <!-- Payment Details (for methods that require it) -->
                            <div id="payment_details_section" class="d-none mt-4">
                                <h6 class="mb-3">تفاصيل إضافية</h6>
                                
                                <!-- Transfer Receipt Upload -->
                                <div id="receipt_upload" class="d-none">
                                    <div class="mb-3">
                                        <label for="transfer_receipt" class="form-label">إيصال التحويل</label>
                                        <input type="file" 
                                               class="form-control" 
                                               id="transfer_receipt" 
                                               name="transfer_receipt"
                                               accept="image/*,.pdf">
                                        <small class="text-muted">صورة الإيصال أو ملف PDF</small>
                                    </div>
                                    <div class="mb-3">
                                        <label for="transfer_reference" class="form-label">رقم المرجع</label>
                                        <input type="text" 
                                               class="form-control" 
                                               id="transfer_reference" 
                                               name="transfer_reference"
                                               placeholder="رقم المرجع من البنك">
                                    </div>
                                </div>

                                <!-- Notes -->
                                <div class="mb-3">
                                    <label for="payment_notes" class="form-label">ملاحظات (اختياري)</label>
                                    <textarea class="form-control" 
                                              id="payment_notes" 
                                              name="payment_notes" 
                                              rows="3"
                                              placeholder="أي ملاحظات إضافية"></textarea>
                                </div>
                            </div>

                            <!-- Submit Button -->
                            <div class="d-grid gap-2 mt-4">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="fas fa-check-circle"></i> تأكيد الدفع
                                </button>
                                <a href="{{ route('shop-owner.subscriptions') }}" class="btn btn-outline-secondary">
                                    العودة للباقات
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <!-- Terms -->
                <div class="text-center text-muted small">
                    <p>بالمتابعة، أنت توافق على <a href="#">شروط الخدمة</a> و <a href="#">سياسة الخصوصية</a></p>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.payment-container {
    background: #f8f9fa;
    min-height: 100vh;
}

.billing-option {
    padding: 1.25rem;
    border: 2px solid #dee2e6;
    border-radius: 0.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
    position: relative;
}

.billing-option:hover {
    border-color: #0d6efd;
    background: #f8f9ff;
}

.billing-option.active {
    border-color: #0d6efd;
    background: #e7f1ff;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
}

.payment-methods {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.payment-method-card {
    position: relative;
}

.payment-radio {
    position: absolute;
    opacity: 0;
    width: 0;
    height: 0;
}

.payment-label {
    display: block;
    padding: 1.25rem;
    border: 2px solid #dee2e6;
    border-radius: 0.5rem;
    cursor: pointer;
    transition: all 0.3s ease;
    margin: 0;
}

.payment-label:hover {
    border-color: #0d6efd;
    background: #f8f9ff;
}

.payment-radio:checked + .payment-label {
    border-color: #0d6efd;
    background: #e7f1ff;
    box-shadow: 0 0 0 3px rgba(13, 110, 253, 0.1);
}

.payment-icon {
    font-size: 2rem;
    margin-left: 1rem;
}

.bank-details {
    font-size: 0.9rem;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const paymentRadios = document.querySelectorAll('.payment-radio');
    const paymentDetailsSection = document.getElementById('payment_details_section');
    const receiptUpload = document.getElementById('receipt_upload');
    
    paymentRadios.forEach(radio => {
        radio.addEventListener('change', function() {
            // Hide all bank details sections
            document.querySelectorAll('[id^="bank_details_"]').forEach(el => {
                el.classList.add('d-none');
            });
            
            // Hide receipt upload
            if (receiptUpload) receiptUpload.classList.add('d-none');
            
            // Show relevant sections based on selected method
            if (this.value === 'bank_transfer') {
                const bankDetails = document.getElementById('bank_details_' + this.value);
                if (bankDetails) bankDetails.classList.remove('d-none');
                if (paymentDetailsSection) paymentDetailsSection.classList.remove('d-none');
                if (receiptUpload) receiptUpload.classList.remove('d-none');
            } else if (this.value === 'cash') {
                if (paymentDetailsSection) paymentDetailsSection.classList.remove('d-none');
            } else {
                if (paymentDetailsSection) paymentDetailsSection.classList.add('d-none');
            }
        });
    });
});
</script>
@endsection
