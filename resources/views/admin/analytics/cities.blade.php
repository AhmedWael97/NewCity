@extends('layouts.admin')

@section('title', 'تحليلات المدن')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-city"></i> تحليلات المدن
        </h1>
        <div>
            <a href="{{ route('admin.analytics.index') }}" class="btn btn-secondary btn-sm">
                <i class="fas fa-arrow-left"></i> العودة للتحليلات
            </a>
        </div>
    </div>

    <!-- Summary Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                إجمالي المدن
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $cities->count() }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-city fa-2x text-gray-300"></i>
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
                                إجمالي المتاجر
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ $cities->sum('shops_count') }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-store fa-2x text-gray-300"></i>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($cities->sum('analytics.total_views')) }}
                            </div>
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
                                الزوار الفريدون
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($cities->sum('analytics.unique_visitors')) }}
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Cities Table -->
    <div class="card shadow mb-4">
        <div class="card-header py-3">
            <h6 class="m-0 font-weight-bold text-primary">تفاصيل المدن</h6>
        </div>
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-bordered table-hover" id="dataTable" width="100%" cellspacing="0">
                    <thead class="thead-light">
                        <tr>
                            <th>المدينة</th>
                            <th>المتاجر</th>
                            <th>المستخدمين</th>
                            <th>المشاهدات</th>
                            <th>الزوار</th>
                            <th>عمليات البحث</th>
                            <th>النقرات</th>
                            <th>معدل الارتداد</th>
                            <th>الإجراءات</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($cities as $city)
                            <tr>
                                <td>
                                    <strong>{{ $city->name }}</strong>
                                    @if($city->is_active)
                                        <span class="badge bg-success text-white ms-1">نشط</span>
                                    @else
                                        <span class="badge bg-secondary text-white ms-1">غير نشط</span>
                                    @endif
                                </td>
                                <td>
                                    <span class="badge bg-primary text-white">
                                        {{ number_format($city->shops_count) }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-info text-white">
                                        {{ number_format($city->users_count ?? 0) }}
                                    </span>
                                </td>
                                <td>{{ number_format($city->analytics['total_views']) }}</td>
                                <td>{{ number_format($city->analytics['unique_visitors']) }}</td>
                                <td>{{ number_format($city->analytics['searches']) }}</td>
                                <td>{{ number_format($city->analytics['contact_clicks']) }}</td>
                                <td>
                                    <span class="badge {{ $city->analytics['bounce_rate'] > 60 ? 'bg-danger' : 'bg-success' }} text-white">
                                        {{ number_format($city->analytics['bounce_rate'], 1) }}%
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.cities.show', $city) }}" class="btn btn-sm btn-info" title="عرض">
                                        <i class="fas fa-eye text-white"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center py-4">
                                    <i class="fas fa-city fa-3x text-muted mb-3"></i>
                                    <p class="text-muted">لا توجد بيانات</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Charts Section -->
    <div class="row">
        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">أفضل 10 مدن حسب المشاهدات</h6>
                </div>
                <div class="card-body">
                    <canvas id="viewsChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">توزيع المتاجر حسب المدينة</h6>
                </div>
                <div class="card-body">
                    <canvas id="shopsChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    #dataTable tbody td {
        color: #000 !important;
        vertical-align: middle;
    }
    
    .badge {
        font-size: 0.85rem;
        padding: 0.35em 0.65em;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
    // City comparison data
    const cityData = @json($cityComparison);

    // Views Chart
    const viewsCtx = document.getElementById('viewsChart').getContext('2d');
    new Chart(viewsCtx, {
        type: 'bar',
        data: {
            labels: cityData.map(city => city.name),
            datasets: [{
                label: 'المشاهدات',
                data: cityData.map(city => city.views),
                backgroundColor: 'rgba(78, 115, 223, 0.8)',
                borderColor: 'rgba(78, 115, 223, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

    // Shops Chart
    const shopsCtx = document.getElementById('shopsChart').getContext('2d');
    new Chart(shopsCtx, {
        type: 'doughnut',
        data: {
            labels: cityData.map(city => city.name),
            datasets: [{
                data: cityData.map(city => city.shops),
                backgroundColor: [
                    'rgba(78, 115, 223, 0.8)',
                    'rgba(28, 200, 138, 0.8)',
                    'rgba(54, 185, 204, 0.8)',
                    'rgba(246, 194, 62, 0.8)',
                    'rgba(231, 74, 59, 0.8)',
                    'rgba(133, 135, 150, 0.8)',
                    'rgba(255, 159, 64, 0.8)',
                    'rgba(153, 102, 255, 0.8)',
                    'rgba(255, 99, 132, 0.8)',
                    'rgba(75, 192, 192, 0.8)'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false
        }
    });
</script>
@endpush
@endsection
