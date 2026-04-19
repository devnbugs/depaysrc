/**
 * PWA Utilities for OneTera
 * Handles service worker registration, install prompts, and PWA features
 */

class PWAManager {
  constructor() {
    this.deferredPrompt = null;
    this.isInstalled = false;
    this.serviceWorkerRegistration = null;
    this.init();
  }

  /**
   * Initialize PWA features
   */
  init() {
    if ('serviceWorker' in navigator) {
      this.registerServiceWorker();
    }

    if ('beforeinstallprompt' in window) {
      this.setupInstallPrompt();
    }

    this.checkIfInstalled();
    this.setupUpdateListener();
    this.setupConnectivityListener();
    this.setupPerformanceOptimizations();
  }

  /**
   * Register service worker
   */
  async registerServiceWorker() {
    try {
      const registration = await navigator.serviceWorker.register('/service-worker.js', {
        scope: '/',
        updateViaCache: 'none'
      });

      this.serviceWorkerRegistration = registration;
      console.log('[PWA] Service Worker registered successfully', registration);

      // Check for updates periodically
      setInterval(() => {
        registration.update();
      }, 60000); // Check every minute

      return registration;
    } catch (error) {
      console.error('[PWA] Service Worker registration failed:', error);
    }
  }

  /**
   * Setup install prompt listener
   */
  setupInstallPrompt() {
    window.addEventListener('beforeinstallprompt', (e) => {
      e.preventDefault();
      this.deferredPrompt = e;
      this.showInstallPrompt();
    });

    window.addEventListener('appinstalled', () => {
      console.log('[PWA] App installed');
      this.isInstalled = true;
      this.deferredPrompt = null;
      this.hideInstallPrompt();
      this.dispatchEvent('pwa:installed');
    });
  }

  /**
   * Show install prompt to user
   */
  showInstallPrompt() {
    const promptElement = document.getElementById('pwa-install-prompt');
    
    if (!promptElement) {
      this.createInstallPrompt();
    } else {
      promptElement.style.display = 'block';
    }

    // Also dispatch event for custom handling
    this.dispatchEvent('pwa:prompt-available', { deferredPrompt: this.deferredPrompt });
  }

  /**
   * Hide install prompt
   */
  hideInstallPrompt() {
    const promptElement = document.getElementById('pwa-install-prompt');
    if (promptElement) {
      promptElement.style.display = 'none';
    }
  }

  /**
   * Create and insert install prompt element
   */
  createInstallPrompt() {
    const promptHTML = `
      <div id="pwa-install-prompt" class="pwa-install-prompt" style="display: none;">
        <div class="pwa-prompt-content">
          <div class="pwa-prompt-header">
            <h3>Install OneTera App</h3>
            <button class="pwa-prompt-close" aria-label="Close">×</button>
          </div>
          <p class="pwa-prompt-description">
            Install our app for quick access and offline support
          </p>
          <div class="pwa-prompt-actions">
            <button class="pwa-prompt-btn pwa-prompt-btn-primary" id="pwa-install-btn">
              Install
            </button>
            <button class="pwa-prompt-btn pwa-prompt-btn-secondary" id="pwa-later-btn">
              Later
            </button>
          </div>
        </div>
      </div>

      <style>
        .pwa-install-prompt {
          position: fixed;
          bottom: 20px;
          right: 20px;
          z-index: 999999;
          animation: slideUp 0.3s ease;
        }

        .pwa-prompt-content {
          background: white;
          border-radius: 12px;
          box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
          overflow: hidden;
          min-width: 300px;
          max-width: 400px;
        }

        .pwa-prompt-header {
          background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
          color: white;
          padding: 16px 20px;
          display: flex;
          justify-content: space-between;
          align-items: center;
        }

        .pwa-prompt-header h3 {
          margin: 0;
          font-size: 18px;
          font-weight: 600;
        }

        .pwa-prompt-close {
          background: none;
          border: none;
          color: white;
          font-size: 28px;
          cursor: pointer;
          padding: 0;
          width: 32px;
          height: 32px;
          display: flex;
          align-items: center;
          justify-content: center;
          transition: transform 0.2s;
        }

        .pwa-prompt-close:hover {
          transform: scale(1.1);
        }

        .pwa-prompt-description {
          padding: 16px 20px;
          margin: 0;
          color: #666;
          font-size: 14px;
          line-height: 1.5;
        }

        .pwa-prompt-actions {
          padding: 12px 20px 20px;
          display: flex;
          gap: 10px;
        }

        .pwa-prompt-btn {
          flex: 1;
          padding: 10px 16px;
          border: none;
          border-radius: 6px;
          font-size: 14px;
          font-weight: 600;
          cursor: pointer;
          transition: all 0.2s;
        }

        .pwa-prompt-btn-primary {
          background: #667eea;
          color: white;
        }

        .pwa-prompt-btn-primary:hover {
          background: #5568d3;
          transform: translateY(-2px);
        }

        .pwa-prompt-btn-secondary {
          background: #f0f0f0;
          color: #333;
        }

        .pwa-prompt-btn-secondary:hover {
          background: #e0e0e0;
        }

        @keyframes slideUp {
          from {
            transform: translateY(400px);
            opacity: 0;
          }
          to {
            transform: translateY(0);
            opacity: 1;
          }
        }

        @media (max-width: 480px) {
          .pwa-install-prompt {
            left: 10px;
            right: 10px;
            bottom: 10px;
          }

          .pwa-prompt-content {
            min-width: auto;
            max-width: none;
          }
        }

        @media (prefers-color-scheme: dark) {
          .pwa-prompt-content {
            background: #1a1a1a;
            color: #f0f0f0;
          }

          .pwa-prompt-description {
            color: #b0b0b0;
          }

          .pwa-prompt-btn-secondary {
            background: #333;
            color: #f0f0f0;
          }

          .pwa-prompt-btn-secondary:hover {
            background: #444;
          }
        }
      </style>
    `;

    const container = document.createElement('div');
    container.innerHTML = promptHTML;
    document.body.appendChild(container);

    // Setup event listeners
    document.getElementById('pwa-install-btn').addEventListener('click', () => this.installApp());
    document.getElementById('pwa-later-btn').addEventListener('click', () => this.hideInstallPrompt());
    document.querySelector('.pwa-prompt-close').addEventListener('click', () => this.hideInstallPrompt());
  }

  /**
   * Trigger app installation
   */
  async installApp() {
    if (!this.deferredPrompt) return;

    this.deferredPrompt.prompt();
    const { outcome } = await this.deferredPrompt.userChoice;
    
    console.log('[PWA] Install outcome:', outcome);
    this.deferredPrompt = null;
    this.hideInstallPrompt();
  }

  /**
   * Check if app is already installed
   */
  checkIfInstalled() {
    if (window.navigator.standalone === true) {
      this.isInstalled = true;
    }

    // Check via display mode
    if (window.matchMedia('(display-mode: standalone)').matches) {
      this.isInstalled = true;
    }

    if (this.isInstalled) {
      console.log('[PWA] App is installed');
      document.documentElement.classList.add('pwa-installed');
    }
  }

  /**
   * Listen for service worker updates
   */
  setupUpdateListener() {
    if (!this.serviceWorkerRegistration) return;

    this.serviceWorkerRegistration.addEventListener('updatefound', () => {
      const newWorker = this.serviceWorkerRegistration.installing;

      newWorker.addEventListener('statechange', () => {
        if (newWorker.state === 'installed' && navigator.serviceWorker.controller) {
          console.log('[PWA] New service worker ready for update');
          this.showUpdatePrompt();
        }
      });
    });
  }

  /**
   * Show update available prompt
   */
  showUpdatePrompt() {
    const updatePrompt = document.createElement('div');
    updatePrompt.className = 'pwa-update-prompt';
    updatePrompt.innerHTML = `
      <div class="pwa-update-content">
        <p>A new version of OneTera is available</p>
        <div class="pwa-update-actions">
          <button class="pwa-update-btn pwa-update-btn-primary" id="pwa-update-btn">Update Now</button>
          <button class="pwa-update-btn pwa-update-btn-secondary" id="pwa-update-close">Later</button>
        </div>
      </div>

      <style>
        .pwa-update-prompt {
          position: fixed;
          bottom: 20px;
          left: 50%;
          transform: translateX(-50%);
          z-index: 999998;
          animation: slideUp 0.3s ease;
        }

        .pwa-update-content {
          background: white;
          border-radius: 8px;
          padding: 16px 20px;
          box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
          max-width: 400px;
        }

        .pwa-update-content p {
          margin: 0 0 12px 0;
          font-size: 14px;
          color: #333;
          font-weight: 500;
        }

        .pwa-update-actions {
          display: flex;
          gap: 8px;
        }

        .pwa-update-btn {
          padding: 8px 16px;
          border: none;
          border-radius: 6px;
          font-size: 13px;
          font-weight: 600;
          cursor: pointer;
          transition: all 0.2s;
        }

        .pwa-update-btn-primary {
          background: #667eea;
          color: white;
          flex: 1;
        }

        .pwa-update-btn-primary:hover {
          background: #5568d3;
        }

        .pwa-update-btn-secondary {
          background: #f0f0f0;
          color: #333;
        }

        .pwa-update-btn-secondary:hover {
          background: #e0e0e0;
        }

        @media (prefers-color-scheme: dark) {
          .pwa-update-content {
            background: #1a1a1a;
            color: #f0f0f0;
          }

          .pwa-update-content p {
            color: #f0f0f0;
          }

          .pwa-update-btn-secondary {
            background: #333;
            color: #f0f0f0;
          }

          .pwa-update-btn-secondary:hover {
            background: #444;
          }
        }
      </style>
    `;

    document.body.appendChild(updatePrompt);

    document.getElementById('pwa-update-btn').addEventListener('click', () => {
      this.applyUpdate();
    });

    document.getElementById('pwa-update-close').addEventListener('click', () => {
      updatePrompt.remove();
    });
  }

  /**
   * Apply pending service worker update
   */
  applyUpdate() {
    if (!this.serviceWorkerRegistration || !this.serviceWorkerRegistration.waiting) return;

    this.serviceWorkerRegistration.waiting.postMessage({ type: 'SKIP_WAITING' });

    navigator.serviceWorker.addEventListener('controllerchange', () => {
      window.location.reload();
    });
  }

  /**
   * Setup connectivity change listener
   */
  setupConnectivityListener() {
    window.addEventListener('online', () => {
      console.log('[PWA] Back online');
      document.documentElement.classList.remove('offline');
      this.dispatchEvent('pwa:online');
    });

    window.addEventListener('offline', () => {
      console.log('[PWA] Gone offline');
      document.documentElement.classList.add('offline');
      this.dispatchEvent('pwa:offline');
    });

    // Check initial connectivity status
    if (!navigator.onLine) {
      document.documentElement.classList.add('offline');
    }
  }

  /**
   * Setup performance optimizations
   */
  setupPerformanceOptimizations() {
    // Lazy load images
    if ('IntersectionObserver' in window) {
      this.setupLazyLoading();
    }

    // Monitor Core Web Vitals
    this.monitorWebVitals();
  }

  /**
   * Setup lazy loading for images
   */
  setupLazyLoading() {
    const imageObserver = new IntersectionObserver((entries, observer) => {
      entries.forEach(entry => {
        if (entry.isIntersecting) {
          const img = entry.target;
          if (img.dataset.src) {
            img.src = img.dataset.src;
            img.removeAttribute('data-src');
          }
          observer.unobserve(img);
        }
      });
    }, {
      rootMargin: '50px'
    });

    document.querySelectorAll('img[data-src]').forEach(img => {
      imageObserver.observe(img);
    });
  }

  /**
   * Monitor Core Web Vitals
   */
  monitorWebVitals() {
    // Monitor Largest Contentful Paint (LCP)
    const observer = new PerformanceObserver((list) => {
      const entries = list.getEntries();
      const lastEntry = entries[entries.length - 1];
      console.log('[PWA] LCP:', lastEntry.renderTime || lastEntry.loadTime);
    });

    try {
      observer.observe({ entryTypes: ['largest-contentful-paint'] });
    } catch (e) {
      // LCP observer not supported
    }

    // Monitor Cumulative Layout Shift (CLS)
    let clsValue = 0;
    const clsObserver = new PerformanceObserver((list) => {
      for (const entry of list.getEntries()) {
        if (!entry.hadRecentInput) {
          clsValue += entry.value;
          console.log('[PWA] CLS:', clsValue);
        }
      }
    });

    try {
      clsObserver.observe({ entryTypes: ['layout-shift'] });
    } catch (e) {
      // CLS observer not supported
    }

    // Monitor First Input Delay (FID) / Interaction to Next Paint (INP)
    const fidObserver = new PerformanceObserver((list) => {
      for (const entry of list.getEntries()) {
        console.log('[PWA] First Input Delay:', entry.processingDuration);
      }
    });

    try {
      fidObserver.observe({ entryTypes: ['first-input'] });
    } catch (e) {
      // First Input observer not supported
    }
  }

  /**
   * Request persistent storage
   */
  async requestPersistentStorage() {
    if (navigator.storage && navigator.storage.persist) {
      const isPersisted = await navigator.storage.persist();
      console.log('[PWA] Persistent storage:', isPersisted);
      return isPersisted;
    }
  }

  /**
   * Get cache size
   */
  async getCacheSize() {
    if (!navigator.storage || !navigator.storage.estimate) {
      return null;
    }

    const estimate = await navigator.storage.estimate();
    return {
      usage: estimate.usage,
      quota: estimate.quota,
      percentage: (estimate.usage / estimate.quota) * 100
    };
  }

  /**
   * Clear cache
   */
  async clearCache() {
    if ('caches' in window) {
      const cacheNames = await caches.keys();
      await Promise.all(
        cacheNames.map(name => caches.delete(name))
      );
      console.log('[PWA] Cache cleared');
    }
  }

  /**
   * Dispatch custom PWA events
   */
  dispatchEvent(eventName, detail = {}) {
    const event = new CustomEvent(eventName, { detail });
    document.dispatchEvent(event);
  }

  /**
   * Request notification permission
   */
  async requestNotificationPermission() {
    if ('Notification' in window) {
      const permission = await Notification.requestPermission();
      console.log('[PWA] Notification permission:', permission);
      return permission === 'granted';
    }
    return false;
  }

  /**
   * Send notification
   */
  sendNotification(title, options = {}) {
    if (this.serviceWorkerRegistration && 'Notification' in window) {
      this.serviceWorkerRegistration.showNotification(title, {
        icon: '/assets/icons/icon-192x192.png',
        badge: '/assets/icons/badge-72x72.png',
        ...options
      });
    }
  }

  /**
   * Get app status
   */
  getStatus() {
    return {
      isInstalled: this.isInstalled,
      isOnline: navigator.onLine,
      serviceWorkerRegistered: !!this.serviceWorkerRegistration,
      deferredPromptAvailable: !!this.deferredPrompt
    };
  }
}

// Export PWA Manager
window.PWAManager = PWAManager;
export default PWAManager;
