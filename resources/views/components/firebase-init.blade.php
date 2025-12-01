@if(config('services.firebase.enabled') && config('services.firebase.web.api_key'))
<!-- Notification Permission Modal -->
<div class="modal fade" id="notificationPermissionModal" tabindex="-1" role="dialog" aria-labelledby="notificationPermissionModalLabel" aria-hidden="true" data-backdrop="static" data-keyboard="false">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white" style="direction: rtl; position: relative;">
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close" style="opacity: 1; font-size: 1.8rem; line-height: 1; font-weight: 300; padding: 0; margin: 0; border: none; background: none; cursor: pointer; position: absolute; right: 15px; top: 50%; transform: translateY(-50%);">
                    <span aria-hidden="true">×</span>
                </button>
                <h5 class="modal-title" id="notificationPermissionModalLabel" style="width: 100%; text-align: center;">
                    <i class="fas fa-bell"></i> تفعيل الإشعارات
                </h5>
            </div>
            <div class="modal-body text-center">
                <div class="mb-3">
                    <i class="fas fa-bell fa-4x text-primary"></i>
                </div>
                <h5 class="mb-3">ابقَ على اطلاع دائم بكل جديد!</h5>
                <p class="text-muted mb-4">
                    فعّل الإشعارات الآن لتصلك آخر العروض والتحديثات من المتاجر والخدمات في مدينتك
                </p>
                <ul class="text-right mb-4" style="list-style: none; padding: 0;">
                    <li class="mb-2"><i class="fas fa-check text-success mr-2"></i> عروض حصرية وخصومات خاصة</li>
                    <li class="mb-2"><i class="fas fa-check text-success mr-2"></i> تحديثات فورية عن المتاجر الجديدة</li>
                    <li class="mb-2"><i class="fas fa-check text-success mr-2"></i> أخبار وإعلانات مهمة</li>
                </ul>
                <p class="small text-muted">
                    <i class="fas fa-info-circle"></i> يمكنك إلغاء الإشعارات في أي وقت من إعدادات المتصفح
                </p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-primary btn-lg px-5" id="enableNotificationsBtn">
                    <i class="fas fa-bell"></i> تفعيل الإشعارات
                </button>
                <button type="button" class="btn btn-outline-secondary" data-dismiss="modal" id="notNowBtn">
                    ليس الآن
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Permission Denied Help Modal -->
<div class="modal fade" id="permissionDeniedModal" tabindex="-1" role="dialog" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered" role="document">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark" style="direction: rtl; position: relative;">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="opacity: 1; font-size: 1.8rem; line-height: 1; font-weight: 300; padding: 0; margin: 0; border: none; background: none; cursor: pointer; position: absolute; right: 15px; top: 50%; transform: translateY(-50%);">
                    <span aria-hidden="true">×</span>
                </button>
                <h5 class="modal-title" style="width: 100%; text-align: center;">
                    <i class="fas fa-exclamation-triangle"></i> الإشعارات محظورة
                </h5>
            </div>
            <div class="modal-body">
                <p class="mb-3">لقد تم حظر الإشعارات مسبقاً. لتفعيلها مرة أخرى:</p>
                
                <div class="alert alert-info">
                    <strong>متصفح Chrome/Edge:</strong>
                    <ol class="mb-0 text-right" style="padding-right: 20px;">
                        <li>انقر على أيقونة القفل <i class="fas fa-lock"></i> بجوار عنوان الموقع</li>
                        <li>ابحث عن "الإشعارات" أو "Notifications"</li>
                        <li>اختر "السماح" أو "Allow"</li>
                        <li>أعد تحميل الصفحة</li>
                    </ol>
                </div>

                <div class="alert alert-info">
                    <strong>متصفح Firefox:</strong>
                    <ol class="mb-0 text-right" style="padding-right: 20px;">
                        <li>انقر على أيقونة الإعدادات <i class="fas fa-cog"></i> في شريط العنوان</li>
                        <li>اختر "إزالة الحظر المؤقت للإشعارات"</li>
                        <li>أعد تحميل الصفحة</li>
                    </ol>
                </div>

                <div class="alert alert-info">
                    <strong>متصفح Safari:</strong>
                    <ol class="mb-0 text-right" style="padding-right: 20px;">
                        <li>افتح تفضيلات Safari → المواقع</li>
                        <li>اختر "الإشعارات" من القائمة</li>
                        <li>ابحث عن الموقع وغيّر الإعداد إلى "السماح"</li>
                    </ol>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">فهمت</button>
                <button type="button" class="btn btn-primary" onclick="window.location.reload();">
                    <i class="fas fa-sync"></i> إعادة تحميل الصفحة
                </button>
            </div>
        </div>
    </div>
</div>

<script type="module">
    // Import the functions you need from the SDKs you need
    import { initializeApp } from "https://www.gstatic.com/firebasejs/12.6.0/firebase-app.js";
    import { getAnalytics } from "https://www.gstatic.com/firebasejs/12.6.0/firebase-analytics.js";
    import { getMessaging, getToken, onMessage } from "https://www.gstatic.com/firebasejs/12.6.0/firebase-messaging.js";

    // Your web app's Firebase configuration
    const firebaseConfig = {
        apiKey: "{{ config('services.firebase.web.api_key') }}",
        authDomain: "{{ config('services.firebase.web.auth_domain') }}",
        projectId: "{{ config('services.firebase.web.project_id') }}",
        storageBucket: "{{ config('services.firebase.web.storage_bucket') }}",
        messagingSenderId: "{{ config('services.firebase.web.messaging_sender_id') }}",
        appId: "{{ config('services.firebase.web.app_id') }}",
        measurementId: "{{ config('services.firebase.web.measurement_id') }}"
    };

    // Initialize Firebase
    const app = initializeApp(firebaseConfig);
    const analytics = getAnalytics(app);

    // Initialize Firebase Cloud Messaging
    const messaging = getMessaging(app);

    // Save token to server
    async function saveTokenToServer(token) {
        console.log('💾 Attempting to save token to server...');
        console.log('📝 Token:', token);
        
        try {
            const headers = {
                'Content-Type': 'application/json',
                'Accept': 'application/json',
            };
            
            @auth
            // Authenticated user - use bearer token
            console.log('👤 User authenticated - using auth endpoint');
            headers['Authorization'] = 'Bearer {{ auth()->user()->createToken("web-fcm")->plainTextToken ?? "" }}';
            const endpoint = '/api/v1/device-tokens';
            @else
            // Guest user - use public endpoint
            console.log('👤 Guest user - using public endpoint');
            headers['X-CSRF-TOKEN'] = document.querySelector('meta[name="csrf-token"]')?.content || '';
            const endpoint = '/api/v1/guest-device-tokens';
            console.log('🔑 CSRF Token:', headers['X-CSRF-TOKEN']);
            @endauth
            
            console.log('🌐 Endpoint:', endpoint);
            console.log('📤 Sending request...');
            
            // Get city_id based on user type
            let cityId = null;
            @auth
            // For authenticated users, use their preferred city
            cityId = {{ auth()->user()->preferred_city_id ?? 'null' }};
            console.log('🌍 User preferred city_id:', cityId);
            @else
            // For guests, use session city (from city selection)
            cityId = {{ session('selected_city_id') ?? session('city_id') ?? 'null' }};
            console.log('🌍 Guest selected city_id from session:', cityId);
            @endauth
            
            const payload = {
                device_token: token,
                device_type: 'web',
                device_name: navigator.userAgent.substring(0, 255),
                app_version: 'web-1.0'
            };
            
            // Add city_id if available
            if (cityId) {
                payload.city_id = cityId;
                console.log('✅ Including city_id in payload:', cityId);
            } else {
                console.log('ℹ️ No city_id available - token will receive all notifications');
            }
            
            console.log('📦 Final payload:', payload);
            
            const response = await fetch(endpoint, {
                method: 'POST',
                headers: headers,
                body: JSON.stringify(payload)
            });
            
            console.log('📡 Response status:', response.status);
            
            const data = await response.json();
            console.log('📥 Response data:', data);
            
            if (data.success) {
                console.log('✅ Device token registered successfully');
                
                // Store in localStorage to prevent asking again
                localStorage.setItem('fcm_token_registered', 'true');
                localStorage.setItem('fcm_token', token);
                
                return true;
            } else {
                console.error('❌ Failed to register token:', data.message);
                console.error('❌ Errors:', data.errors);
                return false;
            }
        } catch (error) {
            console.error('❌ Error saving token:', error);
            console.error('❌ Error details:', error.message);
            return false;
        }
    }

    // Request notification permission and get FCM token
    async function requestNotificationPermission() {
        console.log('🔔 Requesting notification permission...');
        
        try {
            const permission = await Notification.requestPermission();
            console.log('🔔 Permission result:', permission);
            
            if (permission === 'granted') {
                console.log('✅ Notification permission granted.');
                
                // Close modal if open
                $('#notificationPermissionModal').modal('hide');
                
                // Check if service worker is registered
                if ('serviceWorker' in navigator) {
                    const registration = await navigator.serviceWorker.getRegistration();
                    console.log('👷 Service Worker registration:', registration ? 'Found' : 'Not found');
                    
                    if (!registration) {
                        console.log('👷 Registering service worker...');
                        await navigator.serviceWorker.register('/firebase-messaging-sw.js');
                        console.log('✅ Service worker registered');
                    }
                }
                
                // Get FCM token
                const vapidKey = '{{ config('services.firebase.web.vapid_key') ?? '' }}';
                console.log('🔑 VAPID Key configured:', vapidKey ? 'Yes (' + vapidKey.substring(0, 20) + '...)' : 'No');
                
                if (!vapidKey) {
                    console.error('❌ VAPID key is missing! Check your .env file.');
                    alert('⚠️ إعدادات الإشعارات غير مكتملة. يرجى التواصل مع الدعم الفني.');
                    return;
                }
                
                console.log('🎫 Getting FCM token...');
                const token = await getToken(messaging, {
                    vapidKey: vapidKey,
                    serviceWorkerRegistration: await navigator.serviceWorker.getRegistration()
                });
                
                if (token) {
                    console.log('✅ FCM Token received:', token);
                    const saved = await saveTokenToServer(token);
                    if (saved) {
                        console.log('🎉 Token saved successfully!');
                    } else {
                        console.error('❌ Failed to save token to server');
                    }
                } else {
                    console.error('❌ No registration token available. Request might be blocked.');
                    console.log('💡 Troubleshooting:');
                    console.log('1. Check if notifications are blocked in browser settings');
                    console.log('2. Check if service worker is properly registered');
                    console.log('3. Check if VAPID key is correct');
                }
            } else if (permission === 'denied') {
                console.log('❌ Notification permission denied.');
                localStorage.setItem('notification_permission_denied', 'true');
                showPermissionDeniedHelp();
            } else {
                console.log('⚠️ Notification permission dismissed.');
            }
        } catch (error) {
            console.error('❌ FCM initialization error:', error);
            console.error('❌ Error name:', error.name);
            console.error('❌ Error message:', error.message);
            console.error('❌ Error stack:', error.stack);
            
            // Show user-friendly error
            alert('⚠️ حدث خطأ أثناء تفعيل الإشعارات: ' + error.message);
        }
    }

    // Detect iOS and Safari version
    function getIOSVersion() {
        const ua = navigator.userAgent;
        const isIOS = /iPad|iPhone|iPod/.test(ua) && !window.MSStream;
        
        if (!isIOS) return null;
        
        const match = ua.match(/OS (\d+)_(\d+)_?(\d+)?/);
        if (match) {
            const major = parseInt(match[1], 10);
            const minor = parseInt(match[2], 10);
            return { major, minor, version: `${major}.${minor}` };
        }
        return null;
    }

    function isSafari() {
        const ua = navigator.userAgent.toLowerCase();
        return ua.indexOf('safari') > -1 && ua.indexOf('chrome') === -1 && ua.indexOf('android') === -1;
    }

    function checkIOSNotificationSupport() {
        const iosVersion = getIOSVersion();
        const isSafariBrowser = isSafari();
        
        console.log('📱 iOS Version:', iosVersion ? iosVersion.version : 'Not iOS');
        console.log('🌐 Safari Browser:', isSafariBrowser ? 'Yes' : 'No');
        
        // Only check iOS devices with Safari browser
        if (iosVersion) {
            // If not Safari, notifications are not supported on iOS (Chrome, Firefox, etc. don't support notifications on iOS)
            if (!isSafariBrowser) {
                console.log('⚠️ iOS device detected but not Safari - Push notifications only work in Safari on iOS');
                return {
                    supported: false,
                    reason: 'ios_not_safari',
                    message: 'الإشعارات الفورية متاحة فقط في متصفح Safari على أجهزة iPhone'
                };
            }
            
            // iOS Safari but version < 16.4
            if (iosVersion.major < 16 || (iosVersion.major === 16 && iosVersion.minor < 4)) {
                console.log('⚠️ iOS version < 16.4 - Push notifications not supported');
                return {
                    supported: false,
                    reason: 'ios_old',
                    message: 'الإشعارات الفورية متاحة على iOS 16.4 أو أحدث'
                };
            }
            
            // iOS 16.4+ Safari but not installed as PWA
            if (!window.navigator.standalone) {
                console.log('⚠️ iOS 16.4+ but not installed as PWA');
                return {
                    supported: false,
                    reason: 'pwa_required',
                    message: 'للحصول على الإشعارات، أضف الموقع إلى الشاشة الرئيسية أولاً'
                };
            }
        }
        
        return { supported: true };
    }

    // Check if we should show the notification prompt
    function shouldShowNotificationPrompt() {
        console.log('🔔 Checking if notification prompt should show...');
        
        // Don't show if not supported
        if (!('Notification' in window)) {
            console.log('❌ Notifications not supported in this browser');
            return false;
        }

        // Check iOS/Safari support
        const iosSupport = checkIOSNotificationSupport();
        if (!iosSupport.supported) {
            console.log('❌ iOS/Safari limitation detected:', iosSupport.reason);
            
            // Show iOS-specific help message
            if (iosSupport.reason === 'ios_old' || iosSupport.reason === 'pwa_required' || iosSupport.reason === 'ios_not_safari') {
                setTimeout(() => {
                    showIOSNotificationHelp(iosSupport.message, iosSupport.reason);
                }, 2000);
            }
            return false;
        }

        // Show to all users (authenticated and guests)
        @auth
        console.log('✅ User is authenticated');
        @else
        console.log('ℹ️ User is guest - will register anonymous token');
        @endauth

        // Don't show if already registered
        if (localStorage.getItem('fcm_token_registered') === 'true') {
            console.log('❌ Token already registered');
            return false;
        }

        // If permission is denied, show help message instead
        if (Notification.permission === 'denied') {
            console.log('❌ Permission denied by browser');
            // Show help modal instead of permission prompt
            setTimeout(() => {
                showPermissionDeniedHelp();
            }, 2000);
            return false;
        }

        // Don't show if permission already granted (auto-register)
        if (Notification.permission === 'granted') {
            console.log('✅ Permission already granted, auto-registering...');
            requestNotificationPermission(); // Auto-register
            return false;
        }

        // Don't show if user dismissed in last 7 days
        const dismissedAt = localStorage.getItem('notification_prompt_dismissed_at');
        if (dismissedAt) {
            const daysSinceDismissed = (Date.now() - parseInt(dismissedAt)) / (1000 * 60 * 60 * 24);
            if (daysSinceDismissed < 7) {
                console.log(`❌ User dismissed ${daysSinceDismissed.toFixed(1)} days ago`);
                return false;
            }
        }

        console.log('✅ Should show notification prompt! Permission status:', Notification.permission);
        return Notification.permission === 'default';
    }

    // Show iOS-specific notification help
    function showIOSNotificationHelp(message, reason) {
        const iosVersion = getIOSVersion();
        
        const modalHtml = `
            <div class="modal fade" id="iosNotificationHelpModal" tabindex="-1" role="dialog" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header bg-info text-white" style="direction: rtl;">
                            <button type="button" class="close text-white ml-auto" data-dismiss="modal" aria-label="Close" style="opacity: 1; font-size: 1.8rem; line-height: 1; padding: 0; margin: 0; border: none; background: none; cursor: pointer; position: absolute; right: 15px; top: 15px;">
                                <span aria-hidden="true">×</span>
                            </button>
                            <h5 class="modal-title" style="flex: 1; text-align: center;">
                                <i class="fab fa-apple"></i> إشعارات Safari على iPhone
                            </h5>
                        </div>
                        <div class="modal-body text-center">
                            <div class="mb-3">
                                <i class="fab fa-apple fa-4x text-primary"></i>
                            </div>
                            <h5 class="mb-3">${message}</h5>
                            
                            ${reason === 'ios_not_safari' ? `
                                <div class="alert alert-warning text-right">
                                    <strong>⚠️ متصفح غير مدعوم:</strong><br>
                                    الإشعارات الفورية على iPhone متاحة فقط في متصفح Safari
                                </div>
                                <p class="text-muted mb-3">
                                    يرجى فتح الموقع في متصفح Safari للحصول على الإشعارات الفورية
                                </p>
                                <div class="alert alert-info text-right">
                                    <p class="mb-2"><strong>كيفية فتح الموقع في Safari:</strong></p>
                                    <ol class="text-right mb-0" style="padding-right: 20px;">
                                        <li>انسخ رابط الموقع</li>
                                        <li>افتح متصفح Safari</li>
                                        <li>الصق الرابط في شريط العنوان</li>
                                        <li>فعّل الإشعارات عند الطلب</li>
                                    </ol>
                                </div>
                            ` : iosVersion && iosVersion.major < 16 ? `
                                <div class="alert alert-warning text-right">
                                    <strong>📱 نظام التشغيل الحالي:</strong> iOS ${getIOSVersion().version}<br>
                                    <strong>✅ النظام المطلوب:</strong> iOS 16.4 أو أحدث
                                </div>
                                <p class="text-muted mb-3">
                                    لتفعيل الإشعارات الفورية، يرجى تحديث جهازك إلى iOS 16.4 أو أحدث
                                </p>
                            ` : `
                                <div class="alert alert-info text-right">
                                    <p class="mb-2"><strong>لتفعيل الإشعارات على iPhone:</strong></p>
                                    <ol class="text-right mb-0" style="padding-right: 20px;">
                                        <li>اضغط على زر المشاركة <i class="fas fa-share"></i> في Safari</li>
                                        <li>اختر "إضافة إلى الشاشة الرئيسية"</li>
                                        <li>افتح التطبيق من الشاشة الرئيسية</li>
                                        <li>فعّل الإشعارات عند الطلب</li>
                                    </ol>
                                </div>
                                <div class="text-center my-3">
                                    <img src="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'%3E%3Ctext y='50' font-size='60'%3E📱➕🏠%3C/text%3E%3C/svg%3E" alt="Add to Home" style="width: 100px;">
                                </div>
                            `}
                            
                            <p class="small text-muted mb-0">
                                <i class="fas fa-info-circle"></i> يمكنك استخدام التطبيق بدون إشعارات، لكن ستفوتك التحديثات الفورية
                            </p>
                        </div>
                        <div class="modal-footer justify-content-center">
                            <button type="button" class="btn btn-primary" data-dismiss="modal">فهمت</button>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Remove existing modal if present
        const existingModal = document.getElementById('iosNotificationHelpModal');
        if (existingModal) {
            existingModal.remove();
        }
        
        // Append new modal
        document.body.insertAdjacentHTML('beforeend', modalHtml);
        
        // Show modal
        $('#iosNotificationHelpModal').modal('show');
    }

    // Register service worker first
    if ('serviceWorker' in navigator) {
        navigator.serviceWorker.register('/firebase-messaging-sw.js')
            .then((registration) => {
                console.log('✅ Service Worker registered:', registration);
            })
            .catch((error) => {
                console.error('❌ Service Worker registration failed:', error);
            });
    } else {
        console.error('❌ Service Workers not supported in this browser');
    }

    // Show modal after a short delay
    setTimeout(() => {
        if (shouldShowNotificationPrompt()) {
            console.log('🔔 Showing notification permission modal...');
            const modal = document.getElementById('notificationPermissionModal');
            if (modal) {
                $('#notificationPermissionModal').modal('show');
            } else {
                console.error('❌ Modal element not found!');
            }
        }
    }, 2000); // Show after 2 seconds

    // Show help modal when permission is denied
    function showPermissionDeniedHelp() {
        console.log('📢 Showing permission denied help modal');
        $('#permissionDeniedModal').modal('show');
    }

    // Make function globally available for testing
    window.testNotificationPrompt = function() {
        console.log('🧪 Manual test triggered');
        $('#notificationPermissionModal').modal('show');
    };

    window.showPermissionHelp = function() {
        console.log('🧪 Showing permission help');
        showPermissionDeniedHelp();
    };

    window.requestNotificationPermissionNow = function() {
        console.log('🧪 Manual permission request triggered');
        requestNotificationPermission();
    };

    window.clearNotificationSettings = function() {
        localStorage.removeItem('fcm_token_registered');
        localStorage.removeItem('notification_permission_denied');
        localStorage.removeItem('notification_prompt_dismissed_at');
        localStorage.removeItem('fcm_token');
        console.log('✅ All notification settings cleared. Refresh page to see prompt again.');
    };

    // Handle enable notifications button click
    document.addEventListener('DOMContentLoaded', function() {
        const enableBtn = document.getElementById('enableNotificationsBtn');
        if (enableBtn) {
            enableBtn.addEventListener('click', async function() {
                this.disabled = true;
                this.innerHTML = '<i class="fas fa-spinner fa-spin"></i> جاري التفعيل...';
                await requestNotificationPermission();
            });
        }

        // Handle "Not Now" button
        const notNowBtn = document.getElementById('notNowBtn');
        if (notNowBtn) {
            notNowBtn.addEventListener('click', function() {
                localStorage.setItem('notification_prompt_dismissed_at', Date.now().toString());
                $('#notificationPermissionModal').modal('hide');
            });
        }
        
        // Ensure all close buttons work properly
        document.querySelectorAll('[data-dismiss="modal"]').forEach(btn => {
            btn.addEventListener('click', function() {
                const modalId = this.closest('.modal').id;
                $(`#${modalId}`).modal('hide');
            });
        });
    });

    // Handle incoming messages when app is in foreground
    onMessage(messaging, (payload) => {
        console.log('Message received:', payload);
        
        // Show notification
        if (Notification.permission === 'granted') {
            const notificationTitle = payload.notification.title;
            const notificationOptions = {
                body: payload.notification.body,
                icon: payload.notification.icon || '/images/logo.png',
                badge: '/images/logo.png',
                data: payload.data
            };

            new Notification(notificationTitle, notificationOptions);
        }
    });

    // Make Firebase app available globally
    window.firebaseApp = app;
    window.firebaseAnalytics = analytics;
    window.firebaseMessaging = messaging;
</script>
@endif
