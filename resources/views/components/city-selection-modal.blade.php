{{-- Simple City Selection Modal Component (Bootstrap 5) --}}
@props(['cities' => collect(), 'showModal' => false])

<div 
    id="citySelectionModal" 
    class="modal {{ $showModal ? 'd-flex' : 'd-none' }}" 
    style="background-color: rgba(0, 0, 0, 0.5); direction: rtl;"
    tabindex="-1"
    dir="rtl"
>
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            
            <!-- Header -->
            <div class="modal-header border-bottom">
                <h5 class="modal-title fw-bold">اختر مدينتك</h5>
                <button 
                    type="button" 
                    class="btn-close" 
                    onclick="closeCityModal()"
                    aria-label="إغلاق"
                ></button>
            </div>
            
            <!-- Search -->
            <div class="modal-body border-bottom pb-3">
                <div class="mb-0">
                    <input 
                        type="text" 
                        id="citySearch"
                        class="form-control"
                        placeholder="ابحث عن مدينتك..."
                    >
                </div>
            </div>
            
            <!-- Cities List -->
            <div class="modal-body" style="max-height: 400px; overflow-y: auto;">
                <div id="citiesList">
                    @foreach($cities as $city)
                        <button 
                            type="button"
                            class="city-option btn btn-outline-primary w-100 text-end mb-2 p-3"
                            data-city-slug="{{ $city->slug }}"
                            data-city-name="{{ $city->name }}"
                            data-search-text="{{ strtolower($city->name) }}"
                            onclick="selectCity('{{ $city->slug }}', '{{ $city->name }}')"
                        >
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="text-end flex-grow-1">
                                    <div class="fw-medium">{{ $city->name }}</div>
                                    @if($city->governorate)
                                        <small class="text-muted">{{ $city->governorate }}</small>
                                    @endif
                                </div>
                                <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    <small class="fw-bold">{{ mb_substr($city->name, 0, 1) }}</small>
                                </div>
                            </div>
                        </button>
                    @endforeach
                </div>
            </div>
            
            <!-- Footer -->
            <div class="modal-footer border-top">
                <button 
                    type="button"
                    class="btn btn-link text-muted"
                    onclick="skipCitySelection()"
                >
                    تخطي الآن
                </button>
                <small class="text-muted">يمكنك تغيير المدينة لاحقاً</small>
            </div>
            
        </div>
    </div>
</div>

<script>
// Show modal function - OPTIMIZED
function showCityModal() {
    const modal = document.getElementById('citySelectionModal');
    if (modal) {
        modal.classList.remove('d-none');
        modal.classList.add('d-flex');
        document.body.style.overflow = 'hidden';
        
        // Focus on search input for better UX
        setTimeout(() => {
            const searchInput = document.getElementById('citySearch');
            if (searchInput) searchInput.focus();
        }, 100);
    }
}

// Close modal function - OPTIMIZED
function closeCityModal() {
    const modal = document.getElementById('citySelectionModal');
    if (modal) {
        modal.classList.add('d-none');
        modal.classList.remove('d-flex');
        document.body.style.overflow = '';
    }
}

// Select city function - OPTIMIZED VERSION
async function selectCity(citySlug, cityName) {
    try {
        console.log('🏙️ Selecting city:', citySlug, cityName);
        
        // Show quick loading state
        const selectedButton = document.querySelector(`button[data-city-slug="${citySlug}"]`);
        if (selectedButton) {
            const originalText = selectedButton.innerHTML;
            selectedButton.innerHTML = '<div class="text-center"><div class="spinner-border spinner-border-sm me-2" role="status"></div>جاري التحديد...</div>';
            selectedButton.disabled = true;
        }
        
        // Get CSRF token
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        if (!csrfToken) {
            throw new Error('CSRF token not found');
        }
        
        // Optimized AJAX request with shorter timeout
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), 5000); // 5 second timeout
        
        const response = await fetch('{{ route("set.city") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({ city_slug: citySlug }),
            signal: controller.signal
        });
        
        clearTimeout(timeoutId);
        
        if (!response.ok) {
            throw new Error(`HTTP ${response.status}: ${response.statusText}`);
        }
        
        const data = await response.json();
        console.log('✅ City selection response:', data);
        
        if (data.success) {
            // Show quick success feedback
            if (selectedButton) {
                selectedButton.innerHTML = '<div class="text-center text-success"><i class="fas fa-check me-2"></i>تم!</div>';
            }
            
            // Update city context immediately without page reload
            updateCityContext(cityName, citySlug);
            
            // Close modal quickly
            setTimeout(() => {
                closeCityModal();
            }, 500); // Reduced from 1000ms to 500ms
            
        } else {
            throw new Error(data.message || 'فشل في اختيار المدينة');
        }
    } catch (error) {
        console.error('💥 Error selecting city:', error);
        
        // Reset button state quickly
        const selectedButton = document.querySelector(`button[data-city-slug="${citySlug}"]`);
        if (selectedButton) {
            selectedButton.disabled = false;
            selectedButton.innerHTML = selectedButton.getAttribute('data-original-text') || 'إعادة المحاولة';
        }
        
        // Show error briefly
        alert('❌ حدث خطأ: ' + error.message);
    }
}

// NEW: Update city context dynamically without page reload
function updateCityContext(cityName, citySlug) {
    console.log('🔄 Updating city context dynamically...');
    
    // Update hero title
    const heroTitle = document.querySelector('.hero-text h1');
    if (heroTitle) {
        heroTitle.textContent = `اكتشف أفضل المتاجر في ${cityName}`;
    }
    
    // Update hero description
    const heroDesc = document.querySelector('.hero-text p');
    if (heroDesc) {
        heroDesc.textContent = `استعرض مئات المتاجر والخدمات المحلية في ${cityName}. اقرأ التقييمات، اكتشف العروض، واحصل على أفضل الصفقات.`;
    }
    
    // Add or update city context display
    let cityDisplay = document.querySelector('.city-context-display');
    if (!cityDisplay) {
        // Create city context display if it doesn't exist
        const heroSection = document.querySelector('.hero-content');
        if (heroSection) {
            cityDisplay = document.createElement('div');
            cityDisplay.className = 'city-context-display mb-4';
            cityDisplay.innerHTML = `
                <div class="selected-city-info bg-white bg-opacity-20 rounded-xl p-4 backdrop-blur-sm border border-white border-opacity-30">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="city-icon bg-white bg-opacity-30 rounded-circle p-2 me-3" style="width: 40px; height: 40px; display: flex; align-items: center; justify-content: center;">
                                <span style="font-size: 1.2rem;">📍</span>
                            </div>
                            <div>
                                <div class="city-name fw-bold text-white" style="font-size: 1.1rem;">${cityName}</div>
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
                            يتم عرض المحتوى الخاص بمدينة ${cityName} فقط
                        </small>
                    </div>
                </div>
            `;
            heroSection.insertBefore(cityDisplay, heroSection.firstChild);
        }
    } else {
        // Update existing city context display
        const cityNameEl = cityDisplay.querySelector('.city-name');
        if (cityNameEl) {
            cityNameEl.textContent = cityName;
        }
        
        const infoText = cityDisplay.querySelector('small i + text');
        if (infoText) {
            infoText.textContent = `يتم عرض المحتوى الخاص بمدينة ${cityName} فقط`;
        }
    }
    
    // Show success notification
    showSuccessNotification(`تم اختيار مدينة ${cityName} بنجاح!`);
}

// NEW: Show success notification without blocking UI
function showSuccessNotification(message) {
    // Create or update notification
    let notification = document.getElementById('city-success-notification');
    if (!notification) {
        notification = document.createElement('div');
        notification.id = 'city-success-notification';
        notification.style.cssText = `
            position: fixed;
            top: 20px;
            right: 20px;
            background: #28a745;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            z-index: 9999;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            transform: translateX(100%);
            transition: transform 0.3s ease;
        `;
        document.body.appendChild(notification);
    }
    
    notification.innerHTML = `<i class="fas fa-check-circle me-2"></i>${message}`;
    
    // Animate in
    setTimeout(() => {
        notification.style.transform = 'translateX(0)';
    }, 100);
    
    // Animate out
    setTimeout(() => {
        notification.style.transform = 'translateX(100%)';
        setTimeout(() => {
            notification.remove();
        }, 300);
    }, 3000);
}

// Skip city selection - OPTIMIZED VERSION
async function skipCitySelection() {
    try {
        console.log('⏭️ Skipping city selection...');
        
        const response = await fetch('{{ route("set.city") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json'
            },
            body: JSON.stringify({ skip: true }),
            signal: AbortSignal.timeout(3000) // 3 second timeout
        });
        
        const data = await response.json();
        
        if (data.success) {
            console.log('✅ City selection skipped');
            closeCityModal();
            showSuccessNotification('تم تخطي اختيار المدينة');
        }
    } catch (error) {
        console.error('💥 Error skipping city selection:', error);
        // Still close modal even if skip fails
        closeCityModal();
    }
}

// Search functionality
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('citySearch');
    const cityOptions = document.querySelectorAll('.city-option');
    
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const query = this.value.toLowerCase().trim();
            
            cityOptions.forEach(function(option) {
                const searchText = option.dataset.searchText || '';
                if (searchText.includes(query)) {
                    option.style.display = 'block';
                } else {
                    option.style.display = 'none';
                }
            });
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
});
</script>