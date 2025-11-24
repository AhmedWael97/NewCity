<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'لوحة التحكم') - {{ config('app.name', 'City Guide') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap RTL CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Admin Custom CSS -->
    <style>
        * {
            font-family: 'Cairo', sans-serif;
        }

        body {
            background-color: #f8f9fc;
            font-family: 'Cairo', sans-serif;
        }

        .sidebar {
            background: linear-gradient(180deg, #4e73df 10%, #224abe 100%);
            min-height: 100vh;
            position: fixed;
            top: 0;
            right: 0;
            width: 250px;
            z-index: 1000;
            transition: all 0.3s;
        }

        .sidebar.collapsed {
            width: 70px;
        }

        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.8);
            padding: 0.75rem 1rem;
            border-radius: 0.375rem;
            margin: 0.125rem 0;
            transition: all 0.15s ease-in-out;
        }

        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: #fff;
            background: rgba(255, 255, 255, 0.1);
        }

        .sidebar .nav-link i {
            width: 20px;
            text-align: center;
            margin-left: 0.5rem;
        }

        .main-content {
            margin-right: 250px;
            transition: all 0.3s;
            min-height: 100vh;
        }

        .main-content.sidebar-collapsed {
            margin-right: 70px;
        }

        .topbar {
            background: #fff;
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            border-radius: 0;
            padding: 0 1.5rem;
            margin-bottom: 1.5rem;
            height: 60px;
            display: flex;
            align-items: center;
            border-bottom: 1px solid #e3e6f0;
        }

        .navbar-brand {
            font-size: 1.375rem;
            font-weight: 600;
            color: #5a5c69;
            margin-right: 0;
            margin-left: auto;
        }

        .topbar .navbar-nav {
            flex-direction: row;
            align-items: center;
        }

        .topbar .nav-item {
            margin-left: 0.5rem;
        }

        .topbar .nav-link {
            color: #5a5c69;
            padding: 0.5rem;
            border-radius: 0.35rem;
            transition: all 0.15s ease;
        }

        .topbar .nav-link:hover {
            color: #4e73df;
            background-color: #f8f9fc;
        }

        .badge-counter {
            position: absolute;
            top: -5px;
            right: -5px;
            min-width: 18px;
            height: 18px;
            font-size: 0.65rem;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .topbar-divider {
            width: 1px;
            height: 24px;
            background-color: #e3e6f0;
            margin: 0 1rem;
        }

        .dropdown-list {
            min-width: 280px;
        }

        .icon-circle {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .bg-gradient-primary {
            background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);
        }

        .animated--grow-in {
            animation: animateIn 0.15s ease-in;
        }

        @keyframes animateIn {
            0% {
                opacity: 0;
                transform: scale(0.9) translateY(-10px);
            }
            100% {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        /* Responsive Navbar Improvements */
        @media (max-width: 768px) {
            .topbar {
                padding: 0 1rem;
            }
            
            .navbar-brand {
                font-size: 1.1rem;
            }
            
            .topbar .nav-item {
                margin-left: 0.25rem;
            }
            
            .topbar-divider {
                display: none !important;
            }
            
            .dropdown-list {
                min-width: 260px;
            }
        }

        @media (max-width: 576px) {
            .topbar {
                padding: 0 0.75rem;
            }
            
            .navbar-brand {
                font-size: 1rem;
            }
            
            .dropdown-list {
                min-width: 240px;
            }
        }

        .card {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            border: 1px solid #e3e6f0;
        }

        .btn-primary {
            background-color: #4e73df;
            border-color: #4e73df;
        }

        .btn-primary:hover {
            background-color: #2e59d9;
            border-color: #2653d4;
        }

        .text-primary {
            color: #4e73df !important;
        }

        .border-left-primary {
            border-left: 0.25rem solid #4e73df !important;
        }

        .border-left-success {
            border-left: 0.25rem solid #1cc88a !important;
        }

        .border-left-info {
            border-left: 0.25rem solid #36b9cc !important;
        }

        .border-left-warning {
            border-left: 0.25rem solid #f6c23e !important;
        }

        .text-gray-800 {
            color: #5a5c69 !important;
        }

        .text-gray-300 {
            color: #dddfeb !important;
        }

        .sidebar-brand {
            padding: 1.5rem 1rem;
            text-align: center;
            border-bottom: 1px solid rgba(255, 255, 255, 0.1);
        }

        .sidebar-brand-icon {
            font-size: 2rem;
            color: #fff;
        }

        .sidebar-brand-text {
            color: #fff;
            font-size: 1.2rem;
            font-weight: 600;
            margin-top: 0.5rem;
            display: block;
        }

        .sidebar.collapsed .sidebar-brand-text {
            display: none;
        }

        .sidebar-heading {
            font-size: 0.65rem;
            font-weight: 800;
            color: rgba(255, 255, 255, 0.4);
            text-transform: uppercase;
            letter-spacing: 0.05rem;
            padding: 0.75rem 1rem 0.375rem;
            margin-bottom: 0;
        }

        .sidebar.collapsed .sidebar-heading {
            display: none;
        }

        .sidebar.collapsed .nav-link span {
            display: none;
        }

        /* Hide scrollbar but keep scrolling functionality */
        .sidebar-menu {
            scrollbar-width: none; /* Firefox */
            -ms-overflow-style: none; /* IE and Edge */
        }

        .sidebar-menu::-webkit-scrollbar {
            display: none; /* Chrome, Safari, Opera */
        }

        .dropdown-menu {
            border: 1px solid #e3e6f0;
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
        }

        .alert {
            border-radius: 0.375rem;
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Enhanced Table Styling */
        .table {
            background: #fff;
            border-radius: 0.375rem;
            overflow: hidden;
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            font-size: 0.875rem; /* Smaller base font size */
        }

        .table-dark {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: #fff;
        }

        .table-dark th {
            border-color: rgba(255, 255, 255, 0.2);
            font-weight: 600;
            text-transform: uppercase;
            font-size: 0.7rem; /* Smaller header font */
            letter-spacing: 0.05rem;
            padding: 0.75rem 0.5rem; /* Reduced padding */
            white-space: nowrap;
        }

        .table tbody tr {
            transition: all 0.2s ease;
        }

        .table tbody tr:hover {
            background-color: rgba(78, 115, 223, 0.05);
            transform: translateY(-1px);
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.1);
        }

        .table td {
            vertical-align: middle;
            padding: 0.6rem 0.5rem; /* Reduced padding */
            border-color: #e3e6f0;
            font-size: 0.825rem; /* Smaller cell font */
        }

        .table .btn-group .btn {
            padding: 0.2rem 0.4rem; /* Smaller buttons */
            margin: 0 0.1rem;
            font-size: 0.75rem;
        }

        /* Responsive Column Classes */
        .d-none-sm {
            display: table-cell;
        }

        .d-none-md {
            display: table-cell;
        }

        .d-none-lg {
            display: table-cell;
        }

        @media (max-width: 1200px) {
            .d-none-lg {
                display: none !important;
            }
        }

        @media (max-width: 992px) {
            .d-none-md {
                display: none !important;
            }
            .table {
                font-size: 0.8rem;
            }
            .table td {
                padding: 0.5rem 0.4rem;
            }
        }

        @media (max-width: 768px) {
            .d-none-sm {
                display: none !important;
            }
            .table {
                font-size: 0.75rem;
            }
            .table td {
                padding: 0.4rem 0.3rem;
            }
            .table-dark th {
                padding: 0.6rem 0.4rem;
                font-size: 0.65rem;
            }
        }

        /* Compact Table Styles */
        .table-compact {
            font-size: 0.8rem;
        }

        .table-compact td {
            padding: 0.5rem 0.4rem;
        }

        .table-compact .table-dark th {
            padding: 0.6rem 0.4rem;
            font-size: 0.7rem;
        }

        /* Action Dropdown Styles */
        .action-dropdown {
            min-width: auto;
        }

        .action-dropdown .dropdown-toggle {
            border: none;
            background: transparent;
            color: #6c757d;
            padding: 0.25rem 0.5rem;
            font-size: 0.875rem;
        }

        .action-dropdown .dropdown-toggle:hover {
            color: #4e73df;
            background-color: rgba(78, 115, 223, 0.1);
        }

        .action-dropdown .dropdown-menu {
            min-width: 160px;
            border: 1px solid rgba(0, 0, 0, 0.15);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.175);
        }

        .action-dropdown .dropdown-item {
            padding: 0.5rem 1rem;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
        }

        .action-dropdown .dropdown-item i {
            width: 16px;
            margin-left: 0.5rem;
        }

        .badge {
            font-size: 0.65rem; /* Smaller badges */
            padding: 0.25rem 0.5rem;
            border-radius: 0.25rem;
        }

        .badge-sm {
            font-size: 0.6rem;
            padding: 0.2rem 0.4rem;
        }

        /* Text truncation utilities */
        .text-truncate-sm {
            max-width: 100px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .text-truncate-md {
            max-width: 150px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .text-truncate-lg {
            max-width: 200px;
            overflow: hidden;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        /* Card Enhancements */
        .card {
            transition: all 0.2s ease;
            border: 1px solid rgba(0, 0, 0, 0.125);
        }

        .card:hover {
            transform: translateY(-2px);
            box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15);
        }

        .card-header {
            background: linear-gradient(135deg, rgba(78, 115, 223, 0.1) 0%, rgba(78, 115, 223, 0.05) 100%);
            border-bottom: 1px solid rgba(78, 115, 223, 0.2);
        }

        /* Form Enhancements */
        .form-control:focus {
            border-color: #4e73df;
            box-shadow: 0 0 0 0.2rem rgba(78, 115, 223, 0.25);
        }

        .form-control {
            border-radius: 0.375rem;
            transition: all 0.15s ease-in-out;
        }

        /* Button Enhancements */
        .btn {
            border-radius: 0.375rem;
            font-weight: 500;
            transition: all 0.15s ease-in-out;
        }

        .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 0.25rem 0.5rem rgba(0, 0, 0, 0.15);
        }

        .btn-sm {
            font-size: 0.8rem;
            padding: 0.375rem 0.75rem;
        }

        /* Responsive Table */
        .table-responsive {
            border-radius: 0.375rem;
        }

        @media (max-width: 768px) {
            .table-responsive table {
                font-size: 0.85rem;
            }
            
            .table .btn-group {
                display: flex;
                flex-direction: column;
                gap: 0.25rem;
            }
            
            .table .btn-group .btn {
                margin: 0;
                font-size: 0.75rem;
            }
        }

        /* Enhanced Pagination */
        .pagination {
            gap: 0.25rem;
            margin-bottom: 0;
            direction: ltr; /* Fix for RTL arrow direction */
        }

        .page-link {
            border: 1px solid #dee2e6;
            color: #5a5c69;
            padding: 0.5rem 0.75rem;
            border-radius: 0.375rem;
            transition: all 0.15s ease-in-out;
            font-weight: 500;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            height: 40px;
        }

        .page-link:hover {
            background-color: #f8f9fc;
            border-color: #4e73df;
            color: #4e73df;
            transform: translateY(-1px);
            box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
            text-decoration: none;
        }

        .page-item.active .page-link {
            background: linear-gradient(135deg, #4e73df 0%, #2e59d9 100%);
            border-color: #4e73df;
            color: #fff;
            box-shadow: 0 0.25rem 0.5rem rgba(78, 115, 223, 0.25);
            font-weight: 600;
        }

        .page-item.active .page-link:hover {
            transform: none;
            background: linear-gradient(135deg, #2e59d9 0%, #1e3db3 100%);
        }

        .page-item.disabled .page-link {
            color: #858796;
            background-color: #f8f9fc;
            border-color: #e3e6f0;
            cursor: not-allowed;
            opacity: 0.6;
        }

        .page-item.disabled .page-link:hover {
            transform: none;
            box-shadow: none;
        }

        .pagination .page-item:first-child .page-link {
            border-radius: 0.375rem;
        }

        .pagination .page-item:last-child .page-link {
            border-radius: 0.375rem;
        }

        /* Pagination Info Text */
        .pagination-info {
            color: #858796;
            font-size: 0.875rem;
            display: flex;
            align-items: center;
        }

        .pagination-info .fw-bold {
            color: #5a5c69;
            font-weight: 600;
        }

        /* Mobile Pagination */
        @media (max-width: 576px) {
            .page-link {
                padding: 0.375rem 0.5rem;
                min-width: 36px;
                height: 36px;
                font-size: 0.875rem;
            }
            
            .pagination-info {
                font-size: 0.8rem;
            }
        }

        /* Loading States */
        .table-loading {
            position: relative;
            pointer-events: none;
            opacity: 0.6;
        }

        .table-loading::after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 20px;
            height: 20px;
            border: 2px solid #f3f3f3;
            border-top: 2px solid #4e73df;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            transform: translate(-50%, -50%);
        }

        @keyframes spin {
            0% { transform: translate(-50%, -50%) rotate(0deg); }
            100% { transform: translate(-50%, -50%) rotate(360deg); }
        }

        .sidebar-toggle {
            position: absolute;
            top: 15px;
            left: 15px;
            z-index: 10;
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: none;
            border-radius: 8px;
            width: 35px;
            height: 35px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
            cursor: pointer;
        }

        .sidebar-toggle:hover {
            background: rgba(255, 255, 255, 0.3);
        }

        @media (max-width: 768px) {
            .sidebar {
                transform: translateX(100%);
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-right: 0;
            }
            
            .sidebar-toggle {
                left: 15px;
            }
        }
    </style>

    @stack('styles')
</head>

<body>
    <!-- Sidebar -->
    <nav class="sidebar" id="sidebar">
        <!-- Sidebar Brand -->
        <div class="sidebar-brand">
            <div class="sidebar-brand-icon">
                <i class="fas fa-city"></i>
            </div>
            <div class="sidebar-brand-text">دليل المدينة</div>
        </div>

        <!-- Sidebar Menu -->
        <div class="sidebar-menu p-3 overflow-auto" style="max-height: calc(100vh - 100px);">
            <ul class="nav flex-column">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" 
                       href="{{ route('admin.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>لوحة التحكم</span>
                    </a>
                </li>

                <!-- Users Management -->
                <div class="sidebar-heading">إدارة المستخدمين</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}" 
                       href="{{ route('admin.users.index') }}">
                        <i class="fas fa-users"></i>
                        <span>المستخدمين</span>
                    </a>
                </li>

                <!-- Shop Management -->
                <div class="sidebar-heading">إدارة المتاجر</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.shops.index') ? 'active' : '' }}" 
                       href="{{ route('admin.shops.index') }}">
                        <i class="fas fa-store"></i>
                        <span>جميع المتاجر</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.shops.pending') ? 'active' : '' }}" 
                       href="{{ route('admin.shops.pending') }}">
                        <i class="fas fa-clock"></i>
                        <span>المتاجر المعلقة</span>
                        @if(isset($stats) && $stats['pending_shops'] > 0)
                            <span class="badge badge-warning me-2">{{ $stats['pending_shops'] }}</span>
                        @endif
                    </a>
                </li>

                <!-- Content Management -->
                <div class="sidebar-heading">إدارة المحتوى</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.cities.*') ? 'active' : '' }}" 
                       href="{{ route('admin.cities.index') }}">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>المدن</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" 
                       href="{{ route('admin.categories.index') }}">
                        <i class="fas fa-tags"></i>
                        <span>التصنيفات</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.ratings.*') ? 'active' : '' }}" 
                       href="{{ route('admin.ratings.index') }}">
                        <i class="fas fa-star"></i>
                        <span>التقييمات</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.reviews.*') ? 'active' : '' }}" 
                       href="{{ route('admin.reviews.index') }}">
                        <i class="fas fa-comments"></i>
                        <span>المراجعات</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.favorites.*') ? 'active' : '' }}" 
                       href="{{ route('admin.favorites.index') }}">
                        <i class="fas fa-heart"></i>
                        <span>المفضلات</span>
                    </a>
                </li>

                {{-- Reports (Temporarily disabled - controller not implemented)
                <!-- Reports -->
                <div class="sidebar-heading">التقارير والإحصائيات</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.reports.*') ? 'active' : '' }}" 
                       href="{{ route('admin.reports.index') }}">
                        <i class="fas fa-chart-line"></i>
                        <span>التقارير</span>
                    </a>
                </li>
                --}}

                <!-- Business Management -->
                <div class="sidebar-heading">إدارة الأعمال</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.subscriptions.*') ? 'active' : '' }}" 
                       href="{{ route('admin.subscriptions.index') }}">
                        <i class="fas fa-credit-card"></i>
                        <span>خطط الاشتراك</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.payments.*') ? 'active' : '' }}" 
                       href="{{ route('admin.payments.index') }}">
                        <i class="fas fa-money-check-alt"></i>
                        <span>المدفوعات</span>
                        @php
                            $pendingPayments = \App\Models\ShopSubscription::where('payment_status', 'pending')->count();
                        @endphp
                        @if($pendingPayments > 0)
                            <span class="badge bg-warning text-dark me-2">{{ $pendingPayments }}</span>
                        @endif
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.tickets.*') ? 'active' : '' }}" 
                       href="{{ route('admin.tickets.index') }}">
                        <i class="fas fa-ticket-alt"></i>
                        <span>تذاكر الدعم</span>
                        @php
                            $pendingTickets = \App\Models\SupportTicket::where('status', 'open')->count();
                        @endphp
                        @if($pendingTickets > 0)
                            <span class="badge bg-warning text-dark me-2">{{ $pendingTickets }}</span>
                        @endif
                    </a>
                </li>

                <!-- Analytics & Reports -->
                <div class="sidebar-heading">التسويق والإعلانات</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.advertisements.*') ? 'active' : '' }}" 
                       href="{{ route('admin.advertisements.index') }}">
                        <i class="fas fa-bullhorn"></i>
                        <span>إدارة الإعلانات</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.advertisements.analytics') ? 'active' : '' }}" 
                       href="{{ route('admin.advertisements.analytics') }}">
                        <i class="fas fa-chart-line"></i>
                        <span>تقارير الإعلانات</span>
                    </a>
                </li>

                <!-- City Customization -->
                <div class="sidebar-heading">تخصيص المدن</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.city-styles.*') ? 'active' : '' }}" 
                       href="{{ route('admin.city-styles.index') }}">
                        <i class="fas fa-palette"></i>
                        <span>تصاميم المدن</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.city-banners.*') ? 'active' : '' }}" 
                       href="{{ route('admin.city-banners.index') }}">
                        <i class="fas fa-image"></i>
                        <span>إعلانات المدن</span>
                    </a>
                </li>

                <!-- Analytics & Reports -->
                <div class="sidebar-heading">التحليلات والتقارير</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.analytics.*') ? 'active' : '' }}" 
                       href="{{ route('admin.analytics.index') }}">
                        <i class="fas fa-chart-pie"></i>
                        <span>التحليلات المتقدمة</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.analytics.shops') ? 'active' : '' }}" 
                       href="{{ route('admin.analytics.shops') }}">
                        <i class="fas fa-store"></i>
                        <span>أداء المتاجر</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.analytics.cities') ? 'active' : '' }}" 
                       href="{{ route('admin.analytics.cities') }}">
                        <i class="fas fa-map-marked-alt"></i>
                        <span>تحليلات المدن</span>
                    </a>
                </li>

                {{-- Settings (Temporarily disabled - controller not implemented)
                <!-- Settings -->
                <div class="sidebar-heading">الإعدادات</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.settings.*') ? 'active' : '' }}" 
                       href="{{ route('admin.settings.index') }}">
                        <i class="fas fa-cog"></i>
                        <span>إعدادات النظام</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('admin.logs.*') ? 'active' : '' }}" 
                       href="{{ route('admin.logs.index') }}">
                        <i class="fas fa-list"></i>
                        <span>سجل النشاطات</span>
                    </a>
                </li>
                --}}
            </ul>
        </div>

        <!-- Sidebar Toggle Button (inside sidebar) -->
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
    </nav>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Top Bar -->
        <nav class="navbar navbar-expand navbar-light topbar mb-4 static-top shadow">
            <!-- Page Title -->
            <div class="navbar-brand d-none d-md-block">
                <i class="fas fa-tachometer-alt me-2"></i>
                @yield('title', 'لوحة التحكم')
            </div>

            <!-- Spacer -->
            <div class="grow"></div>

            <!-- Topbar Navbar -->
            <ul class="navbar-nav d-flex flex-row align-items-center">
                <!-- Notifications Dropdown -->
                <li class="nav-item dropdown no-arrow mx-1 position-relative">
                    <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                       data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-bell fa-fw"></i>
                        <!-- Counter - Alerts -->
                        @if(isset($stats) && $stats['pending_tickets'] > 0)
                            <span class="badge badge-danger badge-counter">{{ $stats['pending_tickets'] }}</span>
                        @endif
                    </a>
                    <!-- Dropdown - Alerts -->
                    <div class="dropdown-list dropdown-menu dropdown-menu-end shadow animated--grow-in"
                         aria-labelledby="alertsDropdown">
                        <h6 class="dropdown-header">
                            <i class="fas fa-bell me-2"></i>
                            مركز التنبيهات
                        </h6>
                        @if(isset($stats) && $stats['pending_tickets'] > 0)
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.tickets.index') }}">
                                <div class="me-3">
                                    <div class="icon-circle bg-warning">
                                        <i class="fas fa-ticket-alt text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="small text-gray-500">الآن</div>
                                    <span class="font-weight-bold">{{ $stats['pending_tickets'] }} تذكرة دعم جديدة</span>
                                </div>
                            </a>
                        @endif
                        @if(isset($stats) && $stats['pending_shops'] > 0)
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('admin.shops.pending') }}">
                                <div class="me-3">
                                    <div class="icon-circle bg-info">
                                        <i class="fas fa-store text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="small text-gray-500">الآن</div>
                                    <span class="font-weight-bold">{{ $stats['pending_shops'] }} متجر في انتظار المراجعة</span>
                                </div>
                            </a>
                        @endif
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-center small text-gray-500" href="{{ route('admin.system.health') }}">
                            عرض جميع التنبيهات
                        </a>
                    </div>
                </li>

                <!-- Messages Dropdown -->
                <li class="nav-item dropdown no-arrow mx-1 position-relative">
                    <a class="nav-link dropdown-toggle" href="#" id="messagesDropdown" role="button"
                       data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-envelope fa-fw"></i>
                        <!-- Counter - Messages -->
                        <span class="badge badge-primary badge-counter">5</span>
                    </a>
                    <!-- Dropdown - Messages -->
                    <div class="dropdown-list dropdown-menu dropdown-menu-end shadow animated--grow-in"
                         aria-labelledby="messagesDropdown">
                        <h6 class="dropdown-header">
                            <i class="fas fa-envelope me-2"></i>
                            مركز الرسائل
                        </h6>
                        <a class="dropdown-item d-flex align-items-center" href="#">
                            <div class="me-3">
                                <div class="icon-circle bg-success">
                                    <i class="fas fa-user text-white"></i>
                                </div>
                            </div>
                            <div>
                                <div class="small text-gray-500">منذ ساعة</div>
                                <span class="font-weight-bold">رسالة جديدة من أحد المستخدمين</span>
                            </div>
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item text-center small text-gray-500" href="#">قراءة المزيد من الرسائل</a>
                    </div>
                </li>

                <div class="topbar-divider d-none d-sm-block"></div>

                <!-- User Information -->
                <li class="nav-item dropdown no-arrow">
                    <a class="nav-link dropdown-toggle d-flex align-items-center" href="#" id="userDropdown" role="button"
                       data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="me-3 d-none d-lg-inline text-gray-700 small fw-bold">{{ Auth::user()->name }}</span>
                        <div class="rounded-circle bg-gradient-primary text-white d-inline-flex align-items-center justify-content-center shadow" 
                             style="width: 36px; height: 36px; font-size: 0.875rem; font-weight: 600;">
                            {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                        </div>
                    </a>
                    <!-- Dropdown - User Information -->
                    <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in"
                         aria-labelledby="userDropdown">
                        <div class="dropdown-header text-center">
                            <div class="rounded-circle bg-gradient-primary text-white d-inline-flex align-items-center justify-content-center mb-2" 
                                 style="width: 48px; height: 48px; font-size: 1.1rem; font-weight: 600;">
                                {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                            </div>
                            <h6 class="mb-1">{{ Auth::user()->name }}</h6>
                            <small class="text-muted">{{ Auth::user()->email }}</small>
                        </div>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#">
                            <i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i>
                            الملف الشخصي
                        </a>
                        <a class="dropdown-item" href="{{ route('admin.settings.index') }}">
                            <i class="fas fa-cogs fa-sm fa-fw me-2 text-gray-400"></i>
                            الإعدادات
                        </a>
                        <a class="dropdown-item" href="{{ route('admin.logs.index') }}">
                            <i class="fas fa-list fa-sm fa-fw me-2 text-gray-400"></i>
                            سجل النشاطات
                        </a>
                        <div class="dropdown-divider"></div>
                        <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#logoutModal">
                            <i class="fas fa-sign-out-alt fa-sm fa-fw me-2 text-gray-400"></i>
                            تسجيل الخروج
                        </a>
                    </div>
                </li>
            </ul>
        </nav>

        <!-- Page Content -->
        <div class="container-fluid fade-in">
            <!-- Success Message -->
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="fas fa-check-circle me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Error Message -->
            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Warning Message -->
            @if(session('warning'))
                <div class="alert alert-warning alert-dismissible fade show" role="alert">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    {{ session('warning') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <!-- Logout Modal -->
    <div class="modal fade" id="logoutModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">هل أنت متأكد من تسجيل الخروج؟</h5>
                    <button class="btn-close" type="button" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">اضغط على "تسجيل الخروج" أدناه إذا كنت جاهزاً لإنهاء جلستك الحالية.</div>
                <div class="modal-footer">
                    <button class="btn btn-secondary" type="button" data-bs-dismiss="modal">إلغاء</button>
                    <form method="POST" action="{{ route('logout') }}" class="d-inline">
                        @csrf
                        <button class="btn btn-primary" type="submit">تسجيل الخروج</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- Custom JS -->
    <script>
        // Sidebar Toggle
        document.getElementById('sidebarToggle').addEventListener('click', function() {
            const sidebar = document.getElementById('sidebar');
            const mainContent = document.getElementById('mainContent');
            const toggleBtn = document.getElementById('sidebarToggle');
            
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('sidebar-collapsed');
            toggleBtn.classList.toggle('collapsed');
        });

        // Mobile sidebar toggle
        if (window.innerWidth <= 768) {
            document.getElementById('sidebarToggle').addEventListener('click', function() {
                const sidebar = document.getElementById('sidebar');
                sidebar.classList.toggle('show');
            });
        }

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            const alerts = document.querySelectorAll('.alert');
            alerts.forEach(function(alert) {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>

    @stack('scripts')
</body>
</html>