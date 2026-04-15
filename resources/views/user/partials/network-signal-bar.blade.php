<div id="network-strength-bar" class="w-full bg-gradient-to-r from-red-500 via-orange-500 to-green-500 h-1 transition-all duration-300" style="width: 0%;">
</div>

<div class="display-network-info bg-slate-50 dark:bg-zinc-900 px-4 py-2 border-b border-slate-200 dark:border-white/10">
    <div class="max-w-7xl mx-auto flex items-center justify-between text-xs">
        <div class="flex items-center gap-3">
            <div class="flex items-center gap-2">
                <span class="inline-flex h-2 w-2 rounded-full" id="network-status-indicator" style="background-color: #ef4444;"></span>
                <span class="font-medium text-slate-700 dark:text-zinc-300">Network: <span id="network-type-display">Detecting...</span></span>
            </div>
            <span class="text-slate-500 dark:text-zinc-400" id="network-speed-display">Speed: --</span>
            <span class="text-slate-500 dark:text-zinc-400" id="network-quality-display">Quality: --</span>
        </div>
        <button id="network-details-toggle" class="text-sky-600 hover:text-sky-700 dark:text-sky-400 dark:hover:text-sky-300 font-semibold">
            Details ↓
        </button>
    </div>
    
    <div id="network-details-panel" class="hidden mt-3 p-3 bg-white dark:bg-zinc-950 rounded-lg border border-slate-200 dark:border-white/10">
        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
            <div class="text-center">
                <p class="text-xs text-slate-500 dark:text-zinc-400">Connection Type</p>
                <p class="text-sm font-semibold text-slate-900 dark:text-white" id="connection-type">--</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-slate-500 dark:text-zinc-400">Effective Type</p>
                <p class="text-sm font-semibold text-slate-900 dark:text-white" id="effective-type">--</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-slate-500 dark:text-zinc-400">Downlink Speed</p>
                <p class="text-sm font-semibold text-slate-900 dark:text-white" id="downlink-speed">-- Mbps</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-slate-500 dark:text-zinc-400">RTT (Latency)</p>
                <p class="text-sm font-semibold text-slate-900 dark:text-white" id="rtt-value">-- ms</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-slate-500 dark:text-zinc-400">Save Data</p>
                <p class="text-sm font-semibold text-slate-900 dark:text-white" id="save-data">--</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-slate-500 dark:text-zinc-400">Signal Strength</p>
                <p class="text-sm font-semibold text-slate-900 dark:text-white" id="signal-strength">--</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-slate-500 dark:text-zinc-400">Status</p>
                <p class="text-sm font-semibold" id="network-status-text" style="color: #ef4444;">Poor</p>
            </div>
            <div class="text-center">
                <p class="text-xs text-slate-500 dark:text-zinc-400">Recommendation</p>
                <p class="text-sm font-semibold text-slate-900 dark:text-white" id="recommendation">Avoid transactions</p>
            </div>
        </div>
    </div>
</div>

@push('script')
<script>
(function() {
    'use strict';

    const networkBar = document.getElementById('network-strength-bar');
    const networkTypeDisplay = document.getElementById('network-type-display');
    const networkSpeedDisplay = document.getElementById('network-speed-display');
    const networkQualityDisplay = document.getElementById('network-quality-display');
    const networkStatusIndicator = document.getElementById('network-status-indicator');
    const networkStatusText = document.getElementById('network-status-text');
    const recommendationText = document.getElementById('recommendation');
    const detailsToggle = document.getElementById('network-details-toggle');
    const detailsPanel = document.getElementById('network-details-panel');

    const connectionTypeEl = document.getElementById('connection-type');
    const effectiveTypeEl = document.getElementById('effective-type');
    const downlinkSpeedEl = document.getElementById('downlink-speed');
    const rttValueEl = document.getElementById('rtt-value');
    const saveDataEl = document.getElementById('save-data');
    const signalStrengthEl = document.getElementById('signal-strength');

    let networkQuality = 50;

    function updateNetworkDetails() {
        if ('connection' in navigator) {
            const connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection;

            if (connection) {
                const effectiveType = connection.effectiveType || 'unknown';
                const downlink = connection.downlink || 0;
                const rtt = connection.rtt || 0;
                const saveData = connection.saveData || false;
                const type = connection.type || 'unknown';

                connectionTypeEl.textContent = type.charAt(0).toUpperCase() + type.slice(1);
                effectiveTypeEl.textContent = effectiveType.toUpperCase();
                downlinkSpeedEl.textContent = downlink.toFixed(2);
                rttValueEl.textContent = rtt;
                saveDataEl.textContent = saveData ? 'Yes' : 'No';

                // Calculate signal strength based on effective type and downlink
                calculateSignalStrength(effectiveType, downlink, rtt);
            }
        } else {
            // Fallback if Network Information API is not available
            detectNetworkQuality();
        }
    }

    function calculateSignalStrength(effectiveType, downlink, rtt) {
        let strength = 50;

        // Factor in effective type
        switch(effectiveType) {
            case '4g':
                strength += 35;
                break;
            case '3g':
                strength += 15;
                break;
            case '2g':
                strength -= 10;
                break;
            case 'slow-2g':
                strength -= 30;
                break;
        }

        // Factor in downlink speed
        if (downlink > 5) strength += 20;
        else if (downlink > 2) strength += 10;
        else if (downlink < 0.5) strength -= 15;

        // Factor in RTT (latency)
        if (rtt < 30) strength += 10;
        else if (rtt > 100) strength -= 15;

        // Clamp between 0 and 100
        networkQuality = Math.max(0, Math.min(100, strength));
        updateSignalBar();
    }

    function detectNetworkQuality() {
        // Fallback method using simple connectivity test
        const startTime = performance.now();
        fetch('/api/ping', { method: 'HEAD' })
            .then(() => {
                const latency = performance.now() - startTime;
                if (latency < 100) networkQuality = 80;
                else if (latency < 300) networkQuality = 50;
                else networkQuality = 20;
                updateSignalBar();
            })
            .catch(() => {
                networkQuality = 0;
                updateSignalBar();
            });
    }

    function updateSignalBar() {
        const bar = networkBar;
        bar.style.width = networkQuality + '%';

        // Update text displays
        const mbps = (networkQuality / 100) * 10;
        networkSpeedDisplay.textContent = `Speed: ${mbps.toFixed(2)} Mbps`;

        // Update quality display
        let qualityText = '';
        let statusColor = '';
        let statusText = '';
        let recommendation = '';

        if (networkQuality >= 70) {
            qualityText = 'Excellent';
            statusColor = '#22c55e';
            statusText = 'Excellent';
            recommendation = '✓ Transactions safe';
        } else if (networkQuality >= 50) {
            qualityText = 'Good';
            statusColor = '#f59e0b';
            statusText = 'Good';
            recommendation = '⚠ Transactions okay';
        } else if (networkQuality >= 30) {
            qualityText = 'Fair';
            statusColor = '#f97316';
            statusText = 'Fair';
            recommendation = '⚠ Use caution';
        } else {
            qualityText = 'Poor';
            statusColor = '#ef4444';
            statusText = 'Poor';
            recommendation = '✗ Avoid transactions';
        }

        networkQualityDisplay.textContent = `Quality: ${qualityText}`;
        networkStatusIndicator.style.backgroundColor = statusColor;
        networkStatusText.textContent = statusText;
        networkStatusText.style.color = statusColor;
        recommendationText.textContent = recommendation;
        signalStrengthEl.textContent = networkQuality + '%';

        updateNetworkType();
    }

    function updateNetworkType() {
        if ('connection' in navigator) {
            const connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
            if (connection && connection.effectiveType) {
                networkTypeDisplay.textContent = connection.effectiveType.toUpperCase();
            }
        }
    }

    // Toggle details panel
    detailsToggle.addEventListener('click', () => {
        detailsPanel.classList.toggle('hidden');
        const arrow = detailsToggle.textContent.includes('↓') ? '↑' : '↓';
        detailsToggle.textContent = detailsToggle.textContent.replace(/[↑↓]/, arrow);
    });

    // Listen for network changes
    if ('connection' in navigator) {
        const connection = navigator.connection || navigator.mozConnection || navigator.webkitConnection;
        if (connection) {
            connection.addEventListener('change', updateNetworkDetails);
        }
    }

    // Initial update
    updateNetworkDetails();
    
    // Periodic update every 5 seconds
    setInterval(updateNetworkDetails, 5000);
})();
</script>
@endpush
