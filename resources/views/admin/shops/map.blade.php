@extends('layouts.admin')

@section('title', 'استيراد المتاجر من Google Maps')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="h3 mb-0">
            <i class="fas fa-map-marked-alt"></i> استيراد المتاجر من Google Maps
        </h1>
        <div>
            <a href="{{ route('admin.shops.map.test') }}" class="btn btn-warning" target="_blank">
                <i class="fas fa-flask"></i> اختبار تشخيصي
            </a>
            <a href="{{ route('admin.shops.index') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-right"></i> العودة للقائمة
            </a>
        </div>
    </div>

    <!-- Instructions Card -->
    <div class="alert alert-info">
        <h5><i class="fas fa-info-circle"></i> كيفية الاستخدام:</h5>
        <ol class="mb-0">
            <li>انقر على زر "موقعي الحالي" للانتقال إلى موقعك</li>
            <li>انقر على "رسم منطقة" ثم ارسم شكلاً على الخريطة لتحديد منطقة البحث</li>
            <li>سيتم عرض جميع المتاجر من Google Maps داخل المنطقة المحددة</li>
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
                        <i class="fas fa-draw-polygon"></i> رسم منطقة
                    </button>
                    <button class="btn btn-sm btn-warning" onclick="clearPolygon()">
                        <i class="fas fa-times"></i> مسح المنطقة
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
                <p class="text-muted">ارسم منطقة على الخريطة للبحث عن المتاجر</p>
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
let polygon = null;
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
            polygonOptions: {
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

        google.maps.event.addListener(drawingManager, 'polygoncomplete', function(newPolygon) {
            debugLog('🔷 Polygon drawn', 'info');
            if (polygon) {
                debugLog('🗑️ Removing old polygon', 'warning');
                polygon.setMap(null);
            }
            polygon = newPolygon;
            drawingManager.setDrawingMode(null);
            
            // Calculate polygon area
            const area = google.maps.geometry.spherical.computeArea(polygon.getPath());
            const areaKm2 = (area / 1000000).toFixed(2);
            debugLog(`📏 Polygon area: ${areaKm2} km²`, 'info');
            
            searchPlaces();

            // Add listeners for polygon changes
            google.maps.event.addListener(polygon.getPath(), 'set_at', () => {
                clearTimeout(window.searchTimeout);
                window.searchTimeout = setTimeout(searchPlaces, 1000);
            });
            google.maps.event.addListener(polygon.getPath(), 'insert_at', () => {
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
            
            // Automatically draw 2km square polygon and search
            debugLog('🔷 Creating 2km search area...', 'info');
            const distance = 0.018; // Approximately 2km in degrees
            const polygonCoords = [
                {lat: pos.lat + distance, lng: pos.lng - distance},
                {lat: pos.lat + distance, lng: pos.lng + distance},
                {lat: pos.lat - distance, lng: pos.lng + distance},
                {lat: pos.lat - distance, lng: pos.lng - distance}
            ];
            
            polygon = new google.maps.Polygon({
                map: map,
                paths: polygonCoords,
                fillColor: '#4285F4',
                fillOpacity: 0.2,
                strokeColor: '#4285F4',
                strokeOpacity: 0.8,
                strokeWeight: 2,
                editable: true,
                draggable: true
            });
            
            const area = google.maps.geometry.spherical.computeArea(polygon.getPath());
            const areaKm2 = (area / 1000000).toFixed(2);
            debugLog(`📏 Polygon area: ${areaKm2} km²`, 'info');
            
            // Add listeners for polygon changes
            google.maps.event.addListener(polygon.getPath(), 'set_at', () => {
                clearTimeout(window.searchTimeout);
                window.searchTimeout = setTimeout(searchPlaces, 1000);
            });
            google.maps.event.addListener(polygon.getPath(), 'insert_at', () => {
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
            
            // Clear existing polygon if any
            if (polygon) {
                polygon.setMap(null);
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
    if (!polygon) {
        drawingManager.setDrawingMode(google.maps.drawing.OverlayType.POLYGON);
        debugLog('✏️ Draw a polygon on the map. Click points to create shape, double-click to finish.', 'info');
        showNotification('info', 'انقر على الخريطة لرسم المنطقة. انقر مرتين للإنهاء.');
    } else {
        alert('يوجد منطقة مرسومة بالفعل. يرجى مسحها أولاً.');
    }
}

function clearPolygon() {
    if (polygon) {
        polygon.setMap(null);
        polygon = null;
        clearMarkers();
        clearResults();
        debugLog('🗑️ Polygon cleared', 'info');
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
    if (!polygon) {
        debugLog('⚠️ No polygon drawn yet', 'warning');
        return;
    }

    clearMarkers();
    clearResults();
    document.getElementById('loadingResults').style.display = 'block';
    document.getElementById('noResults').style.display = 'none';

    const startTime = Date.now();

    // Get polygon bounds
    const bounds = new google.maps.LatLngBounds();
    polygon.getPath().forEach(function(latLng) {
        bounds.extend(latLng);
    });
    
    const center = bounds.getCenter();
    
    // Calculate search radius based on polygon bounds
    const ne = bounds.getNorthEast();
    const sw = bounds.getSouthWest();
    const radius = google.maps.geometry.spherical.computeDistanceBetween(center, ne);
    
    debugLog(`🔍 Starting search in polygon area (center: [${center.lat().toFixed(4)}, ${center.lng().toFixed(4)}], radius: ${Math.round(radius)}m)`, 'info');

    let allResults = [];

    debugLog(`📡 Searching for shops within polygon...`, 'info');

    try {
        // Search within the calculated radius
        const searchRadius = Math.min(radius, 5000); // Limit to 5km for performance
        debugLog(`📤 Searching within ${Math.round(searchRadius)}m radius`, 'info');
        
        // Try multiple place types to find more results
        const typesToSearch = ['store', 'establishment', 'point_of_interest'];
        let foundResults = false;
        
        for (const type of typesToSearch) {
            try {
                debugLog(`🔎 Trying type: "${type}"`, 'info');
                const results = await searchByType(center, searchRadius, type);
                
                if (results && results.length > 0) {
                    // Filter results to only include places inside the polygon
                    const filteredResults = results.filter(place => {
                        if (!place.geometry || !place.geometry.location) return false;
                        return google.maps.geometry.poly.containsLocation(place.geometry.location, polygon);
                    });
                    
                    if (filteredResults.length > 0) {
                        allResults = filteredResults;
                        const elapsed = Date.now() - startTime;
                        debugLog(`  ✓ Found ${results.length} places, ${filteredResults.length} inside polygon using "${type}" in ${elapsed}ms`, 'success');
                        foundResults = true;
                        break; // Stop if we found results
                    }
                }
            } catch (typeError) {
                debugLog(`  ⚠️ Error with type "${type}": ${typeError.message}`, 'warning');
                // Continue to next type
            }
        }
        
        if (!foundResults) {
            debugLog(`  ℹ️ No results found with any search type`, 'info');
        }
    } catch (error) {
        debugLog(`  ❌ Error searching for shops: ${error.message}`, 'error');
        // Don't throw, just log the error
    }

    const totalTime = Date.now() - startTime;
    debugLog(`✅ Search completed in ${totalTime}ms. Total places in polygon: ${allResults.length}`, 'success');
    document.getElementById('loadingResults').style.display = 'none';
    
    if (allResults.length > 0) {
        currentPlaces = allResults;
        debugLog(`📊 Displaying ${allResults.length} results...`, 'info');
        displayResults(allResults);
        addMarkers(allResults);
        debugLog(`✅ Results displayed successfully`, 'success');
    } else {
        debugLog('⚠️ No places found in this area', 'warning');
        document.getElementById('noResults').innerHTML = `
            <i class="fas fa-store-slash fa-3x text-muted mb-3"></i>
            <p class="text-muted">لم يتم العثور على متاجر في هذه المنطقة</p>
            <small class="text-muted">جرب:</small>
            <ul class="list-unstyled small text-muted">
                <li>• رسم منطقة أكبر</li>
                <li>• اختيار منطقة تجارية مختلفة</li>
                <li>• التأكد من الاتصال بالإنترنت</li>
            </ul>
            <button class="btn btn-sm btn-primary mt-2" onclick="searchPlaces()">
                <i class="fas fa-sync"></i> إعادة المحاولة
            </button>
        `;
        document.getElementById('noResults').style.display = 'block';
    }
}

// Helper function to search by type with Promise
function searchByType(location, radius, type) {
    return new Promise((resolve, reject) => {
        const request = {
            location: location,
            radius: radius,
            type: [type],
            // Optimize request for speed
            rankBy: google.maps.places.RankBy.PROMINENCE // Get best results first
        };

        // Set timeout to prevent hanging
        let hasResponded = false;
        const timeoutId = setTimeout(() => {
            if (!hasResponded) {
                hasResponded = true;
                debugLog(`  ⏱️ Timeout for "${type}" - trying next option`, 'warning');
                resolve([]); // Resolve with empty array instead of rejecting
            }
        }, 15000); // 15 second timeout (increased)

        try {
            placesService.nearbySearch(request, function(results, status, pagination) {
                if (hasResponded) return; // Ignore if already timed out
                hasResponded = true;
                clearTimeout(timeoutId);
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
                    
                    // Only show error UI on first failure
                    if (type === 'store') {
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
                    }
                    resolve([]); // Resolve with empty array instead of rejecting
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
            if (hasResponded) return; // Already handled
            hasResponded = true;
            clearTimeout(timeoutId);
            debugLog(`  ❌ Exception caught for "${type}": ${error.message}`, 'error');
            resolve([]); // Resolve with empty array to prevent unhandled promise rejection
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

    // Limit display to first 20 for performance
    const displayLimit = 20;
    const placesToDisplay = places.slice(0, displayLimit);
    
    if (places.length > displayLimit) {
        debugLog(`⚡ Displaying first ${displayLimit} of ${places.length} results for speed`, 'info');
    }

    let displayedCount = 0;
    placesToDisplay.forEach((place, index) => {
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
    
    // Add row showing there are more results
    if (places.length > displayLimit) {
        const moreRow = document.createElement('tr');
        moreRow.innerHTML = `
            <td colspan="5" class="text-center text-muted">
                <i class="fas fa-info-circle"></i> 
                عرض أول ${displayLimit} نتيجة من ${places.length} (لتحسين الأداء)
                <br><small>جميع المتاجر متوفرة على الخريطة</small>
            </td>
        `;
        tbody.appendChild(moreRow);
    }
    
    debugLog(`✅ Displayed ${displayedCount} places in table (${places.length} total)`, 'success');
}

function addMarkers(places) {
    clearMarkers();
    
    const startTime = Date.now();
    debugLog(`📍 Adding ${places.length} markers to map...`, 'info');

    // Batch marker creation for better performance
    const bounds = new google.maps.LatLngBounds();
    
    places.forEach((place, index) => {
        if (!place.geometry || !place.geometry.location) return;

        const marker = new google.maps.Marker({
            position: place.geometry.location,
            map: map,
            title: place.name,
            icon: 'https://maps.google.com/mapfiles/ms/icons/red-dot.png',
            optimized: true // Enable marker optimization
        });

        marker.addListener('click', () => {
            showPlaceDetails(index);
        });

        markers.push(marker);
        bounds.extend(place.geometry.location);
    });
    
    // Fit map to show all markers
    if (markers.length > 0) {
        map.fitBounds(bounds);
    }
    
    const elapsed = Date.now() - startTime;
    debugLog(`✅ Added ${markers.length} markers in ${elapsed}ms`, 'success');
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
    // Ignore timeout errors as they're handled elsewhere
    if (event.reason && event.reason.message && event.reason.message.includes('timeout')) {
        debugLog(`⏱️ Search timeout handled`, 'warning');
        event.preventDefault();
        return;
    }
    
    debugLog(`❌ Unhandled Promise Rejection: ${event.reason}`, 'error');
    console.error('Unhandled rejection:', event.reason);
    
    // Hide loading spinner
    const loadingEl = document.getElementById('loadingResults');
    if (loadingEl) loadingEl.style.display = 'none';
    
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
@php
    $googleMapsApiKey = config('services.google_maps.api_key');
@endphp

@if(empty($googleMapsApiKey))
<script>
    document.getElementById('map').innerHTML = `
        <div class="alert alert-danger m-3">
            <h5><i class="fas fa-exclamation-triangle"></i> Google Maps API Key غير موجود</h5>
            <p>يرجى إضافة GOOGLE_MAPS_API_KEY في ملف .env</p>
            <p>قم بالخطوات التالية:</p>
            <ol class="text-start">
                <li>افتح <strong>.env</strong> file</li>
                <li>أضف السطر: <code>GOOGLE_MAPS_API_KEY=your_api_key_here</code></li>
                <li>احصل على API Key من: <a href="https://console.cloud.google.com/apis/credentials" target="_blank">Google Cloud Console</a></li>
                <li>فعّل هذه الخدمات:
                    <ul>
                        <li>Maps JavaScript API</li>
                        <li>Places API (New)</li>
                        <li>Geocoding API</li>
                    </ul>
                </li>
                <li>أضف Billing Account (مطلوب)</li>
                <li>احفظ وأعد تحميل الصفحة</li>
            </ol>
        </div>
    `;
    document.getElementById('debugConsole').innerHTML = '<span style="color: #ff0000">[ERROR] GOOGLE_MAPS_API_KEY not found in .env file</span>';
</script>
@else
<script async defer src="https://maps.googleapis.com/maps/api/js?key={{ $googleMapsApiKey }}&libraries=places,drawing,geometry&language=ar&callback=initMap&loading=async"></script>
@endif

<style>
#map {
    border-radius: 0 0 0.375rem 0.375rem;
}
</style>
@endsection
