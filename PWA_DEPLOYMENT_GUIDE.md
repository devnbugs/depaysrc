# PWA Deployment & Performance Optimization Guide

## Pre-Deployment Checklist

### 1. Security & HTTPS
- [ ] HTTPS enabled on production server
- [ ] Valid SSL certificate installed
- [ ] Automatic HTTP to HTTPS redirect configured
- [ ] Security headers properly set
- [ ] CSP (Content Security Policy) configured

### 2. Service Worker
- [ ] Service worker file is valid
- [ ] Service worker is accessible at `/service-worker.js`
- [ ] Service worker `scope` is correct
- [ ] Cache versioning strategy implemented
- [ ] Tested offline functionality

### 3. Manifest
- [ ] `manifest.json` is valid and complete
- [ ] All icons exist and are correct size
- [ ] Manifest is accessible at `/manifest.json`
- [ ] All required fields present
- [ ] Theme colors set correctly

### 4. Icons
- [ ] 192×192 PNG icon created
- [ ] 512×512 PNG icon created
- [ ] 192×192 maskable icon created
- [ ] 512×512 maskable icon created
- [ ] Badge 72×72 icon created
- [ ] All icons optimized for web

### 5. Meta Tags
- [ ] PWA meta tags in all layouts
- [ ] Apple web app meta tags present
- [ ] Theme color set
- [ ] Viewport meta tag correct
- [ ] Open Graph tags for social sharing

### 6. Performance
- [ ] CSS minified and bundled
- [ ] JavaScript minified and bundled
- [ ] Images optimized for web
- [ ] Lazy loading implemented
- [ ] Gzip compression enabled
- [ ] Browser cache headers set
- [ ] Service worker cache headers correct

### 7. Testing
- [ ] Tested on Chrome/Chromium
- [ ] Tested on Firefox
- [ ] Tested on Safari (Mac)
- [ ] Tested on iOS Safari (if applicable)
- [ ] Tested on Android
- [ ] Installation tested on real devices
- [ ] Offline functionality tested
- [ ] Performance tested with DevTools

## Production Deployment

### Step 1: Verify Files Exist

```bash
# Check service worker
ls -la public/service-worker.js

# Check manifest
ls -la public/manifest.json

# Check offline page
ls -la public/offline.html

# Check icons directory
ls -la public/assets/icons/
```

### Step 2: Verify Configuration

```bash
# Check .htaccess is valid
cat public/.htaccess | head -20

# Check manifest is valid JSON
cat public/manifest.json | python3 -m json.tool

# Check service worker JavaScript
tail -10 public/service-worker.js
```

### Step 3: Deploy to Server

```bash
# Using Git
git add public/service-worker.js public/manifest.json public/offline.html
git add public/assets/icons/
git add public/.htaccess
git add resources/js/pwa-manager.js
git add config/pwa.php
git commit -m "Add PWA implementation"
git push production main

# Using FTP/SFTP
sftp user@server
put public/service-worker.js
put public/manifest.json
put public/offline.html
put -r public/assets/icons/
exit
```

### Step 4: Clear Cache

```bash
# Clear browser cache on server
# This helps service worker update faster

# Or via PHP (if you have artisan access)
php artisan cache:clear
php artisan view:clear
```

### Step 5: Verify Deployment

1. Visit your site in browser
2. Open DevTools (F12)
3. Go to Application → Manifest
4. Verify manifest loads without errors
5. Go to Application → Service Workers
6. Verify service worker is registered
7. Check Cache Storage for cached assets

## Performance Optimization

### 1. Cache Strategy Optimization

```javascript
// For frequently changing API data
const RESOURCE_CACHE_STRATEGY = {
    '/api/user/': CACHE_STRATEGIES.NETWORK_FIRST,
    '/api/transactions/': CACHE_STRATEGIES.NETWORK_FIRST,
    '/api/accounts/': CACHE_STRATEGIES.NETWORK_FIRST,
};

// For static assets
'/build/': CACHE_STRATEGIES.CACHE_FIRST,
'/assets/': CACHE_STRATEGIES.CACHE_FIRST,
```

### 2. Image Optimization

```html
<!-- Use srcset for responsive images -->
<img src="image-small.jpg"
     srcset="image-small.jpg 480w,
             image-medium.jpg 768w,
             image-large.jpg 1024w"
     sizes="(max-width: 480px) 100vw,
            (max-width: 768px) 90vw,
            80vw"
     loading="lazy"
     alt="Description">

<!-- Use WebP with fallback -->
<picture>
    <source srcset="image.webp" type="image/webp">
    <img src="image.jpg" alt="Description" loading="lazy">
</picture>
```

### 3. Critical CSS

Extract critical CSS for above-the-fold content:

```php
// In your layout template
<style>
    /* Critical CSS here - essential styles for initial render */
    @import url('css/critical.css');
</style>

<link rel="preload" href="{{ mix('css/app.css') }}" as="style">
<link rel="stylesheet" href="{{ mix('css/app.css') }}">
```

### 4. JavaScript Optimization

```html
<!-- Defer non-critical JavaScript -->
<script src="app.js" defer></script>

<!-- Or use async for non-blocking scripts -->
<script src="analytics.js" async></script>

<!-- Module scripts -->
<script type="module" src="app.js"></script>
```

### 5. Asset Preloading

```html
<!-- Preload critical fonts -->
<link rel="preload" href="/fonts/inter.woff2" as="font" type="font/woff2" crossorigin>

<!-- Prefetch DNS for external services -->
<link rel="dns-prefetch" href="//cdn.jsdelivr.net">
<link rel="dns-prefetch" href="//www.google-analytics.com">

<!-- Prefetch URLs likely to be visited -->
<link rel="prefetch" href="/dashboard">
<link rel="prefetch" href="/transfer">
```

### 6. Minification & Compression

```bash
# Install minifiers
npm install --save-dev cssnano terser

# Build script in package.json
"build": "vite build && npm run minify",
"minify": "terser public/service-worker.js -o public/service-worker.min.js"
```

### 7. Cache Busting

```php
// In your view
<script src="{{ mix('js/app.js') }}"></script>

// Vite automatically handles cache busting
// Files are hashed: app.12345.js
```

## Core Web Vitals Optimization

### LCP (Largest Contentful Paint) - Target: < 2.5s

```html
<!-- Preload hero image -->
<link rel="preload" as="image" href="hero.jpg" imagesrcset="hero-480.jpg 480w, hero-1200.jpg 1200w">

<!-- Make sure hero image is render-critical -->
<img src="hero.jpg" width="1200" height="600" alt="Hero">
```

### FID/INP (Interaction to Next Paint) - Target: < 100ms

```javascript
// Break up long JavaScript tasks
async function processLargeDataset(data) {
    const chunk = data.splice(0, 100);
    await processChunk(chunk);
    if (data.length > 0) {
        setTimeout(() => processLargeDataset(data), 0);
    }
}

// Use requestIdleCallback for non-urgent work
if ('requestIdleCallback' in window) {
    requestIdleCallback(() => {
        // Non-urgent work here
    });
}
```

### CLS (Cumulative Layout Shift) - Target: < 0.1

```css
/* Set explicit dimensions for dynamic content */
.image-container {
    aspect-ratio: 16 / 9;
}

/* Or use explicit width/height */
img {
    width: 100%;
    height: auto;
    display: block;
}

/* Reserve space for ads/embeds */
.ad-space {
    min-height: 250px; /* Reserve space */
}
```

## Monitoring & Analytics

### 1. Set Up Performance Monitoring

```javascript
// In your pwa-manager.js
window.addEventListener('load', () => {
    // Log Core Web Vitals
    const perfData = performance.getEntriesByType('navigation')[0];
    console.log('Page Load Time:', perfData.loadEventEnd - perfData.fetchStart);
    console.log('DOM Content Loaded:', perfData.domContentLoadedEventEnd - perfData.fetchStart);
});
```

### 2. Monitor Service Worker

```javascript
// Log SW registration status
navigator.serviceWorker.ready.then(registration => {
    console.log('SW ready:', registration);
    console.log('Controller:', navigator.serviceWorker.controller);
});
```

### 3. Track Cache Size

```javascript
// Monitor storage usage
async function checkStorage() {
    const estimate = await navigator.storage.estimate();
    const percentUsed = (estimate.usage / estimate.quota) * 100;
    console.log(`Storage used: ${percentUsed.toFixed(2)}%`);
    
    if (percentUsed > 80) {
        console.warn('Storage near limit!');
    }
}
```

## Troubleshooting Performance

### Service Worker Taking Too Long to Load

```javascript
// In service-worker.js - reduce critical assets
const CRITICAL_ASSETS = [
    '/',
    '/manifest.json',
    '/offline.html'
    // Remove non-essential assets
];
```

### Cache Growing Too Large

```javascript
// Implement cache size limits
async function cleanupOldCache() {
    const cacheNames = await caches.keys();
    const cachesToKeep = ['onetera-pwa-v1'];
    
    await Promise.all(
        cacheNames.map(cacheName => {
            if (!cachesToKeep.includes(cacheName)) {
                return caches.delete(cacheName);
            }
        })
    );
}
```

### High LCP (Slow First Paint)

1. Reduce JavaScript execution time
2. Defer non-critical CSS
3. Optimize images (use WebP)
4. Minimize redirects
5. Use CDN for static assets

### Frequent 404s

1. Check service worker paths
2. Verify asset URLs are correct
3. Check offline.html path
4. Verify manifest.json path

## Production Maintenance

### Weekly Tasks
- [ ] Monitor error logs
- [ ] Check service worker updates
- [ ] Verify cache hit rates
- [ ] Check Core Web Vitals

### Monthly Tasks
- [ ] Review performance metrics
- [ ] Update dependencies
- [ ] Audit security headers
- [ ] Check for broken links

### Quarterly Tasks
- [ ] Full PWA audit
- [ ] Performance optimization review
- [ ] Update service worker strategy if needed
- [ ] Review user feedback

## Performance Benchmarks

### Target Metrics
- **FCP** (First Contentful Paint): < 1.8s
- **LCP** (Largest Contentful Paint): < 2.5s
- **FID** (First Input Delay): < 100ms
- **CLS** (Cumulative Layout Shift): < 0.1
- **TTFB** (Time to First Byte): < 600ms

### Testing Tools
- Google PageSpeed Insights: https://pagespeed.web.dev/
- WebPageTest: https://www.webpagetest.org/
- Lighthouse: Built-in to Chrome DevTools
- GTmetrix: https://gtmetrix.com/

## Optimization Checklist

- [ ] Service worker caching optimized
- [ ] Images optimized and responsive
- [ ] CSS minified and critical CSS extracted
- [ ] JavaScript minified and deferred
- [ ] Gzip compression enabled
- [ ] Cache headers configured
- [ ] Security headers set
- [ ] Core Web Vitals optimized
- [ ] Tested on low-end devices
- [ ] Tested on slow networks (3G)

## Common Issues & Solutions

| Issue | Solution |
|-------|----------|
| Service worker not updating | Clear cache, restart browser |
| App loads slowly | Check network tab, optimize images |
| Cache grows too large | Implement cache cleanup strategy |
| Icons missing | Verify file paths and dimensions |
| Install prompt not showing | Check HTTPS, service worker registration |
| Offline not working | Verify offline.html exists |

---

**Last Updated**: April 19, 2026
**Version**: 1.0
