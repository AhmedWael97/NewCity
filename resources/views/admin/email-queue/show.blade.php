@extends('layouts.admin')

@section('title', 'Email Details')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">تفاصيل البريد الإلكتروني</h1>
        <a href="{{ route('admin.email-queue.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> العودة
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Email Details Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-envelope"></i> معلومات البريد
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-3"><strong>ID:</strong></div>
                        <div class="col-md-9">{{ $email->id }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3"><strong>المصدر:</strong></div>
                        <div class="col-md-9">
                            @if($email->source === 'api')
                                <span class="badge badge-primary"><i class="fas fa-code"></i> API Request</span>
                            @elseif($email->source === 'web')
                                <span class="badge badge-secondary"><i class="fas fa-globe"></i> Web Form</span>
                            @else
                                <span class="badge badge-dark"><i class="fas fa-cog"></i> System</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3"><strong>نوع الحدث:</strong></div>
                        <div class="col-md-9">
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
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3"><strong>الحالة:</strong></div>
                        <div class="col-md-9">
                            @if($email->status == 'pending')
                                <span class="badge badge-warning">قيد الانتظار</span>
                            @elseif($email->status == 'processing')
                                <span class="badge badge-primary">جاري المعالجة</span>
                            @elseif($email->status == 'sent')
                                <span class="badge badge-success">تم الإرسال</span>
                            @else
                                <span class="badge badge-danger">فشل</span>
                            @endif
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3"><strong>الموضوع:</strong></div>
                        <div class="col-md-9">{{ $email->subject }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3"><strong>المحتوى:</strong></div>
                        <div class="col-md-9">
                            <div class="border p-3 bg-light" style="white-space: pre-wrap;">{{ $email->body }}</div>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3"><strong>المستلمين:</strong></div>
                        <div class="col-md-9">
                            @foreach($email->recipients as $recipient)
                                <span class="badge badge-secondary">{{ $recipient }}</span>
                            @endforeach
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3"><strong>عدد المحاولات:</strong></div>
                        <div class="col-md-9">{{ $email->attempts }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-3"><strong>تاريخ الإنشاء:</strong></div>
                        <div class="col-md-9">{{ $email->created_at->format('Y-m-d H:i:s') }}</div>
                    </div>

                    @if($email->sent_at)
                        <div class="row mb-3">
                            <div class="col-md-3"><strong>تاريخ الإرسال:</strong></div>
                            <div class="col-md-9">{{ $email->sent_at->format('Y-m-d H:i:s') }}</div>
                        </div>
                    @endif

                    @if($email->error_message)
                        <div class="row mb-3">
                            <div class="col-md-3"><strong>رسالة الخطأ:</strong></div>
                            <div class="col-md-9">
                                <div class="alert alert-danger mb-0">{{ $email->error_message }}</div>
                            </div>
                        </div>
                    @endif

                    @if($email->event_data)
                        <div class="row mb-3">
                            <div class="col-md-3"><strong>بيانات الحدث:</strong></div>
                            <div class="col-md-9">
                                <pre class="border p-3 bg-light mb-0">{{ json_encode($email->event_data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE) }}</pre>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Actions Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-primary text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-tasks"></i> الإجراءات
                    </h6>
                </div>
                <div class="card-body">
                    @if($email->status == 'failed')
                        <form method="POST" action="{{ route('admin.email-queue.retry', $email->id) }}" class="mb-2">
                            @csrf
                            <button type="submit" class="btn btn-warning btn-block">
                                <i class="fas fa-redo"></i> إعادة المحاولة
                            </button>
                        </form>
                    @endif

                    <form method="POST" action="{{ route('admin.email-queue.destroy', $email->id) }}" onsubmit="return confirm('هل أنت متأكد من حذف هذا البريد؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block">
                            <i class="fas fa-trash"></i> حذف البريد
                        </button>
                    </form>
                </div>
            </div>

            <!-- Timeline Card -->
            <div class="card shadow">
                <div class="card-header py-3 bg-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-history"></i> التسلسل الزمني
                    </h6>
                </div>
                <div class="card-body">
                    <div class="timeline">
                        <div class="timeline-item">
                            <i class="fas fa-plus-circle text-primary"></i>
                            <div class="timeline-content">
                                <small class="text-muted">تم الإنشاء</small>
                                <p class="mb-0">{{ $email->created_at->format('Y-m-d H:i:s') }}</p>
                            </div>
                        </div>

                        @if($email->sent_at)
                            <div class="timeline-item">
                                <i class="fas fa-check-circle text-success"></i>
                                <div class="timeline-content">
                                    <small class="text-muted">تم الإرسال</small>
                                    <p class="mb-0">{{ $email->sent_at->format('Y-m-d H:i:s') }}</p>
                                </div>
                            </div>
                        @endif

                        @if($email->status == 'failed')
                            <div class="timeline-item">
                                <i class="fas fa-exclamation-circle text-danger"></i>
                                <div class="timeline-content">
                                    <small class="text-muted">فشل الإرسال</small>
                                    <p class="mb-0">{{ $email->updated_at->format('Y-m-d H:i:s') }}</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.timeline {
    position: relative;
    padding: 0;
}

.timeline-item {
    position: relative;
    padding-left: 35px;
    padding-bottom: 20px;
}

.timeline-item:before {
    content: '';
    position: absolute;
    left: 7px;
    top: 20px;
    bottom: -10px;
    width: 2px;
    background: #e9ecef;
}

.timeline-item:last-child:before {
    display: none;
}

.timeline-item i {
    position: absolute;
    left: 0;
    top: 0;
}

.timeline-content {
    padding-left: 10px;
}
</style>
@endsection
