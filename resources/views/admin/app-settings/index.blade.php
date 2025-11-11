@extends('layouts.admin')

@section('title', 'Mobile App Settings')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">إعدادات التطبيق المحمول</h1>
        <div>
            <a href="{{ route('admin.app-settings.notifications') }}" class="btn btn-primary">
                <i class="fas fa-bell"></i> إدارة الإشعارات
            </a>
            <a href="{{ route('admin.app-settings.devices') }}" class="btn btn-info">
                <i class="fas fa-mobile-alt"></i> الأجهزة المسجلة
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                إجمالي الأجهزة</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_devices'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-mobile-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-success shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                الأجهزة النشطة</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_devices'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-info shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                                iOS / Android</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $stats['ios_devices'] }} / {{ $stats['android_devices'] }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fab fa-apple fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-warning shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                الإشعارات المعلقة</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending_notifications'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bell fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- App Settings Form -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">إعدادات التطبيق</h6>
        </div>
        <div class="card-body">
            <form action="{{ route('admin.app-settings.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <!-- General Settings -->
                <div class="mb-4">
                    <h5 class="text-primary mb-3"><i class="fas fa-cog"></i> الإعدادات العامة</h5>
                    
                    <div class="form-group">
                        <label for="app_name">اسم التطبيق</label>
                        <input type="text" class="form-control" id="app_name" name="app_name" 
                               value="{{ $settings['app_name']->value ?? 'City App' }}" required>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="app_icon">أيقونة التطبيق</label>
                                @if(isset($settings['app_icon_url']) && $settings['app_icon_url']->value)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $settings['app_icon_url']->value) }}" 
                                             alt="App Icon" style="max-width: 100px;">
                                    </div>
                                @endif
                                <input type="file" class="form-control-file" id="app_icon" name="app_icon" accept="image/*">
                                <small class="form-text text-muted">PNG, JPG, JPEG - حجم أقصى 2MB</small>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="app_logo">شعار التطبيق</label>
                                @if(isset($settings['app_logo_url']) && $settings['app_logo_url']->value)
                                    <div class="mb-2">
                                        <img src="{{ asset('storage/' . $settings['app_logo_url']->value) }}" 
                                             alt="App Logo" style="max-width: 200px;">
                                    </div>
                                @endif
                                <input type="file" class="form-control-file" id="app_logo" name="app_logo" accept="image/*">
                                <small class="form-text text-muted">PNG, JPG, JPEG - حجم أقصى 2MB</small>
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- Maintenance Settings -->
                <div class="mb-4">
                    <h5 class="text-primary mb-3"><i class="fas fa-tools"></i> وضع الصيانة</h5>
                    
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="maintenance_mode" 
                                   name="maintenance_mode" value="1"
                                   {{ (isset($settings['maintenance_mode']) && $settings['maintenance_mode']->value === 'true') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="maintenance_mode">
                                تفعيل وضع الصيانة
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="maintenance_message">رسالة الصيانة</label>
                        <textarea class="form-control" id="maintenance_message" name="maintenance_message" rows="3">{{ $settings['maintenance_message']->value ?? '' }}</textarea>
                    </div>
                </div>

                <hr>

                <!-- Version Control -->
                <div class="mb-4">
                    <h5 class="text-primary mb-3"><i class="fas fa-code-branch"></i> التحكم في الإصدار</h5>
                    
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="force_update" 
                                   name="force_update" value="1"
                                   {{ (isset($settings['force_update']) && $settings['force_update']->value === 'true') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="force_update">
                                فرض التحديث
                            </label>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="min_app_version">الحد الأدنى لإصدار التطبيق</label>
                                <input type="text" class="form-control" id="min_app_version" name="min_app_version" 
                                       value="{{ $settings['min_app_version']->value ?? '1.0.0' }}" placeholder="1.0.0">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="latest_app_version">أحدث إصدار متاح</label>
                                <input type="text" class="form-control" id="latest_app_version" name="latest_app_version" 
                                       value="{{ $settings['latest_app_version']->value ?? '1.0.0' }}" placeholder="1.0.0">
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="update_message">رسالة التحديث</label>
                        <textarea class="form-control" id="update_message" name="update_message" rows="2">{{ $settings['update_message']->value ?? '' }}</textarea>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="android_app_url">رابط تطبيق Android</label>
                                <input type="url" class="form-control" id="android_app_url" name="android_app_url" 
                                       value="{{ $settings['android_app_url']->value ?? '' }}" placeholder="https://play.google.com/store/apps/...">
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="ios_app_url">رابط تطبيق iOS</label>
                                <input type="url" class="form-control" id="ios_app_url" name="ios_app_url" 
                                       value="{{ $settings['ios_app_url']->value ?? '' }}" placeholder="https://apps.apple.com/app/...">
                            </div>
                        </div>
                    </div>
                </div>

                <hr>

                <!-- API Settings -->
                <div class="mb-4">
                    <h5 class="text-primary mb-3"><i class="fas fa-plug"></i> إعدادات API</h5>
                    
                    <div class="form-group">
                        <label for="api_status">حالة API</label>
                        <select class="form-control" id="api_status" name="api_status" required>
                            <option value="active" {{ (isset($settings['api_status']) && $settings['api_status']->value === 'active') ? 'selected' : '' }}>نشط</option>
                            <option value="limited" {{ (isset($settings['api_status']) && $settings['api_status']->value === 'limited') ? 'selected' : '' }}>محدود</option>
                            <option value="disabled" {{ (isset($settings['api_status']) && $settings['api_status']->value === 'disabled') ? 'selected' : '' }}>معطل</option>
                        </select>
                    </div>
                </div>

                <hr>

                <!-- Firebase Settings -->
                <div class="mb-4">
                    <h5 class="text-primary mb-3"><i class="fab fa-google"></i> إعدادات Firebase</h5>
                    
                    <div class="form-group">
                        <div class="custom-control custom-switch">
                            <input type="checkbox" class="custom-control-input" id="firebase_enabled" 
                                   name="firebase_enabled" value="1"
                                   {{ (isset($settings['firebase_enabled']) && $settings['firebase_enabled']->value === 'true') ? 'checked' : '' }}>
                            <label class="custom-control-label" for="firebase_enabled">
                                تفعيل إشعارات Firebase
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="firebase_server_key">مفتاح خادم Firebase</label>
                        <input type="text" class="form-control" id="firebase_server_key" name="firebase_server_key" 
                               value="{{ env('FIREBASE_SERVER_KEY', '') }}" placeholder="AAAAxxxxxxx:APA91bH...">
                        <small class="form-text text-muted">يمكن أيضاً تعيينه في ملف .env كـ FIREBASE_SERVER_KEY</small>
                    </div>
                </div>

                <div class="text-right">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> حفظ الإعدادات
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
$(document).ready(function() {
    // Show/hide maintenance message based on switch
    $('#maintenance_mode').change(function() {
        if($(this).is(':checked')) {
            $('#maintenance_message').closest('.form-group').show();
        } else {
            $('#maintenance_message').closest('.form-group').hide();
        }
    });

    // Show/hide Firebase key based on switch
    $('#firebase_enabled').change(function() {
        if($(this).is(':checked')) {
            $('#firebase_server_key').closest('.form-group').show();
        } else {
            $('#firebase_server_key').closest('.form-group').hide();
        }
    });
});
</script>
@endsection
