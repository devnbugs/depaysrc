@extends($activeTemplate.'layouts.dashboard')

@section('content')
@php
    $selectedType = old('card_type', data_get($settings, 'default_type', 'PREPAID_NEW'));
    $creationFee = (float) data_get($settings, 'creation_fee', 0);
@endphp

<section class="space-y-6">
    <div class="hero-surface p-6 sm:p-8">
        <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
            <div class="space-y-4">
                <div class="flex flex-wrap gap-2">
                    <span class="section-kicker">Virtual cards</span>
                    <span class="section-kicker">Interswitch</span>
                </div>
                <div>
                    <h2 class="text-3xl font-semibold tracking-tight text-slate-950 dark:text-white">Create and manage your virtual cards.</h2>
                    <p class="mt-3 max-w-2xl section-copy">Only users with a verified email and a locked BVN or NIN profile can request new cards. When the admin disables the service, this page switches to an unavailable state automatically.</p>
                </div>
                <div class="flex flex-wrap gap-3 text-sm text-slate-600 dark:text-zinc-300">
                    <span class="rounded-full border border-slate-200 bg-white px-4 py-2 dark:border-white/10 dark:bg-zinc-900">Wallet: {{ $general->cur_sym }}{{ showAmount($user->balance) }}</span>
                    <span class="rounded-full border border-slate-200 bg-white px-4 py-2 dark:border-white/10 dark:bg-zinc-900">Creation fee: {{ $general->cur_sym }}{{ number_format($creationFee, 2) }}</span>
                    <span class="rounded-full border border-slate-200 bg-white px-4 py-2 dark:border-white/10 dark:bg-zinc-900">Types: {{ implode(', ', array_values($availableTypes)) }}</span>
                </div>
            </div>

            <div class="rounded-3xl border border-slate-200/80 bg-white/85 p-5 shadow-sm dark:border-white/10 dark:bg-white/5">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-sky-600 dark:text-sky-400">Availability</p>
                @if (!$settings['enabled'])
                    <h3 class="mt-3 text-2xl font-semibold text-slate-950 dark:text-white">Card unavailable</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-600 dark:text-zinc-300">The admin team has disabled virtual card requests for now.</p>
                @elseif (!$canCreate)
                    <h3 class="mt-3 text-2xl font-semibold text-slate-950 dark:text-white">Finish your checks first</h3>
                    <ul class="mt-4 space-y-2 text-sm text-slate-600 dark:text-zinc-300">
                        @foreach ($blockers as $blocker)
                            <li class="rounded-2xl border border-amber-200 bg-amber-50 px-4 py-3 dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-100">{{ $blocker }}</li>
                        @endforeach
                    </ul>
                @else
                    <h3 class="mt-3 text-2xl font-semibold text-slate-950 dark:text-white">Ready to issue</h3>
                    <p class="mt-3 text-sm leading-6 text-slate-600 dark:text-zinc-300">Your profile passes the current card rules, so you can request a new Interswitch card below.</p>
                @endif
            </div>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
        <section class="panel-card p-6">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="section-kicker">New card</p>
                    <h3 class="mt-3 section-title">Request a fresh virtual card.</h3>
                </div>
            </div>

            @if ($settings['enabled'] && $canCreate)
                <form action="" method="post" class="mt-6 space-y-5" id="virtual-card-form">
                    @csrf
                    <div class="grid gap-5 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Card type</label>
                            <select name="card_type" id="card_type" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-zinc-900 dark:text-white">
                                @foreach ($availableTypes as $value => $label)
                                    <option value="{{ $value }}" @selected($selectedType === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Initial card PIN</label>
                            <input type="password" name="initial_pin" maxlength="4" inputmode="numeric" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-zinc-900 dark:text-white" placeholder="Optional 4 digits" value="{{ old('initial_pin') }}">
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Cardholder name</label>
                            <input type="text" class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 text-slate-900 dark:border-white/10 dark:bg-white/5 dark:text-white" value="{{ trim($user->fullname) }}" readonly>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Verified identity</label>
                            <input type="text" class="w-full rounded-2xl border border-slate-300 bg-slate-50 px-4 py-3 text-slate-900 dark:border-white/10 dark:bg-white/5 dark:text-white" value="{{ $user->BVN ?: $user->NIN }}" readonly>
                        </div>
                    </div>

                    <div id="linked-account-wrap" class="{{ $selectedType === 'DEBIT_EXISTING_ACCOUNT' ? '' : 'hidden' }}">
                        <label class="mb-2 block text-sm font-semibold text-slate-700 dark:text-zinc-300">Linked issuer account number</label>
                        <input type="text" name="linked_account_number" class="w-full rounded-2xl border border-slate-300 bg-white px-4 py-3 text-slate-900 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-zinc-900 dark:text-white" value="{{ old('linked_account_number', $user->accountNumber) }}" placeholder="Required for debit virtual cards">
                        <p class="mt-2 text-xs text-slate-500 dark:text-zinc-400">Debit card creation needs an existing issuer account number that belongs to the user.</p>
                    </div>

                    <div class="rounded-3xl border border-slate-200/80 bg-slate-50 p-4 text-sm text-slate-600 dark:border-white/10 dark:bg-white/5 dark:text-zinc-300">
                        The service fee is {{ $general->cur_sym }}{{ number_format($creationFee, 2) }}. This card flow is powered by Interswitch Card 360 and is only exposed to profiles with a verified email plus locked BVN or NIN identity.
                    </div>

                    <button type="submit" class="inline-flex h-11 items-center rounded-full bg-slate-950 px-5 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                        Create card
                    </button>
                </form>
            @else
                <div class="mt-6 rounded-3xl border border-dashed border-slate-200 p-6 text-sm leading-6 text-slate-500 dark:border-white/10 dark:text-zinc-400">
                    Card creation is unavailable right now. Once the service is enabled and your profile clears all required checks, the request form will appear here automatically.
                </div>
            @endif
        </section>

        <section class="panel-card p-6">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="section-kicker">Your cards</p>
                    <h3 class="mt-3 section-title">Issued cards on this profile.</h3>
                </div>
                <span class="text-sm text-slate-500 dark:text-zinc-400">{{ $cards->count() }} card(s)</span>
            </div>

            <div class="mt-6 space-y-4">
                @forelse ($cards as $card)
                    <a href="{{ route('user.view.card', $card->reference) }}" class="block rounded-3xl border border-slate-200/80 bg-slate-50 p-5 transition hover:border-sky-300 hover:bg-sky-50/60 dark:border-white/10 dark:bg-white/5 dark:hover:border-sky-500/30 dark:hover:bg-sky-500/10">
                        <div class="flex items-start justify-between gap-4">
                            <div class="space-y-2">
                                <p class="text-xs uppercase tracking-[0.22em] text-slate-500 dark:text-zinc-400">{{ str_replace('_', ' ', $card->card_type) }}</p>
                                <h4 class="text-xl font-semibold text-slate-950 dark:text-white">{{ $card->masked_pan ?: 'Card number hidden' }}</h4>
                                <p class="text-sm text-slate-600 dark:text-zinc-300">{{ $card->name_on_card }}</p>
                                @if ($card->customer_id)
                                    <p class="text-xs uppercase tracking-[0.18em] text-slate-400 dark:text-zinc-500">Customer {{ $card->customer_id }}</p>
                                @endif
                            </div>

                            @if ((int) $card->status === 0)
                                <span class="inline-flex rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-amber-700 ring-1 ring-amber-200 dark:bg-amber-500/10 dark:text-amber-200 dark:ring-amber-500/20">Blocked</span>
                            @else
                                <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700 ring-1 ring-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-200 dark:ring-emerald-500/20">Active</span>
                            @endif
                        </div>
                    </a>
                @empty
                    <div class="rounded-3xl border border-dashed border-slate-200 p-6 text-sm text-slate-500 dark:border-white/10 dark:text-zinc-400">
                        You have not issued any virtual cards yet.
                    </div>
                @endforelse
            </div>
        </section>
    </div>
</section>
@endsection

@push('script')
    <script>
        (function () {
            const typeField = document.getElementById('card_type');
            const linkedAccountWrap = document.getElementById('linked-account-wrap');

            if (!typeField || !linkedAccountWrap) {
                return;
            }

            const syncDebitField = () => {
                linkedAccountWrap.classList.toggle('hidden', typeField.value !== 'DEBIT_EXISTING_ACCOUNT');
            };

            syncDebitField();
            typeField.addEventListener('change', syncDebitField);
        })();
    </script>
@endpush
