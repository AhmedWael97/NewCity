<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'لوحة تحكم صاحب المتجر') - {{ config('app.name', 'City Guide') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap RTL CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- Shop Owner Custom CSS -->
    <style>
        * {
            font-family: 'Cairo', sans-serif;
        }

        body {
            background-color: #f8f9fc;
            font-family: 'Cairo', sans-serif;
        }

        .sidebar {
            background: linear-gradient(180deg, #1cc88a 10%, #13855c 100%);
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
        }

        .navbar-brand {
            font-size: 1.375rem;
            font-weight: 600;
            color: #5a5c69;
        }

        .card {
            box-shadow: 0 0.15rem 1.75rem 0 rgba(58, 59, 69, 0.15);
            border: 1px solid #e3e6f0;
        }

        .btn-primary {
            background-color: #1cc88a;
            border-color: #1cc88a;
        }

        .btn-primary:hover {
            background-color: #17a673;
            border-color: #169b6b;
        }

        .text-primary {
            color: #1cc88a !important;
        }

        .border-left-primary {
            border-left: 0.25rem solid #1cc88a !important;
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

        .sidebar-toggle {
            position: fixed;
            top: 20px;
            right: 260px;
            z-index: 1001;
            background: #1cc88a;
            color: white;
            border: none;
            border-radius: 50%;
            width: 40px;
            height: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.3s;
        }

        .sidebar-toggle.collapsed {
            right: 80px;
        }

        .fade-in {
            animation: fadeIn 0.5s ease-in;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
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
                right: 20px;
            }
            
            .sidebar-toggle.collapsed {
                right: 20px;
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
                <i class="fas fa-store"></i>
            </div>
            <div class="sidebar-brand-text">متجري</div>
        </div>

        <!-- Sidebar Menu -->
        <div class="sidebar-menu p-3">
            <ul class="nav flex-column">
                <!-- Dashboard -->
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('shop-owner.dashboard') ? 'active' : '' }}" 
                       href="{{ route('shop-owner.dashboard') }}">
                        <i class="fas fa-tachometer-alt"></i>
                        <span>لوحة التحكم</span>
                    </a>
                </li>

                <!-- My Shops -->
                <div class="sidebar-heading">إدارة المتاجر</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('shop-owner.shops.*') ? 'active' : '' }}" 
                       href="{{ route('shop-owner.shops.index') }}">
                        <i class="fas fa-store"></i>
                        <span>متاجري</span>
                    </a>
                </li>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('shop-owner.shops.create') ? 'active' : '' }}" 
                       href="{{ route('shop-owner.shops.create') }}">
                        <i class="fas fa-plus"></i>
                        <span>إضافة متجر</span>
                    </a>
                </li>

                <!-- Ratings & Reviews -->
                <div class="sidebar-heading">التقييمات والمراجعات</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('shop-owner.ratings.*') ? 'active' : '' }}" 
                       href="{{ route('shop-owner.ratings.index') }}">
                        <i class="fas fa-star"></i>
                        <span>التقييمات</span>
                    </a>
                </li>

                <!-- Analytics -->
                <div class="sidebar-heading">التقارير والإحصائيات</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('shop-owner.analytics.*') ? 'active' : '' }}" 
                       href="{{ route('shop-owner.analytics.index') }}">
                        <i class="fas fa-chart-bar"></i>
                        <span>الإحصائيات</span>
                    </a>
                </li>

                <!-- Profile -->
                <div class="sidebar-heading">الإعدادات</div>
                <li class="nav-item">
                    <a class="nav-link {{ request()->routeIs('shop-owner.profile.*') ? 'active' : '' }}" 
                       href="{{ route('shop-owner.profile.edit') }}">
                        <i class="fas fa-user"></i>
                        <span>الملف الشخصي</span>
                    </a>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Sidebar Toggle Button -->
    <button class="sidebar-toggle" id="sidebarToggle">
        <i class="fas fa-bars"></i>
    </button>

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Top Bar -->
        <nav class="navbar navbar-expand navbar-light topbar mb-4 static-top shadow">
            <!-- Page Title -->
            <div class="navbar-brand d-none d-md-block">
                @yield('title', 'لوحة تحكم صاحب المتجر')
            </div>

            <!-- Topbar Navbar -->
            <ul class="navbar-nav me-auto">
                <!-- Notifications Dropdown -->
                <li class="nav-item dropdown no-arrow mx-1">
                    <a class="nav-link dropdown-toggle" href="#" id="alertsDropdown" role="button"
                       data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <i class="fas fa-bell fa-fw"></i>
                        <!-- Counter - Alerts -->
                        <span class="badge badge-danger badge-counter">{{ auth()->user()->shops()->where('status', 'pending')->count() }}</span>
                    </a>
                    <!-- Dropdown - Alerts -->
                    <div class="dropdown-list dropdown-menu dropdown-menu-end shadow animated--grow-in"
                         aria-labelledby="alertsDropdown">
                        <h6 class="dropdown-header">
                            التنبيهات
                        </h6>
                        @foreach(auth()->user()->shops()->where('status', 'pending')->limit(3)->get() as $shop)
                            <a class="dropdown-item d-flex align-items-center" href="{{ route('shop-owner.shops.show', $shop) }}">
                                <div class="me-3">
                                    <div class="icon-circle bg-warning">
                                        <i class="fas fa-clock text-white"></i>
                                    </div>
                                </div>
                                <div>
                                    <div class="small text-gray-500">{{ $shop->created_at->diffForHumans() }}</div>
                                    <span class="font-weight-bold">متجر {{ $shop->name }} في انتظار الموافقة</span>
                                </div>
                            </a>
                        @endforeach
                        <a class="dropdown-item text-center small text-gray-500" href="{{ route('shop-owner.shops.index') }}">عرض جميع المتاجر</a>
                    </div>
                </li>

                <div class="topbar-divider d-none d-sm-block"></div>

                <!-- User Information -->
                <li class="nav-item dropdown no-arrow">
                    <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                       data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <span class="me-2 d-none d-lg-inline text-gray-600 small">{{ Auth::user()->name }}</span>
                        <div class="rounded-circle bg-success text-white d-inline-flex align-items-center justify-content-center" 
                             style="width: 32px; height: 32px; font-size: 0.875rem;">
                            {{ substr(Auth::user()->name, 0, 1) }}
                        </div>
                    </a>
                    <!-- Dropdown - User Information -->
                    <div class="dropdown-menu dropdown-menu-end shadow animated--grow-in"
                         aria-labelledby="userDropdown">
                        <a class="dropdown-item" href="{{ route('shop-owner.profile.edit') }}">
                            <i class="fas fa-user fa-sm fa-fw me-2 text-gray-400"></i>
                            الملف الشخصي
                        </a>
                        <a class="dropdown-item" href="{{ route('shop-owner.analytics.index') }}">
                            <i class="fas fa-chart-bar fa-sm fa-fw me-2 text-gray-400"></i>
                            الإحصائيات
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