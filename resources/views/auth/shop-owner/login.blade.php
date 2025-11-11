<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>تسجيل دخول صاحب المتجر - {{ config('app.name', 'City Guide') }}</title>

    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap RTL CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.rtl.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <style>
        * {
            font-family: 'Cairo', sans-serif;
        }

        body {
            background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            background: rgba(255, 255, 255, 0.95);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            box-shadow: 0 15px 35px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            width: 100%;
            max-width: 900px;
        }

        .login-left {
            background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
            color: white;
            padding: 3rem;
            text-align: center;
        }

        .login-right {
            padding: 3rem;
        }

        .shop-icon {
            font-size: 4rem;
            margin-bottom: 1.5rem;
            opacity: 0.9;
        }

        .form-control {
            border-radius: 10px;
            border: 2px solid #e3e6f0;
            padding: 0.75rem 1rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .form-control:focus {
            border-color: #1cc88a;
            box-shadow: 0 0 0 0.2rem rgba(28, 200, 138, 0.25);
        }

        .btn-shop-owner {
            background: linear-gradient(135deg, #1cc88a 0%, #13855c 100%);
            border: none;
            border-radius: 10px;
            padding: 0.75rem 2rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            transition: all 0.3s ease;
        }

        .btn-shop-owner:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(28, 200, 138, 0.4);
        }

        .input-group-text {
            background: #f8f9fc;
            border: 2px solid #e3e6f0;
            border-left: none;
            border-radius: 0 10px 10px 0;
        }

        .form-control {
            border-left: none;
            border-radius: 10px 0 0 10px;
        }

        .alert {
            border-radius: 10px;
            border: none;
        }

        .login-links {
            text-align: center;
            margin-top: 2rem;
        }

        .login-links a {
            color: #6c757d;
            text-decoration: none;
            margin: 0 1rem;
            transition: color 0.3s ease;
        }

        .login-links a:hover {
            color: #1cc88a;
        }

        .divider {
            text-align: center;
            margin: 2rem 0;
            position: relative;
        }

        .divider::before {
            content: '';
            position: absolute;
            top: 50%;
            left: 0;
            right: 0;
            height: 1px;
            background: #e3e6f0;
        }

        .divider span {
            background: white;
            padding: 0 1rem;
            color: #6c757d;
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <div class="login-container">
        <div class="row g-0">
            <!-- Left Side -->
            <div class="col-lg-6 login-left">
                <div class="h-100 d-flex flex-column justify-content-center">
                    <div class="shop-icon">
                        <i class="fas fa-store"></i>
                    </div>
                    <h2 class="mb-3">لوحة تحكم المتجر</h2>
                    <p class="mb-4">قم بتسجيل الدخول لإدارة متجرك ومتابعة الأداء والتقييمات</p>
                    <div class="features">
                        <div class="feature-item mb-3">
                            <i class="fas fa-chart-line me-2"></i>
                            متابعة أداء المتجر
                        </div>
                        <div class="feature-item mb-3">
                            <i class="fas fa-star me-2"></i>
                            إدارة التقييمات والمراجعات
                        </div>
                        <div class="feature-item mb-3">
                            <i class="fas fa-edit me-2"></i>
                            تحديث بيانات المتجر
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Side -->
            <div class="col-lg-6 login-right">
                <div class="h-100 d-flex flex-column justify-content-center">
                    <div class="text-center mb-4">
                        <h3 class="text-gray-800">تسجيل دخول صاحب المتجر</h3>
                        <p class="text-muted">أدخل بيانات صاحب المتجر للمتابعة</p>
                    </div>

                    <!-- Validation Errors -->
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul class="mb-0">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <!-- Login Form -->
                    <form method="POST" action="{{ route('shop-owner.login') }}">
                        @csrf

                        <!-- Email Address -->
                        <div class="mb-3">
                            <label for="email" class="form-label">البريد الإلكتروني</label>
                            <div class="input-group">
                                <input id="email" 
                                       type="email" 
                                       class="form-control @error('email') is-invalid @enderror" 
                                       name="email" 
                                       value="{{ old('email') }}" 
                                       required 
                                       autocomplete="email" 
                                       autofocus
                                       placeholder="أدخل البريد الإلكتروني">
                                <span class="input-group-text">
                                    <i class="fas fa-envelope"></i>
                                </span>
                            </div>
                            @error('email')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div class="mb-3">
                            <label for="password" class="form-label">كلمة المرور</label>
                            <div class="input-group">
                                <input id="password" 
                                       type="password" 
                                       class="form-control @error('password') is-invalid @enderror" 
                                       name="password" 
                                       required 
                                       autocomplete="current-password"
                                       placeholder="أدخل كلمة المرور">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                            </div>
                            @error('password')
                                <div class="invalid-feedback d-block">
                                    {{ $message }}
                                </div>
                            @enderror
                        </div>

                        <!-- Remember Me -->
                        <div class="mb-3 form-check">
                            <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                            <label class="form-check-label" for="remember">
                                تذكرني
                            </label>
                        </div>

                        <!-- Submit Button -->
                        <div class="d-grid">
                            <button type="submit" class="btn btn-shop-owner btn-success text-white">
                                <i class="fas fa-sign-in-alt me-2"></i>
                                تسجيل الدخول
                            </button>
                        </div>
                    </form>

                    <!-- Divider -->
                    <div class="divider">
                        <span>أو</span>
                    </div>

                    <!-- Login Links -->
                    <div class="login-links">
                        <a href="{{ route('admin.login') }}">
                            <i class="fas fa-user-shield me-1"></i>
                            تسجيل دخول مدير
                        </a>
                        <a href="{{ route('login') }}">
                            <i class="fas fa-user me-1"></i>
                            تسجيل دخول مستخدم
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>