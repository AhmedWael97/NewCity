{{-- Simple Working City Selection Modal --}}
@props(['showModal' => false])

@php
    // Check if city is already selected
    $hasCitySelected = session()->has('selected_city') || session()->has('city_slug');
@endphp

<div id="cityModal" class="modal fade {{ $showModal && !$hasCitySelected ? 'show' : '' }}" 
     style="display: {{ $showModal && !$hasCitySelected ? 'block' : 'none' }}; background: rgba(0,0,0,0.5);"
     tabindex="-1"
     data-has-city="{{ $hasCitySelected ? 'true' : 'false' }}">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content" style="border-radius: 15px;">
            <!-- Header -->
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">
                    <i class="fas fa-map-marker-alt me-2"></i>
                    اختر مدينتك
                </h5>
            </div>

            <!-- Body -->
            <div class="modal-body p-4">
                <!-- Search -->
                <div class="mb-4">
                    <input type="text" 
                           id="citySearchInput" 
                           class="form-control form-control-lg" 
                           placeholder="ابحث عن مدينتك..."
                           style="text-align: right;">
                </div>

                <!-- Loading -->
                <div id="loadingSpinner" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status">
                        <span class="visually-hidden">جاري التحميل...</span>
                    </div>
                    <p class="mt-3 text-muted">جاري تحميل المدن...</p>
                </div>

                <!-- Cities Grid -->
                <div id="citiesGrid" class="row g-3" style="display: none;">
                    <!-- Cities will be inserted here -->
                </div>

                <!-- No Results -->
                <div id="noResultsMsg" class="text-center py-5" style="display: none;">
                    <i class="fas fa-search text-muted" style="font-size: 3rem;"></i>
                    <p class="mt-3 text-muted">لم نجد مدن مطابقة</p>
                </div>
            </div>

            <!-- Footer -->
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="skipCity()">
                    تخطي الآن
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let citiesData = [];
let modalAlreadyShown = false;

// Check if modal should show
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('cityModal');
    const hasCitySelected = modal.getAttribute('data-has-city') === 'true';
    
    @if($showModal)
        // Only show if no city is selected yet
        if (!hasCitySelected) {
            // Also check localStorage to prevent showing on every page load
            const hasSeenModal = localStorage.getItem('cityModalSeen') === 'true';
            if (!hasSeenModal) {
                loadCities();
                localStorage.setItem('cityModalSeen', 'true');
            } else {
                modal.style.display = 'none';
            }
        } else {
            // City already selected, don't show
            modal.style.display = 'none';
        }
    @endif
});

// Load cities from API
function loadCities() {
    fetch('/api/v1/cities-selection')
        .then(response => response.json())
        .then(data => {
            console.log('Cities loaded:', data);
            if (data.success && data.cities) {
                citiesData = data.cities;
                displayCities(citiesData);
                document.getElementById('loadingSpinner').style.display = 'none';
                document.getElementById('citiesGrid').style.display = 'flex';
            }
        })
        .catch(error => {
            console.error('Error loading cities:', error);
            document.getElementById('loadingSpinner').innerHTML = 
                '<div class="alert alert-danger">حدث خطأ في تحميل المدن</div>';
        });
}

// Display cities in grid
function displayCities(cities) {
    const grid = document.getElementById('citiesGrid');
    const noResults = document.getElementById('noResultsMsg');
    
    if (cities.length === 0) {
        grid.style.display = 'none';
        noResults.style.display = 'block';
        return;
    }
    
    grid.style.display = 'flex';
    noResults.style.display = 'none';
    
    grid.innerHTML = cities.map(city => `
        <div class="col-md-6">
            <div class="card h-100 border-0 shadow-sm hover-shadow" 
                 onclick="selectCity('${city.slug}', '${city.name}')" 
                 style="cursor: pointer; transition: all 0.3s;">
                <div class="card-body">
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="d-flex align-items-center">
                            <div class="me-3" style="font-size: 1.5rem; color: #4e73df;">
                                <i class="fas fa-map-marker-alt"></i>
                            </div>
                            <div style="text-align: right;">
                                <h6 class="mb-0 fw-bold">${city.name}</h6>
                                ${city.state ? `<small class="text-muted">${city.state}</small>` : ''}
                            </div>
                        </div>
                        <i class="fas fa-chevron-left text-muted"></i>
                    </div>
                </div>
            </div>
        </div>
    `).join('');
}

// Search cities
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.getElementById('citySearchInput');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const filtered = citiesData.filter(city => 
                city.name.toLowerCase().includes(searchTerm) ||
                (city.state && city.state.toLowerCase().includes(searchTerm))
            );
            displayCities(filtered);
        });
    }
});

// Select city
function selectCity(slug, name) {
    // Show loading in the selected card
    const cards = document.querySelectorAll('.card');
    cards.forEach(card => card.style.opacity = '0.5');
    
    // Mark that user has selected a city
    localStorage.setItem('cityModalSeen', 'true');
    
    // Send to server
    fetch('/set-city', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ city_slug: slug, city_name: name })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // City selected successfully, reload to apply changes
            window.location.reload();
        } else {
            alert('حدث خطأ، حاول مرة أخرى');
            cards.forEach(card => card.style.opacity = '1');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('حدث خطأ، حاول مرة أخرى');
        cards.forEach(card => card.style.opacity = '1');
    });
}

// Skip city selection
function skipCity() {
    document.getElementById('cityModal').style.display = 'none';
}

// Show modal function (for change city button)
function showCityModal() {
    const modal = document.getElementById('cityModal');
    modal.style.display = 'block';
    modal.classList.add('show');
    
    // Reset search
    const searchInput = document.getElementById('citySearchInput');
    if (searchInput) {
        searchInput.value = '';
    }
    
    // Load cities if not loaded
    if (citiesData.length === 0) {
        document.getElementById('loadingSpinner').style.display = 'block';
        document.getElementById('citiesGrid').style.display = 'none';
        loadCities();
    } else {
        // Cities already loaded, just show them
        document.getElementById('loadingSpinner').style.display = 'none';
        document.getElementById('citiesGrid').style.display = 'flex';
        displayCities(citiesData);
    }
}

// Close modal when clicking outside
document.addEventListener('click', function(event) {
    const modal = document.getElementById('cityModal');
    if (event.target === modal) {
        modal.style.display = 'none';
        modal.classList.remove('show');
    }
});
</script>

<style>
.hover-shadow {
    transition: box-shadow 0.3s ease !important;
}
.hover-shadow:hover {
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
    transform: translateY(-2px);
}
</style>
