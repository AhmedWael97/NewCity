@extends('layouts.app')

@section('title', 'تحليلات الخدمة - ' . $service->title)

@push('styles')
<style>
    .analytics-card {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        color: white;
        border-radius: 15px;
        padding: 1.5rem;
        margin-bottom: 1rem;
    }
    
    .analytics-number {
        font-size: 2.5rem;
        font-weight: bold;
        margin-bottom: 0.5rem;
    }
    
    .analytics-chart {
        background: white;
        border-radius: 12px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        margin-bottom: 2rem;
    }
    
    .metric-comparison {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin-top: 0.5rem;
    }
    
    .metric-trend {
        padding: 0.25rem 0.5rem;
        border-radius: 12px;
        font-size: 0.875rem;
        font-weight: 600;
    }
    
    .trend-up {
        background: #d4edda;
        color: #155724;
    }
    
    .trend-down {
        background: #f8d7da;
        color: #721c24;
    }
    
    .trend-neutral {
        background: #fff3cd;
        color: #856404;
    }
    
    .quick-stats {
        background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
        color: white;
        border-radius: 15px;
        padding: 1.5rem;
    }
    
    .performance-indicator {
        text-align: center;
        padding: 1rem;
        border-radius: 8px;
        background: #f8f9fa;
        margin-bottom: 1rem;
    }
    
    .performance-score {
        font-size: 2rem;
        font-weight: bold;
        color: #28a745;
    }
</style>
@endpush

@section('content')
<div class="container py-4">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="{{ route('user.services.index') }}">خدماتي</a></li>
                    <li class="breadcrumb-item"><a href="{{ route('user.services.show', $service) }}">{{ Str::limit($service->title, 30) }}</a></li>
                    <li class="breadcrumb-item active">التحليلات</li>
                </ol>
            </nav>
            <h1 class="h3 mb-1">تحليلات الخدمة</h1>
            <p class="text-muted">{{ $service->title }}</p>
        </div>
        <div>
            <a href="{{ route('user.services.show', $service) }}" class="btn btn-outline-primary">
                <i class="fas fa-arrow-right me-2"></i>عودة للخدمة
            </a>
        </div>
    </div>

    <!-- Key Metrics -->
    <div class="row mb-4">
        <div class="col-md-3 col-6 mb-3">
            <div class="analytics-card">
                <div class="analytics-number">{{ number_format($analytics['total_views']) }}</div>
                <div class="h6 mb-0">إجمالي المشاهدات</div>
                <div class="metric-comparison">
                    <span class="metric-trend trend-up">
                        <i class="fas fa-arrow-up"></i> +12%
                    </span>
                    <small>مقارنة بالشهر الماضي</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-6 mb-3">
            <div class="analytics-card" style="background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);">
                <div class="analytics-number">{{ number_format($analytics['total_contacts']) }}</div>
                <div class="h6 mb-0">إجمالي التواصل</div>
                <div class="metric-comparison">
                    <span class="metric-trend trend-up">
                        <i class="fas fa-arrow-up"></i> +8%
                    </span>
                    <small>مقارنة بالشهر الماضي</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-6 mb-3">
            <div class="analytics-card" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="analytics-number">{{ number_format($analytics['this_month_views']) }}</div>
                <div class="h6 mb-0">مشاهدات هذا الشهر</div>
                <div class="metric-comparison">
                    <span class="metric-trend trend-neutral">
                        <i class="fas fa-minus"></i> 0%
                    </span>
                    <small>مقارنة بالأسبوع الماضي</small>
                </div>
            </div>
        </div>
        
        <div class="col-md-3 col-6 mb-3">
            <div class="analytics-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="analytics-number">{{ number_format($analytics['this_month_contacts']) }}</div>
                <div class="h6 mb-0">تواصل هذا الشهر</div>
                <div class="metric-comparison">
                    <span class="metric-trend trend-up">
                        <i class="fas fa-arrow-up"></i> +15%
                    </span>
                    <small>مقارنة بالأسبوع الماضي</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Performance Score -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="performance-indicator">
                <h5 class="mb-3">نقاط الأداء</h5>
                <div class="performance-score">{{ $service->average_rating ? number_format($service->average_rating * 20, 0) : 0 }}</div>
                <p class="text-muted mb-0">من 100</p>
                @if($service->average_rating > 0)
                    <div class="text-warning mt-2">
                        @for($i = 1; $i <= 5; $i++)
                            @if($i <= $service->average_rating)
                                <i class="fas fa-star"></i>
                            @else
                                <i class="far fa-star"></i>
                            @endif
                        @endfor
                    </div>
                    <small class="text-muted">{{ $service->total_reviews }} تقييم</small>
                @endif
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="performance-indicator">
                <h5 class="mb-3">معدل التحويل</h5>
                <div class="performance-score text-info">
                    {{ $analytics['total_views'] > 0 ? number_format(($analytics['total_contacts'] / $analytics['total_views']) * 100, 1) : 0 }}%
                </div>
                <p class="text-muted mb-0">من المشاهدات إلى التواصل</p>
            </div>
        </div>
        
        <div class="col-md-4">
            <div class="performance-indicator">
                <h5 class="mb-3">النشاط</h5>
                <div class="performance-score text-warning">
                    {{ $service->is_active ? 'نشط' : 'معطل' }}
                </div>
                <p class="text-muted mb-0">حالة الخدمة</p>
                @if($service->is_subscription_active)
                    <small class="text-success">
                        <i class="fas fa-check-circle"></i> الاشتراك نشط
                    </small>
                @else
                    <small class="text-danger">
                        <i class="fas fa-exclamation-circle"></i> الاشتراك منتهي
                    </small>
                @endif
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div class="row">
        <!-- Views and Contacts Chart -->
        <div class="col-lg-8 mb-4">
            <div class="analytics-chart">
                <h5 class="mb-3">
                    <i class="fas fa-chart-line text-primary me-2"></i>
                    المشاهدات والتواصل (آخر 30 يوم)
                </h5>
                <canvas id="viewsContactsChart" height="100"></canvas>
            </div>
        </div>
        
        <!-- Quick Stats -->
        <div class="col-lg-4 mb-4">
            <div class="quick-stats">
                <h5 class="mb-3">
                    <i class="fas fa-tachometer-alt me-2"></i>
                    إحصائيات سريعة
                </h5>
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>متوسط المشاهدات اليومية</span>
                    <strong>{{ number_format($analytics['total_views'] / 30, 1) }}</strong>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>متوسط التواصل اليومي</span>
                    <strong>{{ number_format($analytics['total_contacts'] / 30, 1) }}</strong>
                </div>
                
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <span>أفضل يوم</span>
                    <strong>{{ $analytics['daily_data']->sortByDesc('total')->first()['date'] ?? 'لا يوجد' }}</strong>
                </div>
                
                <div class="d-flex justify-content-between align-items-center">
                    <span>المرتبة في الفئة</span>
                    <strong>#3</strong>
                </div>
            </div>
        </div>
    </div>

    <!-- Detailed Analytics Table -->
    <div class="row">
        <div class="col-12">
            <div class="analytics-chart">
                <h5 class="mb-3">
                    <i class="fas fa-table text-primary me-2"></i>
                    البيانات التفصيلية (آخر 7 أيام)
                </h5>
                
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead class="table-dark">
                            <tr>
                                <th>التاريخ</th>
                                <th>المشاهدات</th>
                                <th>التواصل</th>
                                <th>معدل التحويل</th>
                                <th>التقييم</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($analytics['daily_data']->take(7) as $date => $data)
                                <tr>
                                    <td>{{ \Carbon\Carbon::parse($date)->format('Y-m-d') }}</td>
                                    <td>
                                        <span class="badge bg-primary">
                                            {{ $data->where('metric_type', 'view')->sum('total') }}
                                        </span>
                                    </td>
                                    <td>
                                        <span class="badge bg-success">
                                            {{ $data->where('metric_type', 'contact')->sum('total') }}
                                        </span>
                                    </td>
                                    <td>
                                        @php
                                            $views = $data->where('metric_type', 'view')->sum('total');
                                            $contacts = $data->where('metric_type', 'contact')->sum('total');
                                            $conversion = $views > 0 ? ($contacts / $views) * 100 : 0;
                                        @endphp
                                        <span class="badge bg-{{ $conversion > 5 ? 'success' : ($conversion > 2 ? 'warning' : 'secondary') }}">
                                            {{ number_format($conversion, 1) }}%
                                        </span>
                                    </td>
                                    <td>
                                        <div class="text-warning">
                                            @for($i = 1; $i <= 5; $i++)
                                                @if($i <= $service->average_rating)
                                                    <i class="fas fa-star small"></i>
                                                @else
                                                    <i class="far fa-star small"></i>
                                                @endif
                                            @endfor
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Tips for Improvement -->
    <div class="row mt-4">
        <div class="col-12">
            <div class="card border-info">
                <div class="card-header bg-info text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-lightbulb me-2"></i>
                        نصائح لتحسين الأداء
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-4">
                            <h6 class="text-info">زيادة المشاهدات</h6>
                            <ul class="list-unstyled small">
                                <li><i class="fas fa-check text-success me-2"></i>أضف صور عالية الجودة</li>
                                <li><i class="fas fa-check text-success me-2"></i>حسّن وصف الخدمة</li>
                                <li><i class="fas fa-check text-success me-2"></i>استخدم كلمات مفتاحية</li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-info">زيادة التواصل</h6>
                            <ul class="list-unstyled small">
                                <li><i class="fas fa-check text-success me-2"></i>أضف رقم واتساب</li>
                                <li><i class="fas fa-check text-success me-2"></i>حدد أوقات العمل بوضوح</li>
                                <li><i class="fas fa-check text-success me-2"></i>قدم عروض خاصة</li>
                            </ul>
                        </div>
                        <div class="col-md-4">
                            <h6 class="text-info">تحسين التقييم</h6>
                            <ul class="list-unstyled small">
                                <li><i class="fas fa-check text-success me-2"></i>قدم خدمة ممتازة</li>
                                <li><i class="fas fa-check text-success me-2"></i>تواصل بسرعة مع العملاء</li>
                                <li><i class="fas fa-check text-success me-2"></i>اطلب من العملاء التقييم</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Views and Contacts Chart
    const ctx = document.getElementById('viewsContactsChart').getContext('2d');
    
    const chartData = @json($chartData);
    const dates = Object.keys(chartData);
    const viewsData = dates.map(date => chartData[date].views);
    const contactsData = dates.map(date => chartData[date].contacts);
    
    new Chart(ctx, {
        type: 'line',
        data: {
            labels: dates.map(date => {
                const d = new Date(date);
                return d.getDate() + '/' + (d.getMonth() + 1);
            }),
            datasets: [{
                label: 'المشاهدات',
                data: viewsData,
                borderColor: '#007bff',
                backgroundColor: 'rgba(0, 123, 255, 0.1)',
                tension: 0.4,
                fill: true
            }, {
                label: 'التواصل',
                data: contactsData,
                borderColor: '#28a745',
                backgroundColor: 'rgba(40, 167, 69, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            plugins: {
                title: {
                    display: false
                },
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });
    
    // Auto-refresh every 5 minutes
    setInterval(function() {
        location.reload();
    }, 300000);
});
</script>
@endpush
