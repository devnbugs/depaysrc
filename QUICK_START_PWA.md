# OneTera PWA - Quick Start Guide

## What's New?

Your app is now a **Progressive Web App (PWA)** and can be installed on any device like a native app!

## Installation Instructions

### 🤖 On Android (Chrome)
1. Open the app in Chrome on your Android phone
2. Look for the **"Install"** button/prompt at the bottom of the screen (or in the menu)
3. Tap **"Install"** to add to home screen
4. The app will appear with its icon on your home screen

### 🍎 On iPhone (Safari)
1. Open the app in Safari on your iPhone
2. Tap the **Share button** (↗️ arrow) at the bottom
3. Scroll down and tap **"Add to Home Screen"**
4. Name your shortcut and tap **"Add"**
5. The app will appear on your home screen

### 🖥️ On Desktop (Chrome/Edge)
1. Open the app in Chrome or Edge
2. Click the **Install button** in the address bar (looks like a computer)
3. Click **"Install"**
4. The app will open in a window and appear in your applications

## Key Features

### ✅ Works Offline
- Browse previously loaded pages even without internet
- See all cached data and transactions
- Automatic sync when back online

### ⚡ Super Fast
- Loads almost instantly from cache
- Optimized for mobile devices
- Uses smart caching strategies

### 🎯 Native App Feel
- Full screen, no browser UI
- Home screen icon
- Works just like a native app
- Splash screen on launch

### 🔔 Notifications
- Get notified about important updates
- Real-time transaction alerts
- Remind about pending actions

### 📱 Auto-Updates
- Automatically checks for updates
- Notifies when new version available
- One-click update installation

## Quick Actions (Shortcuts)

Once installed, long-press the app icon to see quick actions:
- **Dashboard** - Quick access to your account
- **Send Money** - Send money to other users
- **Pay Bills** - Quick bill payment

## FAQ

### Q: Is the app safe?
**A:** Yes! PWA uses HTTPS encryption and follows security best practices.

### Q: Will it work offline?
**A:** Yes! Cached pages work offline. When online, it automatically syncs.

### Q: Can I uninstall it?
**A:** Yes, just like any other app. Long-press → Uninstall.

### Q: How much space does it use?
**A:** Very little! Usually 5-10MB depending on cached content.

### Q: Will it drain my battery?
**A:** No more than the regular website. Usually better due to caching.

### Q: Can I use it on multiple devices?
**A:** Yes! Install on as many devices as you want.

## What to Do If Installation Doesn't Work

1. **Clear Cache**: Settings → Apps → Clear cache
2. **Check HTTPS**: Ensure you're using HTTPS (not HTTP)
3. **Update Browser**: Make sure Chrome/Safari is up to date
4. **Retry**: Close and reopen the app
5. **Reinstall**: Uninstall and reinstall the PWA

## What to Do If Something Breaks

1. **Clear All Data**:
   - Open browser console (F12)
   - Run: `localStorage.clear()`
   - Run: `await window.PWA.clearCache()`

2. **Unregister Service Worker**:
   ```javascript
   navigator.serviceWorker.getRegistrations().then(regs => {
     regs.forEach(reg => reg.unregister());
   });
   ```

3. **Uninstall and Reinstall** the PWA

## Settings & Preferences

### Cache Management
The app automatically manages cache. To manually clear:
1. Open DevTools (F12)
2. Go to Application → Cache Storage
3. Delete "onetera-pwa-v1"

### Notifications
- **First time**: You'll see a prompt to allow notifications
- **Settings**: You can change notification settings in browser
- **Turn on/off**: Browser settings → Notifications

### Offline Mode
- When offline, you'll see an offline indicator
- You can still view cached pages
- Changes will sync when back online

## Performance Tips

1. **Images**: Load faster due to caching
2. **Pages**: Previously visited pages load instantly
3. **Data**: Faster than regular web version
4. **Battery**: Better battery life on mobile

## Troubleshooting Checklist

| Issue | Solution |
|-------|----------|
| Can't install | Update browser, check HTTPS, clear cache |
| Icon not showing | Wait a moment, refresh, reinstall |
| Offline not working | Clear cache and service worker |
| App crashes | Clear all data and reinstall |
| Updates not installing | Close app, wait 1 minute, reopen |
| Notifications not working | Check browser notification permissions |

## Security Notes

- ✅ All data encrypted with HTTPS
- ✅ Service worker only runs in HTTPS
- ✅ No tracking or spyware
- ✅ Your data stays your data
- ✅ Complies with privacy policies

## Need Help?

1. **Check Console**: F12 → Console for error messages
2. **Check Manifest**: F12 → Application → Manifest
3. **Check Service Worker**: F12 → Application → Service Workers
4. **Contact Support**: Use in-app support feature

## What Happens When I Use It?

### First Time
1. Opens in full screen (no browser UI)
2. Downloads and caches resources
3. Gets ready for offline use

### Subsequent Visits
1. Loads from cache (super fast!)
2. Checks for updates in background
3. Updates content if needed

### When Offline
1. Shows cached pages
2. Shows offline indicator
3. Lets you access cached data
4. Auto-syncs when back online

## Advanced Features (For Developers)

```javascript
// Check PWA status
console.log(window.PWA.getStatus());

// Get cache size
const size = await window.PWA.getCacheSize();
console.log(size);

// Clear cache
await window.PWA.clearCache();

// Request notifications
await window.PWA.requestNotification();

// Send notification
window.PWA.sendNotification('Hello!', { body: 'Message' });
```

## What's Cached?

- ✅ HTML pages (from cache)
- ✅ CSS and JavaScript files (long-term cache)
- ✅ Images (long-term cache)
- ✅ Fonts (long-term cache)
- ❌ API responses (network first)
- ❌ Sensitive data (not cached)

## Browser Compatibility

| Browser | Status | Installation |
|---------|--------|---------------|
| Chrome | ✅ Full | Auto prompt |
| Firefox | ✅ Full | Menu option |
| Edge | ✅ Full | Auto prompt |
| Safari (iOS 15.4+) | ✅ Limited* | Add to Home |
| Safari (Mac) | ✅ Full | Menu option |

*Limited notifications and background sync on iOS

## Important Notes

- 📌 Keep your browser updated
- 📌 HTTPS is required (for security)
- 📌 Disable ad-blockers for best experience
- 📌 Clear cache only when having issues
- 📌 Keep app permissions enabled

## Next Steps

1. ✅ Install the app on your device
2. ✅ Add it to your home screen
3. ✅ Enable notifications (optional)
4. ✅ Use it like a native app
5. ✅ Share with friends!

---

**Enjoy your new OneTera PWA! 🚀**

For more detailed information, see **PWA_IMPLEMENTATION.md** and **ICONS_SETUP_GUIDE.md**
