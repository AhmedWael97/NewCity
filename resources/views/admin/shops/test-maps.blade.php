<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Google Maps API Test - اختبار تشخيصي</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        body { padding: 20px; background: #f5f5f5; }
        .test-card { margin-bottom: 20px; }
        .status-ok { color: #28a745; }
        .status-error { color: #dc3545; }
        .status-warning { color: #ffc107; }
        #map { height: 400px; width: 100%; border: 2px solid #ddd; border-radius: 8px; }
        .log-entry { 
            padding: 5px 10px; 
            margin: 2px 0; 
            border-radius: 4px; 
            font-family: monospace; 
            font-size: 13px;
        }
        .log-success { background: #d4edda; color: #155724; }
        .log-error { background: #f8d7da; color: #721c24; }
        .log-warning { background: #fff3cd; color: #856404; }
        .log-info { background: #d1ecf1; color: #0c5460; }
        pre { background: #282c34; color: #abb2bf; padding: 15px; border-radius: 8px; max-height: 400px; overflow-y: auto; }
    </style>
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h1><i class="fas fa-flask"></i> Google Maps API - اختبار تشخيصي</h1>
            <a href="{{ route('admin.shops.map') }}" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> العودة للصفحة الأساسية
            </a>
        </div>

        <!-- Test Results Summary -->
        <div class="card test-card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-check-circle"></i> نتائج الاختبار</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6>التكوين الأساسي</h6>
                        <ul id="configTests" class="list-unstyled"></ul>
                    </div>
                    <div class="col-md-6">
                        <h6>وظائف API</h6>
                        <ul id="apiTests" class="list-unstyled"></ul>
                    </div>
                </div>
            </div>
        </div>

        <!-- Live Log Console -->
        <div class="card test-card">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0"><i class="fas fa-terminal"></i> سجل الأحداث المباشر</h5>
            </div>
            <div class="card-body" id="liveLog" style="max-height: 300px; overflow-y: auto; background: #f8f9fa;">
                <!-- Logs will appear here -->
            </div>
        </div>

        <!-- Map Test -->
        <div class="card test-card">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0"><i class="fas fa-map"></i> اختبار تحميل الخريطة</h5>
            </div>
            <div class="card-body">
                <div id="map"></div>
                <div class="mt-3">
                    <button class="btn btn-primary" onclick="testGeocoding()">
                        <i class="fas fa-search-location"></i> اختبار Geocoding API
                    </button>
                    <button class="btn btn-success" onclick="testPlacesSearch()">
                        <i class="fas fa-store"></i> اختبار Places API
                    </button>
                    <button class="btn btn-warning" onclick="testNearbySearch()">
                        <i class="fas fa-map-marked-alt"></i> اختبار Nearby Search
                    </button>
                </div>
            </div>
        </div>

        <!-- Configuration Details -->
        <div class="card test-card">
            <div class="card-header bg-secondary text-white">
                <h5 class="mb-0"><i class="fas fa-cog"></i> تفاصيل التكوين</h5>
            </div>
            <div class="card-body">
                <pre id="configDetails"></pre>
            </div>
        </div>

        <!-- API Response Details -->
        <div class="card test-card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-code"></i> استجابات API</h5>
            </div>
            <div class="card-body">
                <pre id="apiResponses"></pre>
            </div>
        </div>
    </div>

    <script>
        // Global variables
        let map = null;
        let placesService = null;
        let geocoder = null;
        const apiKey = '{{ config("services.google_maps.api_key") }}';
        
        // Logging functions
        function log(message, type = 'info') {
            const logDiv = document.getElementById('liveLog');
            const entry = document.createElement('div');
            entry.className = `log-entry log-${type}`;
            entry.innerHTML = `<strong>[${new Date().toLocaleTimeString()}]</strong> ${message}`;
            logDiv.appendChild(entry);
            logDiv.scrollTop = logDiv.scrollHeight;
            console.log(`[${type.toUpperCase()}] ${message}`);
        }

        function addTest(listId, text, status) {
            const list = document.getElementById(listId);
            const item = document.createElement('li');
            const icon = status === 'ok' ? 'check-circle' : (status === 'error' ? 'times-circle' : 'exclamation-triangle');
            const className = status === 'ok' ? 'status-ok' : (status === 'error' ? 'status-error' : 'status-warning');
            item.innerHTML = `<i class="fas fa-${icon} ${className}"></i> ${text}`;
            list.appendChild(item);
        }

        function appendResponse(message) {
            const pre = document.getElementById('apiResponses');
            pre.textContent += message + '\n\n';
        }

        // Phase 1: Configuration Tests
        function testConfiguration() {
            log('🔍 بدء اختبار التكوين...', 'info');
            
            const config = {
                apiKey: apiKey,
                apiKeyLength: apiKey.length,
                apiKeyPrefix: apiKey.substring(0, 10) + '...',
                hasApiKey: apiKey && apiKey.length > 0,
                appUrl: '{{ config("app.url") }}',
                appEnv: '{{ config("app.env") }}',
                googleMapsConfig: @json(config('services.google_maps')),
                timestamp: new Date().toISOString()
            };

            document.getElementById('configDetails').textContent = JSON.stringify(config, null, 2);

            if (config.hasApiKey) {
                addTest('configTests', 'API Key موجود', 'ok');
                log('✅ API Key موجود في التكوين', 'success');
                
                if (config.apiKeyLength === 39) {
                    addTest('configTests', 'طول API Key صحيح (39 حرف)', 'ok');
                    log('✅ طول API Key صحيح', 'success');
                } else {
                    addTest('configTests', `طول API Key غير صحيح: ${config.apiKeyLength} حرف`, 'warning');
                    log(`⚠️ طول API Key: ${config.apiKeyLength} (المتوقع: 39)`, 'warning');
                }
            } else {
                addTest('configTests', 'API Key غير موجود', 'error');
                log('❌ API Key غير موجود', 'error');
            }

            addTest('configTests', `البيئة: ${config.appEnv}`, 'ok');
            log(`📝 بيئة التطبيق: ${config.appEnv}`, 'info');
        }

        // Phase 2: Initialize Map
        function initMap() {
            log('🗺️ بدء تحميل الخريطة...', 'info');
            
            try {
                // Check if Google Maps loaded
                if (typeof google === 'undefined') {
                    addTest('apiTests', 'Google Maps SDK لم يتم تحميله', 'error');
                    log('❌ Google Maps SDK غير متاح', 'error');
                    document.getElementById('map').innerHTML = '<div class="alert alert-danger">فشل تحميل Google Maps SDK</div>';
                    return;
                }

                addTest('apiTests', 'Google Maps SDK محمّل', 'ok');
                log('✅ Google Maps SDK محمّل بنجاح', 'success');

                // Create map
                map = new google.maps.Map(document.getElementById('map'), {
                    center: { lat: 24.7136, lng: 46.6753 }, // Riyadh
                    zoom: 12
                });

                addTest('apiTests', 'الخريطة تم إنشاؤها', 'ok');
                log('✅ تم إنشاء الخريطة بنجاح', 'success');

                // Initialize services
                placesService = new google.maps.places.PlacesService(map);
                geocoder = new google.maps.Geocoder();

                addTest('apiTests', 'Places Service جاهز', 'ok');
                addTest('apiTests', 'Geocoding Service جاهز', 'ok');
                log('✅ تم تهيئة Places و Geocoding Services', 'success');

                // Add a test marker
                new google.maps.Marker({
                    position: { lat: 24.7136, lng: 46.6753 },
                    map: map,
                    title: 'موقع اختبار - الرياض'
                });

                log('✅ جميع اختبارات التحميل نجحت', 'success');

            } catch (error) {
                addTest('apiTests', `خطأ: ${error.message}`, 'error');
                log(`❌ خطأ في تهيئة الخريطة: ${error.message}`, 'error');
                appendResponse(`ERROR: ${error.message}\n${error.stack}`);
            }
        }

        // Phase 3: Test Geocoding API
        function testGeocoding() {
            log('🌍 بدء اختبار Geocoding API...', 'info');
            
            if (!geocoder) {
                log('❌ Geocoder غير متاح', 'error');
                return;
            }

            geocoder.geocode({ address: 'الرياض، السعودية' }, function(results, status) {
                log(`📡 استجابة Geocoding: ${status}`, 'info');
                
                if (status === 'OK') {
                    log('✅ Geocoding API يعمل بنجاح', 'success');
                    appendResponse('=== GEOCODING TEST ===\n' + JSON.stringify(results[0], null, 2));
                    
                    // Move map to result
                    map.setCenter(results[0].geometry.location);
                    new google.maps.Marker({
                        map: map,
                        position: results[0].geometry.location,
                        title: 'نتيجة Geocoding'
                    });
                } else {
                    log(`❌ Geocoding فشل: ${status}`, 'error');
                    appendResponse(`GEOCODING ERROR: ${status}`);
                }
            });
        }

        // Phase 4: Test Places Search
        function testPlacesSearch() {
            log('🏪 بدء اختبار Places API (Text Search)...', 'info');
            
            if (!placesService) {
                log('❌ Places Service غير متاح', 'error');
                return;
            }

            const request = {
                query: 'مطاعم في الرياض',
                fields: ['name', 'geometry', 'formatted_address', 'rating']
            };

            placesService.textSearch(request, function(results, status) {
                log(`📡 استجابة Places (Text Search): ${status}`, 'info');
                
                if (status === google.maps.places.PlacesServiceStatus.OK) {
                    log(`✅ Places API يعمل! وجد ${results.length} نتيجة`, 'success');
                    appendResponse('=== PLACES TEXT SEARCH TEST ===\n' + JSON.stringify(results.slice(0, 3), null, 2));
                    
                    // Add markers
                    results.slice(0, 5).forEach(place => {
                        new google.maps.Marker({
                            map: map,
                            position: place.geometry.location,
                            title: place.name
                        });
                    });
                    
                    if (results.length > 0) {
                        map.setCenter(results[0].geometry.location);
                    }
                } else {
                    log(`❌ Places Search فشل: ${status}`, 'error');
                    appendResponse(`PLACES SEARCH ERROR: ${status}`);
                    
                    if (status === 'REQUEST_DENIED') {
                        log('⚠️ REQUEST_DENIED - تأكد من تفعيل Places API وإضافة Billing', 'warning');
                    }
                }
            });
        }

        // Phase 5: Test Nearby Search
        function testNearbySearch() {
            log('📍 بدء اختبار Places API (Nearby Search)...', 'info');
            
            if (!placesService) {
                log('❌ Places Service غير متاح', 'error');
                return;
            }

            const request = {
                location: { lat: 24.7136, lng: 46.6753 }, // Riyadh
                radius: 100, // 100m
                type: ['store']
            };

            log(`📤 إرسال طلب: location=[24.7136, 46.6753], radius=100m, type=store`, 'info');
            placesService.nearbySearch(request, function(results, status) {
                log(`📡 استجابة Places (Nearby Search): ${status}`, 'info');
                
                if (status === google.maps.places.PlacesServiceStatus.OK) {
                    log(`✅ Nearby Search يعمل! وجد ${results.length} متجر`, 'success');
                    appendResponse('=== PLACES NEARBY SEARCH TEST ===\nRequest: ' + JSON.stringify(request, null, 2) + '\n\nResults:\n' + JSON.stringify(results.slice(0, 5), null, 2));
                    
                    // Add markers
                    results.slice(0, 10).forEach(place => {
                        new google.maps.Marker({
                            map: map,
                            position: place.geometry.location,
                            title: place.name,
                            icon: 'https://maps.google.com/mapfiles/ms/icons/green-dot.png'
                        });
                    });
                    
                    log(`📊 أول 3 نتائج:`, 'info');
                    results.slice(0, 3).forEach((place, i) => {
                        log(`  ${i+1}. ${place.name} - ${place.vicinity}`, 'info');
                    });
                    
                } else if (status === google.maps.places.PlacesServiceStatus.ZERO_RESULTS) {
                    log('⚠️ لا توجد نتائج في هذه المنطقة', 'warning');
                    appendResponse('NEARBY SEARCH: ZERO_RESULTS');
                } else {
                    log(`❌ Nearby Search فشل: ${status}`, 'error');
                    appendResponse(`NEARBY SEARCH ERROR: ${status}\nRequest: ${JSON.stringify(request, null, 2)}`);
                    
                    if (status === 'REQUEST_DENIED') {
                        log('⚠️ REQUEST_DENIED - Places API غير مفعل أو Billing غير مضاف', 'warning');
                        log('💡 الحل: https://console.cloud.google.com/apis/library/places-backend.googleapis.com', 'info');
                    } else if (status === 'OVER_QUERY_LIMIT') {
                        log('⚠️ OVER_QUERY_LIMIT - تجاوزت حد الطلبات', 'warning');
                    } else if (status === 'INVALID_REQUEST') {
                        log('⚠️ INVALID_REQUEST - الطلب غير صحيح', 'warning');
                    }
                }
            });
        }

        // Error handlers
        window.gm_authFailure = function() {
            addTest('apiTests', 'فشل المصادقة', 'error');
            log('❌ Google Maps authentication failed', 'error');
            document.getElementById('map').innerHTML = '<div class="alert alert-danger"><h5>❌ فشل المصادقة</h5><p>API Key غير صحيح أو غير مفعّل</p></div>';
        };

        // Initialize on load
        window.addEventListener('load', function() {
            log('🚀 بدء الاختبارات التشخيصية...', 'info');
            testConfiguration();
        });

        // Expose initMap globally
        window.initMap = initMap;
    </script>

    <!-- Load Google Maps -->
    @if(!empty(config('services.google_maps.api_key')))
        <script async defer 
            src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google_maps.api_key') }}&libraries=places,drawing,geometry&language=ar&callback=initMap&loading=async"
            onerror="log('❌ فشل تحميل Google Maps script', 'error'); addTest('apiTests', 'فشل تحميل SDK', 'error');">
        </script>
    @else
        <script>
            log('❌ API Key غير موجود في التكوين', 'error');
            addTest('configTests', 'API Key غير موجود', 'error');
            document.getElementById('map').innerHTML = '<div class="alert alert-danger">API Key غير موجود في ملف .env</div>';
        </script>
    @endif
</body>
</html>
