@extends('layouts.app')

@php
    $seoData = $seoData ?? [];
    $contactInfo = $contactInfo ?? [];
    $cityContext = $cityContext ?? ['should_show_modal' => false, 'is_city_selected' => false];
@endphp

@section('content')

{{-- Simple Working City Selection Modal --}}
@if(!($cityContext['is_city_selected'] ?? false))
    <x-city-modal-simple :show-modal="($cityContext['should_show_modal'] ?? false)" />
@endif

    <main class="hero section-white section-decoration" id="home">
        <!-- Enhanced SVG Decorations -->
        <svg class="decoration-svg decoration-top-right" width="200" height="200" viewBox="0 0 200 200">
            <circle cx="100" cy="100" r="80" fill="var(--accent)" opacity="0.3"/>
            <circle cx="150" cy="50" r="30" fill="var(--secondary)" opacity="0.4"/>
            <circle cx="50" cy="150" r="25" fill="var(--primary)" opacity="0.2"/>
        </svg>
        
        <!-- Floating Particles -->
        <div class="floating-particles">
            <div class="particle particle-1">🌟</div>
            <div class="particle particle-2">✨</div>
            <div class="particle particle-3">🏪</div>
            <div class="particle particle-4">🛍️</div>
            <div class="particle particle-5">⭐</div>
            <div class="particle particle-6">🎯</div>
        </div>
        
        <div class="container">
            {{-- Enhanced City Context Display --}}
            @if($cityContext['is_city_selected'] ?? false)
                <div class="city-context-display mb-4">
                    <div class="selected-city-info bg-white bg-opacity-20 rounded-xl p-4 backdrop-blur-sm border border-white border-opacity-30">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center">
                                <div class="city-icon bg-white bg-opacity-30 rounded-circle p-2 me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                    <span style="font-size: 1.2rem;">📍</span>
                                </div>
                                <div>
                                    <div class="city-name fw-bold text-white" style="font-size: 1.1rem;">{{ $cityContext['selected_city_name'] ?? '' }}</div>
                                    <small class="text-white-50">المدينة المختارة حالياً</small>
                                </div>
                            </div>
                            <button onclick="showCityModal()" class="change-city-btn btn btn-light btn-sm px-3 py-2">
                                <i class="fas fa-exchange-alt me-1"></i>
                                تغيير المدينة
                            </button>
                        </div>
                        <div class="mt-3 pt-3 border-top border-white border-opacity-30">
                            <small class="text-white-75">
                                <i class="fas fa-info-circle me-1"></i>
                                يتم عرض المحتوى الخاص بمدينة {{ $cityContext['selected_city_name'] ?? '' }} فقط
                            </small>
                        </div>
                    </div>
                </div>
            @endif
            
            <div class="hero-content fade-in">
                <div class="hero-text">
                    @if($cityContext['is_city_selected'] ?? false)
                        <h1>اكتشف أفضل المتاجر في {{ $cityContext['selected_city_name'] ?? 'مدينتك' }}</h1>
                        <p>استعرض مئات المتاجر والخدمات المحلية في {{ $cityContext['selected_city_name'] ?? 'مدينتك' }}. اقرأ التقييمات، اكتشف العروض، واحصل على أفضل الصفقات.</p>
                    @else
                        <h1>اكتشف أفضل المتاجر في مدينتك</h1>
                        <p>منصة متكاملة للعثور على المتاجر، العروض والتقييمات في جمهورية مصر العربية — مصممة خصيصاً للمستخدم المصري.</p>
                    @endif
                    
                    <!-- Dynamic Statistics -->
                    <div class="hero-stats">
                        <div class="hero-stat">
                            <div class="hero-stat-number">{{ number_format($stats['total_cities'] ?? 8) }}</div>
                            <div class="hero-stat-label">مدينة</div>
                        </div>
                        <div class="hero-stat">
                            <div class="hero-stat-number">{{ number_format($stats['total_shops'] ?? 1000) }}+</div>
                            <div class="hero-stat-label">متجر</div>
                        </div>
                        <div class="hero-stat">
                            <div class="hero-stat-number">{{ number_format($stats['total_categories'] ?? 8) }}</div>
                            <div class="hero-stat-label">فئة</div>
                        </div>
                    </div>
                    
                    <!-- Amazing Search Form -->
                    <div class="hero-search">
                        <form class="search-form hero-search" action="{{ route('search') }}" method="GET">
                            <div class="search-container">
                                <div class="search-input-group">
                                    <div class="search-icon">🔍</div>
                                    <input type="text" 
                                           name="q" 
                                           id="main-search" 
                                           placeholder="ابحث عن متجر، فئة أو مدينة..." 
                                           autocomplete="off"
                                           class="search-input">
                                    <div class="search-suggestions" id="search-suggestions"></div>
                                </div>
                                <div class="search-filters">
                                    <select name="city" class="search-select">
                                        <option value="">كل المدن</option>
                                        @foreach($cities ?? [] as $city)
                                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                                        @endforeach
                                    </select>
                                    <select name="category" class="search-select">
                                        <option value="">كل الفئات</option>
                                        @foreach(['مطاعم', 'ملابس', 'إلكترونيات', 'صيدليات', 'سوبر ماركت', 'مقاهي'] as $category)
                                            <option value="{{ $category }}">{{ $category }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <button type="submit" class="search-btn">
                                    <span>بحث</span>
                                    <div class="search-btn-icon">🚀</div>
                                </button>
                            </div>
                        </form>
                        
                        <!-- Quick Search Tags -->
                        <div class="search-quick-tags">
                            <span class="search-tag" onclick="quickSearch('مطاعم')">🍽️ مطاعم</span>
                            <span class="search-tag" onclick="quickSearch('ملابس')">👕 ملابس</span>
                            <span class="search-tag" onclick="quickSearch('صيدليات')">💊 صيدليات</span>
                            <span class="search-tag" onclick="quickSearch('سوبر ماركت')">🛒 سوبر ماركت</span>
                            <span class="search-tag" onclick="quickSearch('مقاهي')">☕ مقاهي</span>
                        </div>
                    </div>

                    <div class="hero-buttons">
                        <a class="btn btn-primary" href="#cities">ابدأ الاستكشاف</a>
                        <a class="btn btn-outline" href="#features">تعرف أكثر</a>
                    </div>
                </div>

                <div>
                    <div class="phone-mockup">
                        <div class="phone-screen">
                            <div class="search-bar">ابحث عن متجر، فئة أو مدينة</div>
                            <div class="shop-card">
                                <div class="shop-image"></div>
                                <div class="shop-info">
                                    @if($sampleShop ?? null)
                                        <h4>{{ $sampleShop->name }}</h4>
                                        <p>{{ $sampleShop->city->name }} • متاح الآن</p>
                                    @else
                                        <h4>متجر الأزياء العصرية</h4>
                                        <p>القاهرة • مفتوح الآن</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    {{-- Banner Advertisement after Hero --}}
    <section class="py-4 bg-light">
        <div class="container">
            <x-ad-display type="banner" placement="homepage" :city-id="$cityContext['selected_city_id'] ?? null" />
        </div>
    </section>

    <section id="features" class="features section-grey section-decoration">
        <!-- SVG Decoration -->
        <svg class="decoration-svg decoration-bottom-left" width="150" height="150" viewBox="0 0 150 150">
            <path d="M10,10 Q75,50 140,10 Q75,100 10,140 Z" fill="var(--light)" opacity="0.6" />
            <circle cx="75" cy="75" r="20" fill="var(--accent)" opacity="0.5" />
        </svg>
        <div class="container">
            <div class="section-header">
                <h2>لماذا تختار منصتنا؟</h2>
                <p>نوفر لك تجربة فريدة لاستكشاف المتاجر والخدمات المحلية</p>
            </div>

            <div class="features-grid">
                <div class="feature-card fade-in">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M15.5 14h-.79l-.28-.27C15.41 12.59 16 11.11 16 9.5 16 5.91 13.09 3 9.5 3S3 5.91 3 9.5 5.91 16 9.5 16c1.61 0 3.09-.59 4.23-1.57l.27.28v.79l5 4.99L20.49 19l-4.99-5zm-6 0C7.01 14 5 11.99 5 9.5S7.01 5 9.5 5 14 7.01 14 9.5 11.99 14 9.5 14z" />
                        </svg>
                    </div>
                    <h3>بحث ذكي</h3>
                    <p>ابحث باستخدام الموقع، الفئة أو اسم المتجر بسهولة وسرعة.</p>
                </div>
                <div class="feature-card fade-in">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" />
                        </svg>
                    </div>
                    <h3>تحديد الموقع</h3>
                    <p>اعثر على أقرب المتاجر مع خرائط وتوجيه خطوة بخطوة.</p>
                </div>
                <div class="feature-card fade-in">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M12 17.27L18.18 21l-1.64-7.03L22 9.24l-7.19-.61L12 2 9.19 8.63 2 9.24l5.46 4.73L5.82 21z" />
                        </svg>
                    </div>
                    <h3>تقييمات موثوقة</h3>
                    <p>اعتماد شفاف لتقييمات المستخدمين لتسهيل اختيارك.</p>
                </div>
                <div class="feature-card fade-in">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M20 2H4c-1.1 0-1.99.9-1.99 2L2 22l4-4h14c1.1 0 2-.9 2-2V4c0-1.1-.9-2-2-2zm-2 12H6v-2h12v2zm0-3H6V9h12v2zm0-3H6V6h12v2z" />
                        </svg>
                    </div>
                    <h3>دعم مباشر</h3>
                    <p>دعم فني ومجتمع نشط لمساعدتك وقت الحاجة.</p>
                </div>
                <div class="feature-card fade-in">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z" />
                        </svg>
                    </div>
                    <h3>عروض وخصومات</h3>
                    <p>احصل على أفضل العروض الحصرية من المتاجر المشاركة.</p>
                </div>
                <div class="feature-card fade-in">
                    <div class="feature-icon">
                        <svg viewBox="0 0 24 24" fill="currentColor">
                            <path
                                d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z" />
                        </svg>
                    </div>
                    <h3>آمن وموثوق</h3>
                    <p>نضمن سرية بياناتك وموثوقية البائعين.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="cities" class="cities section-white section-decoration">
        <!-- SVG Decoration -->
        <svg class="decoration-svg decoration-center" width="180" height="180" viewBox="0 0 180 180">
            <rect x="20" y="20" width="30" height="40" fill="var(--primary)" opacity="0.3" rx="5" />
            <rect x="60" y="10" width="25" height="50" fill="var(--secondary)" opacity="0.4" rx="5" />
            <rect x="95" y="25" width="35" height="35" fill="var(--accent)" opacity="0.5" rx="5" />
            <rect x="140" y="15" width="20" height="45" fill="var(--light)" opacity="0.6" rx="5" />
        </svg>

        <div class="container">
            <div class="section-header">
                <h2>المدن المتاحة</h2>
                <p>اكتشف المتاجر في أهم المدن المصرية</p>
            </div>

            <div class="cities-grid">
                @forelse($cities as $city)
                    <div class="city-card" data-city-slug="{{ $city->slug }}">
                        <div class="city-image"
                            style="background-image: url('{{ $city->image ? asset('storage/' . $city->image) : asset('images/default-city.jpg') }}'); background-size: cover; background-position: center;">
                        </div>
                        <div class="city-info">
                            <h3>{{ $city->name }}</h3>
                            <p>{{ number_format($city->active_shops_count) }}{{ $city->active_shops_count > 0 ? '+' : '' }} متجر
                            </p>
                            <a class="btn btn-primary" href="{{ route('city.shops', $city->slug) }}">استكشف الآن</a>
                        </div>
                    </div>
                @empty
                    <!-- Fallback: Egypt's New Cities -->
                    <div class="city-card">
                        <div class="city-image" style="background: linear-gradient(135deg, var(--secondary), var(--accent));">
                        </div>
                        <div class="city-info">
                            <h3>العاصمة الإدارية الجديدة</h3>
                            <p>قريباً</p>
                            <a class="btn btn-primary" href="#" onclick="alert('قريباً سيتم إضافة المدن الجديدة')">استكشف
                                الآن</a>
                        </div>
                    </div>
                    <div class="city-card">
                        <div class="city-image" style="background: linear-gradient(135deg, var(--secondary), var(--accent));">
                        </div>
                        <div class="city-info">
                            <h3>العلمين الجديدة</h3>
                            <p>قريباً</p>
                            <a class="btn btn-primary" href="#" onclick="alert('قريباً سيتم إضافة المدن الجديدة')">استكشف
                                الآن</a>
                        </div>
                    </div>
                    <div class="city-card">
                        <div class="city-image" style="background: linear-gradient(135deg, var(--secondary), var(--accent));">
                        </div>
                        <div class="city-info">
                            <h3>القاهرة الجديدة</h3>
                            <p>قريباً</p>
                            <a class="btn btn-primary" href="#" onclick="alert('قريباً سيتم إضافة المدن الجديدة')">استكشف
                                الآن</a>
                        </div>
                    </div>
                    <div class="city-card">
                        <div class="city-image" style="background: linear-gradient(135deg, var(--secondary), var(--accent));">
                        </div>
                        <div class="city-info">
                            <h3>مدينة الشيخ زايد</h3>
                            <p>قريباً</p>
                            <a class="btn btn-primary" href="#" onclick="alert('قريباً سيتم إضافة المدن الجديدة')">استكشف
                                الآن</a>
                        </div>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    {{-- Banner Advertisement between sections --}}
    <section class="py-4 bg-white">
        <div class="container">
            <x-ad-display type="banner" placement="homepage" :city-id="$cityContext['selected_city_id'] ?? null" />
        </div>
    </section>

    <!-- How It Works Section -->
    <section id="how-it-works" class="how-it-works section-white section-decoration">
        <!-- SVG Decoration -->
        <svg class="decoration-svg decoration-bottom-left" width="160" height="160" viewBox="0 0 160 160">
            <circle cx="80" cy="80" r="70" fill="var(--light)" opacity="0.4"/>
            <path d="M40,40 L120,40 L120,120 L40,120 Z" fill="var(--accent)" opacity="0.3"/>
        </svg>
        
        <div class="container">
            <div class="section-header">
                <h2>كيف يعمل الموقع؟</h2>
                <p>خطوات بسيطة للوصول لأفضل المتاجر</p>
            </div>

            <div class="steps-grid">
                <div class="step-card fade-in">
                    <div class="step-number">١</div>
                    <div class="step-icon">🔍</div>
                    <h3>ابحث عن متجرك</h3>
                    <p>استخدم البحث المتقدم للعثور على المتاجر حسب الموقع، الفئة، أو الاسم</p>
                </div>
                <div class="step-card fade-in">
                    <div class="step-number">٢</div>
                    <div class="step-icon">📍</div>
                    <h3>اختر الأقرب إليك</h3>
                    <p>شاهد المسافة، ساعات العمل، والتقييمات لتختار المتجر المناسب</p>
                </div>
                <div class="step-card fade-in">
                    <div class="step-number">٣</div>
                    <div class="step-icon">🛍️</div>
                    <h3>زور وتسوق</h3>
                    <p>احصل على الاتجاهات واستمتع بتجربة تسوق رائعة</p>
                </div>
                <div class="step-card fade-in">
                    <div class="step-number">٤</div>
                    <div class="step-icon">⭐</div>
                    <h3>قيم تجربتك</h3>
                    <p>شارك تقييمك لمساعدة المستخدمين الآخرين</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Statistics Section -->
    <section id="stats" class="stats section-grey">
        <div class="container">
            <div class="stats-grid">
                <div class="stat-item fade-in">
                    <div class="stat-icon">🏪</div>
                    <div class="stat-number">{{ number_format($stats['total_shops'] ?? 1000) }}+</div>
                    <div class="stat-label">متجر معتمد</div>
                </div>
                <div class="stat-item fade-in">
                    <div class="stat-icon">🏙️</div>
                    <div class="stat-number">{{ number_format($stats['total_cities'] ?? 8) }}</div>
                    <div class="stat-label">مدينة مغطاة</div>
                </div>
                <div class="stat-item fade-in">
                    <div class="stat-icon">👥</div>
                    <div class="stat-number">50,000+</div>
                    <div class="stat-label">مستخدم نشط</div>
                </div>
                <div class="stat-item fade-in">
                    <div class="stat-icon">⭐</div>
                    <div class="stat-number">4.8</div>
                    <div class="stat-label">تقييم المستخدمين</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Popular Categories Section -->
    <section id="categories" class="categories section-white section-decoration">
        <!-- SVG Decoration -->
        <svg class="decoration-svg decoration-center" width="200" height="200" viewBox="0 0 200 200">
            <rect x="40" y="40" width="120" height="120" fill="var(--primary)" opacity="0.1" rx="20"/>
            <circle cx="100" cy="60" r="25" fill="var(--secondary)" opacity="0.3"/>
            <circle cx="70" cy="140" r="20" fill="var(--accent)" opacity="0.4"/>
            <circle cx="130" cy="140" r="20" fill="var(--light)" opacity="0.5"/>
        </svg>
        
        <div class="container">
            <div class="section-header">
                <h2>الفئات الشائعة</h2>
                <p>اكتشف أشهر فئات المتاجر في مصر</p>
            </div>

            <div class="categories-grid">
                <a href="{{ route('search', ['category' => 'مطاعم']) }}" class="category-card fade-in">
                    <div class="category-icon">🍽️</div>
                    <h3>مطاعم ومقاهي</h3>
                    <p>أشهى الأطباق والمشروبات</p>
                    <div class="category-count">500+ متجر</div>
                </a>
                <a href="{{ route('search', ['category' => 'ملابس']) }}" class="category-card fade-in">
                    <div class="category-icon">👕</div>
                    <h3>ملابس وأزياء</h3>
                    <p>أحدث صيحات الموضة</p>
                    <div class="category-count">300+ متجر</div>
                </a>
                <a href="{{ route('search', ['category' => 'إلكترونيات']) }}" class="category-card fade-in">
                    <div class="category-icon">📱</div>
                    <h3>إلكترونيات</h3>
                    <p>أجهزة وتقنيات حديثة</p>
                    <div class="category-count">200+ متجر</div>
                </a>
                <a href="{{ route('search', ['category' => 'صيدليات']) }}" class="category-card fade-in">
                    <div class="category-icon">💊</div>
                    <h3>صيدليات</h3>
                    <p>أدوية ومنتجات صحية</p>
                    <div class="category-count">150+ متجر</div>
                </a>
                <a href="{{ route('search', ['category' => 'سوبر ماركت']) }}" class="category-card fade-in">
                    <div class="category-icon">🛒</div>
                    <h3>سوبر ماركت</h3>
                    <p>احتياجات يومية ومنزلية</p>
                    <div class="category-count">100+ متجر</div>
                </a>
                <a href="{{ route('search', ['category' => 'مجوهرات']) }}" class="category-card fade-in">
                    <div class="category-icon">💎</div>
                    <h3>مجوهرات</h3>
                    <p>ذهب ومجوهرات راقية</p>
                    <div class="category-count">80+ متجر</div>
                </a>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="testimonials section-grey section-decoration">
        <!-- SVG Decoration -->
        <svg class="decoration-svg decoration-top-right" width="180" height="180" viewBox="0 0 180 180">
            <path d="M20,90 Q90,20 160,90 Q90,160 20,90" fill="var(--accent)" opacity="0.2"/>
            <circle cx="90" cy="90" r="40" fill="var(--primary)" opacity="0.1"/>
        </svg>
        
        <div class="container">
            <div class="section-header">
                <h2>آراء المستخدمين</h2>
                <p>ماذا يقول عملاؤنا عنا</p>
            </div>

            <div class="testimonials-grid">
                <div class="testimonial-card fade-in">
                    <div class="testimonial-content">
                        <div class="quote-icon">💬</div>
                        <p>"منصة رائعة ساعدتني في العثور على أفضل المطاعم في منطقتي. التقييمات دقيقة والمعلومات محدثة."</p>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">👤</div>
                        <div>
                            <div class="author-name">أحمد محمد</div>
                            <div class="author-location">القاهرة الجديدة</div>
                        </div>
                    </div>
                    <div class="testimonial-rating">
                        <x-rating :rating="5" :show-number="false" size="sm" />
                    </div>
                </div>

                <div class="testimonial-card fade-in">
                    <div class="testimonial-content">
                        <div class="quote-icon">💬</div>
                        <p>"كصاحب متجر، الموقع ساعدني في الوصول لعملاء جدد وزيادة المبيعات بشكل ملحوظ."</p>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">👤</div>
                        <div>
                            <div class="author-name">فاطمة أحمد</div>
                            <div class="author-location">العاصمة الإدارية</div>
                        </div>
                    </div>
                    <div class="testimonial-rating">
                        <x-rating :rating="5" :show-number="false" size="sm" />
                    </div>
                </div>

                <div class="testimonial-card fade-in">
                    <div class="testimonial-content">
                        <div class="quote-icon">💬</div>
                        <p>"واجهة سهلة الاستخدام ومعلومات دقيقة. أصبح البحث عن المتاجر أسهل بكثير."</p>
                    </div>
                    <div class="testimonial-author">
                        <div class="author-avatar">👤</div>
                        <div>
                            <div class="author-name">محمد عبدالله</div>
                            <div class="author-location">التجمع الخامس</div>
                        </div>
                    </div>
                    <div class="testimonial-rating">
                        <x-rating :rating="4.5" :show-number="false" size="sm" />
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- For Business Section -->
    <section id="for-business" class="for-business section-white section-decoration">
        <!-- SVG Decoration -->
        <svg class="decoration-svg decoration-bottom-left" width="140" height="140" viewBox="0 0 140 140">
            <rect x="20" y="20" width="100" height="100" fill="var(--secondary)" opacity="0.2" rx="15"/>
            <circle cx="70" cy="35" r="15" fill="var(--primary)" opacity="0.3"/>
            <rect x="45" y="55" width="50" height="8" fill="var(--accent)" opacity="0.4" rx="4"/>
            <rect x="45" y="70" width="30" height="8" fill="var(--light)" opacity="0.5" rx="4"/>
        </svg>
        
        <div class="container">
            <div class="business-content">
                <div class="business-text">
                    <h2>هل تملك متجراً؟</h2>
                    <p>انضم إلى منصتنا وصل لآلاف العملاء المحتملين</p>
                    
                    <div class="business-features">
                        <div class="business-feature">
                            <div class="feature-icon">📈</div>
                            <div>
                                <h4>زيادة المبيعات</h4>
                                <p>وصول أوسع للعملاء المحتملين</p>
                            </div>
                        </div>
                        <div class="business-feature">
                            <div class="feature-icon">⭐</div>
                            <div>
                                <h4>بناء السمعة</h4>
                                <p>تقييمات وآراء إيجابية من العملاء</p>
                            </div>
                        </div>
                        <div class="business-feature">
                            <div class="feature-icon">📊</div>
                            <div>
                                <h4>إحصائيات مفصلة</h4>
                                <p>تتبع الزيارات والتفاعل مع متجرك</p>
                            </div>
                        </div>
                    </div>
                    
                    <div class="business-buttons">
                        <a class="btn btn-primary" href="#">سجل متجرك مجاناً</a>
                        <a class="btn btn-outline" href="#">تعرف على الباقات</a>
                    </div>
                </div>
                
                <div class="business-image">
                    <div class="business-mockup">
                        <div class="mockup-screen">
                            <div class="mockup-header">لوحة تحكم المتجر</div>
                            <div class="mockup-stats">
                                <div class="mockup-stat">
                                    <div class="stat-value">150</div>
                                    <div class="stat-label">زيارة اليوم</div>
                                </div>
                                <div class="mockup-stat">
                                    <div class="stat-value">4.8</div>
                                    <div class="stat-label">التقييم</div>
                                </div>
                            </div>
                            <div class="mockup-chart">📊</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="faq section-grey">
        <div class="container">
            <div class="section-header">
                <h2>الأسئلة الشائعة</h2>
                <p>إجابات للأسئلة الأكثر تكراراً</p>
            </div>

            <div class="faq-grid">
                <div class="faq-item fade-in">
                    <div class="faq-question">
                        <h4>هل الموقع مجاني للاستخدام؟</h4>
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>نعم، الموقع مجاني تماماً للمستخدمين. يمكنك البحث وتصفح المتاجر دون أي رسوم.</p>
                    </div>
                </div>

                <div class="faq-item fade-in">
                    <div class="faq-question">
                        <h4>كيف يمكنني إضافة متجري؟</h4>
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>يمكنك تسجيل متجرك مجاناً من خلال النقر على "تسجيل متجر" وملء البيانات المطلوبة.</p>
                    </div>
                </div>

                <div class="faq-item fade-in">
                    <div class="faq-question">
                        <h4>هل المعلومات محدثة؟</h4>
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>نعم، نحرص على تحديث المعلومات بانتظام ونتحقق من دقة البيانات المعروضة.</p>
                    </div>
                </div>

                <div class="faq-item fade-in">
                    <div class="faq-question">
                        <h4>كيف يمكنني الإبلاغ عن خطأ؟</h4>
                        <span class="faq-toggle">+</span>
                    </div>
                    <div class="faq-answer">
                        <p>يمكنك التواصل معنا عبر البريد الإلكتروني أو نموذج الاتصال لإبلاغنا عن أي أخطاء.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Newsletter Section -->
    <section id="newsletter" class="newsletter section-primary">
        <div class="container">
            <div class="newsletter-content">
                <div class="newsletter-text">
                    <h2>اشترك في نشرتنا الإخبارية</h2>
                    <p>احصل على آخر الأخبار والعروض الحصرية</p>
                </div>
                <div class="newsletter-form">
                    <form class="newsletter-subscribe">
                        <input type="email" placeholder="أدخل بريدك الإلكتروني" class="newsletter-input">
                        <button type="submit" class="newsletter-btn">اشتراك</button>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Final CTA Section -->
    <section class="cta section-grey section-decoration">
        <!-- SVG Decoration -->
        <svg class="decoration-svg decoration-top-right" width="120" height="120" viewBox="0 0 120 120">
            <polygon points="60,10 90,40 60,70 30,40" fill="var(--accent)" opacity="0.4" />
            <circle cx="60" cy="40" r="15" fill="var(--primary)" opacity="0.3" />
        </svg>
        <div class="container">
            <div class="cta-content">
                <div>
                    <h2>ابدأ رحلة الاستكشاف الآن</h2>
                    <p>انضم إلى آلاف المستخدمين واكتشف أفضل المتاجر في مدينتك.</p>
                </div>
                <div style="display:flex;gap:12px;align-items:center">
                    <a class="btn btn-primary" href="#cities">ابدأ الاستكشاف</a>
                    <a class="btn btn-outline" href="#">حمل التطبيق</a>
                </div>
            </div>
        </div>
    </section>
@endsection

    @push('scripts')
        <script>
            function toggleMenu() {
                document.querySelector('.nav-links').classList.toggle('active');
            }

            // Smooth scrolling
            document.querySelectorAll('a[href^="#"]').forEach(a => {
                a.addEventListener('click', function (e) {
                    const href = this.getAttribute('href');
                    if (href.length > 1) {
                        e.preventDefault();
                        const el = document.querySelector(href);
                        if (el) el.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                });
            });

            // Fade-in animation on scroll
            const observerOptions = {
                threshold: 0.1,
                rootMargin: '0px 0px -50px 0px'
            };

            const observer = new IntersectionObserver((entries) => {
                entries.forEach(entry => {
                    if (entry.isIntersecting) {
                        entry.target.classList.add('visible');
                    }
                });
            }, observerOptions);

            // Observe all fade-in elements
            document.addEventListener('DOMContentLoaded', () => {
                const fadeInElements = document.querySelectorAll('.fade-in');
                fadeInElements.forEach(el => {
                    observer.observe(el);
                });

                // Initial animation for hero
                setTimeout(() => {
                    const heroFade = document.querySelector('.hero .fade-in');
                    if (heroFade) heroFade.classList.add('visible');
                }, 200);
                
                // FAQ functionality
                const faqItems = document.querySelectorAll('.faq-item');
                faqItems.forEach(item => {
                    const question = item.querySelector('.faq-question');
                    question.addEventListener('click', () => {
                        // Close other items
                        faqItems.forEach(otherItem => {
                            if (otherItem !== item && otherItem.classList.contains('active')) {
                                otherItem.classList.remove('active');
                            }
                        });
                        // Toggle current item
                        item.classList.toggle('active');
                    });
                });
                
                // Newsletter form
                const newsletterForm = document.querySelector('.newsletter-subscribe');
                if (newsletterForm) {
                    newsletterForm.addEventListener('submit', (e) => {
                        e.preventDefault();
                        const email = newsletterForm.querySelector('.newsletter-input').value;
                        if (email) {
                            alert('شكراً لك! تم تسجيل بريدك الإلكتروني بنجاح.');
                            newsletterForm.reset();
                        }
                    });
                }
                
                // Animate statistics on scroll
                const statNumbers = document.querySelectorAll('.stat-number');
                const statObserver = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            animateNumber(entry.target);
                        }
                    });
                }, { threshold: 0.5 });
                
                statNumbers.forEach(stat => {
                    statObserver.observe(stat);
                });
            });

            // Parallax effect for decorations
            window.addEventListener('scroll', () => {
                const scrolled = window.pageYOffset;
                const parallaxElements = document.querySelectorAll('.decoration-svg');

                parallaxElements.forEach((element, index) => {
                    const speed = 0.5 + (index * 0.1);
                    element.style.transform = `translateY(${scrolled * speed}px)`;
                });
            });
            
            // Animate numbers
            function animateNumber(element) {
                if (element.dataset.animated) return;
                element.dataset.animated = true;
                
                const text = element.textContent;
                const number = parseInt(text.replace(/[^0-9]/g, ''));
                const suffix = text.replace(/[0-9]/g, '');
                
                if (isNaN(number)) return;
                
                let current = 0;
                const increment = number / 50;
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= number) {
                        element.textContent = number.toLocaleString() + suffix;
                        clearInterval(timer);
                    } else {
                        element.textContent = Math.floor(current).toLocaleString() + suffix;
                    }
                }, 30);
            }

            // Amazing Search Functionality
            function quickSearch(query) {
                const searchInput = document.getElementById('main-search');
                if (!searchInput) return;
                searchInput.value = query;
                searchInput.focus();

                // Add a nice animation effect
                searchInput.style.background = 'rgba(1, 107, 97, 0.05)';
                setTimeout(() => {
                    searchInput.style.background = 'transparent';
                }, 300);
            }

            // Search suggestions data (in real app, this would come from API)
            const searchData = [
                { type: 'متجر', name: 'مطعم الأهرامات', city: 'العاصمة الإدارية الجديدة', icon: '🍽️' },
                { type: 'متجر', name: 'صيدلية النيل', city: 'العلمين الجديدة', icon: '💊' },
                { type: 'متجر', name: 'بوتيك الأزياء', city: 'القاهرة الجديدة', icon: '👕' },
                { type: 'فئة', name: 'مطاعم', icon: '🍽️' },
                { type: 'فئة', name: 'ملابس', icon: '👕' },
                { type: 'فئة', name: 'صيدليات', icon: '💊' },
                { type: 'فئة', name: 'سوبر ماركت', icon: '🛒' },
                { type: 'فئة', name: 'مقاهي', icon: '☕' },
                { type: 'مدينة', name: 'العاصمة الإدارية الجديدة', icon: '🏙️' },
                { type: 'مدينة', name: 'العلمين الجديدة', icon: '🏙️' },
                { type: 'مدينة', name: 'القاهرة الجديدة', icon: '🏙️' },
                { type: 'مدينة', name: 'مدينة الشيخ زايد', icon: '🏙️' }
            ];

            // Search input handler
            document.addEventListener('DOMContentLoaded', () => {
                const searchInput = document.getElementById('main-search');
                const suggestionsDiv = document.getElementById('search-suggestions');
                if (!searchInput || !suggestionsDiv) return;
                let searchTimeout;

                searchInput.addEventListener('input', (e) => {
                    const query = e.target.value.trim();

                    clearTimeout(searchTimeout);

                    if (query.length < 2) {
                        suggestionsDiv.style.display = 'none';
                        return;
                    }

                    searchTimeout = setTimeout(() => {
                        showSuggestions(query, suggestionsDiv);
                    }, 200);
                });

                // Hide suggestions when clicking outside
                document.addEventListener('click', (e) => {
                    if (!e.target.closest('.search-input-group')) {
                        suggestionsDiv.style.display = 'none';
                    }
                });

                // Show suggestions when input is focused and has value
                searchInput.addEventListener('focus', () => {
                    if (searchInput.value.trim().length >= 2) {
                        showSuggestions(searchInput.value.trim(), suggestionsDiv);
                    }
                });
            });

            function showSuggestions(query, suggestionsDiv) {
                const filtered = searchData.filter(item =>
                    item.name.includes(query) ||
                    (item.city && item.city.includes(query))
                );

                if (filtered.length === 0) {
                    suggestionsDiv.style.display = 'none';
                    return;
                }

                suggestionsDiv.innerHTML = filtered.slice(0, 6).map(item => `
                    <div class="search-suggestion" onclick="selectSuggestion('${item.name.replace("'", "\'")}')">
                        <span style="font-size: 16px;">${item.icon}</span>
                        <div>
                            <div style="font-weight: 600; color: var(--primary);">${item.name}</div>
                            ${item.city ? `<div style="font-size: 12px; color: #6a786f;">${item.city}</div>` : ''}
                            <div style="font-size: 11px; color: var(--secondary); margin-top: 2px;">${item.type}</div>
                        </div>
                    </div>
                `).join('');

                suggestionsDiv.style.display = 'block';
            }

            function selectSuggestion(suggestion) {
                const searchInput = document.getElementById('main-search');
                const suggestionsDiv = document.getElementById('search-suggestions');
                if (!searchInput) return;

                searchInput.value = suggestion;
                if (suggestionsDiv) suggestionsDiv.style.display = 'none';

                // Add selection animation
                searchInput.style.background = 'rgba(1, 107, 97, 0.1)';
                setTimeout(() => {
                    searchInput.style.background = 'transparent';
                }, 500);
            }

            // Enhanced search form animation
            document.addEventListener('DOMContentLoaded', () => {
                const searchContainer = document.querySelector('.search-container');
                if (!searchContainer) return;

                // Add floating animation on hover
                searchContainer.addEventListener('mouseenter', () => {
                    searchContainer.style.animation = 'searchFloat 2s ease-in-out infinite';
                });

                searchContainer.addEventListener('mouseleave', () => {
                    searchContainer.style.animation = 'none';
                });
            });

            // Add CSS animation for search float
            const style = document.createElement('style');
            style.textContent = `
                @keyframes searchFloat {
                    0%, 100% { transform: translateY(-2px) scale(1); }
                    50% { transform: translateY(-5px) scale(1.01); }
                }
            `;
            document.head.appendChild(style);
        </script>
    @endpush