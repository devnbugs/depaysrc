@extends($activeTemplate.'layouts.dashboard')

@section('content')
@php
    $statusLabel = (int) $card->status === 0 ? 'Blocked' : 'Active';
    $statusClasses = (int) $card->status === 0
        ? 'bg-amber-50 text-amber-700 ring-amber-200 dark:bg-amber-500/10 dark:text-amber-200 dark:ring-amber-500/20'
        : 'bg-emerald-50 text-emerald-700 ring-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-200 dark:ring-emerald-500/20';
@endphp

<section class="space-y-6">
    <div class="hero-surface p-6 sm:p-8">
        <div class="grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
            <div class="space-y-4">
                <div class="flex flex-wrap gap-2">
                    <span class="section-kicker">Card details</span>
                    <span class="section-kicker">{{ str_replace('_', ' ', $card->card_type) }}</span>
                </div>
                <div>
                    <h2 class="text-3xl font-semibold tracking-tight text-slate-950 dark:text-white">{{ $card->masked_pan }}</h2>
                    <p class="mt-3 max-w-2xl section-copy">This view keeps the card live status, customer mapping, expiry, and the latest balance snapshot in one place. If the admin disables the service, this page stays readable but new actions are blocked.</p>
                </div>
                <div class="flex flex-wrap gap-3">
                    <span class="inline-flex rounded-full px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] ring-1 {{ $statusClasses }}">{{ $statusLabel }}</span>
                    @if (!$serviceEnabled)
                        <span class="inline-flex rounded-full bg-rose-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-rose-700 ring-1 ring-rose-200 dark:bg-rose-500/10 dark:text-rose-200 dark:ring-rose-500/20">Card unavailable</span>
                    @endif
                </div>
            </div>

            <div class="rounded-[28px] bg-gradient-to-br from-slate-950 via-slate-900 to-sky-900 p-6 text-white shadow-lg">
                <p class="text-xs uppercase tracking-[0.28em] text-sky-200/80">Interswitch virtual card</p>
                <div class="mt-10 space-y-3">
                    <p class="text-2xl font-semibold tracking-[0.28em]">{{ $card->pan }}</p>
                    <div class="flex items-end justify-between gap-4">
                        <div>
                            <p class="text-[11px] uppercase tracking-[0.22em] text-sky-100/70">Cardholder</p>
                            <p class="mt-2 text-lg font-semibold">{{ $card->name_on_card }}</p>
                        </div>
                        <div class="text-right">
                            <p class="text-[11px] uppercase tracking-[0.22em] text-sky-100/70">Expiry / CVV</p>
                            <p class="mt-2 text-lg font-semibold">{{ $card->expiry_date }} / {{ $card->cvv2 }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if ($balanceError)
        <div class="rounded-3xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800 dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-100">
            Balance refresh could not complete right now: {{ $balanceError }}
        </div>
    @endif

    <div class="grid gap-6 xl:grid-cols-[0.95fr_1.05fr]">
        <section class="panel-card p-6">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="section-kicker">Actions</p>
                    <h3 class="mt-3 section-title">Control this card safely.</h3>
                </div>
            </div>

            <div class="mt-6 space-y-3">
                @if ($serviceEnabled)
                    @if ((int) $card->status === 0)
                        <a href="{{ route('user.card.unblock', $card->reference) }}" class="inline-flex h-11 items-center rounded-full bg-emerald-600 px-5 text-sm font-semibold text-white transition hover:bg-emerald-700">
                            Unblock card
                        </a>
                    @else
                        <a href="{{ route('user.card.block', $card->reference) }}" class="inline-flex h-11 items-center rounded-full bg-amber-500 px-5 text-sm font-semibold text-white transition hover:bg-amber-600">
                            Block card
                        </a>
                    @endif
                @else
                    <div class="rounded-3xl border border-dashed border-slate-200 p-5 text-sm text-slate-500 dark:border-white/10 dark:text-zinc-400">
                        Card unavailable. The admin has disabled this service, so live actions are paused.
                    </div>
                @endif

                <a href="{{ route('user.vcard') }}" class="inline-flex h-11 items-center rounded-full border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 transition hover:border-sky-200 hover:text-sky-700 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-sky-500/30 dark:hover:text-sky-300">
                    Back to cards
                </a>
            </div>
        </section>

        <section class="panel-card p-6">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <p class="section-kicker">Snapshot</p>
                    <h3 class="mt-3 section-title">Current identifiers and balance state.</h3>
                </div>
            </div>

            <div class="mt-6 grid gap-4 sm:grid-cols-2">
                <article class="rounded-3xl border border-slate-200/80 bg-slate-50 p-4 dark:border-white/10 dark:bg-white/5">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400 dark:text-zinc-500">Available balance</p>
                    <p class="mt-3 text-2xl font-semibold text-slate-950 dark:text-white">{{ $general->cur_sym }}{{ number_format((float) ($card->available_balance ?? 0), 2) }}</p>
                </article>
                <article class="rounded-3xl border border-slate-200/80 bg-slate-50 p-4 dark:border-white/10 dark:bg-white/5">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400 dark:text-zinc-500">Ledger balance</p>
                    <p class="mt-3 text-2xl font-semibold text-slate-950 dark:text-white">{{ $general->cur_sym }}{{ number_format((float) ($card->ledger_balance ?? 0), 2) }}</p>
                </article>
                <article class="rounded-3xl border border-slate-200/80 bg-slate-50 p-4 dark:border-white/10 dark:bg-white/5">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400 dark:text-zinc-500">Customer ID</p>
                    <p class="mt-3 text-sm font-semibold text-slate-950 dark:text-white">{{ $card->customer_id ?: 'Not returned' }}</p>
                </article>
                <article class="rounded-3xl border border-slate-200/80 bg-slate-50 p-4 dark:border-white/10 dark:bg-white/5">
                    <p class="text-xs uppercase tracking-[0.2em] text-slate-400 dark:text-zinc-500">Sequence / Synced</p>
                    <p class="mt-3 text-sm font-semibold text-slate-950 dark:text-white">{{ $card->card_sequence_number ?: 'N/A' }} · {{ optional($card->last_synced_at)->diffForHumans() ?: 'Not synced yet' }}</p>
                </article>
                @if ($card->account_id)
                    <article class="rounded-3xl border border-slate-200/80 bg-slate-50 p-4 dark:border-white/10 dark:bg-white/5 sm:col-span-2">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400 dark:text-zinc-500">Linked debit account</p>
                        <p class="mt-3 text-sm font-semibold text-slate-950 dark:text-white">{{ $card->account_id }} · Type {{ $card->account_type ?: data_get($settings, 'account_type', '20') }}</p>
                    </article>
                @endif
            </div>
        </section>
    </div>
</section>
@endsection
