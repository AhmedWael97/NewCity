@extends('layouts.admin')

@section('title', 'تفاصيل اقتراح المتجر')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">تفاصيل اقتراح المتجر</h1>
        <a href="{{ route('admin.shop-suggestions.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right me-2"></i>العودة للقائمة
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <!-- Shop Details Card -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-store me-2"></i>معلومات المتجر</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">اسم المتجر</label>
                            <p class="fw-bold">{{ $suggestion->shop_name }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">المدينة</label>
                            <p class="fw-bold">{{ $suggestion->city->name ?? 'غير محدد' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">الفئة</label>
                            <p class="fw-bold">{{ $suggestion->category->name ?? 'غير محدد' }}</p>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="text-muted small">الحالة</label>
                            <p>
                                @if($suggestion->status === 'pending')
                                    <span class="badge bg-warning">قيد المراجعة</span>
                                @elseif($suggestion->status === 'approved')
                                    <span class="badge bg-success">موافق عليه</span>
                                @elseif($suggestion->status === 'rejected')
                                    <span class="badge bg-danger">مرفوض</span>
                                @elseif($suggestion->status === 'completed')
                                    <span class="badge bg-info">مكتمل</span>
                                @endif
                            </p>
                        </div>
                        @if($suggestion->description)
                            <div class="col-md-12 mb-3">
                                <label class="text-muted small">الوصف</label>
                                <p>{{ $suggestion->description }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Contact Information Card -->
            <div class="card mb-4">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0"><i class="fas fa-phone me-2"></i>معلومات الاتصال</h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @if($suggestion->phone)
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">رقم الهاتف</label>
                                <p><a href="tel:{{ $suggestion->phone }}">{{ $suggestion->phone }}</a></p>
                            </div>
                        @endif
                        @if($suggestion->whatsapp)
                            <div class="col-md-6 mb-3">
                                <label class="text-muted small">واتساب</label>
                                <p><a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $suggestion->whatsapp) }}" target="_blank">{{ $suggestion->whatsapp }}</a></p>
                            </div>
                        @endif
                        @if($suggestion->email)
                            <div class="col-md-12 mb-3">
                                <label class="text-muted small">البريد الإلكتروني</label>
                                <p><a href="mailto:{{ $suggestion->email }}">{{ $suggestion->email }}</a></p>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Location Information Card -->
            @if($suggestion->address || $suggestion->google_maps_url)
                <div class="card mb-4">
                    <div class="card-header bg-success text-white">
                        <h5 class="mb-0"><i class="fas fa-map-marker-alt me-2"></i>معلومات الموقع</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if($suggestion->address)
                                <div class="col-md-12 mb-3">
                                    <label class="text-muted small">العنوان</label>
                                    <p>{{ $suggestion->address }}</p>
                                </div>
                            @endif
                            @if($suggestion->google_maps_url)
                                <div class="col-md-12 mb-3">
                                    <label class="text-muted small">رابط خرائط جوجل</label>
                                    <p><a href="{{ $suggestion->google_maps_url }}" target="_blank">عرض على الخريطة</a></p>
                                </div>
                            @endif
                            @if($suggestion->latitude && $suggestion->longitude)
                                <div class="col-md-12">
                                    <label class="text-muted small">الإحداثيات</label>
                                    <p>{{ $suggestion->latitude }}, {{ $suggestion->longitude }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif

            <!-- Additional Information Card -->
            @if($suggestion->website || $suggestion->social_media || $suggestion->opening_hours)
                <div class="card mb-4">
                    <div class="card-header bg-secondary text-white">
                        <h5 class="mb-0"><i class="fas fa-info-circle me-2"></i>معلومات إضافية</h5>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @if($suggestion->website)
                                <div class="col-md-12 mb-3">
                                    <label class="text-muted small">الموقع الإلكتروني</label>
                                    <p><a href="{{ $suggestion->website }}" target="_blank">{{ $suggestion->website }}</a></p>
                                </div>
                            @endif
                            @if($suggestion->social_media && is_array($suggestion->social_media))
                                <div class="col-md-12 mb-3">
                                    <label class="text-muted small">وسائل التواصل الاجتماعي</label>
                                    <div class="d-flex gap-2">
                                        @if(!empty($suggestion->social_media['facebook']))
                                            <a href="{{ $suggestion->social_media['facebook'] }}" target="_blank" class="btn btn-sm btn-primary">
                                                <i class="fab fa-facebook"></i> Facebook
                                            </a>
                                        @endif
                                        @if(!empty($suggestion->social_media['instagram']))
                                            <a href="{{ $suggestion->social_media['instagram'] }}" target="_blank" class="btn btn-sm btn-danger">
                                                <i class="fab fa-instagram"></i> Instagram
                                            </a>
                                        @endif
                                    </div>
                                </div>
                            @endif
                            @if($suggestion->opening_hours)
                                <div class="col-md-12">
                                    <label class="text-muted small">أوقات العمل</label>
                                    <p>{{ $suggestion->opening_hours }}</p>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <div class="col-lg-4">
            <!-- Status Management Card -->
            <div class="card mb-4">
                <div class="card-header bg-warning">
                    <h5 class="mb-0"><i class="fas fa-tasks me-2"></i>إدارة الحالة</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.shop-suggestions.update-status', $suggestion) }}" method="POST">
                        @csrf
                        @method('PATCH')
                        
                        <div class="mb-3">
                            <label class="form-label">الحالة</label>
                            <select name="status" class="form-select" required>
                                <option value="pending" {{ $suggestion->status === 'pending' ? 'selected' : '' }}>قيد المراجعة</option>
                                <option value="approved" {{ $suggestion->status === 'approved' ? 'selected' : '' }}>موافق عليه</option>
                                <option value="rejected" {{ $suggestion->status === 'rejected' ? 'selected' : '' }}>مرفوض</option>
                                <option value="completed" {{ $suggestion->status === 'completed' ? 'selected' : '' }}>مكتمل</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">ملاحظات المسؤول</label>
                            <textarea name="admin_notes" class="form-control" rows="3">{{ $suggestion->admin_notes }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100">
                            <i class="fas fa-save me-2"></i>حفظ التغييرات
                        </button>
                    </form>
                    
                    <hr class="my-3">
                    
                    <a href="{{ route('admin.shops.create', ['suggestion_id' => $suggestion->id]) }}" class="btn btn-success w-100">
                        <i class="fas fa-store me-2"></i>إنشاء متجر من هذا الاقتراح
                    </a>
                </div>
            </div>

            <!-- Suggester Information Card -->
            <div class="card mb-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="mb-0"><i class="fas fa-user me-2"></i>معلومات المقترح</h5>
                </div>
                <div class="card-body">
                    @if($suggestion->user)
                        <div class="mb-3">
                            <label class="text-muted small">المستخدم</label>
                            <p class="fw-bold">{{ $suggestion->user->name }}</p>
                        </div>
                    @endif
                    @if($suggestion->suggested_by_name)
                        <div class="mb-3">
                            <label class="text-muted small">الاسم</label>
                            <p>{{ $suggestion->suggested_by_name }}</p>
                        </div>
                    @endif
                    @if($suggestion->suggested_by_phone)
                        <div class="mb-3">
                            <label class="text-muted small">الهاتف</label>
                            <p><a href="tel:{{ $suggestion->suggested_by_phone }}">{{ $suggestion->suggested_by_phone }}</a></p>
                        </div>
                    @endif
                    @if($suggestion->suggested_by_email)
                        <div class="mb-3">
                            <label class="text-muted small">البريد الإلكتروني</label>
                            <p><a href="mailto:{{ $suggestion->suggested_by_email }}">{{ $suggestion->suggested_by_email }}</a></p>
                        </div>
                    @endif
                    <div class="mb-3">
                        <label class="text-muted small">تاريخ الاقتراح</label>
                        <p>{{ $suggestion->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                </div>
            </div>

            <!-- Review Information Card -->
            @if($suggestion->reviewed_at)
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-check-circle me-2"></i>معلومات المراجعة</h5>
                    </div>
                    <div class="card-body">
                        @if($suggestion->reviewer)
                            <div class="mb-3">
                                <label class="text-muted small">تمت المراجعة بواسطة</label>
                                <p class="fw-bold">{{ $suggestion->reviewer->name }}</p>
                            </div>
                        @endif
                        <div class="mb-3">
                            <label class="text-muted small">تاريخ المراجعة</label>
                            <p>{{ $suggestion->reviewed_at->format('Y-m-d H:i') }}</p>
                        </div>
                        @if($suggestion->admin_notes)
                            <div>
                                <label class="text-muted small">ملاحظات</label>
                                <p>{{ $suggestion->admin_notes }}</p>
                            </div>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Actions Card -->
            <div class="card">
                <div class="card-header bg-danger text-white">
                    <h5 class="mb-0"><i class="fas fa-cog me-2"></i>إجراءات</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.shop-suggestions.destroy', $suggestion) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذا الاقتراح؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100">
                            <i class="fas fa-trash me-2"></i>حذف الاقتراح
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
