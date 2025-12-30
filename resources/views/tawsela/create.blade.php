@extends('layouts.app')

@section('content')
<div class="container my-5">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h4 class="mb-0"><i class="fas fa-plus-circle"></i> إضافة رحلة جديدة</h4>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle"></i>
                        <strong>ملاحظة:</strong> تأكد من إدخال جميع البيانات بدقة لضمان تواصل أفضل مع الركاب.
                    </div>

                    <form id="rideForm">
                        @csrf

                        <!-- City -->
                        <div class="mb-4">
                            <label class="form-label required">المدينة</label>
                            <select name="city_id" class="form-select" required>
                                <option value="">اختر المدينة</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Car Details -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fas fa-car"></i> معلومات السيارة</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">موديل السيارة</label>
                                        <input type="text" name="car_model" class="form-control" placeholder="مثال: تويوتا كورولا" required>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label required">السنة</label>
                                        <input type="number" name="car_year" class="form-control" min="1900" max="{{ date('Y') + 1 }}" value="{{ date('Y') }}" required>
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label required">اللون</label>
                                        <input type="text" name="car_color" class="form-control" placeholder="أبيض" required>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label required">عدد المقاعد المتاحة</label>
                                    <select name="available_seats" class="form-select" required>
                                        <option value="">اختر عدد المقاعد</option>
                                        @for($i = 1; $i <= 7; $i++)
                                            <option value="{{ $i }}">{{ $i }} مقعد</option>
                                        @endfor
                                    </select>
                                </div>
                            </div>
                        </div>

                        <!-- Route Details -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fas fa-route"></i> معلومات الرحلة</h5>
                            </div>
                            <div class="card-body">
                                <!-- Start Location -->
                                <div class="mb-4">
                                    <label class="form-label required">
                                        <i class="fas fa-map-marker-alt text-success"></i> نقطة البداية
                                    </label>
                                    <input type="text" id="startLocation" class="form-control mb-2" placeholder="ابحث عن موقع البداية" required>
                                    <input type="hidden" name="start_latitude" id="startLat">
                                    <input type="hidden" name="start_longitude" id="startLng">
                                    <input type="text" name="start_address" id="startAddress" class="form-control" placeholder="العنوان بالتفصيل" required readonly>
                                    <div id="startMap" style="height: 300px; margin-top: 10px; border-radius: 8px;"></div>
                                </div>

                                <!-- Stop Points -->
                                <div class="mb-4">
                                    <label class="form-label">
                                        <i class="fas fa-map-pin text-warning"></i> نقاط توقف (اختياري)
                                    </label>
                                    <div id="stopPointsContainer"></div>
                                    <button type="button" class="btn btn-outline-primary btn-sm" id="addStopPoint">
                                        <i class="fas fa-plus"></i> إضافة نقطة توقف
                                    </button>
                                </div>

                                <!-- Destination -->
                                <div class="mb-4">
                                    <label class="form-label required">
                                        <i class="fas fa-map-marker-alt text-danger"></i> الوجهة النهائية
                                    </label>
                                    <input type="text" id="destLocation" class="form-control mb-2" placeholder="ابحث عن الوجهة" required>
                                    <input type="hidden" name="destination_latitude" id="destLat">
                                    <input type="hidden" name="destination_longitude" id="destLng">
                                    <input type="text" name="destination_address" id="destAddress" class="form-control" placeholder="العنوان بالتفصيل" required readonly>
                                    <div id="destMap" style="height: 300px; margin-top: 10px; border-radius: 8px;"></div>
                                </div>

                                <!-- Departure Time -->
                                <div class="mb-3">
                                    <label class="form-label required">
                                        <i class="fas fa-clock"></i> موعد المغادرة
                                    </label>
                                    <input type="datetime-local" name="departure_time" class="form-control" required min="{{ date('Y-m-d\TH:i') }}">
                                </div>
                            </div>
                        </div>

                        <!-- Pricing -->
                        <div class="card mb-4">
                            <div class="card-header bg-light">
                                <h5 class="mb-0"><i class="fas fa-money-bill-wave"></i> التسعير</h5>
                            </div>
                            <div class="card-body">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">السعر (جنيه)</label>
                                        <input type="number" name="price" class="form-control" min="0" step="0.01" placeholder="50.00" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label required">وحدة السعر</label>
                                        <select name="price_unit" class="form-select" required>
                                            <option value="per_person">للشخص الواحد</option>
                                            <option value="per_trip">للرحلة كاملة</option>
                                        </select>
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label required">نوع السعر</label>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="price_type" value="fixed" id="priceFixed" checked>
                                        <label class="form-check-label" for="priceFixed">
                                            سعر ثابت
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input" type="radio" name="price_type" value="negotiable" id="priceNegotiable">
                                        <label class="form-check-label" for="priceNegotiable">
                                            قابل للتفاوض
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Notes -->
                        <div class="mb-4">
                            <label class="form-label">
                                <i class="fas fa-sticky-note"></i> ملاحظات إضافية
                            </label>
                            <textarea name="notes" class="form-control" rows="3" placeholder="مثال: ممنوع التدخين - يُفضل الحجز المسبق"></textarea>
                        </div>

                        <!-- Submit -->
                        <div class="d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg" id="submitBtn">
                                <i class="fas fa-check-circle"></i> نشر الرحلة
                            </button>
                            <a href="{{ route('tawsela.index') }}" class="btn btn-outline-secondary">
                                إلغاء
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
.required::after {
    content: ' *';
    color: red;
}

.stop-point-item {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    margin-bottom: 10px;
    position: relative;
}

.stop-point-remove {
    position: absolute;
    top: 10px;
    left: 10px;
    background: #dc3545;
    color: white;
    border: none;
    border-radius: 50%;
    width: 30px;
    height: 30px;
    cursor: pointer;
}
</style>
@endpush

@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&libraries=places&language=ar"></script>
<script>
let startMap, destMap;
let startMarker, destMarker;
let stopPoints = [];

// Initialize maps
function initMaps() {
    const mapOptions = {
        zoom: 13,
        center: { lat: 30.0444, lng: 31.2357 }, // Cairo default
    };
    
    startMap = new google.maps.Map(document.getElementById('startMap'), mapOptions);
    destMap = new google.maps.Map(document.getElementById('destMap'), mapOptions);
    
    // Initialize autocomplete
    initAutocomplete();
    
    // Try to get user's current location
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const pos = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };
            startMap.setCenter(pos);
            destMap.setCenter(pos);
        });
    }
}

function initAutocomplete() {
    const startInput = document.getElementById('startLocation');
    const destInput = document.getElementById('destLocation');
    
    const startAutocomplete = new google.maps.places.Autocomplete(startInput, {
        componentRestrictions: { country: 'eg' }
    });
    
    const destAutocomplete = new google.maps.places.Autocomplete(destInput, {
        componentRestrictions: { country: 'eg' }
    });
    
    startAutocomplete.addListener('place_changed', function() {
        const place = startAutocomplete.getPlace();
        if (place.geometry) {
            const location = place.geometry.location;
            document.getElementById('startLat').value = location.lat();
            document.getElementById('startLng').value = location.lng();
            document.getElementById('startAddress').value = place.formatted_address;
            
            startMap.setCenter(location);
            startMap.setZoom(15);
            
            if (startMarker) startMarker.setMap(null);
            startMarker = new google.maps.Marker({
                position: location,
                map: startMap,
                title: 'نقطة البداية',
                icon: 'http://maps.google.com/mapfiles/ms/icons/green-dot.png'
            });
        }
    });
    
    destAutocomplete.addListener('place_changed', function() {
        const place = destAutocomplete.getPlace();
        if (place.geometry) {
            const location = place.geometry.location;
            document.getElementById('destLat').value = location.lat();
            document.getElementById('destLng').value = location.lng();
            document.getElementById('destAddress').value = place.formatted_address;
            
            destMap.setCenter(location);
            destMap.setZoom(15);
            
            if (destMarker) destMarker.setMap(null);
            destMarker = new google.maps.Marker({
                position: location,
                map: destMap,
                title: 'الوجهة',
                icon: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png'
            });
        }
    });
}

// Stop points management
let stopPointIndex = 0;
document.getElementById('addStopPoint').addEventListener('click', function() {
    stopPointIndex++;
    const container = document.getElementById('stopPointsContainer');
    const stopPointHtml = `
        <div class="stop-point-item" data-index="${stopPointIndex}">
            <button type="button" class="stop-point-remove" onclick="removeStopPoint(${stopPointIndex})">
                <i class="fas fa-times"></i>
            </button>
            <input type="text" class="form-control mb-2 stop-location" placeholder="ابحث عن نقطة التوقف">
            <input type="hidden" name="stop_points[${stopPointIndex}][latitude]" class="stop-lat">
            <input type="hidden" name="stop_points[${stopPointIndex}][longitude]" class="stop-lng">
            <input type="text" name="stop_points[${stopPointIndex}][address]" class="form-control stop-address" placeholder="العنوان بالتفصيل" readonly required>
        </div>
    `;
    container.insertAdjacentHTML('beforeend', stopPointHtml);
    
    // Initialize autocomplete for this stop point
    const input = container.querySelector(`[data-index="${stopPointIndex}"] .stop-location`);
    const autocomplete = new google.maps.places.Autocomplete(input, {
        componentRestrictions: { country: 'eg' }
    });
    
    autocomplete.addListener('place_changed', function() {
        const place = autocomplete.getPlace();
        if (place.geometry) {
            const item = input.closest('.stop-point-item');
            item.querySelector('.stop-lat').value = place.geometry.location.lat();
            item.querySelector('.stop-lng').value = place.geometry.location.lng();
            item.querySelector('.stop-address').value = place.formatted_address;
        }
    });
});

function removeStopPoint(index) {
    document.querySelector(`[data-index="${index}"]`).remove();
}

// Form submission
document.getElementById('rideForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    const submitBtn = document.getElementById('submitBtn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري النشر...';
    
    const formData = new FormData(this);
    
    // Convert stop points to proper JSON format
    const stopPointsData = [];
    document.querySelectorAll('.stop-point-item').forEach(item => {
        stopPointsData.push({
            latitude: parseFloat(item.querySelector('.stop-lat').value),
            longitude: parseFloat(item.querySelector('.stop-lng').value),
            address: item.querySelector('.stop-address').value
        });
    });
    
    const data = {
        city_id: formData.get('city_id'),
        car_model: formData.get('car_model'),
        car_year: parseInt(formData.get('car_year')),
        car_color: formData.get('car_color'),
        available_seats: parseInt(formData.get('available_seats')),
        start_latitude: parseFloat(formData.get('start_latitude')),
        start_longitude: parseFloat(formData.get('start_longitude')),
        start_address: formData.get('start_address'),
        destination_latitude: parseFloat(formData.get('destination_latitude')),
        destination_longitude: parseFloat(formData.get('destination_longitude')),
        destination_address: formData.get('destination_address'),
        stop_points: stopPointsData.length > 0 ? stopPointsData : null,
        price: parseFloat(formData.get('price')),
        price_type: formData.get('price_type'),
        price_unit: formData.get('price_unit'),
        departure_time: formData.get('departure_time'),
        notes: formData.get('notes')
    };
    
    try {
        const response = await fetch('/api/v1/tawsela/rides', {
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
            alert('تم نشر الرحلة بنجاح!');
            window.location.href = '/tawsela/' + result.data.id;
        } else {
            alert('حدث خطأ: ' + (result.message || 'يرجى المحاولة مرة أخرى'));
            submitBtn.disabled = false;
            submitBtn.innerHTML = '<i class="fas fa-check-circle"></i> نشر الرحلة';
        }
    } catch (error) {
        console.error('Error:', error);
        alert('حدث خطأ في الاتصال بالخادم');
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="fas fa-check-circle"></i> نشر الرحلة';
    }
});

// Initialize on page load
document.addEventListener('DOMContentLoaded', function() {
    initMaps();
});
</script>
@endpush
@endsection
