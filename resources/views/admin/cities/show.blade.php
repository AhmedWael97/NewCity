@extends('layouts.admin')

@section('title', 'عرض المدينة: ' . $city->name)

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-city"></i> {{ $city->name }}
        </h1>
        <div>
            <a href="{{ route('admin.cities.edit', $city) }}" class="btn btn-primary btn-sm">
                <i class="fas fa-edit"></i> تعديل
            </a>
            <a href="{{ route('admin.cities.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-right"></i> العودة للقائمة
            </a>
        </div>
    </div>

    <div class="row">
        <!-- City Information -->
        <div class="col-lg-8">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">معلومات المدينة</h6>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>الاسم:</strong>
                            <p>{{ $city->name }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>الرابط (Slug):</strong>
                            <p><span class="badge badge-info">{{ $city->slug }}</span></p>
                        </div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>الدولة:</strong>
                            <p>{{ $city->country }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>الولاية/المحافظة:</strong>
                            <p>{{ $city->state ?: 'غير محدد' }}</p>
                        </div>
                    </div>

                    @if($city->description)
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <strong>الوصف:</strong>
                            <p>{{ $city->description }}</p>
                        </div>
                    </div>
                    @endif

                    @if($city->latitude && $city->longitude)
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>خط العرض:</strong>
                            <p>{{ $city->latitude }}</p>
                        </div>
                        <div class="col-md-6">
                            <strong>خط الطول:</strong>
                            <p>{{ $city->longitude }}</p>
                        </div>
                    </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-6">
                            <strong>الحالة:</strong>
                            <p>
                                @if($city->is_active)
                                    <span class="badge badge-success">
                                        <i class="fas fa-check"></i> نشط
                                    </span>
                                @else
                                    <span class="badge badge-secondary">
                                        <i class="fas fa-pause"></i> غير نشط
                                    </span>
                                @endif
                            </p>
                        </div>
                        <div class="col-md-6">
                            <strong>تاريخ الإنشاء:</strong>
                            <p>{{ $city->created_at->format('Y-m-d H:i') }}</p>
                        </div>
                    </div>

                    @if($city->updated_at != $city->created_at)
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <strong>آخر تحديث:</strong>
                            <p>{{ $city->updated_at->format('Y-m-d H:i') }} 
                                <small class="text-muted">({{ $city->updated_at->diffForHumans() }})</small>
                            </p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- City Image & Stats -->
        <div class="col-lg-4">
            <!-- City Image -->
            @if($city->image)
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">صورة المدينة</h6>
                </div>
                <div class="card-body text-center">
                    <img src="{{ asset('storage/' . $city->image) }}" 
                         alt="{{ $city->name }}" 
                         class="img-fluid rounded"
                         style="max-height: 300px;"
                         onerror="this.style.display='none'; this.parentElement.innerHTML='<p class=\'text-muted\'>فشل تحميل الصورة</p>'">
                </div>
            </div>
            @endif

            <!-- Statistics -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">الإحصائيات</h6>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <h3 class="text-primary">{{ $stats['total_shops'] }}</h3>
                        <small class="text-muted">إجمالي المتاجر</small>
                    </div>
                    <hr>
                    <div class="text-center mb-3">
                        <h3 class="text-success">{{ $stats['active_shops'] }}</h3>
                        <small class="text-muted">متاجر نشطة</small>
                    </div>
                    <hr>
                    <div class="text-center mb-3">
                        <h3 class="text-warning">{{ $stats['pending_shops'] }}</h3>
                        <small class="text-muted">متاجر قيد المراجعة</small>
                    </div>
                    <hr>
                    <div class="text-center">
                        <h3 class="text-info">{{ $stats['total_users'] }}</h3>
                        <small class="text-muted">المستخدمين</small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Shops Table -->
    @if($city->shops->count() > 0)
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">المتاجر في هذه المدينة ({{ $city->shops->count() }})</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>اسم المتجر</th>
                            <th>الفئة</th>
                            <th>الحالة</th>
                            <th>التقييم</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($city->shops as $shop)
                        <tr>
                            <td>{{ $shop->id }}</td>
                            <td>
                                <strong>{{ $shop->name }}</strong>
                                @if($shop->is_verified)
                                    <i class="fas fa-check-circle text-success" title="متجر موثق"></i>
                                @endif
                            </td>
                            <td>{{ $shop->category->name ?? 'غير محدد' }}</td>
                            <td>
                                @if($shop->status == 'active')
                                    <span class="badge badge-success">نشط</span>
                                @elseif($shop->status == 'pending')
                                    <span class="badge badge-warning">قيد المراجعة</span>
                                @else
                                    <span class="badge badge-secondary">غير نشط</span>
                                @endif
                            </td>
                            <td>
                                @if($shop->ratings_count > 0)
                                    <span class="text-warning">
                                        <i class="fas fa-star"></i> {{ number_format($shop->ratings_avg_rating, 1) }}
                                    </span>
                                    <small class="text-muted">({{ $shop->ratings_count }})</small>
                                @else
                                    <span class="text-muted">لا توجد تقييمات</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('admin.shops.show', $shop) }}" class="btn btn-info btn-sm">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{ route('admin.shops.edit', $shop) }}" class="btn btn-primary btn-sm">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection
