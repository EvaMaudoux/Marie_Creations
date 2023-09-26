self.addEventListener('install', () => {
    self.skipWaiting()
})

// Activation du service worker
self.addEventListener('activate', function(event) {
    console.log('Service worker activated.');
    event.waitUntil(self.clients.claim());
});

self.addEventListener('push', (event) => {
    const data = event.data ? event.data.json() : {};
    event.waitUntil(
        self.registration.showNotification(data.title, data)
    )
})

// Redirection quand l'utilisateur clique sur la notification
self.addEventListener('notificationclick', (event) => {
    event.waitUntil(
        // à modifier avec la bonne url du site
        openUrl('http://127.0.0.1:8000/profil/mes-reservations')
    )
})

// Si la page de redirection est déjà ouverte dans le navigateur, redirection. Sinon, ouverture de la page
async function openUrl(url) {
    const windowClients = await self.clients.matchAll({type: 'window', includeUncontrolled: true})
    for (let i = 0; i < windowClients.length; i++) {
        const client = windowClients[i]
        if (client.url === url && 'focus' in client) {
            return client.focus()
        }
    }
    if (self.clients.openWindow) {
        return self.clients.openWindow(url);
    }
    return null;
}







