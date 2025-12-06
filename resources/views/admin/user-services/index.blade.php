@extends('layouts.admin')

@section('title', 'إدارة خدمات المستخدمين')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-tools"></i> إدارة خدمات المستخدمين
        </h1>
        <a href="{{ route('admin.user-services.create') }}" class="btn btn-primary btn-icon-split">
            <span class="icon text-white-50">
                <i class="fas fa-plus"></i>
            </span>
            <span class="text">إضافة خدمة جديدة</span>
        </a>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">فلترة الخدمات</h6>
        </div>
        <div class="card-body">
            <form method="GET" action="{{ route('admin.user-services.index') }}">
                <div class="row">
                    <div class="col-md-3">
                        <label>البحث</label>
                        <input type="text" name="search" class="form-control" 
                               placeholder="العنوان، الوصف، المستخدم..." 
                               value="{{ request('search') }}">
                    </div>
                    <div class="col-md-2">
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
                    <div class="col-md-2">
                        <label>التصنيف</label>
                        <select name="service_category_id" class="form-control">
                            <option value="">جميع التصنيفات</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" 
                                    {{ request('service_category_id') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
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

    <!-- Services Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">قائمة الخدمات ({{ $services->total() }})</h6>
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
            <div class="row mb-3">
                <div class="col-md-6">
                    <select id="bulkAction" class="form-control">
                        <option value="">اختر العملية</option>
                        <option value="verify">تحقق</option>
                        <option value="unverify">إلغاء التحقق</option>
                        <option value="activate">تفعيل</option>
                        <option value="deactivate">إلغاء التفعيل</option>
                        <option value="feature">إبراز</option>
                        <option value="unfeature">إلغاء الإبراز</option>
                        <option value="delete">حذف</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <button type="button" class="btn btn-warning" onclick="submitBulkAction()">تطبيق على المحدد</button>
                </div>
            </div>

            <form id="bulkActionForm" method="POST" action="{{ route('admin.user-services.bulk-action') }}" style="display: none;">
                @csrf
                <input type="hidden" name="action" id="bulkActionInput">
                <div id="bulkServicesContainer"></div>
            </form>

            <div class="table-responsive">
                <table class="table table-bordered table-hover" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th style="width: 40px;"><input type="checkbox" id="selectAllCheckbox"></th>
                            <th class="d-none-sm" style="width: 60px;">الصورة</th>
                            <th>العنوان</th>
                            <th class="d-none-md">المستخدم</th>
                            <th class="d-none-lg">المدينة</th>
                            <th class="d-none-lg">التصنيف</th>
                            <th class="d-none-sm">السعر</th>
                            <th>الحالة</th>
                            <th class="d-none-md">التحقق</th>
                            <th class="d-none-lg">مميز</th>
                            <th class="d-none-sm">التاريخ</th>
                            <th style="width: 140px;">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($services as $service)
                            <tr>
                                <td>
                                    <input type="checkbox" value="{{ $service->id }}" class="service-checkbox">
                                </td>
                                <td class="d-none-sm">
                                    @if($service->images && count($service->images) > 0)
                                        <img src="{{ asset('storage/' . $service->images[0]) }}" 
                                             alt="{{ $service->title }}" 
                                             class="rounded" 
                                             style="width: 40px; height: 40px; object-fit: cover;">
                                    @else
                                        <div class="bg-light rounded d-flex align-items-center justify-content-center" 
                                             style="width: 40px; height: 40px;">
                                            <i class="fas fa-tools text-muted"></i>
                                        </div>
                                    @endif
                                </td>
                                <td>
                                    <strong class="text-truncate d-block" style="max-width: 200px;">
                                        {{ $service->title }}
                                    </strong>
                                    <small class="text-muted">{{ Str::limit($service->description, 50) }}</small>
                                </td>
                                <td class="d-none-md">
                                    @if($service->user)
                                        <strong>{{ $service->user->name }}</strong>
                                        <br><small class="text-muted">{{ $service->user->phone }}</small>
                                    @endif
                                </td>
                                <td class="d-none-lg">
                                    @if($service->city)
                                        <span class="badge bg-info text-white">{{ $service->city->name }}</span>
                                    @endif
                                </td>
                                <td class="d-none-lg">
                                    @if($service->serviceCategory)
                                        <span class="badge bg-secondary text-white">{{ $service->serviceCategory->name }}</span>
                                    @endif
                                </td>
                                <td class="d-none-sm">
                                    @if($service->pricing_type == 'fixed')
                                        {{ number_format($service->price_from) }} جنيه
                                    @elseif($service->pricing_type == 'hourly')
                                        {{ number_format($service->price_from) }} جنيه/ساعة
                                    @elseif($service->pricing_type == 'per_km')
                                        {{ number_format($service->price_from) }} جنيه/كم
                                    @else
                                        قابل للتفاوض
                                    @endif
                                </td>
                                <td>
                                    @if($service->is_active)
                                        <span class="badge bg-success text-white">نشط</span>
                                    @else
                                        <span class="badge bg-secondary text-white">غير نشط</span>
                                    @endif
                                </td>
                                <td class="d-none-md">
                                    @if($service->is_verified)
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
                                    @if($service->is_featured)
                                        <span class="badge bg-primary text-white">
                                            <i class="fas fa-star"></i> مميز
                                        </span>
                                    @else
                                        <span class="badge bg-light text-dark">عادي</span>
                                    @endif
                                </td>
                                <td class="d-none-sm">
                                    {{ $service->created_at->format('Y-m-d') }}
                                    <br><small class="text-muted">{{ $service->created_at->diffForHumans() }}</small>
                                </td>
                                <td>
                                    <div class="d-flex gap-1 flex-nowrap">
                                        <a href="{{ route('admin.user-services.show', $service) }}" 
                                           class="btn btn-sm btn-info" 
                                           title="عرض">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        <a href="{{ route('admin.user-services.edit', $service) }}" 
                                           class="btn btn-sm btn-primary" 
                                           title="تعديل">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form method="POST" action="{{ route('admin.user-services.destroy', $service) }}" 
                                              style="display: inline;"
                                              onsubmit="return confirm('هل أنت متأكد من حذف هذه الخدمة؟')">
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
                                        <i class="fas fa-tools fa-3x text-muted mb-3"></i>
                                        <h5 class="text-muted">لا توجد خدمات</h5>
                                        <p class="text-muted">لم يتم العثور على خدمات مطابقة لمعايير البحث</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($services->hasPages())
                <div class="card-footer bg-light border-0">
                    <div class="d-flex justify-content-between align-items-center">
                        <div class="pagination-info">
                            عرض {{ $services->firstItem() }} إلى {{ $services->lastItem() }} من {{ $services->total() }} خدمة
                        </div>
                        <div>
                            {{ $services->appends(request()->query())->links() }}
                        </div>
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>

<script>
function selectAll() {
    document.querySelectorAll('.service-checkbox').forEach(checkbox => {
        checkbox.checked = true;
    });
    document.getElementById('selectAllCheckbox').checked = true;
}

function deselectAll() {
    document.querySelectorAll('.service-checkbox').forEach(checkbox => {
        checkbox.checked = false;
    });
    document.getElementById('selectAllCheckbox').checked = false;
}

document.getElementById('selectAllCheckbox').addEventListener('change', function() {
    const isChecked = this.checked;
    document.querySelectorAll('.service-checkbox').forEach(checkbox => {
        checkbox.checked = isChecked;
    });
});

function submitBulkAction() {
    const action = document.getElementById('bulkAction').value;
    if (!action) {
        alert('الرجاء اختيار عملية');
        return;
    }
    
    const checkedBoxes = document.querySelectorAll('.service-checkbox:checked');
    if (checkedBoxes.length === 0) {
        alert('الرجاء اختيار خدمة واحدة على الأقل');
        return;
    }
    
    if (!confirm('هل أنت متأكد من تطبيق هذه العملية على ' + checkedBoxes.length + ' خدمة؟')) {
        return;
    }
    
    const form = document.getElementById('bulkActionForm');
    const container = document.getElementById('bulkServicesContainer');
    
    container.innerHTML = '';
    
    checkedBoxes.forEach(checkbox => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'services[]';
        input.value = checkbox.value;
        container.appendChild(input);
    });
    
    document.getElementById('bulkActionInput').value = action;
    form.submit();
}

document.querySelectorAll('select[name="city_id"], select[name="service_category_id"], select[name="is_active"], select[name="is_verified"]').forEach(select => {
    select.addEventListener('change', function() {
        this.form.submit();
    });
});
</script>
@endsection
