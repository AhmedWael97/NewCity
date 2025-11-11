@extends('layouts.minimal')

@section('title', 'اختر مدينتك')

@section('content')
<div class="select-city-page min-vh-100 d-flex align-items-center" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);">
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
                        <input type="text" 
                               id="citySearchInput" 
                               class="form-control border-0 pe-5" 
                               placeholder="ابحث عن مدينتك..."
                               style="text-align: right; font-size: 1.1rem;">
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
                @forelse($cities as $city)
                    <div class="col-lg-3 col-md-4 col-sm-6 city-item" 
                         data-name="{{ strtolower($city->name) }}"
                         data-state="{{ strtolower($city->state ?? '') }}"
                         data-country="{{ strtolower($city->country ?? '') }}">
                        <div class="city-card shadow-lg" onclick="selectCity('{{ $city->slug }}', '{{ $city->name }}', this)">
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

    </div>
</div>

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
</style>
@endpush

@push('scripts')
<script>
// Search functionality
document.getElementById('citySearchInput').addEventListener('input', function() {
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
window.addEventListener('DOMContentLoaded', function() {
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
</script>
@endpush
@endsection
