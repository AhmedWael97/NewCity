<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', config('app.name'))</title>
    
    <!-- Bootstrap 5 RTL -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    
    <!-- Google Fonts - Cairo -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;600;700&display=swap" rel="stylesheet">
    
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            margin: 0;
            padding: 0;
        }
        
        .minimal-header {
            padding: 1.5rem 0;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        
        .minimal-logo {
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            transition: all 0.3s ease;
        }
        
        .minimal-logo:hover {
            transform: translateY(-2px);
        }
        
        .logo-icon {
            font-size: 2.5rem;
            color: #fff;
            margin-left: 1rem;
        }
        
        .logo-text {
            color: #fff;
            font-size: 1.8rem;
            font-weight: 700;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }
        
        .logo-subtitle {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.9rem;
            font-weight: 400;
            margin: 0;
        }
        
        @media (max-width: 576px) {
            .logo-icon {
                font-size: 2rem;
            }
            .logo-text {
                font-size: 1.4rem;
            }
            .logo-subtitle {
                font-size: 0.8rem;
            }
        }
    </style>
    
    @stack('styles')
</head>
<body>
    <!-- Minimal Header with Logo -->
    <header class="minimal-header">
        <div class="container">
            <a href="{{ url('/') }}" class="minimal-logo">
                <i class="fas fa-city logo-icon"></i>
                <div class="text-center">
                    <h1 class="logo-text">{{ config('app.name', 'دليل المدينة') }}</h1>
                    <p class="logo-subtitle">اكتشف أفضل المتاجر والخدمات</p>
                </div>
            </a>
        </div>
    </header>
    
    @yield('content')
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    @stack('scripts')
</body>
</html>
