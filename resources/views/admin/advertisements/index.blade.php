@extends('layouts.admin')

@section('title', 'إدارة الإعلانات')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">إدارة الإعلانات</h1>
        <a href="{{ route('admin.advertisements.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> إضافة إعلان جديد
        </a>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                إجمالي الإعلانات
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_ads'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-bullhorn fa-2x text-gray-300"></i>
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
                                الإعلانات النشطة
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_ads'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-play-circle fa-2x text-gray-300"></i>
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
                                إجمالي المشاهدات
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_impressions']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-eye fa-2x text-gray-300"></i>
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
                                إجمالي الإيرادات
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($stats['total_revenue'], 2) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">فلترة الإعلانات</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.advertisements.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="search">البحث</label>
                            <input type="text" class="form-control" id="search" name="search" 
                                   value="{{ request('search') }}" placeholder="البحث في العنوان...">
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="status">الحالة</label>
                            <select class="form-control" id="status" name="status">
                                <option value="">جميع الحالات</option>
                                <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                                <option value="paused" {{ request('status') == 'paused' ? 'selected' : '' }}>متوقف</option>
                                <option value="pending_review" {{ request('status') == 'pending_review' ? 'selected' : '' }}>في المراجعة</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>مرفوض</option>
                                <option value="completed" {{ request('status') == 'completed' ? 'selected' : '' }}>مكتمل</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label for="type">نوع الإعلان</label>
                            <select class="form-control" id="type" name="type">
                                <option value="">جميع الأنواع</option>
                                <option value="banner" {{ request('type') == 'banner' ? 'selected' : '' }}>بانر</option>
                                <option value="hero" {{ request('type') == 'hero' ? 'selected' : '' }}>رئيسي</option>
                                <option value="sponsored_listing" {{ request('type') == 'sponsored_listing' ? 'selected' : '' }}>قائمة مموّلة</option>
                                <option value="sidebar" {{ request('type') == 'sidebar' ? 'selected' : '' }}>جانبي</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group">
                            <label for="city_id">المدينة</label>
                            <select class="form-control" id="city_id" name="city_id">
                                <option value="">جميع المدن</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}" {{ request('city_id') == $city->id ? 'selected' : '' }}>
                                        {{ $city->name_ar }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-md-2">
                        <div class="form-group">
                            <label>&nbsp;</label>
                            <div>
                                <button type="submit" class="btn btn-primary btn-block">
                                    <i class="fas fa-search"></i> بحث
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Advertisements Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">قائمة الإعلانات</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered" width="100%" cellspacing="0">
                    <thead>
                        <tr>
                            <th>الصورة</th>
                            <th>العنوان</th>
                            <th>النوع</th>
                            <th>المدينة</th>
                            <th>نموذج التسعير</th>
                            <th>المشاهدات</th>
                            <th>النقرات</th>
                            <th>معدل النقر</th>
                            <th>الحالة</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($advertisements as $ad)
                            <tr>
                                <td>
                                    @if($ad->image_path)
                                        <img src="{{ Storage::url($ad->image_path) }}" 
                                             alt="{{ $ad->title }}" 
                                             class="img-thumbnail" 
                                             style="width: 60px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="bg-light d-flex align-items-center justify-content-center" 
                                             style="width: 60px; height: 40px;">
                                            <i class="fas fa-image text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $ad->title }}</strong>
                                    @if($ad->description)
                                        <br><small class="text-muted">{{ Str::limit($ad->description, 50) }}</small>
                                    @endif
                                </td>
                                <td>
                                    @switch($ad->type)
                                        @case('banner')
                                            <span class="badge badge-info">بانر</span>
                                            @break
                                        @case('hero')
                                            <span class="badge badge-primary">رئيسي</span>
                                            @break
                                        @case('sponsored_listing')
                                            <span class="badge badge-warning">قائمة مموّلة</span>
                                            @break
                                        @case('sidebar')
                                            <span class="badge badge-secondary">جانبي</span>
                                            @break
                                    @endswitch
                                </td>
                                <td>
                                    @if($ad->scope === 'global')
                                        <span class="text-muted">عالمي</span>
                                    @else
                                        {{ $ad->city?->name_ar ?? 'غير محدد' }}
                                    @endif
                                </td>
                                <td>
                                    <span class="badge badge-light">{{ strtoupper($ad->pricing_model) }}</span>
                                    <br><small>${{ number_format($ad->price_amount, 2) }}</small>
                                </td>
                                <td>{{ number_format($ad->impressions) }}</td>
                                <td>{{ number_format($ad->clicks) }}</td>
                                <td>{{ number_format($ad->ctr, 2) }}%</td>
                                <td>
                                    @switch($ad->status)
                                        @case('active')
                                            <span class="badge badge-success">نشط</span>
                                            @break
                                        @case('paused')
                                            <span class="badge badge-warning">متوقف</span>
                                            @break
                                        @case('pending_review')
                                            <span class="badge badge-info">في المراجعة</span>
                                            @break
                                        @case('rejected')
                                            <span class="badge badge-danger">مرفوض</span>
                                            @break
                                        @case('completed')
                                            <span class="badge badge-secondary">مكتمل</span>
                                            @break
                                    @endswitch
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a href="{{ route('admin.advertisements.show', $ad) }}" 
                                           class="btn btn-outline-info" title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.advertisements.edit', $ad) }}" 
                                           class="btn btn-outline-primary" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        @if($ad->status === 'pending_review')
                                            <form action="{{ route('admin.advertisements.approve', $ad) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-outline-success" title="موافقة">
                                                    <i class="fas fa-check"></i>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        @if($ad->status === 'active')
                                            <form action="{{ route('admin.advertisements.pause', $ad) }}" 
                                                  method="POST" class="d-inline">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="btn btn-outline-warning" title="إيقاف">
                                                    <i class="fas fa-pause"></i>
                                                </button>
                                            </form>
                                        @endif
                                        
                                        <form action="{{ route('admin.advertisements.destroy', $ad) }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذا الإعلان؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-outline-danger" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center py-4">
                                    <div class="text-muted">
                                        <i class="fas fa-bullhorn fa-3x mb-3"></i>
                                        <p>لا توجد إعلانات متاحة</p>
                                        <a href="{{ route('admin.advertisements.create') }}" class="btn btn-primary">
                                            إضافة إعلان جديد
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            <!-- Pagination -->
            <div class="d-flex justify-content-center">
                {{ $advertisements->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
@endsection