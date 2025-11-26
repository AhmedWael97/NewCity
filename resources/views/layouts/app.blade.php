<!doctype html>
<html lang="ar" dir="rtl">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ data_get($seoData, 'title', 'SENÚ سنو') }}</title>
    <meta name="description" content="{{ data_get($seoData, 'description', 'منصة لاكتشاف المتاجر والخدمات المحلية') }}">
    <meta name="keywords" content="{{ data_get($seoData, 'keywords', 'متاجر, خدمات, مصر, سنو') }}">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="author" content="SENÚ سنو">
    <meta name="robots" content="index, follow">
    <link rel="canonical" href="{{ data_get($seoData, 'canonical', url('/')) }}">

    <meta property="og:title" content="{{ data_get($seoData, 'title', 'SENÚ سنو') }}">
    <meta property="og:description" content="{{ data_get($seoData, 'description', '') }}">
    <meta property="og:image" content="{{ data_get($seoData, 'og_image', asset('images/og-default.jpg')) }}">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="ar_EG">

    <!-- Favicon -->
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="apple-touch-icon" href="{{ asset('favicon.svg') }}">

    @stack('head')

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@200;300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Bootstrap 5 RTL CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.rtl.min.css" rel="stylesheet">

    <!-- Main CSS -->
    <link rel="stylesheet" href="{{ asset('css/style.css') }}">
    
    <!-- Enhanced Responsive CSS -->
    <link rel="stylesheet" href="{{ asset('css/responsive-fixes.css') }}">
    
    <!-- Performance Optimizations -->
    <link rel="stylesheet" href="{{ asset('css/performance.css') }}">

    <!-- Select2 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    <!-- Select2 RTL CSS -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2-rtl.css" rel="stylesheet" />

    <!-- Swiper CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/swiper@11/swiper-bundle.min.css" />

    <link rel="icon" type="image/x-icon" href="{{ asset('favicon.ico') }}">

    <!-- User Tracking Script -->
    <script src="{{ asset('js/user-tracking.js') }}" defer></script>

    @stack('styles')
</head>

<body>
    @include('partials.navbar')

    {{-- City Selection Modal - Available on all pages --}}
    {{-- Only show if no city is selected in session --}}
    <x-city-modal-simple :show-modal="!session('selected_city') && !session('city_slug')" />

    @yield('content')

    @include('partials.footer')

    <!-- Floating City Selector -->
    @include('partials.floating-city-selector')

    <!-- App JS (if you bundle scripts) -->
    <script src="{{ asset('js/app.js') }}" defer></script>

    <!-- jQuery (required for Select2) -->
    <script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>

    <!-- Select2 JS -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Select2 Arabic Translation -->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/i18n/ar.js"></script>

    <!-- Global Select2 Initialization -->
    <script>
        $(document).ready(function () {
            // Global Select2 initialization function
            // function initializeSelect2() {
            //     // Initialize Select2 for all select elements that haven't been initialized yet
            //     $('select:not(.select2-hidden-accessible)').each(function () {
            //         const $select = $(this);

            //         // Get custom configuration based on select classes or data attributes
            //         let config = {
            //             theme: 'classic',
            //             dir: 'rtl',
            //             language: 'ar',
            //             placeholder: $select.data('placeholder') || 'اختر...',
            //             allowClear: !$select.prop('required'),
            //             minimumResultsForSearch: 5,
            //             width: '100%'
            //         };

            //         // Special configuration for search forms
            //         if ($select.closest('.search-form, .hero-search').length) {
            //             config.minimumResultsForSearch = 3;
            //             config.dropdownParent = $('body');
            //         }

            //         // Special configuration for filter forms
            //         if ($select.hasClass('filter-select')) {
            //             config.minimumResultsForSearch = 10;
            //         }

            //         // Initialize Select2
            //         $select.select2(config);
            //     });

            //     // Auto-submit forms when Select2 changes (for filter forms)
            //     $('select.filter-select, select.search-select').off('select2:select select2:clear').on('select2:select select2:clear', function () {
            //         const $form = $(this).closest('form');
            //         if ($form.length && $form.hasClass('auto-submit')) {
            //             $form.submit();
            //         }
            //     });
            // }

            // // Initialize on page load
            // initializeSelect2();

            // Re-initialize when new content is loaded dynamically
            $(document).on('DOMNodeInserted', function (e) {
                if ($(e.target).find('select').length) {
                    setTimeout(initializeSelect2, 100);
                }
            });
        });

        // Mobile menu toggle
        function toggleMobileMenu() {
            const mobileNav = document.getElementById('mobileNav');
            const toggle = document.querySelector('.mobile-menu-toggle');

            if (mobileNav && toggle) {
                mobileNav.classList.toggle('active');
                toggle.classList.toggle('active');
            }
        }

        // Search suggestions (autocomplete)
        $(document).ready(function () {
            const searchInput = $('.search-input');

            searchInput.on('input', function () {
                const term = $(this).val();

                if (term.length >= 2) {
                    $.ajax({
                        url: '{{ route("search.suggestions") }}',
                        data: { term: term },
                        success: function (data) {
                            // You can implement dropdown suggestions here
                            console.log('Suggestions:', data);
                        }
                    });
                }
            });
        });

        // Category dropdown hover effects for desktop
        document.addEventListener('DOMContentLoaded', function () {
            console.log('Loading category dropdowns...');
            const categoryDropdowns = document.querySelectorAll('.category-dropdown');
            console.log('Found dropdowns:', categoryDropdowns.length);

            categoryDropdowns.forEach((dropdown, index) => {
                const submenu = dropdown.querySelector('.category-submenu');
                const arrow = dropdown.querySelector('.dropdown-arrow');

                console.log(`Dropdown ${index}:`, {
                    hasSubmenu: !!submenu,
                    hasArrow: !!arrow,
                    categorySlug: dropdown.dataset.category
                });

                if (submenu) {
                    let hoverTimeout;

                    // Mouse enter event
                    dropdown.addEventListener('mouseenter', () => {
                        console.log('Mouse enter dropdown:', dropdown.dataset.category);
                        clearTimeout(hoverTimeout);
                        dropdown.classList.add('active');
                        submenu.style.opacity = '1';
                        submenu.style.visibility = 'visible';
                        submenu.style.transform = 'translateY(0)';
                        submenu.style.display = 'block';
                        if (arrow) {
                            arrow.style.transform = 'rotate(180deg)';
                        }
                    });

                    // Mouse leave event
                    dropdown.addEventListener('mouseleave', () => {
                        console.log('Mouse leave dropdown:', dropdown.dataset.category);
                        hoverTimeout = setTimeout(() => {
                            dropdown.classList.remove('active');
                            submenu.style.opacity = '0';
                            submenu.style.visibility = 'hidden';
                            submenu.style.transform = 'translateY(-15px)';
                            if (arrow) {
                                arrow.style.transform = 'rotate(0deg)';
                            }
                        }, 100);
                    });

                    // Keep submenu open when hovering over it
                    submenu.addEventListener('mouseenter', () => {
                        console.log('Mouse enter submenu');
                        clearTimeout(hoverTimeout);
                        dropdown.classList.add('active');
                    });

                    submenu.addEventListener('mouseleave', () => {
                        console.log('Mouse leave submenu');
                        hoverTimeout = setTimeout(() => {
                            dropdown.classList.remove('active');
                            submenu.style.opacity = '0';
                            submenu.style.visibility = 'hidden';
                            submenu.style.transform = 'translateY(-15px)';
                            if (arrow) {
                                arrow.style.transform = 'rotate(0deg)';
                            }
                        }, 100);
                    });
                }
            });

            // Close all dropdowns when clicking outside
            document.addEventListener('click', (e) => {
                if (!e.target.closest('.category-dropdown')) {
                    categoryDropdowns.forEach(dropdown => {
                        const submenu = dropdown.querySelector('.category-submenu');
                        const arrow = dropdown.querySelector('.dropdown-arrow');

                        if (submenu) {
                            dropdown.classList.remove('active');
                            submenu.style.opacity = '0';
                            submenu.style.visibility = 'hidden';
                            submenu.style.transform = 'translateY(-15px)';
                            if (arrow) {
                                arrow.style.transform = 'rotate(0deg)';
                            }
                        }
                    });
                }
            });
        });
    </script>

    <!-- Bootstrap 5 JavaScript Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    @stack('scripts')

    <!-- Firebase Initialization -->
    <x-firebase-init />
</body>

</html>