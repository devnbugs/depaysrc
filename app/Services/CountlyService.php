<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;

/**
 * Countly Analytics Service
 * 
 * Tracks user behavior and events in a fintech payment platform
 * Works with authenticated users only (no admin panel)
 * 
 * Features:
 * - User session tracking
 * - Payment/transfer events
 * - Bill payment analytics
 * - Error tracking
 * - Security events
 * - Feature usage analytics
 */
class CountlyService
{
    private const COUNTLY_API_URL = 'https://your-countly-server.com';
    private const REQUEST_TIMEOUT = 10;
    private const BATCH_SIZE = 50;

    private string $appKey = '';
    private string $deviceId = '';
    private bool $isEnabled = false;
    private array $eventBatch = [];

    public function __construct()
    {
        $this->appKey = config('services.countly.app_key', '');
        $this->isEnabled = config('services.countly.enabled', false) && !empty($this->appKey);
    }

    /**
     * Initialize Countly session for authenticated user
     * Called when user logs in or on dashboard load
     */
    public function initializeSession(): void
    {
        if (!$this->isEnabled || !auth()->check()) {
            return;
        }

        $this->deviceId = $this->generateDeviceId();

        // Begin session
        $this->sendRequest('begin_session', [
            'app_key' => $this->appKey,
            'device_id' => $this->deviceId,
            'sdk_version' => '24.1',
            'metrics' => $this->getMetrics(),
        ]);

        // Log session start event
        $this->trackEvent('session_start', [
            'user_id' => auth()->id(),
            'username' => auth()->user()->username,
            'email' => auth()->user()->email,
            'timestamp' => now()->timestamp,
        ]);

        Log::channel('countly')->info('Countly session initialized', [
            'user_id' => auth()->id(),
            'device_id' => $this->deviceId,
        ]);
    }

    /**
     * End Countly session for authenticated user
     * Called when user logs out
     */
    public function endSession(): void
    {
        if (!$this->isEnabled || !auth()->check()) {
            return;
        }

        // Flush any pending events
        $this->flushEvents();

        // End session
        $this->sendRequest('end_session', [
            'app_key' => $this->appKey,
            'device_id' => $this->deviceId,
            'timestamp' => now()->timestamp,
        ]);

        Log::channel('countly')->info('Countly session ended', [
            'user_id' => auth()->id(),
        ]);
    }

    /**
     * Track user authentication events
     */
    public function trackAuthEvent(string $eventType, array $data = []): void
    {
        $events = [
            'login_success' => 'User successfully logged in',
            'login_failed' => 'User login failed',
            'register_success' => 'User registered successfully',
            'logout' => 'User logged out',
            'password_reset_requested' => 'Password reset requested',
            'password_reset_completed' => 'Password reset completed',
            '2fa_enabled' => '2FA enabled',
            '2fa_disabled' => '2FA disabled',
            'passkey_enrolled' => 'Passkey enrolled',
        ];

        if (isset($events[$eventType])) {
            $this->trackEvent("auth_{$eventType}", array_merge([
                'timestamp' => now()->timestamp,
            ], $data));
        }
    }

    /**
     * Track payment/transfer events
     */
    public function trackPaymentEvent(string $eventType, array $data = []): void
    {
        if (!auth()->check()) {
            return;
        }

        $events = [
            'deposit_initiated' => 'Deposit initiated',
            'deposit_completed' => 'Deposit completed',
            'deposit_failed' => 'Deposit failed',
            'withdrawal_initiated' => 'Withdrawal initiated',
            'withdrawal_completed' => 'Withdrawal completed',
            'withdrawal_failed' => 'Withdrawal failed',
            'transfer_initiated' => 'Transfer initiated',
            'transfer_completed' => 'Transfer completed',
            'transfer_failed' => 'Transfer failed',
        ];

        if (isset($events[$eventType])) {
            $eventData = array_merge([
                'user_id' => auth()->id(),
                'timestamp' => now()->timestamp,
            ], $data);

            // Add monetary value if present
            if (isset($data['amount'])) {
                $eventData['amount'] = (float) $data['amount'];
                $eventData['currency'] = config('app.currency', 'NGN');
            }

            $this->trackEvent("payment_{$eventType}", $eventData);
        }
    }

    /**
     * Track bill payment events
     */
    public function trackBillPaymentEvent(string $billType, string $eventType, array $data = []): void
    {
        if (!auth()->check()) {
            return;
        }

        $billTypes = [
            'airtime' => 'Airtime',
            'data' => 'Data Bundle',
            'electricity' => 'Electricity',
            'water' => 'Water Bill',
            'internet' => 'Internet Subscription',
            'cable_tv' => 'Cable TV',
            'exam_registration' => 'Exam Registration (WAEC/NECO/JAMB)',
        ];

        if (isset($billTypes[$billType])) {
            $eventData = array_merge([
                'user_id' => auth()->id(),
                'bill_type' => $billTypes[$billType],
                'timestamp' => now()->timestamp,
            ], $data);

            $this->trackEvent("bill_{$billType}_{$eventType}", $eventData);
        }
    }

    /**
     * Track security events
     */
    public function trackSecurityEvent(string $eventType, array $data = []): void
    {
        if (!auth()->check()) {
            return;
        }

        $events = [
            'suspicious_activity' => 'Suspicious activity detected',
            'ip_changed' => 'IP address changed',
            'device_changed' => 'New device login',
            'rate_limit_exceeded' => 'Rate limit exceeded',
            'verification_failed' => 'Verification failed',
            'pin_set' => 'PIN set',
            'pin_changed' => 'PIN changed',
            'kyc_initiated' => 'KYC initiated',
            'kyc_completed' => 'KYC completed',
            'kyc_failed' => 'KYC failed',
        ];

        if (isset($events[$eventType])) {
            $this->trackEvent("security_{$eventType}", array_merge([
                'user_id' => auth()->id(),
                'ip' => request()->ip(),
                'timestamp' => now()->timestamp,
            ], $data));
        }
    }

    /**
     * Track feature usage events
     */
    public function trackFeatureEvent(string $feature, string $action = 'access', array $data = []): void
    {
        if (!auth()->check()) {
            return;
        }

        $features = [
            'dashboard' => 'Dashboard',
            'cards' => 'Virtual Cards',
            'withdraw' => 'Withdrawal',
            'transfer' => 'Transfer',
            'savings' => 'Savings',
            'investment' => 'Investment',
            'loans' => 'Loans',
            'kyc_services' => 'KYC Services',
            'support' => 'Support',
            'settings' => 'Settings',
            'security' => 'Security Settings',
        ];

        if (isset($features[$feature])) {
            $this->trackEvent("feature_{$feature}_{$action}", array_merge([
                'user_id' => auth()->id(),
                'feature_name' => $features[$feature],
                'timestamp' => now()->timestamp,
            ], $data));
        }
    }

    /**
     * Track error events
     */
    public function trackErrorEvent(string $errorType, string $message = '', array $data = []): void
    {
        $this->trackEvent('error_occurred', array_merge([
            'error_type' => $errorType,
            'error_message' => $message,
            'user_id' => auth()->id() ?? null,
            'url' => request()->url(),
            'timestamp' => now()->timestamp,
        ], $data));

        Log::channel('countly')->error('Countly error tracked', [
            'error_type' => $errorType,
            'message' => $message,
        ]);
    }

    /**
     * Track custom event
     */
    public function trackEvent(string $eventName, array $data = []): void
    {
        if (!$this->isEnabled) {
            return;
        }

        $event = [
            'key' => $eventName,
            'count' => 1,
            'timestamp' => $data['timestamp'] ?? now()->timestamp,
            'segmentation' => $data,
        ];

        $this->eventBatch[] = $event;

        // Auto-flush if batch is full
        if (count($this->eventBatch) >= self::BATCH_SIZE) {
            $this->flushEvents();
        }
    }

    /**
     * Flush batched events to Countly
     */
    public function flushEvents(): void
    {
        if (empty($this->eventBatch) || !$this->isEnabled) {
            return;
        }

        try {
            $this->sendRequest('add_events', [
                'app_key' => $this->appKey,
                'device_id' => $this->deviceId,
                'events' => $this->eventBatch,
            ]);

            Log::channel('countly')->info('Events flushed to Countly', [
                'event_count' => count($this->eventBatch),
            ]);

            $this->eventBatch = [];
        } catch (\Exception $e) {
            Log::channel('countly')->error('Failed to flush events', [
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Send request to Countly server
     */
    private function sendRequest(string $method, array $data): bool
    {
        if (!$this->isEnabled) {
            return false;
        }

        try {
            $url = self::COUNTLY_API_URL . "/i?{$method}";
            
            \Illuminate\Support\Facades\Http::timeout(self::REQUEST_TIMEOUT)
                ->post($url, $data);

            return true;
        } catch (\Exception $e) {
            Log::channel('countly')->error('Countly request failed', [
                'method' => $method,
                'error' => $e->getMessage(),
            ]);

            return false;
        }
    }

    /**
     * Generate unique device ID based on user
     */
    private function generateDeviceId(): string
    {
        if (!auth()->check()) {
            return md5(request()->ip());
        }

        return md5(auth()->id() . '_' . auth()->user()->email . '_' . request()->ip());
    }

    /**
     * Get device metrics
     */
    private function getMetrics(): array
    {
        return [
            '_os' => php_uname('s'),
            '_os_version' => php_uname('r'),
            '_device' => 'Web',
            '_resolution' => '1920x1080', // Server-side can't know this, but can be sent from JS
            '_app_version' => config('app.version', '1.0.0'),
            '_locale' => app()->getLocale(),
        ];
    }

    /**
     * Check if Countly is enabled
     */
    public function isEnabled(): bool
    {
        return $this->isEnabled;
    }

    /**
     * Get Countly JavaScript snippet for client-side tracking
     */
    public function getClientScript(): string
    {
        if (!$this->isEnabled) {
            return '';
        }

        return <<<JAVASCRIPT
<script type="text/javascript">
  var Countly = Countly || {};
  Countly.q = Countly.q || [];
  
  Countly.q.push(['setApp', '{$this->appKey}']);
  Countly.q.push(['setUrl', 'https://your-countly-server.com']);
  Countly.q.push(['setRequiresConsent', false]);
  Countly.q.push(['onConsent', ['sessions', 'events', 'views', 'crashes', 'attribution', 'users']]);
  Countly.q.push(['beginSession']);
  
  (function() {
    var cly = document.createElement('script'); cly.type = 'text/javascript'; cly.async = true;
    cly.src = 'https://your-countly-server.com/sdk/web/countly.min.js';
    cly.onload = function(){Countly.q.forEach(function(item) { Countly[item[0]].apply(Countly, typeof item[1] == 'object' ? item[1] : [item[1]]); }); };
    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(cly, s);
  })();
</script>
JAVASCRIPT;
    }
}
