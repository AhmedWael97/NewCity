@extends('layouts.admin')

@section('title', 'أداء المتاجر - التحليلات')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">أداء المتاجر</h1>
            <p class="text-muted">تحليل تفصيلي لأداء جميع المتاجر في النظام</p>
        </div>
        <div>
            <a href="{{ route('admin.analytics.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-right"></i> العودة للتحليلات
            </a>
        </div>
    </div>

    <!-- Filters -->
    <div class="card shadow mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('admin.analytics.shops') }}" class="row g-3">
                <div class="col-md-3">
                    <label for="city_filter" class="form-label">المدينة</label>
                    <select name="city" id="city_filter" class="form-select">
                        <option value="">جميع المدن</option>
                        @foreach(\App\Models\City::pluck('name', 'id') as $id => $name)
                            <option value="{{ $id }}" {{ request('city') == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="category_filter" class="form-label">الفئة</label>
                    <select name="category" id="category_filter" class="form-select">
                        <option value="">جميع الفئات</option>
                        @foreach(\App\Models\Category::pluck('name', 'id') as $id => $name)
                            <option value="{{ $id }}" {{ request('category') == $id ? 'selected' : '' }}>
                                {{ $name }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="status_filter" class="form-label">الحالة</label>
                    <select name="status" id="status_filter" class="form-select">
                        <option value="">جميع الحالات</option>
                        <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>نشط</option>
                        <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>غير نشط</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="sort_filter" class="form-label">ترتيب حسب</label>
                    <select name="sort" id="sort_filter" class="form-select">
                        <option value="views" {{ request('sort') == 'views' ? 'selected' : '' }}>المشاهدات</option>
                        <option value="visitors" {{ request('sort') == 'visitors' ? 'selected' : '' }}>الزوار الفريدين</option>
                        <option value="contacts" {{ request('sort') == 'contacts' ? 'selected' : '' }}>النقرات على جهات الاتصال</option>
                        <option value="created_at" {{ request('sort') == 'created_at' ? 'selected' : '' }}>تاريخ الإنشاء</option>
                    </select>
                </div>
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-filter"></i> تطبيق الفلاتر
                    </button>
                    <a href="{{ route('admin.analytics.shops') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> إزالة الفلاتر
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Shop Performance Table -->
    <div class="card shadow">
        <div class="card-header py-3 d-flex justify-content-between align-items-center">
            <h6 class="m-0 font-weight-bold text-primary">قائمة المتاجر وأدائها</h6>
            <div class="text-muted">
                <small>المجموع: {{ $shops->total() }} متجر</small>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="border-0">#</th>
                            <th class="border-0">اسم المتجر</th>
                            <th class="border-0">المالك</th>
                            <th class="border-0">المدينة</th>
                            <th class="border-0">الفئة</th>
                            <th class="border-0">الحالة</th>
                            <th class="border-0 text-center">المشاهدات</th>
                            <th class="border-0 text-center">الزوار الفريدين</th>
                            <th class="border-0 text-center">نقرات الاتصال</th>
                            <th class="border-0 text-center">معدل التحويل</th>
                            <th class="border-0 text-center">الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($shops as $shop)
                            <tr>
                                <td class="align-middle">{{ ($shops->currentPage() - 1) * $shops->perPage() + $loop->iteration }}</td>
                                <td class="align-middle">
                                    <div class="d-flex align-items-center">
                                        <div class="avatar-sm rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3">
                                            {{ strtoupper(substr($shop->name, 0, 1)) }}
                                        </div>
                                        <div>
                                            <h6 class="mb-0 fw-bold">{{ $shop->name }}</h6>
                                            <small class="text-muted">{{ Str::limit($shop->description, 50) }}</small>
                                        </div>
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <span class="text-dark fw-medium">{{ $shop->user->name ?? 'غير محدد' }}</span>
                                    <br>
                                    <small class="text-muted">{{ $shop->user->email ?? '' }}</small>
                                </td>
                                <td class="align-middle">
                                    <span class="badge bg-info">{{ $shop->city->name ?? 'غير محدد' }}</span>
                                </td>
                                <td class="align-middle">
                                    <span class="badge bg-secondary">{{ $shop->category->name ?? 'غير محدد' }}</span>
                                </td>
                                <td class="align-middle">
                                    @if($shop->is_active)
                                        <span class="badge bg-success">نشط</span>
                                    @else
                                        <span class="badge bg-danger">غير نشط</span>
                                    @endif
                                </td>
                                <td class="align-middle text-center">
                                    <div class="fw-bold text-primary">{{ number_format($shop->analytics['total_views'] ?? 0) }}</div>
                                    <small class="text-muted">الشهر الحالي: {{ number_format($shop->analytics['monthly_views'] ?? 0) }}</small>
                                </td>
                                <td class="align-middle text-center">
                                    <div class="fw-bold text-success">{{ number_format($shop->analytics['unique_visitors'] ?? 0) }}</div>
                                </td>
                                <td class="align-middle text-center">
                                    <div class="fw-bold text-warning">{{ number_format($shop->analytics['contact_clicks'] ?? 0) }}</div>
                                </td>
                                <td class="align-middle text-center">
                                    @php
                                        $views = $shop->analytics['total_views'] ?? 0;
                                        $contacts = $shop->analytics['contact_clicks'] ?? 0;
                                        $conversionRate = $views > 0 ? ($contacts / $views) * 100 : 0;
                                    @endphp
                                    <div class="fw-bold {{ $conversionRate > 5 ? 'text-success' : ($conversionRate > 2 ? 'text-warning' : 'text-danger') }}">
                                        {{ number_format($conversionRate, 2) }}%
                                    </div>
                                </td>
                                <td class="align-middle">
                                    <div class="dropdown">
                                        <button class="btn btn-sm btn-outline-secondary dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                            الإجراءات
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.shops.show', $shop->id) }}">
                                                    <i class="fas fa-eye me-2"></i> عرض التفاصيل
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item" href="{{ route('admin.shops.edit', $shop->id) }}">
                                                    <i class="fas fa-edit me-2"></i> تعديل
                                                </a>
                                            </li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li>
                                                <a class="dropdown-item text-info" href="#" onclick="viewDetailedAnalytics({{ $shop->id }})">
                                                    <i class="fas fa-chart-line me-2"></i> تحليلات تفصيلية
                                                </a>
                                            </li>
                                            @if($shop->is_active)
                                                <li>
                                                    <form action="{{ route('admin.shops.toggle-status', $shop->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="dropdown-item text-warning">
                                                            <i class="fas fa-pause me-2"></i> إيقاف
                                                        </button>
                                                    </form>
                                                </li>
                                            @else
                                                <li>
                                                    <form action="{{ route('admin.shops.toggle-status', $shop->id) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('PATCH')
                                                        <button type="submit" class="dropdown-item text-success">
                                                            <i class="fas fa-play me-2"></i> تفعيل
                                                        </button>
                                                    </form>
                                                </li>
                                            @endif
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-5">
                                    <div class="text-muted">
                                        <i class="fas fa-store fa-3x mb-3"></i>
                                        <h5>لا توجد متاجر مطابقة للفلاتر المحددة</h5>
                                        <p>جرب تغيير الفلاتر أو إزالتها لعرض المزيد من النتائج</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        
        @if($shops->hasPages())
            <div class="card-footer">
                {{ $shops->appends(request()->query())->links('custom.pagination') }}
            </div>
        @endif
    </div>
</div>

<!-- Detailed Analytics Modal -->
<div class="modal fade" id="analyticsModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">تحليلات تفصيلية للمتجر</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="analyticsModalBody">
                <!-- Analytics content will be loaded here -->
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function viewDetailedAnalytics(shopId) {
    // Show modal
    $('#analyticsModal').modal('show');
    
    // Load analytics data
    $('#analyticsModalBody').html('<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x"></i><br>جاري تحميل البيانات...</div>');
    
    fetch(`/admin/shops/${shopId}/analytics`)
        .then(response => response.json())
        .then(data => {
            $('#analyticsModalBody').html(`
                <div class="row">
                    <div class="col-md-6">
                        <div class="card bg-primary text-white">
                            <div class="card-body">
                                <h5>إجمالي المشاهدات</h5>
                                <h2>${data.total_views || 0}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="card bg-success text-white">
                            <div class="card-body">
                                <h5>الزوار الفريدين</h5>
                                <h2>${data.unique_visitors || 0}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mt-3">
                        <div class="card bg-warning text-white">
                            <div class="card-body">
                                <h5>نقرات الاتصال</h5>
                                <h2>${data.contact_clicks || 0}</h2>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6 mt-3">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h5>معدل التحويل</h5>
                                <h2>${data.conversion_rate || 0}%</h2>
                            </div>
                        </div>
                    </div>
                </div>
            `);
        })
        .catch(error => {
            $('#analyticsModalBody').html('<div class="alert alert-danger">حدث خطأ في تحميل البيانات</div>');
        });
}

// Auto-submit form on filter change
document.querySelectorAll('#city_filter, #category_filter, #status_filter, #sort_filter').forEach(select => {
    select.addEventListener('change', function() {
        this.closest('form').submit();
    });
});
</script>
@endsection