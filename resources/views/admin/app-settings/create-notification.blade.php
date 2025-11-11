@extends('layouts.admin')

@section('title', 'Create Push Notification')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">إرسال إشعار جديد</h1>
        <a href="{{ route('admin.app-settings.notifications') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> العودة
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">تفاصيل الإشعار</h6>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.app-settings.notifications.store') }}" method="POST" enctype="multipart/form-data">
                        @csrf

                        <div class="form-group">
                            <label for="title">عنوان الإشعار <span class="text-danger">*</span></label>
                            <input type="text" class="form-control @error('title') is-invalid @enderror" 
                                   id="title" name="title" value="{{ old('title') }}" required maxlength="255">
                            @error('title')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="form-group">
                            <label for="body">محتوى الإشعار <span class="text-danger">*</span></label>
                            <textarea class="form-control @error('body') is-invalid @enderror" 
                                      id="body" name="body" rows="4" required maxlength="1000">{{ old('body') }}</textarea>
                            @error('body')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">الحد الأقصى 1000 حرف</small>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="type">نوع الإشعار <span class="text-danger">*</span></label>
                                    <select class="form-control @error('type') is-invalid @enderror" 
                                            id="type" name="type" required>
                                        <option value="general" {{ old('type') === 'general' ? 'selected' : '' }}>عام</option>
                                        <option value="alert" {{ old('type') === 'alert' ? 'selected' : '' }}>تنبيه</option>
                                        <option value="promo" {{ old('type') === 'promo' ? 'selected' : '' }}>عرض ترويجي</option>
                                        <option value="update" {{ old('type') === 'update' ? 'selected' : '' }}>تحديث</option>
                                    </select>
                                    @error('type')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="target">الفئة المستهدفة <span class="text-danger">*</span></label>
                                    <select class="form-control @error('target') is-invalid @enderror" 
                                            id="target" name="target" required>
                                        <option value="all" {{ old('target') === 'all' ? 'selected' : '' }}>جميع المستخدمين</option>
                                        <option value="city" {{ old('target') === 'city' ? 'selected' : '' }}>مدينة محددة</option>
                                        <option value="specific_users" {{ old('target') === 'specific_users' ? 'selected' : '' }}>مستخدمون محددون</option>
                                    </select>
                                    @error('target')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="form-group" id="target_ids_group" style="display: none;">
                            <label for="target_ids">معرّفات الفئة المستهدفة</label>
                            <input type="text" class="form-control" id="target_ids" name="target_ids[]" 
                                   placeholder="أدخل المعرفات مفصولة بفاصلة (مثال: 1,2,3)">
                            <small class="form-text text-muted">
                                أدخل معرفات المدن أو المستخدمين المستهدفين
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="image">صورة الإشعار (اختياري)</label>
                            <input type="file" class="form-control-file @error('image') is-invalid @enderror" 
                                   id="image" name="image" accept="image/*">
                            @error('image')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">JPEG, PNG, JPG - حجم أقصى 2MB</small>
                        </div>

                        <div class="form-group">
                            <label for="action_url">رابط الإجراء (اختياري)</label>
                            <input type="text" class="form-control @error('action_url') is-invalid @enderror" 
                                   id="action_url" name="action_url" value="{{ old('action_url') }}" 
                                   placeholder="https://example.com/page">
                            @error('action_url')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                رابط يتم فتحه عند النقر على الإشعار
                            </small>
                        </div>

                        <div class="form-group">
                            <label for="scheduled_at">جدولة الإرسال (اختياري)</label>
                            <input type="datetime-local" class="form-control @error('scheduled_at') is-invalid @enderror" 
                                   id="scheduled_at" name="scheduled_at" value="{{ old('scheduled_at') }}">
                            @error('scheduled_at')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                            <small class="form-text text-muted">
                                اترك فارغاً للإرسال الفوري أو حدد وقت الإرسال
                            </small>
                        </div>

                        <div class="form-group">
                            <div class="custom-control custom-checkbox">
                                <input type="checkbox" class="custom-control-input" id="send_now" 
                                       name="send_now" value="1" {{ old('send_now') ? 'checked' : '' }}>
                                <label class="custom-control-label" for="send_now">
                                    إرسال فوراً بعد الحفظ
                                </label>
                            </div>
                            <small class="form-text text-muted">
                                إذا لم يتم التحديد، سيتم حفظ الإشعار كـ "قيد الانتظار"
                            </small>
                        </div>

                        <hr>

                        <div class="text-right">
                            <a href="{{ route('admin.app-settings.notifications') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> إلغاء
                            </a>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane"></i> إنشاء الإشعار
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Preview Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">معاينة الإشعار</h6>
                </div>
                <div class="card-body">
                    <div class="notification-preview">
                        <div class="notification-icon mb-2">
                            <i class="fas fa-mobile-alt fa-3x text-primary"></i>
                        </div>
                        <div class="notification-content">
                            <h6 class="font-weight-bold" id="preview-title">عنوان الإشعار</h6>
                            <p class="text-muted mb-0" id="preview-body">محتوى الإشعار سيظهر هنا...</p>
                        </div>
                        <div class="notification-image mt-3" id="preview-image-container" style="display: none;">
                            <img id="preview-image" src="" alt="Notification Image" class="img-fluid rounded">
                        </div>
                    </div>
                </div>
            </div>

            <!-- Help Card -->
            <div class="card shadow mb-4 border-left-info">
                <div class="card-body">
                    <h6 class="font-weight-bold text-info mb-3">
                        <i class="fas fa-info-circle"></i> ملاحظات مهمة
                    </h6>
                    <ul class="small mb-0">
                        <li>تأكد من تفعيل Firebase في الإعدادات</li>
                        <li>يجب أن يكون عنوان الإشعار واضحاً ومباشراً</li>
                        <li>استخدم صوراً مناسبة وذات جودة عالية</li>
                        <li>تحقق من الفئة المستهدفة قبل الإرسال</li>
                        <li>يمكنك جدولة الإشعارات للإرسال في وقت لاحق</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Live preview
    $('#title').on('input', function() {
        $('#preview-title').text($(this).val() || 'عنوان الإشعار');
    });

    $('#body').on('input', function() {
        $('#preview-body').text($(this).val() || 'محتوى الإشعار سيظهر هنا...');
    });

    // Show/hide target IDs based on target selection
    $('#target').change(function() {
        if($(this).val() === 'all') {
            $('#target_ids_group').hide();
        } else {
            $('#target_ids_group').show();
        }
    });

    // Image preview
    $('#image').change(function() {
        const file = this.files[0];
        if(file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#preview-image').attr('src', e.target.result);
                $('#preview-image-container').show();
            };
            reader.readAsDataURL(file);
        } else {
            $('#preview-image-container').hide();
        }
    });

    // Disable send_now when scheduled_at is set
    $('#scheduled_at').change(function() {
        if($(this).val()) {
            $('#send_now').prop('checked', false).prop('disabled', true);
        } else {
            $('#send_now').prop('disabled', false);
        }
    });
});
</script>
@endsection
