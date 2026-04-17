<div>
    @if ($showModal && ($requirePin || $require2fa))
        <!-- Payment Confirmation Modal Backdrop -->
        <div class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4" wire:loading.class="hidden" style="display: @js($showModal) ? 'flex' : 'none'">
            <!-- Modal Content -->
            <div class="bg-white dark:bg-slate-900 rounded-3xl shadow-2xl max-w-md w-full space-y-6 p-6 md:p-8 relative z-50">
                <!-- Header -->
                <div class="space-y-2">
                    <div class="flex items-center gap-3">
                        <div class="flex-shrink-0 w-10 h-10 rounded-full bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center">
                            <svg class="w-6 h-6 text-blue-600 dark:text-blue-400" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" />
                            </svg>
                        </div>
                        <h3 class="text-lg font-semibold text-slate-900 dark:text-white">Confirm Payment</h3>
                    </div>
                    <p class="text-sm text-slate-600 dark:text-zinc-300">{{ $typeLabel }} - Complete the security verification below</p>
                </div>

                <!-- Payment Summary -->
                <div class="bg-slate-50 dark:bg-slate-800/50 rounded-2xl p-4 space-y-3">
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-600 dark:text-zinc-400">Type</span>
                        <span class="font-medium text-slate-900 dark:text-white">{{ $typeLabel }}</span>
                    </div>
                    <div class="flex justify-between text-sm">
                        <span class="text-slate-600 dark:text-zinc-400">Phone Number</span>
                        <span class="font-medium text-slate-900 dark:text-white">{{ substr($phone, 0, 4) }}***{{ substr($phone, -3) }}</span>
                    </div>
                    @if ($bundleName)
                        <div class="flex justify-between text-sm">
                            <span class="text-slate-600 dark:text-zinc-400">Bundle</span>
                            <span class="font-medium text-slate-900 dark:text-white">{{ $bundleName }}</span>
                        </div>
                    @endif
                    <div class="border-t border-slate-200 dark:border-slate-700 pt-3 flex justify-between text-sm">
                        <span class="text-slate-600 dark:text-zinc-400">Amount</span>
                        <span class="font-bold text-lg text-blue-600 dark:text-blue-400">{{ $general->cur_sym }}{{ number_format($amount, 2) }}</span>
                    </div>
                </div>

                <!-- Error Message -->
                @if ($errorMessage)
                    <div class="bg-red-50 border border-red-200 rounded-xl p-4 dark:bg-red-900/20 dark:border-red-900/30">
                        <p class="text-sm text-red-600 dark:text-red-400">{{ $errorMessage }}</p>
                    </div>
                @endif

                <!-- PIN Input (if required) -->
                @if ($requirePin)
                    <div class="space-y-2">
                        <label class="block text-sm font-medium text-slate-700 dark:text-zinc-200">
                            Authorization PIN
                        </label>
                        <p class="text-xs text-slate-500 dark:text-zinc-400">Enter your 4-digit PIN to authorize this payment</p>
                        <div class="flex gap-3 justify-center pt-2">
                            <input
                                type="password"
                                wire:model.live="pin1"
                                inputmode="numeric"
                                maxlength="1"
                                placeholder="•"
                                @focus="$el.select()"
                                @keyup="if($el.value.length === 1) { @js($wire->focus('pin2')); }"
                                @keydown.backspace="if($el.value === '') { @js($wire->focus('pin1')); }"
                                class="w-12 h-12 text-center text-2xl font-bold rounded-xl border-2 border-slate-300 dark:border-slate-600 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900 dark:bg-slate-800 dark:text-white outline-none transition"
                            >
                            <input
                                type="password"
                                wire:model.live="pin2"
                                inputmode="numeric"
                                maxlength="1"
                                placeholder="•"
                                @focus="$el.select()"
                                @keyup="if($el.value.length === 1) { @js($wire->focus('pin3')); }"
                                @keydown.backspace="if($el.value === '') { @js($wire->focus('pin1')); }"
                                class="w-12 h-12 text-center text-2xl font-bold rounded-xl border-2 border-slate-300 dark:border-slate-600 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900 dark:bg-slate-800 dark:text-white outline-none transition"
                            >
                            <input
                                type="password"
                                wire:model.live="pin3"
                                inputmode="numeric"
                                maxlength="1"
                                placeholder="•"
                                @focus="$el.select()"
                                @keyup="if($el.value.length === 1) { @js($wire->focus('pin4')); }"
                                @keydown.backspace="if($el.value === '') { @js($wire->focus('pin2')); }"
                                class="w-12 h-12 text-center text-2xl font-bold rounded-xl border-2 border-slate-300 dark:border-slate-600 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900 dark:bg-slate-800 dark:text-white outline-none transition"
                            >
                            <input
                                type="password"
                                wire:model.live="pin4"
                                inputmode="numeric"
                                maxlength="1"
                                placeholder="•"
                                @focus="$el.select()"
                                @keydown.backspace="if($el.value === '') { @js($wire->focus('pin3')); }"
                                class="w-12 h-12 text-center text-2xl font-bold rounded-xl border-2 border-slate-300 dark:border-slate-600 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900 dark:bg-slate-800 dark:text-white outline-none transition"
                            >
                        </div>
                    </div>
                @elseif ($require2fa)
                    <div class="space-y-2">
                        <label for="authenticator_code" class="block text-sm font-medium text-slate-700 dark:text-zinc-200">
                            2FA Authenticator Code
                        </label>
                        <p class="text-xs text-slate-500 dark:text-zinc-400">Enter the 6-digit code from your authenticator app</p>
                        <input
                            type="text"
                            wire:model="authenticatorCode"
                            inputmode="numeric"
                            maxlength="6"
                            placeholder="000000"
                            class="w-full text-center text-2xl font-bold tracking-widest rounded-xl border-2 border-slate-300 dark:border-slate-600 focus:border-blue-500 focus:ring-2 focus:ring-blue-200 dark:focus:ring-blue-900 dark:bg-slate-800 dark:text-white px-4 py-3 outline-none transition"
                        >
                    </div>
                @endif

                <!-- Action Buttons -->
                <div class="flex gap-3 pt-2">
                    <button
                        type="button"
                        wire:click="closeModal"
                        @if ($isProcessing) disabled @endif
                        class="flex-1 px-4 py-2.5 rounded-full border border-slate-300 dark:border-slate-600 text-slate-700 dark:text-zinc-300 font-medium hover:bg-slate-50 dark:hover:bg-slate-800 transition disabled:opacity-50"
                    >
                        Cancel
                    </button>
                    <button
                        type="button"
                        wire:click="confirmPayment"
                        wire:loading.attr="disabled"
                        @if ($isProcessing) disabled @endif
                        class="flex-1 px-4 py-2.5 rounded-full bg-blue-600 hover:bg-blue-700 text-white font-medium transition disabled:opacity-50 flex items-center justify-center gap-2"
                    >
                        <span wire:loading.remove>Confirm & Pay</span>
                        <span wire:loading>
                            <svg class="animate-spin h-5 w-5" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            Processing...
                        </span>
                    </button>
                </div>

                <!-- Security Info -->
                <p class="text-xs text-center text-slate-500 dark:text-zinc-400">
                    🔒 Your payment is protected by industry-standard encryption
                </p>
            </div>
        </div>
    @endif

    @script
    <script>
        document.addEventListener('livewire:navigated', () => {
            // Re-focus when needed
            Livewire.on('focus-field', (field) => {
                const inputMap = {
                    'pin1': document.querySelector('[wire\\:model="pin1"]'),
                    'pin2': document.querySelector('[wire\\:model="pin2"]'),
                    'pin3': document.querySelector('[wire\\:model="pin3"]'),
                    'pin4': document.querySelector('[wire\\:model="pin4"]'),
                };

                if (inputMap[field.field]) {
                    inputMap[field.field].focus();
                }
            });

            // Handle payment confirmation
            Livewire.on('payment-confirmed', (data) => {
                if (data[0]?.redirect) {
                    window.location.href = data[0].redirect;
                } else {
                    location.reload();
                }
            });
        });
    </script>
    @endscript
</div>
