@extends('layouts.admin')

@section('title', 'عرض الخدمة - ' . $userService->title)

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">{{ $userService->title }}</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb mb-0">
                    <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">لوحة التحكم</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('admin.user-services.index') }}">الخدمات</a></li>
                    <li class="breadcrumb-item active">عرض الخدمة</li>
                </ol>
            </nav>
        </div>
        <div>
            <a href="{{ route('admin.user-services.edit', $userService) }}" class="btn btn-primary">
                <i class="fas fa-edit"></i> تعديل
            </a>
            <a href="{{ route('admin.user-services.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i> رجوع
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Main Details -->
        <div class="col-lg-8">
            <!-- Service Info Card -->
            <div class="card mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-info-circle"></i> معلومات الخدمة</h5>
                </div>
                <div class="card-body">
                    <!-- Status Badges -->
                    <div class="mb-3">
                        @if($userService->is_verified)
                            <span class="badge bg-success"><i class="fas fa-check-circle"></i> موثقة</span>
                        @else
                            <span class="badge bg-warning"><i class="fas fa-clock"></i> غير موثقة</span>
                        @endif
                        
                        @if($userService->is_featured)
                            <span class="badge bg-info"><i class="fas fa-star"></i> مميزة</span>
                        @endif
                        
                        @if($userService->is_active)
                            <span class="badge bg-success"><i class="fas fa-check"></i> نشطة</span>
                        @else
                            <span class="badge bg-danger"><i class="fas fa-times"></i> غير نشطة</span>
                        @endif
                    </div>

                    <!-- Basic Info -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>العنوان:</strong>
                            <p>{{ $userService->title }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>الرابط:</strong>
                            <p><code>{{ $userService->slug }}</code></p>
                        </div>
                    </div>

                    <div class="mb-3">
                        <strong>الوصف:</strong>
                        <p>{{ $userService->description }}</p>
                    </div>

                    <!-- Category & City -->
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>الفئة:</strong>
                            <p>
                                @if($userService->serviceCategory)
                                    <span class="badge bg-primary">
                                        <i class="{{ $userService->serviceCategory->icon }}"></i> 
                                        {{ $userService->serviceCategory->name_ar }}
                                    </span>
                                @else
                                    <span class="text-muted">غير محدد</span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <strong>المدينة:</strong>
                            <p>
                                @if($userService->city)
                                    <span class="badge bg-info">
                                        <i class="fas fa-map-marker-alt"></i> {{ $userService->city->name }}
                                    </span>
                                @else
                                    <span class="text-muted">غير محدد</span>
                                @endif
                            </p>
                        </div>
                    </div>

                    <!-- Pricing -->
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-dollar-sign"></i> التسعير</h6>
                            <div class="row">
                                <div class="col-md-4">
                                    <strong>نوع التسعير:</strong>
                                    <p>
                                        @switch($userService->pricing_type)
                                            @case('fixed')
                                                <span class="badge bg-success">سعر ثابت</span>
                                                @break
                                            @case('hourly')
                                                <span class="badge bg-info">بالساعة</span>
                                                @break
                                            @case('per_km')
                                                <span class="badge bg-warning">بالكيلومتر</span>
                                                @break
                                            @case('negotiable')
                                                <span class="badge bg-secondary">قابل للتفاوض</span>
                                                @break
                                        @endswitch
                                    </p>
                                </div>
                                @if($userService->price_from)
                                    <div class="col-md-4">
                                        <strong>السعر من:</strong>
                                        <p>{{ number_format($userService->price_from, 2) }} {{ $userService->currency }}</p>
                                    </div>
                                @endif
                                @if($userService->price_to)
                                    <div class="col-md-4">
                                        <strong>السعر إلى:</strong>
                                        <p>{{ number_format($userService->price_to, 2) }} {{ $userService->currency }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Contact Info -->
                    <div class="card bg-light mb-3">
                        <div class="card-body">
                            <h6 class="card-title"><i class="fas fa-phone"></i> معلومات الاتصال</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <strong>رقم الهاتف:</strong>
                                    <p><a href="tel:{{ $userService->phone }}">{{ $userService->phone }}</a></p>
                                </div>
                                @if($userService->whatsapp)
                                    <div class="col-md-6">
                                        <strong>واتساب:</strong>
                                        <p><a href="https://wa.me/{{ $userService->whatsapp }}">{{ $userService->whatsapp }}</a></p>
                                    </div>
                                @endif
                                @if($userService->location)
                                    <div class="col-md-12">
                                        <strong>الموقع:</strong>
                                        <p>{{ $userService->location }}</p>
                                    </div>
                                @endif
                                @if($userService->address)
                                    <div class="col-md-12">
                                        <strong>العنوان:</strong>
                                        <p>{{ $userService->address }}</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Images -->
                    @if($userService->images && count($userService->images) > 0)
                        <div class="mb-3">
                            <strong>الصور:</strong>
                            <div class="row g-2 mt-2">
                                @foreach($userService->images as $image)
                                    <div class="col-md-3">
                                        <img src="{{ asset('storage/' . $image) }}" alt="Service Image" class="img-fluid rounded">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Service Areas -->
                    @if($userService->service_areas && count($userService->service_areas) > 0)
                        <div class="mb-3">
                            <strong>مناطق الخدمة:</strong>
                            <div class="mt-2">
                                @foreach($userService->service_areas as $area)
                                    <span class="badge bg-secondary me-1">{{ $area }}</span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Requirements -->
                    @if($userService->requirements)
                        <div class="mb-3">
                            <strong>المتطلبات:</strong>
                            <p>{{ $userService->requirements }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Provider Info -->
            @if($userService->user)
                <div class="card mb-4">
                    <div class="card-header bg-info text-white">
                        <h5 class="mb-0"><i class="fas fa-user"></i> مقدم الخدمة</h5>
                    </div>
                    <div class="card-body">
                        <div class="text-center mb-3">
                            @if($userService->user->avatar)
                                <img src="{{ asset('storage/' . $userService->user->avatar) }}" alt="{{ $userService->user->name }}" class="rounded-circle" style="width: 80px; height: 80px; object-fit: cover;">
                            @else
                                <div class="rounded-circle bg-primary text-white d-inline-flex align-items-center justify-content-center" style="width: 80px; height: 80px;">
                                    <i class="fas fa-user fa-2x"></i>
                                </div>
                            @endif
                        </div>
                        <h6 class="text-center">{{ $userService->user->name }}</h6>
                        <p class="text-center text-muted mb-0">{{ $userService->user->email }}</p>
                        @if($userService->user->phone)
                            <p class="text-center text-muted">{{ $userService->user->phone }}</p>
                        @endif
                    </div>
                </div>
            @endif

            <!-- Statistics -->
            <div class="card mb-4">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-chart-line"></i> الإحصائيات</h5>
                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-star text-warning"></i> التقييم:</span>
                        <strong>{{ number_format($userService->rating, 1) }} / 5</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-comments text-primary"></i> المراجعات:</span>
                        <strong>{{ $userService->total_reviews }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span><i class="fas fa-eye text-info"></i> المشاهدات:</span>
                        <strong>{{ number_format($userService->total_views) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between">
                        <span><i class="fas fa-phone-alt text-success"></i> الاتصالات:</span>
                        <strong>{{ number_format($userService->total_contacts) }}</strong>
                    </div>
                </div>
            </div>

            <!-- Dates -->
            <div class="card mb-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="mb-0"><i class="fas fa-calendar"></i> التواريخ</h5>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong>تاريخ الإنشاء:</strong>
                        <p class="mb-0">{{ $userService->created_at->format('Y-m-d H:i') }}</p>
                    </div>
                    <div>
                        <strong>آخر تحديث:</strong>
                        <p class="mb-0">{{ $userService->updated_at->format('Y-m-d H:i') }}</p>
                    </div>
                    @if($userService->is_featured && $userService->featured_until)
                        <div class="mt-2">
                            <strong>مميزة حتى:</strong>
                            <p class="mb-0">{{ $userService->featured_until->format('Y-m-d H:i') }}</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-cog"></i> إجراءات</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('admin.user-services.verify', $userService) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-{{ $userService->is_verified ? 'warning' : 'success' }} btn-sm w-100">
                            <i class="fas fa-{{ $userService->is_verified ? 'times' : 'check' }}-circle"></i> 
                            {{ $userService->is_verified ? 'إلغاء التوثيق' : 'توثيق الخدمة' }}
                        </button>
                    </form>
                    <form action="{{ route('admin.user-services.feature', $userService) }}" method="POST" class="mb-2">
                        @csrf
                        <button type="submit" class="btn btn-{{ $userService->is_featured ? 'secondary' : 'info' }} btn-sm w-100">
                            <i class="fas fa-star"></i> 
                            {{ $userService->is_featured ? 'إلغاء التمييز' : 'تمييز الخدمة' }}
                        </button>
                    </form>
                    <form action="{{ route('admin.user-services.destroy', $userService) }}" method="POST" onsubmit="return confirm('هل أنت متأكد من حذف هذه الخدمة؟')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-sm w-100">
                            <i class="fas fa-trash"></i> حذف الخدمة
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
