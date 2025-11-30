@extends('layouts.admin')

@section('content')
<div class="container-fluid py-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="fas fa-shopping-cart"></i> تفاصيل الإعلان</h2>
        <a href="{{ route('admin.marketplace.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> العودة
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show">
        <i class="fas fa-check-circle"></i> {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="row">
        <!-- Item Details -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">معلومات الإعلان</h5>
                </div>
                <div class="card-body">
                    <!-- Status Badge -->
                    <div class="mb-3">
                        @switch($item->status)
                            @case('active')
                                <span class="badge bg-success fs-6">
                                    <i class="fas fa-check-circle"></i> نشط
                                </span>
                                @break
                            @case('pending')
                                <span class="badge bg-warning fs-6">
                                    <i class="fas fa-clock"></i> قيد المراجعة
                                </span>
                                @break
                            @case('rejected')
                                <span class="badge bg-danger fs-6">
                                    <i class="fas fa-times-circle"></i> مرفوض
                                </span>
                                @break
                            @case('sold')
                                <span class="badge bg-secondary fs-6">
                                    <i class="fas fa-check"></i> مباع
                                </span>
                                @break
                        @endswitch
                        
                        @if($item->is_sponsored && $item->sponsored_until > now())
                        <span class="badge bg-warning text-dark fs-6 ms-2">
                            <i class="fas fa-star"></i> مميز (متبقي {{ now()->diffInDays($item->sponsored_until) }} يوم)
                        </span>
                        @endif
                    </div>

                    <!-- Images Gallery -->
                    @if($item->images && count($item->images) > 0)
                    <div class="mb-4">
                        <h6>الصور</h6>
                        <div class="row g-2">
                            @foreach($item->images as $index => $image)
                            <div class="col-md-3">
                                <img src="{{ $image }}" alt="Image {{ $index + 1 }}" 
                                     class="img-fluid rounded" style="cursor: pointer; height: 150px; object-fit: cover; width: 100%;"
                                     onclick="showImageModal('{{ $image }}')">
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    <!-- Item Info -->
                    <h3 class="mb-3">{{ $item->title }}</h3>
                    
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong>السعر:</strong> 
                                <span class="text-primary fs-5">{{ number_format($item->price, 0) }} جنيه</span>
                                @if($item->is_negotiable)
                                <span class="badge bg-info">قابل للتفاوض</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>الحالة:</strong> 
                                @switch($item->condition)
                                    @case('new')
                                        <span class="badge bg-success">جديد</span>
                                        @break
                                    @case('like_new')
                                        <span class="badge bg-info">شبه جديد</span>
                                        @break
                                    @case('good')
                                        <span class="badge bg-primary">جيد</span>
                                        @break
                                    @case('fair')
                                        <span class="badge bg-warning">مقبول</span>
                                        @break
                                @endswitch
                            </p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <p><strong><i class="fas fa-map-marker-alt"></i> المدينة:</strong> {{ $item->city->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <p><strong><i class="fas fa-tag"></i> التصنيف:</strong> {{ $item->category->name }}</p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>الوصف:</strong>
                        <p class="mt-2">{{ $item->description }}</p>
                    </div>

                    @if($item->status === 'rejected' && $item->rejection_reason)
                    <div class="alert alert-danger">
                        <strong><i class="fas fa-exclamation-triangle"></i> سبب الرفض:</strong><br>
                        {{ $item->rejection_reason }}
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- User Info -->
            <div class="card shadow mb-4">
                <div class="card-header bg-info text-white">
                    <h6 class="mb-0">معلومات المعلن</h6>
                </div>
                <div class="card-body">
                    <p><strong>الاسم:</strong> {{ $item->user->name }}</p>
                    <p><strong>البريد:</strong> {{ $item->user->email }}</p>
                    <p><strong>الهاتف:</strong> {{ $item->contact_phone }}</p>
                    @if($item->contact_whatsapp)
                    <p><strong>واتساب:</strong> {{ $item->contact_whatsapp }}</p>
                    @endif
                </div>
            </div>

            <!-- Statistics -->
            <div class="card shadow mb-4">
                <div class="card-header bg-secondary text-white">
                    <h6 class="mb-0">الإحصائيات</h6>
                </div>
                <div class="card-body">
                    <p><strong>المشاهدات:</strong> {{ $item->view_count }} / {{ $item->max_views + $item->sponsored_views_boost }}</p>
                    <div class="progress mb-3" style="height: 20px;">
                        @php
                            $percentage = ($item->view_count / ($item->max_views + $item->sponsored_views_boost)) * 100;
                        @endphp
                        <div class="progress-bar bg-{{ $percentage > 80 ? 'danger' : ($percentage > 50 ? 'warning' : 'success') }}" 
                             style="width: {{ min($percentage, 100) }}%">
                            {{ round($percentage) }}%
                        </div>
                    </div>
                    <p><strong>الاتصالات:</strong> {{ $item->contact_count }}</p>
                    <p><strong>تاريخ الإنشاء:</strong> {{ $item->created_at->format('Y-m-d H:i') }}</p>
                    @if($item->approved_at)
                    <p><strong>تاريخ الموافقة:</strong> {{ $item->approved_at->format('Y-m-d H:i') }}</p>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            @if($item->status === 'pending')
            <div class="card shadow mb-4">
                <div class="card-header bg-warning text-dark">
                    <h6 class="mb-0">الإجراءات</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.marketplace.approve', $item->id) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-success w-100" 
                                onclick="return confirm('هل تريد الموافقة على هذا الإعلان؟')">
                            <i class="fas fa-check"></i> الموافقة على الإعلان
                        </button>
                    </form>

                    <button type="button" class="btn btn-danger w-100" 
                            data-bs-toggle="modal" data-bs-target="#rejectModal">
                        <i class="fas fa-times"></i> رفض الإعلان
                    </button>
                </div>
            </div>
            @endif

            <!-- Delete -->
            <div class="card shadow border-danger">
                <div class="card-header bg-danger text-white">
                    <h6 class="mb-0">منطقة الخطر</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.marketplace.destroy', $item->id) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger w-100"
                                onclick="return confirm('هل أنت متأكد من حذف هذا الإعلان نهائياً؟ لا يمكن التراجع عن هذا الإجراء.')">
                            <i class="fas fa-trash"></i> حذف الإعلان نهائياً
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Reject Modal -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('admin.marketplace.reject', $item->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">رفض الإعلان</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>رفض الإعلان: <strong>{{ $item->title }}</strong></p>
                    <div class="mb-3">
                        <label class="form-label">سبب الرفض <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" class="form-control" rows="4" 
                                  placeholder="أدخل سبب الرفض بشكل واضح..." required></textarea>
                        <small class="text-muted">سيتم إرسال هذا السبب إلى المستخدم</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="fas fa-times"></i> رفض الإعلان
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Image Modal -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-body p-0">
                <img id="modalImage" src="" class="w-100">
            </div>
        </div>
    </div>
</div>

<script>
function showImageModal(imageUrl) {
    document.getElementById('modalImage').src = imageUrl;
    new bootstrap.Modal(document.getElementById('imageModal')).show();
}
</script>
@endsection
