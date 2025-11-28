// Firebase Cloud Messaging Service Worker
// This enables background notification handling in Chrome

importScripts('https://www.gstatic.com/firebasejs/12.6.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/12.6.0/firebase-messaging-compat.js');

// Initialize Firebase in the service worker
firebase.initializeApp({
    apiKey: "AIzaSyDiVlpeECTtVrseh86myD1c6LNZuGNaKH4",
    authDomain: "senu-66fb2.firebaseapp.com",
    projectId: "senu-66fb2",
    storageBucket: "senu-66fb2.firebasestorage.app",
    messagingSenderId: "628088931694",
    appId: "1:628088931694:web:dd3e45338a5cb60bb02d7f",
    measurementId: "G-MDM720Y5MW"
});

const messaging = firebase.messaging();

// Handle background messages
messaging.onBackgroundMessage((payload) => {
    console.log('[firebase-messaging-sw.js] Received background message ', payload);
    
    const notificationTitle = payload.notification?.title || 'New Notification';
    const notificationOptions = {
        body: payload.notification?.body || '',
        icon: payload.notification?.icon || '/images/senu-logo.svg',
        badge: '/images/senu-logo.svg',
        tag: payload.data?.notification_id || 'default',
        requireInteraction: false,
        data: payload.data || {}
    };

    self.registration.showNotification(notificationTitle, notificationOptions);
});

// Handle notification click
self.addEventListener('notificationclick', (event) => {
    console.log('[Service Worker] Notification click received.');
    
    event.notification.close();
    
    // Get the action URL from notification data
    const urlToOpen = event.notification.data?.action_url || '/';
    
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true })
            .then((clientList) => {
                // Check if there's already a window/tab open
                for (let i = 0; i < clientList.length; i++) {
                    const client = clientList[i];
                    if (client.url === urlToOpen && 'focus' in client) {
                        return client.focus();
                    }
                }
                // If not, open new window/tab
                if (clients.openWindow) {
                    return clients.openWindow(urlToOpen);
                }
            })
    );
    
    // Mark notification as opened
    if (event.notification.data?.notification_log_id) {
        fetch('/api/v1/notifications/opened', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                notification_log_id: event.notification.data.notification_log_id
            })
        }).catch(err => console.error('Error marking notification as opened:', err));
    }
});
