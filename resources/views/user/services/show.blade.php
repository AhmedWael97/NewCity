@extends('layouts.app')

@section('title', $service->title)

@section('content')
<div class="container py-5" dir="rtl">
    <div class="row">
        <div class="col-lg-8">
            <!-- Service Header -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex justify-content-between align-items-start mb-3">
                        <div>
                            <h2 class="fw-bold mb-2">{{ $service->title }}</h2>
                            <div class="d-flex gap-2 mb-3">
                                <span class="badge bg-primary">{{ $service->serviceCategory->name_ar }}</span>
                                <span class="badge bg-info text-dark">{{ $service->city->name }}</span>
                                @if($service->is_active)
                                    <span class="badge bg-success">نشط</span>
                                @else
                                    <span class="badge bg-secondary">غير نشط</span>
                                @endif
                                @if($service->is_verified)
                                    <span class="badge bg-primary"><i class="bi bi-check-circle-fill"></i> موثق</span>
                                @endif
                            </div>
                        </div>
                        @if($isOwner)
                        <div class="dropdown">
                            <button class="btn btn-light" type="button" data-bs-toggle="dropdown">
                                <i class="bi bi-three-dots-vertical"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item" href="{{ route('user.services.edit', $service) }}">
                                        <i class="bi bi-pencil"></i> تعديل
                                    </a>
                                </li>
                                <li>
                                    <a class="dropdown-item" href="{{ route('user.services.analytics', $service) }}">
                                        <i class="bi bi-graph-up"></i> الإحصائيات
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('user.services.toggle-status', $service) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="dropdown-item">
                                            <i class="bi bi-toggle-{{ $service->is_active ? 'off' : 'on' }}"></i>
                                            {{ $service->is_active ? 'إيقاف' : 'تفعيل' }}
                                        </button>
                                    </form>
                                </li>
                                <li>
                                    <form action="{{ route('user.services.destroy', $service) }}" method="POST" 
                                          onsubmit="return confirm('هل أنت متأكد من حذف هذه الخدمة؟')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-trash"></i> حذف
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                        @endif
                    </div>

                    <!-- Images -->
                    @if($service->images && count($service->images) > 0)
                        <div id="serviceCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
                            <div class="carousel-inner rounded">
                                @foreach($service->images as $index => $image)
                                    <div class="carousel-item {{ $index === 0 ? 'active' : '' }}">
                                        <img src="{{ $image }}" 
                                             class="d-block w-100" 
                                             alt="Service Image"
                                             style="height: 400px; object-fit: cover;">
                                    </div>
                                @endforeach
                            </div>
                            @if(count($service->images) > 1)
                                <button class="carousel-control-prev" type="button" data-bs-target="#serviceCarousel" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon"></span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#serviceCarousel" data-bs-slide="next">
                                    <span class="carousel-control-next-icon"></span>
                                </button>
                            @endif
                        </div>
                    @endif

                    <!-- Description -->
                    <h5 class="fw-bold mb-3">وصف الخدمة</h5>
                    <p class="text-muted mb-4">{{ $service->description }}</p>

                    <!-- Pricing -->
                    <h5 class="fw-bold mb-3">التسعير</h5>
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="border rounded p-3">
                                <small class="text-muted d-block mb-1">نوع التسعير</small>
                                <strong>
                                    @if($service->pricing_type === 'fixed') سعر ثابت
                                    @elseif($service->pricing_type === 'hourly') بالساعة
                                    @elseif($service->pricing_type === 'per_km') بالكيلومتر
                                    @else تفاوض
                                    @endif
                                </strong>
                            </div>
                        </div>
                        @if($service->price_from || $service->price_to)
                            <div class="col-md-6">
                                <div class="border rounded p-3">
                                    <small class="text-muted d-block mb-1">نطاق السعر</small>
                                    <strong>
                                        @if($service->price_from && $service->price_to)
                                            {{ $service->price_from }} - {{ $service->price_to }} جنيه مصري
                                        @elseif($service->price_from)
                                            من {{ $service->price_from }} جنيه مصري
                                        @else
                                            حتى {{ $service->price_to }} جنيه مصري
                                        @endif
                                    </strong>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Contact Info -->
                    <h5 class="fw-bold mb-3">معلومات التواصل</h5>
                    <div class="row g-3 mb-4">
                        @if($service->phone)
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-telephone-fill text-primary fs-4 me-3"></i>
                                    <div>
                                        <small class="text-muted d-block">الهاتف</small>
                                        <strong>{{ $service->phone }}</strong>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if($service->whatsapp)
                            <div class="col-md-6">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-whatsapp text-success fs-4 me-3"></i>
                                    <div>
                                        <small class="text-muted d-block">واتساب</small>
                                        <strong>{{ $service->whatsapp }}</strong>
                                    </div>
                                </div>
                            </div>
                        @endif
                        @if($service->address)
                            <div class="col-12">
                                <div class="d-flex align-items-center">
                                    <i class="bi bi-geo-alt-fill text-danger fs-4 me-3"></i>
                                    <div>
                                        <small class="text-muted d-block">العنوان</small>
                                        <strong>{{ $service->address }}</strong>
                                    </div>
                                </div>
                            </div>
                        @endif
                    </div>

                    <!-- Additional Info -->
                    @if($service->requirements)
                        <h5 class="fw-bold mb-3">متطلبات أو ملاحظات</h5>
                        <p class="text-muted">{{ $service->requirements }}</p>
                    @endif
                </div>
            </div>
        </div>

        <!-- Sidebar -->
        <div class="col-lg-4">
            @if($isOwner)
            <!-- Quick Stats -->
            <div class="card border-0 shadow-sm mb-4">
                <div class="card-body">
                    <h5 class="fw-bold mb-4">إحصائيات سريعة</h5>
                    
                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-eye-fill text-primary fs-4 me-2"></i>
                            <span>المشاهدات الكلية</span>
                        </div>
                        <strong class="fs-5">{{ $analytics['total_views'] ?? 0 }}</strong>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-telephone-fill text-success fs-4 me-2"></i>
                            <span>التواصلات الكلية</span>
                        </div>
                        <strong class="fs-5">{{ $analytics['total_contacts'] ?? 0 }}</strong>
                    </div>

                    <div class="d-flex justify-content-between align-items-center mb-3 pb-3 border-bottom">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-eye text-info fs-4 me-2"></i>
                            <span>مشاهدات هذا الشهر</span>
                        </div>
                        <strong class="fs-5">{{ $analytics['this_month_views'] ?? 0 }}</strong>
                    </div>

                    <div class="d-flex justify-content-between align-items-center">
                        <div class="d-flex align-items-center">
                            <i class="bi bi-telephone text-warning fs-4 me-2"></i>
                            <span>تواصلات هذا الشهر</span>
                        </div>
                        <strong class="fs-5">{{ $analytics['this_month_contacts'] ?? 0 }}</strong>
                    </div>

                    <a href="{{ route('user.services.analytics', $service) }}" class="btn btn-outline-primary w-100 mt-4">
                        <i class="bi bi-graph-up me-2"></i> عرض التفاصيل الكاملة
                    </a>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold mb-3">إجراءات سريعة</h5>
                    
                    <a href="{{ route('user.services.edit', $service) }}" class="btn btn-primary w-100 mb-2">
                        <i class="bi bi-pencil me-2"></i> تعديل الخدمة
                    </a>
                    
                    <form action="{{ route('user.services.toggle-status', $service) }}" method="POST" class="mb-2">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-outline-{{ $service->is_active ? 'warning' : 'success' }} w-100">
                            <i class="bi bi-toggle-{{ $service->is_active ? 'off' : 'on' }} me-2"></i>
                            {{ $service->is_active ? 'إيقاف الخدمة' : 'تفعيل الخدمة' }}
                        </button>
                    </form>
                    
                    <a href="{{ route('user.services.index') }}" class="btn btn-outline-secondary w-100">
                        <i class="bi bi-arrow-right me-2"></i> العودة لقائمة الخدمات
                    </a>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>
@endsection
