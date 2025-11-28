@extends('layouts.admin')

@section('title', 'التحليلات المتقدمة')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0 text-gray-800">التحليلات المتقدمة</h1>
        <div>
            <a href="{{ route('admin.analytics.website-visits') }}" class="btn btn-outline-danger">
                <i class="fas fa-globe"></i> زيارات الموقع
            </a>
            <a href="{{ route('admin.analytics.shops') }}" class="btn btn-outline-success">
                <i class="fas fa-store"></i> أداء المتاجر
            </a>
            <a href="{{ route('admin.analytics.cities') }}" class="btn btn-outline-info">
                <i class="fas fa-map-marker-alt"></i> تحليلات المدن
            </a>
            <a href="{{ route('admin.analytics.users') }}" class="btn btn-outline-primary">
                <i class="fas fa-users"></i> سلوك المستخدمين
            </a>
        </div>
    </div>

    <!-- Website Visits Overview -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">الزوار الآن</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($realTimeVisitors) }}</div>
                            <div class="text-xs text-muted mt-1">متصلون الآن</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-circle text-danger fa-2x pulse-animation"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">زوار اليوم</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($todayVisitors) }}</div>
                            <div class="text-xs text-muted mt-1">زائر فريد</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">إجمالي الزيارات</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($websiteStats['total_visits'] ?? 0) }}</div>
                            <div class="text-xs text-muted mt-1">آخر 30 يوم</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-chart-line fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">معدل معاينات الصفحة</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($websiteStats['avg_pages_per_visit'] ?? 0, 1) }}</div>
                            <div class="text-xs text-muted mt-1">صفحة/زيارة</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Analytics Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">الأنشطة اليومية (آخر 30 يوم)</h6>
                </div>
                <div class="card-body">
                    <canvas id="dailyAnalyticsChart" width="100%" height="50"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Metrics -->
    <div class="row mb-4">
        <!-- Top Performing Shops -->
        <div class="col-lg-6">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-success">أفضل المتاجر أداءً</h6>
                    <a href="{{ route('admin.analytics.shops') }}" class="btn btn-success btn-sm">عرض الكل</a>
                </div>
                <div class="card-body">
                    @forelse($topShopsByViews->take(10) as $shopAnalytic)
                        <div class="d-flex align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="avatar-sm rounded-circle bg-success text-white d-flex align-items-center justify-content-center mr-3">
                                {{ $loop->iteration }}
                            </div>
                            <div class="flex-grow-1">
                                <div class="font-weight-bold">{{ $shopAnalytic->shop->name ?? 'متجر محذوف' }}</div>
                                <div class="text-muted small">
                                    {{ $shopAnalytic->shop->city->name_ar ?? 'غير محدد' }} - 
                                    {{ $shopAnalytic->shop->category->name_ar ?? 'غير محدد' }}
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-success font-weight-bold">{{ number_format($shopAnalytic->total_views) }}</div>
                                <div class="text-muted small">مشاهدة</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-chart-line fa-3x mb-3"></i>
                            <p>لا توجد بيانات تحليلية متاحة</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Top Cities by Visitors -->
        <div class="col-lg-6">
            <div class="card shadow h-100">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-info">أكثر المدن زيارة</h6>
                    <a href="{{ route('admin.analytics.cities') }}" class="btn btn-info btn-sm">عرض الكل</a>
                </div>
                <div class="card-body">
                    @forelse($topCitiesByVisitors->take(10) as $cityAnalytic)
                        <div class="d-flex align-items-center py-2 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="avatar-sm rounded-circle bg-info text-white d-flex align-items-center justify-content-center mr-3">
                                {{ $loop->iteration }}
                            </div>
                            <div class="flex-grow-1">
                                <div class="font-weight-bold">{{ $cityAnalytic->city->name_ar ?? 'مدينة محذوفة' }}</div>
                                <div class="text-muted small">
                                    {{ $cityAnalytic->city->governorate ?? 'غير محدد' }}
                                </div>
                            </div>
                            <div class="text-right">
                                <div class="text-info font-weight-bold">{{ number_format($cityAnalytic->unique_visitors) }}</div>
                                <div class="text-muted small">زائر فريد</div>
                            </div>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-map-marked-alt fa-3x mb-3"></i>
                            <p>لا توجد بيانات زيارات متاحة</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Search Terms and Categories -->
    <div class="row mb-4">
        <!-- Popular Search Terms -->
        <div class="col-lg-6">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">أكثر كلمات البحث استخداماً</h6>
                </div>
                <div class="card-body">
                    @forelse($topSearchTerms->take(15) as $searchTerm)
                        <div class="d-flex justify-content-between align-items-center py-1 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="font-weight-bold">{{ $searchTerm->search_term }}</div>
                            <span class="badge bg-warning text-dark">{{ number_format($searchTerm->search_count) }} بحث</span>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-search fa-3x mb-3"></i>
                            <p>لا توجد عمليات بحث مسجلة</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Popular Categories -->
        <div class="col-lg-6">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-purple">الفئات الأكثر شعبية</h6>
                </div>
                <div class="card-body">
                    @forelse($popularCategories as $category)
                        <div class="d-flex justify-content-between align-items-center py-1 {{ !$loop->last ? 'border-bottom' : '' }}">
                            <div class="font-weight-bold">{{ $category->name_ar }}</div>
                            <span class="badge bg-purple">{{ number_format($category->total_views) }} مشاهدة</span>
                        </div>
                    @empty
                        <div class="text-center text-muted py-4">
                            <i class="fas fa-tags fa-3x mb-3"></i>
                            <p>لا توجد بيانات فئات متاحة</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Traffic Sources and Device Types -->
    <div class="row mb-4">
        <!-- Traffic Sources -->
        <div class="col-lg-6">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-dark">مصادر الزيارات</h6>
                </div>
                <div class="card-body">
                    <canvas id="trafficSourcesChart" width="100%" height="200"></canvas>
                </div>
            </div>
        </div>

        <!-- Device Types -->
        <div class="col-lg-6">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-secondary">أنواع الأجهزة</h6>
                </div>
                <div class="card-body">
                    <canvas id="deviceTypesChart" width="100%" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Report Generation -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-dark">إنشاء تقرير مخصص</h6>
                </div>
                <div class="card-body">
                    <form id="reportForm">
                        <div class="row">
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="start_date">تاريخ البداية</label>
                                    <input type="date" class="form-control" id="start_date" name="start_date" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="end_date">تاريخ النهاية</label>
                                    <input type="date" class="form-control" id="end_date" name="end_date" required>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label for="report_type">نوع التقرير</label>
                                    <select class="form-control" id="report_type" name="type" required>
                                        <option value="">اختر نوع التقرير</option>
                                        <option value="overview">نظرة عامة</option>
                                        <option value="shops">تقرير المتاجر</option>
                                        <option value="cities">تقرير المدن</option>
                                        <option value="users">تقرير المستخدمين</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="form-group">
                                    <label>&nbsp;</label>
                                    <button type="submit" class="btn btn-primary btn-block">
                                        <i class="fas fa-file-alt"></i> إنشاء التقرير
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    
                    <div id="reportResult" class="mt-4" style="display: none;">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title">نتائج التقرير</h6>
                                <div id="reportContent"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.avatar-sm {
    width: 32px;
    height: 32px;
    font-size: 0.875rem;
}

.text-purple {
    color: #6f42c1 !important;
}

.badge-purple {
    background-color: #6f42c1;
    color: white;
}

.text-gray-800 {
    color: #5a5c69 !important;
}

.flex-grow-1 {
    flex-grow: 1;
}
</style>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
// Daily Analytics Chart
const dailyAnalyticsCtx = document.getElementById('dailyAnalyticsChart').getContext('2d');
const dailyAnalyticsChart = new Chart(dailyAnalyticsCtx, {
    type: 'line',
    data: {
        labels: @json(collect($dailyAnalytics)->pluck('date')),
        datasets: [{
            label: 'المشاهدات',
            data: @json(collect($dailyAnalytics)->pluck('views')),
            borderColor: '#1cc88a',
            backgroundColor: 'rgba(28, 200, 138, 0.1)',
            tension: 0.3
        }, {
            label: 'عمليات البحث',
            data: @json(collect($dailyAnalytics)->pluck('searches')),
            borderColor: '#36b9cc',
            backgroundColor: 'rgba(54, 185, 204, 0.1)',
            tension: 0.3
        }, {
            label: 'الزوار الفريدون',
            data: @json(collect($dailyAnalytics)->pluck('unique_visitors')),
            borderColor: '#4e73df',
            backgroundColor: 'rgba(78, 115, 223, 0.1)',
            tension: 0.3
        }, {
            label: 'متاجر جديدة',
            data: @json(collect($dailyAnalytics)->pluck('new_shops')),
            borderColor: '#f6c23e',
            backgroundColor: 'rgba(246, 194, 62, 0.1)',
            tension: 0.3
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true
            }
        },
        plugins: {
            legend: {
                position: 'top'
            }
        }
    }
});

// Traffic Sources Chart
const trafficSourcesCtx = document.getElementById('trafficSourcesChart').getContext('2d');
const trafficSourcesChart = new Chart(trafficSourcesCtx, {
    type: 'doughnut',
    data: {
        labels: @json($trafficSources->pluck('source')),
        datasets: [{
            data: @json($trafficSources->pluck('visits')),
            backgroundColor: [
                '#4e73df',
                '#1cc88a',
                '#36b9cc',
                '#f6c23e',
                '#e74a3b',
                '#6c757d'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

// Device Types Chart
const deviceTypesCtx = document.getElementById('deviceTypesChart').getContext('2d');
const deviceTypesChart = new Chart(deviceTypesCtx, {
    type: 'bar',
    data: {
        labels: @json($deviceTypes->pluck('device_type')),
        datasets: [{
            label: 'عدد الزيارات',
            data: @json($deviceTypes->pluck('count')),
            backgroundColor: [
                '#4e73df',
                '#1cc88a',
                '#36b9cc',
                '#f6c23e'
            ]
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});

// Report Form
document.getElementById('reportForm').addEventListener('submit', function(e) {
    e.preventDefault();
    
    const formData = new FormData(this);
    const data = Object.fromEntries(formData);
    
    // Validate dates
    if (new Date(data.start_date) >= new Date(data.end_date)) {
        alert('تاريخ البداية يجب أن يكون قبل تاريخ النهاية');
        return;
    }
    
    // Show loading
    const resultDiv = document.getElementById('reportResult');
    const contentDiv = document.getElementById('reportContent');
    
    resultDiv.style.display = 'block';
    contentDiv.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i><p class="mt-2">جاري إنشاء التقرير...</p></div>';
    
    // Send request
    fetch('{{ route("admin.analytics.reports.generate") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        displayReportResults(data);
    })
    .catch(error => {
        console.error('Error:', error);
        contentDiv.innerHTML = '<div class="alert alert-danger">حدث خطأ أثناء إنشاء التقرير</div>';
    });
});

function displayReportResults(data) {
    const contentDiv = document.getElementById('reportContent');
    let html = '';
    
    if (data.period) {
        html += `<h6>فترة التقرير: ${data.period.start} إلى ${data.period.end}</h6>`;
    }
    
    if (data.metrics) {
        html += '<div class="row">';
        Object.entries(data.metrics).forEach(([key, value]) => {
            const label = getMetricLabel(key);
            html += `
                <div class="col-md-3 mb-2">
                    <div class="card bg-primary text-white">
                        <div class="card-body text-center">
                            <div class="h5">${value}</div>
                            <small>${label}</small>
                        </div>
                    </div>
                </div>
            `;
        });
        html += '</div>';
    }
    
    contentDiv.innerHTML = html;
}

function getMetricLabel(key) {
    const labels = {
        'total_views': 'إجمالي المشاهدات',
        'unique_visitors': 'الزوار الفريدون',
        'new_shops': 'متاجر جديدة',
        'new_users': 'مستخدمون جدد',
        'searches': 'عمليات البحث'
    };
    return labels[key] || key;
}

// Set default dates (last 30 days)
document.getElementById('end_date').valueAsDate = new Date();
const startDate = new Date();
startDate.setDate(startDate.getDate() - 30);
document.getElementById('start_date').valueAsDate = startDate;
</script>
@endpush