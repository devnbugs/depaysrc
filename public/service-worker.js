// Service Worker for OneTera PWA
const CACHE_PREFIX = 'onetera-pwa';
const CACHE_VERSION = 'v1';
const CACHE_NAME = `${CACHE_PREFIX}-${CACHE_VERSION}`;

// Assets that should always be cached on install
const CRITICAL_ASSETS = [
  '/',
  '/manifest.json',
  '/offline.html',
  '/assets/logo.png'
];

// Cache strategies
const CACHE_STRATEGIES = {
  // Cache first, fallback to network
  CACHE_FIRST: 'CACHE_FIRST',
  // Network first, fallback to cache
  NETWORK_FIRST: 'NETWORK_FIRST',
  // Stale while revalidate
  STALE_WHILE_REVALIDATE: 'STALE_WHILE_REVALIDATE',
  // Network only
  NETWORK_ONLY: 'NETWORK_ONLY'
};

// Define caching strategy for different resource types
const RESOURCE_CACHE_STRATEGY = {
  // Static assets - cache first
  '/assets/': CACHE_STRATEGIES.CACHE_FIRST,
  '/build/': CACHE_STRATEGIES.CACHE_FIRST,
  '.css': CACHE_STRATEGIES.CACHE_FIRST,
  '.js': CACHE_STRATEGIES.CACHE_FIRST,
  '.jpg': CACHE_STRATEGIES.CACHE_FIRST,
  '.jpeg': CACHE_STRATEGIES.CACHE_FIRST,
  '.png': CACHE_STRATEGIES.CACHE_FIRST,
  '.gif': CACHE_STRATEGIES.CACHE_FIRST,
  '.svg': CACHE_STRATEGIES.CACHE_FIRST,
  '.webp': CACHE_STRATEGIES.CACHE_FIRST,
  '.woff': CACHE_STRATEGIES.CACHE_FIRST,
  '.woff2': CACHE_STRATEGIES.CACHE_FIRST,
  '.ttf': CACHE_STRATEGIES.CACHE_FIRST,
  '.eot': CACHE_STRATEGIES.CACHE_FIRST,
  
  // HTML pages - network first for fresh content
  '.html': CACHE_STRATEGIES.NETWORK_FIRST,
  
  // API calls - network first with fallback
  '/api/': CACHE_STRATEGIES.NETWORK_FIRST,
  
  // Default strategy
  'default': CACHE_STRATEGIES.NETWORK_FIRST
};

/**
 * Determine which caching strategy to use based on URL
 */
function getStrategyForUrl(url) {
  try {
    const urlObj = new URL(url);
    const pathname = urlObj.pathname;
    
    for (const [pattern, strategy] of Object.entries(RESOURCE_CACHE_STRATEGY)) {
      if (pattern === 'default') continue;
      if (pathname.includes(pattern) || url.includes(pattern)) {
        return strategy;
      }
    }
  } catch (e) {
    console.error('Error parsing URL:', url, e);
  }
  
  return RESOURCE_CACHE_STRATEGY.default;
}

/**
 * Install event - cache critical assets
 */
self.addEventListener('install', (event) => {
  console.log('[Service Worker] Installing...');
  
  event.waitUntil(
    caches.open(CACHE_NAME)
      .then((cache) => {
        console.log('[Service Worker] Caching critical assets...');
        return cache.addAll(CRITICAL_ASSETS)
          .catch((error) => {
            console.warn('[Service Worker] Failed to cache some critical assets:', error);
            // Continue even if some assets fail
            return Promise.resolve();
          });
      })
      .then(() => {
        console.log('[Service Worker] Installation complete');
        return self.skipWaiting();
      })
  );
});

/**
 * Activate event - clean up old caches
 */
self.addEventListener('activate', (event) => {
  console.log('[Service Worker] Activating...');
  
  event.waitUntil(
    caches.keys()
      .then((cacheNames) => {
        return Promise.all(
          cacheNames.map((cacheName) => {
            if (cacheName.startsWith(CACHE_PREFIX) && cacheName !== CACHE_NAME) {
              console.log('[Service Worker] Deleting old cache:', cacheName);
              return caches.delete(cacheName);
            }
          })
        );
      })
      .then(() => {
        console.log('[Service Worker] Activation complete');
        return self.clients.claim();
      })
  );
});

/**
 * Fetch event - implement caching strategies
 */
self.addEventListener('fetch', (event) => {
  const { request } = event;
  const url = request.url;
  
  // Skip non-GET requests
  if (request.method !== 'GET') {
    return;
  }
  
  // Skip chrome extensions and other non-http requests
  if (!url.startsWith('http')) {
    return;
  }
  
  const strategy = getStrategyForUrl(url);
  
  switch (strategy) {
    case CACHE_STRATEGIES.CACHE_FIRST:
      event.respondWith(cacheFirst(request));
      break;
    case CACHE_STRATEGIES.NETWORK_FIRST:
      event.respondWith(networkFirst(request));
      break;
    case CACHE_STRATEGIES.STALE_WHILE_REVALIDATE:
      event.respondWith(staleWhileRevalidate(request));
      break;
    case CACHE_STRATEGIES.NETWORK_ONLY:
      event.respondWith(networkOnly(request));
      break;
    default:
      event.respondWith(networkFirst(request));
  }
});

/**
 * Cache First strategy: Try cache first, fallback to network
 */
async function cacheFirst(request) {
  try {
    const cached = await caches.match(request);
    if (cached) {
      console.log('[Service Worker] Cache hit:', request.url);
      return cached;
    }
    
    const response = await fetch(request);
    if (!response || response.status !== 200 || response.type === 'error') {
      return response;
    }
    
    const cache = await caches.open(CACHE_NAME);
    cache.put(request, response.clone());
    
    return response;
  } catch (error) {
    console.error('[Service Worker] Cache first error:', error);
    const cached = await caches.match(request);
    if (cached) return cached;
    
    return new Response('Offline - Resource not available', {
      status: 503,
      statusText: 'Service Unavailable'
    });
  }
}

/**
 * Network First strategy: Try network first, fallback to cache
 */
async function networkFirst(request) {
  try {
    const response = await fetch(request);
    
    if (!response || response.status !== 200) {
      const cached = await caches.match(request);
      return cached || response;
    }
    
    // Cache successful responses
    const cache = await caches.open(CACHE_NAME);
    cache.put(request, response.clone());
    
    return response;
  } catch (error) {
    console.log('[Service Worker] Network request failed, using cache:', request.url);
    const cached = await caches.match(request);
    
    if (cached) {
      return cached;
    }
    
    // Return offline page for HTML requests
    if (request.headers.get('accept').includes('text/html')) {
      return caches.match('/offline.html')
        .then(response => response || new Response('Offline', { status: 503 }));
    }
    
    return new Response('Offline - Resource not available', {
      status: 503,
      statusText: 'Service Unavailable'
    });
  }
}

/**
 * Stale While Revalidate strategy: Return cache immediately, update in background
 */
async function staleWhileRevalidate(request) {
  const cached = await caches.match(request);
  
  const fetchPromise = fetch(request).then((response) => {
    if (!response || response.status !== 200) {
      return response;
    }
    
    const cache = caches.open(CACHE_NAME);
    cache.then(c => c.put(request, response.clone()));
    
    return response;
  }).catch(() => cached);
  
  return cached || fetchPromise;
}

/**
 * Network Only strategy: Always use network
 */
async function networkOnly(request) {
  try {
    return await fetch(request);
  } catch (error) {
    return new Response('Offline - Network request failed', {
      status: 503,
      statusText: 'Service Unavailable'
    });
  }
}

/**
 * Handle messages from clients
 */
self.addEventListener('message', (event) => {
  if (event.data && event.data.type === 'SKIP_WAITING') {
    self.skipWaiting();
  }
  
  if (event.data && event.data.type === 'CLEAR_CACHE') {
    caches.delete(CACHE_NAME).then(() => {
      event.ports[0].postMessage({ success: true });
    });
  }
});

/**
 * Handle background sync for offline actions
 */
self.addEventListener('sync', (event) => {
  console.log('[Service Worker] Background sync event:', event.tag);
  
  if (event.tag === 'sync-transfers') {
    event.waitUntil(syncTransfers());
  }
  
  if (event.tag === 'sync-payments') {
    event.waitUntil(syncPayments());
  }
});

/**
 * Sync pending transfers
 */
async function syncTransfers() {
  try {
    const response = await fetch('/api/sync/transfers', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      }
    });
    
    if (!response.ok) {
      throw new Error('Sync failed');
    }
    
    return response.json();
  } catch (error) {
    console.error('[Service Worker] Transfer sync failed:', error);
    throw error;
  }
}

/**
 * Sync pending payments
 */
async function syncPayments() {
  try {
    const response = await fetch('/api/sync/payments', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json'
      }
    });
    
    if (!response.ok) {
      throw new Error('Sync failed');
    }
    
    return response.json();
  } catch (error) {
    console.error('[Service Worker] Payment sync failed:', error);
    throw error;
  }
}
