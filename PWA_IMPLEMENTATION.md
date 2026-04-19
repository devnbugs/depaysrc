# OneTera Progressive Web App (PWA) Implementation

## Overview

OneTera is now a fully functional Progressive Web App (PWA) that can be installed on any device (iOS, Android, Desktop) and provides offline functionality, fast performance, and an app-like experience.

## What's Included

### 1. Service Worker (`/public/service-worker.js`)
- **Offline Functionality**: App continues working when internet is unavailable
- **Intelligent Caching**: Different caching strategies for different resource types:
  - **CACHE_FIRST**: Static assets (CSS, JS, images, fonts)
  - **NETWORK_FIRST**: HTML pages and API calls (always fetch latest, fallback to cache)
  - **STALE_WHILE_REVALIDATE**: Content that can be updated in background
- **Background Sync**: Sync pending transactions and payments when connection restored
- **Automatic Updates**: Service worker updates automatically

### 2. Web App Manifest (`/public/manifest.json`)
- App metadata and configuration
- App icons for various devices and sizes
- App shortcuts for quick actions (Dashboard, Send Money, Pay Bills)
- Display mode: Standalone (full screen app experience)
- Theme and background colors

### 3. PWA Meta Tags (Added to all layouts)
- iOS web app capabilities
- Apple touch icons
- Theme color for app bars
- Mobile web app configurations
- Color scheme support (light/dark)

### 4. PWA Manager (`/resources/js/pwa-manager.js`)
- Service worker registration and lifecycle management
- Install prompt handling
- Update notifications
- Offline/online state management
- Performance monitoring
- Notification permission handling
- Cache management utilities

### 5. Offline Page (`/public/offline.html`)
- Beautiful, responsive offline page
- Shows connectivity status
- Allows users to retry or go home
- Responsive design for all devices

## Features

### ✅ Installation
The app can be installed on:
- **Android**: Via Chrome "Add to Home Screen" or install prompt
- **iOS**: Via Safari "Add to Home Screen" (iOS 15.4+)
- **Desktop**: Chrome, Edge, Firefox can install as desktop app

### ✅ Offline Support
- View previously cached pages while offline
- Access cached transactions and data
- See offline status indicator
- Automatic sync when back online

### ✅ Performance
- **Fast Loading**: Caching strategy minimizes network requests
- **Lazy Loading**: Images load only when visible
- **Core Web Vitals Monitoring**: Tracks LCP, CLS, FID
- **Optimized Assets**: CSS and JS are minified and bundled

### ✅ Native App Experience
- Full screen mode without browser UI
- App icon on home screen
- Status bar integration
- Splash screen support
- Customizable theme colors

### ✅ Background Sync
- Pending transfers sync when online
- Pending payments sync when online
- Automatic retry on connection

### ✅ Notifications
- Request notification permission
- Send push notifications
- Badging support

### ✅ Updates
- Automatic service worker updates every minute
- User notification when updates available
- One-click update installation

## How to Install the App

### On Android
1. Open the app in Chrome
2. Tap menu (⋮) → "Install app" or wait for install prompt
3. Tap "Install"
4. App will appear on home screen

### On iOS (Safari)
1. Open the app in Safari
2. Tap Share icon (↗️)
3. Scroll and tap "Add to Home Screen"
4. Enter app name and tap "Add"
5. App will appear on home screen

### On Desktop (Chrome/Edge)
1. Open the app in Chrome or Edge
2. Click the install icon in the address bar
3. Click "Install"
4. App will appear in your applications

## API Usage

The PWA features are accessible through the `window.PWA` object:

```javascript
// Get PWA status
const status = window.PWA.getStatus();
// Returns: { isInstalled, isOnline, serviceWorkerRegistered, deferredPromptAvailable }

// Trigger installation programmatically
window.PWA.install();

// Clear all cache
window.PWA.clearCache();

// Get cache size information
const cacheInfo = await window.PWA.getCacheSize();
// Returns: { usage, quota, percentage }

// Request notification permission
const granted = await window.PWA.requestNotification();

// Send a notification
window.PWA.sendNotification('Title', {
    body: 'Notification message',
    icon: '/path/to/icon.png'
});
```

## Event Listeners

Listen for PWA events:

```javascript
// PWA installed
document.addEventListener('pwa:installed', () => {
    console.log('App installed!');
});

// PWA install prompt available
document.addEventListener('pwa:prompt-available', (e) => {
    console.log('Installation prompt available');
});

// Online/offline status changed
document.addEventListener('pwa:online', () => {
    console.log('Back online');
});

document.addEventListener('pwa:offline', () => {
    console.log('Gone offline');
});
```

## Configuration

PWA settings are in `/config/pwa.php`:

```php
return [
    'name' => 'OneTera',
    'theme_color' => '#3b82f6',
    'cache_strategies' => [
        'static_assets' => 'CACHE_FIRST',
        'html_pages' => 'NETWORK_FIRST',
        'api_calls' => 'NETWORK_FIRST',
    ],
    // ... more configuration
];
```

## Caching Strategies Explained

### CACHE_FIRST
- Check cache first
- If not found, fetch from network
- Cache successful responses
- **Best for**: Static assets that rarely change

### NETWORK_FIRST
- Always try network first
- If network fails, use cached version
- Cache successful responses
- **Best for**: Dynamic content, API calls

### STALE_WHILE_REVALIDATE
- Return cached version immediately
- Fetch from network in background
- Update cache if new version available
- **Best for**: Content that can be slightly outdated

## Performance Optimization Tips

1. **Images**:
   - Use `data-src` attribute for lazy loading
   - Provide multiple image sizes using `srcset`
   - Use WebP format when possible

2. **CSS/JS**:
   - Already minified and bundled by Vite
   - Critical CSS is inlined for faster FCP
   - Defer non-critical JavaScript

3. **Caching**:
   - Service worker caches assets automatically
   - Use browser cache headers appropriately
   - Clear old caches when deploying updates

4. **Network**:
   - Minimize API requests
   - Batch requests when possible
   - Use compression (gzip/brotli)

## Monitoring and Debugging

### Check PWA Status
```javascript
console.log(window.pwaManager.getStatus());
```

### View Service Worker
1. Open DevTools (F12)
2. Go to Application → Service Workers
3. See registered service worker status

### View Cache Storage
1. Open DevTools (F12)
2. Go to Application → Cache Storage
3. Inspect cached resources

### Clear Cache and Data
```javascript
// Clear all caches
await window.PWA.clearCache();

// Clear localStorage
localStorage.clear();

// Unregister service worker (use with caution)
navigator.serviceWorker.getRegistrations().then(registrations => {
    registrations.forEach(reg => reg.unregister());
});
```

## Browser Support

| Browser | Support | Installation |
|---------|---------|------------------|
| Chrome/Chromium | ✅ Full | Address bar icon |
| Firefox | ✅ Full | Menu option |
| Edge | ✅ Full | Address bar icon |
| Safari (iOS 15.4+) | ✅ Limited* | "Add to Home Screen" |
| Safari (macOS) | ✅ Full | Menu option |

*iOS Safari has some limitations (no background sync, limited notifications)

## Troubleshooting

### Service Worker Not Updating
- Restart the app
- Clear cache: `await window.PWA.clearCache()`
- Check DevTools → Application → Service Workers

### Installation Not Working
- Ensure HTTPS is enabled
- Check if manifest.json is valid
- Must have service worker registered
- At least 2 screens recommended

### Offline Features Not Working
- Check if offline.html exists at `/public/offline.html`
- Verify service worker is registered
- Check DevTools console for errors

### Icons Not Showing
- Verify icon files exist at `/public/assets/icons/`
- Icons should be at least 192x192 (512x512 preferred)
- Format should be PNG

## Security Considerations

1. **HTTPS Required**: PWA only works over HTTPS (except localhost)
2. **CSP Headers**: Ensure Content Security Policy allows service worker
3. **Credentials**: API calls with credentials use `{credentials: 'include'}`
4. **User Data**: Cache only non-sensitive data locally

## File Structure

```
public/
├── service-worker.js          # Service worker implementation
├── offline.html               # Offline fallback page
├── manifest.json              # Web app manifest
└── assets/icons/              # App icons
    ├── icon-192x192.png
    ├── icon-512x512.png
    ├── maskable-icon-192x192.png
    └── maskable-icon-512x512.png

resources/js/
└── pwa-manager.js             # PWA management class

resources/views/user/layouts/
├── frontend.blade.php         # Frontend layout with PWA meta tags
├── auth.blade.php            # Auth layout with PWA meta tags
└── dashboard.blade.php       # Dashboard layout with PWA meta tags

config/
└── pwa.php                    # PWA configuration

app.js
└── Includes PWA Manager initialization
```

## Next Steps

1. **Add Icons**: Place icon files in `/public/assets/icons/`
   - icon-192x192.png
   - icon-512x512.png
   - maskable-icon-192x192.png
   - maskable-icon-512x512.png

2. **Test Installation**: Test on Android, iOS, and desktop

3. **Monitor Performance**: Use DevTools to monitor Core Web Vitals

4. **Gather Feedback**: Monitor user feedback on app experience

5. **Optimize**: Based on usage patterns, adjust caching strategies

## Additional Resources

- [MDN: Progressive Web Apps](https://developer.mozilla.org/en-US/docs/Web/Progressive_web_apps)
- [Web.dev: PWA Basics](https://web.dev/progressive-web-apps/)
- [Manifest Specification](https://www.w3.org/TR/appmanifest/)
- [Service Workers API](https://developer.mozilla.org/en-US/docs/Web/API/Service_Worker_API)

## Support

For issues or questions about PWA implementation, refer to:
- Browser DevTools (F12)
- Service Worker registration logs
- Application cache inspection
- Network tab for request/response debugging

---

**Last Updated**: April 19, 2026
**Version**: 1.0
