{{-- Enhanced City Selection Modal Component --}}
@props(['cities' => collect(), 'showModal' => false])

<div 
    id="citySelectionModal" 
    class="modal {{ $showModal ? 'd-flex' : 'd-none' }}" 
    style="background-color: rgba(0, 0, 0, 0.6); backdrop-filter: blur(5px); text-align: right;"
    tabindex="-1"
    dir="rtl"
>
    <div class="modal-dialog modal-xl modal-dialog-centered">
        <div class="modal-content border-0 shadow-lg" style="border-radius: 20px; overflow: scroll; direction: rtl; ">
            
            <!-- Enhanced Header -->
            <div class="modal-header bg-gradient-primary text-white border-0 py-4" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
                <div class="d-flex align-items-center w-100" style="direction: rtl;">
                    <div class="modal-icon ms-3">
                        <i class="fas fa-map-marked-alt" style="font-size: 2rem;"></i>
                    </div>
                    <div class="flex-grow-1 text-start">
                        <h4 class="modal-title fw-bold mb-1">اختر مدينتك المفضلة</h4>
                        <p class="mb-0 opacity-75">للحصول على أفضل تجربة تسوق محلية</p>
                    </div>
                    
                </div>
            </div>
            
            <!-- Enhanced Search -->
            <div class="modal-body p-0">
                <div class="search-section p-4 bg-light border-bottom">
                    <div class="search-container position-relative">
                        <div class="search-icon position-absolute top-50 start-0 translate-middle-y ms-3 text-muted">
                            <i class="fas fa-search"></i>
                        </div>
                        <input 
                            type="text" 
                            id="citySearch"
                            class="form-control form-control-lg ps-5 pe-4 border-0 shadow-sm text-start"
                            placeholder="ابحث عن مدينتك..."
                            style="border-radius: 15px; background: white; direction: rtl;"
                        >
                       
                    </div>
                </div>

                <!-- Enhanced Cities Grid -->
                <div class="cities-container" style="overflow-y: auto;">
                    <div class="p-2">
                        <div class="cities-grid" id="citiesList">
                            @foreach($cities as $index => $city)
                                <div class="city-option-card mb-3 text-center" 
                                     data-city-slug="{{ $city->slug }}"
                                     data-city-name="{{ $city->name }}"
                                     data-search-text="{{ strtolower($city->name . ' ' . ($city->governorate ?? '')) }}"
                                     onclick="selectCity('{{ $city->slug }}', '{{ $city->name }}')"
                                     style="cursor: pointer;">
                                    
                                    <div class="city-card bg-white rounded-lg p-4 shadow-sm border border-transparent hover-border-primary transition-all" style="direction: rtl;">
                                        <div class="d-flex align-items-center">
                                            
                                            
                                            <!-- City Info -->
                                            <div class="">
                                                <div class="d-flex align-items-center justify-content-between" style="direction: rtl; text-align:center;">
                                                    <div class="text-center">
                                                        <h6 class="city-name mb-1 fw-bold text-dark">{{ $city->name }}</h6>
                                                        @if($city->governorate)
                                                            <p class="city-governorate text-muted mb-2 small">
                                                                <i class="fas fa-map-marker-alt ms-1"></i>
                                                                {{ $city->governorate }}</p>
                                                            </p>
                                                        @endif
                                                        
                                                       
                                                    </div>
                                                    
                                                    <!-- Selection Arrow -->
                                                    <div class="selection-arrow text-muted">
                                                        <i class="fas fa-chevron-right" style="font-size: 1.2rem;"></i>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                        <!-- Loading State (Hidden by default) -->
                                        <div class="loading-state d-none">
                                            <div class="d-flex align-items-center justify-content-center py-2">
                                                <div class="spinner-border spinner-border-sm text-primary me-2" role="status"></div>
                                                <span class="text-primary">جاري التحديد...</span>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
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
            
            <!-- Enhanced Footer -->
            <div class="modal-footer bg-light border-0 p-4">
                <div class="d-flex align-items-center justify-content-between w-100">
                    <div class="footer-info">
                        <small class="text-muted">
                            <i class="fas fa-info-circle me-1"></i>
                            يمكنك تغيير المدينة في أي وقت لاحقاً
                        </small>
                    </div>
                    <div class="footer-actions">
                     
                        <button 
                            type="button"
                            class="btn btn-outline-secondary"
                            onclick="closeCityModal()"
                        >
                            <i class="fas fa-arrow-right me-1"></i>
                            إغلاق
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.hover-border-primary:hover {
    border-color: #0d6efd !important;
    box-shadow: 0 4px 15px rgba(13, 110, 253, 0.15);
}

.transition-all {
    transition: all 0.3s ease;
}

.city-option-card:hover .city-card {
    transform: translateY(-3px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.1) !important;
}

.city-option-card:hover .selection-arrow {
    color: #0d6efd !important;
    transform: translateX(5px);
}

.selection-arrow {
    transition: all 0.3s ease;
}

.modal-content {
    max-height: 90vh;
    overflow: scroll;
    font-family: 'Cairo', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

.cities-container::-webkit-scrollbar {
    width: 8px;
}

.cities-container::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 10px;
}

.cities-container::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 10px;
}

.cities-container::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}

/* RTL specific styles */
.city-card {
    direction: rtl;
    text-align: right;
}

.city-card * {
    direction: rtl;
}

.modal-header .d-flex {
    flex-direction: row-reverse;
}

.search-container input {
    text-align: right;
    direction: rtl;
}

.search-icon {
    right: 15px;
    left: auto;
}

@media (max-width: 768px) {
    .modal-dialog {
        margin: 1rem;
        max-width: calc(100% - 2rem);
    }
    
    .city-card {
        padding: 1rem !important;
    }
    
    .city-avatar {
        margin-right: 0.75rem !important;
        margin-left: 0 !important;
    }
}
</style>

<script>
// Enhanced modal functions with localStorage support
function showCityModal() {
    // First check if city is already selected
    if (checkPreviousCitySelection()) {
        console.log('🏙️ City already selected, not showing modal');
        return;
    }
    
    const modal = document.getElementById('citySelectionModal');
    if (modal) {
        modal.classList.remove('d-none');
        modal.classList.add('d-flex');
        document.body.style.overflow = 'hidden';
        
        // Focus on search input for better UX
        setTimeout(() => {
            const searchInput = document.getElementById('citySearch');
            if (searchInput) searchInput.focus();
        }, 300);
    }
}

function closeCityModal() {
    const modal = document.getElementById('citySelectionModal');
    if (modal) {
        modal.classList.add('d-none');
        modal.classList.remove('d-flex');
        document.body.style.overflow = '';
    }
}

// Enhanced city selection with localStorage
async function selectCity(citySlug, cityName) {
    try {
        console.log('🏙️ Selecting city:', citySlug, cityName);
        
        // Show loading state for selected city
        const selectedCard = document.querySelector(`[data-city-slug="${citySlug}"]`);
        if (selectedCard) {
            const loadingState = selectedCard.querySelector('.loading-state');
            const cityCard = selectedCard.querySelector('.city-card');
            
            loadingState.classList.remove('d-none');
            cityCard.style.opacity = '0.7';
            cityCard.style.pointerEvents = 'none';
        }
        
        // Save to localStorage for guest users
        localStorage.setItem('selected_city', citySlug);
        localStorage.setItem('selected_city_name', cityName);
        localStorage.setItem('city_selection_timestamp', new Date().getTime());
        
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        if (!csrfToken) {
            throw new Error('CSRF token not found');
        }
        
        // Make AJAX request
        const response = await fetch('{{ route("set.city") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ city_slug: citySlug }),
            signal: AbortSignal.timeout(5000)
        });
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const data = await response.json();
        console.log('✅ City selection response:', data);
        
        if (data.success) {
            // Show success state
            if (selectedCard) {
                const loadingState = selectedCard.querySelector('.loading-state');
                loadingState.innerHTML = `
                    <div class="d-flex align-items-center justify-content-center py-2">
                        <i class="fas fa-check-circle text-success me-2"></i>
                        <span class="text-success">تم بنجاح!</span>
                    </div>
                `;
            }
            
            // Close modal and redirect to city landing
            setTimeout(() => {
                closeCityModal();
                window.location.href = `/city-landing/${citySlug}`;
            }, 1000);
            
        } else {
            throw new Error(data.message || 'فشل في اختيار المدينة');
        }
    } catch (error) {
        console.error('💥 Error selecting city:', error);
        
        // Reset card state
        const selectedCard = document.querySelector(`[data-city-slug="${citySlug}"]`);
        if (selectedCard) {
            const loadingState = selectedCard.querySelector('.loading-state');
            const cityCard = selectedCard.querySelector('.city-card');
            
            loadingState.classList.add('d-none');
            cityCard.style.opacity = '1';
            cityCard.style.pointerEvents = 'auto';
        }
        
        alert('❌ حدث خطأ: ' + error.message);
    }
}

// Enhanced skip with localStorage
async function skipCitySelection() {
    try {
        // Clear localStorage
        localStorage.removeItem('selected_city');
        localStorage.removeItem('selected_city_name');
        localStorage.removeItem('city_selection_timestamp');
        
        const response = await fetch('{{ route("set.city") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ skip: true }),
            signal: AbortSignal.timeout(3000)
        });
        
        const data = await response.json();
        
        if (data.success) {
            console.log('✅ City selection skipped');
            closeCityModal();
            showSuccessNotification('تم تخطي اختيار المدينة');
        }
    } catch (error) {
        console.error('💥 Error skipping city selection:', error);
        closeCityModal();
    }
}

// Enhanced search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('citySearch');
    const cityOptions = document.querySelectorAll('.city-option-card');
    const noResults = document.getElementById('noResults');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            let visibleCount = 0;
            
            cityOptions.forEach(function(option) {
                const searchText = option.dataset.searchText || '';
                if (searchText.includes(query)) {
                    option.style.display = 'block';
                    visibleCount++;
                } else {
                    option.style.display = 'none';
                }
            });
            
            // Show/hide no results message
            if (visibleCount === 0 && query.length > 0) {
                noResults.classList.remove('d-none');
            } else {
                noResults.classList.add('d-none');
            }
        });
        
        // Clear search on escape
        searchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                this.value = '';
                this.dispatchEvent(new Event('input'));
            }
        });
    }
    
    // Close modal when clicking outside
    document.getElementById('citySelectionModal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeCityModal();
        }
    });
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const modal = document.getElementById('citySelectionModal');
            if (!modal.classList.contains('d-none')) {
                closeCityModal();
            }
        }
    });
    
    // Check localStorage for previous city selection
    checkPreviousCitySelection();
});

// Check for previous city selection in localStorage
function checkPreviousCitySelection() {
    const savedCity = localStorage.getItem('selected_city');
    const savedCityName = localStorage.getItem('selected_city_name');
    const timestamp = localStorage.getItem('city_selection_timestamp');
    
    // Check if selection is not too old (7 days)
    if (savedCity && savedCityName && timestamp) {
        const now = new Date().getTime();
        const selectionTime = parseInt(timestamp);
        const daysDiff = (now - selectionTime) / (1000 * 60 * 60 * 24);
        
        if (daysDiff < 7) {
            console.log(`🔄 Found previous city selection: ${savedCityName} (${daysDiff.toFixed(1)} days ago)`);
            
            // Hide modal if city is already selected
            const modal = document.getElementById('citySelectionModal');
            if (modal && !modal.classList.contains('d-none')) {
                modal.classList.add('d-none');
                modal.classList.remove('d-flex');
            }
            
            // Auto-select if user is not authenticated
            @guest
                // For guests, auto-redirect to previous city after 2 seconds
                if (!sessionStorage.getItem('city_auto_redirect_shown')) {
                    sessionStorage.setItem('city_auto_redirect_shown', 'true');
                    
                    showSuccessNotification(`مرحباً بعودتك! سيتم توجيهك إلى ${savedCityName}`);
                    
                    setTimeout(() => {
                        window.location.href = `/city/${savedCity}`;
                    }, 2000);
                    
                    return true; // City is already selected
                }
            @endguest
            
            return true; // City is already selected
        } else {
            // Clear old selection
            localStorage.removeItem('selected_city');
            localStorage.removeItem('selected_city_name');
            localStorage.removeItem('city_selection_timestamp');
        }
    }
    
    return false; // No city selected or expired
}

// Success notification function
function showSuccessNotification(message) {
    let notification = document.getElementById('city-success-notification');
    if (!notification) {
        notification = document.createElement('div');
        notification.id = 'city-success-notification';
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: linear-gradient(135deg, #28a745, #20c997);
            color: white;
            padding: 15px 25px;
            border-radius: 10px;
            z-index: 10000;
            box-shadow: 0 4px 20px rgba(0,0,0,0.15);
            transform: translateX(100%);
            transition: transform 0.3s ease;
            max-width: 350px;
        `;
        document.body.appendChild(notification);
    }
    
    notification.innerHTML = `
        <div class="d-flex align-items-center">
            <i class="fas fa-check-circle me-2" style="font-size: 1.2rem;"></i>
            <span>${message}</span>
        </div>
    `;
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Animate out
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            notification.remove();
        }, 4000);
    }, 4000);
}

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    // Check for previous city selection
    checkPreviousCitySelection();
    
    // Setup search functionality
    const searchInput = document.getElementById('citySearch');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            const cityCards = document.querySelectorAll('.city-option-card');
            const noResults = document.getElementById('noResults');
            let hasResults = false;
            
            cityCards.forEach(card => {
                const searchText = card.getAttribute('data-search-text');
                if (searchText.includes(searchTerm)) {
                    card.style.display = 'block';
                    hasResults = true;
                } else {
                    card.style.display = 'none';
                }
            });
            
            if (noResults) {
                noResults.classList.toggle('d-none', hasResults);
            }
        });
    }
    
    // Close modal when clicking outside
    const modal = document.getElementById('citySelectionModal');
    if (modal) {
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                closeCityModal();
            }
        });
    }
    
    // Close modal with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeCityModal();
        }
    });
});
</script>