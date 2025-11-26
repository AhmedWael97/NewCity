@if(config('services.firebase.enabled') && config('services.firebase.web.api_key'))
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

    // Request notification permission and get FCM token
    async function initializeFCM() {
        try {
            const permission = await Notification.requestPermission();
            
            if (permission === 'granted') {
                console.log('Notification permission granted.');
                
                // Get FCM token
                const token = await getToken(messaging, {
                    vapidKey: '{{ config('services.firebase.web.vapid_key') ?? '' }}'
                });
                
                if (token) {
                    console.log('FCM Token:', token);
                    
                    // Send token to server
                    @auth
                    fetch('/api/v1/device-tokens', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'Authorization': 'Bearer {{ auth()->user()->createToken("web-fcm")->plainTextToken ?? "" }}',
                            'Accept': 'application/json',
                        },
                        body: JSON.stringify({
                            token: token,
                            platform: 'web',
                            device_name: navigator.userAgent
                        })
                    }).then(response => response.json())
                      .then(data => console.log('Token saved:', data))
                      .catch(error => console.error('Error saving token:', error));
                    @endauth
                } else {
                    console.log('No registration token available.');
                }
            } else {
                console.log('Unable to get permission to notify.');
            }
        } catch (error) {
            console.error('An error occurred while retrieving token:', error);
        }
    }

    // Initialize FCM when page loads
    if ('Notification' in window) {
        initializeFCM();
    }

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
