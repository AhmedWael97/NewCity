@extends('layouts.minimal')

@section('title', 'اختر مدينتك - تسوق محلي في مدينتك')

@push('meta')
    <!-- Primary Meta Tags -->
    <meta name="title" content="اختر مدينتك - تسوق محلي في مدينتك">
    <meta name="description"
        content="اختر مدينتك للحصول على أفضل تجربة تسوق محلية. اكتشف المتاجر والخدمات المحلية في مدينتك بسهولة.">
    <meta name="keywords" content="اختيار المدينة، تسوق محلي، متاجر محلية، خدمات محلية، مدن السعودية">
    <meta name="robots" content="index, follow">
    <meta name="language" content="Arabic">
    <meta name="author" content="{{ config('app.name') }}">

    <!-- Open Graph / Facebook -->
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ url()->current() }}">
    <meta property="og:title" content="اختر مدينتك - تسوق محلي في مدينتك">
    <meta property="og:description"
        content="اختر مدينتك للحصول على أفضل تجربة تسوق محلية. اكتشف المتاجر والخدمات المحلية في مدينتك بسهولة.">
    <meta property="og:image" content="{{ asset('images/city-selection-og.jpg') }}">
    <meta property="og:locale" content="ar_SA">
    <meta property="og:site_name" content="{{ config('app.name') }}">

    <!-- Twitter -->
    <meta property="twitter:card" content="summary_large_image">
    <meta property="twitter:url" content="{{ url()->current() }}">
    <meta property="twitter:title" content="اختر مدينتك - تسوق محلي في مدينتك">
    <meta property="twitter:description"
        content="اختر مدينتك للحصول على أفضل تجربة تسوق محلية. اكتشف المتاجر والخدمات المحلية في مدينتك بسهولة.">
    <meta property="twitter:image" content="{{ asset('images/city-selection-og.jpg') }}">

    <!-- Canonical URL -->
    <link rel="canonical" href="{{ url()->current() }}">

    <!-- Structured Data / JSON-LD -->
    <script type="application/ld+json">
        {!! json_encode([
        '@context' => 'https://schema.org',
        '@type' => 'WebPage',
        'name' => 'اختر مدينتك',
        'description' => 'اختر مدينتك للحصول على أفضل تجربة تسوق محلية',
        'url' => url()->current(),
        'inLanguage' => 'ar-SA',
        'isPartOf' => [
            '@type' => 'WebSite',
            'name' => config('app.name'),
            'url' => url('/')
        ],
        'breadcrumb' => [
            '@type' => 'BreadcrumbList',
            'itemListElement' => [
                [
                    '@type' => 'ListItem',
                    'position' => 1,
                    'name' => 'الرئيسية',
                    'item' => url('/')
                ],
                [
                    '@type' => 'ListItem',
                    'position' => 2,
                    'name' => 'اختر مدينتك',
                    'item' => url()->current()
                ]
            ]
        ]
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) !!}
        </script>
@endpush

@section('content')
    <div class="select-city-page min-vh-100 d-flex align-items-center"
        style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
        <div class="container py-5">

            <!-- Header -->
            <div class="text-center text-white mb-5">
                <div class="mb-4">
                    <i class="fas fa-map-marked-alt" style="font-size: 4rem; opacity: 0.9;"></i>
                </div>
                <h1 class="display-4 fw-bold mb-3">اختر مدينتك</h1>
                <p class="lead mb-0" style="font-size: 1.25rem;">
                    للحصول على أفضل تجربة تسوق محلية
                </p>
            </div>

            <!-- Search Box -->
            <div class="row justify-content-center mb-4">
                <div class="col-lg-6 col-md-8">
                    <div class="search-box bg-white rounded-3 shadow-lg p-2">
                        <div class="input-group input-group-lg">
                            <input type="text" id="citySearchInput" class="form-control border-0 pe-5"
                                placeholder="ابحث عن مدينتك..." style="text-align: right; font-size: 1.1rem;">
                            <span class="input-group-text bg-transparent border-0">
                                <i class="fas fa-search text-muted"></i>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Cities Grid -->
            <div id="citiesContainer">
                <div class="row g-4" id="citiesGrid">
                    @forelse($cities as $key => $city)
                        <div class="col-lg-3 col-md-4 col-sm-6 city-item" data-name="{{ strtolower($city->name) }}"
                            data-state="{{ strtolower($city->state ?? '') }}"
                            data-country="{{ strtolower($city->country ?? '') }}">
                            <div class="city-card shadow-lg"
                                onclick="selectCity('{{ $city->slug }}', '{{ $city->name }}', this)">
                                <div class="city-icon-wrapper">
                                    <i class="fas fa-city"></i>
                                </div>
                                <h5 class="city-name">{{ $city->name }}</h5>
                                @if($city->state)
                                    <p class="city-meta mb-0">{{ $city->state }}@if($city->country), {{ $city->country }}@endif</p>
                                @endif
                                @if($city->shops_count > 0)
                                    <div class="city-stats">
                                        <span class="city-stat-item">
                                            <i class="fas fa-store"></i>
                                            <span>{{ $city->shops_count }} متجر</span>
                                        </span>
                                    </div>
                                @endif
                            </div>
                        </div>

                        @if($key == (count($cities) - 1))
                            <!-- Add City Suggestion Card -->
                            <div class="col-lg-3 col-md-4 col-sm-6 city-item"  >
                                <div class="city-card city-card-add shadow-lg" style="background-color: white !important;" data-bs-toggle="modal"
                                    data-bs-target="#citySuggestionModal">
                                    <div class="city-icon-wrapper city-icon-add">
                                        <i class="fas fa-plus-circle"></i>
                                    </div>
                                    <h5 class="city-name">اقترح مدينتك</h5>
                                    <p class="city-meta mb-0">لم تجد مدينتك؟ اقترحها الآن</p>
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="col-12">
                            <div class="bg-white rounded-3 shadow p-5 text-center">
                                <i class="fas fa-city text-muted mb-3" style="font-size: 3rem;"></i>
                                <h4 class="text-muted">لا توجد مدن متاحة حالياً</h4>
                                <p class="text-muted mb-0">الرجاء المحاولة لاحقاً</p>
                            </div>
                        </div>
                    @endforelse
                </div>

                <!-- No Search Results -->
                <div id="noResults" class="text-center py-5" style="display: none;">
                    <div class="bg-white rounded-3 shadow p-5">
                        <i class="fas fa-search text-muted mb-3" style="font-size: 3rem;"></i>
                        <h4 class="text-muted">لم نجد مدن مطابقة</h4>
                        <p class="text-muted mb-0">جرب البحث بكلمات أخرى</p>
                    </div>
                </div>


            </div>

            <!-- City Suggestion Modal -->
            <div class="modal fade" id="citySuggestionModal" tabindex="-1" aria-labelledby="citySuggestionModalLabel"
                aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header border-0">
                            <h5 class="modal-title" id="citySuggestionModalLabel">
                                <i class="fas fa-map-marker-alt text-primary ms-2"></i>
                                اقترح مدينتك
                            </h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <!-- Success/Error Messages -->
                            <div id="suggestionAlert" style="display: none;"></div>

                            <form id="citySuggestionForm" action="{{ route('city.suggestion') }}" method="POST">
                                @csrf
                                <div class="mb-3">
                                    <label for="cityName" class="form-label">اسم المدينة <span
                                            class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="cityName" name="city_name"
                                        placeholder="مثال: حدائق العاصمة" required>
                                </div>
                                <div class="mb-3">
                                    <label for="contactPhone" class="form-label">رقم الهاتف <span
                                            class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="contactPhone" name="phone"
                                        placeholder="01xxxxxxxx" required>
                                </div>
                                <div class="mb-3">
                                    <label for="groupUrl" class="form-label">رابط المجموعة الرئيسية <span
                                            class="text-danger">*</span></label>
                                    <input type="url" class="form-control" id="groupUrl" name="group_url"
                                        placeholder="https://..." required>
                                    <small class="text-muted">رابط مجموعة واتساب أو تيليجرام أو أي منصة أخرى</small>
                                </div>
                            </form>
                        </div>
                        <div class="modal-footer border-0">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">إلغاء</button>
                            <button type="submit" form="citySuggestionForm" class="btn btn-primary" id="submitSuggestion">
                                <i class="fas fa-paper-plane ms-2"></i>
                                إرسال الاقتراح
                            </button>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
@endsection

@push('styles')
    <style>
        .city-card {
            background: white;
            border-radius: 15px;
            padding: 1.5rem;
            cursor: pointer;
            transition: all 0.3s ease;
            border: 2px solid transparent;
            height: 100%;
        }

        .city-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
            border-color: #667eea;
        }

        .city-card.selecting {
            opacity: 0.6;
            pointer-events: none;
        }

        .city-icon-wrapper {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            color: white;
            margin-left: auto;
            margin-right: auto;
            margin-bottom: 1rem;
        }

        .city-name {
            font-size: 1.25rem;
            font-weight: bold;
            color: #2d3748;
            margin-bottom: 0.5rem;
            text-align: center;
        }

        .city-meta {
            font-size: 0.9rem;
            color: #718096;
            text-align: center;
        }

        .city-stats {
            display: flex;
            justify-content: center;
            gap: 1rem;
            margin-top: 0.75rem;
            padding-top: 0.75rem;
            border-top: 1px solid #e2e8f0;
        }

        .city-stat-item {
            display: flex;
            align-items: center;
            gap: 0.25rem;
            font-size: 0.85rem;
            color: #4a5568;
        }

        .city-stat-item i {
            color: #667eea;
        }

        .city-card-add {
            border: 2px dashed #667eea !important;
            background: rgba(102, 126, 234, 0.05) !important;
        }

        .city-card-add:hover {
            background: rgba(102, 126, 234, 0.1) !important;
            border-color: #764ba2 !important;
        }

        .city-icon-add {
            background: transparent !important;
            color: #667eea !important;
            border: 2px solid #667eea;
        }

        .city-card-add:hover .city-icon-add {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
            color: white !important;
            border-color: transparent;
        }

        .modal-content {
            border-radius: 15px;
            border: none;
        }

        .modal-header {
            padding: 1.5rem;
        }

        .modal-title {
            font-weight: 600;
            color: #2d3748;
        }

        .modal-body {
            padding: 1.5rem;
        }

        .modal-footer {
            padding: 1.5rem;
        }

        .modal-body .form-label {
            font-weight: 600;
            color: #2d3748;
            margin-bottom: 0.5rem;
        }

        .modal-body .form-control {
            border-radius: 8px;
            padding: 0.75rem;
            border: 1px solid #e2e8f0;
            transition: all 0.2s ease;
        }

        .modal-body .form-control:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        }

        .modal-footer .btn-primary {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border: none;
            border-radius: 8px;
            font-weight: 600;
            padding: 0.5rem 2rem;
        }

        .modal-footer .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4);
        }

        .modal-footer .btn-secondary {
            border-radius: 8px;
            padding: 0.5rem 2rem;
        }
    </style>
@endpush

@push('scripts')
    <script>
        // Search functionality
        document.getElementById('citySearchInput').addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase().trim();
            const cityItems = document.querySelectorAll('.city-item');
            const noResults = document.getElementById('noResults');
            let visibleCount = 0;

            cityItems.forEach(item => {
                const name = item.dataset.name;
                const state = item.dataset.state;
                const country = item.dataset.country;

                const matches = name.includes(searchTerm) ||
                    state.includes(searchTerm) ||
                    country.includes(searchTerm);

                if (matches || searchTerm === '') {
                    item.style.display = '';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });

            // Show/hide no results message
            noResults.style.display = (visibleCount === 0 && searchTerm !== '') ? 'block' : 'none';
        });

        // Select city and save
        function selectCity(slug, name, cardElement) {
            // Show loading state on the card
            cardElement.classList.add('selecting');
            cardElement.innerHTML = `
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">جاري التحميل...</span>
                    </div>
                    <p class="mt-3 mb-0 text-muted">جاري التحويل...</p>
                </div>
            `;

            // Save to localStorage for persistent storage
            localStorage.setItem('selectedCity', slug);
            localStorage.setItem('selectedCityName', name);
            localStorage.setItem('citySelectedAt', new Date().toISOString());

            // Also set a cookie for server-side detection (expires in 30 days)
            setCookie('selected_city_slug', slug, 30);

            // Send to server to save in session
            fetch('/set-city', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify({
                    city_slug: slug,
                    city_name: name
                })
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Redirect to city landing page
                        window.location.href = `/city/${slug}`;
                    } else {
                        alert('حدث خطأ، حاول مرة أخرى');
                        window.location.reload();
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Even if server fails, still redirect using localStorage
                    window.location.href = `/city/${slug}`;
                });
        }

        // Helper function to set cookie
        function setCookie(name, value, days) {
            const expires = new Date();
            expires.setTime(expires.getTime() + (days * 24 * 60 * 60 * 1000));
            document.cookie = name + '=' + value + ';expires=' + expires.toUTCString() + ';path=/';
        }

        // Check localStorage on page load and auto-redirect if city was selected before
        window.addEventListener('DOMContentLoaded', function () {
            const savedCity = localStorage.getItem('selectedCity');
            const savedAt = localStorage.getItem('citySelectedAt');

            // If city was selected before, redirect automatically
            if (savedCity && savedAt) {
                // Check if selection is still valid (within 30 days)
                const selectedDate = new Date(savedAt);
                const now = new Date();
                const daysSinceSelection = (now - selectedDate) / (1000 * 60 * 60 * 24);

                if (daysSinceSelection < 30) {
                    // Redirect immediately
                    window.location.href = `/city/${savedCity}`;
                } else {
                    // Selection expired, clear localStorage
                    localStorage.removeItem('selectedCity');
                    localStorage.removeItem('selectedCityName');
                    localStorage.removeItem('citySelectedAt');
                }
            }
        });

        // Handle city suggestion form submission
        document.getElementById('citySuggestionForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const form = this;
            const submitBtn = document.getElementById('submitSuggestion');
            const alertDiv = document.getElementById('suggestionAlert');
            const formData = new FormData(form);
            const modal = bootstrap.Modal.getInstance(document.getElementById('citySuggestionModal'));

            // Disable submit button
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<span class="spinner-border spinner-border-sm ms-2"></span> جاري الإرسال...';

            // Send form data
            fetch(form.action, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                },
                body: formData
            })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Show success message
                        alertDiv.innerHTML = `
                            <div class="alert alert-success alert-dismissible fade show" role="alert">
                                <i class="fas fa-check-circle ms-2"></i>
                                ${data.message || 'تم إرسال اقتراحك بنجاح! سنتواصل معك قريباً.'}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `;
                        alertDiv.style.display = 'block';
                        form.reset();

                        // Close modal after 2 seconds
                        setTimeout(() => {
                            if (modal) {
                                modal.hide();
                            }
                            alertDiv.style.display = 'none';
                            alertDiv.innerHTML = '';
                        }, 2000);
                    } else {
                        // Show error message
                        alertDiv.innerHTML = `
                            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                                <i class="fas fa-exclamation-circle ms-2"></i>
                                ${data.message || 'حدث خطأ، الرجاء المحاولة مرة أخرى.'}
                                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                            </div>
                        `;
                        alertDiv.style.display = 'block';
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alertDiv.innerHTML = `
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle ms-2"></i>
                            حدث خطأ في الاتصال، الرجاء المحاولة مرة أخرى.
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    `;
                    alertDiv.style.display = 'block';
                })
                .finally(() => {
                    // Re-enable submit button
                    submitBtn.disabled = false;
                    submitBtn.innerHTML = '<i class="fas fa-paper-plane ms-2"></i> إرسال الاقتراح';
                });
        });

        // Reset form when modal is closed
        document.getElementById('citySuggestionModal').addEventListener('hidden.bs.modal', function () {
            document.getElementById('citySuggestionForm').reset();
            document.getElementById('suggestionAlert').style.display = 'none';
            document.getElementById('suggestionAlert').innerHTML = '';
        });
    </script>
@endpush