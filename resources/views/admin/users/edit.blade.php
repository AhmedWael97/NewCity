@extends('layouts.admin')

@section('title', 'تعديل المستخدم')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-edit"></i> تعديل المستخدم: {{ $user->name }}
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
                    <form method="POST" action="{{ route('admin.users.update', $user) }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="name">الاسم <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control @error('name') is-invalid @enderror" 
                                           id="name" name="name" value="{{ old('name', $user->name) }}" required>
                                    @error('name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="email">البريد الإلكتروني <span class="text-danger">*</span></label>
                                    <input type="email" class="form-control @error('email') is-invalid @enderror" 
                                           id="email" name="email" value="{{ old('email', $user->email) }}" required>
                                    @error('email')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password">كلمة المرور الجديدة</label>
                                    <input type="password" class="form-control @error('password') is-invalid @enderror" 
                                           id="password" name="password" placeholder="اتركه فارغاً إذا لم ترد التغيير">
                                    @error('password')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="password_confirmation">تأكيد كلمة المرور</label>
                                    <input type="password" class="form-control" 
                                           id="password_confirmation" name="password_confirmation" 
                                           placeholder="تأكيد كلمة المرور الجديدة">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="city_id">المدينة</label>
                                    <select class="form-control @error('city_id') is-invalid @enderror" id="city_id" name="city_id">
                                        <option value="">اختر المدينة</option>
                                        @foreach($cities as $city)
                                            <option value="{{ $city->id }}" {{ old('city_id', $user->city_id) == $city->id ? 'selected' : '' }}>
                                                {{ $city->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('city_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="phone">رقم الهاتف</label>
                                    <input type="text" class="form-control @error('phone') is-invalid @enderror" 
                                           id="phone" name="phone" value="{{ old('phone', $user->phone) }}">
                                    @error('phone')
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
                                              id="address" name="address" rows="3">{{ old('address', $user->address) }}</textarea>
                                    @error('address')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="date_of_birth">تاريخ الميلاد</label>
                                    <input type="date" class="form-control @error('date_of_birth') is-invalid @enderror" 
                                           id="date_of_birth" name="date_of_birth" value="{{ old('date_of_birth', $user->date_of_birth) }}">
                                    @error('date_of_birth')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="avatar">الصورة الشخصية</label>
                                    <input type="file" class="form-control-file @error('avatar') is-invalid @enderror" 
                                           id="avatar" name="avatar" accept="image/*">
                                    @error('avatar')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    @if($user->avatar)
                                        <small class="form-text text-muted">
                                            <img src="{{ $user->avatar }}" alt="Current Avatar" class="img-thumbnail mt-2" style="max-width: 100px;">
                                        </small>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <!-- Roles & Permissions -->
                        @can('assign-roles')
                        <div class="card mt-4">
                            <div class="card-header bg-purple text-white">
                                <h6 class="m-0 font-weight-bold">
                                    <i class="fas fa-user-shield"></i> الأدوار والصلاحيات
                                </h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="form-group">
                                            <label>الأدوار المخصصة</label>
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
                                                               {{ in_array($role->id, old('roles', $userRoles)) ? 'checked' : '' }}>
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
                        <div class="card mt-4" id="cityAssignmentCard" style="display: {{ in_array('city_manager', $user->roles->pluck('name')->toArray()) ? 'block' : 'none' }};">
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
                                                   {{ in_array($city->id, old('assigned_city_ids', $user->assigned_city_ids ?? [])) ? 'checked' : '' }}>
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

                        <!-- Status Section -->
                        <div class="card mt-4">
                            <div class="card-header">
                                <h6 class="m-0 font-weight-bold text-primary">حالة المستخدم</h6>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" value="1" 
                                                   {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_active">
                                                المستخدم نشط
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="is_verified" name="is_verified" value="1" 
                                                   {{ old('is_verified', $user->is_verified) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="is_verified">
                                                المستخدم محقق
                                            </label>
                                        </div>
                                    </div>

                                    <div class="col-md-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" id="email_verified" name="email_verified" value="1" 
                                                   {{ old('email_verified', $user->email_verified_at ? true : false) ? 'checked' : '' }}>
                                            <label class="form-check-label" for="email_verified">
                                                البريد الإلكتروني محقق
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Action Buttons -->
                        <div class="form-group mt-4">
                            <div class="d-flex justify-content-between">
                                <div>
                                    <button type="submit" class="btn btn-primary">
                                        <i class="fas fa-save"></i> حفظ التغييرات
                                    </button>
                                    <a href="{{ route('admin.users.index') }}" class="btn btn-secondary ml-2">
                                        <i class="fas fa-times"></i> إلغاء
                                    </a>
                                </div>
                                <div>
                                    <small class="text-muted">
                                        آخر تحديث: {{ $user->updated_at->diffForHumans() }}
                                    </small>
                                </div>
                            </div>
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
    
    // Check when any role checkbox changes
    $('.role-checkbox').on('change', function() {
        toggleCityAssignment();
    });
    
    // Auto-generate slug from name
    $('#name').on('input', function() {
        var name = $(this).val();
        var slug = name.toLowerCase()
                      .replace(/[^\w\s-]/g, '') // Remove special characters
                      .replace(/\s+/g, '-');    // Replace spaces with hyphens
        $('#slug').val(slug);
    });
});
</script>
@endpush
@endsection