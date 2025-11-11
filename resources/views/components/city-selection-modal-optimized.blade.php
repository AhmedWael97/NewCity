{{-- Optimized City Selection Modal - Load Cities via AJAX --}}
@props(['showModal' => false])

<div 
    id="citySelectionModal" 
    class="modal {{ $showModal ? 'd-flex' : 'd-none' }}" 
    style="background-color: rgba(0, 0, 0, 0.6); backdrop-filter: blur(5px); position: fixed; top: 0; left: 0; right: 0; bottom: 0; z-index: 1050; overflow-y: auto;"
    tabindex="-1"
    dir="rtl"
>
    <div class="modal-dialog modal-dialog-centered" style="max-width: 900px; width: 95%; margin: 1.75rem auto;">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: hidden; direction: rtl;">
            
            <!-- Enhanced Header -->
            <div class="modal-header bg-gradient-primary text-white border-0 py-3 py-md-4" style="background: linear-gradient(135deg, #4e73df 0%, #224abe 100%);">
                <div class="d-flex align-items-center w-100">
                    <div class="modal-icon me-3">
                        <i class="fas fa-map-marked-alt" style="font-size: 1.5rem;"></i>
                    </div>
                    <div class="flex-grow-1">
                        <h4 class="modal-title fw-bold mb-1" style="font-size: 1.25rem;">اختر مدينتك المفضلة</h4>
                        <p class="mb-0 opacity-75" style="font-size: 0.875rem;">للحصول على أفضل تجربة تسوق محلية</p>
                    </div>
                </div>
            </div>
            
            <!-- Enhanced Search -->
            <div class="modal-body p-0">
                <div class="search-section p-3 p-md-4 bg-light border-bottom">
                    <div class="search-container position-relative">
                        <div class="search-icon position-absolute top-50 end-0 translate-middle-y me-3 text-muted">
                            <i class="fas fa-search"></i>
                        </div>
                        <input 
                            type="text" 
                            id="citySearch"
                            class="form-control form-control-lg pe-5 ps-3 border-0 shadow-sm"
                            placeholder="ابحث عن مدينتك..."
                            style="border-radius: 15px; background: white; text-align: right;"
                        >
                    </div>
                </div>

                <!-- Cities Grid with Loading State -->
                <div class="cities-container" style="overflow-y: auto; max-height: 60vh;">
                    
                    <!-- Loading Spinner -->
                    <div id="citiesLoading" class="text-center py-5">
                        <div class="spinner-border text-primary mb-3" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">جاري التحميل...</span>
                        </div>
                        <h5 class="text-muted">جاري تحميل المدن...</h5>
                    </div>

                    <!-- Cities List (will be populated via AJAX) -->
                    <div class="p-3 p-md-4">
                        <div class="cities-grid row row-cols-1 row-cols-sm-2 row-cols-lg-3 g-3" id="citiesList">
                            <!-- Cities will be loaded dynamically -->
                        </div>
                        
                        <!-- No Results Message -->
                        <div id="noResults" class="text-center py-5 d-none">
                            <i class="fas fa-search text-muted mb-3" style="font-size: 2rem;"></i>
                            <h5 class="text-muted">لم نجد مدن مطابقة</h5>
                            <p class="text-muted">جرب البحث بكلمات أخرى</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer border-0 bg-light p-3">
                <button type="button" class="btn btn-secondary" onclick="skipCitySelection()">
                    <i class="fas fa-times me-2"></i>
                    تخطي الآن
                </button>
            </div>
        </div>
    </div>
</div>

<style>
    #citySelectionModal .modal-dialog {
        display: flex;
        align-items: center;
        min-height: calc(100% - 3.5rem);
    }

    .city-option-card {
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .city-card {
        background: white;
        border: 2px solid #e9ecef;
        border-radius: 12px;
        padding: 1.5rem;
        transition: all 0.3s ease;
        direction: rtl;
        text-align: right;
        height: 100%;
    }

    .city-option-card:hover .city-card {
        border-color: var(--primary);
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(1, 107, 97, 0.15) !important;
    }

    .city-name {
        color: var(--primary);
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
    }

    .city-info-text {
        color: #6c757d;
        font-size: 0.875rem;
    }

    .selection-arrow {
        transition: all 0.3s ease;
    }

    .city-option-card:hover .selection-arrow {
        transform: translateX(-5px);
        color: var(--primary) !important;
    }

    .search-icon {
        z-index: 10;
    }

    #citySearch:focus {
        box-shadow: 0 0 0 0.25rem rgba(1, 107, 97, 0.25);
        border-color: var(--primary);
    }

    @media (max-width: 768px) {
        .cities-grid {
            grid-template-columns: 1fr;
        }
    }
</style>

<script>
    // Global variables
    let allCities = [];
    let citiesLoaded = false;

    // Check if user has already seen the modal
    function hasSeenModal() {
        return localStorage.getItem('cityModalShown') === 'true' || sessionStorage.getItem('citySelected') === 'true';
    }

    // Mark modal as shown
    function markModalShown() {
        localStorage.setItem('cityModalShown', 'true');
    }

    // Load cities via AJAX when modal is shown
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('citySelectionModal');
        
        @if($showModal)
            // Only show modal on first visit
            if (!hasSeenModal()) {
                loadCitiesAjax();
                markModalShown();
            } else {
                modal.classList.add('d-none');
                modal.classList.remove('d-flex');
            }
        @endif

        // Also load when modal becomes visible
        const observer = new MutationObserver(function(mutations) {
            mutations.forEach(function(mutation) {
                if (mutation.attributeName === 'class') {
                    const isVisible = modal.classList.contains('d-flex');
                    if (isVisible && !citiesLoaded) {
                        loadCitiesAjax();
                    }
                }
            });
        });

        observer.observe(modal, { attributes: true });
    });

    // Load cities via AJAX
    function loadCitiesAjax() {
        if (citiesLoaded) return;
        citiesLoaded = true;

        const loadingDiv = document.getElementById('citiesLoading');
        const citiesList = document.getElementById('citiesList');

        console.log('Loading cities from API...');

        fetch('{{ url("/api/v1/cities-selection") }}')
            .then(response => {
                console.log('Response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Cities loaded:', data);
                allCities = data.cities || [];
                console.log('Total cities:', allCities.length);
                
                if (allCities.length === 0) {
                    if (loadingDiv) {
                        loadingDiv.innerHTML = `
                            <div class="alert alert-warning m-4">
                                <i class="fas fa-info-circle me-2"></i>
                                لا توجد مدن متاحة حالياً. الرجاء المحاولة لاحقاً.
                            </div>
                        `;
                    }
                    return;
                }
                
                renderCities(allCities);
                
                // Hide loading spinner
                if (loadingDiv) {
                    loadingDiv.style.display = 'none';
                }

                // Setup search after cities are loaded
                setupSearch();
            })
            .catch(error => {
                console.error('Error loading cities:', error);
                citiesLoaded = false; // Allow retry
                if (loadingDiv) {
                    loadingDiv.innerHTML = `
                        <div class="alert alert-danger m-4">
                            <i class="fas fa-exclamation-triangle me-2"></i>
                            حدث خطأ في تحميل المدن. الرجاء إعادة تحميل الصفحة.
                            <br><small class="text-muted">خطأ: ${error.message}</small>
                            <br><button onclick="loadCitiesAjax()" class="btn btn-sm btn-primary mt-2">
                                <i class="fas fa-redo"></i> إعادة المحاولة
                            </button>
                        </div>
                    `;
                }
            });
    }

    // Render cities in the grid
    function renderCities(cities) {
        const citiesList = document.getElementById('citiesList');
        const noResults = document.getElementById('noResults');

        console.log('Rendering cities:', cities);

        if (!cities || cities.length === 0) {
            console.log('No cities to render');
            citiesList.innerHTML = '';
            if (noResults) noResults.classList.remove('d-none');
            return;
        }

        if (noResults) noResults.classList.add('d-none');
        
        const html = cities.map(city => {
            console.log('Rendering city:', city);
            const stateText = city.state || city.governorate || '';
            return `
            <div class="col">
                <div class="city-option-card h-100" data-city-slug="${city.slug || ''}"
                     data-city-name="${city.name || ''}"
                     data-search-text="${(city.name || '').toLowerCase()} ${stateText.toLowerCase()}">
                    <div class="city-card p-3 h-100" onclick="selectCity('${(city.slug || '').replace(/'/g, "\\'")}', '${(city.name || '').replace(/'/g, "\\'")}')">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center flex-grow-1">
                                <div class="city-icon text-primary me-3" style="font-size: 1.5rem;">
                                    <i class="fas fa-map-marker-alt"></i>
                                </div>
                                <div class="city-info text-end">
                                    <h6 class="city-name mb-0 fw-bold">${city.name || 'مدينة'}</h6>
                                    ${stateText ? `<small class="text-muted d-block">${stateText}</small>` : ''}
                                </div>
                            </div>
                            <div class="city-arrow text-muted">
                                <i class="fas fa-chevron-left"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        `;
        }).join('');
        
        console.log('Setting innerHTML with', cities.length, 'cities');
        citiesList.innerHTML = html;
        console.log('Cities rendered in DOM');
    }

    // Setup search functionality
    function setupSearch() {
        const searchInput = document.getElementById('citySearch');
        if (!searchInput) return;

        let searchTimeout;
        searchInput.addEventListener('input', function(e) {
            clearTimeout(searchTimeout);
            
            searchTimeout = setTimeout(() => {
                const searchTerm = e.target.value.trim().toLowerCase();
                
                if (searchTerm === '') {
                    renderCities(allCities);
                    return;
                }

                const filtered = allCities.filter(city => {
                    const searchText = `${city.name} ${city.governorate || ''}`.toLowerCase();
                    return searchText.includes(searchTerm);
                });

                renderCities(filtered);
            }, 300);
        });
    }

    // Select city and submit
    function selectCity(citySlug, cityName) {
        // Show loading state on the card
        const card = document.querySelector(`[data-city-slug="${citySlug}"] .city-card`);
        if (card) {
            card.innerHTML = `
                <div class="text-center py-2">
                    <div class="spinner-border spinner-border-sm text-primary" role="status">
                        <span class="visually-hidden">جاري التحميل...</span>
                    </div>
                    <p class="mb-0 mt-2 small text-muted">جاري التحميل...</p>
                </div>
            `;
        }

        // Mark city as selected
        sessionStorage.setItem('citySelected', 'true');

        // Submit selection
        fetch('/set-city', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                city_slug: citySlug,
                city_name: cityName
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Reload page or redirect
                if (data.redirect) {
                    window.location.href = data.redirect;
                } else {
                    window.location.reload();
                }
            } else {
                alert('حدث خطأ. الرجاء المحاولة مرة أخرى.');
                window.location.reload();
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('حدث خطأ. الرجاء المحاولة مرة أخرى.');
            window.location.reload();
        });
    }

    // Skip city selection
    function skipCitySelection() {
        fetch('/skip-city-selection', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(() => {
            document.getElementById('citySelectionModal').classList.add('d-none');
        })
        .catch(error => {
            console.error('Error:', error);
            document.getElementById('citySelectionModal').classList.add('d-none');
        });
    }

    // Show modal function (callable from outside)
    function showCityModal() {
        const modal = document.getElementById('citySelectionModal');
        modal.classList.remove('d-none');
        modal.classList.add('d-flex');
    }

    // Close modal on outside click
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('citySelectionModal');
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                skipCitySelection();
            }
        });
    });
</script>
