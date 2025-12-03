@extends('layouts.admin')

@section('title', 'رحلات المستخدمين')

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-0 text-gray-800">
                <i class="fas fa-route text-primary"></i> رحلات المستخدمين
            </h1>
            <p class="text-muted small mb-0">تتبع مسار كل مستخدم عبر صفحات الموقع والوقت المستغرق</p>
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
            <form method="GET" action="{{ route('admin.analytics.user-journeys') }}" class="row g-3">
                <div class="col-md-3">
                    <label class="form-label">الفترة الزمنية</label>
                    <select name="period" class="form-select" onchange="this.form.submit()">
                        <option value="today" {{ request('period', 'today') == 'today' ? 'selected' : '' }}>اليوم</option>
                        <option value="yesterday" {{ request('period') == 'yesterday' ? 'selected' : '' }}>أمس</option>
                        <option value="7days" {{ request('period') == '7days' ? 'selected' : '' }}>آخر 7 أيام</option>
                        <option value="30days" {{ request('period') == '30days' ? 'selected' : '' }}>آخر 30 يوم</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">عدد الصفحات</label>
                    <select name="min_pages" class="form-select" onchange="this.form.submit()">
                        <option value="1" {{ request('min_pages', '1') == '1' ? 'selected' : '' }}>كل الرحلات</option>
                        <option value="2" {{ request('min_pages') == '2' ? 'selected' : '' }}>صفحتان فأكثر</option>
                        <option value="3" {{ request('min_pages') == '3' ? 'selected' : '' }}>3 صفحات فأكثر</option>
                        <option value="5" {{ request('min_pages') == '5' ? 'selected' : '' }}>5 صفحات فأكثر</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">نوع الجهاز</label>
                    <select name="device" class="form-select" onchange="this.form.submit()">
                        <option value="" {{ request('device') == '' ? 'selected' : '' }}>كل الأجهزة</option>
                        <option value="mobile" {{ request('device') == 'mobile' ? 'selected' : '' }}>موبايل</option>
                        <option value="desktop" {{ request('device') == 'desktop' ? 'selected' : '' }}>كمبيوتر</option>
                        <option value="tablet" {{ request('device') == 'tablet' ? 'selected' : '' }}>تابلت</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label">عرض</label>
                    <select name="per_page" class="form-select" onchange="this.form.submit()">
                        <option value="10" {{ request('per_page', '10') == '10' ? 'selected' : '' }}>10 رحلات</option>
                        <option value="25" {{ request('per_page') == '25' ? 'selected' : '' }}>25 رحلة</option>
                        <option value="50" {{ request('per_page') == '50' ? 'selected' : '' }}>50 رحلة</option>
                    </select>
                </div>
            </form>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white shadow">
                <div class="card-body">
                    <h6 class="card-title mb-0">إجمالي الجلسات</h6>
                    <h2 class="mt-2">{{ number_format($stats['total_sessions']) }}</h2>
                    <small>جلسة تصفح</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white shadow">
                <div class="card-body">
                    <h6 class="card-title mb-0">متوسط الصفحات</h6>
                    <h2 class="mt-2">{{ number_format($stats['avg_pages'], 1) }}</h2>
                    <small>صفحة / جلسة</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-info text-white shadow">
                <div class="card-body">
                    <h6 class="card-title mb-0">متوسط الوقت</h6>
                    <h2 class="mt-2">{{ gmdate('i:s', $stats['avg_time']) }}</h2>
                    <small>دقيقة / جلسة</small>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white shadow">
                <div class="card-body">
                    <h6 class="card-title mb-0">معدل التحويل</h6>
                    <h2 class="mt-2">{{ number_format($stats['conversion_rate'], 1) }}%</h2>
                    <small>من إجمالي الجلسات</small>
                </div>
            </div>
        </div>
    </div>

    <!-- User Journeys List -->
    @if($journeys->count() > 0)
        @foreach($journeys as $journey)
            <div class="card shadow mb-3">
                <div class="card-header bg-light">
                    <div class="row align-items-center">
                        <div class="col-md-8">
                            <h6 class="mb-0">
                                <i class="fas fa-user-circle text-muted"></i>
                                @if($journey->user)
                                    <span class="badge bg-success">{{ $journey->user->name }}</span>
                                @else
                                    <span class="badge bg-secondary">ضيف - {{ $journey->ip_address }}</span>
                                @endif
                                <span class="badge bg-info ms-2">
                                    <i class="fas fa-{{ $journey->device_type == 'mobile' ? 'mobile-alt' : ($journey->device_type == 'tablet' ? 'tablet-alt' : 'desktop') }}"></i>
                                    {{ ucfirst($journey->device_type) }}
                                </span>
                                <span class="badge bg-secondary ms-2">{{ $journey->browser }}</span>
                            </h6>
                        </div>
                        <div class="col-md-4 text-end">
                            <small class="text-muted">
                                <i class="fas fa-clock"></i> {{ $journey->started_at->format('Y-m-d H:i') }}
                                ({{ $journey->started_at->diffForHumans() }})
                            </small>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-12">
                            <div class="d-flex justify-content-between align-items-center mb-2">
                                <div>
                                    <strong>{{ $journey->pages_count }}</strong> صفحة
                                    <span class="text-muted ms-3">
                                        <i class="fas fa-hourglass-half"></i>
                                        مدة الجلسة: <strong>{{ gmdate('H:i:s', $journey->total_time) }}</strong>
                                    </span>
                                    @if($journey->has_conversion)
                                        <span class="badge bg-success ms-2">
                                            <i class="fas fa-check-circle"></i> تحويل ناجح
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Journey Path -->
                    <div class="journey-path">
                        @foreach($journey->pages as $index => $page)
                            <div class="journey-step {{ $loop->last ? '' : 'mb-3' }}">
                                <div class="d-flex align-items-start">
                                    <div class="step-number">
                                        <span class="badge bg-primary rounded-circle" style="width: 35px; height: 35px; line-height: 22px;">
                                            {{ $index + 1 }}
                                        </span>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <div class="card border-start border-primary border-3">
                                            <div class="card-body py-2">
                                                <div class="row align-items-center">
                                                    <div class="col-md-6">
                                                        <h6 class="mb-1">
                                                            @if($page->page_title)
                                                                {{ Str::limit($page->page_title, 50) }}
                                                            @else
                                                                <span class="text-muted">بدون عنوان</span>
                                                            @endif
                                                        </h6>
                                                        <small class="text-muted">
                                                            <i class="fas fa-link"></i>
                                                            {{ Str::limit(str_replace(url('/'), '', $page->page_url) ?: '/', 60) }}
                                                        </small>
                                                    </div>
                                                    <div class="col-md-3">
                                                        <small class="text-muted">
                                                            <i class="fas fa-clock text-info"></i>
                                                            <strong>{{ gmdate('i:s', $page->time_on_page ?? 0) }}</strong>
                                                            دقيقة
                                                        </small>
                                                        @if($page->scroll_depth)
                                                            <br>
                                                            <small class="text-muted">
                                                                <i class="fas fa-arrows-alt-v text-success"></i>
                                                                تمرير: <strong>{{ $page->scroll_depth }}%</strong>
                                                            </small>
                                                        @endif
                                                    </div>
                                                    <div class="col-md-3 text-end">
                                                        <small class="text-muted">
                                                            {{ \Carbon\Carbon::parse($page->created_at)->format('H:i:s') }}
                                                        </small>
                                                        @if($page->event_action == 'phone_call')
                                                            <br><span class="badge bg-success">📞 اتصال</span>
                                                        @elseif($page->event_action == 'map_directions')
                                                            <br><span class="badge bg-info">🗺️ اتجاهات</span>
                                                        @elseif($page->shop_id)
                                                            <br><span class="badge bg-warning text-dark">🏪 متجر</span>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                @if(!$loop->last)
                                    <div class="journey-arrow ms-2">
                                        <i class="fas fa-arrow-down text-muted"></i>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>

                    @if($journey->referrer && !str_contains($journey->referrer, url('/')))
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="fas fa-external-link-alt"></i>
                                <strong>مصدر خارجي:</strong>
                                <a href="{{ $journey->referrer }}" target="_blank" class="text-primary">
                                    {{ Str::limit($journey->referrer, 80) }}
                                </a>
                            </small>
                        </div>
                    @endif
                </div>
            </div>
        @endforeach

        <!-- Pagination -->
        <div class="d-flex justify-content-center">
            {{ $journeys->appends(request()->query())->links() }}
        </div>
    @else
        <div class="card shadow">
            <div class="card-body text-center py-5">
                <i class="fas fa-route fa-4x text-muted mb-3"></i>
                <h4 class="text-muted">لا توجد رحلات مستخدمين</h4>
                <p class="text-muted mb-0">لم يتم تسجيل أي رحلات للمستخدمين في الفترة المحددة</p>
                <small class="text-muted">جرّب تغيير الفلاتر أو الانتظار للمزيد من الزيارات</small>
            </div>
        </div>
    @endif
</div>

<style>
    .journey-arrow {
        text-align: center;
        margin: 5px 0;
        margin-left: 17px;
    }
    
    .step-number {
        min-width: 35px;
    }
    
    .journey-path {
        margin-top: 15px;
    }
</style>
@endsection
