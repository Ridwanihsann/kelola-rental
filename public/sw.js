const CACHE_NAME = 'rental-app-v1';
const STATIC_ASSETS = [
    '/',
    '/items',
    '/css/app.css',
    '/js/app.js',
    '/manifest.json',
    '/icons/icon-192x192.png',
    '/icons/icon-512x512.png'
];

// Install event - cache static assets
self.addEventListener('install', event => {
    console.log('[SW] Installing...');
    event.waitUntil(
        caches.open(CACHE_NAME).then(cache => {
            console.log('[SW] Caching static assets');
            return cache.addAll(STATIC_ASSETS);
        })
    );
    self.skipWaiting();
});

// Activate event - clean old caches
self.addEventListener('activate', event => {
    console.log('[SW] Activating...');
    event.waitUntil(
        caches.keys().then(keys => {
            return Promise.all(
                keys.filter(key => key !== CACHE_NAME)
                    .map(key => caches.delete(key))
            );
        })
    );
    self.clients.claim();
});

// Fetch event - serve from cache, fallback to network
self.addEventListener('fetch', event => {
    // Skip non-GET requests
    if (event.request.method !== 'GET') return;
    
    // Skip API calls - always fetch from network
    if (event.request.url.includes('/api/')) {
        event.respondWith(
            fetch(event.request).catch(() => {
                return new Response(JSON.stringify({ 
                    error: 'Offline', 
                    message: 'Tidak ada koneksi internet' 
                }), {
                    status: 503,
                    headers: { 'Content-Type': 'application/json' }
                });
            })
        );
        return;
    }
    
    // For HTML pages - network first, cache fallback
    if (event.request.headers.get('accept').includes('text/html')) {
        event.respondWith(
            fetch(event.request)
                .then(response => {
                    // Cache the page
                    const clone = response.clone();
                    caches.open(CACHE_NAME).then(cache => {
                        cache.put(event.request, clone);
                    });
                    return response;
                })
                .catch(() => {
                    return caches.match(event.request).then(cached => {
                        return cached || caches.match('/offline.html');
                    });
                })
        );
        return;
    }
    
    // For other assets - cache first, network fallback
    event.respondWith(
        caches.match(event.request).then(cached => {
            return cached || fetch(event.request).then(response => {
                // Cache new assets
                const clone = response.clone();
                caches.open(CACHE_NAME).then(cache => {
                    cache.put(event.request, clone);
                });
                return response;
            });
        })
    );
});

// Background sync for offline data
self.addEventListener('sync', event => {
    if (event.tag === 'sync-rentals') {
        event.waitUntil(syncRentals());
    }
});

async function syncRentals() {
    // Get pending rentals from IndexedDB and sync to server
    const db = await openDB();
    const pending = await db.getAll('pendingRentals');
    
    for (const rental of pending) {
        try {
            await fetch('/api/rentals', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(rental)
            });
            await db.delete('pendingRentals', rental.id);
        } catch (error) {
            console.log('[SW] Sync failed, will retry later');
        }
    }
}

// Simple IndexedDB wrapper
function openDB() {
    return new Promise((resolve, reject) => {
        const request = indexedDB.open('RentalAppDB', 1);
        
        request.onerror = () => reject(request.error);
        request.onsuccess = () => resolve({
            db: request.result,
            getAll: (store) => new Promise((res, rej) => {
                const tx = request.result.transaction(store, 'readonly');
                const req = tx.objectStore(store).getAll();
                req.onsuccess = () => res(req.result);
                req.onerror = () => rej(req.error);
            }),
            delete: (store, id) => new Promise((res, rej) => {
                const tx = request.result.transaction(store, 'readwrite');
                const req = tx.objectStore(store).delete(id);
                req.onsuccess = () => res();
                req.onerror = () => rej(req.error);
            })
        });
        
        request.onupgradeneeded = (e) => {
            const db = e.target.result;
            if (!db.objectStoreNames.contains('pendingRentals')) {
                db.createObjectStore('pendingRentals', { keyPath: 'id', autoIncrement: true });
            }
            if (!db.objectStoreNames.contains('cachedItems')) {
                db.createObjectStore('cachedItems', { keyPath: 'code' });
            }
        };
    });
}
