<!DOCTYPE html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- SEO Meta Tags -->
    <title>{{ $seoData['title'] }}</title>
    <meta name="description" content="{{ $seoData['description'] }}">
    <meta name="keywords" content="{{ $seoData['keywords'] }}">
    <meta name="author" content="اكتشف المدن">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ $seoData['canonical'] }}">
    
    <!-- Open Graph Meta Tags -->
    <meta property="og:title" content="{{ $seoData['title'] ?? 'اكتشف المدن' }}">
    <meta property="og:description" content="{{ $seoData['description'] ?? 'اكتشف أفضل المتاجر' }}">
    <meta property="og:image" content="{{ $seoData['og_image'] ?? asset('images/og-discover-cities.jpg') }}">
    <meta property="og:url" content="{{ url('/') }}">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="ar_SA">
    <meta property="og:site_name" content="اكتشف المدن">

    <!-- Arabic Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    
    <!-- Select2 RTL CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2-rtl.css" rel="stylesheet" />

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">
    <link rel="apple-touch-icon" href="{{ asset('apple-touch-icon.png') }}">

    <style>
        :root {
            --primary: #016B61;
            --secondary: #70B2B2;
            --accent: #9ECFD4;
            --light: #E5E9C5;
            --grey-light: #f8f9fa;
            --white: #ffffff;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            font-family: 'Cairo', sans-serif;
            line-height: 1.6;
            color: #333;
            background: var(--white);
            min-height: 100vh;
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Navigation */
        .nav {
            background: white;
            padding: 18px 0;
            position: sticky;
            top: 0;
            z-index: 40;
            box-shadow: 0 2px 10px rgba(1, 107, 97, 0.1);
        }

        .nav .container {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .nav-brand h2 {
            margin: 0;
            color: var(--primary);
            font-weight: 700;
        }

        .nav-links {
            display: flex;
            gap: 14px;
            align-items: center;
        }

        .nav-links a {
            padding: 8px 14px;
            border-radius: 10px;
            color: var(--text);
            font-weight: 600;
            transition: all 0.3s ease;
        }

        .nav-links a:hover {
            background: rgba(1, 107, 97, 0.1);
            color: var(--primary);
        }

        .btn {
            display: inline-block;
            padding: 10px 16px;
            border-radius: 10px;
            font-weight: 700;
            transition: all 0.3s ease;
        }

        .btn-primary {
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            color: white;
        }

        .btn-outline {
            border: 2px solid var(--primary);
            color: var(--primary);
            background: transparent;
        }

        /* Search Header */
        .search-header {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            padding: 40px 0;
        }

        .search-header h1 {
            font-size: 32px;
            margin-bottom: 10px;
        }

        .search-stats {
            display: flex;
            gap: 20px;
            margin-top: 20px;
            flex-wrap: wrap;
        }

        .search-stat {
            background: rgba(255, 255, 255, 0.2);
            padding: 10px 16px;
            border-radius: 12px;
            backdrop-filter: blur(10px);
        }

        /* Search Form */
        .search-form-container {
            background: white;
            padding: 30px 0;
            box-shadow: 0 4px 20px rgba(1, 107, 97, 0.1);
        }

        .search-form {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 20px;
            align-items: center;
        }

        .search-inputs {
            display: grid;
            grid-template-columns: 1fr auto auto;
            gap: 15px;
            align-items: center;
        }

        .search-input, .search-select {
            padding: 12px 16px;
            border: 2px solid rgba(1, 107, 97, 0.2);
            border-radius: 12px;
            font-family: 'Cairo', sans-serif;
            font-size: 16px;
            outline: none;
            transition: all 0.3s ease;
        }

        .search-input:focus, .search-select:focus {
            border-color: var(--primary);
            box-shadow: 0 0 0 3px rgba(1, 107, 97, 0.1);
        }

        .search-btn {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 12px;
            font-family: 'Cairo', sans-serif;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .search-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(1, 107, 97, 0.3);
        }

        /* Results */
        .results-section {
            padding: 40px 0;
        }

        .results-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
            gap: 25px;
            margin-top: 30px;
        }

        .shop-card {
            background: white;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 8px 30px rgba(1, 107, 97, 0.08);
            border: 2px solid rgba(1, 107, 97, 0.05);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .shop-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 50px rgba(1, 107, 97, 0.15);
            border-color: rgba(1, 107, 97, 0.2);
        }

        .shop-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 15px;
        }

        .shop-name {
            font-size: 20px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 5px;
        }

        .shop-category {
            font-size: 14px;
            color: var(--secondary);
            font-weight: 600;
        }

        .shop-rating {
            display: flex;
            align-items: center;
            gap: 5px;
            background: linear-gradient(135deg, #FFD700, #FFA500);
            color: white;
            padding: 6px 12px;
            border-radius: 20px;
            font-size: 14px;
            font-weight: 700;
        }

        .shop-details {
            margin: 15px 0;
        }

        .shop-address, .shop-phone {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 8px 0;
            font-size: 14px;
            color: #666;
        }

        .shop-description {
            color: #555;
            font-size: 14px;
            line-height: 1.5;
            margin: 10px 0;
        }

        .shop-footer {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid rgba(1, 107, 97, 0.1);
        }

        .shop-city {
            color: var(--primary);
            font-weight: 600;
            font-size: 14px;
        }

        .shop-status {
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: 600;
        }

        .status-open {
            background: rgba(34, 197, 94, 0.1);
            color: #059669;
        }

        .status-closed {
            background: rgba(239, 68, 68, 0.1);
            color: #dc2626;
        }

        /* No Results */
        .no-results {
            text-align: center;
            padding: 60px 20px;
            color: #666;
        }

        .no-results-icon {
            font-size: 80px;
            margin-bottom: 20px;
            opacity: 0.5;
        }

        .no-results h3 {
            color: var(--primary);
            margin-bottom: 10px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .search-inputs {
                grid-template-columns: 1fr;
            }

            .search-form {
                grid-template-columns: 1fr;
            }

            .search-stats {
                justify-content: center;
            }

            .results-grid {
                grid-template-columns: 1fr;
            }

            .shop-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
        }
    </style>
</head>

<body>
    <nav class="nav">
        <div class="container">
            <div class="nav-brand">
                <a href="{{url('/') }}">
                    <h2>اكتشف المدن</h2>
                </a>
            </div>

            <div class="nav-links">
                <a href="{{url('/') }}">الرئيسية</a>
                <a href="{{url('/') }}#features">الميزات</a>
                <a href="{{url('/') }}#cities">المدن</a>
                <a href="{{url('/') }}#contact">اتصل بنا</a>
            </div>
        </div>
    </nav>

    <section class="search-header">
        <div class="container">
            <h1>
                @if(!empty($query))
                    نتائج البحث عن "{{ $query }}"
                @else
                    البحث في المتاجر
                @endif
            </h1>
            <p>اكتشف أفضل المتاجر والخدمات المحلية</p>
            
            <div class="search-stats">
                <div class="search-stat">
                    <strong>{{ number_format($stats['total_results']) }}</strong> نتيجة
                </div>
                @if($stats['city_filter'])
                    <div class="search-stat">
                        📍 {{ $stats['city_filter'] }}
                    </div>
                @endif
                @if($stats['category_filter'])
                    <div class="search-stat">
                        🏷️ {{ $stats['category_filter'] }}
                    </div>
                @endif
            </div>
        </div>
    </section>

    <section class="search-form-container">
        <div class="container">
            <form class="search-form auto-submit" action="{{ route('search') }}" method="GET">
                <div class="search-inputs">
                    <input type="text" 
                           name="q" 
                           value="{{ $query }}"
                           placeholder="ابحث عن متجر، فئة أو مدينة..." 
                           class="search-input">
                    
                    <select name="city" class="search-select">
                        <option value="">كل المدن</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" {{ $cityId == $city->id ? 'selected' : '' }}>
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                    
                    <select name="category" class="search-select">
                        <option value="">كل الفئات</option>
                        @foreach(['مطاعم', 'ملابس', 'إلكترونيات', 'صيدليات', 'سوبر ماركت', 'مقاهي'] as $cat)
                            <option value="{{ $cat }}" {{ $category == $cat ? 'selected' : '' }}>
                                {{ $cat }}
                            </option>
                        @endforeach
                    </select>
                </div>
                
                <button type="submit" class="search-btn">
                    🔍 بحث
                </button>
            </form>
        </div>
    </section>

    <section class="results-section">
        <div class="container">
            @if($results->count() > 0)
                <div class="shops-grid">
                    @foreach($results as $shop)
                        <x-shop-card :shop="$shop" :loop="$loop" />
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($results->hasPages())
                    <x-pagination :paginator="$results" />
                @endif
            @else
                <div class="no-results">
                    <div class="no-results-icon">🔍</div>
                    <h3>لم نجد نتائج مطابقة</h3>
                    <p>جرب كلمات بحث مختلفة أو قم بتوسيع نطاق البحث</p>
                    <a href="{{url('/') }}" class="btn btn-primary" style="margin-top: 20px;">
                        العودة للرئيسية
                    </a>
                </div>
            @endif
        </div>
    </section>

    <footer style="background: #063e36; color: #eaf7f3; padding: 30px 0; text-align: center;">
        <div class="container">
            <p>&copy; 2025 اكتشف المدن مصر. جميع الحقوق محفوظة.</p>
        </div>
    </footer>

    <!-- jQuery (required for Select2) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
    
    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    
    <!-- Select2 Arabic Translation -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/ar.js"></script>

    <script>
        // The global Select2 initialization from layout will handle all select elements
        // No additional initialization needed here
    </script>
</body>

</html>