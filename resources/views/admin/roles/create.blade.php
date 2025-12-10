@extends('layouts.admin')

@section('title', 'إضافة دور جديد')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-plus-circle"></i> إضافة دور جديد
        </h1>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> رجوع
        </a>
    </div>

    <!-- Form Card -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">معلومات الدور</h6>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.roles.store') }}">
                @csrf

                <!-- Role Name -->
                <div class="mb-4">
                    <label for="name" class="form-label">
                        اسم الدور <span class="text-danger">*</span>
                    </label>
                    <input type="text" 
                           class="form-control @error('name') is-invalid @enderror" 
                           id="name" 
                           name="name" 
                           value="{{ old('name') }}"
                           placeholder="مثال: content_manager"
                           required>
                    @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                    <small class="form-text text-muted">
                        <i class="fas fa-info-circle"></i> استخدم الأحرف الإنجليزية الصغيرة والشرطة السفلية فقط
                    </small>
                </div>

                <!-- Guard Name (Hidden - Admin panel uses web guard) -->
                <input type="hidden" name="guard_name" value="web">

                <!-- Permissions Section -->
                <div class="mb-4">
                    <label class="form-label d-block mb-3">
                        <h5><i class="fas fa-shield-alt"></i> الصلاحيات</h5>
                    </label>
                    
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i> اختر الصلاحيات التي تريد منحها لهذا الدور
                    </div>

                    <!-- Select All / Deselect All -->
                    <div class="mb-3">
                        <button type="button" class="btn btn-sm btn-primary" id="selectAll">
                            <i class="fas fa-check-square"></i> تحديد الكل
                        </button>
                        <button type="button" class="btn btn-sm btn-secondary" id="deselectAll">
                            <i class="fas fa-square"></i> إلغاء تحديد الكل
                        </button>
                    </div>

                    @foreach($permissions as $module => $modulePermissions)
                    <div class="card mb-3">
                        <div class="card-header bg-light">
                            <div class="form-check">
                                <input class="form-check-input module-checkbox" 
                                       type="checkbox" 
                                       id="module_{{ $module }}"
                                       data-module="{{ $module }}">
                                <label class="form-check-label fw-bold" for="module_{{ $module }}">
                                    <i class="fas fa-folder"></i> {{ ucfirst($module) }}
                                    <span class="badge bg-primary">{{ $modulePermissions->count() }}</span>
                                </label>
                            </div>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                @foreach($modulePermissions as $permission)
                                <div class="col-md-3 mb-2">
                                    <div class="form-check">
                                        <input class="form-check-input permission-checkbox" 
                                               type="checkbox" 
                                               name="permissions[]" 
                                               value="{{ $permission->id }}"
                                               id="permission_{{ $permission->id }}"
                                               data-module="{{ $module }}"
                                               {{ in_array($permission->id, old('permissions', [])) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="permission_{{ $permission->id }}">
                                            {{ $permission->name }}
                                        </label>
                                    </div>
                                </div>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    @endforeach

                    @error('permissions')
                        <div class="text-danger mt-2">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Submit Buttons -->
                <div class="d-flex justify-content-between">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> حفظ الدور
                    </button>
                    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">
                        <i class="fas fa-times"></i> إلغاء
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
$(document).ready(function() {
    // Select All Permissions
    $('#selectAll').click(function() {
        $('.permission-checkbox').prop('checked', true);
        $('.module-checkbox').prop('checked', true);
    });

    // Deselect All Permissions
    $('#deselectAll').click(function() {
        $('.permission-checkbox').prop('checked', false);
        $('.module-checkbox').prop('checked', false);
    });

    // Module Checkbox - Select/Deselect all permissions in module
    $('.module-checkbox').change(function() {
        var module = $(this).data('module');
        var isChecked = $(this).prop('checked');
        $('.permission-checkbox[data-module="' + module + '"]').prop('checked', isChecked);
    });

    // Update module checkbox when individual permissions change
    $('.permission-checkbox').change(function() {
        var module = $(this).data('module');
        var totalInModule = $('.permission-checkbox[data-module="' + module + '"]').length;
        var checkedInModule = $('.permission-checkbox[data-module="' + module + '"]:checked').length;
        
        $('#module_' + module).prop('checked', totalInModule === checkedInModule);
    });

    // Initialize module checkboxes state
    $('.module-checkbox').each(function() {
        var module = $(this).data('module');
        var totalInModule = $('.permission-checkbox[data-module="' + module + '"]').length;
        var checkedInModule = $('.permission-checkbox[data-module="' + module + '"]:checked').length;
        
        $(this).prop('checked', totalInModule === checkedInModule && totalInModule > 0);
    });
});
</script>
@endpush
@endsection
