@extends('layouts.app')

@section('content')
<!-- Hero Section -->
<div class="tawsela-hero" style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 60px 0;">
    <div class="container">
        <div class="hero-content text-center">
            <h1 class="hero-title mb-3">
                <i class="fas fa-car"></i>
                توصيلة - شارك رحلتك
            </h1>
            <p class="lead">وفّر في المصاريف واحمي البيئة بمشاركة رحلتك مع الآخرين</p>
        </div>
    </div>
</div>

<div class="container my-5">
    <!-- Action Buttons -->
    <div class="row mb-4">
        <div class="col-md-6 mb-3">
            @auth
            <a href="{{ route('tawsela.create') }}" class="btn btn-primary btn-lg w-100">
                <i class="fas fa-plus-circle"></i> أضف رحلة جديدة
            </a>
            @else
            <a href="{{ route('login') }}" class="btn btn-outline-primary btn-lg w-100">
                <i class="fas fa-sign-in-alt"></i> تسجيل الدخول لإضافة رحلة
            </a>
            @endauth
        </div>
        <div class="col-md-6 mb-3">
            <button class="btn btn-outline-primary btn-lg w-100" id="searchToggleBtn">
                <i class="fas fa-search"></i> ابحث عن رحلة
            </button>
        </div>
    </div>

    <!-- Search Form (Initially Hidden) -->
    <div class="card mb-4" id="searchForm" style="display: none;">
        <div class="card-body">
            <h5 class="card-title mb-4"><i class="fas fa-search"></i> ابحث عن رحلة</h5>
            <form id="rideSearchForm">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">المدينة</label>
                        <select name="city_id" class="form-select" id="citySelect">
                            <option value="">اختر المدينة</option>
                            @foreach($cities as $city)
                                <option value="{{ $city->id }}">{{ $city->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">المسافة القصوى (كم)</label>
                        <input type="number" name="max_distance" class="form-control" value="10" min="1" max="100">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">نقطة البداية</label>
                        <input type="text" id="startLocation" class="form-control" placeholder="ابحث عن موقعك">
                        <input type="hidden" name="start_lat" id="startLat">
                        <input type="hidden" name="start_lng" id="startLng">
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label">الوجهة</label>
                        <input type="text" id="destLocation" class="form-control" placeholder="ابحث عن وجهتك">
                        <input type="hidden" name="dest_lat" id="destLat">
                        <input type="hidden" name="dest_lng" id="destLng">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary">
                    <i class="fas fa-search"></i> بحث
                </button>
            </form>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-4 mb-2">
                    <select class="form-select" id="cityFilter">
                        <option value="">كل المدن</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}">{{ $city->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-2">
                    <select class="form-select" id="sortFilter">
                        <option value="newest">الأحدث</option>
                        <option value="soonest">الأقرب موعداً</option>
                        <option value="cheapest">الأرخص</option>
                    </select>
                </div>
                <div class="col-md-4 mb-2">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="hasSeatsFilter" checked>
                        <label class="form-check-label" for="hasSeatsFilter">
                            مقاعد متاحة فقط
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Rides List -->
    <div id="ridesContainer">
        <div class="text-center py-5">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">جاري التحميل...</span>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.tawsela-hero {
    position: relative;
    overflow: hidden;
}

.ride-card {
    border: 1px solid #e0e0e0;
    border-radius: 12px;
    transition: all 0.3s;
    margin-bottom: 20px;
}

.ride-card:hover {
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    transform: translateY(-2px);
}

.ride-header {
    padding: 20px;
    border-bottom: 1px solid #f0f0f0;
}

.ride-body {
    padding: 20px;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.user-avatar {
    width: 50px;
    height: 50px;
    border-radius: 50%;
    object-fit: cover;
}

.route-info {
    display: flex;
    align-items: center;
    gap: 15px;
    margin: 20px 0;
}

.location-point {
    display: flex;
    align-items: center;
    gap: 8px;
    flex: 1;
}

.route-arrow {
    color: #667eea;
    font-size: 24px;
}

.car-info {
    background: #f8f9fa;
    padding: 12px;
    border-radius: 8px;
    margin: 15px 0;
}

.price-badge {
    background: #667eea;
    color: white;
    padding: 8px 16px;
    border-radius: 20px;
    font-weight: bold;
}

.seats-badge {
    background: #28a745;
    color: white;
    padding: 6px 12px;
    border-radius: 15px;
    font-size: 0.9rem;
}

.negotiable-badge {
    background: #ffc107;
    color: #000;
    padding: 4px 10px;
    border-radius: 12px;
    font-size: 0.85rem;
}
</style>
@endpush

@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&libraries=places&language=ar"></script>
<script>
let ridesData = [];
let filteredRides = [];

// Toggle search form
document.getElementById('searchToggleBtn').addEventListener('click', function() {
    const searchForm = document.getElementById('searchForm');
    searchForm.style.display = searchForm.style.display === 'none' ? 'block' : 'none';
});

// Initialize Google Places Autocomplete
function initAutocomplete() {
    const startInput = document.getElementById('startLocation');
    const destInput = document.getElementById('destLocation');
    
    if (startInput && destInput) {
        const startAutocomplete = new google.maps.places.Autocomplete(startInput, {
            componentRestrictions: { country: 'eg' }
        });
        
        const destAutocomplete = new google.maps.places.Autocomplete(destInput, {
            componentRestrictions: { country: 'eg' }
        });
        
        startAutocomplete.addListener('place_changed', function() {
            const place = startAutocomplete.getPlace();
            if (place.geometry) {
                document.getElementById('startLat').value = place.geometry.location.lat();
                document.getElementById('startLng').value = place.geometry.location.lng();
            }
        });
        
        destAutocomplete.addListener('place_changed', function() {
            const place = destAutocomplete.getPlace();
            if (place.geometry) {
                document.getElementById('destLat').value = place.geometry.location.lat();
                document.getElementById('destLng').value = place.geometry.location.lng();
            }
        });
    }
}

// Load rides
async function loadRides(params = {}) {
    const container = document.getElementById('ridesContainer');
    container.innerHTML = '<div class="text-center py-5"><div class="spinner-border text-primary"></div></div>';
    
    try {
        const queryString = new URLSearchParams(params).toString();
        const response = await fetch(`/api/v1/tawsela/rides?${queryString}`);
        const data = await response.json();
        
        if (data.success) {
            ridesData = data.data.data;
            filteredRides = ridesData;
            renderRides(filteredRides);
        }
    } catch (error) {
        console.error('Error loading rides:', error);
        container.innerHTML = '<div class="alert alert-danger">حدث خطأ في تحميل الرحلات</div>';
    }
}

// Render rides
function renderRides(rides) {
    const container = document.getElementById('ridesContainer');
    
    if (rides.length === 0) {
        container.innerHTML = `
            <div class="text-center py-5">
                <i class="fas fa-car fa-3x text-muted mb-3"></i>
                <h4>لا توجد رحلات متاحة حالياً</h4>
                <p class="text-muted">جرب البحث بمعايير مختلفة أو أضف رحلتك الخاصة</p>
            </div>
        `;
        return;
    }
    
    container.innerHTML = rides.map(ride => `
        <div class="ride-card">
            <div class="ride-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div class="user-info">
                        <img src="${ride.user.avatar || '/images/default-avatar.png'}" alt="${ride.user.name}" class="user-avatar">
                        <div>
                            <h6 class="mb-0">${ride.user.name}</h6>
                            <small class="text-muted">
                                <i class="fas fa-clock"></i> 
                                ${new Date(ride.departure_time).toLocaleString('ar-EG')}
                            </small>
                        </div>
                    </div>
                    <div class="text-end">
                        <div class="price-badge mb-2">
                            ${ride.price} جنيه
                            ${ride.price_type === 'negotiable' ? '<span class="negotiable-badge ms-2">قابل للتفاوض</span>' : ''}
                        </div>
                        <small class="text-muted">${ride.price_unit === 'per_person' ? 'للشخص' : 'للرحلة'}</small>
                    </div>
                </div>
            </div>
            
            <div class="ride-body">
                <div class="route-info">
                    <div class="location-point">
                        <i class="fas fa-map-marker-alt text-success fa-lg"></i>
                        <div>
                            <small class="text-muted">من</small>
                            <div class="fw-bold">${ride.start_address}</div>
                        </div>
                    </div>
                    <i class="fas fa-arrow-left route-arrow"></i>
                    <div class="location-point">
                        <i class="fas fa-map-marker-alt text-danger fa-lg"></i>
                        <div>
                            <small class="text-muted">إلى</small>
                            <div class="fw-bold">${ride.destination_address}</div>
                        </div>
                    </div>
                </div>
                
                ${ride.stop_points && ride.stop_points.length > 0 ? `
                    <div class="mb-3">
                        <small class="text-muted">
                            <i class="fas fa-route"></i> نقاط توقف: ${ride.stop_points.length}
                        </small>
                    </div>
                ` : ''}
                
                <div class="car-info">
                    <div class="row">
                        <div class="col-md-8">
                            <i class="fas fa-car"></i>
                            <strong>${ride.car_model}</strong> - ${ride.car_year}
                            <span class="badge bg-secondary ms-2">${ride.car_color}</span>
                        </div>
                        <div class="col-md-4 text-end">
                            <span class="seats-badge">
                                <i class="fas fa-users"></i>
                                ${ride.remaining_seats} مقعد متاح
                            </span>
                        </div>
                    </div>
                </div>
                
                ${ride.notes ? `
                    <div class="mb-3">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> ${ride.notes}
                        </small>
                    </div>
                ` : ''}
                
                <div class="d-flex gap-2">
                    <a href="/tawsela/${ride.id}" class="btn btn-primary flex-grow-1">
                        <i class="fas fa-eye"></i> عرض التفاصيل
                    </a>
                    <a href="/tawsela/${ride.id}/request" class="btn btn-success">
                        <i class="fas fa-paper-plane"></i> طلب الانضمام
                    </a>
                </div>
            </div>
        </div>
    `).join('');
}

// Search form submission
document.getElementById('rideSearchForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const params = Object.fromEntries(formData);
    await loadRides(params);
});

// Filters
document.getElementById('cityFilter').addEventListener('change', function() {
    applyFilters();
});

document.getElementById('sortFilter').addEventListener('change', function() {
    applyFilters();
});

document.getElementById('hasSeatsFilter').addEventListener('change', function() {
    applyFilters();
});

function applyFilters() {
    let filtered = [...ridesData];
    
    // City filter
    const cityId = document.getElementById('cityFilter').value;
    if (cityId) {
        filtered = filtered.filter(ride => ride.city_id == cityId);
    }
    
    // Seats filter
    const hasSeats = document.getElementById('hasSeatsFilter').checked;
    if (hasSeats) {
        filtered = filtered.filter(ride => ride.remaining_seats > 0);
    }
    
    // Sort
    const sort = document.getElementById('sortFilter').value;
    if (sort === 'soonest') {
        filtered.sort((a, b) => new Date(a.departure_time) - new Date(b.departure_time));
    } else if (sort === 'cheapest') {
        filtered.sort((a, b) => a.price - b.price);
    } else {
        filtered.sort((a, b) => new Date(b.created_at) - new Date(a.created_at));
    }
    
    filteredRides = filtered;
    renderRides(filteredRides);
}

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    initAutocomplete();
    loadRides();
});
</script>
@endpush
@endsection
