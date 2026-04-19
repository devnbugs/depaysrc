@extends($activeTemplate.'layouts.dashboard')

@section('content')
    <div class="min-h-screen space-y-6">
        <!-- Header -->
        <section class="rounded-[2rem] border border-slate-200 bg-gradient-to-br from-white to-slate-50 p-6 shadow-sm dark:border-white/10 dark:from-white/5 dark:to-white/3">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="max-w-2xl">
                    <p class="text-xs font-semibold uppercase tracking-[0.26em] text-emerald-600 dark:text-emerald-400">💚 User To User</p>
                    <h2 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950 dark:text-white">{{ $pageTitle }}</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-600 dark:text-zinc-400">
                        Transfer instantly to other users with zero fees. Fast, secure, and receipt included.
                    </p>
                </div>
                <div class="grid gap-3 sm:grid-cols-2 wallet-cards theme-cards">
                    <div class="rounded-2xl border border-slate-200 bg-gradient-to-br from-white to-slate-50 px-4 py-3 shadow-xs transition-colors dark:border-white/10 dark:from-zinc-900/80 dark:to-zinc-900/40">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-500 dark:text-zinc-500">Your Balance</p>
                        <p class="mt-2 text-xl font-semibold text-emerald-600 dark:text-emerald-400">{{ $general->cur_sym }}{{ showAmount(Auth::user()->balance) }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-gradient-to-br from-white to-slate-50 px-4 py-3 shadow-xs transition-colors dark:border-white/10 dark:from-zinc-900/80 dark:to-zinc-900/40">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-500 dark:text-zinc-500">Transfer Fee</p>
                        <p class="mt-2 text-xl font-semibold text-sky-600 dark:text-sky-400">FREE ✓</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Main Transfer Form -->
        <section class="grid gap-6 xl:grid-cols-[1.25fr,0.75fr]">
            <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-white/5">
                <!-- Progress -->
                <div class="mb-8 flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-emerald-100 text-sm font-bold text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300">1</div>
                        <div>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">Recipient</p>
                            <p class="text-xs text-slate-500 dark:text-zinc-500">Choose or search</p>
                        </div>
                    </div>
                    <div class="h-1 flex-1 mx-3 rounded-full bg-slate-200 dark:bg-white/10"></div>
                    <div class="flex items-center gap-3">
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-slate-200 text-sm font-bold text-slate-500 dark:bg-white/10 dark:text-zinc-500">2</div>
                        <div>
                            <p class="text-sm font-semibold text-slate-500 dark:text-zinc-400">Amount</p>
                            <p class="text-xs text-slate-500 dark:text-zinc-500">& Confirm</p>
                        </div>
                    </div>
                </div>

                <form method="POST" action="{{ route('user.usertransfer') }}" id="user-transfer-form" data-confirm-form="1" data-busy-form data-busy-message="Processing your transfer. Please wait..." data-confirm-title="Confirm instant transfer" data-confirm-message="Please review the recipient and amount before sending this transfer." data-confirm-accept-text="Send instantly" class="space-y-6">
                    @csrf

                    <!-- STEP 1: Recipient Selection -->
                    <div id="step-1-container" class="space-y-4">
                        <div class="space-y-2">
                            <label for="recipient-type" class="block text-sm font-semibold text-slate-700 dark:text-zinc-300">
                                Select Recipient Type
                            </label>
                            <div class="grid gap-3 sm:grid-cols-2">
                                <button type="button" id="saved-btn" class="recipient-type-btn relative rounded-lg border-2 border-emerald-300 bg-emerald-50 p-4 text-left transition dark:border-emerald-500/30 dark:bg-emerald-500/10 is-active" data-type="saved">
                                    <p class="text-sm font-semibold text-emerald-900 dark:text-emerald-100">📁 Saved Beneficiary</p>
                                    <p class="mt-1 text-xs text-emerald-700 dark:text-emerald-300">From your saved list</p>
                                </button>
                                <button type="button" id="new-btn" class="recipient-type-btn relative rounded-lg border-2 border-slate-300 bg-white p-4 text-left transition hover:border-sky-300 hover:bg-sky-50 dark:border-white/10 dark:bg-zinc-950 dark:hover:border-sky-500/30 dark:hover:bg-sky-500/10" data-type="new">
                                    <p class="text-sm font-semibold text-slate-900 dark:text-white">🔍 Search User</p>
                                    <p class="mt-1 text-xs text-slate-600 dark:text-zinc-400">Find by username</p>
                                </button>
                            </div>
                            <input type="hidden" name="type" id="recipient-type-input" value="1">
                        </div>

                        <!-- Saved Beneficiary Option -->
                        <div id="saved-beneficiary-container" class="space-y-2 animate-slide-in">
                            <label for="beneficiary-saved" class="block text-sm font-semibold text-slate-700 dark:text-zinc-300">
                                Choose from Saved
                            </label>
                            <select id="beneficiary-saved" name="beneficiary" class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                                <option value="">Select a saved beneficiary...</option>
                                @foreach($benefit as $data)
                                    <option value="{{ $data->details }}">{{ $data->details }}</option>
                                @endforeach
                            </select>
                            @if(count($benefit) == 0)
                                <p class="text-sm text-amber-600 dark:text-amber-400">📌 No saved beneficiaries yet. Switch to "Search User" to add one.</p>
                            @endif
                        </div>

                        <!-- New Recipient Option -->
                        <div id="new-recipient-container" class="hidden space-y-2 animate-slide-in">
                            <label for="username-search" class="block text-sm font-semibold text-slate-700 dark:text-zinc-300">
                                Search Username or Account ID
                            </label>
                            <div class="relative">
                                <input type="text" id="username-search" name="username" placeholder="Enter username or account number" class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-sky-500 focus:ring-2 focus:ring-sky-500/20 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                                <div id="search-results" class="absolute top-full left-0 right-0 z-20 mt-2 hidden rounded-xl border border-slate-200 bg-white shadow-xl dark:border-white/10 dark:bg-zinc-950 max-h-60 overflow-y-auto"></div>
                            </div>
                        </div>

                        <!-- Recipient Info Card -->
                        <div id="recipient-info-container" class="hidden rounded-xl border-2 border-emerald-300 bg-emerald-50 p-4 dark:border-emerald-500/30 dark:bg-emerald-500/10 animate-slide-in">
                            <div class="flex items-start gap-3">
                                <div class="flex h-12 w-12 items-center justify-center rounded-full bg-emerald-200 dark:bg-emerald-500/30">
                                    <span class="text-lg">👤</span>
                                </div>
                                <div class="flex-1">
                                    <p class="text-sm font-semibold text-emerald-900 dark:text-emerald-100">
                                        <span id="recipient-name">--</span>
                                    </p>
                                    <p class="text-xs text-emerald-700 dark:text-emerald-300">
                                        Account: <span id="recipient-username" class="font-mono">--</span>
                                    </p>
                                </div>
                                <button type="button" id="clear-recipient-btn" class="rounded-full bg-emerald-200/50 p-2 text-emerald-700 hover:bg-emerald-300/50 dark:bg-emerald-500/20 dark:text-emerald-300 dark:hover:bg-emerald-500/30">
                                    ✕
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- STEP 2: Amount & Confirmation -->
                    <div id="step-2-container" class="hidden space-y-5 border-t border-slate-200 pt-6 dark:border-white/10 animate-slide-in">
                        <div class="space-y-2">
                            <label for="amount" class="block text-sm font-semibold text-slate-700 dark:text-zinc-300">
                                Transfer Amount
                            </label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-lg font-semibold text-slate-500 dark:text-zinc-400">{{ $general->cur_sym }}</span>
                                <input type="number" min="1" step="0.01" id="amount" name="amount" value="{{ old('amount') }}" placeholder="0.00" class="w-full rounded-xl border-2 border-slate-300 bg-white py-3 pl-8 pr-4 text-lg font-semibold text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-2 focus:ring-emerald-500/20 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                            </div>
                        </div>

                        <!-- Amount Summary -->
                        <div class="rounded-xl bg-emerald-50 p-4 dark:bg-emerald-500/10">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs uppercase tracking-[0.15em] font-semibold text-emerald-600 dark:text-emerald-400">Total to Send</p>
                                    <p id="amount-display" class="mt-2 text-2xl font-bold text-emerald-900 dark:text-emerald-100">{{ $general->cur_sym }}0.00</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-emerald-700 dark:text-emerald-300">No charges for internal transfers</p>
                                    <p class="mt-2 text-sm font-semibold text-emerald-600 dark:text-emerald-400">✓ Zero Fee</p>
                                </div>
                            </div>
                        </div>

                        <!-- Recipient Review -->
                        <div class="rounded-xl border-2 border-slate-200 bg-slate-50 p-4 dark:border-white/10 dark:bg-zinc-900/50">
                            <p class="text-xs uppercase tracking-[0.15em] font-semibold text-slate-500 dark:text-zinc-500">Sending to</p>
                            <p id="amount-recipient-name" class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">--</p>
                        </div>

                        <!-- Save Beneficiary -->
                        <label class="flex items-center gap-3 rounded-xl border-2 border-slate-300 bg-white p-4 cursor-pointer transition hover:border-sky-300 dark:border-white/10 dark:bg-zinc-950 dark:hover:border-sky-500/30">
                            <input type="checkbox" id="save-beneficiary" name="save_beneficiary" class="h-5 w-5 rounded border-slate-300 text-emerald-600 focus:ring-2 focus:ring-emerald-500/20 dark:border-white/20 dark:bg-zinc-900">
                            <div>
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">Save this recipient</p>
                                <p class="text-xs text-slate-600 dark:text-zinc-400">Add to your beneficiary list for faster transfers</p>
                            </div>
                        </label>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex flex-col gap-3 border-t border-slate-200 pt-6 dark:border-white/10 sm:flex-row sm:items-center sm:justify-between">
                        <p id="form-status" class="text-sm text-slate-600 dark:text-zinc-400">Select a recipient to continue...</p>
                        <div class="flex gap-3">
                            <button type="button" id="reset-form-btn" class="hidden rounded-full border-2 border-slate-300 bg-white px-6 py-2 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:bg-slate-50 dark:border-white/10 dark:bg-zinc-950 dark:text-zinc-300 dark:hover:border-white/20 dark:hover:bg-zinc-900">Reset</button>
                            <button type="submit" id="submit-btn" class="rounded-full bg-slate-950 px-8 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:bg-slate-400 disabled:text-slate-200 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200 dark:disabled:bg-slate-600 dark:disabled:text-slate-300" disabled>Transfer Instantly</button>
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
                            <article class="rounded-xl border border-slate-200 bg-slate-50 p-3 dark:border-white/10 dark:bg-zinc-900/70 group">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="flex-1">
                                        <p class="text-sm font-semibold text-slate-950 dark:text-white">{{ $item->details }}</p>
                                        <p class="mt-1 text-xs text-slate-500 dark:text-zinc-500">{{ $item->trx }}</p>
                                    </div>
                                    <a href="#" data-transfer-id="{{ $item->id }}" class="download-receipt-btn hidden group-hover:block rounded px-2 py-1 text-xs text-sky-600 hover:bg-sky-100 dark:text-sky-400 dark:hover:bg-sky-500/10" title="Download receipt">
                                        📥
                                    </a>
                                </div>
                                <div class="mt-3 flex items-end justify-between gap-2">
                                    <p class="text-base font-semibold text-slate-950 dark:text-white">{{ $general->cur_sym }}{{ showAmount($item->amount) }}</p>
                                    <p class="text-xs text-slate-500 dark:text-zinc-500">{{ $item->created_at ? $item->created_at->format('M d, Y') : '' }}</p>
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

                <!-- Saved Beneficiaries -->
                <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-white/5">
                    <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500 dark:text-zinc-500">⭐ Saved List</p>
                    <div class="mt-5 space-y-2">
                        @forelse ($benefit as $data)
                            <div class="flex items-center justify-between rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 dark:border-white/10 dark:bg-zinc-900/70">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $data->details }}</p>
                                    <p class="text-xs text-slate-500 dark:text-zinc-500">{{ $data->created_at->format('M d, Y') }}</p>
                                </div>
                                <a href="{{ route('user.deletebeneficiary', $data->id) }}" class="rounded px-2 py-1 text-xs text-red-600 hover:bg-red-100 dark:text-red-400 dark:hover:bg-red-500/10" data-confirm-link data-confirm-tone="danger" data-confirm-title="Delete beneficiary" data-confirm-message="Remove from saved list?">
                                    ✕
                                </a>
                            </div>
                        @empty
                            <p class="text-center text-xs text-slate-500 dark:text-zinc-500 py-4">No saved beneficiaries</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </section>
    </div>
@endsection

@push('style')
    <style>
        @keyframes slideInUp {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .animate-slide-in { animation: slideInUp 0.3s ease; }
        
        .recipient-type-btn {
            cursor: pointer;
        }
        .recipient-type-btn.is-active {
            --tw-ring-offset-color: transparent;
        }
        
        #step-2-container {
            transition: opacity 0.3s ease;
        }
    </style>
@endpush

@push('script')
    <script>
        (() => {
            const config = {
                currency: @json($general->cur_sym),
                userId: @json(Auth::user()->id),
                beneficiaries: @json($benefit->pluck('details')),
            };
            
            // DOM Elements
            const form = document.getElementById('user-transfer-form');
            const savedBtn = document.getElementById('saved-btn');
            const newBtn = document.getElementById('new-btn');
            const typeInput = document.getElementById('recipient-type-input');
            const savedContainer = document.getElementById('saved-beneficiary-container');
            const newContainer = document.getElementById('new-recipient-container');
            const beneficiarySelect = document.getElementById('beneficiary-saved');
            const usernameSearch = document.getElementById('username-search');
            const searchResults = document.getElementById('search-results');
            const recipientInfoContainer = document.getElementById('recipient-info-container');
            const recipientName = document.getElementById('recipient-name');
            const recipientUsername = document.getElementById('recipient-username');
            const clearBtn = document.getElementById('clear-recipient-btn');
            const step2Container = document.getElementById('step-2-container');
            const amountInput = document.getElementById('amount');
            const amountDisplay = document.getElementById('amount-display');
            const amountRecipientName = document.getElementById('amount-recipient-name');
            const submitBtn = document.getElementById('submit-btn');
            const resetBtn = document.getElementById('reset-form-btn');
            const formStatus = document.getElementById('form-status');
            const saveBeneficiaryCheckbox = document.getElementById('save-beneficiary');
            
            // State
            let selectedRecipient = null;
            let selectedRecipientType = 'saved';
            
            const money = (value) => `${config.currency}${Number(value || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
            
            // Recipient Type Selection
            savedBtn.addEventListener('click', () => {
                selectedRecipientType = 'saved';
                typeInput.value = '1';
                savedBtn.classList.add('is-active', 'border-emerald-300', 'bg-emerald-50', 'dark:border-emerald-500/30', 'dark:bg-emerald-500/10');
                savedBtn.classList.remove('border-slate-300', 'bg-white', 'hover:border-sky-300', 'dark:border-white/10', 'dark:bg-zinc-950');
                newBtn.classList.remove('is-active', 'border-emerald-300', 'bg-emerald-50', 'dark:border-emerald-500/30', 'dark:bg-emerald-500/10');
                newBtn.classList.add('border-slate-300', 'bg-white', 'hover:border-sky-300', 'dark:border-white/10', 'dark:bg-zinc-950');
                savedContainer.classList.remove('hidden');
                newContainer.classList.add('hidden');
                selectedRecipient = null;
                recipientInfoContainer.classList.add('hidden');
                step2Container.classList.add('hidden');
                updateSubmitButton();
            });
            
            newBtn.addEventListener('click', () => {
                selectedRecipientType = 'new';
                typeInput.value = '2';
                newBtn.classList.add('is-active', 'border-emerald-300', 'bg-emerald-50', 'dark:border-emerald-500/30', 'dark:bg-emerald-500/10');
                newBtn.classList.remove('border-slate-300', 'bg-white', 'hover:border-sky-300', 'dark:border-white/10', 'dark:bg-zinc-950');
                savedBtn.classList.remove('is-active', 'border-emerald-300', 'bg-emerald-50', 'dark:border-emerald-500/30', 'dark:bg-emerald-500/10');
                savedBtn.classList.add('border-slate-300', 'bg-white', 'hover:border-sky-300', 'dark:border-white/10', 'dark:bg-zinc-950');
                newContainer.classList.remove('hidden');
                savedContainer.classList.add('hidden');
                selectedRecipient = null;
                recipientInfoContainer.classList.add('hidden');
                step2Container.classList.add('hidden');
                usernameSearch.focus();
                updateSubmitButton();
            });
            
            // Saved Beneficiary Selection
            beneficiarySelect.addEventListener('change', (e) => {
                if (e.target.value) {
                    selectedRecipient = e.target.value;
                    recipientName.textContent = e.target.value;
                    recipientUsername.textContent = e.target.value;
                    recipientInfoContainer.classList.remove('hidden');
                    step2Container.classList.remove('hidden');
                    amountRecipientName.textContent = selectedRecipient;
                    formStatus.textContent = '✓ Recipient selected. Enter amount to proceed.';
                    resetBtn.classList.remove('hidden');
                } else {
                    selectedRecipient = null;
                    recipientInfoContainer.classList.add('hidden');
                    step2Container.classList.add('hidden');
                    formStatus.textContent = 'Select a saved beneficiary to continue...';
                    resetBtn.classList.add('hidden');
                }
                updateSubmitButton();
            });
            
            // Username Search
            usernameSearch.addEventListener('input', async (e) => {
                const query = e.target.value.trim();
                if (!query || query.length < 2) {
                    searchResults.classList.add('hidden');
                    return;
                }
                
                try {
                    const response = await window.axios.post('@json(route("user.search-users"))', { query });
                    const users = response.data?.data || [];
                    
                    if (!users.length) {
                        searchResults.innerHTML = '<div class="px-4 py-3 text-sm text-slate-500">No users found</div>';
                        searchResults.classList.remove('hidden');
                        return;
                    }
                    
                    searchResults.innerHTML = users.map(user => `
                        <button type="button" data-username="${user.username}" class="w-full text-left px-4 py-3 hover:bg-slate-100 dark:hover:bg-white/10 border-b border-slate-100 dark:border-white/10 last:border-b-0 transition">
                            <div class="font-semibold text-slate-900 dark:text-white">${user.name}</div>
                            <div class="text-xs text-slate-500 dark:text-zinc-500">@${user.username}</div>
                        </button>
                    `).join('');
                    
                    searchResults.classList.remove('hidden');
                    
                    searchResults.querySelectorAll('[data-username]').forEach(btn => {
                        btn.addEventListener('click', (e) => {
                            e.preventDefault();
                            selectSearchResult(btn.dataset.username, e.target.closest('[data-username]').querySelector('.font-semibold').textContent);
                        });
                    });
                } catch (error) {
                    searchResults.innerHTML = '<div class="px-4 py-3 text-sm text-red-600">Search error. Try again.</div>';
                    searchResults.classList.remove('hidden');
                }
            });
            
            const selectSearchResult = (username, name) => {
                selectedRecipient = username;
                usernameSearch.value = username;
                searchResults.innerHTML = '';
                searchResults.classList.add('hidden');
                recipientName.textContent = name;
                recipientUsername.textContent = username;
                recipientInfoContainer.classList.remove('hidden');
                step2Container.classList.remove('hidden');
                amountRecipientName.textContent = name;
                formStatus.textContent = '✓ Recipient found. Enter amount to proceed.';
                resetBtn.classList.remove('hidden');
                updateSubmitButton();
            };
            
            // Clear Recipient
            clearBtn.addEventListener('click', () => {
                selectedRecipient = null;
                beneficiarySelect.value = '';
                usernameSearch.value = '';
                recipientInfoContainer.classList.add('hidden');
                step2Container.classList.add('hidden');
                amountInput.value = '';
                amountDisplay.textContent = money(0);
                formStatus.textContent = selectedRecipientType === 'saved' ? 'Select a saved beneficiary to continue...' : 'Search for a user to continue...';
                resetBtn.classList.add('hidden');
                updateSubmitButton();
            });
            
            // Amount Input
            amountInput.addEventListener('input', (e) => {
                const amount = Number(e.target.value || 0);
                amountDisplay.textContent = money(amount);
                updateSubmitButton();
            });
            
            // Reset Form
            resetBtn.addEventListener('click', () => {
                form.reset();
                beneficiarySelect.value = '';
                usernameSearch.value = '';
                selectedRecipient = null;
                amountInput.value = '';
                amountDisplay.textContent = money(0);
                recipientInfoContainer.classList.add('hidden');
                step2Container.classList.add('hidden');
                savedBtn.click();
                updateSubmitButton();
            });
            
            const updateSubmitButton = () => {
                const hasRecipient = selectedRecipient !== null;
                const hasAmount = Number(amountInput.value || 0) > 0;
                
                if (hasRecipient && hasAmount) {
                    submitBtn.disabled = false;
                    formStatus.textContent = '✓ Ready to transfer. Review and confirm.';
                } else if (hasRecipient) {
                    submitBtn.disabled = true;
                    formStatus.textContent = 'Enter amount to proceed...';
                } else {
                    submitBtn.disabled = true;
                    formStatus.textContent = selectedRecipientType === 'saved' ? 'Select a saved beneficiary to continue...' : 'Search for a user to continue...';
                }
            };
            
            // Close search results when clicking outside
            document.addEventListener('click', (e) => {
                if (!usernameSearch.contains(e.target) && !searchResults.contains(e.target)) {
                    searchResults.classList.add('hidden');
                }
            });
            
            // Initialize
            if (!form) return;
            updateSubmitButton();
        })();
    </script>
@endpush
