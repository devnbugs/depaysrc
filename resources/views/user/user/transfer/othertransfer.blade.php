@extends($activeTemplate.'layouts.dashboard')

@section('content')
    <div class="space-y-6">
        <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-white/5">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="max-w-2xl">
                    <p class="text-xs font-semibold uppercase tracking-[0.26em] text-sky-600 dark:text-sky-400">Wallet To Bank</p>
                    <h2 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950 dark:text-white">{{ $pageTitle }}</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-600 dark:text-zinc-400">
                        Complete each step in order: beneficiary, amount, then PIN authorization.
                    </p>
                </div>
                <div class="grid gap-3 sm:grid-cols-3">
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-white/10 dark:bg-zinc-900/70">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-500 dark:text-zinc-500">Wallet</p>
                        <p class="mt-2 text-lg font-semibold text-slate-950 dark:text-zinc-100">{{ $general->cur_sym }}{{ showAmount($user->balance) }}</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-white/10 dark:bg-zinc-900/70">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-500 dark:text-zinc-500">Fee</p>
                        <p class="mt-2 text-lg font-semibold text-slate-950 dark:text-zinc-100">{{ showAmount($general->transferfee) }}%</p>
                    </div>
                    <div class="rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 dark:border-white/10 dark:bg-zinc-900/70">
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
                            {{ $bankLoadError }}
                        </div>
                    @endif

                    <div class="grid gap-3 md:grid-cols-3">
                        <div class="transfer-stage is-active" data-stage="1"><span>1</span><div><p>Beneficiary</p><small>Account number, bank, account name</small></div></div>
                        <div class="transfer-stage" data-stage="2"><span>2</span><div><p>Amount</p><small>Amount and fee preview</small></div></div>
                        <div class="transfer-stage" data-stage="3"><span>3</span><div><p>PIN Auth</p><small>Authorize and confirm</small></div></div>
                    </div>

                    <form method="POST" action="{{ route('user.othertransfer') }}" id="local-transfer-form" data-confirm-form="1" data-busy-form data-busy-message="Submitting local transfer. Please wait." data-confirm-title="Confirm bank transfer" data-confirm-message="Please review the beneficiary, amount, and narration before sending this transfer." data-confirm-accept-text="Send transfer" class="mt-6 space-y-5">
                        @csrf

                        <section class="transfer-step-card" data-step-panel="1">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900 dark:text-zinc-100">Step 1: Beneficiary Details</p>
                                    <p class="mt-1 text-sm text-slate-600 dark:text-zinc-400">Enter the account number first, then select the bank. Auto resolve starts once both are ready.</p>
                                </div>
                                <span class="rounded-full bg-sky-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-sky-700 dark:bg-sky-500/10 dark:text-sky-300">Active</span>
                            </div>

                            <div class="mt-5 grid gap-5 lg:grid-cols-3">
                                <div class="space-y-2">
                                    <label for="account-number" class="text-sm font-semibold text-slate-700 dark:text-zinc-300">Account number</label>
                                    <input type="text" inputmode="numeric" maxlength="10" id="account-number" name="account_number" value="{{ old('account_number') }}" placeholder="0123456789" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-sky-500 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                                </div>
                                <div class="space-y-2">
                                    <label for="bank-search" class="text-sm font-semibold text-slate-700 dark:text-zinc-300">Select bank name</label>
                                    <div class="relative">
                                        <input type="text" id="bank-search" value="{{ old('bank_name') }}" autocomplete="off" placeholder="Search and select bank" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-sky-500 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                                        <div id="bank-suggestions" class="absolute z-20 mt-2 hidden w-full rounded-2xl border border-slate-200 bg-white p-2 shadow-xl dark:border-white/10 dark:bg-zinc-950"></div>
                                    </div>
                                    <input type="hidden" name="bank_name" id="bank-name" value="{{ old('bank_name') }}">
                                    <input type="hidden" name="bank_code" id="bank-code" value="{{ old('bank_code') }}">
                                </div>
                                <div class="space-y-2">
                                    <label for="account-name" class="text-sm font-semibold text-slate-700 dark:text-zinc-300">Account name</label>
                                    <input type="text" id="account-name" name="account_name" value="{{ old('account_name') }}" readonly placeholder="Resolved account name" class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 text-sm text-slate-900 outline-none dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-100">
                                </div>
                            </div>

                            <div class="mt-5 flex items-center justify-between gap-3 border-t border-slate-200 pt-5 dark:border-white/10">
                                <p id="resolve-status" class="text-sm text-slate-600 dark:text-zinc-400">Choose a bank and enter a 10-digit account number to start.</p>
                                <div class="flex items-center gap-3">
                                    <div id="resolve-indicator" class="hidden items-center gap-2 rounded-full bg-sky-100 px-3 py-2 text-xs font-semibold uppercase tracking-[0.2em] text-sky-700 dark:bg-sky-500/10 dark:text-sky-300">
                                        <span class="h-2 w-2 animate-pulse rounded-full bg-current"></span>
                                        Resolving
                                    </div>
                                    <button type="button" id="step-1-next" class="inline-flex items-center rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:bg-slate-400 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200" disabled>Proceed to Amount</button>
                                </div>
                            </div>
                        </section>

                        <section class="transfer-step-card hidden" data-step-panel="2">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900 dark:text-zinc-100">Step 2: Amount</p>
                                    <p class="mt-1 text-sm text-slate-600 dark:text-zinc-400">Set the transfer amount and check the wallet debit before you continue.</p>
                                </div>
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-700 dark:bg-white/10 dark:text-zinc-300">Pending</span>
                            </div>

                            <div class="mt-5 grid gap-5 lg:grid-cols-2">
                                <div class="space-y-2">
                                    <label for="amount" class="text-sm font-semibold text-slate-700 dark:text-zinc-300">Amount</label>
                                    <input type="number" min="1" step="0.01" id="amount" name="amount" value="{{ old('amount') }}" placeholder="0.00" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-sky-500 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                                </div>
                                <div class="space-y-2">
                                    <label for="narration" class="text-sm font-semibold text-slate-700 dark:text-zinc-300">Narration</label>
                                    <input type="text" id="narration" name="narration" maxlength="191" value="{{ old('narration', 'Local transfer') }}" placeholder="Optional narration" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-sky-500 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                                </div>
                            </div>

                            <div class="mt-5 grid gap-3 sm:grid-cols-3">
                                <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-white/10 dark:bg-white/5">
                                    <p class="text-xs uppercase tracking-[0.2em] text-slate-500 dark:text-zinc-500">Charge</p>
                                    <p id="fee-preview" class="mt-2 text-lg font-semibold text-slate-950 dark:text-zinc-100">{{ $general->cur_sym }}0.00</p>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-white/10 dark:bg-white/5">
                                    <p class="text-xs uppercase tracking-[0.2em] text-slate-500 dark:text-zinc-500">Total Debit</p>
                                    <p id="total-preview" class="mt-2 text-lg font-semibold text-slate-950 dark:text-zinc-100">{{ $general->cur_sym }}0.00</p>
                                </div>
                                <div class="rounded-2xl border border-slate-200 bg-white px-4 py-3 dark:border-white/10 dark:bg-white/5">
                                    <p class="text-xs uppercase tracking-[0.2em] text-slate-500 dark:text-zinc-500">Resolved By</p>
                                    <p id="provider-preview" class="mt-2 text-sm font-semibold text-slate-950 dark:text-zinc-100">Awaiting lookup</p>
                                </div>
                            </div>

                            <div class="mt-5 flex items-center justify-between gap-3 border-t border-slate-200 pt-5 dark:border-white/10">
                                <button type="button" data-step-back="1" class="inline-flex items-center rounded-full border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:text-slate-900 dark:border-white/10 dark:bg-zinc-950 dark:text-zinc-200 dark:hover:border-white/20 dark:hover:text-white">Back to Beneficiary</button>
                                <button type="button" id="step-2-next" class="inline-flex items-center rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:bg-slate-400 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200" disabled>Proceed to PIN Auth</button>
                            </div>
                        </section>

                        <section class="transfer-step-card hidden" data-step-panel="3">
                            <div class="flex items-start justify-between gap-4">
                                <div>
                                    <p class="text-sm font-semibold text-slate-900 dark:text-zinc-100">Step 3: PIN Auth</p>
                                    <p class="mt-1 text-sm text-slate-600 dark:text-zinc-400">Authorize the transfer, then confirm the popup before submission.</p>
                                </div>
                                <span class="rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.2em] text-slate-700 dark:bg-white/10 dark:text-zinc-300">Final</span>
                            </div>

                            @if ($settings['require_pin'] && (int) $user->pin_state === 1)
                                <div class="mt-5 space-y-2">
                                    <label for="pin-code" class="text-sm font-semibold text-slate-700 dark:text-zinc-300">Authorization PIN</label>
                                    <input type="password" inputmode="numeric" maxlength="4" id="pin-code" name="pin_code" placeholder="Enter your 4-digit PIN" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-sky-500 dark:border-white/10 dark:bg-zinc-950 dark:text-white">
                                </div>
                            @else
                                <div class="mt-5 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm text-slate-600 dark:border-white/10 dark:bg-white/5 dark:text-zinc-400">
                                    PIN authorization is not required for this account right now. Final confirmation will still appear before submission.
                                </div>
                            @endif

                            <div class="mt-5 flex items-center justify-between gap-3 border-t border-slate-200 pt-5 dark:border-white/10">
                                <button type="button" data-step-back="2" class="inline-flex items-center rounded-full border border-slate-300 bg-white px-5 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-400 hover:text-slate-900 dark:border-white/10 dark:bg-zinc-950 dark:text-zinc-200 dark:hover:border-white/20 dark:hover:text-white">Back to Amount</button>
                                <button type="submit" id="submit-transfer" class="inline-flex items-center rounded-full bg-slate-950 px-6 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:bg-slate-400 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200" disabled>Confirm and Send Transfer</button>
                            </div>
                        </section>
                    </form>
                </div>

                <div class="space-y-6">
                    <div class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-white/5">
                        <p class="text-xs font-semibold uppercase tracking-[0.24em] text-slate-500 dark:text-zinc-500">Recent Transfers</p>
                        <div class="mt-5 space-y-3">
                            @forelse ($log as $item)
                                <article class="rounded-2xl border border-slate-200 bg-slate-50 p-4 dark:border-white/10 dark:bg-zinc-900/70">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-semibold text-slate-950 dark:text-white">{{ $item->bank_name ?: 'Bank transfer' }}</p>
                                            <p class="mt-1 text-xs text-slate-500 dark:text-zinc-500">{{ $item->account_name }} • {{ $item->account_number }}</p>
                                        </div>
                                        <span class="rounded-full px-3 py-1 text-xs font-semibold {{ $item->status == 1 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300' : ($item->status == 2 ? 'bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-300' : 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300') }}">{{ $item->status == 1 ? 'Successful' : ($item->status == 2 ? 'Failed' : 'Pending') }}</span>
                                    </div>
                                    <div class="mt-4 flex items-end justify-between gap-3">
                                        <div>
                                            <p class="text-lg font-semibold text-slate-950 dark:text-white">{{ $general->cur_sym }}{{ showAmount($item->amount) }}</p>
                                            <p class="text-xs text-slate-500 dark:text-zinc-500">Charge {{ $general->cur_sym }}{{ showAmount($item->charge) }}</p>
                                        </div>
                                        <div class="text-right text-xs text-slate-500 dark:text-zinc-500">
                                            <p>{{ $item->provider ? strtoupper($item->provider) : 'ROUTING' }}</p>
                                            <p class="mt-1">{{ $item->created_at ? $item->created_at->format('d M Y, h:i A') : '' }}</p>
                                        </div>
                                    </div>
                                </article>
                            @empty
                                <div class="rounded-2xl border border-dashed border-slate-300 px-4 py-8 text-center text-sm text-slate-500 dark:border-white/10 dark:text-zinc-500">No local transfer history yet.</div>
                            @endforelse
                        </div>
                        @if ($log->hasPages())
                            <div class="mt-5">{{ $log->links() }}</div>
                        @endif
                    </div>
                </div>
            </section>
        @endif
    </div>
@endsection

@push('style')
    <style>
        .transfer-stage{display:flex;gap:.875rem;align-items:center;padding:1rem;border-radius:1.25rem;border:1px solid rgba(148,163,184,.22);background:rgba(248,250,252,.85);color:rgb(71,85,105);transition:180ms ease}.dark .transfer-stage{background:rgba(15,23,42,.65);border-color:rgba(255,255,255,.08);color:rgb(161,161,170)}.transfer-stage span{display:inline-flex;height:2.5rem;width:2.5rem;align-items:center;justify-content:center;border-radius:999px;background:rgba(15,23,42,.08);font-size:.875rem;font-weight:700}.dark .transfer-stage span{background:rgba(255,255,255,.08)}.transfer-stage p{font-size:.95rem;font-weight:700;color:rgb(15,23,42)}.dark .transfer-stage p{color:#fff}.transfer-stage small{display:block;margin-top:.2rem;font-size:.72rem;line-height:1.4;color:inherit}.transfer-stage.is-active,.transfer-stage.is-complete{border-color:rgba(14,165,233,.35);background:linear-gradient(135deg,rgba(14,165,233,.12),rgba(56,189,248,.03));color:rgb(3,105,161)}.dark .transfer-stage.is-active,.dark .transfer-stage.is-complete{background:linear-gradient(135deg,rgba(14,165,233,.16),rgba(56,189,248,.05));color:rgb(125,211,252)}.transfer-stage.is-complete span{background:rgb(14,165,233);color:#fff}.transfer-step-card{border:1px solid rgba(148,163,184,.18);border-radius:1.75rem;background:rgba(248,250,252,.75);padding:1.4rem}.dark .transfer-step-card{border-color:rgba(255,255,255,.08);background:rgba(15,23,42,.42)}
    </style>
@endpush

@push('script')
    <script>
        (() => {
            const banks = @json($banks), feeRate = Number(@json((float) $general->transferfee)), currency = @json($general->cur_sym), resolveUrl = @json(route('user.othertransfer.resolve'));
            const form = document.getElementById('local-transfer-form'); if (!form) return;
            const panels = Array.from(document.querySelectorAll('[data-step-panel]')), stages = Array.from(document.querySelectorAll('.transfer-stage'));
            const bankSearch = document.getElementById('bank-search'), bankName = document.getElementById('bank-name'), bankCode = document.getElementById('bank-code'), accountNumber = document.getElementById('account-number'), accountName = document.getElementById('account-name'), amount = document.getElementById('amount'), pinCode = document.getElementById('pin-code'), suggestions = document.getElementById('bank-suggestions'), resolveStatus = document.getElementById('resolve-status'), resolveIndicator = document.getElementById('resolve-indicator'), feePreview = document.getElementById('fee-preview'), totalPreview = document.getElementById('total-preview'), providerPreview = document.getElementById('provider-preview'), step1Next = document.getElementById('step-1-next'), step2Next = document.getElementById('step-2-next'), submitButton = document.getElementById('submit-transfer');
            let selectedBank = bankName.value ? { name: bankName.value, code: bankCode.value || '' } : null, resolved = accountName.value.trim() !== '', resolveSequence = 0, currentStep = 1;
            const money = (value) => `${currency}${Number(value || 0).toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`, digitsOnly = (value) => String(value || '').replace(/\D+/g, '');
            const showStep = (step) => { currentStep = step; panels.forEach((panel) => panel.classList.toggle('hidden', Number(panel.dataset.stepPanel) !== step)); stages.forEach((stage) => { const index = Number(stage.dataset.stage); stage.classList.remove('is-active', 'is-complete'); if (index < step) stage.classList.add('is-complete'); if (index === step) stage.classList.add('is-active'); }); };
            const syncButtons = () => { const amountReady = Number(amount.value || 0) > 0, pinReady = @json($settings['require_pin'] && (int) $user->pin_state === 1) ? (pinCode?.value || '').length === 4 : true; step1Next.disabled = !resolved; step2Next.disabled = !resolved || !amountReady; submitButton.disabled = !resolved || !amountReady || !pinReady; };
            const updateTotals = () => { const value = Number(amount.value || 0), fee = value > 0 ? (value * feeRate) / 100 : 0; feePreview.textContent = money(fee); totalPreview.textContent = money(value + fee); syncButtons(); };
            const setResolvedState = (active, message) => { resolved = active; if (!active) { accountName.value = ''; providerPreview.textContent = 'Awaiting lookup'; } resolveStatus.textContent = message || 'Choose a bank and enter a 10-digit account number to start.'; syncButtons(); };
            const hideSuggestions = () => { suggestions.innerHTML = ''; suggestions.classList.add('hidden'); };
            const chooseBank = (bank) => { selectedBank = bank; bankSearch.value = bank.name; bankName.value = bank.name; bankCode.value = bank.code || ''; hideSuggestions(); setResolvedState(false, 'Bank selected. Enter the 10-digit account number to resolve the beneficiary.'); maybeResolve(); };
            const renderSuggestions = (matches) => { if (!matches.length) return hideSuggestions(); suggestions.innerHTML = matches.map((bank, index) => `<button type="button" class="flex w-full items-center justify-between rounded-xl px-3 py-2 text-left text-sm text-slate-700 transition hover:bg-slate-100 dark:text-zinc-200 dark:hover:bg-white/5" data-bank-index="${index}"><span>${bank.name}</span><small class="text-xs text-slate-400">${bank.code || ''}</small></button>`).join(''); suggestions.classList.remove('hidden'); suggestions.querySelectorAll('[data-bank-index]').forEach((button) => button.addEventListener('click', () => chooseBank(matches[Number(button.dataset.bankIndex)]))); };
            const searchBanks = () => { const query = bankSearch.value.trim().toLowerCase(); bankName.value = ''; bankCode.value = ''; selectedBank = null; setResolvedState(false); if (!query) return hideSuggestions(); const matches = banks.filter((bank) => bank.name.toLowerCase().includes(query) || String(bank.code || '').toLowerCase().includes(query)).slice(0, 8); renderSuggestions(matches); if (matches.length === 1) chooseBank(matches[0]); };
            const maybeResolve = async () => { const account = digitsOnly(accountNumber.value).slice(0, 10); accountNumber.value = account; if (!selectedBank || account.length !== 10) return setResolvedState(false, selectedBank ? 'Enter all 10 digits to resolve this account.' : 'Choose a bank and enter a 10-digit account number to start.'); const currentSequence = ++resolveSequence; resolveIndicator.classList.remove('hidden'); resolveIndicator.classList.add('inline-flex'); resolveStatus.textContent = 'Resolving account details...'; try { const response = await window.axios.post(resolveUrl, { bank_name: selectedBank.name, bank_code: selectedBank.code || '', account_number: account }); if (currentSequence !== resolveSequence) return; const data = response.data?.data || {}; accountName.value = data.account_name || ''; bankName.value = data.bank_name || selectedBank.name; bankCode.value = data.bank_code || selectedBank.code || ''; providerPreview.textContent = (data.resolved_by || 'provider').toUpperCase(); setResolvedState(true, `Account resolved successfully for ${accountName.value}.`); } catch (error) { if (currentSequence !== resolveSequence) return; const message = error?.response?.data?.message || 'Account resolution failed.'; setResolvedState(false, message); window.depayToast?.({ title: 'Resolve failed', message, tone: 'warning' }); } finally { if (currentSequence === resolveSequence) { resolveIndicator.classList.add('hidden'); resolveIndicator.classList.remove('inline-flex'); } } };
            bankSearch.addEventListener('input', searchBanks); bankSearch.addEventListener('focus', searchBanks); accountNumber.addEventListener('input', () => { setResolvedState(false, selectedBank ? 'Enter all 10 digits to resolve this account.' : 'Choose a bank and enter a 10-digit account number to start.'); maybeResolve(); }); amount.addEventListener('input', updateTotals); pinCode?.addEventListener('input', syncButtons);
            step1Next.addEventListener('click', () => { if (!step1Next.disabled) showStep(2); }); step2Next.addEventListener('click', () => { if (!step2Next.disabled) showStep(3); }); document.querySelectorAll('[data-step-back]').forEach((button) => button.addEventListener('click', () => showStep(Number(button.dataset.stepBack))));
            document.addEventListener('click', (event) => { if (!suggestions.contains(event.target) && event.target !== bankSearch) hideSuggestions(); });
            updateTotals(); syncButtons(); showStep(1); if (selectedBank && digitsOnly(accountNumber.value).length === 10 && accountName.value.trim() !== '') { providerPreview.textContent = 'RESTORED'; setResolvedState(true, `Account resolved successfully for ${accountName.value}.`); }
        })();
    </script>
@endpush
