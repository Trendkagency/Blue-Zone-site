// Firebase Messaging Service Worker for Blue Zone Admin
importScripts('https://www.gstatic.com/firebasejs/9.23.0/firebase-app-compat.js');
importScripts('https://www.gstatic.com/firebasejs/9.23.0/firebase-messaging-compat.js');

// Parse URL parameters if passed when registering worker, or use BlueZone Firebase defaults
const urlParams = new URLSearchParams(location.search);
const firebaseConfig = {
    apiKey: urlParams.get('apiKey') || 'AIzaSyCRAdcHPrsQOtHJGW7gMY9ZN1WiIOi8ClU',
    authDomain: urlParams.get('authDomain') || 'bluezone-998e6.firebaseapp.com',
    projectId: urlParams.get('projectId') || 'bluezone-998e6',
    storageBucket: urlParams.get('storageBucket') || 'bluezone-998e6.firebasestorage.app',
    messagingSenderId: urlParams.get('messagingSenderId') || '440590917450',
    appId: urlParams.get('appId') || '1:440590917450:web:9e289f9a0a36b2f10d2bb3',
};

try {
    if (!firebase.apps.length) {
        firebase.initializeApp(firebaseConfig);
    }

        const messaging = firebase.messaging();

        messaging.onBackgroundMessage(function(payload) {
            const title = (payload.notification && payload.notification.title) || (payload.data && payload.data.title) || 'BlueZone Alert';
            const body = (payload.notification && payload.notification.body) || (payload.data && payload.data.body) || '';
            const actionUrl = (payload.data && payload.data.action_url) || '/admin';

            const options = {
                body: body,
                icon: '/bluezone logo.png',
                badge: '/favicon.ico',
                data: {
                    action_url: actionUrl
                }
            };

            return self.registration.showNotification(title, options);
        });
    } catch (e) {
        console.warn('FCM SW initialization note:', e);
    }

self.addEventListener('notificationclick', function(event) {
    event.notification.close();
    const targetUrl = (event.notification.data && event.notification.data.action_url) || '/admin';
    event.waitUntil(
        clients.matchAll({ type: 'window', includeUncontrolled: true }).then(function(clientList) {
            for (let i = 0; i < clientList.length; i++) {
                const client = clientList[i];
                if ('focus' in client) {
                    client.navigate(targetUrl);
                    return client.focus();
                }
            }
            if (clients.openWindow) {
                return clients.openWindow(targetUrl);
            }
        })
    );
});
