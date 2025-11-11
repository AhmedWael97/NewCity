@extends('layouts.admin')

@section('title', 'تعديل التقييم')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-edit"></i> تعديل التقييم
        </h1>
        <a href="{{ route('admin.ratings.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-right"></i> العودة للقائمة
        </a>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">معلومات التقييم</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.ratings.update', $rating) }}">
                        @csrf
                        @method('PUT')
                        
                        <!-- Rating Info Card -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-info">معلومات التقييم الأساسية</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="user_id">المستخدم <span class="text-danger">*</span></label>
                                            <select class="form-control @error('user_id') is-invalid @enderror" 
                                                    id="user_id" name="user_id" required>
                                                <option value="">-- اختر المستخدم --</option>
                                                @foreach(\App\Models\User::all() as $user)
                                                    <option value="{{ $user->id }}" {{ old('user_id', $rating->user_id) == $user->id ? 'selected' : '' }}>
                                                        {{ $user->name }} ({{ $user->email }})
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('user_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="shop_id">المتجر <span class="text-danger">*</span></label>
                                            <select class="form-control @error('shop_id') is-invalid @enderror" 
                                                    id="shop_id" name="shop_id" required>
                                                <option value="">-- اختر المتجر --</option>
                                                @foreach(\App\Models\Shop::all() as $shop)
                                                    <option value="{{ $shop->id }}" {{ old('shop_id', $rating->shop_id) == $shop->id ? 'selected' : '' }}>
                                                        {{ $shop->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('shop_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="rating">التقييم <span class="text-danger">*</span></label>
                                            <div class="input-group">
                                                <select class="form-control @error('rating') is-invalid @enderror" 
                                                        id="rating" name="rating" required>
                                                    <option value="">-- اختر التقييم --</option>
                                                    <option value="1" {{ old('rating', $rating->rating) == 1 ? 'selected' : '' }}>
                                                        ⭐ 1 نجمة - سيء جداً
                                                    </option>
                                                    <option value="2" {{ old('rating', $rating->rating) == 2 ? 'selected' : '' }}>
                                                        ⭐⭐ 2 نجمة - سيء
                                                    </option>
                                                    <option value="3" {{ old('rating', $rating->rating) == 3 ? 'selected' : '' }}>
                                                        ⭐⭐⭐ 3 نجمة - مقبول
                                                    </option>
                                                    <option value="4" {{ old('rating', $rating->rating) == 4 ? 'selected' : '' }}>
                                                        ⭐⭐⭐⭐ 4 نجمة - جيد
                                                    </option>
                                                    <option value="5" {{ old('rating', $rating->rating) == 5 ? 'selected' : '' }}>
                                                        ⭐⭐⭐⭐⭐ 5 نجمة - ممتاز
                                                    </option>
                                                </select>
                                                <div class="input-group-append">
                                                    <span class="input-group-text" id="rating-display">
                                                        {{ str_repeat('⭐', $rating->rating) }}
                                                    </span>
                                                </div>
                                            </div>
                                            @error('rating')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="status">حالة التقييم</label>
                                            <select class="form-control @error('status') is-invalid @enderror" 
                                                    id="status" name="status">
                                                <option value="approved" {{ old('status', $rating->status) == 'approved' ? 'selected' : '' }}>
                                                    ✅ موافق عليه
                                                </option>
                                                <option value="pending" {{ old('status', $rating->status) == 'pending' ? 'selected' : '' }}>
                                                    ⏳ في الانتظار
                                                </option>
                                                <option value="rejected" {{ old('status', $rating->status) == 'rejected' ? 'selected' : '' }}>
                                                    ❌ مرفوض
                                                </option>
                                            </select>
                                            @error('status')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Comment Section -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">التعليق والملاحظات</h6>
                            </div>
                            <div class="card-body">
                                <div class="form-group">
                                    <label for="comment">التعليق</label>
                                    <textarea class="form-control @error('comment') is-invalid @enderror" 
                                              id="comment" name="comment" rows="5" 
                                              placeholder="اكتب تعليقك حول المتجر...">{{ old('comment', $rating->comment) }}</textarea>
                                    @error('comment')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">
                                        عدد الأحرف: <span id="comment-count">{{ strlen($rating->comment ?? '') }}</span>
                                    </small>
                                </div>

                                <div class="form-group">
                                    <label for="admin_notes">ملاحظات الإدارة (داخلية)</label>
                                    <textarea class="form-control @error('admin_notes') is-invalid @enderror" 
                                              id="admin_notes" name="admin_notes" rows="3" 
                                              placeholder="ملاحظات للاستخدام الإداري فقط...">{{ old('admin_notes', $rating->admin_notes) }}</textarea>
                                    @error('admin_notes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <small class="form-text text-muted">هذه الملاحظات للاستخدام الإداري ولن تظهر للمستخدمين</small>
                                </div>
                            </div>
                        </div>

                        <!-- Current Rating Info -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-info">معلومات التقييم الحالي</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h4 class="text-primary">{{ $rating->user->name ?? 'غير محدد' }}</h4>
                                            <small class="text-muted">المستخدم</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h4 class="text-success">{{ $rating->shop->name ?? 'غير محدد' }}</h4>
                                            <small class="text-muted">المتجر</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h4 class="text-warning">{{ str_repeat('⭐', $rating->rating) }}</h4>
                                            <small class="text-muted">التقييم الحالي</small>
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="text-center">
                                            <h4 class="
                                                @if($rating->status == 'approved') text-success
                                                @elseif($rating->status == 'pending') text-warning
                                                @else text-danger
                                                @endif
                                            ">
                                                @if($rating->status == 'approved') ✅
                                                @elseif($rating->status == 'pending') ⏳
                                                @else ❌
                                                @endif
                                            </h4>
                                            <small class="text-muted">الحالة</small>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-6">
                                        <small class="text-muted">تاريخ الإنشاء: {{ $rating->created_at->format('Y-m-d H:i') }}</small>
                                    </div>
                                    <div class="col-md-6">
                                        <small class="text-muted">آخر تحديث: {{ $rating->updated_at->format('Y-m-d H:i') }}</small>
                                    </div>
                                </div>

                                @if($rating->comment)
                                <div class="mt-3">
                                    <strong>التعليق الحالي:</strong>
                                    <div class="bg-light p-3 rounded">
                                        "{{ $rating->comment }}"
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>

                        <!-- Moderation Section -->
                        @if($rating->status == 'pending')
                        <div class="card mb-4">
                            <div class="card-header bg-warning">
                                <h6 class="m-0 font-weight-bold text-white">إجراءات المراجعة</h6>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-warning">
                                    <i class="fas fa-exclamation-triangle"></i>
                                    هذا التقييم في انتظار المراجعة. يرجى مراجعة المحتوى واتخاذ الإجراء المناسب.
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-success btn-block" onclick="setStatus('approved')">
                                            <i class="fas fa-check"></i> الموافقة على التقييم
                                        </button>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-warning btn-block" onclick="setStatus('pending')">
                                            <i class="fas fa-clock"></i> إبقاء في الانتظار
                                        </button>
                                    </div>
                                    <div class="col-md-4">
                                        <button type="button" class="btn btn-danger btn-block" onclick="setStatus('rejected')">
                                            <i class="fas fa-times"></i> رفض التقييم
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endif

                        <!-- Action Buttons -->
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> حفظ التغييرات
                            </button>
                            <a href="{{ route('admin.ratings.index') }}" class="btn btn-secondary ml-2">
                                <i class="fas fa-times"></i> إلغاء
                            </a>
                            <a href="{{ route('admin.ratings.show', $rating) }}" class="btn btn-info ml-2">
                                <i class="fas fa-eye"></i> عرض التقييم
                            </a>
                            @if($rating->shop)
                            <a href="{{ route('admin.shops.show', $rating->shop) }}" class="btn btn-outline-primary ml-2">
                                <i class="fas fa-store"></i> عرض المتجر
                            </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Update rating display when rating changes
    $('#rating').on('change', function() {
        var rating = $(this).val();
        var stars = '';
        for (var i = 0; i < rating; i++) {
            stars += '⭐';
        }
        $('#rating-display').text(stars);
    });

    // Character count for comment
    $('#comment').on('input', function() {
        var count = $(this).val().length;
        $('#comment-count').text(count);
    });

    // Set status function for quick moderation
    window.setStatus = function(status) {
        $('#status').val(status);
        
        // Add visual feedback
        $('#status').removeClass('is-valid is-invalid');
        if (status === 'approved') {
            $('#status').addClass('is-valid');
        } else if (status === 'rejected') {
            $('#status').addClass('is-invalid');
        }
    };

    // Auto-save admin notes (optional)
    let adminNotesTimeout;
    $('#admin_notes').on('input', function() {
        clearTimeout(adminNotesTimeout);
        adminNotesTimeout = setTimeout(function() {
            // You can implement auto-save functionality here
            console.log('Admin notes updated');
        }, 2000);
    });

    // Filter shops by user's city (if available)
    $('#user_id').on('change', function() {
        var userId = $(this).val();
        if (userId) {
            // You can implement AJAX to filter shops by user's city
            console.log('User changed to:', userId);
        }
    });

    // Validation feedback
    $('form').on('submit', function(e) {
        var rating = $('#rating').val();
        var userId = $('#user_id').val();
        var shopId = $('#shop_id').val();

        if (!rating || !userId || !shopId) {
            e.preventDefault();
            alert('يرجى ملء جميع الحقول المطلوبة');
            
            if (!userId) $('#user_id').addClass('is-invalid');
            if (!shopId) $('#shop_id').addClass('is-invalid');
            if (!rating) $('#rating').addClass('is-invalid');
        }
    });
});
</script>
@endpush
@endsection