@extends('layouts.admin')

@section('title', 'إدارة المتاجر')

@push('styles')
<style>
    /* Table container */
    .table-responsive {
        min-height: 70vh;
    }
    
    /* Action buttons gap */
    .gap-1 {
        gap: 0.25rem !important;
    }
    
    /* Force all table text to be black */
    #dataTable tbody td,
    #dataTable tbody td span,
    #dataTable tbody td strong,
    #dataTable tbody td small,
    #dataTable tbody td div,
    #dataTable tbody td a {
        color: #000 !important;
    }
    
    /* Keep muted text gray */
    #dataTable tbody td .text-muted,
    #dataTable tbody td small.text-muted {
        color: black !important;
    }
    
    /* Keep badges with their colors */
    #dataTable tbody td .badge {
        color: #fff !important;
    }
    
    /* Dropdown items */
    .dropdown-item {
        color: #212529 !important;
    }
    .dropdown-item:hover {
        background-color: #f8f9fa;
    }
    
    /* Header text */
    #dataTable thead th {
        color: #000 !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-store"></i> إدارة المتاجر
        </h1>
        <div>
            <a href="{{ route('admin.shops.create') }}" class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> إضافة متجر جديد
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">فلترة المتاجر</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.shops.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label>البحث</label>
                        <input type="text" name="search" class="form-control" 
                               placeholder="اسم المتجر، الوصف، العنوان..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
                        <label>المدينة</label>
                        <select name="city_id" class="form-control">
                            <option value="">جميع المدن</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}" 
                                    {{ request('city_id') == $city->id ? 'selected' : '' }}>
                                    {{ $city->name_ar }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>التصنيف</label>
                        <select name="category_id" class="form-control">
                            <option value="">جميع التصنيفات</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" 
                                    {{ request('category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name_ar }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>الحالة</label>
                        <select name="status" class="form-control">
                            <option value="">جميع الحالات</option>
                            <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>
                                في الانتظار
                            </option>
                            <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>
                                مقبول
                            </option>
                            <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>
                                مرفوض
                            </option>
                            <option value="suspended" {{ request('status') == 'suspended' ? 'selected' : '' }}>
                                معلق
                            </option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <label>التحقق</label>
                        <select name="is_verified" class="form-control">
                            <option value="">الجميع</option>
                            <option value="1" {{ request('is_verified') == '1' ? 'selected' : '' }}>
                                محقق
                            </option>
                            <option value="0" {{ request('is_verified') == '0' ? 'selected' : '' }}>
                                غير محقق
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

    <!-- Shops Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">قائمة المتاجر ({{ $shops->total() }})</h6>
            <div>
                <button type="button" class="btn btn-sm btn-outline-primary" onclick="selectAll()">
                    <i class="fas fa-check-square"></i> تحديد الكل
                </button>
                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="deselectAll()">
                    <i class="fas fa-square"></i> إلغاء التحديد
                </button>
            </div>
        </div>
        <div class="card-body">
            <form id="bulkActionForm" method="POST" action="{{ route('admin.shops.bulk-action') }}">
                @csrf
                <div class="row mb-3">
                    <div class="col-md-6">
                        <select name="action" class="form-control" required>
                            <option value="">اختر العملية</option>
                            <option value="verify">تحقق</option>
                            <option value="unverify">إلغاء التحقق</option>
                            <option value="activate">تفعيل</option>
                            <option value="deactivate">إلغاء التفعيل</option>
                            <option value="feature">إبراز</option>
                            <option value="unfeature">إلغاء الإبراز</option>
                            <option value="approve">قبول</option>
                            <option value="reject">رفض</option>
                            <option value="suspend">تعليق</option>
                            <option value="delete">حذف</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <button type="submit" class="btn btn-warning">تطبيق على المحدد</button>
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                        <thead class="thead-light">
                            <tr>
                                <th style="width: 40px;"><input type="checkbox" id="selectAllCheckbox"></th>
                                <th class="d-none-sm" style="width: 60px;">الصورة</th>
                                <th>المتجر</th>
                                <th class="d-none-md">المالك</th>
                                <th class="d-none-lg">المدينة</th>
                                <th class="d-none-lg">التصنيف</th>
                                <th class="d-none-sm">التقييم</th>
                                <th>الحالة</th>
                                <th class="d-none-md">التحقق</th>
                                <th class="d-none-lg">مميز</th>
                                <th class="d-none-sm">تاريخ الإنشاء</th>
                                <th style="width: 140px;">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($shops as $shop)
                                <tr>
                                    <td>
                                        <input type="checkbox" name="shops[]" value="{{ $shop->id }}" class="shop-checkbox">
                                    </td>
                                    <td class="d-none-sm">
                                        @if($shop->images && count($shop->images) > 0)
                                            <img src="{{ Storage::url($shop->images[0]) }}" 
                                                 alt="{{ $shop->name }}" 
                                                 class="rounded" 
                                                 style="width: 40px; height: 40px; object-fit: cover;">
                                        @else
                                            <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                                 style="width: 40px; height: 40px;">
                                                <i class="fas fa-store text-muted"></i>
                                            </div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="d-flex flex-column">
                                            <strong class="text-truncate" style="max-width: 150px;">{{ $shop->name }}</strong>
                                            @if($shop->slug)
                                                <small class="text-muted text-truncate" style="max-width: 150px;">{{ $shop->slug }}</small>
                                            @endif
                                            <!-- Mobile-only info -->
                                            <div class="d-block d-md-none">
                                                <small class="text-muted">
                                                    @if($shop->owner){{ $shop->owner->name }}@endif
                                                    @if($shop->city) • {{ $shop->city->name }}@endif
                                                </small>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="d-none-md">
                                        @if($shop->owner)
                                            <div class="text-truncate" style="max-width: 120px;">
                                                <strong>{{ $shop->owner->name }}</strong>
                                                <br><small class="text-muted">{{ $shop->owner->email }}</small>
                                            </div>
                                        @else
                                            <span class="text-muted">غير محدد</span>
                                        @endif
                                    </td>
                                    <td class="d-none-lg">
                                        @if($shop->city)
                                            <span class="badge bg-info text-white">{{ $shop->city->name }}</span>
                                        @else
                                            <span class="text-muted">غير محدد</span>
                                        @endif
                                    </td>
                                    <td class="d-none-lg">
                                        @if($shop->category)
                                            <span class="badge bg-secondary text-white">{{ $shop->category->name }}</span>
                                        @else
                                            <span class="text-muted">غير محدد</span>
                                        @endif
                                    </td>
                                    <td class="d-none-sm">
                                        @if($shop->rating > 0)
                                            <span class="badge bg-warning text-dark">
                                                {{ number_format($shop->rating, 1) }} <i class="fas fa-star"></i>
                                            </span>
                                            <br><small class="text-muted">({{ $shop->review_count }})</small>
                                        @else
                                            <span class="text-muted">--</span>
                                        @endif
                                    </td>
                                    <td>
                                        @switch($shop->status)
                                            @case('pending')
                                                <span class="badge bg-warning text-dark">انتظار</span>
                                                @break
                                            @case('approved')
                                                <span class="badge bg-success text-white">مقبول</span>
                                                @break
                                            @case('rejected')
                                                <span class="badge bg-danger text-white">مرفوض</span>
                                                @break
                                            @case('suspended')
                                                <span class="badge bg-secondary text-white">معلق</span>
                                                @break
                                            @default
                                                <span class="badge bg-light text-dark">{{ $shop->status }}</span>
                                        @endswitch
                                        <!-- Mobile-only additional info -->
                                        <div class="d-block d-md-none mt-1">
                                            @if($shop->is_verified)
                                                <span class="badge bg-success text-white">محقق</span>
                                            @endif
                                            @if($shop->is_featured)
                                                <span class="badge bg-primary text-white">مميز</span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="d-none-md">
                                        @if($shop->is_verified)
                                            <span class="badge bg-success text-white">
                                                <i class="fas fa-check"></i> محقق
                                            </span>
                                        @else
                                            <span class="badge bg-secondary text-white">
                                                <i class="fas fa-times"></i> غير محقق
                                            </span>
                                        @endif
                                    </td>
                                    <td class="d-none-lg">
                                        @if($shop->is_featured)
                                            <span class="badge bg-primary text-white">
                                                <i class="fas fa-star"></i> مميز
                                            </span>
                                        @else
                                            <span class="badge bg-light text-dark">عادي</span>
                                        @endif
                                    </td>
                                    <td class="d-none-sm">
                                        <div class="text-truncate" style="max-width: 100px;">
                                            {{ $shop->created_at->format('Y-m-d') }}
                                            <br><small class="text-muted">{{ $shop->created_at->diffForHumans() }}</small>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="d-flex gap-1 flex-nowrap">
                                            <a href="{{ route('admin.shops.show', $shop) }}" 
                                               class="btn btn-sm btn-info" 
                                               title="عرض">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.shops.edit', $shop) }}" 
                                               class="btn btn-sm btn-primary" 
                                               title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form method="POST" action="{{ route('admin.shops.destroy', $shop) }}" 
                                                  style="display: inline;"
                                                  onsubmit="return confirm('هل أنت متأكد من حذف هذا المتجر؟')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="حذف">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="12" class="text-center">
                                        <div class="py-4">
                                            <i class="fas fa-store fa-3x text-muted mb-3"></i>
                                            <h5 class="text-muted">لا توجد متاجر</h5>
                                            <p class="text-muted">لم يتم العثور على متاجر مطابقة لمعايير البحث</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </form>

            <!-- Pagination -->
            @if($shops->hasPages())
                <div class="card-footer bg-light border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="pagination-info">
                            عرض {{ $shops->firstItem() }} إلى {{ $shops->lastItem() }} من {{ $shops->total() }} متجر
                        </div>
                        <div>
                            {{ $shops->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function selectAll() {
    document.querySelectorAll('.shop-checkbox').forEach(checkbox => {
        checkbox.checked = true;
    });
    document.getElementById('selectAllCheckbox').checked = true;
}

function deselectAll() {
    document.querySelectorAll('.shop-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    document.getElementById('selectAllCheckbox').checked = false;
}

document.getElementById('selectAllCheckbox').addEventListener('change', function() {
    const isChecked = this.checked;
    document.querySelectorAll('.shop-checkbox').forEach(checkbox => {
        checkbox.checked = isChecked;
    });
});

// Auto-submit search form on filter change
document.querySelectorAll('select[name="city_id"], select[name="category_id"], select[name="status"], select[name="is_verified"]').forEach(select => {
    select.addEventListener('change', function() {
        this.form.submit();
    });
});

// Initialize Bootstrap 5 dropdowns manually
document.addEventListener('DOMContentLoaded', function() {
    // Make sure Bootstrap is loaded
    if (typeof bootstrap !== 'undefined') {
        // Initialize all dropdowns
        var dropdownElementList = [].slice.call(document.querySelectorAll('[data-bs-toggle="dropdown"]'));
        var dropdownList = dropdownElementList.map(function (dropdownToggleEl) {
            return new bootstrap.Dropdown(dropdownToggleEl);
        });
    }
});
</script>
@endsection