<nav class="main-nav">
    <div class="container">
        <!-- Top Navigation -->
        <div class="nav-top">
            <div class="nav-brand">
                <a href="{{ url('/') }}">
                    <h2>🏙️ اكتشف المدن</h2>
                </a>
            </div>

            <!-- Search Bar -->
            <div class="nav-search">
                <form action="{{ route('search') }}" method="GET" class="search-form">
                    <div class="search-input-group">
                        <input type="text" 
                               name="q" 
                               placeholder="🔍 ابحث عن متجر، منتج، خدمة، أو فئة..." 
                               value="{{ request('q') }}"
                               class="search-input">
                        <select name="city" class="search-city-select">
                            <option value="">📍 كل المدن</option>
                            @if(isset($navCities))
                                @foreach($navCities as $city)
                                    <option value="{{ $city->slug }}" {{ request('city') == $city->slug ? 'selected' : '' }}>
                                        {{ $city->name }}
                                    </option>
                                @endforeach
                            @endif
                        </select>
                        <button type="submit" class="search-btn">
                            <span>🔍</span>
                        </button>
                    </div>
                </form>
            </div>

            <!-- User Actions -->
            <div class="nav-actions">
                <!-- Selected City Indicator - Clickable -->
                @if(session('selected_city'))
                    <div class="selected-city-indicator clickable-city" onclick="showCityModal()" title="انقر لتغيير المدينة">
                        <span class="city-indicator-icon">📍</span>
                        <span class="city-indicator-name">{{ session('selected_city_name') }}</span>
                        <span class="city-change-arrow">⌄</span>
                    </div>
                @else
                    <div class="no-city-indicator clickable-city" onclick="showCityModal()" title="انقر لاختيار مدينة">
                        <span class="city-indicator-icon">📍</span>
                        <span class="city-indicator-name">اختر مدينة</span>
                        <span class="city-change-arrow">⌄</span>
                    </div>
                @endif
                
                @if (Route::has('login'))
                    @auth
                        <a href="{{ url('/dashboard') }}" class="nav-link">
                            👤 لوحة التحكم
                        </a>
                    @else
                        <a href="{{ route('login') }}" class="nav-link">دخول</a>
                        @if (Route::has('register'))
                            <a href="{{ route('register') }}" class="btn btn-primary">تسجيل</a>
                        @endif
                    @endauth
                @endif
            </div>

            <button class="mobile-menu-toggle" onclick="toggleMobileMenu()">
                <span></span>
                <span></span>
                <span></span>
            </button>
        </div>

        <!-- Categories Navigation -->
        <div class="nav-categories">
            <div class="categories-menu">
                <a href="{{ url('/') }}" class="category-nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
                    🏠 الرئيسية
                </a>
                
                <!-- Debug: Show categories count -->
                {{-- Debug: {{ isset($navCategories) ? $navCategories->count() : 'No categories' }} --}}
                
                @if(isset($navCategories) && $navCategories->count() > 0)
                    @foreach($navCategories as $category)
                        <div class="category-dropdown" data-category="{{ $category->slug }}">
                            <a href="{{ route('category.shops', $category->slug) }}" 
                               class="category-nav-item {{ request('category') == $category->id ? 'active' : '' }}">
                                {{ $category->icon }} {{ $category->name }}
                                @if($category->children && $category->children->count() > 0)
                                    <span class="dropdown-arrow">▼</span>
                                @endif
                            </a>
                            
                            @if($category->children && $category->children->count() > 0)
                                <div class="category-submenu" data-submenu="{{ $category->slug }}">
                                    <div class="submenu-grid">
                                        {{-- Debug: {{ $category->children->count() }} children --}}
                                        @foreach($category->children as $child)
                                            <a href="{{ route('category.shops', $child->slug) }}" 
                                               class="submenu-item">
                                                {{ $child->icon }} {{ $child->name }}
                                            </a>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>
                    @endforeach
                @else
                    <!-- Fallback categories if data is not loaded -->
                    <div class="category-dropdown">
                        <a href="#" class="category-nav-item">
                            🍽️ مطاعم ومأكولات
                            <span class="dropdown-arrow">▼</span>
                        </a>
                        <div class="category-submenu">
                            <div class="submenu-grid">
                                <a href="#" class="submenu-item">🏠 مطاعم شعبية</a>
                                <a href="#" class="submenu-item">🌍 مطاعم عالمية</a>
                                <a href="#" class="submenu-item">🐟 مطاعم أسماك</a>
                                <a href="#" class="submenu-item">🐔 مطاعم فراخ</a>
                            </div>
                        </div>
                    </div>
                @endif

                <a href="{{ route('categories.index') }}" class="category-nav-item {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                    📂 جميع الفئات
                </a>

                <a href="{{ route('cities.index') }}" class="category-nav-item">
                    📍 كل المدن
                </a>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div class="mobile-nav" id="mobileNav">
        <div class="mobile-search">
            <form action="{{ route('search') }}" method="GET">
                <input type="text" name="q" placeholder="ابحث..." value="{{ request('q') }}">
                <button type="submit">🔍</button>
            </form>
        </div>
        
        <div class="mobile-categories">
            <a href="{{ url('/') }}">🏠 الرئيسية</a>
            @if(isset($navCategories))
                @foreach($navCategories as $category)
                    <div class="mobile-category-group">
                        <a href="{{ route('category.shops', $category->slug) }}" class="mobile-category-main">
                            {{ $category->icon }} {{ $category->name }}
                        </a>
                        @if($category->children->count() > 0)
                            <div class="mobile-subcategories">
                                @foreach($category->children as $child)
                                    <a href="{{ route('category.shops', $child->slug) }}">
                                        {{ $child->icon }} {{ $child->name }}
                                    </a>
                                @endforeach
                            </div>
                        @endif
                    </div>
                @endforeach
            @endif
        </div>
    </div>
</nav>
