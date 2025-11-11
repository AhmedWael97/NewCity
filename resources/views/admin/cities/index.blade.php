@extends('layouts.admin')

@section('title', 'إدارة المدن')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-city"></i> إدارة المدن
        </h1>
        <div>
            <a href="{{ route('admin.cities.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> إضافة مدينة جديدة
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">فلترة المدن</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.cities.index') }}">
                <div class="row">
                    <div class="col-md-4">
                        <label>البحث</label>
                        <input type="text" name="search" class="form-control" 
                               placeholder="اسم المدينة، الرابط..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-3">
                        <label>الدولة</label>
                        <select name="country" class="form-control">
                            <option value="">جميع الدول</option>
                            @foreach($countries ?? [] as $country)
                                <option value="{{ $country }}" 
                                    {{ request('country') == $country ? 'selected' : '' }}>
                                    {{ $country }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label>الولاية/المحافظة</label>
                        <select name="state" class="form-control">
                            <option value="">الجميع</option>
                            @foreach($states ?? [] as $state)
                                <option value="{{ $state }}" 
                                    {{ request('state') == $state ? 'selected' : '' }}>
                                    {{ $state }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>الحالة</label>
                        <select name="is_active" class="form-control">
                            <option value="">الجميع</option>
                            <option value="1" {{ request('is_active') == '1' ? 'selected' : '' }}>
                                نشط
                            </option>
                            <option value="0" {{ request('is_active') == '0' ? 'selected' : '' }}>
                                غير نشط
                            </option>
                        </select>
                    </div>
                    <div class="col-md-1">
                        <label>&nbsp;</label>
                        <button type="submit" class="btn btn-primary btn-block">
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Cities Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">قائمة المدن ({{ $cities->total() }})</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="table-dark">
                        <tr>
                            <th>ID</th>
                            <th>اسم المدينة</th>
                            <th>الرابط (Slug)</th>
                            <th>الدولة</th>
                            <th>الولاية/المحافظة</th>
                            <th>عدد المتاجر</th>
                            <th>الحالة</th>
                            <th>تاريخ الإنشاء</th>
                            <th class="no-sort">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cities as $city)
                            <tr>
                                <td>{{ $city->id }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        @if($city->image)
                                            <img src="{{ asset('storage/' . $city->image) }}" 
                                                 alt="{{ $city->name }}" 
                                                 class="rounded-circle me-2" 
                                                 style="width: 40px; height: 40px; object-fit: cover;"
                                                 onerror="this.style.display='none'">
                                        @else
                                            <div class="rounded-circle me-2 bg-primary text-white d-flex align-items-center justify-content-center" 
                                                 style="width: 40px; height: 40px; font-size: 18px; font-weight: bold;">
                                                {{ substr($city->name, 0, 1) }}
                                            </div>
                                        @endif
                                        <div>
                                            <strong>{{ $city->name }}</strong>
                                            @if($city->shops_count > 0)
                                                <br><small class="text-success">{{ $city->shops_count }} متجر</small>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td>
                                    <span class="badge badge-info">{{ $city->slug }}</span>
                                </td>
                                <td>{{ $city->country }}</td>
                                <td>{{ $city->state ?: 'غير محدد' }}</td>
                                <td>
                                    <span class="badge badge-primary">{{ $city->shops_count ?? 0 }}</span>
                                </td>
                                <td>
                                    @if($city->is_active)
                                        <span class="badge badge-success">
                                            <i class="fas fa-check"></i> نشط
                                        </span>
                                    @else
                                        <span class="badge badge-secondary">
                                            <i class="fas fa-pause"></i> غير نشط
                                        </span>
                                    @endif
                                </td>
                                <td>
                                    {{ $city->created_at->format('Y-m-d') }}
                                    <br><small class="text-muted">{{ $city->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <div class="btn-group" role="group">
                                        <a href="{{ route('admin.cities.show', $city) }}" class="btn btn-info btn-sm">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.cities.edit', $city) }}" class="btn btn-primary btn-sm">
                                            <i class="fas fa-edit"></i>
                                        </a>

                                        <form method="POST" action="{{ route('admin.cities.toggle-active', $city) }}" class="d-inline">
                                            @csrf
                                            <button type="submit" 
                                                    class="btn btn-{{ $city->is_active ? 'warning' : 'success' }} btn-sm"
                                                    title="{{ $city->is_active ? 'إلغاء التفعيل' : 'تفعيل' }}">
                                                <i class="fas fa-{{ $city->is_active ? 'pause' : 'play' }}"></i>
                                            </button>
                                        </form>

                                        @if($city->shops_count == 0)
                                            <form method="POST" action="{{ route('admin.cities.destroy', $city) }}" class="d-inline" 
                                                  onsubmit="return confirm('هل أنت متأكد من حذف هذه المدينة؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-danger btn-sm">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">
                                    <div class="py-4">
                                        <i class="fas fa-city fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">لا توجد مدن</h5>
                                        <p class="text-muted">لم يتم العثور على مدن مطابقة لمعايير البحث</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($cities->hasPages())
                <div class="d-flex justify-content-center mt-4">
                    {{ $cities->appends(request()->query())->links() }}
                </div>
            @endif
        </div>
    </div>
</div>

<script>
// Auto-submit search form on filter change
document.querySelectorAll('select[name="governorate"], select[name="is_active"]').forEach(select => {
    select.addEventListener('change', function() {
        this.form.submit();
    });
});
</script>
@endsection