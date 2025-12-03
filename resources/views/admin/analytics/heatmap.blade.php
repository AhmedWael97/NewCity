@extends('layouts.admin')

@section('title', 'User Activity Heatmap')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-fire"></i> خريطة نشاط المستخدمين (Heatmap)
        </h1>
        <div>
            <select id="dateRange" class="form-select d-inline-block w-auto">
                <option value="7">آخر 7 أيام</option>
                <option value="30" selected>آخر 30 يوم</option>
                <option value="90">آخر 90 يوم</option>
            </select>
        </div>
    </div>

    <!-- Overview Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <h6 class="card-title">إجمالي النقرات</h6>
                    <h2>{{ number_format($totalClicks) }}</h2>
                    <small>جميع التفاعلات</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <h6 class="card-title">أكثر صفحة</h6>
                    <h2>{{ $topPage->count ?? 0 }}</h2>
                    <small>{{ Str::limit($topPage->page_url ?? 'N/A', 30) }}</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <h6 class="card-title">متوسط العمق</h6>
                    <h2>{{ number_format($avgScrollDepth) }}%</h2>
                    <small>Scroll Depth</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white">
                <div class="card-body">
                    <h6 class="card-title">متوسط الوقت</h6>
                    <h2>{{ gmdate('i:s', $avgTimeOnPage) }}</h2>
                    <small>على الصفحة</small>
                </div>
            </div>
        </div>
    </div>

    <!-- Click Heatmap -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-mouse-pointer"></i> خريطة النقرات الحرارية
                    </h5>
                    <small class="text-muted">أكثر الأماكن التي ينقر عليها المستخدمون</small>
                </div>
                <div class="card-body">
                    <canvas id="clickHeatmap" height="80"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Clicked Elements -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-hand-pointer"></i> أكثر العناصر نقراً
                    </h5>
                    <small class="text-muted">الأزرار والروابط التي ينقر عليها المستخدمون</small>
                </div>
                <div class="card-body">
                    @if($topClickedElements->count() > 0)
                        <div class="table-responsive">
                            <table class="table table-hover">
                                <thead>
                                    <tr>
                                        <th>العنصر</th>
                                        <th>الإجراء</th>
                                        <th>النقرات</th>
                                        <th>النسبة</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($topClickedElements as $element)
                                    <tr>
                                        <td>
                                            @php
                                                $label = $element->event_label ?? 'غير محدد';
                                                // Translate common labels
                                                $translations = [
                                                    'phone_call' => 'زر الاتصال',
                                                    'map_directions' => 'زر الاتجاهات',
                                                    'share' => 'زر المشاركة',
                                                    'favorite' => 'زر المفضلة',
                                                    'view_shop' => 'عرض المتجر',
                                                    'view_product' => 'عرض المنتج',
                                                    'click' => 'نقرة',
                                                    'button' => 'زر',
                                                ];
                                                foreach($translations as $en => $ar) {
                                                    $label = str_replace($en, $ar, $label);
                                                }
                                            @endphp
                                            <strong>{{ Str::limit($label, 40) }}</strong>
                                        </td>
                                        <td>
                                            @php
                                                $action = $element->event_action;
                                                $actionAr = [
                                                    'phone_call' => '☎️ اتصال',
                                                    'map_directions' => '🗺️ اتجاهات',
                                                    'share' => '📤 مشاركة',
                                                    'favorite' => '⭐ مفضلة',
                                                    'click' => '👆 نقرة',
                                                    'view' => '👁️ عرض',
                                                ][$action] ?? $action;
                                            @endphp
                                            <span class="badge bg-primary">{{ $actionAr }}</span>
                                        </td>
                                        <td><strong>{{ number_format($element->clicks) }}</strong></td>
                                        <td>
                                            <div class="progress" style="height: 20px;">
                                                <div class="progress-bar bg-success" 
                                                     style="width: {{ min(100, ($element->clicks / max($totalClicks, 1)) * 100) }}%">
                                                    {{ number_format(($element->clicks / max($totalClicks, 1)) * 100, 1) }}%
                                                </div>
                                            </div>
                                        </td>
                                    </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="fas fa-info-circle fa-2x mb-2"></i>
                            <p class="mb-0"><strong>لا توجد بيانات للنقرات حالياً</strong></p>
                            <small>ستظهر نقرات المستخدمين على الأزرار والعناصر التفاعلية هنا</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-route"></i> مسارات المستخدمين الشائعة
                    </h5>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>المسار</th>
                                    <th>الزيارات</th>
                                    <th>معدل التحويل</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($userJourneys as $journey)
                                <tr>
                                    <td>
                                        <small>{{ Str::limit($journey->path, 50) }}</small>
                                    </td>
                                    <td>{{ number_format($journey->visits) }}</td>
                                    <td>
                                        <span class="badge {{ $journey->conversion_rate > 5 ? 'bg-success' : 'bg-warning' }}">
                                            {{ number_format($journey->conversion_rate, 1) }}%
                                        </span>
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Scroll Depth Analysis -->
    <div class="row mb-4">
        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-arrows-alt-v"></i> تحليل عمق التمرير
                    </h5>
                    <small class="text-muted">كم من الصفحة يقرأ المستخدمون؟</small>
                </div>
                <div class="card-body">
                    @php
                        $totalScrollData = array_sum($scrollDepthDistribution);
                    @endphp
                    @if($totalScrollData > 0)
                        <canvas id="scrollDepthChart" height="250"></canvas>
                        <div class="mt-3">
                            <p class="text-muted text-center mb-0">
                                <strong>متوسط عمق التمرير:</strong> {{ number_format($avgScrollDepth, 1) }}%
                            </p>
                            <small class="text-muted d-block text-center">
                                @if($avgScrollDepth < 40)
                                    ⚠️ المستخدمون يغادرون بسرعة - حاول جعل المحتوى أكثر جاذبية
                                @elseif($avgScrollDepth < 70)
                                    📊 عمق تمرير متوسط - يمكن تحسينه بمحتوى أفضل
                                @else
                                    ✅ ممتاز! المستخدمون يقرؤون معظم المحتوى
                                @endif
                            </small>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="fas fa-arrows-alt-v fa-2x mb-2"></i>
                            <p class="mb-0">لا توجد بيانات لعمق التمرير</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div class="col-md-6">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-clock"></i> توزيع الوقت على الصفحات
                    </h5>
                    <small class="text-muted">كم من الوقت يقضي المستخدمون على صفحاتك؟</small>
                </div>
                <div class="card-body">
                    @php
                        $totalTimeData = array_sum($timeDistribution);
                    @endphp
                    @if($totalTimeData > 0)
                        <canvas id="timeDistributionChart" height="250"></canvas>
                        <div class="mt-3">
                            <p class="text-muted text-center mb-0">
                                <strong>متوسط الوقت على الصفحة:</strong> {{ gmdate('i:s', $avgTimeOnPage) }} دقيقة
                            </p>
                            <small class="text-muted d-block text-center">
                                @if($avgTimeOnPage < 20)
                                    ⚠️ زيارات سريعة جداً - المحتوى قد لا يكون جذاباً
                                @elseif($avgTimeOnPage < 60)
                                    📊 وقت معقول - المستخدمون يتصفحون بسرعة متوسطة
                                @else
                                    ✅ رائع! المستخدمون يقضون وقتاً جيداً على موقعك
                                @endif
                            </small>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="fas fa-clock fa-2x mb-2"></i>
                            <p class="mb-0">لا توجد بيانات للوقت المستغرق</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Page Performance -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-chart-line"></i> أداء الصفحات
                    </h5>
                    <small class="text-muted">تحليل أداء كل صفحة: الوقت المستغرق، عمق التمرير، ومعدل الارتداد</small>
                </div>
                <div class="card-body">
                    @if($pagePerformance->count() > 0)
                        <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>الصفحة</th>
                                    <th>الزيارات</th>
                                    <th>متوسط الوقت</th>
                                    <th>عمق التمرير</th>
                                    <th>معدل الارتداد</th>
                                    <th>التحويلات</th>
                                    <th>التوصية</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($pagePerformance as $page)
                                <tr>
                                    <td>
                                        <strong>{{ Str::limit($page->page_title ?? 'Untitled', 30) }}</strong><br>
                                        <small class="text-muted">{{ Str::limit($page->page_url, 50) }}</small>
                                    </td>
                                    <td>{{ number_format($page->visits) }}</td>
                                    <td>{{ gmdate('i:s', $page->avg_time) }}</td>
                                    <td>
                                        <div class="progress" style="height: 20px;">
                                            <div class="progress-bar {{ $page->avg_scroll >= 75 ? 'bg-success' : ($page->avg_scroll >= 50 ? 'bg-warning' : 'bg-danger') }}" 
                                                 style="width: {{ $page->avg_scroll }}%">
                                                {{ number_format($page->avg_scroll) }}%
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge {{ $page->bounce_rate < 40 ? 'bg-success' : ($page->bounce_rate < 60 ? 'bg-warning' : 'bg-danger') }}">
                                            {{ number_format($page->bounce_rate) }}%
                                        </span>
                                    </td>
                                    <td>{{ number_format($page->conversions) }}</td>
                                    <td>
                                        @if($page->avg_scroll < 50)
                                            <span class="badge bg-danger">⚠️ محتوى غير جذاب</span>
                                        @elseif($page->bounce_rate > 70)
                                            <span class="badge bg-warning">⚠️ معدل ارتداد عالي</span>
                                        @elseif($page->conversions == 0)
                                            <span class="badge bg-info">💡 أضف CTA</span>
                                        @else
                                            <span class="badge bg-success">✅ أداء جيد</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                        </div>
                    @else
                        <div class="alert alert-info text-center">
                            <i class="fas fa-file-alt fa-3x mb-2"></i>
                            <h5>لا توجد بيانات لأداء الصفحات</h5>
                            <p class="mb-0">ستظهر هنا معلومات عن أداء كل صفحة: الزيارات، الوقت المستغرق، عمق التمرير، ومعدل الارتداد.</p>
                            <small class="text-muted">تأكد من أن التتبع مفعّل على الصفحات العامة للموقع.</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Conversion Funnel -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-filter"></i> مسار التحويل (Conversion Funnel)
                    </h5>
                    <small class="text-muted">رحلة المستخدم من الزيارة إلى الإجراء (التحويل = اتصال أو طلب اتجاهات)</small>
                </div>
                <div class="card-body">
                    @php
                        $totalFunnel = array_sum($conversionFunnel);
                    @endphp
                    @if($totalFunnel > 0)
                        <canvas id="conversionFunnelChart" height="100"></canvas>
                        <div class="row mt-4 text-center">
                            <div class="col">
                                <h3 class="text-primary">{{ number_format($conversionFunnel[0]) }}</h3>
                                <small>زوار</small>
                            </div>
                            <div class="col">
                                <h3 class="text-info">{{ number_format($conversionFunnel[1]) }}</h3>
                                <small>شاهدوا متاجر</small>
                                <br><span class="badge bg-secondary">{{ $conversionFunnel[0] > 0 ? number_format(($conversionFunnel[1]/$conversionFunnel[0])*100, 1) : 0 }}%</span>
                            </div>
                            <div class="col">
                                <h3 class="text-warning">{{ number_format($conversionFunnel[2]) }}</h3>
                                <small>تفاعلوا</small>
                                <br><span class="badge bg-secondary">{{ $conversionFunnel[0] > 0 ? number_format(($conversionFunnel[2]/$conversionFunnel[0])*100, 1) : 0 }}%</span>
                            </div>
                            <div class="col">
                                <h3 class="text-success">{{ number_format($conversionFunnel[3]) }}</h3>
                                <small>نقروا على اتصال/اتجاهات</small>
                                <br><span class="badge bg-secondary">{{ $conversionFunnel[0] > 0 ? number_format(($conversionFunnel[3]/$conversionFunnel[0])*100, 1) : 0 }}%</span>
                            </div>
                            <div class="col">
                                <h3 class="text-danger">{{ number_format($conversionFunnel[4]) }}</h3>
                                <small><strong>تحويلات مكتملة</strong></small>
                                <br><span class="badge bg-success">{{ $conversionFunnel[0] > 0 ? number_format(($conversionFunnel[4]/$conversionFunnel[0])*100, 1) : 0 }}%</span>
                            </div>
                        </div>
                        <div class="alert alert-info mt-3">
                            <strong>ℹ️ ما هو التحويل؟</strong> التحويل يعني أن الزائر قام بإجراء مفيد مثل: الاتصال بالمتجر، طلب الاتجاهات، أو التواصل عبر واتساب.
                        </div>
                    @else
                        <div class="alert alert-warning text-center">
                            <i class="fas fa-chart-line fa-3x mb-2"></i>
                            <p class="mb-0"><strong>لا توجد بيانات كافية لمسار التحويل</strong></p>
                            <small>يحتاج هذا التقرير إلى المزيد من زيارات المستخدمين</small>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Recommendations -->
    <div class="row">
        <div class="col-12">
            <div class="card border-warning">
                <div class="card-header bg-warning text-white">
                    <h5 class="mb-0">
                        <i class="fas fa-lightbulb"></i> توصيات التحسين
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        @foreach($recommendations as $rec)
                        <div class="col-md-4 mb-3">
                            <div class="alert alert-{{ $rec['type'] }}">
                                <h6>
                                    <i class="fas fa-{{ $rec['icon'] }}"></i> {{ $rec['title'] }}
                                </h6>
                                <p class="mb-2">{{ $rec['description'] }}</p>
                                <small><strong>الحل:</strong> {{ $rec['solution'] }}</small>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Click Heatmap
const clickHeatmapCtx = document.getElementById('clickHeatmap').getContext('2d');
new Chart(clickHeatmapCtx, {
    type: 'bar',
    data: {
        labels: {!! json_encode($clickHeatmapData->pluck('label')) !!},
        datasets: [{
            label: 'النقرات',
            data: {!! json_encode($clickHeatmapData->pluck('clicks')) !!},
            backgroundColor: function(context) {
                const value = context.parsed.y;
                const max = Math.max(...{!! json_encode($clickHeatmapData->pluck('clicks')) !!});
                const intensity = value / max;
                return `rgba(255, ${Math.round(255 * (1 - intensity))}, 0, ${0.3 + intensity * 0.7})`;
            }
        }]
    },
    options: {
        responsive: true,
        indexAxis: 'y',
        plugins: {
            legend: { display: false }
        }
    }
});

// Scroll Depth Chart
const scrollDepthCtx = document.getElementById('scrollDepthChart').getContext('2d');
new Chart(scrollDepthCtx, {
    type: 'doughnut',
    data: {
        labels: ['0-25%', '26-50%', '51-75%', '76-100%'],
        datasets: [{
            data: {!! json_encode($scrollDepthDistribution) !!},
            backgroundColor: ['#dc3545', '#ffc107', '#17a2b8', '#28a745']
        }]
    }
});

// Time Distribution Chart
const timeDistCtx = document.getElementById('timeDistributionChart').getContext('2d');
new Chart(timeDistCtx, {
    type: 'pie',
    data: {
        labels: ['< 10s', '10-30s', '31-60s', '1-3min', '> 3min'],
        datasets: [{
            data: {!! json_encode($timeDistribution) !!},
            backgroundColor: ['#e74c3c', '#f39c12', '#3498db', '#2ecc71', '#9b59b6']
        }]
    }
});

// Conversion Funnel
const funnelCtx = document.getElementById('conversionFunnelChart').getContext('2d');
new Chart(funnelCtx, {
    type: 'bar',
    data: {
        labels: ['زيارة الصفحة', 'مشاهدة المتجر', 'عرض المنتجات', 'النقر على اتصال', 'تحويل'],
        datasets: [{
            label: 'المستخدمين',
            data: {!! json_encode($conversionFunnel) !!},
            backgroundColor: ['#3498db', '#2ecc71', '#f39c12', '#e67e22', '#e74c3c']
        }]
    },
    options: {
        indexAxis: 'y',
        responsive: true
    }
});
</script>
@endsection
