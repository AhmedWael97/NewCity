@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row">
        <div class="col-lg-8">
            <!-- Ride Details Card -->
            <div class="card shadow mb-4" id="rideDetails">
                <div class="text-center py-5">
                    <div class="spinner-border text-primary"></div>
                </div>
            </div>

            <!-- Map -->
            <div class="card shadow mb-4">
                <div class="card-header bg-light">
                    <h5 class="mb-0"><i class="fas fa-map"></i> خريطة الرحلة</h5>
                </div>
                <div class="card-body p-0">
                    <div id="rideMap" style="height: 400px;"></div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            @auth
            <!-- Request Card (Authenticated Users Only) -->
            <div class="card shadow mb-4">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-paper-plane"></i> طلب الانضمام</h5>
                </div>
                <div class="card-body">
                    <form id="requestForm">
                        <div class="mb-3">
                            <label class="form-label">نقطة الصعود</label>
                            <input type="text" id="pickupLocation" class="form-control mb-2" placeholder="ابحث عن موقعك" required>
                            <input type="hidden" name="pickup_latitude" id="pickupLat">
                            <input type="hidden" name="pickup_longitude" id="pickupLng">
                            <input type="text" name="pickup_address" id="pickupAddress" class="form-control" placeholder="العنوان بالتفصيل" required readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">نقطة النزول (اختياري)</label>
                            <input type="text" id="dropoffLocation" class="form-control mb-2" placeholder="إذا كانت مختلفة عن الوجهة النهائية">
                            <input type="hidden" name="dropoff_latitude" id="dropoffLat">
                            <input type="hidden" name="dropoff_longitude" id="dropoffLng">
                            <input type="text" name="dropoff_address" id="dropoffAddress" class="form-control" placeholder="العنوان بالتفصيل" readonly>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">عدد الركاب</label>
                            <select name="passengers_count" class="form-select" required>
                                <option value="1">1 راكب</option>
                                <option value="2">2 ركاب</option>
                                <option value="3">3 ركاب</option>
                                <option value="4">4 ركاب</option>
                            </select>
                        </div>

                        <div class="mb-3" id="offerPriceContainer" style="display: none;">
                            <label class="form-label">السعر المعروض (اختياري)</label>
                            <input type="number" name="offered_price" class="form-control" min="0" step="0.01" placeholder="اترك فارغاً لقبول السعر الأصلي">
                        </div>

                        <div class="mb-3">
                            <label class="form-label">رسالة للسائق</label>
                            <textarea name="message" class="form-control" rows="3" placeholder="اكتب رسالة للسائق..." required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary w-100" id="requestBtn">
                            <i class="fas fa-paper-plane"></i> إرسال الطلب
                        </button>
                    </form>
                </div>
            </div>
            @else
            <!-- Guest Login Prompt -->
            <div class="card shadow mb-4">
                <div class="card-header bg-warning text-dark">
                    <h5 class="mb-0"><i class="fas fa-lock"></i> تسجيل الدخول مطلوب</h5>
                </div>
                <div class="card-body text-center">
                    <i class="fas fa-user-lock fa-3x text-warning mb-3"></i>
                    <p class="mb-3">يجب تسجيل الدخول لإرسال طلب انضمام أو التواصل مع السائق</p>
                    <a href="{{ route('login') }}" class="btn btn-primary w-100 mb-2">
                        <i class="fas fa-sign-in-alt"></i> تسجيل الدخول
                    </a>
                    <a href="{{ route('register') }}" class="btn btn-outline-primary w-100">
                        <i class="fas fa-user-plus"></i> إنشاء حساب جديد
                    </a>
                </div>
            </div>
            @endauth

            <!-- Contact Card (Authenticated Users Only) -->
            <div class="card shadow" id="contactCard" style="display: none;">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0"><i class="fas fa-phone"></i> معلومات الاتصال</h5>
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        <i class="fas fa-phone"></i>
                        <strong>الهاتف:</strong>
                        <span id="driverPhone"></span>
                    </p>
                    <a href="#" class="btn btn-success w-100" id="whatsappBtn" target="_blank">
                        <i class="fab fa-whatsapp"></i> تواصل عبر واتساب
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.user-info-header {
    display: flex;
    align-items: center;
    gap: 15px;
    padding: 20px;
    border-bottom: 2px solid #f0f0f0;
}

.user-avatar-large {
    width: 80px;
    height: 80px;
    border-radius: 50%;
    object-fit: cover;
    border: 3px solid #667eea;
}

.route-display {
    padding: 20px;
}

.location-card {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    margin: 10px 0;
}

.info-row {
    display: flex;
    justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid #e0e0e0;
}

.info-row:last-child {
    border-bottom: none;
}
</style>
@endpush

@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&libraries=places&language=ar"></script>
<script>
const rideId = {!! json_encode($id) !!};
let rideData = null;
let map = null;

// Load ride details
async function loadRideDetails() {
    try {
        const response = await fetch(`/api/v1/fe-tare2k/rides/${rideId}`);
        const data = await response.json();
        
        if (data.success) {
            rideData = data.data;
            renderRideDetails(rideData);
            initMap(rideData);
            
            // Show offer price field if negotiable
            if (rideData.price_type === 'negotiable') {
                document.getElementById('offerPriceContainer').style.display = 'block';
            }
        }
    } catch (error) {
        console.error('Error loading ride:', error);
        document.getElementById('rideDetails').innerHTML = `
            <div class="alert alert-danger m-3">حدث خطأ في تحميل تفاصيل الرحلة</div>
        `;
    }
}

function renderRideDetails(ride) {
    const container = document.getElementById('rideDetails');
    const isAuthenticated = ride.is_authenticated;
    
    let headerHtml = '';
    if (isAuthenticated && ride.user) {
        headerHtml = `
        <div class="user-info-header">
            <img src="${ride.user.avatar || '/images/default-avatar.png'}" alt="${ride.user.name}" class="user-avatar-large">
            <div>
                <h4 class="mb-1">${ride.user.name}</h4>
                <p class="text-muted mb-0">
                    <i class="fas fa-clock"></i> 
                    ${new Date(ride.departure_time).toLocaleString('ar-EG', { 
                        weekday: 'long', 
                        year: 'numeric', 
                        month: 'long', 
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    })}
                </p>
            </div>
        </div>`;
    } else {
        headerHtml = `
        <div class="card-header bg-light">
            <h5 class="mb-0">
                <i class="fas fa-route"></i> تفاصيل الرحلة
                <span class="badge bg-primary float-end">
                    <i class="fas fa-clock"></i> 
                    ${new Date(ride.departure_time).toLocaleString('ar-EG', { 
                        month: 'short', 
                        day: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit'
                    })}
                </span>
            </h5>
        </div>`;
    }
    
    container.innerHTML = headerHtml + `
        
        <div class="route-display">
            <div class="location-card">
                <h6 class="text-success mb-2">
                    <i class="fas fa-map-marker-alt"></i> نقطة البداية
                </h6>
                <p class="mb-0">${ride.start_address}</p>
            </div>
            
            ${ride.stop_points && ride.stop_points.length > 0 ? `
                <div class="text-center my-3">
                    <i class="fas fa-arrow-down text-muted"></i>
                    <small class="text-muted d-block">${ride.stop_points.length} نقطة توقف</small>
                </div>
                ${ride.stop_points.map(stop => `
                    <div class="location-card">
                        <h6 class="text-warning mb-2">
                            <i class="fas fa-map-pin"></i> نقطة توقف
                        </h6>
                        <p class="mb-0">${stop.address}</p>
                    </div>
                `).join('')}
            ` : `
                <div class="text-center my-3">
                    <i class="fas fa-arrow-down text-muted fa-2x"></i>
                </div>
            `}
            
            <div class="location-card">
                <h6 class="text-danger mb-2">
                    <i class="fas fa-map-marker-alt"></i> الوجهة النهائية
                </h6>
                <p class="mb-0">${ride.destination_address}</p>
            </div>
        </div>
        
        <div class="card-body">
            ${isAuthenticated ? `
            <h5 class="mb-3"><i class="fas fa-car"></i> معلومات السيارة</h5>
            <div class="info-row">
                <span>الموديل:</span>
                <strong>${ride.car_model} - ${ride.car_year}</strong>
            </div>
            <div class="info-row">
                <span>اللون:</span>
                <strong>${ride.car_color}</strong>
            </div>
            ` : ''}
            <div class="info-row">
                <span>المقاعد المتاحة:</span>
                <strong class="text-success">${ride.remaining_seats} من ${ride.available_seats}</strong>
            </div>
            <div class="info-row" id="tripDurationRow" style="display: none;">
                <span>مدة الرحلة التقريبية:</span>
                <strong class="text-info" id="tripDuration">
                    <i class="fas fa-spinner fa-spin"></i> جاري الحساب...
                </strong>
            </div>
            <div class="info-row" id="tripDistanceRow" style="display: none;">
                <span>المسافة الكلية:</span>
                <strong class="text-secondary" id="tripDistance">
                    <i class="fas fa-spinner fa-spin"></i> جاري الحساب...
                </strong>
            </div>
            
            <hr class="my-4">
            
            <h5 class="mb-3"><i class="fas fa-money-bill-wave"></i> التسعير</h5>
            <div class="info-row">
                <span>السعر:</span>
                <strong class="text-primary fs-4">${ride.price} جنيه</strong>
            </div>
            <div class="info-row">
                <span>نوع السعر:</span>
                <strong>${ride.price_type === 'fixed' ? 'ثابت' : 'قابل للتفاوض'}</strong>
            </div>
            <div class="info-row">
                <span>وحدة السعر:</span>
                <strong>${ride.price_unit === 'per_person' ? 'للشخص' : 'للرحلة'}</strong>
            </div>
            
            ${isAuthenticated && ride.notes ? `
                <hr class="my-4">
                <h5 class="mb-3"><i class="fas fa-sticky-note"></i> ملاحظات</h5>
                <p class="text-muted">${ride.notes}</p>
            ` : ''}
        </div>
    `;
}

function initMap(ride) {
    const bounds = new google.maps.LatLngBounds();
    
    map = new google.maps.Map(document.getElementById('rideMap'), {
        zoom: 12
    });
    
    // Start marker
    const startMarker = new google.maps.Marker({
        position: { lat: parseFloat(ride.start_latitude), lng: parseFloat(ride.start_longitude) },
        map: map,
        title: 'نقطة البداية',
        icon: 'http://maps.google.com/mapfiles/ms/icons/green-dot.png',
        label: {
            text: 'أ',
            color: 'white',
            fontWeight: 'bold'
        }
    });
    bounds.extend(startMarker.position);
    
    // Stop points
    if (ride.stop_points && ride.stop_points.length > 0) {
        ride.stop_points.forEach((stop, index) => {
            const marker = new google.maps.Marker({
                position: { lat: parseFloat(stop.latitude), lng: parseFloat(stop.longitude) },
                map: map,
                title: `نقطة توقف ${index + 1}`,
                icon: 'http://maps.google.com/mapfiles/ms/icons/yellow-dot.png',
                label: {
                    text: `${index + 1}`,
                    color: 'black',
                    fontWeight: 'bold'
                }
            });
            bounds.extend(marker.position);
        });
    }
    
    // Destination marker
    const destMarker = new google.maps.Marker({
        position: { lat: parseFloat(ride.destination_latitude), lng: parseFloat(ride.destination_longitude) },
        map: map,
        title: 'الوجهة',
        icon: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png',
        label: {
            text: 'ب',
            color: 'white',
            fontWeight: 'bold'
        }
    });
    bounds.extend(destMarker.position);
    
    map.fitBounds(bounds);
    
    // Draw route with directions
    const directionsService = new google.maps.DirectionsService();
    const directionsRenderer = new google.maps.DirectionsRenderer({
        map: map,
        suppressMarkers: true, // We're using custom markers
        polylineOptions: {
            strokeColor: '#4285F4',
            strokeWeight: 5,
            strokeOpacity: 0.8
        }
    });
    
    const waypoints = [];
    if (ride.stop_points && ride.stop_points.length > 0) {
        ride.stop_points.forEach(stop => {
            waypoints.push({
                location: { lat: parseFloat(stop.latitude), lng: parseFloat(stop.longitude) },
                stopover: true
            });
        });
    }
    
    directionsService.route({
        origin: { lat: parseFloat(ride.start_latitude), lng: parseFloat(ride.start_longitude) },
        destination: { lat: parseFloat(ride.destination_latitude), lng: parseFloat(ride.destination_longitude) },
        waypoints: waypoints,
        travelMode: google.maps.TravelMode.DRIVING
    }, function(result, status) {
        if (status === 'OK') {
            directionsRenderer.setDirections(result);
            
            // Calculate total duration and distance
            let totalDuration = 0;
            let totalDistance = 0;
            
            result.routes[0].legs.forEach(leg => {
                totalDuration += leg.duration.value; // in seconds
                totalDistance += leg.distance.value; // in meters
            });
            
            // Convert and display
            const hours = Math.floor(totalDuration / 3600);
            const minutes = Math.floor((totalDuration % 3600) / 60);
            
            let durationText = '';
            if (hours > 0) {
                durationText = `${hours} ساعة`;
                if (minutes > 0) {
                    durationText += ` و ${minutes} دقيقة`;
                }
            } else {
                durationText = `${minutes} دقيقة`;
            }
            
            const distanceKm = (totalDistance / 1000).toFixed(1);
            
            // Update the UI
            document.getElementById('tripDuration').innerHTML = `<i class="fas fa-clock"></i> ${durationText}`;
            document.getElementById('tripDistance').innerHTML = `<i class="fas fa-road"></i> ${distanceKm} كم`;
            document.getElementById('tripDurationRow').style.display = 'flex';
            document.getElementById('tripDistanceRow').style.display = 'flex';
        } else {
            console.error('Directions request failed:', status);
            // Hide duration/distance rows if calculation fails
            document.getElementById('tripDurationRow').style.display = 'none';
            document.getElementById('tripDistanceRow').style.display = 'none';
        }
    });
}

// Initialize autocomplete for request form
function initRequestAutocomplete() {
    const pickupInput = document.getElementById('pickupLocation');
    const dropoffInput = document.getElementById('dropoffLocation');
    
    const pickupAutocomplete = new google.maps.places.Autocomplete(pickupInput, {
        componentRestrictions: { country: 'eg' }
    });
    
    const dropoffAutocomplete = new google.maps.places.Autocomplete(dropoffInput, {
        componentRestrictions: { country: 'eg' }
    });
    
    pickupAutocomplete.addListener('place_changed', function() {
        const place = pickupAutocomplete.getPlace();
        if (place.geometry) {
            document.getElementById('pickupLat').value = place.geometry.location.lat();
            document.getElementById('pickupLng').value = place.geometry.location.lng();
            document.getElementById('pickupAddress').value = place.formatted_address;
        }
    });
    
    dropoffAutocomplete.addListener('place_changed', function() {
        const place = dropoffAutocomplete.getPlace();
        if (place.geometry) {
            document.getElementById('dropoffLat').value = place.geometry.location.lat();
            document.getElementById('dropoffLng').value = place.geometry.location.lng();
            document.getElementById('dropoffAddress').value = place.formatted_address;
        }
    });
}

// Request form submission
document.getElementById('requestForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // Check if user is authenticated
    @guest
        alert('يجب تسجيل الدخول أولاً');
        window.location.href = '/login';
        return;
    @endguest
    
    const requestBtn = document.getElementById('requestBtn');
    requestBtn.disabled = true;
    requestBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الإرسال...';
    
    const formData = new FormData(this);
    const data = {
        pickup_latitude: parseFloat(formData.get('pickup_latitude')),
        pickup_longitude: parseFloat(formData.get('pickup_longitude')),
        pickup_address: formData.get('pickup_address'),
        passengers_count: parseInt(formData.get('passengers_count')),
        message: formData.get('message')
    };
    
    if (formData.get('dropoff_latitude')) {
        data.dropoff_latitude = parseFloat(formData.get('dropoff_latitude'));
        data.dropoff_longitude = parseFloat(formData.get('dropoff_longitude'));
        data.dropoff_address = formData.get('dropoff_address');
    }
    
    if (formData.get('offered_price')) {
        data.offered_price = parseFloat(formData.get('offered_price'));
    }
    
    try {
        const response = await fetch(`/api/v1/fe-tare2k/rides/${rideId}/request`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify(data)
        });
        
        const result = await response.json();
        
        if (result.success) {
            alert('تم إرسال طلبك بنجاح! سيتم إشعارك عند قبول الطلب.');
            window.location.href = '/fe-tare2k/my-requests';
        } else {
            alert('حدث خطأ: ' + (result.message || 'يرجى المحاولة مرة أخرى'));
        }
    } catch (error) {
        console.error('Error:', error);
        alert('حدث خطأ في الاتصال بالخادم');
    } finally {
        requestBtn.disabled = false;
        requestBtn.innerHTML = '<i class="fas fa-paper-plane"></i> إرسال الطلب';
    }
});

// Initialize
document.addEventListener('DOMContentLoaded', function() {
    loadRideDetails();
    initRequestAutocomplete();
});
</script>
@endpush
@endsection
