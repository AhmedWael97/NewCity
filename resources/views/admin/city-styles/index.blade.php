@extends('layouts.admin')

@section('title', 'إدارة تصاميم المدن')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">إدارة تصاميم المدن</h1>
        <a href="{{ route('admin.advertisements.analytics') }}" class="btn btn-info">
            <i class="fas fa-chart-bar"></i> تقارير الإعلانات
        </a>
    </div>

    <!-- Cities Grid -->
    <div class="row">
        @foreach($cities as $city)
            <div class="col-lg-4 col-md-6 mb-4">
                <div class="card shadow h-100">
                    <!-- City Header Image -->
                    <div class="card-header p-0" style="height: 120px; overflow: hidden;">
                        @if($city->hero_image)
                            <img src="{{ $city->hero_image }}" 
                                 alt="{{ $city->name_ar }}" 
                                 class="w-100 h-100" 
                                 style="object-fit: cover;">
                        @else
                            <div class="w-100 h-100 d-flex align-items-center justify-content-center" 
                                 style="background: linear-gradient(135deg, {{ $city->primary_color ?? '#3b82f6' }}, {{ $city->secondary_color ?? '#64748b' }});">
                                <h3 class="text-white mb-0">{{ $city->name_ar }}</h3>
                            </div>
                        @endif
                    </div>

                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start mb-3">
                            <div>
                                <h5 class="card-title mb-1">{{ $city->name_ar }}</h5>
                                <small class="text-muted">{{ $city->name_en }}</small>
                            </div>
                            
                            @if($city->enable_custom_styling)
                                <span class="badge bg-success text-white">تصميم مخصص</span>
                            @else
                                <span class="badge bg-secondary text-white">تصميم افتراضي</span>
                            @endif
                        </div>

                        <!-- Color Palette -->
                        @if($city->enable_custom_styling && ($city->primary_color || $city->secondary_color || $city->accent_color))
                            <div class="mb-3">
                                <small class="text-muted d-block mb-2">لوحة الألوان:</small>
                                <div class="d-flex gap-2">
                                    @if($city->primary_color)
                                        <div class="color-swatch" 
                                             style="width: 20px; height: 20px; background-color: {{ $city->primary_color }}; border-radius: 50%; border: 1px solid #ddd;"
                                             title="اللون الأساسي: {{ $city->primary_color }}"></div>
                                    @endif
                                    @if($city->secondary_color)
                                        <div class="color-swatch" 
                                             style="width: 20px; height: 20px; background-color: {{ $city->secondary_color }}; border-radius: 50%; border: 1px solid #ddd;"
                                             title="اللون الثانوي: {{ $city->secondary_color }}"></div>
                                    @endif
                                    @if($city->accent_color)
                                        <div class="color-swatch" 
                                             style="width: 20px; height: 20px; background-color: {{ $city->accent_color }}; border-radius: 50%; border: 1px solid #ddd;"
                                             title="لون التمييز: {{ $city->accent_color }}"></div>
                                    @endif
                                </div>
                            </div>
                        @endif

                        <!-- Font Family -->
                        @if($city->font_family && $city->font_family !== 'default')
                            <div class="mb-3">
                                <small class="text-muted">الخط:</small>
                                <span class="badge bg-light text-dark">{{ ucfirst(str_replace('_', ' ', $city->font_family)) }}</span>
                            </div>
                        @endif

                        <!-- Stats -->
                        <div class="row text-center small mb-3">
                            <div class="col-4">
                                <div class="font-weight-bold text-primary">{{ $city->shops_count ?? 0 }}</div>
                                <div class="text-muted">المتاجر</div>
                            </div>
                            <div class="col-4">
                                <div class="font-weight-bold text-success">{{ $city->categories_count ?? 0 }}</div>
                                <div class="text-muted">الفئات</div>
                            </div>
                            <div class="col-4">
                                <div class="font-weight-bold text-info">{{ $city->users_count ?? 0 }}</div>
                                <div class="text-muted">المستخدمين</div>
                            </div>
                        </div>
                    </div>

                    <div class="card-footer bg-white">
                        <div class="btn-group btn-group-sm w-100 mb-2" role="group">
                            <a href="{{ route('admin.city-styles.edit', $city) }}" 
                               class="btn btn-outline-primary flex-fill">
                                <i class="fas fa-palette"></i> تصميم المدينة
                            </a>
                            <a href="{{ route('admin.city-styles.landing-page', $city) }}" 
                               class="btn btn-outline-success flex-fill">
                                <i class="fas fa-mobile-alt"></i> الصفحة الرئيسية
                            </a>
                            <a href="{{ route('city.landing', $city->slug) }}" 
                               class="btn btn-outline-info flex-fill" 
                               target="_blank">
                                <i class="fas fa-external-link-alt"></i> معاينة
                            </a>
                        </div>
                        <div class="btn-group btn-group-sm w-100" role="group">
                            <a href="{{ route('admin.city-banners.index', ['city_id' => $city->id]) }}" 
                               class="btn btn-outline-warning flex-fill">
                                <i class="fas fa-image"></i> الإعلانات
                            </a>
                            @if($city->enable_custom_styling)
                                <form action="{{ route('admin.city-styles.reset', $city) }}" 
                                      method="POST" class="flex-fill"
                                      onsubmit="return confirm('هل أنت متأكد من إعادة تعيين التصميم للوضع الافتراضي؟')">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-outline-warning w-100">
                                        <i class="fas fa-undo"></i> إعادة تعيين
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Pagination -->
    <div class="d-flex justify-content-center">
        {{ $cities->links() }}
    </div>

    <!-- Quick Styling Tips -->
    <div class="row mt-5">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">نصائح سريعة لتخصيص التصاميم</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">الألوان</h6>
                            <ul class="mb-3">
                                <li><strong>اللون الأساسي:</strong> يُستخدم للأزرار والروابط المهمة</li>
                                <li><strong>اللون الثانوي:</strong> للنصوص الثانوية والخلفيات</li>
                                <li><strong>لون التمييز:</strong> للعناصر التي تتطلب انتباه خاص</li>
                            </ul>
                            
                            <h6 class="font-weight-bold">الخلفيات</h6>
                            <ul class="mb-3">
                                <li><strong>لون واحد:</strong> خلفية بلون موحد</li>
                                <li><strong>تدرج:</strong> انتقال لوني جميل</li>
                                <li><strong>صورة:</strong> صورة خلفية مخصصة</li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="font-weight-bold">الخطوط</h6>
                            <ul class="mb-3">
                                <li><strong>Cairo:</strong> خط عربي حديث ومقروء</li>
                                <li><strong>Tajawal:</strong> خط عربي أنيق</li>
                                <li><strong>Amiri:</strong> خط عربي تقليدي</li>
                                <li><strong>Noto Sans Arabic:</strong> خط عربي من Google</li>
                            </ul>
                            
                            <h6 class="font-weight-bold">أفضل الممارسات</h6>
                            <ul class="mb-3">
                                <li>استخدم ألوان متناسقة مع هوية المدينة</li>
                                <li>تأكد من وضوح النصوص على الخلفيات</li>
                                <li>اختبر التصميم على أجهزة مختلفة</li>
                                <li>استخدم صور عالية الجودة</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.color-swatch {
    transition: transform 0.2s ease;
}

.color-swatch:hover {
    transform: scale(1.2);
}

.btn-group .btn {
    border-radius: 0;
}

.btn-group .btn:first-child {
    border-top-left-radius: 0.25rem;
    border-bottom-left-radius: 0.25rem;
}

.btn-group .btn:last-child {
    border-top-right-radius: 0.25rem;
    border-bottom-right-radius: 0.25rem;
}

.card {
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.card:hover {
    transform: translateY(-2px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15) !important;
}
</style>
@endsection