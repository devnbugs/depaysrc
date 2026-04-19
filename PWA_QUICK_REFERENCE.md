# PWA Quick Reference for Developers

## Command Reference

### Check Service Worker Status
```javascript
navigator.serviceWorker.getRegistrations().then(regs => {
    regs.forEach(reg => console.log('SW registered:', reg));
});
```

### Get PWA Status
```javascript
console.log(window.PWA.getStatus());
// Output: { isInstalled, isOnline, serviceWorkerRegistered, deferredPromptAvailable }
```

### Force Install Prompt
```javascript
window.PWA.install();
```

### Clear All Caches
```javascript
await window.PWA.clearCache();
console.log('Caches cleared!');
```

### Get Cache Size Info
```javascript
const info = await window.PWA.getCacheSize();
console.log(`Using ${info.percentage.toFixed(2)}% of storage`);
```

### Request Notifications
```javascript
const granted = await window.PWA.requestNotification();
if (granted) {
    window.PWA.sendNotification('Hello!', {
        body: 'This is a test notification',
        icon: '/assets/icons/icon-192x192.png'
    });
}
```

### Listen for PWA Events
```javascript
document.addEventListener('pwa:installed', () => {
    console.log('PWA installed!');
});

document.addEventListener('pwa:online', () => {
    console.log('Back online!');
});

document.addEventListener('pwa:offline', () => {
    console.log('Gone offline!');
});

document.addEventListener('pwa:prompt-available', () => {
    console.log('Install prompt available');
});
```

## DevTools Inspection

### View Service Worker
1. Open DevTools (F12)
2. Go to **Application → Service Workers**
3. See registration status and scope

### View Cached Assets
1. Open DevTools (F12)
2. Go to **Application → Cache Storage**
3. Expand "onetera-pwa-v1"
4. See all cached resources

### View Manifest
1. Open DevTools (F12)
2. Go to **Application → Manifest**
3. See all app metadata
4. Check for any errors

### Check Network Activity
1. Open DevTools (F12)
2. Go to **Network** tab
3. Look for service worker in requests
4. Verify caching strategy working

## Common Development Tasks

### Add a New API Endpoint to Cache Strategy
```javascript
// In service-worker.js
const RESOURCE_CACHE_STRATEGY = {
    '/api/new-endpoint/': CACHE_STRATEGIES.NETWORK_FIRST,
    // ... existing entries
};
```

### Change Cache Strategy for a Resource
```javascript
// In service-worker.js
RESOURCE_CACHE_STRATEGY = {
    '/api/frequently-changing-data/': CACHE_STRATEGIES.CACHE_FIRST, // Change from NETWORK_FIRST
    // ...
};
```

### Update Cache Name (Forces Cache Refresh)
```javascript
// In service-worker.js
const CACHE_VERSION = 'v2'; // Change from 'v1'
const CACHE_NAME = `${CACHE_PREFIX}-${CACHE_VERSION}`;
```

### Add Custom Offline Handling
```javascript
// In service-worker.js
async function networkFirst(request) {
    try {
        return await fetch(request);
    } catch (error) {
        // Custom offline handling
        if (request.headers.get('accept').includes('application/json')) {
            return new Response(JSON.stringify({ offline: true }), {
                headers: { 'Content-Type': 'application/json' }
            });
        }
        return caches.match(request);
    }
}
```

### Test Offline Mode
```javascript
// In browser console
// Simulate offline (DevTools → Network → Offline checkbox)
// Test your app functionality
// Check Service Worker caching
```

## File Modifications

### Add PWA Meta Tags to New Layout
```blade
<!-- Add to <head> in your layout -->
<meta name="theme-color" content="#3b82f6">
<meta name="application-name" content="OneTera">
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-title" content="OneTera">
<meta name="mobile-web-app-capable" content="yes">

<link rel="apple-touch-icon" href="/assets/icons/icon-192x192.png">
<link rel="manifest" href="/manifest.json">
```

### Update Manifest Metadata
```json
{
  "name": "New App Name",
  "short_name": "App",
  "description": "New description",
  "theme_color": "#new-color",
  "background_color": "#new-bg"
}
```

### Modify PWA Configuration
```php
// In config/pwa.php
return [
    'service_worker' => [
        'update_interval' => 30000, // 30 seconds instead of 60
    ],
    'cache_strategies' => [
        'api_calls' => CACHE_STRATEGIES.CACHE_FIRST, // Change strategy
    ],
];
```

## Testing Checklist

### Before Deployment
- [ ] Service worker registered
- [ ] Manifest valid (no errors in DevTools)
- [ ] Icons exist and are correct size
- [ ] HTTPS enabled
- [ ] Offline page accessible
- [ ] Offline functionality works
- [ ] Installation works on real device
- [ ] App shortcuts visible

### Performance Testing
- [ ] LCP < 2.5s
- [ ] FID < 100ms
- [ ] CLS < 0.1
- [ ] Cache hit rate > 70%

### Device Testing
- [ ] Android Chrome installation
- [ ] iOS Safari installation
- [ ] Desktop Chrome/Edge installation
- [ ] Offline mode functionality
- [ ] Notification permission
- [ ] App shortcuts working

## Environment Variables

Not needed for basic PWA - all configuration is in:
- `/public/manifest.json`
- `/config/pwa.php`
- `/public/service-worker.js`

## Debugging Tips

### Service Worker Not Registering
```javascript
navigator.serviceWorker.register('/service-worker.js')
    .then(reg => console.log('Registered:', reg))
    .catch(err => console.error('SW registration failed:', err));
```

### Cache Not Working
1. Check if HTTPS enabled
2. Check if service worker is active
3. Check DevTools → Application → Service Workers
4. Check DevTools → Application → Cache Storage

### Manifest Not Loading
```javascript
fetch('/manifest.json')
    .then(r => r.json())
    .then(data => console.log('Manifest:', data))
    .catch(err => console.error('Manifest error:', err));
```

### Check Cache Size
```javascript
navigator.storage.estimate().then(estimate => {
    console.log(`Using ${estimate.usage} bytes of ${estimate.quota} available`);
});
```

## Useful URLs

### Local Testing
- Dev: `http://localhost:8000`
- Production: `https://onetera.ng`

### Documentation
- MDN PWA: https://developer.mozilla.org/en-US/docs/Web/Progressive_web_apps
- Service Workers: https://developer.mozilla.org/en-US/docs/Web/API/Service_Worker_API
- Web App Manifest: https://www.w3.org/TR/appmanifest/

### Tools
- Manifest Validator: https://manifest-validator.appspot.com/
- Lighthouse: Built-in to Chrome DevTools
- PWA Builder: https://www.pwabuilder.com/

## Emergency Procedures

### Remove PWA Completely
```javascript
// In console
navigator.serviceWorker.getRegistrations().then(regs => {
    regs.forEach(reg => reg.unregister());
});
caches.keys().then(names => {
    names.forEach(name => caches.delete(name));
});
localStorage.clear();
```

### Force Service Worker Update
```javascript
// In console
navigator.serviceWorker.getRegistrations().then(regs => {
    regs.forEach(reg => reg.update());
});
```

### Reset Cache Completely
```javascript
// In console
caches.keys().then(async (names) => {
    await Promise.all(names.map(n => caches.delete(n)));
    location.reload();
});
```

## Performance Optimization Commands

### Monitor Cache Performance
```javascript
// Check cache hit rate
caches.keys().then(names => {
    console.log('Active caches:', names);
    names.forEach(async name => {
        const cache = await caches.open(name);
        const keys = await cache.keys();
        console.log(`${name}: ${keys.length} items`);
    });
});
```

### Monitor Core Web Vitals
```javascript
// View real-time metrics
const observer = new PerformanceObserver((list) => {
    for (const entry of list.getEntries()) {
        console.log('Performance:', entry);
    }
});
observer.observe({ entryTypes: ['largest-contentful-paint', 'layout-shift'] });
```

## Git Commands

### Commit PWA Changes
```bash
git add public/service-worker.js public/manifest.json public/offline.html
git add resources/js/pwa-manager.js config/pwa.php
git add resources/views/user/layouts/
git add public/.htaccess
git commit -m "feat: Add PWA implementation"
```

### Deploy PWA
```bash
git push origin main  # or your main branch
```

---

## Quick Links to Documentation

- Full Implementation Guide: `PWA_IMPLEMENTATION.md`
- User Quick Start: `QUICK_START_PWA.md`
- Icon Setup: `ICONS_SETUP_GUIDE.md`
- Deployment Guide: `PWA_DEPLOYMENT_GUIDE.md`
- Complete Summary: `PWA_COMPLETE_SUMMARY.md`

---

**Last Updated**: April 19, 2026
