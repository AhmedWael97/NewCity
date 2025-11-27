@extends('layouts.admin')

@section('title', 'استيراد المتاجر من Google Maps')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-map-marked-alt"></i> استيراد المتاجر من Google Maps
        </h1>
        <a href="{{ route('admin.shops.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-right"></i> العودة للقائمة
        </a>
    </div>

    <!-- Instructions Card -->
    <div class="alert alert-info">
        <h5><i class="fas fa-info-circle"></i> كيفية الاستخدام:</h5>
        <ol class="mb-0">
            <li>انقر على زر "موقعي الحالي" للانتقال إلى موقعك</li>
            <li>ارسم دائرة على الخريطة لتحديد منطقة البحث</li>
            <li>سيتم عرض جميع المتاجر من Google Maps داخل الدائرة</li>
            <li>انقر على "إضافة" بجانب كل متجر لإضافته لقاعدة البيانات</li>
        </ol>
    </div>

    <!-- Settings Row -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">المدينة</h6>
                    <select id="citySelect" class="form-select">
                        <option value="">اختر المدينة</option>
                        @foreach($cities as $city)
                            <option value="{{ $city->id }}" data-lat="{{ $city->latitude }}" data-lng="{{ $city->longitude }}">
                                {{ $city->name }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">التصنيف</h6>
                    <select id="categorySelect" class="form-select">
                        <option value="">اختر التصنيف</option>
                        @foreach($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card">
                <div class="card-body">
                    <h6 class="card-title">صاحب المتجر الافتراضي</h6>
                    <select id="userSelect" class="form-select">
                        <option value="{{ auth('admin')->id() }}">المسؤول ({{ auth('admin')->user()->name }})</option>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <!-- Map Container -->
    <div class="card mb-4">
        <div class="card-header">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0">
                    <i class="fas fa-map"></i> خريطة البحث
                </h5>
                <div>
                    <button class="btn btn-sm btn-primary" onclick="getCurrentLocation()">
                        <i class="fas fa-crosshairs"></i> موقعي الحالي
                    </button>
                    <button class="btn btn-sm btn-success" onclick="startDrawing()">
                        <i class="fas fa-circle-notch"></i> رسم دائرة
                    </button>
                    <button class="btn btn-sm btn-warning" onclick="clearCircle()">
                        <i class="fas fa-times"></i> مسح الدائرة
                    </button>
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div id="map" style="height: 60vh; width: 100%;"></div>
        </div>
    </div>

    <!-- Results Card -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-store"></i> المتاجر المكتشفة (<span id="resultsCount">0</span>)
            </h5>
        </div>
        <div class="card-body">
            <div id="loadingResults" class="text-center py-4" style="display: none;">
                <div class="spinner-border text-primary" role="status">
                    <span class="visually-hidden">جاري البحث...</span>
                </div>
                <p class="mt-2">جاري البحث عن المتاجر...</p>
            </div>
            <div id="noResults" class="text-center py-4">
                <i class="fas fa-search fa-3x text-muted mb-3"></i>
                <p class="text-muted">ارسم دائرة على الخريطة للبحث عن المتاجر</p>
            </div>
            <div id="resultsTable" style="display: none;">
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th style="width: 30%">اسم المتجر</th>
                                <th style="width: 25%">العنوان</th>
                                <th style="width: 15%">التقييم</th>
                                <th style="width: 15%">النوع</th>
                                <th style="width: 15%">الإجراءات</th>
                            </tr>
                        </thead>
                        <tbody id="resultsBody">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCeaKlnTU88qhTp7za2H301HWPPT7zhGyo&libraries=places,drawing,geometry&language=ar"></script>

<script>
let map;
let drawingManager;
let circle = null;
let markers = [];
let placesService;
let currentPlaces = [];

function initMap() {
    map = new google.maps.Map(document.getElementById('map'), {
        center: { lat: 30.0444, lng: 31.2357 },
        zoom: 13
    });

    placesService = new google.maps.places.PlacesService(map);

    drawingManager = new google.maps.drawing.DrawingManager({
        drawingMode: null,
        drawingControl: false,
        circleOptions: {
            fillColor: '#4285F4',
            fillOpacity: 0.2,
            strokeColor: '#4285F4',
            strokeOpacity: 0.8,
            strokeWeight: 2,
            editable: true,
            draggable: true
        }
    });
    drawingManager.setMap(map);

    google.maps.event.addListener(drawingManager, 'circlecomplete', function(newCircle) {
        if (circle) circle.setMap(null);
        circle = newCircle;
        drawingManager.setDrawingMode(null);
        searchPlaces();

        google.maps.event.addListener(circle, 'radius_changed', () => {
            clearTimeout(window.searchTimeout);
            window.searchTimeout = setTimeout(searchPlaces, 500);
        });
        google.maps.event.addListener(circle, 'center_changed', () => {
            clearTimeout(window.searchTimeout);
            window.searchTimeout = setTimeout(searchPlaces, 500);
        });
    });
}

function getCurrentLocation() {
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const pos = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };
            map.setCenter(pos);
            map.setZoom(15);
            new google.maps.Marker({
                position: pos,
                map: map,
                title: 'موقعي',
                icon: 'http://maps.google.com/mapfiles/ms/icons/blue-dot.png'
            });
        }, function() {
            alert('تعذر تحديد موقعك');
        });
    } else {
        alert('المتصفح لا يدعم تحديد الموقع');
    }
}

function startDrawing() {
    if (!circle) {
        drawingManager.setDrawingMode(google.maps.drawing.OverlayType.CIRCLE);
    } else {
        alert('يوجد دائرة بالفعل. يرجى مسحها أولاً.');
    }
}

function clearCircle() {
    if (circle) {
        circle.setMap(null);
        circle = null;
        clearMarkers();
        clearResults();
    }
}

function clearMarkers() {
    markers.forEach(marker => marker.setMap(null));
    markers = [];
}

function clearResults() {
    document.getElementById('resultsBody').innerHTML = '';
    document.getElementById('resultsCount').textContent = '0';
    document.getElementById('noResults').style.display = 'block';
    document.getElementById('resultsTable').style.display = 'none';
    currentPlaces = [];
}

function searchPlaces() {
    if (!circle) return;

    clearMarkers();
    clearResults();
    document.getElementById('loadingResults').style.display = 'block';
    document.getElementById('noResults').style.display = 'none';

    const center = circle.getCenter();
    const radius = circle.getRadius();

    console.log('Searching with center:', center.lat(), center.lng(), 'radius:', radius);

    const request = {
        location: center,
        radius: Math.min(radius, 50000), // Max 50km
        type: ['store', 'restaurant', 'cafe', 'shop', 'pharmacy', 'supermarket', 'bakery', 'clothing_store']
    };

    placesService.nearbySearch(request, function(results, status, pagination) {
        document.getElementById('loadingResults').style.display = 'none';

        console.log('Places API Status:', status);
        console.log('Results:', results);

        if (status === google.maps.places.PlacesServiceStatus.OK && results && results.length > 0) {
            currentPlaces = results;
            displayResults(results);
            addMarkers(results);
        } else if (status === google.maps.places.PlacesServiceStatus.ZERO_RESULTS) {
            document.getElementById('noResults').innerHTML = '<i class="fas fa-store-slash fa-3x text-muted mb-3"></i><p class="text-muted">لم يتم العثور على متاجر في هذه المنطقة</p>';
            document.getElementById('noResults').style.display = 'block';
        } else if (status === google.maps.places.PlacesServiceStatus.REQUEST_DENIED) {
            document.getElementById('noResults').innerHTML = '<i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i><p class="text-danger"><strong>خطأ في الوصول للـ API</strong></p><p>يرجى التأكد من:<br>1. تفعيل Places API في Google Cloud Console<br>2. إضافة بيانات الدفع (Billing)<br>3. صلاحية API Key</p>';
            document.getElementById('noResults').style.display = 'block';
            console.error('Places API Request Denied. Check:', 
                '\n1. Places API is enabled', 
                '\n2. Billing is set up', 
                '\n3. API Key is valid');
        } else if (status === google.maps.places.PlacesServiceStatus.OVER_QUERY_LIMIT) {
            document.getElementById('noResults').innerHTML = '<i class="fas fa-exclamation-triangle fa-3x text-warning mb-3"></i><p class="text-warning">تم تجاوز حد الاستعلامات. يرجى المحاولة لاحقاً</p>';
            document.getElementById('noResults').style.display = 'block';
        } else {
            document.getElementById('noResults').innerHTML = `<i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i><p class="text-danger">خطأ: ${status}</p><p class="small">تحقق من Console للمزيد من التفاصيل</p>`;
            document.getElementById('noResults').style.display = 'block';
            console.error('Places API Error:', status);
        }
    });
}

function displayResults(places) {
    const tbody = document.getElementById('resultsBody');
    tbody.innerHTML = '';

    document.getElementById('resultsCount').textContent = places.length;
    document.getElementById('resultsTable').style.display = 'block';

    places.forEach((place, index) => {
        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <strong>${place.name}</strong><br>
                <small class="text-muted">${place.place_id}</small>
            </td>
            <td>${place.vicinity || 'لا يوجد عنوان'}</td>
            <td>
                ${place.rating ? `
                    <span class="badge bg-warning text-dark">
                        <i class="fas fa-star"></i> ${place.rating}
                    </span>
                    <small class="text-muted">(${place.user_ratings_total || 0})</small>
                ` : '<span class="text-muted">لا يوجد تقييم</span>'}
            </td>
            <td>
                <small>${place.types ? place.types[0].replace(/_/g, ' ') : 'متجر'}</small>
            </td>
            <td>
                <button class="btn btn-sm btn-success" onclick="importPlace(${index})" id="importBtn${index}">
                    <i class="fas fa-plus"></i> إضافة
                </button>
                <button class="btn btn-sm btn-info" onclick="showPlaceDetails(${index})">
                    <i class="fas fa-info-circle"></i>
                </button>
            </td>
        `;
        tbody.appendChild(row);
    });
}

function addMarkers(places) {
    clearMarkers();

    places.forEach((place, index) => {
        const marker = new google.maps.Marker({
            position: place.geometry.location,
            map: map,
            title: place.name,
            icon: 'http://maps.google.com/mapfiles/ms/icons/red-dot.png'
        });

        marker.addListener('click', () => {
            showPlaceDetails(index);
        });

        markers.push(marker);
    });
}

function showPlaceDetails(index) {
    const place = currentPlaces[index];
    const infoWindow = new google.maps.InfoWindow({
        content: `
            <div style="max-width: 250px;">
                <h6><strong>${place.name}</strong></h6>
                <p>${place.vicinity || 'لا يوجد عنوان'}</p>
                ${place.rating ? `<p>⭐ ${place.rating} (${place.user_ratings_total} تقييم)</p>` : ''}
                <button class="btn btn-sm btn-success" onclick="importPlace(${index})">
                    <i class="fas fa-plus"></i> إضافة للنظام
                </button>
            </div>
        `
    });
    infoWindow.open(map, markers[index]);
}

function importPlace(index) {
    const place = currentPlaces[index];
    const cityId = document.getElementById('citySelect').value;
    const categoryId = document.getElementById('categorySelect').value;
    const userId = document.getElementById('userSelect').value;

    if (!cityId) {
        alert('يرجى اختيار المدينة أولاً');
        return;
    }
    if (!categoryId) {
        alert('يرجى اختيار التصنيف أولاً');
        return;
    }

    const button = document.getElementById('importBtn' + index);
    button.disabled = true;
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري الإضافة...';

    fetch('{{ route("admin.shops.import-google") }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            place_id: place.place_id,
            name: place.name,
            address: place.vicinity,
            latitude: place.geometry.location.lat(),
            longitude: place.geometry.location.lng(),
            rating: place.rating,
            review_count: place.user_ratings_total,
            city_id: cityId,
            category_id: categoryId,
            user_id: userId,
            google_types: place.types
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            button.innerHTML = '<i class="fas fa-check"></i> تمت الإضافة';
            button.classList.remove('btn-success');
            button.classList.add('btn-secondary');
            markers[index].setIcon('http://maps.google.com/mapfiles/ms/icons/green-dot.png');
            
            showNotification('success', data.message);
        } else {
            button.disabled = false;
            button.innerHTML = '<i class="fas fa-plus"></i> إضافة';
            showNotification('warning', data.message);
        }
    })
    .catch(error => {
        button.disabled = false;
        button.innerHTML = '<i class="fas fa-plus"></i> إضافة';
        showNotification('danger', 'حدث خطأ أثناء الإضافة');
        console.error('Error:', error);
    });
}

function showNotification(type, message) {
    const alert = document.createElement('div');
    alert.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    alert.style.cssText = 'top: 20px; left: 20px; z-index: 9999; max-width: 400px;';
    alert.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    document.body.appendChild(alert);
    setTimeout(() => alert.remove(), 5000);
}

document.getElementById('citySelect').addEventListener('change', function() {
    const option = this.options[this.selectedIndex];
    if (option.dataset.lat && option.dataset.lng) {
        map.setCenter({
            lat: parseFloat(option.dataset.lat),
            lng: parseFloat(option.dataset.lng)
        });
        map.setZoom(13);
    }
});

document.addEventListener('DOMContentLoaded', initMap);
</script>

<style>
#map {
    border-radius: 0 0 0.375rem 0.375rem;
}
</style>
@endsection
