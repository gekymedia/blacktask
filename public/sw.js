// Service Worker for BLACKTASK Push Notifications

self.addEventListener('install', function(event) {
    console.log('Service Worker installing.');
    self.skipWaiting();
});

self.addEventListener('activate', function(event) {
    console.log('Service Worker activating.');
});

self.addEventListener('push', function(event) {
    console.log('Push message received.', event);

    if (event.data) {
        const data = event.data.json();

        const options = {
            body: data.body,
            icon: data.icon || '/favicon.ico',
            badge: data.badge || '/favicon.ico',
            data: data.data || {},
            actions: data.actions || []
        };

        event.waitUntil(
            self.registration.showNotification(data.title, options)
        );
    }
});

self.addEventListener('notificationclick', function(event) {
    console.log('Notification click received.', event);

    event.notification.close();

    if (event.action === 'complete') {
        // Handle complete action - could open app and mark task as complete
        event.waitUntil(
            clients.openWindow('/dashboard')
        );
    } else if (event.action === 'view') {
        // Handle view action
        event.waitUntil(
            clients.openWindow('/dashboard')
        );
    } else {
        // Default action
        event.waitUntil(
            clients.openWindow('/dashboard')
        );
    }
});