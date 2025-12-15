@extends('layouts.admin')

@section('title', 'تحليلات API')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">تحليلات API</h1>
            <p class="text-muted mb-0">مراقبة شاملة لجميع طلبات API</p>
        </div>
        <div>
            <div class="btn-group" role="group">
                <a href="{{ route('admin.api-analytics.index', ['days' => 1]) }}" class="btn btn-sm {{ $days == 1 ? 'btn-primary' : 'btn-outline-primary' }}">اليوم</a>
                <a href="{{ route('admin.api-analytics.index', ['days' => 7]) }}" class="btn btn-sm {{ $days == 7 ? 'btn-primary' : 'btn-outline-primary' }}">7 أيام</a>
                <a href="{{ route('admin.api-analytics.index', ['days' => 30]) }}" class="btn btn-sm {{ $days == 30 ? 'btn-primary' : 'btn-outline-primary' }}">30 يوم</a>
                <a href="{{ route('admin.api-analytics.index', ['days' => 90]) }}" class="btn btn-sm {{ $days == 90 ? 'btn-primary' : 'btn-outline-primary' }}">90 يوم</a>
            </div>
            <a href="{{ route('admin.api-analytics.export', ['days' => $days]) }}" class="btn btn-sm btn-success">
                <i class="fas fa-download"></i> تصدير CSV
            </a>
            <a href="{{ route('admin.analytics.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="fas fa-chart-line"></i> تحليلات الموقع
            </a>
        </div>
    </div>

    <!-- Overview Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">إجمالي الطلبات</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($totalRequests) }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exchange-alt fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">طلبات ناجحة</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($successfulRequests) }}</div>
                            <div class="text-xs text-muted">{{ $totalRequests > 0 ? number_format(($successfulRequests / $totalRequests) * 100, 1) : 0 }}%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-danger shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">طلبات فاشلة</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($failedRequests) }}</div>
                            <div class="text-xs text-muted">{{ $totalRequests > 0 ? number_format(($failedRequests / $totalRequests) * 100, 1) : 0 }}%</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-exclamation-circle fa-2x text-gray-300"></i>
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
                            <div class="text-xs font-weight-bold text-info text-uppercase mb-1">متوسط الوقت</div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($avgResponseTime, 0) }}ms</div>
                            <div class="text-xs text-muted">{{ $uniqueUsers }} مستخدم فريد</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row mb-4">
        <!-- Requests Over Time -->
        <div class="col-lg-8 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">الطلبات عبر الزمن</h6>
                </div>
                <div class="card-body">
                    <canvas id="requestsChart" height="80"></canvas>
                </div>
            </div>
        </div>

        <!-- Status Codes Distribution -->
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">توزيع حالات الاستجابة</h6>
                </div>
                <div class="card-body">
                    <canvas id="statusChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Action & Resource Types -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">أنواع الإجراءات</h6>
                </div>
                <div class="card-body">
                    <canvas id="actionChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">أنواع الموارد</h6>
                </div>
                <div class="card-body">
                    <canvas id="resourceChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Endpoints -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-primary">أكثر النقاط استخداماً</h6>
                    <span class="badge badge-primary">أعلى 20</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-sm table-hover">
                            <thead class="thead-light sticky-top">
                                <tr>
                                    <th>النقطة</th>
                                    <th>الطريقة</th>
                                    <th class="text-center">العدد</th>
                                    <th class="text-center">متوسط الوقت</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($topEndpoints as $endpoint)
                                <tr>
                                    <td><code class="text-xs">{{ Str::limit($endpoint->endpoint, 40) }}</code></td>
                                    <td><span class="badge badge-{{ $endpoint->method == 'GET' ? 'info' : ($endpoint->method == 'POST' ? 'success' : 'warning') }}">{{ $endpoint->method }}</span></td>
                                    <td class="text-center">{{ number_format($endpoint->count) }}</td>
                                    <td class="text-center">{{ number_format($endpoint->avg_time, 0) }}ms</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">لا توجد بيانات</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Slowest Endpoints -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-warning">أبطأ النقاط</h6>
                    <span class="badge badge-warning">أعلى 10</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-sm table-hover">
                            <thead class="thead-light sticky-top">
                                <tr>
                                    <th>النقطة</th>
                                    <th>الطريقة</th>
                                    <th class="text-center">العدد</th>
                                    <th class="text-center">متوسط الوقت</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($slowestEndpoints as $endpoint)
                                <tr class="{{ $endpoint->avg_time > 1000 ? 'table-danger' : ($endpoint->avg_time > 500 ? 'table-warning' : '') }}">
                                    <td><code class="text-xs">{{ Str::limit($endpoint->endpoint, 40) }}</code></td>
                                    <td><span class="badge badge-{{ $endpoint->method == 'GET' ? 'info' : ($endpoint->method == 'POST' ? 'success' : 'warning') }}">{{ $endpoint->method }}</span></td>
                                    <td class="text-center">{{ number_format($endpoint->count) }}</td>
                                    <td class="text-center"><strong>{{ number_format($endpoint->avg_time, 0) }}ms</strong></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="text-center text-muted">لا توجد بيانات</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Failed Endpoints & Errors -->
    <div class="row mb-4">
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3 d-flex justify-content-between align-items-center">
                    <h6 class="m-0 font-weight-bold text-danger">النقاط الفاشلة</h6>
                    <span class="badge badge-danger">أعلى 10</span>
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-sm table-hover">
                            <thead class="thead-light sticky-top">
                                <tr>
                                    <th>النقطة</th>
                                    <th>الطريقة</th>
                                    <th class="text-center">عدد الأخطاء</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($failedEndpoints as $endpoint)
                                <tr>
                                    <td><code class="text-xs">{{ Str::limit($endpoint->endpoint, 45) }}</code></td>
                                    <td><span class="badge badge-{{ $endpoint->method == 'GET' ? 'info' : ($endpoint->method == 'POST' ? 'success' : 'warning') }}">{{ $endpoint->method }}</span></td>
                                    <td class="text-center"><span class="badge badge-danger">{{ number_format($endpoint->count) }}</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-success">لا توجد أخطاء! 🎉</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Common Errors -->
        <div class="col-lg-6 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-danger">الأخطاء الشائعة</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive" style="max-height: 400px; overflow-y: auto;">
                        <table class="table table-sm table-hover">
                            <thead class="thead-light sticky-top">
                                <tr>
                                    <th>رسالة الخطأ</th>
                                    <th>النقطة</th>
                                    <th class="text-center">العدد</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($commonErrors as $error)
                                <tr>
                                    <td><small>{{ Str::limit($error->error_message, 50) }}</small></td>
                                    <td><code class="text-xs">{{ Str::limit($error->endpoint, 30) }}</code></td>
                                    <td class="text-center"><span class="badge badge-danger">{{ $error->count }}</span></td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="3" class="text-center text-success">لا توجد أخطاء! 🎉</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Device Types & Top Users -->
    <div class="row mb-4">
        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">أنواع الأجهزة</h6>
                </div>
                <div class="card-body">
                    <canvas id="deviceChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">طرق HTTP</h6>
                </div>
                <div class="card-body">
                    <canvas id="methodChart"></canvas>
                </div>
            </div>
        </div>

        <div class="col-lg-4 mb-4">
            <div class="card shadow">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">أكثر المستخدمين نشاطاً</h6>
                </div>
                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                    @forelse($topUsers as $userRequest)
                        @if($userRequest->user)
                        <div class="d-flex justify-content-between align-items-center mb-2 pb-2 border-bottom">
                            <div>
                                <strong>{{ $userRequest->user->name }}</strong>
                                <br><small class="text-muted">{{ $userRequest->user->email }}</small>
                            </div>
                            <span class="badge badge-primary">{{ number_format($userRequest->count) }}</span>
                        </div>
                        @endif
                    @empty
                        <p class="text-muted text-center">لا توجد بيانات مستخدمين</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
// Requests Over Time Chart
const requestsCtx = document.getElementById('requestsChart').getContext('2d');
new Chart(requestsCtx, {
    type: 'line',
    data: {
        labels: {!! json_encode($requestsOverTime->pluck('date')->map(fn($d) => \Carbon\Carbon::parse($d)->format('M d'))) !!},
        datasets: [{
            label: 'إجمالي الطلبات',
            data: {!! json_encode($requestsOverTime->pluck('total')) !!},
            borderColor: 'rgb(75, 192, 192)',
            backgroundColor: 'rgba(75, 192, 192, 0.1)',
            tension: 0.4
        }, {
            label: 'ناجحة',
            data: {!! json_encode($requestsOverTime->pluck('successful')) !!},
            borderColor: 'rgb(75, 192, 75)',
            backgroundColor: 'rgba(75, 192, 75, 0.1)',
            tension: 0.4
        }, {
            label: 'فاشلة',
            data: {!! json_encode($requestsOverTime->pluck('failed')) !!},
            borderColor: 'rgb(255, 99, 132)',
            backgroundColor: 'rgba(255, 99, 132, 0.1)',
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: true,
                position: 'top'
            }
        }
    }
});

// Status Codes Chart
const statusCtx = document.getElementById('statusChart').getContext('2d');
new Chart(statusCtx, {
    type: 'doughnut',
    data: {
        labels: {!! json_encode($statusCodes->keys()) !!},
        datasets: [{
            data: {!! json_encode($statusCodes->values()) !!},
            backgroundColor: ['#28a745', '#ffc107', '#dc3545', '#6c757d']
        }]
    }
});

// Action Types Chart
const actionCtx = document.getElementById('actionChart').getContext('2d');
new Chart(actionCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($actionTypes->pluck('action_type')) !!},
        datasets: [{
            label: 'عدد الطلبات',
            data: {!! json_encode($actionTypes->pluck('count')) !!},
            backgroundColor: 'rgba(54, 162, 235, 0.5)',
            borderColor: 'rgb(54, 162, 235)',
            borderWidth: 1
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: true
    }
});

// Resource Types Chart
const resourceCtx = document.getElementById('resourceChart').getContext('2d');
new Chart(resourceCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($resourceTypes->pluck('resource_type')) !!},
        datasets: [{
            label: 'عدد الطلبات',
            data: {!! json_encode($resourceTypes->pluck('count')) !!},
            backgroundColor: 'rgba(255, 159, 64, 0.5)',
            borderColor: 'rgb(255, 159, 64)',
            borderWidth: 1
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true,
        maintainAspectRatio: true
    }
});

// Device Types Chart
const deviceCtx = document.getElementById('deviceChart').getContext('2d');
new Chart(deviceCtx, {
    type: 'pie',
    data: {
        labels: {!! json_encode($deviceTypes->pluck('device_type')) !!},
        datasets: [{
            data: {!! json_encode($deviceTypes->pluck('count')) !!},
            backgroundColor: ['#007bff', '#28a745', '#ffc107', '#dc3545']
        }]
    }
});

// HTTP Methods Chart
const methodCtx = document.getElementById('methodChart').getContext('2d');
new Chart(methodCtx, {
    type: 'pie',
    data: {
        labels: {!! json_encode($methods->pluck('method')) !!},
        datasets: [{
            data: {!! json_encode($methods->pluck('count')) !!},
            backgroundColor: ['#17a2b8', '#28a745', '#ffc107', '#dc3545']
        }]
    }
});
</script>
@endpush
@endsection
