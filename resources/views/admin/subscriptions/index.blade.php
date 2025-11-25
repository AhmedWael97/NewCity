@extends('layouts.admin')

@section('title', 'إدارة خطط الاشتراك')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-crown"></i> إدارة خطط الاشتراك
        </h1>
        <a href="{{ route('admin.subscription-plans.create') }}" class="btn btn-primary btn-icon-split">
            <span class="icon text-white-50">
                <i class="fas fa-plus"></i>
            </span>
            <span class="text">إضافة خطة جديدة</span>
        </a>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="close" data-dismiss="alert">
                <span>&times;</span>
            </button>
        </div>
    @endif

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">إجمالي الخطط</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_plans'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-list fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">الاشتراكات النشطة</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_subscriptions'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">إيرادات الشهر</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['monthly_revenue'], 2) }} ج.م</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">إجمالي الإيرادات</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($stats['total_revenue'], 2) }} ج.م</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Subscription Plans List -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">خطط الاشتراك المتاحة</h6>
        </div>
        <div class="card-body">
            @if($plans->count() > 0)
                <div class="table-responsive">
                    <table class="table table-bordered table-hover" width="100%">
                        <thead class="bg-light">
                            <tr>
                                <th width="5%">#</th>
                                <th width="15%">اسم الخطة</th>
                                <th width="20%">الوصف</th>
                                <th width="10%">السعر الشهري</th>
                                <th width="10%">السعر السنوي</th>
                                <th width="10%">عدد المشتركين</th>
                                <th width="8%">الحالة</th>
                                <th width="7%">مميزة</th>
                                <th width="15%">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($plans as $plan)
                                <tr>
                                    <td>{{ $plan->id }}</td>
                                    <td>
                                        <strong>{{ $plan->name }}</strong>
                                        @if($plan->is_popular)
                                            <span class="badge bg-warning text-dark">الأكثر شعبية</span>
                                        @endif
                                    </td>
                                    <td>{{ Str::limit($plan->description, 50) }}</td>
                                    <td>
                                        <strong class="text-success">{{ number_format($plan->monthly_price, 2) }} ج.م</strong>
                                    </td>
                                    <td>
                                        <strong class="text-info">{{ number_format($plan->yearly_price, 2) }} ج.م</strong>
                                    </td>
                                    <td>
                                        <span class="badge bg-primary text-white rounded-pill">{{ $plan->subscriptions_count }}</span>
                                    </td>
                                    <td>
                                        <form action="{{ route('admin.subscription-plans.toggle-status', $plan) }}" method="POST" style="display: inline;">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-{{ $plan->is_active ? 'success' : 'secondary' }}">
                                                <i class="fas fa-{{ $plan->is_active ? 'check-circle' : 'times-circle' }}"></i>
                                                {{ $plan->is_active ? 'نشط' : 'معطل' }}
                                            </button>
                                        </form>
                                    </td>
                                    <td class="text-center">
                                        @if($plan->is_popular)
                                            <i class="fas fa-star text-warning"></i>
                                        @else
                                            <i class="far fa-star text-muted"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('admin.subscription-plans.show', $plan) }}" 
                                               class="btn btn-sm btn-info" 
                                               title="عرض التفاصيل">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('admin.subscription-plans.edit', $plan) }}" 
                                               class="btn btn-sm btn-warning" 
                                               title="تعديل">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            <form action="{{ route('admin.subscription-plans.destroy', $plan) }}" 
                                                  method="POST" 
                                                  style="display: inline;"
                                                  onsubmit="return confirm('هل أنت متأكد من حذف هذه الخطة؟');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-danger" title="حذف">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i> لا توجد خطط اشتراك حالياً.
                    <a href="{{ route('admin.subscription-plans.create') }}" class="alert-link">إضافة خطة جديدة</a>
                </div>
            @endif
        </div>
    </div>

    <!-- Features Comparison -->
    @if($plans->count() > 0)
    <div class="card shadow mb-4">
        <div class="card-header py-3 bg-gradient-primary">
            <h6 class="m-0 font-weight-bold text-white">
                <i class="fas fa-table"></i> مقارنة الميزات
            </h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead class="thead-light">
                        <tr>
                            <th>الميزة</th>
                            @foreach($plans as $plan)
                                <th class="text-center">{{ $plan->name }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>عدد المتاجر</strong></td>
                            @foreach($plans as $plan)
                                <td class="text-center">{{ $plan->max_shops }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td><strong>المنتجات لكل متجر</strong></td>
                            @foreach($plans as $plan)
                                <td class="text-center">{{ $plan->max_products_per_shop }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td><strong>الخدمات لكل متجر</strong></td>
                            @foreach($plans as $plan)
                                <td class="text-center">{{ $plan->max_services_per_shop }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td><strong>الصور لكل متجر</strong></td>
                            @foreach($plans as $plan)
                                <td class="text-center">{{ $plan->max_images_per_shop }}</td>
                            @endforeach
                        </tr>
                        <tr>
                            <td><strong>الوصول للتحليلات</strong></td>
                            @foreach($plans as $plan)
                                <td class="text-center">
                                    <i class="fas fa-{{ $plan->analytics_access ? 'check text-success' : 'times text-danger' }}"></i>
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td><strong>الظهور المميز</strong></td>
                            @foreach($plans as $plan)
                                <td class="text-center">
                                    <i class="fas fa-{{ $plan->priority_listing ? 'check text-success' : 'times text-danger' }}"></i>
                                </td>
                            @endforeach
                        </tr>
                        <tr>
                            <td><strong>شارة التحقق</strong></td>
                            @foreach($plans as $plan)
                                <td class="text-center">
                                    <i class="fas fa-{{ $plan->verified_badge ? 'check text-success' : 'times text-danger' }}"></i>
                                </td>
                            @endforeach
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
$(document).ready(function() {
    // Initialize DataTables if needed
    if ($('.table').length) {
        $('.table').DataTable({
            "language": {
                "url": "//cdn.datatables.net/plug-ins/1.10.24/i18n/Arabic.json"
            },
            "order": [[0, "desc"]]
        });
    }
});
</script>
@endpush
