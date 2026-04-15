@extends('admin.layouts.app')

@section('panel')
    <div class="space-y-6">
        <div>
            <p class="section-kicker">Virtual Cards</p>
            <h2 class="mt-2 section-title">Card Details</h2>
            <p class="mt-2 section-copy max-w-2xl">Review the stored Interswitch virtual card data, live balance snapshot, and current action availability.</p>
        </div>

        @if ($balanceError)
            <div class="rounded-2xl border border-amber-200 bg-amber-50 px-5 py-4 text-sm text-amber-800 dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-100">
                Live balance refresh failed: {{ $balanceError }}
            </div>
        @endif

        @if (! $serviceEnabled)
            <div class="rounded-2xl border border-rose-200 bg-rose-50 px-5 py-4 text-sm text-rose-800 dark:border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-100">
                Card unavailable. The admin team has disabled the Interswitch virtual card service in general settings.
            </div>
        @endif

        <div class="grid gap-6 xl:grid-cols-[1.1fr_0.9fr]">
            <div class="panel-card rounded-2xl border border-slate-200 p-6 dark:border-white/10">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="text-xs font-semibold uppercase tracking-[0.22em] text-sky-600 dark:text-sky-400">Issued Card</p>
                        <h3 class="mt-3 text-2xl font-semibold text-slate-950 dark:text-white">{{ $card->masked_pan }}</h3>
                    </div>
                    @if ((int) $card->status === 0)
                        <span class="inline-flex rounded-full bg-amber-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-amber-700 ring-1 ring-amber-200 dark:bg-amber-500/10 dark:text-amber-200 dark:ring-amber-500/20">Blocked</span>
                    @elseif ((int) $card->status === 2)
                        <span class="inline-flex rounded-full bg-slate-100 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-slate-700 ring-1 ring-slate-200 dark:bg-white/10 dark:text-zinc-200 dark:ring-white/10">Archived</span>
                    @else
                        <span class="inline-flex rounded-full bg-emerald-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700 ring-1 ring-emerald-200 dark:bg-emerald-500/10 dark:text-emerald-200 dark:ring-emerald-500/20">Active</span>
                    @endif
                </div>

                <div class="mt-6 grid gap-4 sm:grid-cols-2">
                    <article class="rounded-2xl border border-slate-200/80 bg-slate-50 p-4 dark:border-white/10 dark:bg-white/5">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400 dark:text-zinc-500">Cardholder</p>
                        <p class="mt-2 font-semibold text-slate-950 dark:text-white">{{ $card->name_on_card }}</p>
                    </article>
                    <article class="rounded-2xl border border-slate-200/80 bg-slate-50 p-4 dark:border-white/10 dark:bg-white/5">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400 dark:text-zinc-500">Card type</p>
                        <p class="mt-2 font-semibold text-slate-950 dark:text-white">{{ str_replace('_', ' ', $card->card_type) }}</p>
                    </article>
                    <article class="rounded-2xl border border-slate-200/80 bg-slate-50 p-4 dark:border-white/10 dark:bg-white/5">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400 dark:text-zinc-500">Customer ID</p>
                        <p class="mt-2 font-semibold text-slate-950 dark:text-white">{{ $card->customer_id ?: 'Not returned' }}</p>
                    </article>
                    <article class="rounded-2xl border border-slate-200/80 bg-slate-50 p-4 dark:border-white/10 dark:bg-white/5">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400 dark:text-zinc-500">Expiry / Sequence</p>
                        <p class="mt-2 font-semibold text-slate-950 dark:text-white">{{ $card->expiry_date ?: 'N/A' }} / {{ $card->card_sequence_number ?: 'N/A' }}</p>
                    </article>
                    <article class="rounded-2xl border border-slate-200/80 bg-slate-50 p-4 dark:border-white/10 dark:bg-white/5">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400 dark:text-zinc-500">Available balance</p>
                        <p class="mt-2 font-semibold text-slate-950 dark:text-white">{{ number_format((float) ($card->available_balance ?? 0), 2) }} {{ $card->currency ?: 'NGN' }}</p>
                    </article>
                    <article class="rounded-2xl border border-slate-200/80 bg-slate-50 p-4 dark:border-white/10 dark:bg-white/5">
                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400 dark:text-zinc-500">Ledger balance</p>
                        <p class="mt-2 font-semibold text-slate-950 dark:text-white">{{ number_format((float) ($card->ledger_balance ?? 0), 2) }} {{ $card->currency ?: 'NGN' }}</p>
                    </article>
                    @if ($card->account_id)
                        <article class="rounded-2xl border border-slate-200/80 bg-slate-50 p-4 dark:border-white/10 dark:bg-white/5 sm:col-span-2">
                            <p class="text-xs uppercase tracking-[0.2em] text-slate-400 dark:text-zinc-500">Linked debit account</p>
                            <p class="mt-2 font-semibold text-slate-950 dark:text-white">{{ $card->account_id }} · Type {{ $card->account_type ?: data_get($settings, 'account_type', '20') }}</p>
                        </article>
                    @endif
                </div>
            </div>

            <div class="panel-card rounded-2xl border border-slate-200 p-6 dark:border-white/10">
                <p class="text-xs font-semibold uppercase tracking-[0.22em] text-sky-600 dark:text-sky-400">Actions</p>
                <div class="mt-6 flex flex-col gap-3">
                    @if ($serviceEnabled && (int) $card->status !== 2)
                        @if ((int) $card->status === 0)
                            <a href="{{ route('admin.card.unblock', $card->reference) }}" class="inline-flex h-11 items-center justify-center rounded-full bg-emerald-600 px-5 text-sm font-semibold text-white transition hover:bg-emerald-700">Unblock card</a>
                        @else
                            <a href="{{ route('admin.card.block', $card->reference) }}" class="inline-flex h-11 items-center justify-center rounded-full bg-amber-500 px-5 text-sm font-semibold text-white transition hover:bg-amber-600">Block card</a>
                        @endif
                    @endif

                    <a href="{{ route('admin.card.terminate', $card->reference) }}" class="inline-flex h-11 items-center justify-center rounded-full border border-slate-200 bg-white px-5 text-sm font-semibold text-slate-700 transition hover:border-rose-200 hover:text-rose-700 dark:border-white/10 dark:bg-white/5 dark:text-zinc-200 dark:hover:border-rose-500/30 dark:hover:text-rose-300">Archive locally</a>
                </div>

                <div class="mt-6 rounded-2xl border border-dashed border-slate-300 px-4 py-4 text-sm text-slate-500 dark:border-white/10 dark:text-zinc-400">
                    Reference: <span class="font-medium text-slate-700 dark:text-zinc-300">{{ $card->reference }}</span><br>
                    Provider: <span class="font-medium text-slate-700 dark:text-zinc-300">{{ strtoupper($card->provider ?: 'INTERSWITCH') }}</span><br>
                    Synced: <span class="font-medium text-slate-700 dark:text-zinc-300">{{ optional($card->last_synced_at)->toDayDateTimeString() ?: 'Not yet' }}</span>
                </div>
            </div>
        </div>
    </div>
@endsection
