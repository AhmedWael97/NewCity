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

    <!-- Debug Console -->
    <div class="card mb-4">
        <div class="card-header bg-dark text-white">
            <h6 class="mb-0">
                <i class="fas fa-bug"></i> سجل التشخيص (Debug Console)
            </h6>
        </div>
        <div class="card-body bg-dark text-light" style="max-height: 200px; overflow-y: auto;">
            <pre id="debugConsole" style="color: #00ff00; font-family: monospace; font-size: 12px; margin: 0;"></pre>
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

<script>
let map = null;
let drawingManager = null;
let circle = null;
let markers = [];
let placesService = null;
let currentPlaces = [];
let sharedInfoWindow = null;

// Debug logging function
function debugLog(message, type = 'info') {
    const console_elem = document.getElementById('debugConsole');
    const timestamp = new Date().toLocaleTimeString();
    const colors = {
        'info': '#00ff00',
        'error': '#ff0000',
        'warning': '#ffaa00',
        'success': '#00ffff'
    };
    const color = colors[type] || colors.info;
    console_elem.innerHTML += `<span style="color: ${color}">[${timestamp}] ${message}</span>\n`;
    console_elem.scrollTop = console_elem.scrollHeight;
    console.log(`[${type.toUpperCase()}]`, message);
}

function initMap() {
    debugLog('🚀 Initializing map...');
    try {
        debugLog('📍 Creating map instance...');
        map = new google.maps.Map(document.getElementById('map'), {
            center: { lat: 24.7136, lng: 46.6753 }, // Riyadh, Saudi Arabia (default)
            zoom: 12,
            mapTypeControl: true,
            streetViewControl: false,
            fullscreenControl: true
        });
        debugLog('✅ Map created successfully', 'success');
        
        // Auto-detect user location and search nearby places
        autoDetectAndSearch();

        debugLog('🔧 Initializing Places Service...');
        placesService = new google.maps.places.PlacesService(map);
        sharedInfoWindow = new google.maps.InfoWindow();
        debugLog('✅ Places Service initialized', 'success');

        debugLog('✏️ Setting up Drawing Manager...');
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
        debugLog('✅ Drawing Manager ready', 'success');

        google.maps.event.addListener(drawingManager, 'circlecomplete', function(newCircle) {
            debugLog('🔵 Circle drawn', 'info');
            if (circle) {
                debugLog('🗑️ Removing old circle', 'warning');
                circle.setMap(null);
            }
            circle = newCircle;
            drawingManager.setDrawingMode(null);
            
            // Validate circle size
            const radius = circle.getRadius();
            debugLog(`📏 Circle radius: ${Math.round(radius)}m`, 'info');
            if (radius > 5000) {
                debugLog('⚠️ Circle too large, will search within 5km only', 'warning');
                showNotification('warning', 'الدائرة كبيرة جداً. سيتم البحث ضمن نطاق 5 كم فقط.');
            }
            
            searchPlaces();

            google.maps.event.addListener(circle, 'radius_changed', () => {
                clearTimeout(window.searchTimeout);
                window.searchTimeout = setTimeout(searchPlaces, 1000);
            });
            google.maps.event.addListener(circle, 'center_changed', () => {
                clearTimeout(window.searchTimeout);
                window.searchTimeout = setTimeout(searchPlaces, 1000);
            });
        });

        debugLog('✅ Map initialized successfully', 'success');
    } catch (error) {
        debugLog('❌ ERROR: ' + error.message, 'error');
        console.error('Error initializing map:', error);
        document.getElementById('map').innerHTML = '<div class="alert alert-danger m-3">خطأ في تحميل الخريطة. يرجى التأكد من صلاحية API Key</div>';
    }
}

// Expose initMap to global scope for Google Maps callback
window.initMap = initMap;

function autoDetectAndSearch() {
    debugLog('🌍 Auto-detecting user location...', 'info');
    
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const pos = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };
            
            debugLog(`✅ Location detected: [${pos.lat.toFixed(4)}, ${pos.lng.toFixed(4)}]`, 'success');
            map.setCenter(pos);
            map.setZoom(15);
            
            // Add user location marker
            new google.maps.Marker({
                position: pos,
                map: map,
                title: 'موقعي الحالي',
                icon: 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png',
                zIndex: 9999
            });
            
            // Automatically draw 2km circle and search
            debugLog('🔵 Creating 2km search radius...', 'info');
            circle = new google.maps.Circle({
                map: map,
                center: pos,
                radius: 2000, // 2km
                fillColor: '#4285F4',
                fillOpacity: 0.2,
                strokeColor: '#4285F4',
                strokeOpacity: 0.8,
                strokeWeight: 2,
                editable: true,
                draggable: true
            });
            
            debugLog('📏 Circle radius: 2000m (2km)', 'info');
            
            // Add listeners for circle changes
            google.maps.event.addListener(circle, 'radius_changed', () => {
                clearTimeout(window.searchTimeout);
                window.searchTimeout = setTimeout(searchPlaces, 1000);
            });
            google.maps.event.addListener(circle, 'center_changed', () => {
                clearTimeout(window.searchTimeout);
                window.searchTimeout = setTimeout(searchPlaces, 1000);
            });
            
            // Start searching automatically
            searchPlaces();
            
        }, function(error) {
            debugLog(`⚠️ Location detection failed: ${error.message}`, 'warning');
            showNotification('warning', 'تعذر تحديد موقعك. يمكنك رسم دائرة يدوياً للبحث.');
            console.error('Geolocation error:', error);
        }, {
            enableHighAccuracy: true,
            timeout: 10000,
            maximumAge: 0
        });
    } else {
        debugLog('❌ Geolocation not supported', 'error');
        showNotification('warning', 'المتصفح لا يدعم تحديد الموقع. يمكنك رسم دائرة يدوياً للبحث.');
    }
}

function getCurrentLocation() {
    debugLog('🌍 Manual location detection requested...', 'info');
    
    if (navigator.geolocation) {
        navigator.geolocation.getCurrentPosition(function(position) {
            const pos = {
                lat: position.coords.latitude,
                lng: position.coords.longitude
            };
            map.setCenter(pos);
            map.setZoom(15);
            
            // Clear existing circle if any
            if (circle) {
                circle.setMap(null);
            }
            
            new google.maps.Marker({
                position: pos,
                map: map,
                title: 'موقعي',
                icon: 'https://maps.google.com/mapfiles/ms/icons/blue-dot.png'
            });
            
            debugLog(`✅ Centered on location: [${pos.lat.toFixed(4)}, ${pos.lng.toFixed(4)}]`, 'success');
            showNotification('success', 'تم تحديد موقعك بنجاح');
        }, function() {
            showNotification('danger', 'تعذر تحديد موقعك');
        });
    } else {
        showNotification('danger', 'المتصفح لا يدعم تحديد الموقع');
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

async function searchPlaces() {
    if (!circle) {
        debugLog('⚠️ No circle drawn yet', 'warning');
        return;
    }

    clearMarkers();
    clearResults();
    document.getElementById('loadingResults').style.display = 'block';
    document.getElementById('noResults').style.display = 'none';

    const center = circle.getCenter();
    const radius = circle.getRadius();

    debugLog(`🔍 Starting search at [${center.lat().toFixed(4)}, ${center.lng().toFixed(4)}] with radius ${Math.round(radius)}m`, 'info');

    const types = ['store'];
    let allResults = [];

    debugLog(`📡 Searching for shops only...`, 'info');

    try {
        debugLog(`📤 Searching for "store" type`, 'info');
        
        const results = await searchByType(center, Math.min(radius, 5000), 'store');
        
        if (results && results.length > 0) {
            allResults = results;
            debugLog(`  ✓ Found ${results.length} shops`, 'success');
        }
    } catch (error) {
        debugLog(`  ❌ Error searching for shops: ${error.message}`, 'error');
    }

    debugLog(`✅ All requests completed. Total unique places: ${allResults.length}`, 'success');
    document.getElementById('loadingResults').style.display = 'none';
    
    if (allResults.length > 0) {
        currentPlaces = allResults;
        debugLog(`📊 Displaying ${allResults.length} results...`, 'info');
        displayResults(allResults);
        addMarkers(allResults);
        debugLog(`✅ Results displayed successfully`, 'success');
    } else {
        debugLog('⚠️ No places found in this area', 'warning');
        document.getElementById('noResults').innerHTML = '<i class="fas fa-store-slash fa-3x text-muted mb-3"></i><p class="text-muted">لم يتم العثور على متاجر في هذه المنطقة<br><small>حاول توسيع الدائرة أو اختيار منطقة أخرى</small></p>';
        document.getElementById('noResults').style.display = 'block';
    }
}

// Helper function to search by type with Promise
function searchByType(location, radius, type) {
    return new Promise((resolve, reject) => {
        const request = {
            location: location,
            radius: radius,
            type: [type]
        };

        try {
            placesService.nearbySearch(request, function(results, status, pagination) {
                debugLog(`  📥 Raw response for "${type}": status=${status}, results=${results ? results.length : 'null'}`, 'info');
                
                if (status === google.maps.places.PlacesServiceStatus.OK) {
                    debugLog(`  ✅ "${type}": ${results.length} results`, 'success');
                    if (results && results.length > 0) {
                        debugLog(`  📝 First result: ${results[0].name}`, 'info');
                    }
                    resolve(results || []);
                } else if (status === google.maps.places.PlacesServiceStatus.ZERO_RESULTS) {
                    debugLog(`  ℹ️ "${type}": No results`, 'warning');
                    resolve([]);
                } else if (status === google.maps.places.PlacesServiceStatus.REQUEST_DENIED) {
                    debugLog(`  ❌ REQUEST DENIED for "${type}"`, 'error');
                    debugLog('  📋 SOLUTION: Go to Google Cloud Console and:', 'error');
                    debugLog('  1️⃣ Enable Places API (New)', 'error');
                    debugLog('  2️⃣ Set up Billing (REQUIRED)', 'error');
                    debugLog('  3️⃣ Wait 5 minutes after setup', 'error');
                    document.getElementById('loadingResults').style.display = 'none';
                    document.getElementById('noResults').innerHTML = `
                        <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
                        <h5 class="text-danger">Google Places API غير مفعل</h5>
                        <div class="alert alert-danger text-start">
                            <h6>⚠️ يجب عليك:</h6>
                            <ol>
                                <li><strong>تفعيل Places API (New)</strong><br>
                                    <a href="https://console.cloud.google.com/apis/library/places-backend.googleapis.com" target="_blank" class="btn btn-sm btn-danger mt-1">
                                        <i class="fas fa-external-link-alt"></i> تفعيل الآن
                                    </a>
                                </li>
                                <li><strong>إضافة بيانات الدفع (Billing)</strong> - مطلوب حتى للباقة المجانية<br>
                                    <a href="https://console.cloud.google.com/billing" target="_blank" class="btn btn-sm btn-danger mt-1">
                                        <i class="fas fa-credit-card"></i> إضافة الآن
                                    </a>
                                </li>
                                <li><strong>انتظر 5-10 دقائق</strong> ثم أعد تحميل الصفحة</li>
                            </ol>
                            <p class="mb-0 small"><strong>ملاحظة:</strong> ستحصل على رصيد $200 مجاناً شهرياً</p>
                        </div>
                    `;
                    document.getElementById('noResults').style.display = 'block';
                    reject(new Error('REQUEST_DENIED - Places API not enabled or Billing not set up'));
                } else if (status === google.maps.places.PlacesServiceStatus.OVER_QUERY_LIMIT) {
                    debugLog(`  ⚠️ QUERY LIMIT for "${type}" - retrying...`, 'warning');
                    setTimeout(() => {
                        placesService.nearbySearch(request, (r, s) => {
                            if (s === 'OK') resolve(r || []);
                            else resolve([]);
                        });
                    }, 1000);
                } else {
                    debugLog(`  ❓ Unknown status for "${type}": ${status}`, 'error');
                    resolve([]);
                }
            });
        } catch (error) {
            debugLog(`  ❌ Exception caught for "${type}": ${error.message}`, 'error');
            reject(error);
        }
    });
}

function displayResults(places) {
    debugLog(`📋 displayResults called with ${places.length} places`, 'info');
    const tbody = document.getElementById('resultsBody');
    
    if (!tbody) {
        debugLog('❌ resultsBody element not found!', 'error');
        return;
    }
    
    tbody.innerHTML = '';

    document.getElementById('resultsCount').textContent = places.length;
    document.getElementById('resultsTable').style.display = 'block';

    let displayedCount = 0;
    places.forEach((place, index) => {
        // Validate required data
        if (!place.name || !place.geometry) {
            debugLog(`  ⚠️ Skipping place with missing data at index ${index}`, 'warning');
            return;
        }
        displayedCount++;

        const address = place.formatted_address || place.vicinity || 'لا يوجد عنوان';
        const rating = place.rating || null;
        const reviewCount = place.user_ratings_total || 0;
        const types = place.types || [];
        const primaryType = types.length > 0 ? types[0].replace(/_/g, ' ') : 'متجر';

        const row = document.createElement('tr');
        row.innerHTML = `
            <td>
                <strong>${place.name}</strong><br>
                <small class="text-muted">${place.place_id}</small>
            </td>
            <td><small>${address}</small></td>
            <td>
                ${rating ? `
                    <span class="badge bg-warning text-dark">
                        <i class="fas fa-star"></i> ${rating}
                    </span>
                    <small class="text-muted">(${reviewCount})</small>
                ` : '<span class="text-muted">لا يوجد تقييم</span>'}
            </td>
            <td>
                <small>${primaryType}</small>
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
    
    debugLog(`✅ Displayed ${displayedCount} places in table`, 'success');
}

function addMarkers(places) {
    clearMarkers();

    places.forEach((place, index) => {
        if (!place.geometry || !place.geometry.location) return;

        const marker = new google.maps.Marker({
            position: place.geometry.location,
            map: map,
            title: place.name,
            icon: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png'
        });

        marker.addListener('click', () => {
            showPlaceDetails(index);
        });

        markers.push(marker);
    });
}

function showPlaceDetails(index) {
    const place = currentPlaces[index];
    const address = place.formatted_address || place.vicinity || 'لا يوجد عنوان';
    const rating = place.rating || null;
    const reviewCount = place.user_ratings_total || 0;

    sharedInfoWindow.setContent(`
        <div style="max-width: 250px;">
            <h6><strong>${place.name}</strong></h6>
            <p>${address}</p>
            ${rating ? `<p>⭐ ${rating} (${reviewCount} تقييم)</p>` : ''}
            <button class="btn btn-sm btn-success" onclick="importPlace(${index})">
                <i class="fas fa-plus"></i> إضافة للنظام
            </button>
        </div>
    `);
    sharedInfoWindow.open(map, markers[index]);
}

function importPlace(index) {
    const place = currentPlaces[index];
    const cityId = document.getElementById('citySelect').value;
    const categoryId = document.getElementById('categorySelect').value;
    const userId = document.getElementById('userSelect').value;

    if (!cityId) {
        showNotification('warning', 'يرجى اختيار المدينة أولاً');
        return;
    }
    if (!categoryId) {
        showNotification('warning', 'يرجى اختيار التصنيف أولاً');
        return;
    }
    if (!userId) {
        showNotification('warning', 'يرجى تحديد صاحب المتجر');
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
            address: place.formatted_address || place.vicinity || '',
            latitude: place.geometry.location.lat(),
            longitude: place.geometry.location.lng(),
            rating: place.rating || null,
            review_count: place.user_ratings_total || 0,
            city_id: cityId,
            category_id: categoryId,
            user_id: userId,
            google_types: place.types || []
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            button.innerHTML = '<i class="fas fa-check"></i> تمت الإضافة';
            button.classList.remove('btn-success');
            button.classList.add('btn-secondary');
            if (markers[index]) {
                markers[index].setIcon('https://maps.google.com/mapfiles/ms/icons/green-dot.png');
            }
            
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

// Handle Google Maps errors
window.gm_authFailure = function() {
    debugLog('❌ Google Maps authentication failed', 'error');
    console.error('Google Maps authentication failed');
    document.getElementById('map').innerHTML = `
        <div class="alert alert-danger m-3">
            <h5><i class="fas fa-exclamation-triangle"></i> خطأ في Google Maps API</h5>
            <p>فشل التحقق من API Key. يرجى التأكد من:</p>
            <ul>
                <li>تفعيل Maps JavaScript API</li>
                <li>تفعيل Places API</li>
                <li>إضافة بيانات الدفع (Billing)</li>
                <li>صلاحية API Key</li>
            </ul>
        </div>
    `;
};

// Catch unhandled promise rejections from Google Maps
window.addEventListener('unhandledrejection', function(event) {
    debugLog(`❌ Unhandled Promise Rejection: ${event.reason}`, 'error');
    console.error('Unhandled rejection:', event.reason);
    
    // Hide loading spinner
    document.getElementById('loadingResults').style.display = 'none';
    
    // Show detailed error message
    document.getElementById('noResults').innerHTML = `
        <i class="fas fa-exclamation-triangle fa-3x text-danger mb-3"></i>
        <h5 class="text-danger">❌ Google Places API Error</h5>
        <div class="alert alert-danger text-start">
            <p><strong>Error:</strong> ${event.reason}</p>
            <hr>
            <h6>🔧 كيفية الحل:</h6>
            <ol>
                <li><strong>افتح Google Cloud Console:</strong><br>
                    <a href="https://console.cloud.google.com/apis/library/places-backend.googleapis.com?project=_" target="_blank" class="btn btn-sm btn-primary mt-1">
                        <i class="fas fa-cloud"></i> فتح Console
                    </a>
                </li>
                <li><strong>فعّل Places API (New):</strong><br>
                    اضغط على زر "Enable" في الصفحة
                </li>
                <li><strong>أضف Billing Account:</strong><br>
                    <a href="https://console.cloud.google.com/billing/linkedaccount?project=_" target="_blank" class="btn btn-sm btn-success mt-1">
                        <i class="fas fa-credit-card"></i> إضافة Billing
                    </a><br>
                    <small class="text-muted">مطلوب حتى للباقة المجانية - تحصل على $200 شهرياً مجاناً</small>
                </li>
                <li><strong>انتظر 5 دقائق</strong> ثم أعد تحميل الصفحة</li>
            </ol>
        </div>
        <button class="btn btn-primary" onclick="location.reload()">
            <i class="fas fa-sync"></i> إعادة تحميل الصفحة
        </button>
    `;
    document.getElementById('noResults').style.display = 'block';
    
    debugLog('🔍 This is a Google Maps Places API configuration error', 'error');
    debugLog('💡 ACTION REQUIRED:', 'warning');
    debugLog('   1️⃣ Enable Places API: https://console.cloud.google.com/apis/library/places-backend.googleapis.com', 'warning');
    debugLog('   2️⃣ Set up Billing: https://console.cloud.google.com/billing', 'warning');
    debugLog('   3️⃣ Wait 5-10 minutes for propagation', 'warning');
    debugLog('   4️⃣ Reload this page', 'warning');
    
    // Prevent default error handling
    event.preventDefault();
});
</script>

<!-- Load Google Maps API at the end so initMap is already defined -->
<script async defer src="https://maps.googleapis.com/maps/api/js?key=AIzaSyCeaKlnTU88qhTp7za2H301HWPPT7zhGyo&libraries=places,drawing,geometry&language=ar&callback=initMap"></script>

<style>
#map {
    border-radius: 0 0 0.375rem 0.375rem;
}
</style>
@endsection
