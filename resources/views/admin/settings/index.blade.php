@extends('layouts.admin')

@section('title', 'إعدادات النظام')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-cogs"></i> إعدادات النظام
        </h1>
    </div>

    <div class="row">
        <!-- Application Settings -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">إعدادات التطبيق</h6>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('admin.settings.update') }}">
                        @csrf
                        @method('PUT')
                        
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="app_name">اسم التطبيق</label>
                                    <input type="text" class="form-control @error('app_name') is-invalid @enderror" 
                                           id="app_name" name="app_name" value="{{ old('app_name', $settings['app_name']) }}" required>
                                    @error('app_name')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="app_url">رابط التطبيق</label>
                                    <input type="url" class="form-control @error('app_url') is-invalid @enderror" 
                                           id="app_url" name="app_url" value="{{ old('app_url', $settings['app_url']) }}" required>
                                    @error('app_url')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>وضع الصيانة</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="maintenance_mode" id="maintenance_off" value="0" checked>
                                            <label class="form-check-label" for="maintenance_off">مغلق</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="maintenance_mode" id="maintenance_on" value="1">
                                            <label class="form-check-label" for="maintenance_on">مفعل</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>السماح بالتسجيل</label>
                                    <div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="registration_enabled" id="registration_on" value="1" checked>
                                            <label class="form-check-label" for="registration_on">مفعل</label>
                                        </div>
                                        <div class="form-check form-check-inline">
                                            <input class="form-check-input" type="radio" name="registration_enabled" id="registration_off" value="0">
                                            <label class="form-check-label" for="registration_off">مغلق</label>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> حفظ الإعدادات
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- System Information -->
        <div class="col-lg-4">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">معلومات النظام</h6>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                            <strong>PHP Version:</strong>
                            <span class="badge badge-info">{{ PHP_VERSION }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                            <strong>Laravel Version:</strong>
                            <span class="badge badge-success">{{ app()->version() }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                            <strong>Mail Driver:</strong>
                            <span class="badge badge-secondary">{{ $settings['mail_driver'] }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                            <strong>Cache Driver:</strong>
                            <span class="badge badge-secondary">{{ $settings['cache_driver'] }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                            <strong>Session Driver:</strong>
                            <span class="badge badge-secondary">{{ $settings['session_driver'] }}</span>
                        </div>
                        <div class="list-group-item d-flex justify-content-between align-items-center border-0 px-0">
                            <strong>Queue Driver:</strong>
                            <span class="badge badge-secondary">{{ $settings['queue_driver'] }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cache Management -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">إدارة التخزين المؤقت</h6>
                </div>
                <div class="card-body">
                    <p class="text-muted small">يمكنك مسح التخزين المؤقت لتحسين الأداء</p>
                    
                    <form method="POST" action="{{ route('admin.settings.clear-cache') }}" class="mb-2">
                        @csrf
                        <input type="hidden" name="cache_type" value="all">
                        <button type="submit" class="btn btn-warning btn-block">
                            <i class="fas fa-broom"></i> مسح جميع التخزينات
                        </button>
                    </form>

                    <div class="row">
                        <div class="col-6">
                            <form method="POST" action="{{ route('admin.settings.clear-cache') }}">
                                @csrf
                                <input type="hidden" name="cache_type" value="config">
                                <button type="submit" class="btn btn-outline-secondary btn-sm btn-block">
                                    <i class="fas fa-cog"></i> Config
                                </button>
                            </form>
                        </div>
                        <div class="col-6">
                            <form method="POST" action="{{ route('admin.settings.clear-cache') }}">
                                @csrf
                                <input type="hidden" name="cache_type" value="route">
                                <button type="submit" class="btn btn-outline-secondary btn-sm btn-block">
                                    <i class="fas fa-route"></i> Routes
                                </button>
                            </form>
                        </div>
                    </div>

                    <div class="row mt-2">
                        <div class="col-6">
                            <form method="POST" action="{{ route('admin.settings.clear-cache') }}">
                                @csrf
                                <input type="hidden" name="cache_type" value="view">
                                <button type="submit" class="btn btn-outline-secondary btn-sm btn-block">
                                    <i class="fas fa-eye"></i> Views
                                </button>
                            </form>
                        </div>
                        <div class="col-6">
                            <form method="POST" action="{{ route('admin.settings.clear-cache') }}">
                                @csrf
                                <input type="hidden" name="cache_type" value="application">
                                <button type="submit" class="btn btn-outline-secondary btn-sm btn-block">
                                    <i class="fas fa-database"></i> App Cache
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection