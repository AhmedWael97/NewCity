@extends('layouts.admin')

@section('title', 'Email Preferences')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">تفضيلات البريد الإلكتروني</h1>
        <a href="{{ route('admin.email-queue.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> العودة
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <div class="row">
        <div class="col-lg-8">
            <!-- Preferences Form -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        <i class="fas fa-bell"></i> إعدادات الإشعارات
                    </h6>
                </div>
                <div class="card-body">
                    <p class="text-muted mb-4">
                        اختر الأحداث التي تريد تلقي إشعارات بريد إلكتروني عنها.
                    </p>

                    <form action="{{ route('admin.email-queue.preferences.update') }}" method="POST">
                        @csrf
                        @method('PUT')

                        <div class="list-group">
                            <div class="list-group-item">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="shop_suggestion" 
                                           name="shop_suggestion"
                                           {{ $preference->shop_suggestion ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="shop_suggestion">
                                        <strong>اقتراحات المتاجر</strong>
                                        <small class="d-block text-muted">إشعار عند استلام اقتراح متجر جديد</small>
                                    </label>
                                </div>
                            </div>

                            <div class="list-group-item">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="city_suggestion" 
                                           name="city_suggestion"
                                           {{ $preference->city_suggestion ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="city_suggestion">
                                        <strong>اقتراحات المدن</strong>
                                        <small class="d-block text-muted">إشعار عند استلام اقتراح مدينة جديدة</small>
                                    </label>
                                </div>
                            </div>

                            <div class="list-group-item">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="shop_rate" 
                                           name="shop_rate"
                                           {{ $preference->shop_rate ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="shop_rate">
                                        <strong>تقييمات المتاجر</strong>
                                        <small class="d-block text-muted">إشعار عند استلام تقييم متجر جديد</small>
                                    </label>
                                </div>
                            </div>

                            <div class="list-group-item">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="service_rate" 
                                           name="service_rate"
                                           {{ $preference->service_rate ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="service_rate">
                                        <strong>تقييمات الخدمات</strong>
                                        <small class="d-block text-muted">إشعار عند استلام تقييم خدمة جديد</small>
                                    </label>
                                </div>
                            </div>

                            <div class="list-group-item">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="new_service" 
                                           name="new_service"
                                           {{ $preference->new_service ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="new_service">
                                        <strong>خدمات جديدة</strong>
                                        <small class="d-block text-muted">إشعار عند إضافة خدمة جديدة</small>
                                    </label>
                                </div>
                            </div>

                            <div class="list-group-item">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="new_marketplace" 
                                           name="new_marketplace"
                                           {{ $preference->new_marketplace ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="new_marketplace">
                                        <strong>منتجات سوق جديدة</strong>
                                        <small class="d-block text-muted">إشعار عند إضافة منتج جديد في السوق</small>
                                    </label>
                                </div>
                            </div>

                            <div class="list-group-item">
                                <div class="custom-control custom-switch">
                                    <input type="checkbox" 
                                           class="custom-control-input" 
                                           id="new_user" 
                                           name="new_user"
                                           {{ $preference->new_user ? 'checked' : '' }}>
                                    <label class="custom-control-label" for="new_user">
                                        <strong>مستخدمين جدد</strong>
                                        <small class="d-block text-muted">إشعار عند تسجيل مستخدم جديد</small>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-save"></i> حفظ التفضيلات
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="selectAll()">
                                <i class="fas fa-check-double"></i> تحديد الكل
                            </button>
                            <button type="button" class="btn btn-secondary" onclick="deselectAll()">
                                <i class="fas fa-times"></i> إلغاء تحديد الكل
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <!-- Info Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3 bg-info text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-info-circle"></i> معلومات
                    </h6>
                </div>
                <div class="card-body">
                    <p class="mb-3">
                        <i class="fas fa-user"></i> <strong>المستخدم:</strong><br>
                        {{ auth()->user()->name }}
                    </p>
                    <p class="mb-3">
                        <i class="fas fa-envelope"></i> <strong>البريد الإلكتروني:</strong><br>
                        {{ auth()->user()->email }}
                    </p>
                    <hr>
                    <p class="small text-muted mb-0">
                        <i class="fas fa-lightbulb"></i> 
                        سيتم إرسال الإشعارات فقط للأحداث التي قمت بتفعيلها. يمكنك تعديل التفضيلات في أي وقت.
                    </p>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="card shadow">
                <div class="card-header py-3 bg-success text-white">
                    <h6 class="m-0 font-weight-bold">
                        <i class="fas fa-chart-bar"></i> إحصائيات سريعة
                    </h6>
                </div>
                <div class="card-body">
                    @php
                        $activePreferences = 0;
                        if($preference->shop_suggestion) $activePreferences++;
                        if($preference->city_suggestion) $activePreferences++;
                        if($preference->shop_rate) $activePreferences++;
                        if($preference->service_rate) $activePreferences++;
                        if($preference->new_service) $activePreferences++;
                        if($preference->new_marketplace) $activePreferences++;
                        if($preference->new_user) $activePreferences++;
                    @endphp
                    <p class="mb-2">
                        <strong>الإشعارات المفعلة:</strong><br>
                        <span class="h3">{{ $activePreferences }}/7</span>
                    </p>
                    <div class="progress mb-3">
                        <div class="progress-bar bg-success" 
                             role="progressbar" 
                             style="width: {{ ($activePreferences/7)*100 }}%" 
                             aria-valuenow="{{ $activePreferences }}" 
                             aria-valuemin="0" 
                             aria-valuemax="7">
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function selectAll() {
    document.querySelectorAll('input[type="checkbox"]').forEach(function(checkbox) {
        checkbox.checked = true;
    });
}

function deselectAll() {
    document.querySelectorAll('input[type="checkbox"]').forEach(function(checkbox) {
        checkbox.checked = false;
    });
}
</script>
@endsection
