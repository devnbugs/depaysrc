@extends($activeTemplate.'layouts.dashboard')

@section('content')
    <div class="min-h-screen space-y-6">
        <!-- Header -->
        <section class="rounded-[2rem] border border-slate-200 bg-gradient-to-br from-white to-slate-50 p-6 shadow-sm dark:border-white/10 dark:from-white/5 dark:to-white/3">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="max-w-2xl">
                    <p class="text-xs font-semibold uppercase tracking-[0.26em] text-sky-600 dark:text-sky-400">💳 Wallet To Bank</p>
                    <h2 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950 dark:text-white">{{ $pageTitle }}</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-600 dark:text-zinc-400">
                        Enter account details → Confirm amount → Authorize with PIN
                    </p>
                </div>
                <div class="grid gap-3 sm:grid-cols-3 wallet-cards theme-cards">
                    <div class="rounded-2xl border border-slate-200 bg-gradient-to-br from-white to-slate-50 px-4 py-3 shadow-xs transition-colors dark:border-white/10 dark:from-zinc-900/80 dark:to-zinc-900/40">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-500 dark:text-zinc-500">Wallet Balance</p>
                        <p class="mt-2 text-xl font-semibold text-emerald-600 dark:text-emerald-400">{{ $general->cur_sym }}{{ showAmount($user->balance) }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-gradient-to-br from-white to-slate-50 px-4 py-3 shadow-xs transition-colors dark:border-white/10 dark:from-zinc-900/80 dark:to-zinc-900/40">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-500 dark:text-zinc-500">Transfer Fee</p>
                        <p class="mt-2 text-xl font-semibold text-orange-600 dark:text-orange-400">{{ showAmount($general->transferfee) }}%</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-gradient-to-br from-white to-slate-50 px-4 py-3 shadow-xs transition-colors dark:border-white/10 dark:from-zinc-900/80 dark:to-zinc-900/40">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-500 dark:text-zinc-500">Limit</p>
                        <p class="mt-2 text-sm font-semibold text-slate-950 dark:text-zinc-100">{{ $general->cur_sym }}{{ showAmount($settings['minimum']) }} - {{ $general->cur_sym }}{{ showAmount($settings['maximum']) }}</p>
                    </div>
                </div>
            </div>
        </section>

        @if (! $settings['enabled'])
            <section class="rounded-[2rem] border border-amber-200 bg-amber-50 p-6 text-sm text-amber-900 shadow-sm dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-100">
                Local transfer is currently disabled from the admin dashboard.
            </section>
        @else
            <section class="grid gap-6 xl:grid-cols-[1.25fr,0.75fr]">
                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-white/5">
                    @if (!empty($bankLoadError))
                        <div class="mb-5 rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 text-sm text-amber-900 dark:border-amber-500/30 dark:bg-amber-500/10 dark:text-amber-100">
                            ⚠️ {{ $bankLoadError }}
                        </div>
                    @endif

                    <!-- Progress Steps -->
                    <div class="mb-8 flex items-center justify-between">
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-sky-100 text-sm font-bold text-sky-700 dark:bg-sky-500/20 dark:text-sky-300">1</div>
                            <div>
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">Account</p>
                                <p class="text-xs text-slate-500 dark:text-zinc-500">Number & Bank</p>
                            </div>
                        </div>
                        <div class="h-1 flex-1 mx-3 rounded-full bg-slate-200 dark:bg-white/10"></div>
                        <div class="flex items-center gap-3">
                            <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-200 text-sm font-bold text-slate-500 dark:bg-white/10 dark:text-zinc-500" id="step-2-indicator">2</div>
                            <div>
                                <p class="text-sm font-semibold text-slate-500 dark:text-zinc-400">Amount</p>
                                <p class="text-xs text-slate-500 dark:text-zinc-500">& Details</p>
                            </div>
                        </div>
                    </div>

                    <!-- Main Form -->
                    <form method="POST" action="{{ route('user.othertransfer') }}" id="local-transfer-form" data-confirm-form="1" data-busy-form data-busy-message="Processing your transfer. Please wait..." data-confirm-title="Confirm bank transfer" data-confirm-message="Please review the beneficiary, amount, and narration before sending this transfer." data-confirm-accept-text="Send transfer" class="space-y-6">
                        @csrf

                        <!-- STEP 1: Account Details -->
                        <div id="step-1-container" class="space-y-5">
                            <div>
                                <label for="account-number" class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">
                                    Account Number
                                    <span class="text-xs text-slate-500 dark:text-zinc-500"> (10 digits)</span>
                                </label>
                                <div class="relative">
                                    <input type="text" inputmode="numeric" maxlength="10" id="account-number" name="account_number" value="{{ old('account_number') }}" placeholder="0123456789" class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-3 text-lg font-semibold text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                                    <div id="account-length-indicator" class="absolute right-4 top-1/2 -translate-y-1/2 text-xs font-semibold text-slate-400 dark:text-zinc-500">
                                        <span id="account-digits">0</span>/10
                                    </div>
                                </div>
                                <p id="account-status" class="mt-2 text-sm text-slate-500 dark:text-zinc-400">Enter 10 digits to proceed...</p>
                            </div>

                            <!-- Bank Selection - Hidden until account has 10 digits -->
                            <div id="bank-selection-container" class="hidden space-y-2 opacity-0 transition-opacity duration-300">
                                <label for="bank-search" class="block text-sm font-semibold text-slate-700 dark:text-zinc-300">
                                    Select Bank
                                </label>
                                <div class="relative">
                                    <input type="text" id="bank-search" value="{{ old('bank_name') }}" autocomplete="off" placeholder="Search your bank..." class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                                    <div id="bank-suggestions" class="absolute top-full left-0 right-0 z-20 mt-2 hidden rounded-xl border border-slate-200 bg-white shadow-xl dark:border-white/10 dark:bg-zinc-950 max-h-60 overflow-y-auto"></div>
                                </div>
                                <input type="hidden" name="bank_name" id="bank-name" value="{{ old('bank_name') }}">
                                <input type="hidden" name="bank_code" id="bank-code" value="{{ old('bank_code') }}">
                            </div>

                            <!-- Account Name Resolution - Hidden until bank is selected and resolving -->
                            <div id="account-resolution-container" class="hidden space-y-2 animate-in opacity-0 transition-opacity duration-300">
                                <label class="block text-sm font-semibold text-slate-700 dark:text-zinc-300">Account Holder</label>
                                <div id="resolve-indicator" class="hidden items-center gap-3 rounded-xl border-2 border-sky-300 bg-sky-50 px-4 py-3 dark:border-sky-500/30 dark:bg-sky-500/10">
                                    <div class="h-2 w-2 animate-pulse rounded-full bg-sky-600 dark:bg-sky-400"></div>
                                    <span class="text-sm font-medium text-sky-700 dark:text-sky-300">Resolving account details...</span>
                                </div>
                                <input type="text" id="account-name" name="account_name" value="{{ old('account_name') }}" readonly placeholder="Resolving..." class="w-full rounded-xl border-2 border-emerald-300 bg-emerald-50 px-4 py-3 text-sm font-semibold text-emerald-900 outline-none dark:border-emerald-500/30 dark:bg-emerald-500/10 dark:text-emerald-100">
                                <p id="resolve-status" class="text-xs text-slate-500 dark:text-zinc-500">✓ Account verified</p>
                            </div>
                        </div>

                        <!-- STEP 2: Amount & Details - Hidden until step 1 is complete -->
                        <div id="step-2-container" class="hidden space-y-5 border-t border-slate-200 pt-6 dark:border-white/10">
                            <div class="grid gap-4 md:grid-cols-2">
                                <div class="space-y-2">
                                    <label for="amount" class="block text-sm font-semibold text-slate-700 dark:text-zinc-300">
                                        Amount to Send
                                    </label>
                                    <div class="relative">
                                        <span class="absolute left-4 top-1/2 -translate-y-1/2 text-lg font-semibold text-slate-500 dark:text-zinc-400">{{ $general->cur_sym }}</span>
                                        <input type="number" min="1" step="0.01" id="amount" name="amount" value="{{ old('amount') }}" placeholder="0.00" class="w-full rounded-xl border-2 border-slate-300 bg-white py-3 pl-8 pr-4 text-lg font-semibold text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                                    </div>
                                </div>
                                <div class="space-y-2">
                                    <label for="narration" class="block text-sm font-semibold text-slate-700 dark:text-zinc-300">
                                        Description
                                    </label>
                                    <input type="text" id="narration" name="narration" maxlength="191" value="{{ old('narration', 'Bank transfer') }}" placeholder="e.g., Payment for goods" class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                                </div>
                            </div>

                            <!-- Charges & Total -->
                            <div class="charges-preview grid gap-3 sm:grid-cols-3 rounded-xl bg-gradient-to-br from-slate-50 to-slate-100 p-4 transition-colors dark:from-zinc-900/60 dark:to-zinc-900/40 dark:border dark:border-white/5">
                                <div class="rounded-lg bg-white/50 p-3 dark:bg-zinc-800/30 dark:border dark:border-white/5">
                                    <p class="text-xs uppercase tracking-[0.15em] font-semibold text-slate-500 dark:text-zinc-400">Transfer Fee</p>
                                    <p id="fee-preview" class="mt-2 text-lg font-bold text-orange-600 dark:text-orange-400">{{ $general->cur_sym }}0.00</p>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-zinc-500">{{ showAmount($general->transferfee) }}%</p>
                                </div>
                                <div class="rounded-lg bg-white/50 p-3 dark:bg-zinc-800/30 dark:border dark:border-white/5">
                                    <p class="text-xs uppercase tracking-[0.15em] font-semibold text-slate-500 dark:text-zinc-400">You'll Pay</p>
                                    <p id="total-preview" class="mt-2 text-lg font-bold text-slate-950 dark:text-zinc-100">{{ $general->cur_sym }}0.00</p>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-zinc-500">From your wallet</p>
                                </div>
                                <div class="rounded-lg bg-white/50 p-3 dark:bg-zinc-800/30 dark:border dark:border-white/5">
                                    <p class="text-xs uppercase tracking-[0.15em] font-semibold text-slate-500 dark:text-zinc-400">Provider</p>
                                    <p id="provider-preview" class="mt-2 text-lg font-bold text-sky-600 dark:text-sky-400">---</p>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-zinc-500">Routing bank</p>
                                </div>
                            </div>

                            <!-- Transaction Split Info (if enabled) -->
                            @if ($transactionSplitEnabled)
                            <div id="split-info-container" class="hidden rounded-xl border-2 border-amber-200 bg-amber-50 p-4 dark:border-amber-500/30 dark:bg-amber-500/10">
                                <p class="text-sm font-semibold text-amber-900 dark:text-amber-100">
                                    <span id="split-icon">📦</span> This transfer will be split into multiple transactions
                                </p>
                                <div id="split-details" class="mt-3 space-y-2 text-xs text-amber-800 dark:text-amber-200"></div>
                            </div>
                            @endif
                        </div>

                        <!-- Action Buttons -->
                        <div class="flex flex-col gap-3 border-t border-slate-200 pt-6 dark:border-white/10 sm:flex-row sm:items-center sm:justify-between">
                            <p id="form-status" class="text-sm text-slate-600 dark:text-zinc-400">Enter account number and select bank to continue...</p>
                            <div class="flex gap-3">
                                <button type="button" id="reset-form-btn" class="hidden rounded-full border-2 border-slate-300 bg-white px-6 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50 dark:border-white/10 dark:bg-zinc-950 dark:text-zinc-300 dark:hover:border-white/20 dark:hover:bg-zinc-900">Reset</button>
                                <button type="submit" id="submit-btn" class="rounded-full bg-slate-950 px-8 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:bg-slate-400 disabled:text-slate-200 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200 dark:disabled:bg-slate-600 dark:disabled:text-slate-300" disabled>Send Transfer</button>
                            </div>
                        </div>
                    </form>
                </div>

                <!-- Recent Transfers Sidebar -->
                <div class="space-y-6">
                    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-white/5">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500 dark:text-zinc-500">📋 Recent Transfers</p>
                        <div class="mt-5 space-y-3">
                            @forelse ($log as $item)
                                <article class="rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-white/10 dark:bg-zinc-900/70">
                                    <div class="flex items-start justify-between gap-2">
                                        <div class="flex-1">
                                            <p class="text-sm font-semibold text-slate-950 dark:text-white">{{ $item->bank_name ?: 'Bank transfer' }}</p>
                                            <p class="mt-1 text-xs text-slate-500 dark:text-zinc-500">{{ $item->account_name }} • {{ $item->account_number }}</p>
                                        </div>
                                        <span class="whitespace-nowrap rounded-full px-2 py-1 text-xs font-semibold {{ $item->status == 1 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300' : ($item->status == 2 ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300') }}">{{ $item->status == 1 ? '✓' : ($item->status == 2 ? '✕' : '⏳') }}</span>
                                    </div>
                                    <div class="mt-3 flex items-end justify-between gap-2">
                                        <div>
                                            <p class="text-base font-semibold text-slate-950 dark:text-white">{{ $general->cur_sym }}{{ showAmount($item->amount) }}</p>
                                            <p class="text-xs text-slate-500 dark:text-zinc-500">Fee {{ $general->cur_sym }}{{ showAmount($item->charge) }}</p>
                                        </div>
                                        <p class="text-right text-xs text-slate-500 dark:text-zinc-500">
                                            {{ $item->created_at ? $item->created_at->format('M d, Y') : '' }}
                                        </p>
                                    </div>
                                </article>
                            @empty
                                <div class="rounded-xl border border-dashed border-slate-300 px-4 py-6 text-center text-sm text-slate-500 dark:border-white/10 dark:text-zinc-500">No transfer history yet</div>
                            @endforelse
                        </div>
                        @if ($log->hasPages())
                            <div class="mt-4">{{ $log->links() }}</div>
                        @endif
                    </div>
                </div>
            </section>
        @endif
    </div>
@endsection

@push('style')
    <style>
        /* Modern animations */
        @keyframes slideInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .animate-slide-in { animation: slideInUp 0.3s ease; }
        .animate-in { animation: fadeIn 0.3s ease; }
        
        /* Smooth transitions */
        input[type="text"], input[type="number"], textarea {
            transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
        }
        
        /* Form container transitions */
        #step-2-container {
            transition: opacity 0.3s ease, max-height 0.3s ease;
            max-height: 0;
            overflow: hidden;
        }
        #step-2-container:not(.hidden) {
            max-height: 2000px;
            opacity: 1;
            animation: slideInUp 0.4s ease;
        }
        
        #bank-selection-container:not(.hidden) {
            opacity: 1 !important;
        }
        
        #account-resolution-container:not(.hidden) {
            opacity: 1 !important;
        }
    </style>
@endpush

@push('script')
    <script>
        (() => {
            // Configuration
            const config = {
                banks: @json($banks),
                feeRate: Number(@json((float) $general->transferfee)),
                currency: @json($general->cur_sym),
                resolveUrl: @json(route('user.othertransfer.resolve')),
                splitEnabled: @json((bool) $transactionSplitEnabled),
                splitThreshold: @json((float) $transactionSplitThreshold),
                requirePin: @json($settings['require_pin'] && (int) $user->pin_state === 1),
            };
            
            // DOM References
            const form = document.getElementById('local-transfer-form');
            const accountNumberInput = document.getElementById('account-number');
            const bankSearchInput = document.getElementById('bank-search');
            const bankNameInput = document.getElementById('bank-name');
            const bankCodeInput = document.getElementById('bank-code');
            const accountNameInput = document.getElementById('account-name');
            const amountInput = document.getElementById('amount');
            const narrationInput = document.getElementById('narration');
            const submitBtn = document.getElementById('submit-btn');
            const resetBtn = document.getElementById('reset-form-btn');
            const formStatus = document.getElementById('form-status');
            const accountStatus = document.getElementById('account-status');
            const accountDigits = document.getElementById('account-digits');
            const bankSelectionContainer = document.getElementById('bank-selection-container');
            const accountResolutionContainer = document.getElementById('account-resolution-container');
            const resolveIndicator = document.getElementById('resolve-indicator');
            const resolveStatus = document.getElementById('resolve-status');
            const feePreview = document.getElementById('fee-preview');
            const totalPreview = document.getElementById('total-preview');
            const providerPreview = document.getElementById('provider-preview');
            const step2Container = document.getElementById('step-2-container');
            const step2Indicator = document.getElementById('step-2-indicator');
            const bankSuggestions = document.getElementById('bank-suggestions');
            const splitInfoContainer = document.getElementById('split-info-container');
            const splitDetails = document.getElementById('split-details');
            
            // State
            let selectedBank = null;
            let resolvedData = null;
            let resolveSequence = 0;
            let formDirty = false;
            
            const money = (value) => `${config.currency}${Number(value || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            const digitsOnly = (value) => String(value || '').replace(/\D+/g, '');
            
            // Progressive Disclosure: Account Number Input
            accountNumberInput.addEventListener('input', (e) => {
                const digits = digitsOnly(e.target.value).slice(0, 10);
                accountNumberInput.value = digits;
                accountDigits.textContent = digits.length;
                formDirty = true;
                
                if (digits.length === 10) {
                    // Show bank selection
                    bankSelectionContainer.classList.remove('hidden');
                    bankSelectionContainer.style.opacity = '0';
                    setTimeout(() => {
                        bankSelectionContainer.style.opacity = '1';
                    }, 10);
                    accountStatus.textContent = '✓ Now select your bank...';
                    bankSearchInput.focus();
                } else {
                    bankSelectionContainer.classList.add('hidden');
                    bankSelectionContainer.style.opacity = '0';
                    accountResolutionContainer.classList.add('hidden');
                    accountResolutionContainer.style.opacity = '0';
                    accountStatus.textContent = `${10 - digits.length} more digits needed...`;
                    selectedBank = null;
                    resolvedData = null;
                    updateActionButtons();
                }
            });
            
            // Bank Search & Selection
            const searchBanks = () => {
                const query = bankSearchInput.value.trim().toLowerCase();
                bankNameInput.value = '';
                bankCodeInput.value = '';
                
                if (!query || query.length < 1) {
                    bankSuggestions.innerHTML = '';
                    bankSuggestions.classList.add('hidden');
                    return;
                }
                
                const matches = config.banks
                    .filter(b => b.name.toLowerCase().includes(query) || String(b.code || '').toLowerCase().includes(query))
                    .slice(0, 10);
                
                if (!matches.length) {
                    bankSuggestions.innerHTML = '<div class="px-4 py-3 text-sm text-slate-500">No banks found</div>';
                    bankSuggestions.classList.remove('hidden');
                    return;
                }
                
                bankSuggestions.innerHTML = matches.map((bank, i) => `
                    <button type="button" data-bank-index="${i}" class="w-full text-left px-4 py-3 hover:bg-slate-100 dark:hover:bg-white/10 border-b border-slate-100 dark:border-white/10 last:border-b-0 transition text-sm">
                        <div class="font-semibold text-slate-900 dark:text-white">${bank.name}</div>
                        <div class="text-xs text-slate-500 dark:text-zinc-500">${bank.code || 'No code'}</div>
                    </button>
                `).join('');
                
                bankSuggestions.classList.remove('hidden');
                
                // Auto-select if only one match
                if (matches.length === 1) {
                    selectBank(matches[0]);
                } else {
                    // Add click handlers
                    bankSuggestions.querySelectorAll('[data-bank-index]').forEach(btn => {
                        btn.addEventListener('click', (e) => {
                            e.preventDefault();
                            selectBank(matches[parseInt(btn.dataset.bankIndex)]);
                        });
                    });
                }
            };
            
            const selectBank = (bank) => {
                selectedBank = bank;
                bankSearchInput.value = bank.name;
                bankNameInput.value = bank.name;
                bankCodeInput.value = bank.code || '';
                bankSuggestions.innerHTML = '';
                bankSuggestions.classList.add('hidden');
                
                // Show account resolution container and trigger resolution
                accountResolutionContainer.classList.remove('hidden');
                accountResolutionContainer.style.opacity = '0';
                setTimeout(() => {
                    accountResolutionContainer.style.opacity = '1';
                }, 10);
                
                resolveAccount();
            };
            
            // Resolve Account
            const resolveAccount = async () => {
                const accountNum = digitsOnly(accountNumberInput.value).slice(0, 10);
                
                if (accountNum.length !== 10 || !selectedBank) {
                    return;
                }
                
                const currentSequence = ++resolveSequence;
                resolveIndicator.classList.remove('hidden');
                
                try {
                    const response = await window.axios.post(config.resolveUrl, {
                        bank_name: selectedBank.name,
                        bank_code: selectedBank.code || '',
                        account_number: accountNum
                    });
                    
                    if (currentSequence !== resolveSequence) return;
                    
                    const data = response.data?.data || {};
                    resolvedData = data;
                    
                    accountNameInput.value = data.account_name || '';
                    bankNameInput.value = data.bank_name || selectedBank.name;
                    bankCodeInput.value = data.bank_code || selectedBank.code || '';
                    providerPreview.textContent = (data.resolved_by || 'BANK').toUpperCase();
                    
                    resolveIndicator.classList.add('hidden');
                    resolveStatus.textContent = '✓ Account verified and ready';
                    accountStatus.textContent = '✓ Account resolved successfully';
                    
                    // Show step 2
                    step2Container.classList.remove('hidden');
                    step2Indicator.classList.add('bg-sky-100', 'text-sky-700', 'dark:bg-sky-500/20', 'dark:text-sky-300');
                    step2Indicator.classList.remove('bg-slate-200', 'text-slate-500', 'dark:bg-white/10', 'dark:text-zinc-500');
                    
                    updateActionButtons();
                } catch (error) {
                    if (currentSequence !== resolveSequence) return;
                    resolveIndicator.classList.add('hidden');
                    
                    const message = error?.response?.data?.message || 'Could not resolve account';
                    resolveStatus.textContent = '✕ ' + message;
                    window.depayToast?.({ title: 'Resolution failed', message, tone: 'warning' });
                }
            };
            
            // Amount & Narration
            const updateTotals = () => {
                const amount = Number(amountInput.value || 0);
                if (amount <= 0) {
                    feePreview.textContent = money(0);
                    totalPreview.textContent = money(0);
                    submitBtn.disabled = true;
                    return;
                }
                
                const fee = (amount * config.feeRate) / 100;
                const total = amount + fee;
                
                feePreview.textContent = money(fee);
                totalPreview.textContent = money(total);
                
                // Show split info if enabled and amount exceeds threshold
                if (config.splitEnabled && amount > config.splitThreshold) {
                    showSplitInfo(amount);
                } else {
                    splitInfoContainer.classList.add('hidden');
                }
                
                updateActionButtons();
            };
            
            const showSplitInfo = (amount) => {
                const threshold = config.splitThreshold;
                const maxChunk = 10000; // Standard chunk size
                let chunks = [];
                let remaining = amount;
                
                while (remaining > maxChunk) {
                    chunks.push(maxChunk);
                    remaining -= maxChunk;
                }
                if (remaining > 0) {
                    chunks.push(remaining);
                }
                
                if (chunks.length > 1) {
                    splitInfoContainer.classList.remove('hidden');
                    splitDetails.innerHTML = chunks.map((chunk, i) => 
                        `<div class="flex items-center justify-between"><span>Transfer ${i + 1}</span><strong>${money(chunk)}</strong></div>`
                    ).join('');
                } else {
                    splitInfoContainer.classList.add('hidden');
                }
            };
            
            const updateActionButtons = () => {
                const hasAccountInfo = accountNameInput.value.trim() !== '';
                const hasAmount = Number(amountInput.value || 0) > 0;
                
                if (hasAccountInfo && hasAmount) {
                    submitBtn.disabled = false;
                    formStatus.textContent = '✓ Ready to send. Review details and submit.';
                    resetBtn.classList.remove('hidden');
                } else if (hasAccountInfo) {
                    submitBtn.disabled = true;
                    formStatus.textContent = 'Enter amount to proceed...';
                    resetBtn.classList.remove('hidden');
                } else {
                    submitBtn.disabled = true;
                    formStatus.textContent = 'Complete account details to continue...';
                    resetBtn.classList.add('hidden');
                }
            };
            
            // Reset Form
            resetBtn.addEventListener('click', () => {
                accountNumberInput.value = '';
                bankSearchInput.value = '';
                bankNameInput.value = '';
                bankCodeInput.value = '';
                accountNameInput.value = '';
                amountInput.value = '';
                narrationInput.value = 'Bank transfer';
                
                bankSelectionContainer.classList.add('hidden');
                accountResolutionContainer.classList.add('hidden');
                step2Container.classList.add('hidden');
                splitInfoContainer.classList.add('hidden');
                step2Indicator.classList.add('bg-slate-200', 'text-slate-500', 'dark:bg-white/10', 'dark:text-zinc-500');
                step2Indicator.classList.remove('bg-sky-100', 'text-sky-700', 'dark:bg-sky-500/20', 'dark:text-sky-300');
                
                accountNumberInput.focus();
                updateActionButtons();
                
                window.depayToast?.({ title: 'Form reset', message: 'Ready for a new transfer', tone: 'info' });
            });
            
            // Event Listeners
            bankSearchInput.addEventListener('input', searchBanks);
            bankSearchInput.addEventListener('focus', searchBanks);
            amountInput.addEventListener('input', updateTotals);
            narrationInput.addEventListener('change', () => { formDirty = true; });
            
            // Close suggestions when clicking outside
            document.addEventListener('click', (e) => {
                if (!bankSuggestions.contains(e.target) && e.target !== bankSearchInput) {
                    bankSuggestions.classList.add('hidden');
                }
            });
            
            // Restore state if form has old values
            const restoreForm = () => {
                const accountNum = digitsOnly(accountNumberInput.value);
                const bankName = bankNameInput.value;
                const accountName = accountNameInput.value;
                
                if (accountNum.length === 10 && bankName && accountName) {
                    accountNumberInput.value = accountNum;
                    accountDigits.textContent = '10';
                    bankSearchInput.value = bankName;
                    bankSelectionContainer.classList.remove('hidden');
                    accountResolutionContainer.classList.remove('hidden');
                    step2Container.classList.remove('hidden');
                    step2Indicator.classList.add('bg-sky-100', 'text-sky-700', 'dark:bg-sky-500/20', 'dark:text-sky-300');
                    step2Indicator.classList.remove('bg-slate-200', 'text-slate-500', 'dark:bg-white/10', 'dark:text-zinc-500');
                    updateActionButtons();
                }
            };
            
            // Initialize
            if (!form) return;
            accountNumberInput.focus();
            updateTotals();
            restoreForm();
        })();
    </script>
@endpush
