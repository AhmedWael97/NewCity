@extends('layouts.admin')

@section('title', 'تفاصيل المتجر - ' . $shop->name)

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-store"></i> تفاصيل المتجر: {{ $shop->name }}
        </h1>
        <div>
            <a href="{{ route('admin.shops.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-right"></i> العودة للقائمة
            </a>
            <a href="{{ route('admin.shops.edit', $shop) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit"></i> تعديل
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Shop Details -->
        <div class="col-xl-8 col-lg-7">
            <!-- Basic Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">المعلومات الأساسية</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <strong>اسم المتجر:</strong>
                            <p>{{ $shop->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>الرابط:</strong>
                            <p>{{ $shop->slug ?: 'غير محدد' }}</p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <strong>الوصف:</strong>
                            <p>{{ $shop->description ?: 'لا يوجد وصف' }}</p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <strong>العنوان:</strong>
                            <p>{{ $shop->address ?: 'غير محدد' }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>خط الطول:</strong>
                            <p>{{ $shop->longitude ?: 'غير محدد' }}</p>
                        </div>
                        <div class="col-md-3">
                            <strong>خط العرض:</strong>
                            <p>{{ $shop->latitude ?: 'غير محدد' }}</p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <strong>الهاتف:</strong>
                            <p>{{ $shop->phone ?: 'غير محدد' }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>البريد الإلكتروني:</strong>
                            <p>{{ $shop->email ?: 'غير محدد' }}</p>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-12">
                            <strong>الموقع الإلكتروني:</strong>
                            <p>
                                @if($shop->website)
                                    <a href="{{ $shop->website }}" target="_blank">{{ $shop->website }}</a>
                                @else
                                    غير محدد
                                @endif
                            </p>
                        </div>
                    </div>

                    @if($shop->opening_hours)
                        <div class="row">
                            <div class="col-md-12">
                                <strong>ساعات العمل:</strong>
                                <div class="mt-2">
                                    @foreach($shop->opening_hours as $day => $hours)
                                        <div class="row mb-1">
                                            <div class="col-3">
                                                <strong>{{ $day }}:</strong>
                                            </div>
                                            <div class="col-9">
                                                {{ $hours['open'] ?? 'مغلق' }} - {{ $hours['close'] ?? '' }}
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Images -->
            @if($shop->images && count($shop->images) > 0)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">صور المتجر</h6>
                    </div>
                    <div class="card-body">
                        <div class="row">
                            @foreach($shop->images as $image)
                                <div class="col-md-4 mb-3">
                                    <img src="{{ Storage::url($image) }}" 
                                         alt="{{ $shop->name }}" 
                                         class="img-fluid rounded shadow">
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            @endif

            <!-- Ratings -->
            @if($shop->ratings && $shop->ratings->count() > 0)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">التقييمات ({{ $shop->ratings->count() }})</h6>
                    </div>
                    <div class="card-body">
                        @foreach($shop->ratings->take(5) as $rating)
                            <div class="media mb-3">
                                <div class="media-body">
                                    <div class="d-flex justify-content-between">
                                        <h6 class="mt-0">{{ $rating->user->name }}</h6>
                                        <div>
                                            @for($i = 1; $i <= 5; $i++)
                                                <i class="fas fa-star {{ $i <= $rating->rating ? 'text-warning' : 'text-muted' }}"></i>
                                            @endfor
                                        </div>
                                    </div>
                                    @if($rating->comment)
                                        <p class="mb-1">{{ $rating->comment }}</p>
                                    @endif
                                    <small class="text-muted">{{ $rating->created_at->diffForHumans() }}</small>
                                </div>
                            </div>
                            @if(!$loop->last)
                                <hr>
                            @endif
                        @endforeach
                        
                        @if($shop->ratings->count() > 5)
                            <div class="text-center mt-3">
                                <small class="text-muted">وعرض {{ $shop->ratings->count() - 5 }} تقييمات أخرى...</small>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="col-xl-4 col-lg-5">
            <!-- Status and Actions -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">الحالة والإجراءات</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <strong>الحالة:</strong>
                        @switch($shop->status)
                            @case('pending')
                                <span class="badge badge-warning">في الانتظار</span>
                                @break
                            @case('approved')
                                <span class="badge badge-success">مقبول</span>
                                @break
                            @case('rejected')
                                <span class="badge badge-danger">مرفوض</span>
                                @break
                            @case('suspended')
                                <span class="badge badge-secondary">معلق</span>
                                @break
                            @default
                                <span class="badge badge-light">{{ $shop->status }}</span>
                        @endswitch
                    </div>

                    <div class="mb-3">
                        <strong>التحقق:</strong>
                        @if($shop->is_verified)
                            <span class="badge badge-success">
                                <i class="fas fa-check"></i> محقق
                            </span>
                        @else
                            <span class="badge badge-secondary">
                                <i class="fas fa-times"></i> غير محقق
                            </span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <strong>مميز:</strong>
                        @if($shop->is_featured)
                            <span class="badge badge-primary">
                                <i class="fas fa-star"></i> مميز
                            </span>
                        @else
                            <span class="badge badge-light">عادي</span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <strong>نشط:</strong>
                        @if($shop->is_active)
                            <span class="badge badge-success">
                                <i class="fas fa-check"></i> نشط
                            </span>
                        @else
                            <span class="badge badge-secondary">
                                <i class="fas fa-pause"></i> غير نشط
                            </span>
                        @endif
                    </div>

                    <div class="mb-3">
                        <strong>التقييم:</strong>
                        @if($shop->rating > 0)
                            <span class="badge badge-warning">
                                {{ number_format($shop->rating, 1) }} 
                                <i class="fas fa-star"></i>
                            </span>
                            <br><small class="text-muted">({{ $shop->review_count }} تقييم)</small>
                        @else
                            <span class="text-muted">لا توجد تقييمات</span>
                        @endif
                    </div>

                    <hr>

                    <!-- Quick Actions -->
                    <div class="d-grid gap-2">
                        @if(!$shop->is_verified)
                            <form method="POST" action="{{ route('admin.shops.verify', $shop) }}" class="mb-2">
                                @csrf
                                <button type="submit" class="btn btn-success btn-block">
                                    <i class="fas fa-check"></i> تحقق من المتجر
                                </button>
                            </form>
                        @endif

                        @if(!$shop->is_featured)
                            <form method="POST" action="{{ route('admin.shops.feature', $shop) }}" class="mb-2">
                                @csrf
                                <button type="submit" class="btn btn-warning btn-block">
                                    <i class="fas fa-star"></i> إضافة للمميزة
                                </button>
                            </form>
                        @endif

                        <form method="POST" action="{{ route('admin.shops.toggle-status', $shop) }}" class="mb-2">
                            @csrf
                            <button type="submit" 
                                    class="btn btn-{{ $shop->is_active ? 'secondary' : 'success' }} btn-block">
                                <i class="fas fa-{{ $shop->is_active ? 'pause' : 'play' }}"></i>
                                {{ $shop->is_active ? 'إلغاء التفعيل' : 'تفعيل' }}
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Owner Information -->
            @if($shop->owner)
                <div class="card shadow mb-4">
                    <div class="card-header py-3">
                        <h6 class="m-0 font-weight-bold text-primary">معلومات المالك</h6>
                    </div>
                    <div class="card-body">
                        <div class="mb-2">
                            <strong>الاسم:</strong>
                            <p>{{ $shop->owner->name }}</p>
                        </div>
                        <div class="mb-2">
                            <strong>البريد الإلكتروني:</strong>
                            <p>{{ $shop->owner->email }}</p>
                        </div>
                        <div class="mb-2">
                            <strong>نوع المستخدم:</strong>
                            <span class="badge badge-{{ $shop->owner->user_type == 'admin' ? 'danger' : ($shop->owner->user_type == 'shop_owner' ? 'success' : 'secondary') }}">
                                {{ $shop->owner->user_type == 'regular' ? 'عادي' : ($shop->owner->user_type == 'shop_owner' ? 'صاحب متجر' : 'مدير') }}
                            </span>
                        </div>
                        <div class="mb-2">
                            <strong>تاريخ التسجيل:</strong>
                            <p>{{ $shop->owner->created_at->format('Y-m-d') }}</p>
                        </div>
                        <div class="mt-3">
                            <a href="{{ route('admin.users.show', $shop->owner) }}" class="btn btn-primary btn-sm btn-block">
                                <i class="fas fa-user"></i> عرض ملف المستخدم
                            </a>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Location Information -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">معلومات الموقع</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong>المدينة:</strong>
                        <p>{{ $shop->city->name_ar ?? 'غير محدد' }}</p>
                    </div>
                    <div class="mb-2">
                        <strong>التصنيف:</strong>
                        <p>{{ $shop->category->name_ar ?? 'غير محدد' }}</p>
                    </div>
                    @if($shop->latitude && $shop->longitude)
                        <div class="mb-2">
                            <strong>الإحداثيات:</strong>
                            <p>{{ $shop->latitude }}, {{ $shop->longitude }}</p>
                        </div>
                        <div class="mt-3">
                            <a href="https://maps.google.com/?q={{ $shop->latitude }},{{ $shop->longitude }}" 
                               target="_blank" class="btn btn-info btn-sm btn-block">
                                <i class="fas fa-map-marker-alt"></i> عرض على الخريطة
                            </a>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Timestamps -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">معلومات التوقيت</h6>
                </div>
                <div class="card-body">
                    <div class="mb-2">
                        <strong>تاريخ الإنشاء:</strong>
                        <p>{{ $shop->created_at->format('Y-m-d H:i') }}</p>
                        <small class="text-muted">{{ $shop->created_at->diffForHumans() }}</small>
                    </div>
                    <div class="mb-2">
                        <strong>آخر تحديث:</strong>
                        <p>{{ $shop->updated_at->format('Y-m-d H:i') }}</p>
                        <small class="text-muted">{{ $shop->updated_at->diffForHumans() }}</small>
                    </div>
                    @if($shop->verified_at)
                        <div class="mb-2">
                            <strong>تاريخ التحقق:</strong>
                            <p>{{ $shop->verified_at->format('Y-m-d H:i') }}</p>
                            <small class="text-muted">{{ $shop->verified_at->diffForHumans() }}</small>
                        </div>
                    @endif
                    @if($shop->verification_notes)
                        <div class="mb-2">
                            <strong>ملاحظات التحقق:</strong>
                            <p class="text-muted">{{ $shop->verification_notes }}</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
@endsection