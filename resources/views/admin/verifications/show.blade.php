@extends('layouts.admin')

@section('title', 'تفاصيل التحقق')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-shield-check text-primary"></i>
            تفاصيل التحقق #{{ $verification->id }}
        </h1>
        <a href="{{ route('admin.verifications.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i>
            العودة للقائمة
        </a>
    </div>

    <div class="row">
        <!-- Main Info -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h6 class="m-0">
                        <i class="fas fa-info-circle"></i>
                        المعلومات الأساسية
                    </h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong><i class="fas fa-user text-primary"></i> الاسم:</strong>
                            <p>{{ $verification->name ?? 'غير متوفر' }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong><i class="fas fa-envelope text-success"></i> البريد الإلكتروني:</strong>
                            <p>{{ $verification->email ?? 'غير متوفر' }}</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong><i class="fas fa-comment-dots text-info"></i> الرسالة:</strong>
                        <div class="alert alert-light mt-2">
                            {{ $verification->message }}
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong><i class="fas fa-calendar text-warning"></i> تاريخ التحقق:</strong>
                            <p>{{ $verification->verified_at->format('Y-m-d H:i:s') }}</p>
                            <small class="text-muted">{{ $verification->verified_at->diffForHumans() }}</small>
                        </div>
                        <div class="col-md-6">
                            <strong><i class="fas fa-check-circle text-success"></i> الحالة:</strong>
                            <p>
                                @if($verification->is_verified)
                                    <span class="badge badge-success">تم التحقق</span>
                                @else
                                    <span class="badge badge-warning">معلق</span>
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Technical Info -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="m-0">
                        <i class="fas fa-network-wired"></i>
                        معلومات تقنية
                    </h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong><i class="fas fa-globe text-primary"></i> IP Address:</strong>
                        <p><code>{{ $verification->ip_address }}</code></p>
                    </div>

                    <div class="mb-3">
                        <strong><i class="fas fa-browser text-info"></i> المتصفح:</strong>
                        <p>{{ $verification->browser ?? 'Unknown' }}</p>
                    </div>

                    <div class="mb-3">
                        <strong><i class="fas fa-mobile-alt text-success"></i> الجهاز:</strong>
                        <p>
                            @if($verification->device === 'Mobile')
                                <i class="fas fa-mobile-alt"></i> موبايل
                            @elseif($verification->device === 'Tablet')
                                <i class="fas fa-tablet-alt"></i> تابلت
                            @else
                                <i class="fas fa-desktop"></i> كمبيوتر
                            @endif
                        </p>
                    </div>

                    <div class="mb-0">
                        <strong><i class="fas fa-fingerprint text-warning"></i> Session ID:</strong>
                        <p><small><code>{{ $verification->session_id }}</code></small></p>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="card shadow">
                <div class="card-header bg-danger text-white">
                    <h6 class="m-0">
                        <i class="fas fa-cog"></i>
                        الإجراءات
                    </h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.verifications.destroy', $verification) }}" 
                          method="POST"
                          onsubmit="return confirm('هل أنت متأكد من حذف هذا التحقق؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-block">
                            <i class="fas fa-trash"></i>
                            حذف التحقق
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- User Agent -->
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header bg-secondary text-white">
                    <h6 class="m-0">
                        <i class="fas fa-code"></i>
                        User Agent الكامل
                    </h6>
                </div>
                <div class="card-body">
                    <code style="word-break: break-all;">{{ $verification->user_agent }}</code>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
