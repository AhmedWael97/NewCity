@extends('layouts.admin')

@section('title', 'زيارات الموقع')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-globe text-danger"></i> زيارات الموقع
            </h1>
            <p class="text-muted small mb-0">تتبع وتحليل زوار موقعك الإلكتروني</p>
        </div>
        <div>
            <a href="{{ route('admin.analytics.index') }}" class="btn btn-outline-secondary">
                <i class="fas fa-arrow-right"></i> العودة للتحليلات
            </a>
            <button class="btn btn-primary" onclick="location.reload()">
                <i class="fas fa-sync-alt"></i> تحديث البيانات
            </button>
        </div>
    </div>

    <!-- Real-time & Today Stats -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">الزوار الآن</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($realTimeVisitors) }}</div>
                            <div class="text-xs text-muted mt-1"><i class="fas fa-circle text-danger pulse-animation"></i> متصلون الآن (آخر 5 دقائق)</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
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
                            <div class="text-xs text-muted mt-1">{{ number_format($todayStats['total_visits']) }} زيارة</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-calendar-day fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">معاينات الصفحات اليوم</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($todayStats['total_page_views']) }}</div>
                            <div class="text-xs text-muted mt-1">{{ number_format($todayStats['avg_pages_per_visit'], 1) }} صفحة/زيارة</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-file-alt fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">معدل الارتداد اليوم</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($todayStats['bounce_rate'], 1) }}%</div>
                            <div class="text-xs text-muted mt-1">متوسط المدة: {{ gmdate('i:s', $todayStats['avg_duration']) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-percentage fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Period Comparison -->
    <div class="row mb-4">
        <div class="col-lg-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">إحصائيات آخر 7 أيام</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-xs font-weight-bold">إجمالي الزيارات</span>
                            <span class="text-xs font-weight-bold text-primary">{{ number_format($weekStats['total_visits']) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-xs">زوار فريدون</span>
                            <span class="text-xs">{{ number_format($weekStats['unique_visitors']) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-xs">معاينات الصفحات</span>
                            <span class="text-xs">{{ number_format($weekStats['total_page_views']) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-xs">متوسط الصفحات/زيارة</span>
                            <span class="text-xs">{{ number_format($weekStats['avg_pages_per_visit'], 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-xs">معدل الارتداد</span>
                            <span class="text-xs text-{{ $weekStats['bounce_rate'] > 60 ? 'danger' : 'success' }}">{{ number_format($weekStats['bounce_rate'], 1) }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">إحصائيات آخر 30 يوم</h6>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-xs font-weight-bold">إجمالي الزيارات</span>
                            <span class="text-xs font-weight-bold text-success">{{ number_format($monthStats['total_visits']) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-xs">زوار فريدون</span>
                            <span class="text-xs">{{ number_format($monthStats['unique_visitors']) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-xs">معاينات الصفحات</span>
                            <span class="text-xs">{{ number_format($monthStats['total_page_views']) }}</span>
                        </div>
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-xs">متوسط الصفحات/زيارة</span>
                            <span class="text-xs">{{ number_format($monthStats['avg_pages_per_visit'], 2) }}</span>
                        </div>
                        <div class="d-flex justify-content-between">
                            <span class="text-xs">معدل الارتداد</span>
                            <span class="text-xs text-{{ $monthStats['bounce_rate'] > 60 ? 'danger' : 'success' }}">{{ number_format($monthStats['bounce_rate'], 1) }}%</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">مقارنة بالفترة السابقة</h6>
                </div>
                <div class="card-body">
                    @foreach(['total_visits' => 'الزيارات', 'unique_visitors' => 'الزوار الفريدون', 'total_page_views' => 'معاينات الصفحات', 'bounce_rate' => 'معدل الارتداد'] as $key => $label)
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <span class="text-xs">{{ $label }}</span>
                            <span class="text-xs">
                                @if($comparison[$key]['trend'] == 'up')
                                    <i class="fas fa-arrow-up text-success"></i>
                                    <span class="text-success">+{{ number_format($comparison[$key]['change'], 1) }}%</span>
                                @elseif($comparison[$key]['trend'] == 'down')
                                    <i class="fas fa-arrow-down text-danger"></i>
                                    <span class="text-danger">{{ number_format($comparison[$key]['change'], 1) }}%</span>
                                @else
                                    <i class="fas fa-minus text-muted"></i>
                                    <span class="text-muted">0%</span>
                                @endif
                            </span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>

    <!-- Daily Visits Chart -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">الزيارات اليومية (آخر 30 يوم)</h6>
                </div>
                <div class="card-body">
                    <canvas id="dailyVisitsChart" style="height: 300px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Hourly Distribution & Device Breakdown -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">توزيع الزيارات حسب الساعة</h6>
                </div>
                <div class="card-body">
                    <canvas id="hourlyChart" style="height: 250px;"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-success">توزيع الأجهزة</h6>
                </div>
                <div class="card-body">
                    <canvas id="deviceChart" style="height: 250px;"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Pages & Referrers -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-info">أكثر الصفحات زيارة</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>الصفحة</th>
                                    <th class="text-center">الزيارات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topLandingPages as $index => $page)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td class="text-truncate" style="max-width: 300px;" title="{{ $page['landing_page'] }}">
                                            {{ $page['landing_page'] }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-primary">{{ number_format($page['visits']) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">لا توجد بيانات</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow h-100">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-warning">مصادر الزيارات</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>المصدر</th>
                                    <th class="text-center">الزيارات</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topReferrers as $index => $referrer)
                                    <tr>
                                        <td>{{ $index + 1 }}</td>
                                        <td class="text-truncate" style="max-width: 300px;" title="{{ $referrer['referrer'] }}">
                                            {{ $referrer['referrer'] }}
                                        </td>
                                        <td class="text-center">
                                            <span class="badge bg-warning text-dark">{{ number_format($referrer['count']) }}</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted">لا توجد بيانات</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Browser Breakdown -->
    <div class="row mb-4">
        <div class="col-lg-12">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">المتصفحات المستخدمة</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($browserBreakdown as $browser)
                            <div class="col-md-3 mb-3">
                                <div class="card border-left-primary">
                                    <div class="card-body py-2">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <div>
                                                <div class="text-xs font-weight-bold text-uppercase">{{ $browser['browser'] }}</div>
                                            </div>
                                            <div class="text-right">
                                                <div class="h5 mb-0 font-weight-bold">{{ number_format($browser['count']) }}</div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Visits -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">الزيارات الأخيرة (آخر 50 زيارة)</h6>
                    <small class="text-muted">زيارات حقيقية فقط - تم استبعاد الروبوتات والصفحات الإدارية</small>
                </div>
                <div class="card-body">
                    @if($recentVisits->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover table-sm">
                                <thead class="table-light">
                                    <tr>
                                        <th>الوقت</th>
                                        <th>نوع الزائر</th>
                                        <th>الموقع</th>
                                        <th>الجهاز</th>
                                        <th>المتصفح</th>
                                        <th>الصفحة المقصودة</th>
                                        <th class="text-center">التفاعل</th>
                                        <th class="text-center">النشاط</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($recentVisits as $visit)
                                        <tr class="{{ $visit->is_bounce ? 'table-warning' : '' }}">
                                            <td class="text-nowrap">
                                                <small>
                                                    <strong>{{ $visit->created_at->format('H:i') }}</strong><br>
                                                    <span class="text-muted">{{ $visit->created_at->diffForHumans() }}</span>
                                                </small>
                                            </td>
                                            <td>
                                                @if($visit->user)
                                                    <span class="badge bg-success"><i class="fas fa-user"></i> {{ $visit->user->name }}</span>
                                                @else
                                                    <span class="badge bg-secondary"><i class="fas fa-user-secret"></i> ضيف</span>
                                                @endif
                                                @if($visit->is_unique_visit)
                                                    <span class="badge bg-info" title="زائر جديد اليوم"><i class="fas fa-star"></i> جديد</span>
                                                @endif
                                            </td>
                                            <td class="text-nowrap">
                                                <small>
                                                    <i class="fas fa-map-marker-alt text-muted"></i> {{ $visit->ip_address }}<br>
                                                    @if($visit->country || $visit->city)
                                                        <span class="text-muted">{{ $visit->city ?? '' }}{{ $visit->country ? ', ' . $visit->country : '' }}</span>
                                                    @endif
                                                </small>
                                            </td>
                                            <td>
                                                <i class="fas fa-{{ $visit->device_type == 'mobile' ? 'mobile-alt text-primary' : ($visit->device_type == 'tablet' ? 'tablet-alt text-info' : 'desktop text-success') }}"></i>
                                                <small>{{ ucfirst($visit->device_type) }}</small>
                                            </td>
                                            <td><small>{{ $visit->browser }}</small></td>
                                            <td class="text-truncate" style="max-width: 250px;">
                                                <small title="{{ $visit->landing_page }}">
                                                    <i class="fas fa-external-link-alt text-muted"></i> 
                                                    {{ str_replace(url('/'), '', $visit->landing_page) ?: '/' }}
                                                </small>
                                                @if($visit->referrer && !str_contains($visit->referrer, url('/')))
                                                    <br><small class="text-info" title="{{ $visit->referrer }}"><i class="fas fa-arrow-left"></i> من مصدر خارجي</small>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                @if($visit->is_bounce)
                                                    <span class="badge bg-warning text-dark" title="ارتداد - صفحة واحدة فقط">
                                                        <i class="fas fa-sign-out-alt"></i> ارتداد
                                                    </span>
                                                @else
                                                    <span class="badge bg-success" title="تصفح عدة صفحات">
                                                        <i class="fas fa-check"></i> تفاعل
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <small>
                                                    <strong>{{ $visit->pages_viewed }}</strong> صفحة<br>
                                                    <span class="text-muted">{{ gmdate('i:s', $visit->duration_seconds) }} دقيقة</span>
                                                </small>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="mt-3">
                            <div class="row">
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h5 class="text-success">{{ $recentVisits->where('is_bounce', false)->count() }}</h5>
                                        <small class="text-muted">زيارات متفاعلة</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h5 class="text-warning">{{ $recentVisits->where('is_bounce', true)->count() }}</h5>
                                        <small class="text-muted">زيارات منطوية (ارتداد)</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h5 class="text-info">{{ $recentVisits->where('is_unique_visit', true)->count() }}</h5>
                                        <small class="text-muted">زوار جدد</small>
                                    </div>
                                </div>
                                <div class="col-md-3">
                                    <div class="text-center">
                                        <h5 class="text-primary">{{ number_format($recentVisits->avg('pages_viewed'), 1) }}</h5>
                                        <small class="text-muted">متوسط الصفحات/زيارة</small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle fa-3x mb-3"></i>
                            <h5>لا توجد بيانات للزيارات حتى الآن</h5>
                            <p class="mb-0">ستظهر زيارات الموقع هنا تلقائياً عندما يبدأ المستخدمون بزيارة موقعك.</p>
                            <small class="text-muted">تأكد من أن التتبع مفعّل ولا يتم حظره بواسطة أدوات حظر الإعلانات.</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .pulse-animation {
        animation: pulse 2s infinite;
    }
    
    @keyframes pulse {
        0% {
            opacity: 1;
        }
        50% {
            opacity: 0.5;
        }
        100% {
            opacity: 1;
        }
    }
</style>

@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if data exists
    const dailyStats = {!! json_encode($dailyStats) !!};
    const hourlyDistribution = {!! json_encode($hourlyDistribution) !!};
    const deviceBreakdown = {!! json_encode($deviceBreakdown) !!};

    console.log('Daily Stats:', dailyStats);
    console.log('Hourly Distribution:', hourlyDistribution);
    console.log('Device Breakdown:', deviceBreakdown);

    // Daily Visits Chart
    if (dailyStats && dailyStats.length > 0) {
        const dailyVisitsCtx = document.getElementById('dailyVisitsChart');
        if (dailyVisitsCtx) {
            new Chart(dailyVisitsCtx.getContext('2d'), {
    type: 'line',
    data: {
        labels: {!! json_encode(array_column($dailyStats, 'date')) !!},
        datasets: [
            {
                label: 'إجمالي الزيارات',
                data: {!! json_encode(array_column($dailyStats, 'total_visits')) !!},
                borderColor: 'rgb(78, 115, 223)',
                backgroundColor: 'rgba(78, 115, 223, 0.1)',
                tension: 0.4
            },
            {
                label: 'الزوار الفريدون',
                data: {!! json_encode(array_column($dailyStats, 'unique_visitors')) !!},
                borderColor: 'rgb(28, 200, 138)',
                backgroundColor: 'rgba(28, 200, 138, 0.1)',
                tension: 0.4
            },
            {
                label: 'معاينات الصفحات',
                data: {!! json_encode(array_column($dailyStats, 'page_views')) !!},
                borderColor: 'rgb(54, 185, 204)',
                backgroundColor: 'rgba(54, 185, 204, 0.1)',
                tension: 0.4
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
    }
    });
        } else {
            console.error('dailyVisitsChart canvas not found');
        }
    } else {
        console.warn('No daily stats data available');
    }

    // Hourly Distribution Chart
    if (hourlyDistribution && hourlyDistribution.length > 0) {
        const hourlyCtx = document.getElementById('hourlyChart');
        if (hourlyCtx) {
            new Chart(hourlyCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: hourlyDistribution.map(h => h.hour + ':00'),
                    datasets: [{
                        label: 'الزيارات',
                        data: hourlyDistribution.map(h => h.visits),
            backgroundColor: 'rgba(78, 115, 223, 0.8)',
            borderColor: 'rgb(78, 115, 223)',
            borderWidth: 1
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true
            }
        }
            }
            });
        } else {
            console.error('hourlyChart canvas not found');
        }
    } else {
        console.warn('No hourly distribution data available');
    }

    // Device Chart
    if (deviceBreakdown && deviceBreakdown.length > 0) {
        const deviceCtx = document.getElementById('deviceChart');
        if (deviceCtx) {
            new Chart(deviceCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: deviceBreakdown.map(d => d.device_type),
                    datasets: [{
                        data: deviceBreakdown.map(d => d.count),
            backgroundColor: [
                'rgba(78, 115, 223, 0.8)',
                'rgba(28, 200, 138, 0.8)',
                'rgba(54, 185, 204, 0.8)',
                'rgba(246, 194, 62, 0.8)'
            ],
            borderColor: [
                'rgb(78, 115, 223)',
                'rgb(28, 200, 138)',
                'rgb(54, 185, 204)',
                'rgb(246, 194, 62)'
            ],
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                display: true,
                position: 'bottom'
            }
        }
            }
            });
        } else {
            console.error('deviceChart canvas not found');
        }
    } else {
        console.warn('No device breakdown data available');
    }
});

// Auto refresh every 5 minutes
setTimeout(() => {
    location.reload();
}, 300000);
</script>
@endsection
