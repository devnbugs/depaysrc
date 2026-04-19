@extends($activeTemplate.'layouts.dashboard')

@section('content')
    <div class="min-h-screen space-y-6">
        <!-- Header -->
        <section class="rounded-[2rem] border border-slate-200 bg-gradient-to-br from-white to-slate-50 p-6 shadow-sm dark:border-white/10 dark:from-white/5 dark:to-white/3">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="max-w-2xl">
                    <p class="text-xs font-semibold uppercase tracking-[0.26em] text-blue-600 dark:text-blue-400">🏦 Interbank Transfer</p>
                    <h2 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950 dark:text-white">{{ $pageTitle }}</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-600 dark:text-zinc-400">
                        Transfer instantly to any bank account. Fast, secure, and transparent fees.
                    </p>
                </div>
                <div class="grid gap-3 sm:grid-cols-2 wallet-cards theme-cards">
                    <div class="rounded-2xl border border-slate-200 bg-gradient-to-br from-white to-slate-50 px-4 py-3 shadow-xs transition-colors dark:border-white/10 dark:from-zinc-900/80 dark:to-zinc-900/40">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-500 dark:text-zinc-500">Your Balance</p>
                        <p class="mt-2 text-xl font-semibold text-blue-600 dark:text-blue-400">{{ $general->cur_sym }}{{ showAmount($user->balance) }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-gradient-to-br from-white to-slate-50 px-4 py-3 shadow-xs transition-colors dark:border-white/10 dark:from-zinc-900/80 dark:to-zinc-900/40">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-500 dark:text-zinc-500">Saved Recipients</p>
                        <p class="mt-2 text-xl font-semibold text-sky-600 dark:text-sky-400">{{ $beneficiaries->count() }}</p>
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
                        <div class="flex h-10 w-10 items-center justify-center rounded-full bg-blue-100 text-sm font-bold text-blue-700 dark:bg-blue-500/20 dark:text-blue-300">1</div>
                        <div>
                            <p class="text-sm font-semibold text-slate-900 dark:text-white">Recipient</p>
                            <p class="text-xs text-slate-500 dark:text-zinc-500">Phone or saved</p>
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

                <form action="{{ route('user.dpay.submit') }}" method="POST" id="dpay-transfer-form" class="space-y-6">
                    @csrf

                    <!-- Recipient Type Selection -->
                    <div class="space-y-4">
                        <label class="block text-sm font-semibold text-slate-700 dark:text-zinc-300">
                            Recipient Type
                        </label>
                        <div class="grid gap-3 sm:grid-cols-2">
                            <button type="button" id="saved-btn" class="recipient-type-btn relative rounded-lg border-2 border-blue-300 bg-blue-50 p-4 text-left transition dark:border-blue-500/30 dark:bg-blue-500/10 is-active" data-type="saved">
                                <p class="text-sm font-semibold text-blue-900 dark:text-blue-100">📋 Saved Recipients</p>
                                <p class="mt-1 text-xs text-blue-700 dark:text-blue-300">From your list</p>
                            </button>
                            <button type="button" id="new-btn" class="recipient-type-btn relative rounded-lg border-2 border-slate-300 bg-white p-4 text-left transition hover:border-sky-300 hover:bg-sky-50 dark:border-white/10 dark:bg-zinc-950 dark:hover:border-sky-500/30 dark:hover:bg-sky-500/10" data-type="new">
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">📱 Phone Number</p>
                                <p class="mt-1 text-xs text-slate-600 dark:text-zinc-400">New recipient</p>
                            </button>
                        </div>
                        <input type="hidden" name="type" id="recipient-type-input" value="1">
                    </div>

                    <!-- Saved Beneficiary Option -->
                    <div id="saved-beneficiary-container" class="space-y-2 animate-slide-in">
                        <label for="beneficiary-saved" class="block text-sm font-semibold text-slate-700 dark:text-zinc-300">
                            Choose from Saved
                        </label>
                        <select id="beneficiary-saved" class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                            <option value="">Select a saved recipient...</option>
                            @foreach($beneficiaries as $data)
                                <option value="{{ $data->account_number }}">{{ $data->name ?? 'N/A' }} - {{ $data->account_number }}</option>
                            @endforeach
                        </select>
                        @if($beneficiaries->count() == 0)
                            <p class="text-sm text-amber-600 dark:text-amber-400">📌 No saved recipients yet. Switch to "Phone Number" to add one.</p>
                        @endif
                    </div>

                    <!-- New Recipient Option -->
                    <div id="new-recipient-container" class="hidden space-y-2 animate-slide-in">
                        <label for="phone-search" class="block text-sm font-semibold text-slate-700 dark:text-zinc-300">
                            Phone Number or Account
                        </label>
                        <input type="text" 
                               id="phone-search"
                               placeholder="Enter phone number or account number" 
                               class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                    </div>

                    <!-- Recipient Info Card -->
                    <div id="recipient-info-container" class="hidden rounded-xl border-2 border-blue-300 bg-blue-50 p-4 dark:border-blue-500/30 dark:bg-blue-500/10 animate-slide-in">
                        <div class="flex items-start gap-3">
                            <div class="flex h-12 w-12 items-center justify-center rounded-full bg-blue-200 dark:bg-blue-500/30">
                                <span class="text-lg">🏦</span>
                            </div>
                            <div class="flex-1">
                                <p class="text-sm font-semibold text-blue-900 dark:text-blue-100">
                                    <span id="recipient-name">--</span>
                                </p>
                                <p class="text-xs text-blue-700 dark:text-blue-300">
                                    Account: <span id="recipient-account" class="font-mono">--</span>
                                </p>
                                <p class="text-xs text-blue-700 dark:text-blue-300 mt-1">
                                    Bank: <span id="recipient-bank">--</span>
                                </p>
                            </div>
                            <button type="button" id="clear-recipient-btn" class="rounded-full bg-blue-200/50 p-2 text-blue-700 hover:bg-blue-300/50 dark:bg-blue-500/20 dark:text-blue-300 dark:hover:bg-blue-500/30">
                                ✕
                            </button>
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
                                <input type="number" min="1" step="0.01" id="amount" name="amount" placeholder="0.00" class="w-full rounded-xl border-2 border-slate-300 bg-white py-3 pl-8 pr-4 text-lg font-semibold text-slate-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                            </div>
                        </div>

                        <!-- Amount Summary -->
                        <div class="rounded-xl bg-blue-50 p-4 dark:bg-blue-500/10">
                            <div class="flex items-center justify-between">
                                <div>
                                    <p class="text-xs uppercase tracking-[0.15em] font-semibold text-blue-600 dark:text-blue-400">Total to Send</p>
                                    <p id="amount-display" class="mt-2 text-2xl font-bold text-blue-900 dark:text-blue-100">{{ $general->cur_sym }}0.00</p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs text-blue-700 dark:text-blue-300">Interbank transfer</p>
                                    <p class="mt-2 text-sm font-semibold text-blue-600 dark:text-blue-400">✓ Secure</p>
                                </div>
                            </div>
                        </div>

                        <!-- Recipient Review -->
                        <div class="rounded-xl border-2 border-slate-200 bg-slate-50 p-4 dark:border-white/10 dark:bg-zinc-900/50">
                            <p class="text-xs uppercase tracking-[0.15em] font-semibold text-slate-500 dark:text-zinc-500">Sending to</p>
                            <p id="amount-recipient-name" class="mt-2 text-sm font-semibold text-slate-900 dark:text-white">--</p>
                            <p id="amount-recipient-account" class="text-xs text-slate-600 dark:text-zinc-400 font-mono">--</p>
                        </div>

                        <!-- Narration -->
                        <div class="space-y-2">
                            <label for="narration" class="block text-sm font-semibold text-slate-700 dark:text-zinc-300">
                                Description (Optional)
                            </label>
                            <textarea id="narration"
                                      name="narration" 
                                      class="w-full rounded-xl border-2 border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-blue-500 focus:ring-2 focus:ring-blue-500/20 dark:border-white/10 dark:bg-zinc-950 dark:text-white"
                                      rows="2"
                                      placeholder="Optional: Transfer description"></textarea>
                        </div>

                        <!-- Save Beneficiary -->
                        <label class="flex items-center gap-3 rounded-xl border-2 border-slate-300 bg-white p-4 cursor-pointer transition hover:border-sky-300 dark:border-white/10 dark:bg-zinc-950 dark:hover:border-sky-500/30">
                            <input type="checkbox" id="save-beneficiary" name="save_beneficiary" value="1" class="h-5 w-5 rounded border-slate-300 text-blue-600 focus:ring-2 focus:ring-blue-500/20 dark:border-white/20 dark:bg-zinc-900">
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
                            <button type="submit" id="submit-btn" class="rounded-full bg-slate-950 px-8 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:bg-slate-400 disabled:text-slate-200 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200 dark:disabled:bg-slate-600 dark:disabled:text-slate-300" disabled>Transfer Now</button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Saved Recipients Sidebar -->
            <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-white/5">
                <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500 dark:text-zinc-500">⭐ Saved List</p>
                <div class="mt-5 space-y-2">
                    @forelse ($beneficiaries as $data)
                        <div class="flex items-center justify-between rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 dark:border-white/10 dark:bg-zinc-900/70">
                            <button type="button" onclick="selectBeneficiary('{{ $data->account_number }}', '{{ $data->name ?? 'N/A' }}')" class="flex-1 text-left">
                                <p class="text-sm font-semibold text-slate-900 dark:text-white">{{ $data->name ?? 'N/A' }}</p>
                                <p class="text-xs text-slate-500 dark:text-zinc-500">{{ $data->account_number }}</p>
                            </button>
                        </div>
                    @empty
                        <p class="text-center text-xs text-slate-500 dark:text-zinc-500 py-4">No saved recipients</p>
                    @endforelse
                </div>
            </div>
        </section>

        <!-- Transfer History -->
        @if($transfers->count() > 0)
        <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-white/5">
            <h3 class="mb-6 text-xl font-semibold text-slate-900 dark:text-white">📋 Transfer History</h3>
            
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b-2 border-slate-200 dark:border-white/10">
                            <th class="px-4 py-3 text-left font-semibold text-slate-700 dark:text-zinc-300">Date</th>
                            <th class="px-4 py-3 text-left font-semibold text-slate-700 dark:text-zinc-300">Recipient</th>
                            <th class="px-4 py-3 text-right font-semibold text-slate-700 dark:text-zinc-300">Amount</th>
                            <th class="px-4 py-3 text-center font-semibold text-slate-700 dark:text-zinc-300">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                        @foreach($transfers as $transfer)
                            <tr class="hover:bg-slate-50 dark:hover:bg-white/5">
                                <td class="px-4 py-3 text-slate-600 dark:text-zinc-400">{{ showDateTime($transfer->created_at) }}</td>
                                <td class="px-4 py-3">
                                    @php
                                        $details = json_decode($transfer->details, true);
                                    @endphp
                                    <p class="font-medium text-slate-900 dark:text-white">{{ $details['account_name'] ?? 'N/A' }}</p>
                                    <p class="text-xs text-slate-500 dark:text-zinc-500">{{ $details['account_number'] ?? '' }}</p>
                                </td>
                                <td class="px-4 py-3 text-right font-semibold text-slate-900 dark:text-white">{{ $general->cur_sym }}{{ showAmount($transfer->amount) }}</td>
                                <td class="px-4 py-3 text-center">
                                    @if($transfer->status == 1)
                                        <span class="inline-flex rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-500/20 dark:text-emerald-300">✓ Completed</span>
                                    @elseif($transfer->status == 0)
                                        <span class="inline-flex rounded-full bg-amber-100 px-3 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-500/20 dark:text-amber-300">⏳ Pending</span>
                                    @else
                                        <span class="inline-flex rounded-full bg-red-100 px-3 py-1 text-xs font-semibold text-red-700 dark:bg-red-500/20 dark:text-red-300">✕ Failed</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            @if($transfers->hasPages())
            <div class="mt-6">
                {{ $transfers->links() }}
            </div>
            @endif
        </section>
        @else
        <section class="rounded-[2rem] border border-slate-200 bg-white p-12 text-center shadow-sm dark:border-white/10 dark:bg-white/5">
            <p class="text-slate-600 dark:text-zinc-400">No transfer history yet. Make your first transfer to get started.</p>
        </section>
        @endif
    </div>

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
                };
                
                // DOM Elements
                const form = document.getElementById('dpay-transfer-form');
                const savedBtn = document.getElementById('saved-btn');
                const newBtn = document.getElementById('new-btn');
                const typeInput = document.getElementById('recipient-type-input');
                const savedContainer = document.getElementById('saved-beneficiary-container');
                const newContainer = document.getElementById('new-recipient-container');
                const beneficiarySelect = document.getElementById('beneficiary-saved');
                const phoneSearch = document.getElementById('phone-search');
                const recipientInfoContainer = document.getElementById('recipient-info-container');
                const recipientName = document.getElementById('recipient-name');
                const recipientAccount = document.getElementById('recipient-account');
                const recipientBank = document.getElementById('recipient-bank');
                const clearBtn = document.getElementById('clear-recipient-btn');
                const step2Container = document.getElementById('step-2-container');
                const amountInput = document.getElementById('amount');
                const amountDisplay = document.getElementById('amount-display');
                const amountRecipientName = document.getElementById('amount-recipient-name');
                const amountRecipientAccount = document.getElementById('amount-recipient-account');
                const submitBtn = document.getElementById('submit-btn');
                const resetBtn = document.getElementById('reset-form-btn');
                const formStatus = document.getElementById('form-status');
                
                // State
                let selectedRecipient = null;
                let selectedRecipientType = 'saved';
                let recipientDetails = {};
                
                const money = (value) => `${config.currency}${Number(value || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
                
                // Recipient Type Selection
                savedBtn.addEventListener('click', () => {
                    selectedRecipientType = 'saved';
                    typeInput.value = '1';
                    savedBtn.classList.add('is-active', 'border-blue-300', 'bg-blue-50', 'dark:border-blue-500/30', 'dark:bg-blue-500/10');
                    savedBtn.classList.remove('border-slate-300', 'bg-white', 'hover:border-sky-300', 'dark:border-white/10', 'dark:bg-zinc-950');
                    newBtn.classList.remove('is-active', 'border-blue-300', 'bg-blue-50', 'dark:border-blue-500/30', 'dark:bg-blue-500/10');
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
                    newBtn.classList.add('is-active', 'border-blue-300', 'bg-blue-50', 'dark:border-blue-500/30', 'dark:bg-blue-500/10');
                    newBtn.classList.remove('border-slate-300', 'bg-white', 'hover:border-sky-300', 'dark:border-white/10', 'dark:bg-zinc-950');
                    savedBtn.classList.remove('is-active', 'border-blue-300', 'bg-blue-50', 'dark:border-blue-500/30', 'dark:bg-blue-500/10');
                    savedBtn.classList.add('border-slate-300', 'bg-white', 'hover:border-sky-300', 'dark:border-white/10', 'dark:bg-zinc-950');
                    newContainer.classList.remove('hidden');
                    savedContainer.classList.add('hidden');
                    selectedRecipient = null;
                    recipientInfoContainer.classList.add('hidden');
                    step2Container.classList.add('hidden');
                    phoneSearch.focus();
                    updateSubmitButton();
                });
                
                // Saved Beneficiary Selection
                beneficiarySelect.addEventListener('change', (e) => {
                    if (e.target.value) {
                        selectedRecipient = e.target.value;
                        const option = e.target.selectedOptions[0];
                        const text = option.textContent;
                        recipientName.textContent = text.split('-')[0].trim() || text;
                        recipientAccount.textContent = e.target.value;
                        recipientBank.textContent = 'Dpay Network';
                        recipientInfoContainer.classList.remove('hidden');
                        step2Container.classList.remove('hidden');
                        amountRecipientName.textContent = recipientName.textContent;
                        amountRecipientAccount.textContent = e.target.value;
                        formStatus.textContent = '✓ Recipient selected. Enter amount to proceed.';
                        resetBtn.classList.remove('hidden');
                    } else {
                        selectedRecipient = null;
                        recipientInfoContainer.classList.add('hidden');
                        step2Container.classList.add('hidden');
                        formStatus.textContent = 'Select a saved recipient to continue...';
                        resetBtn.classList.add('hidden');
                    }
                    updateSubmitButton();
                });
                
                // Phone/Account Search
                phoneSearch.addEventListener('input', (e) => {
                    const value = e.target.value.trim();
                    if (!value || value.length < 2) {
                        selectedRecipient = null;
                        recipientInfoContainer.classList.add('hidden');
                        step2Container.classList.add('hidden');
                        updateSubmitButton();
                        return;
                    }
                    
                    // Validate and set recipient (simplified for demo)
                    selectedRecipient = value;
                    recipientDetails = {
                        phone: value,
                        name: 'Account Holder',
                        bank: 'Dpay Network'
                    };
                    
                    recipientName.textContent = recipientDetails.name;
                    recipientAccount.textContent = value;
                    recipientBank.textContent = recipientDetails.bank;
                    recipientInfoContainer.classList.remove('hidden');
                    step2Container.classList.remove('hidden');
                    amountRecipientName.textContent = recipientDetails.name;
                    amountRecipientAccount.textContent = value;
                    formStatus.textContent = '✓ Recipient found. Enter amount to proceed.';
                    resetBtn.classList.remove('hidden');
                    updateSubmitButton();
                });
                
                // Clear Recipient
                clearBtn.addEventListener('click', () => {
                    selectedRecipient = null;
                    beneficiarySelect.value = '';
                    phoneSearch.value = '';
                    recipientInfoContainer.classList.add('hidden');
                    step2Container.classList.add('hidden');
                    amountInput.value = '';
                    amountDisplay.textContent = money(0);
                    formStatus.textContent = selectedRecipientType === 'saved' ? 'Select a saved recipient to continue...' : 'Enter phone number to continue...';
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
                    phoneSearch.value = '';
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
                        formStatus.textContent = '✓ Ready to transfer. Review and proceed.';
                    } else if (hasRecipient) {
                        submitBtn.disabled = true;
                        formStatus.textContent = 'Enter amount to proceed...';
                    } else {
                        submitBtn.disabled = true;
                        formStatus.textContent = selectedRecipientType === 'saved' ? 'Select a saved recipient to continue...' : 'Enter phone number to continue...';
                    }
                };
                
                // Initialize
                if (!form) return;
                updateSubmitButton();
            })();

            function selectBeneficiary(account, name) {
                document.getElementById('beneficiary-saved').value = account;
                document.getElementById('beneficiary-saved').dispatchEvent(new Event('change'));
            }
        </script>
    @endpush
@endsection
