@props([
    'addressId' => 'address',
    'latitudeId' => 'latitude',
    'longitudeId' => 'longitude',
    'addressValue' => '',
    'latitudeValue' => '',
    'longitudeValue' => '',
    'height' => '400px',
    'defaultLat' => 24.774265,
    'defaultLng' => 46.738586,
    'defaultZoom' => 12
])

<div class="google-maps-picker mb-4">
    <div class="card">
        <div class="card-header bg-primary text-white">
            <h6 class="mb-0">
                <i class="fas fa-map-marked-alt"></i> اختر الموقع على الخريطة
            </h6>
        </div>
        <div class="card-body">
            <div class="alert alert-info">
                <i class="fas fa-info-circle"></i>
                <strong>تعليمات:</strong> انقر على الخريطة لتحديد موقع المتجر، أو ابحث عن العنوان في صندوق البحث أعلى الخريطة
            </div>
            
            <!-- Search Box -->
            <div class="mb-3">
                <div class="input-group">
                    <span class="input-group-text">
                        <i class="fas fa-search"></i>
                    </span>
                    <input type="text" 
                           id="map-search-{{ $addressId }}" 
                           class="form-control" 
                           placeholder="ابحث عن عنوان أو مكان...">
                    <button type="button" 
                            class="btn btn-primary" 
                            onclick="searchLocation{{ $addressId }}()">
                        <i class="fas fa-search"></i> بحث
                    </button>
                </div>
            </div>

            <!-- Map Container -->
            <div id="map-{{ $addressId }}" style="width: 100%; height: {{ $height }}; border-radius: 8px; border: 2px solid #dee2e6;"></div>
            
            <!-- Current Location Button -->
            <div class="mt-3">
                <button type="button" 
                        class="btn btn-success btn-sm" 
                        onclick="getCurrentLocation{{ $addressId }}()">
                    <i class="fas fa-crosshairs"></i> استخدم موقعي الحالي
                </button>
                <button type="button" 
                        class="btn btn-secondary btn-sm" 
                        onclick="resetMap{{ $addressId }}()">
                    <i class="fas fa-redo"></i> إعادة تعيين
                </button>
            </div>
        </div>
    </div>
</div>

@push('styles')
<style>
    .google-maps-picker .gm-style-iw {
        text-align: right !important;
        direction: rtl !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCeaKlnTU88qhTp7za2H301HWPPT7zhGyo&libraries=places&language=ar"></script>
<script>
    let map{{ $addressId }};
    let marker{{ $addressId }};
    let geocoder{{ $addressId }};
    let searchBox{{ $addressId }};
    
    function initMap{{ $addressId }}() {
        // Get existing values or use defaults
        const lat = parseFloat(document.getElementById('{{ $latitudeId }}').value) || {{ $defaultLat }};
        const lng = parseFloat(document.getElementById('{{ $longitudeId }}').value) || {{ $defaultLng }};
        
        const mapCenter = { lat: lat, lng: lng };
        
        // Initialize map
        map{{ $addressId }} = new google.maps.Map(document.getElementById('map-{{ $addressId }}'), {
            center: mapCenter,
            zoom: {{ $defaultZoom }},
            mapTypeControl: true,
            streetViewControl: true,
            fullscreenControl: true,
            zoomControl: true,
        });
        
        // Initialize geocoder
        geocoder{{ $addressId }} = new google.maps.Geocoder();
        
        // Initialize search box
        const searchInput = document.getElementById('map-search-{{ $addressId }}');
        searchBox{{ $addressId }} = new google.maps.places.SearchBox(searchInput);
        
        // Bias search results to map viewport
        map{{ $addressId }}.addListener('bounds_changed', () => {
            searchBox{{ $addressId }}.setBounds(map{{ $addressId }}.getBounds());
        });
        
        // Add marker
        marker{{ $addressId }} = new google.maps.Marker({
            position: mapCenter,
            map: map{{ $addressId }},
            draggable: true,
            animation: google.maps.Animation.DROP,
            title: 'موقع المتجر'
        });
        
        // Update fields when marker is dragged
        marker{{ $addressId }}.addListener('dragend', function(event) {
            updateLocation{{ $addressId }}(event.latLng.lat(), event.latLng.lng());
        });
        
        // Add click listener to map
        map{{ $addressId }}.addListener('click', function(event) {
            placeMarker{{ $addressId }}(event.latLng);
        });
        
        // Listen for search box results
        searchBox{{ $addressId }}.addListener('places_changed', () => {
            const places = searchBox{{ $addressId }}.getPlaces();
            
            if (places.length == 0) {
                return;
            }
            
            const place = places[0];
            
            if (!place.geometry || !place.geometry.location) {
                console.log("Place has no geometry");
                return;
            }
            
            // Update marker position
            placeMarker{{ $addressId }}(place.geometry.location);
            
            // Center map on the place
            if (place.geometry.viewport) {
                map{{ $addressId }}.fitBounds(place.geometry.viewport);
            } else {
                map{{ $addressId }}.setCenter(place.geometry.location);
                map{{ $addressId }}.setZoom(17);
            }
            
            // Update address field
            document.getElementById('{{ $addressId }}').value = place.formatted_address || place.name;
        });
    }
    
    function placeMarker{{ $addressId }}(location) {
        marker{{ $addressId }}.setPosition(location);
        map{{ $addressId }}.panTo(location);
        updateLocation{{ $addressId }}(location.lat(), location.lng());
    }
    
    function updateLocation{{ $addressId }}(lat, lng) {
        // Update latitude and longitude fields
        document.getElementById('{{ $latitudeId }}').value = lat.toFixed(6);
        document.getElementById('{{ $longitudeId }}').value = lng.toFixed(6);
        
        // Reverse geocode to get address
        geocoder{{ $addressId }}.geocode({ location: { lat: lat, lng: lng } }, function(results, status) {
            if (status === 'OK' && results[0]) {
                document.getElementById('{{ $addressId }}').value = results[0].formatted_address;
            }
        });
    }
    
    function getCurrentLocation{{ $addressId }}() {
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(
                function(position) {
                    const pos = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };
                    
                    placeMarker{{ $addressId }}(new google.maps.LatLng(pos.lat, pos.lng));
                    map{{ $addressId }}.setCenter(pos);
                    map{{ $addressId }}.setZoom(15);
                },
                function() {
                    alert('خطأ: لا يمكن الحصول على موقعك الحالي');
                }
            );
        } else {
            alert('المتصفح لا يدعم تحديد الموقع الجغرافي');
        }
    }
    
    function searchLocation{{ $addressId }}() {
        const address = document.getElementById('map-search-{{ $addressId }}').value;
        
        if (!address) {
            alert('الرجاء إدخال عنوان للبحث');
            return;
        }
        
        geocoder{{ $addressId }}.geocode({ address: address }, function(results, status) {
            if (status === 'OK' && results[0]) {
                const location = results[0].geometry.location;
                placeMarker{{ $addressId }}(location);
                map{{ $addressId }}.setCenter(location);
                map{{ $addressId }}.setZoom(15);
                document.getElementById('{{ $addressId }}').value = results[0].formatted_address;
            } else {
                alert('لم يتم العثور على العنوان: ' + status);
            }
        });
    }
    
    function resetMap{{ $addressId }}() {
        const defaultPos = { lat: {{ $defaultLat }}, lng: {{ $defaultLng }} };
        map{{ $addressId }}.setCenter(defaultPos);
        map{{ $addressId }}.setZoom({{ $defaultZoom }});
        marker{{ $addressId }}.setPosition(defaultPos);
        document.getElementById('{{ $addressId }}').value = '';
        document.getElementById('{{ $latitudeId }}').value = '';
        document.getElementById('{{ $longitudeId }}').value = '';
        document.getElementById('map-search-{{ $addressId }}').value = '';
    }
    
    // Initialize map when page loads
    if (typeof google !== 'undefined') {
        google.maps.event.addDomListener(window, 'load', initMap{{ $addressId }});
    } else {
        document.addEventListener('DOMContentLoaded', function() {
            if (typeof google !== 'undefined') {
                initMap{{ $addressId }}();
            }
        });
    }
</script>
@endpush
