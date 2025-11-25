@extends('layouts.admin')

@section('title', 'التقارير والإحصائيات')

@section('content')
<div class="container-fluid">
    <!-- Page Heading -->
    <div class="d-sm-flex align-items-center justify-content-between mb-4">
        <h1 class="h3 mb-0 text-gray-800">
            <i class="fas fa-chart-line"></i> التقارير والإحصائيات
        </h1>
        <div>
            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal" data-bs-target="#generateReportModal">
                <i class="fas fa-plus"></i> إنشاء تقرير جديد
            </button>
        </div>
    </div>

    <!-- Statistics Cards -->
    <div class="row">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="card border-left-primary shadow h-100 py-2">
                <div class="card-body">
                    <div class="row no-gutters align-items-center">
                        <div class="col mr-2">
                            <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                إجمالي المستخدمين
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $userStats['total_users'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-users fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-success">
                            <i class="fas fa-plus"></i> {{ $userStats['new_users_this_month'] }} جديد هذا الشهر
                        </small>
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
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $shopStats['total_shops'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-store fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-info">
                            <i class="fas fa-check"></i> {{ $shopStats['verified_shops'] }} محقق
                        </small>
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
                                إجمالي المدن
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $cityStats['total_cities'] }}</div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-city fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-warning">
                            <i class="fas fa-store"></i> {{ $cityStats['cities_with_shops'] }} بها متاجر
                        </small>
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
                                متوسط التقييم
                            </div>
                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                {{ number_format($ratingStats['average_rating'], 1) }}/5
                            </div>
                        </div>
                        <div class="col-auto">
                            <i class="fas fa-star fa-2x text-gray-300"></i>
                        </div>
                    </div>
                    <div class="mt-2">
                        <small class="text-info">
                            <i class="fas fa-comments"></i> {{ $ratingStats['total_ratings'] }} تقييم
                        </small>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Reports Section -->
    <div class="row">
        <div class="col-12">
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">تقارير النظام</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h5>تقارير المستخدمين</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    مستخدمين محققين
                                    <span class="badge bg-primary text-white rounded-pill">{{ $userStats['verified_users'] }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    أصحاب متاجر
                                    <span class="badge bg-success text-white rounded-pill">{{ $userStats['shop_owners'] }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    مستخدمين جدد هذا الشهر
                                    <span class="badge bg-info text-white rounded-pill">{{ $userStats['new_users_this_month'] }}</span>
                                </li>
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h5>تقارير المتاجر</h5>
                            <ul class="list-group list-group-flush">
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    متاجر نشطة
                                    <span class="badge bg-success text-white rounded-pill">{{ $shopStats['active_shops'] }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    متاجر مميزة
                                    <span class="badge bg-warning text-dark rounded-pill">{{ $shopStats['featured_shops'] }}</span>
                                </li>
                                <li class="list-group-item d-flex justify-content-between align-items-center">
                                    تقييمات هذا الشهر
                                    <span class="badge bg-info text-white rounded-pill">{{ $ratingStats['ratings_this_month'] }}</span>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Generate Report Modal -->
<div class="modal fade" id="generateReportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">إنشاء تقرير جديد</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('admin.reports.generate') }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="report_type" class="form-label">نوع التقرير</label>
                        <select class="form-control" id="report_type" name="report_type" required>
                            <option value="">اختر نوع التقرير</option>
                            <option value="users">تقرير المستخدمين</option>
                            <option value="shops">تقرير المتاجر</option>
                            <option value="ratings">تقرير التقييمات</option>
                            <option value="cities">تقرير المدن</option>
                        </select>
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date_from" class="form-label">من تاريخ</label>
                                <input type="date" class="form-control" id="date_from" name="date_from">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="mb-3">
                                <label for="date_to" class="form-label">إلى تاريخ</label>
                                <input type="date" class="form-control" id="date_to" name="date_to">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label for="format" class="form-label">تنسيق التقرير</label>
                        <select class="form-control" id="format" name="format" required>
                            <option value="csv">CSV</option>
                            <option value="pdf">PDF</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                    <button type="submit" class="btn btn-primary">إنشاء التقرير</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection