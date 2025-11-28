@extends('layouts.app')

@section('content')
    <!-- City Hero Section -->
    <section class="city-hero">
        <div class="container">
            <div class="city-hero-content"> 
                <div class="city-main-info">
                    <h1 class="city-title">متاجر {{ $city->name }}</h1>
                    <p class="city-description">اكتشف أفضل المتاجر والخدمات في {{ $city->name }}</p>
                    
                    <div class="city-stats">
                        <div class="stat-item">
                            <span class="stat-number">{{ $shops->total() }}</span>
                            <span class="stat-label text-white">متجر</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">{{ collect(['مطاعم', 'ملابس', 'إلكترونيات', 'صيدليات', 'سوبر ماركت', 'مقاهي'])->count() }}</span>
                            <span class="stat-label text-white">فئة</span>
                        </div>
                        <div class="stat-item">
                            <span class="stat-number">4.8</span>
                            <span class="stat-label text-white">متوسط التقييم</span>
                        </div>
                    </div>
                </div>
                
                <div class="city-hero-visual">
                    <div class="city-icon">🏙️</div>
                    <div class="floating-elements">
                        <div class="floating-element" style="top: 20%; left: 10%;">🏪</div>
                        <div class="floating-element" style="top: 60%; right: 15%;">🛍️</div>
                        <div class="floating-element" style="bottom: 30%; left: 20%;">⭐</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- City Content -->
    <section class="city-content">
        <div class="container">
            <div class="city-layout">
                <!-- Filters Sidebar -->
                <aside class="city-sidebar">
                    <div class="filter-card">
                        <h3 class="filter-title">تصفية النتائج</h3>
                        
                        <form method="GET" class="filter-form auto-submit">
                            <!-- Search -->
                            <div class="filter-group">
                                <label class="filter-label">البحث</label>
                                <div class="search-input-wrapper">
                                    <input type="text" name="q" value="{{ request('q') }}" 
                                           class="filter-input search-input" 
                                           placeholder="ابحث باسم المتجر..." />
                                    <i class="search-icon">🔍</i>
                                </div>
                            </div>

                            <!-- Category Filter -->
                            <div class="filter-group">
                                <label class="filter-label">الفئة</label>
                                <select name="category" class="filter-select">
                                    <option value="">كل الفئات</option>
                                    @foreach($categories as $category)
                                        <option value="{{ $category->id }}" 
                                                {{ request('category') == $category->id ? 'selected' : '' }}
                                                style="color: {{ $category->color }}; font-weight: bold;">
                                            {{ $category->icon }} {{ $category->name }}
                                        </option>
                                        @foreach($category->children as $child)
                                            <option value="{{ $child->id }}" 
                                                    {{ request('category') == $child->id ? 'selected' : '' }}
                                                    style="color: {{ $child->color }}; padding-left: 20px;">
                                                &nbsp;&nbsp;&nbsp;&nbsp;{{ $child->icon }} {{ $child->name }}
                                            </option>
                                        @endforeach
                                    @endforeach
                                </select>
                            </div>

                            <!-- Rating Filter -->
                            <div class="filter-group">
                                <label class="filter-label">التقييم</label>
                                <div class="rating-filters">
                                    <label class="rating-option">
                                        <input type="radio" name="rating" value="5" {{ request('rating') == '5' ? 'checked' : '' }}>
                                        <x-rating :rating="5" :show-number="false" size="sm" readonly />
                                    </label>
                                    <label class="rating-option">
                                        <input type="radio" name="rating" value="4" {{ request('rating') == '4' ? 'checked' : '' }}>
                                        <x-rating :rating="4" :show-number="false" size="sm" readonly />
                                    </label>
                                    <label class="rating-option">
                                        <input type="radio" name="rating" value="3" {{ request('rating') == '3' ? 'checked' : '' }}>
                                        <x-rating :rating="3" :show-number="false" size="sm" readonly />
                                    </label>
                                </div>
                            </div>

                            <!-- Filter Actions -->
                            <div class="filter-actions">
                                <button type="submit" class="btn btn-primary filter-btn">
                                    <i class="icon">🔍</i>
                                    تطبيق التصفية
                                </button>
                                <a href="{{ route('city.shops', $city->slug) }}" class="btn btn-outline clear-btn">
                                    <i class="icon">🗑️</i>
                                    مسح الكل
                                </a>
                            </div>
                        </form>
                    </div>

                    <!-- Quick Categories -->
                    <div class="quick-categories">
                        <h4 class="categories-title">الفئات الشائعة</h4>
                        <div class="category-tags">
                            @foreach($categories->take(8) as $category)
                                <a href="?category={{ $category->id }}" 
                                   class="category-tag {{ request('category') == $category->id ? 'active' : '' }}"
                                   style="border-color: {{ $category->color }};">
                                    <span class="tag-icon">{{ $category->icon }}</span>
                                    {{ $category->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </aside>

                <!-- Main Content -->
                <main class="city-main">
                    <!-- Results Header -->
                    <div class="results-header">
                        <div class="results-info">
                            <h2 class="results-title">
                                @if(request('q') || request('category'))
                                    نتائج البحث
                                @else
                                    جميع المتاجر
                                @endif
                            </h2>
                            <p class="results-count">
                                عرض {{ $shops->count() }} من {{ $shops->total() }} متجر
                                @if(request('category'))
                                    في فئة "{{ request('category') }}"
                                @endif
                            </p>
                        </div>
                        
                        <div class="view-options">
                            <button class="view-btn active" data-view="grid" title="عرض شبكي">
                                <i class="icon">⊞</i>
                            </button>
                            <button class="view-btn" data-view="list" title="عرض قائمة">
                                <i class="icon">☰</i>
                            </button>
                        </div>
                    </div>

                    <!-- Shops Grid -->
                    @if($shops->count())
                        <div class="shops-grid" id="shops-container">
                            @foreach($shops as $shop)
                                <x-shop-card :shop="$shop" :loop="$loop" :city="$city" />
                            @endforeach
                        </div>

                        <!-- Pagination -->
                        <x-pagination :paginator="$shops" />
                    @else
                        <div class="no-results">
                            <div class="no-results-icon">🔍</div>
                            <h3>لا توجد نتائج</h3>
                            <p>لم يتم العثور على متاجر تطابق معايير البحث الخاصة بك.</p>
                            <a href="{{ route('city.shops', $city->slug) }}" class="btn btn-primary">
                                عرض جميع المتاجر
                            </a>
                        </div>
                    @endif
                </main>
            </div>
        </div>
    </section>

    <!-- Mobile App Download Section -->
    <section style="background: linear-gradient(135deg, #016B61 0%, #70B2B2 100%); padding: 60px 0; margin-top: 40px;">
        <div class="container">
            <div style="text-align: center; color: white; margin-bottom: 40px;">
                <h2 style="font-size: 2.5rem; font-weight: bold; margin-bottom: 15px; text-shadow: 0 2px 4px rgba(0,0,0,0.1);">
                    حمّل تطبيقنا الآن
                </h2>
                <p style="font-size: 1.2rem; opacity: 0.95; max-width: 600px; margin: 0 auto;">
                    اكتشف المتاجر واحصل على أفضل العروض من خلال تطبيقنا على الهاتف
                </p>
            </div>

            <div style="display: flex; justify-content: center; align-items: center; gap: 20px; flex-wrap: wrap;">
                <!-- App Store Button -->
                <a href="#" 
                   style="display: inline-flex; align-items: center; gap: 15px; background: #000000; color: white; padding: 15px 30px; border-radius: 12px; text-decoration: none; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0,0,0,0.2); min-width: 200px;"
                   onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 6px 20px rgba(0,0,0,0.3)';"
                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0,0,0,0.2)';">
                    <svg style="width: 40px; height: 40px; flex-shrink: 0;" viewBox="0 0 24 24" fill="white">
                        <path d="M18.71,19.5C17.88,20.74 17,21.95 15.66,21.97C14.32,22 13.89,21.18 12.37,21.18C10.84,21.18 10.37,21.95 9.1,22C7.79,22.05 6.8,20.68 5.96,19.47C4.25,17 2.94,12.45 4.7,9.39C5.57,7.87 7.13,6.91 8.82,6.88C10.1,6.86 11.32,7.75 12.11,7.75C12.89,7.75 14.37,6.68 15.92,6.84C16.57,6.87 18.39,7.1 19.56,8.82C19.47,8.88 17.39,10.1 17.41,12.63C17.44,15.65 20.06,16.66 20.09,16.67C20.06,16.74 19.67,18.11 18.71,19.5M13,3.5C13.73,2.67 14.94,2.04 15.94,2C16.07,3.17 15.6,4.35 14.9,5.19C14.21,6.04 13.07,6.7 11.95,6.61C11.8,5.46 12.36,4.26 13,3.5Z"/>
                    </svg>
                    <div style="text-align: right;">
                        <div style="font-size: 0.75rem; opacity: 0.9; margin-bottom: 2px;">متاح على</div>
                        <div style="font-size: 1.25rem; font-weight: bold;">App Store</div>
                    </div>
                </a>

                <!-- Google Play Button -->
                <a href="#" 
                   style="display: inline-flex; align-items: center; gap: 15px; background: linear-gradient(135deg, #34A853 0%, #4285F4 50%, #FBBC04 100%); color: white; padding: 15px 30px; border-radius: 12px; text-decoration: none; transition: all 0.3s ease; box-shadow: 0 4px 15px rgba(0,0,0,0.2); min-width: 200px;"
                   onmouseover="this.style.transform='translateY(-3px)'; this.style.boxShadow='0 6px 20px rgba(0,0,0,0.3)';"
                   onmouseout="this.style.transform='translateY(0)'; this.style.boxShadow='0 4px 15px rgba(0,0,0,0.2)';">
                    <svg style="width: 40px; height: 40px; flex-shrink: 0;" viewBox="0 0 24 24" fill="white">
                        <path d="M3,20.5V3.5C3,2.91 3.34,2.39 3.84,2.15L13.69,12L3.84,21.85C3.34,21.6 3,21.09 3,20.5M16.81,15.12L6.05,21.34L14.54,12.85L16.81,15.12M20.16,10.81C20.5,11.08 20.75,11.5 20.75,12C20.75,12.5 20.53,12.9 20.18,13.18L17.89,14.5L15.39,12L17.89,9.5L20.16,10.81M6.05,2.66L16.81,8.88L14.54,11.15L6.05,2.66Z"/>
                    </svg>
                    <div style="text-align: right;">
                        <div style="font-size: 0.75rem; opacity: 0.9; margin-bottom: 2px;">حمّل من</div>
                        <div style="font-size: 1.25rem; font-weight: bold;">Google Play</div>
                    </div>
                </a>
            </div>

            <!-- App Features -->
            <div style="display: flex; justify-content: center; gap: 40px; margin-top: 50px; flex-wrap: wrap;">
                <div style="text-align: center; color: white;">
                    <div style="font-size: 3rem; margin-bottom: 10px;">📱</div>
                    <div style="font-size: 1rem; font-weight: 600;">واجهة سهلة</div>
                    <div style="font-size: 0.875rem; opacity: 0.9;">تصميم بسيط وسهل الاستخدام</div>
                </div>
                <div style="text-align: center; color: white;">
                    <div style="font-size: 3rem; margin-bottom: 10px;">🔔</div>
                    <div style="font-size: 1rem; font-weight: 600;">إشعارات فورية</div>
                    <div style="font-size: 0.875rem; opacity: 0.9;">احصل على آخر العروض</div>
                </div>
                <div style="text-align: center; color: white;">
                    <div style="font-size: 3rem; margin-bottom: 10px;">⭐</div>
                    <div style="font-size: 1rem; font-weight: 600;">تقييمات موثوقة</div>
                    <div style="font-size: 0.875rem; opacity: 0.9;">آراء حقيقية من المستخدمين</div>
                </div>
                <div style="text-align: center; color: white;">
                    <div style="font-size: 3rem; margin-bottom: 10px;">🎁</div>
                    <div style="font-size: 1rem; font-weight: 600;">عروض حصرية</div>
                    <div style="font-size: 0.875rem; opacity: 0.9;">خصومات خاصة لمستخدمي التطبيق</div>
                </div>
            </div>
        </div>
    </section>
@endsection

@push('scripts')
<script>
    // View Toggle
    document.querySelectorAll('.view-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const view = this.dataset.view;
            const container = document.getElementById('shops-container');
            
            // Update active button
            document.querySelectorAll('.view-btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            
            // Update container class
            if (view === 'list') {
                container.classList.add('list-view');
            } else {
                container.classList.remove('list-view');
            }
        });
    });

    // Favorite Toggle
    async function toggleFavoriteShop(shopId) {
        const btn = event.target.closest('.favorite-btn-small');
        const icon = btn.querySelector('.heart-icon');
        
        event.preventDefault();
        event.stopPropagation();
        
        // Check if user is authenticated
        @guest
            showToast('يجب تسجيل الدخول لاستخدام هذه الميزة', 'warning');
            setTimeout(() => {
                window.location.href = '{{ route("login") }}';
            }, 1500);
            return;
        @endguest
        
        const isFavorite = icon.textContent === '❤️';
        
        // Disable button while processing
        btn.disabled = true;
        
        try {
            const response = await fetch(`/favorites/shops/${shopId}`, {
                method: isFavorite ? 'DELETE' : 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });
            
            const data = await response.json();
            
            if (response.ok && data.success) {
                // Toggle icon
                icon.textContent = isFavorite ? '🤍' : '❤️';
                showToast(data.message, 'success');
            } else {
                // Handle error
                if (response.status === 401) {
                    showToast('يجب تسجيل الدخول لاستخدام هذه الميزة', 'warning');
                    setTimeout(() => {
                        window.location.href = '{{ route("login") }}';
                    }, 1500);
                } else {
                    showToast(data.message || 'حدث خطأ ما', 'error');
                }
            }
        } catch (error) {
            console.error('Error toggling favorite:', error);
            showToast('حدث خطأ في الاتصال', 'error');
        } finally {
            btn.disabled = false;
        }
    }

    function showToast(message, type = 'info') {
        const toast = document.createElement('div');
        const icons = {
            success: '✅',
            error: '❌',
            warning: '⚠️',
            info: 'ℹ️'
        };
        const colors = {
            success: 'linear-gradient(135deg, #10b981, #059669)',
            error: 'linear-gradient(135deg, #ef4444, #dc2626)',
            warning: 'linear-gradient(135deg, #f59e0b, #d97706)',
            info: 'linear-gradient(135deg, #3b82f6, #2563eb)'
        };
        
        toast.innerHTML = `${icons[type]} ${message}`;
        toast.style.cssText = `
            position: fixed;
            bottom: 30px;
            right: 30px;
            background: ${colors[type]};
            color: white;
            padding: 15px 25px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.2);
            z-index: 10000;
            font-weight: bold;
            animation: slideIn 0.3s ease-out;
        `;
        document.body.appendChild(toast);
        setTimeout(() => {
            toast.style.animation = 'slideOut 0.3s ease-out';
            setTimeout(() => document.body.removeChild(toast), 300);
        }, 3000);
    }

    // Get Directions using coordinates or address
    function getDirections(latitude, longitude, address) {
        event.preventDefault();
        event.stopPropagation();
        
        if (latitude && longitude) {
            // Use coordinates for precise location
            window.open(`https://www.google.com/maps/dir/?api=1&destination=${latitude},${longitude}`, '_blank');
        } else if (address) {
            // Fallback to address search
            const encodedAddress = encodeURIComponent(address);
            window.open(`https://www.google.com/maps/search/${encodedAddress}`, '_blank');
        } else {
            alert('عذراً، الموقع غير متوفر');
        }
    }

    // Auto-submit form on filter change
    document.querySelectorAll('.filter-select, input[name="rating"]').forEach(element => {
        element.addEventListener('change', function() {
            this.closest('form').submit();
        });
    });

    // Prevent card overlay link from interfering with action buttons
    document.querySelectorAll('.shop-actions-mini a, .shop-actions-mini button, .favorite-btn-small').forEach(element => {
        element.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    });

    // Image placeholder handling
    function handleImageError(img) {
        img.style.display = 'none';
        const placeholder = img.nextElementSibling;
        if (placeholder && placeholder.classList.contains('shop-image-placeholder')) {
            placeholder.style.display = 'flex';
        }
    }

    // Add error handling to all shop images
    document.querySelectorAll('.shop-image').forEach(img => {
        img.addEventListener('error', function() {
            handleImageError(this);
        });
        
        // Also check if image is already broken
        if (!img.complete || img.naturalWidth === 0) {
            handleImageError(img);
        }
    });

    // Lazy loading for better performance
    if ('IntersectionObserver' in window) {
        const imageObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const img = entry.target;
                    if (img.dataset.src) {
                        img.src = img.dataset.src;
                        img.removeAttribute('data-src');
                        observer.unobserve(img);
                    }
                }
            });
        });

        // Apply lazy loading to images that have data-src
        document.querySelectorAll('img[data-src]').forEach(img => {
            imageObserver.observe(img);
        });
    }
</script>
@endpush
