/**
 * BUF Template — Service Worker stub
 * Registers the template as a PWA-capable site worker.
 * Extend with caching strategies as needed.
 */

const CACHE_NAME = 'buf-v1';

self.addEventListener('install', (event) => {
    self.skipWaiting();
});

self.addEventListener('activate', (event) => {
    event.waitUntil(
        caches.keys().then((keys) =>
            Promise.all(
                keys.filter((key) => key !== CACHE_NAME).map((key) => caches.delete(key))
            )
        )
    );
    self.clients.claim();
});

self.addEventListener('fetch', (event) => {
    // Network-first strategy — pass through all requests unchanged.
    // Replace with a cache-first strategy to enable offline support.
    event.respondWith(fetch(event.request));
});
