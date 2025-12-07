@extends('layouts.admin')

@section('title', 'تفاصيل اقتراح المدينة')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-2">تفاصيل اقتراح المدينة</h1>
            <p class="text-muted mb-0">معلومات تفصيلية عن الاقتراح</p>
        </div>
        <a href="{{ route('admin.city-suggestions.index') }}" class="btn btn-outline-secondary">
            <i class="fas fa-arrow-right me-2"></i>
            العودة للقائمة
        </a>
    </div>

    <div class="row">
        <!-- Main Information -->
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">معلومات الاقتراح</h5>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">اسم المدينة</label>
                            <h5 class="mb-0">{{ $suggestion->city_name }}</h5>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">رقم الهاتف</label>
                            <h5 class="mb-0">
                                <a href="tel:{{ $suggestion->phone }}" class="text-decoration-none">
                                    <i class="fas fa-phone me-2"></i>{{ $suggestion->phone }}
                                </a>
                            </h5>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label text-muted">رابط المجموعة الرئيسية</label>
                        <div class="input-group">
                            <input type="text" class="form-control" value="{{ $suggestion->group_url }}" readonly>
                            <a href="{{ $suggestion->group_url }}" target="_blank" class="btn btn-outline-primary">
                                <i class="fas fa-external-link-alt"></i> فتح
                            </a>
                        </div>
                    </div>

                    <hr>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted">الحالة</label>
                            <div>
                                @if($suggestion->status == 'pending')
                                    <span class="badge bg-warning fs-6">
                                        <i class="fas fa-clock me-1"></i>قيد المراجعة
                                    </span>
                                @elseif($suggestion->status == 'approved')
                                    <span class="badge bg-success fs-6">
                                        <i class="fas fa-check-circle me-1"></i>موافق عليها
                                    </span>
                                @else
                                    <span class="badge bg-danger fs-6">
                                        <i class="fas fa-times-circle me-1"></i>مرفوضة
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted">تاريخ الإرسال</label>
                            <div>
                                <i class="fas fa-calendar me-2"></i>
                                {{ $suggestion->created_at->format('Y-m-d h:i A') }}
                                <small class="text-muted">({{ $suggestion->created_at->diffForHumans() }})</small>
                            </div>
                        </div>
                    </div>

                    @if($suggestion->admin_notes)
                        <div class="mb-3">
                            <label class="form-label text-muted">ملاحظات الإدارة</label>
                            <div class="alert alert-info mb-0">
                                <i class="fas fa-info-circle me-2"></i>
                                {{ $suggestion->admin_notes }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Technical Information -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">المعلومات التقنية</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="form-label text-muted">عنوان IP</label>
                        <div>
                            <code>{{ $suggestion->ip_address ?? 'غير متوفر' }}</code>
                        </div>
                    </div>
                    <div class="mb-0">
                        <label class="form-label text-muted">User Agent</label>
                        <div>
                            <small class="text-muted">{{ $suggestion->user_agent ?? 'غير متوفر' }}</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Actions Sidebar -->
        <div class="col-lg-4">
            <!-- Status Update -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">تحديث الحالة</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.city-suggestions.update-status', $suggestion) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        
                        <div class="mb-3">
                            <label class="form-label">الحالة</label>
                            <select name="status" class="form-select" required>
                                <option value="pending" {{ $suggestion->status == 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                                <option value="approved" {{ $suggestion->status == 'approved' ? 'selected' : '' }}>موافق عليها</option>
                                <option value="rejected" {{ $suggestion->status == 'rejected' ? 'selected' : '' }}>مرفوضة</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">ملاحظات الإدارة</label>
                            <textarea name="admin_notes" class="form-control" rows="4" 
                                      placeholder="أضف ملاحظات للاقتراح...">{{ $suggestion->admin_notes }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-2"></i>
                            حفظ التغييرات
                        </button>
                    </form>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">إجراءات سريعة</h5>
                </div>
                <div class="card-body">
                    @if($suggestion->status == 'pending')
                        <form action="{{ route('admin.city-suggestions.update-status', $suggestion) }}" method="POST" class="mb-2">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="approved">
                            <button type="submit" class="btn btn-success w-100 mb-2">
                                <i class="fas fa-check-circle me-2"></i>
                                موافقة على الاقتراح
                            </button>
                        </form>

                        <form action="{{ route('admin.city-suggestions.update-status', $suggestion) }}" method="POST" class="mb-2">
                            @csrf
                            @method('PATCH')
                            <input type="hidden" name="status" value="rejected">
                            <button type="submit" class="btn btn-warning w-100 mb-2">
                                <i class="fas fa-times-circle me-2"></i>
                                رفض الاقتراح
                            </button>
                        </form>
                    @endif

                    <form action="{{ route('admin.city-suggestions.destroy', $suggestion) }}" method="POST" 
                          onsubmit="return confirm('هل أنت متأكد من حذف هذا الاقتراح؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-trash me-2"></i>
                            حذف الاقتراح
                        </button>
                    </form>
                </div>
            </div>

            <!-- WhatsApp Contact -->
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3">
                    <h5 class="mb-0">التواصل</h5>
                </div>
                <div class="card-body">
                    <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $suggestion->phone) }}" 
                       target="_blank" class="btn btn-success w-100 mb-2">
                        <i class="fab fa-whatsapp me-2"></i>
                        التواصل عبر واتساب
                    </a>
                    <a href="tel:{{ $suggestion->phone }}" class="btn btn-outline-primary w-100">
                        <i class="fas fa-phone me-2"></i>
                        اتصال هاتفي
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
