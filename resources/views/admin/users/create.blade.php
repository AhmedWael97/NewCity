@extends('layouts.admin')

@section('title', 'إضافة مستخدم جديد')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-user-plus"></i> إضافة مستخدم جديد
        </h1>
        <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-sm">
            <i class="fas fa-arrow-right"></i> العودة للقائمة
        </a>
    </div>

    <div class="row">
        <div class="col-lg-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">معلومات المستخدم</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.users.store') }}" enctype="multipart/form-data">
                        @csrf
                        
                        <!-- Basic Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">المعلومات الأساسية</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="name">الاسم الكامل <span class="text-danger">*</span></label>
                                            <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                                   id="name" name="name" value="{{ old('name') }}" required>
                                            @error('name')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                    
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="email">البريد الإلكتروني <span class="text-danger">*</span></label>
                                            <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                                   id="email" name="email" value="{{ old('email') }}" required>
                                            @error('email')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password">كلمة المرور <span class="text-danger">*</span></label>
                                            <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                                   id="password" name="password" required>
                                            @error('password')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="password_confirmation">تأكيد كلمة المرور <span class="text-danger">*</span></label>
                                            <input type="password" class="form-control" 
                                                   id="password_confirmation" name="password_confirmation" required>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="phone">رقم الهاتف</label>
                                            <input type="tel" class="form-control @error('phone') is-invalid @enderror" 
                                                   id="phone" name="phone" value="{{ old('phone') }}" 
                                                   placeholder="01xxxxxxxxx">
                                            @error('phone')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="birth_date">تاريخ الميلاد</label>
                                            <input type="date" class="form-control @error('birth_date') is-invalid @enderror" 
                                                   id="birth_date" name="birth_date" value="{{ old('birth_date') }}">
                                            @error('birth_date')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Location -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-info">معلومات الموقع</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="city_id">المدينة</label>
                                            <select class="form-control @error('city_id') is-invalid @enderror" 
                                                    id="city_id" name="city_id">
                                                <option value="">-- اختر المدينة --</option>
                                                @foreach(\App\Models\City::where('is_active', true)->orderBy('name')->get() as $city)
                                                    <option value="{{ $city->id }}" {{ old('city_id') == $city->id ? 'selected' : '' }}>
                                                        {{ $city->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            @error('city_id')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-group">
                                            <label for="gender">الجنس</label>
                                            <select class="form-control @error('gender') is-invalid @enderror" 
                                                    id="gender" name="gender">
                                                <option value="">-- اختر الجنس --</option>
                                                <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>ذكر</option>
                                                <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>أنثى</option>
                                            </select>
                                            @error('gender')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="address">العنوان</label>
                                            <textarea class="form-control @error('address') is-invalid @enderror" 
                                                      id="address" name="address" rows="3" 
                                                      placeholder="العنوان التفصيلي...">{{ old('address') }}</textarea>
                                            @error('address')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Profile Information -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-success">معلومات الملف الشخصي</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="profile_image">صورة الملف الشخصي</label>
                                            <input type="file" class="form-control-file @error('profile_image') is-invalid @enderror" 
                                                   id="profile_image" name="profile_image" accept="image/*">
                                            @error('profile_image')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">الحد الأقصى: 2MB. الصيغ المقبولة: JPG, PNG, GIF</small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="bio">نبذة شخصية</label>
                                            <textarea class="form-control @error('bio') is-invalid @enderror" 
                                                      id="bio" name="bio" rows="4" 
                                                      placeholder="اكتب نبذة مختصرة عن المستخدم...">{{ old('bio') }}</textarea>
                                            @error('bio')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- User Type & Status -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-warning">نوع المستخدم والحالة</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="user_type">نوع المستخدم <span class="text-danger">*</span></label>
                                            <select class="form-control @error('user_type') is-invalid @enderror" 
                                                    id="user_type" name="user_type" required>
                                                <option value="">-- اختر نوع المستخدم --</option>
                                                <option value="regular" {{ old('user_type') == 'regular' ? 'selected' : '' }}>مستخدم عادي</option>
                                                <option value="shop_owner" {{ old('user_type') == 'shop_owner' ? 'selected' : '' }}>صاحب متجر</option>
                                                <option value="admin" {{ old('user_type') == 'admin' ? 'selected' : '' }}>مسؤول</option>
                                            </select>
                                            @error('user_type')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">
                                                <i class="fas fa-info-circle"></i> يحدد نوع الحساب والصلاحيات الأساسية للمستخدم
                                            </small>
                                        </div>
                                    </div>

                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label class="d-block">الحالة</label>
                                            <div class="custom-control custom-switch">
                                                <input type="checkbox" class="custom-control-input" 
                                                       id="is_verified" name="is_verified" value="1" 
                                                       {{ old('is_verified') ? 'checked' : '' }}>
                                                <label class="custom-control-label" for="is_verified">
                                                    <i class="fas fa-check-circle text-success"></i> مستخدم موثق
                                                </label>
                                            </div>
                                            <small class="form-text text-muted">
                                                تفعيل هذا الخيار يعني أن البريد الإلكتروني للمستخدم موثق
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Roles & Permissions -->
                        @can('assign-roles')
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-purple">
                                    <i class="fas fa-user-shield"></i> الأدوار والصلاحيات
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>اختر الأدوار</label>
                                            <div class="row">
                                                @foreach($roles as $role)
                                                <div class="col-md-3 mb-2">
                                                    <div class="form-check">
                                                        <input class="form-check-input role-checkbox" 
                                                               type="checkbox" 
                                                               name="roles[]" 
                                                               value="{{ $role->id }}"
                                                               id="role_{{ $role->id }}"
                                                               data-role="{{ $role->name }}"
                                                               {{ in_array($role->id, old('roles', [])) ? 'checked' : '' }}>
                                                        <label class="form-check-label" for="role_{{ $role->id }}">
                                                            {{ $role->name }}
                                                            @if(in_array($role->name, ['super_admin', 'admin']))
                                                                <span class="badge bg-danger">نظام</span>
                                                            @elseif($role->name === 'city_manager')
                                                                <span class="badge bg-info">مدير مدينة</span>
                                                            @endif
                                                        </label>
                                                    </div>
                                                </div>
                                                @endforeach
                                            </div>
                                            @error('roles')
                                                <div class="text-danger mt-2">{{ $message }}</div>
                                            @enderror
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @endcan

                        <!-- City Assignment (for city_manager role) -->
                        @can('assign-cities')
                        <div class="card mb-4" id="cityAssignmentCard" style="display: none;">
                            <div class="card-header bg-info text-white">
                                <h6 class="m-0 font-weight-bold">
                                    <i class="fas fa-city"></i> تخصيص المدن (لمدير المدينة)
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="alert alert-info">
                                    <i class="fas fa-info-circle"></i> 
                                    اختر المدن التي يمكن لهذا المستخدم إدارتها. هذا الخيار متاح فقط لدور "مدير مدينة".
                                </div>
                                <div class="row">
                                    @foreach($cities as $city)
                                    <div class="col-md-3 mb-2">
                                        <div class="form-check">
                                            <input class="form-check-input" 
                                                   type="checkbox" 
                                                   name="assigned_city_ids[]" 
                                                   value="{{ $city->id }}"
                                                   id="city_{{ $city->id }}"
                                                   {{ in_array($city->id, old('assigned_city_ids', [])) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="city_{{ $city->id }}">
                                                {{ $city->name }}
                                            </label>
                                        </div>
                                    </div>
                                    @endforeach
                                </div>
                                @error('assigned_city_ids')
                                    <div class="text-danger mt-2">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        @endcan

                        <!-- Settings & Permissions -->
                        <div class="card mb-4">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-warning">الإعدادات والصلاحيات</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                                   {{ old('is_active', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">
                                                المستخدم نشط
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_verified" name="is_verified" value="1" 
                                                   {{ old('is_verified') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_verified">
                                                مستخدم موثق
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="email_verified" name="email_verified" value="1" 
                                                   {{ old('email_verified') ? 'checked' : '' }}>
                                            <label class="form-check-label" for="email_verified">
                                                البريد موثق
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-3">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="can_receive_notifications" name="can_receive_notifications" value="1" 
                                                   {{ old('can_receive_notifications', true) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="can_receive_notifications">
                                                يمكن إرسال إشعارات
                                            </label>
                                        </div>
                                    </div>
                                </div>

                                <div class="row mt-3">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label for="notes">ملاحظات إدارية</label>
                                            <textarea class="form-control @error('notes') is-invalid @enderror" 
                                                      id="notes" name="notes" rows="3" 
                                                      placeholder="ملاحظات للاستخدام الإداري فقط...">{{ old('notes') }}</textarea>
                                            @error('notes')
                                                <div class="invalid-feedback">{{ $message }}</div>
                                            @enderror
                                            <small class="form-text text-muted">هذه الملاحظات للاستخدام الإداري ولن تظهر للمستخدم</small>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="form-group mt-4">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-user-plus"></i> إنشاء المستخدم
                            </button>
                            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary btn-lg ml-2">
                                <i class="fas fa-times"></i> إلغاء
                            </a>
                            <button type="reset" class="btn btn-outline-warning btn-lg ml-2">
                                <i class="fas fa-undo"></i> إعادة تعيين
                            </button>
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
    // Show/hide city assignment based on city_manager role
    function toggleCityAssignment() {
        var cityManagerChecked = $('.role-checkbox[data-role="city_manager"]').is(':checked');
        if (cityManagerChecked) {
            $('#cityAssignmentCard').slideDown();
        } else {
            $('#cityAssignmentCard').slideUp();
        }
    }
    
    // Check on page load
    toggleCityAssignment();
    
    // Check when any role checkbox changes
    $('.role-checkbox').on('change', function() {
        toggleCityAssignment();
    });
    
    // Password strength indicator
    $('#password').on('input', function() {
        var password = $(this).val();
        var strength = 0;
        
        if (password.length >= 8) strength++;
        if (password.match(/[a-z]/)) strength++;
        if (password.match(/[A-Z]/)) strength++;
        if (password.match(/[0-9]/)) strength++;
        if (password.match(/[^a-zA-Z0-9]/)) strength++;
        
        var strengthText = '';
        var strengthClass = '';
        
        switch(strength) {
            case 0:
            case 1:
                strengthText = 'ضعيف';
                strengthClass = 'text-danger';
                break;
            case 2:
            case 3:
                strengthText = 'متوسط';
                strengthClass = 'text-warning';
                break;
            case 4:
            case 5:
                strengthText = 'قوي';
                strengthClass = 'text-success';
                break;
        }
        
        $('#password').next('.password-strength').remove();
        $('#password').after('<small class="password-strength ' + strengthClass + '">قوة كلمة المرور: ' + strengthText + '</small>');
    });

    // Confirm password matching
    $('#password_confirmation').on('input', function() {
        var password = $('#password').val();
        var confirmPassword = $(this).val();
        
        $(this).next('.password-match').remove();
        
        if (confirmPassword !== '') {
            if (password === confirmPassword) {
                $(this).after('<small class="password-match text-success">كلمات المرور متطابقة ✓</small>');
                $(this).removeClass('is-invalid').addClass('is-valid');
            } else {
                $(this).after('<small class="password-match text-danger">كلمات المرور غير متطابقة ✗</small>');
                $(this).removeClass('is-valid').addClass('is-invalid');
            }
        }
    });

    // Email validation
    $('#email').on('blur', function() {
        var email = $(this).val();
        var emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        
        if (email !== '' && !emailRegex.test(email)) {
            $(this).addClass('is-invalid');
            $(this).next('.email-validation').remove();
            $(this).after('<div class="email-validation invalid-feedback">صيغة البريد الإلكتروني غير صحيحة</div>');
        } else {
            $(this).removeClass('is-invalid');
            $(this).next('.email-validation').remove();
        }
    });

    // Phone number formatting
    $('#phone').on('input', function() {
        var phone = $(this).val().replace(/\D/g, '');
        if (phone.startsWith('20')) {
            phone = phone.substring(2);
        }
        if (phone.length > 11) {
            phone = phone.substring(0, 11);
        }
        $(this).val(phone);
    });

    // User type change handling
    $('#user_type').on('change', function() {
        var userType = $(this).val();
        
        // Show/hide relevant fields based on user type
        if (userType === 'admin') {
            $('#is_verified').prop('checked', true);
            $('#email_verified').prop('checked', true);
        }
    });

    // Form validation before submit
    $('form').on('submit', function(e) {
        var password = $('#password').val();
        var confirmPassword = $('#password_confirmation').val();
        
        if (password !== confirmPassword) {
            e.preventDefault();
            alert('كلمات المرور غير متطابقة');
            $('#password_confirmation').focus();
            return false;
        }
        
        if (password.length < 8) {
            e.preventDefault();
            alert('كلمة المرور يجب أن تكون 8 أحرف على الأقل');
            $('#password').focus();
            return false;
        }
    });
});
</script>
@endpush
@endsection