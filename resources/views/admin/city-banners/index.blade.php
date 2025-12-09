@extends('layouts.admin')

@section('title', 'إدارة إعلانات المدن')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-image"></i> إدارة إعلانات المدن (Banners)
        </h1>
        <div>
            @can('create-banners')
            <a href="{{ route('admin.city-banners.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> إضافة إعلان جديد
            </a>
            @endcan
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                <span aria-hidden="true">&times;</span>
            </button>
        </div>
    @endif

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">فلترة الإعلانات</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.city-banners.index') }}">
                <div class="row">
                    <div class="col-md-4">
                        <label>البحث</label>
                        <input type="text" name="search" class="form-control" 
                               placeholder="عنوان الإعلان..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label>المدينة</label>
                        <select name="city_id" class="form-control">
                            <option value="">جميع المدن</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" 
                                    {{ request('city_id') == $city->id ? 'selected' : '' }}>
                                    {{ $city->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>الحالة</label>
                        <select name="is_active" class="form-control">
                            <option value="">جميع الحالات</option>
                            <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>
                                نشط
                            </option>
                            <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>
                                غير نشط
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-search"></i> بحث
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Banners Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">
                قائمة الإعلانات ({{ $banners->total() }} إعلان)
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%">
                    <thead class="bg-light">
                        <tr>
                            <th width="5%">#</th>
                            <th width="10%">الصورة</th>
                            <th width="20%">العنوان</th>
                            <th width="15%">المدينة</th>
                            <th width="10%">نوع الرابط</th>
                            <th width="10%">الأولوية</th>
                            <th width="12%">الفترة</th>
                            <th width="8%">الحالة</th>
                            <th width="10%">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($banners as $banner)
                            <tr>
                                <td>{{ $banner->id }}</td>
                                <td>
                                    @if($banner->image)
                                        <img src="{{ $banner->image }}" alt="{{ $banner->title }}" 
                                             class="img-thumbnail" style="max-width: 100px; max-height: 60px;">
                                    @else
                                        <span class="text-muted">لا توجد صورة</span>
                                    @endif
                                </td>
                                <td>
                                    <strong>{{ $banner->title }}</strong>
                                    @if($banner->description)
                                        <br><small class="text-muted">{{ Str::limit($banner->description, 50) }}</small>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-info text-white">
                                        <i class="fas fa-map-marker-alt"></i> {{ $banner->city->name }}
                                    </span>
                                </td>
                                <td>
                                    @if($banner->link_type == 'internal')
                                        <span class="badge bg-primary text-white">داخلي</span>
                                    @elseif($banner->link_type == 'external')
                                        <span class="badge bg-success text-white">خارجي</span>
                                    @else
                                        <span class="badge bg-secondary text-white">بدون رابط</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-warning text-dark" style="font-size: 14px;">
                                        {{ $banner->priority }}
                                    </span>
                                </td>
                                <td>
                                    <small>
                                        <strong>من:</strong> {{ $banner->start_date->format('Y-m-d') }}<br>
                                        <strong>إلى:</strong> {{ $banner->end_date ? $banner->end_date->format('Y-m-d') : 'دائم' }}
                                    </small>
                                </td>
                                <td>
                                    <form action="{{ route('admin.city-banners.toggle-status', $banner) }}" 
                                          method="POST" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button type="submit" class="btn btn-sm btn-{{ $banner->is_active ? 'success' : 'secondary' }} btn-block">
                                            @if($banner->is_active)
                                                <i class="fas fa-check-circle"></i> نشط
                                            @else
                                                <i class="fas fa-times-circle"></i> غير نشط
                                            @endif
                                        </button>
                                    </form>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        @can('edit-banners')
                                        <a href="{{ route('admin.city-banners.edit', $banner) }}" 
                                           class="btn btn-sm btn-warning" title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endcan
                                        
                                        @can('delete-banners')
                                        <form action="{{ route('admin.city-banners.destroy', $banner) }}" 
                                              method="POST" class="d-inline"
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذا الإعلان؟')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn btn-sm btn-danger" title="حذف">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                        @endcan
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">
                                    <i class="fas fa-inbox fa-3x mb-3"></i>
                                    <p>لا توجد إعلانات حالياً</p>
                                    <a href="{{ route('admin.city-banners.create') }}" class="btn btn-primary">
                                        <i class="fas fa-plus"></i> إضافة إعلان جديد
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <div class="mt-3">
                {{ $banners->links() }}
            </div>
        </div>
    </div>
</div>
@endsection
