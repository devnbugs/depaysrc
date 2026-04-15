@extends($activeTemplate.'layouts.receipt')

@section('content')
    @php
        $typeLabel = match ((int) $bill->type) {
            1 => 'Airtime Purchase Receipt',
            2 => 'Data Purchase Receipt',
            3 => 'Cable TV Receipt',
            4 => 'Utility Bill Receipt',
            5 => 'WAEC Registration Receipt',
            6 => 'WAEC Result Checker Receipt',
            default => 'Transaction Receipt',
        };
    @endphp

    <div class="space-y-6">
        <div class="no-print flex items-center justify-between gap-3">
            <a href="javascript:history.back()" class="inline-flex items-center justify-center rounded-full bg-sky-600 px-4 py-2 text-sm font-semibold text-white transition hover:bg-sky-700">
                Back
            </a>
            <div class="flex items-center gap-2">
                <a href="{{ route('user.beta.receipt.print', ['billId' => $bill->id]) }}" target="_blank" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-sky-200 hover:text-sky-700 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-sky-500/30 dark:hover:text-sky-300">
                    Print
                </a>
                <a href="{{ route('user.beta.receipt.download', ['billId' => $bill->id]) }}" class="inline-flex items-center justify-center rounded-full bg-slate-950 px-4 py-2 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                    Download
                </a>
            </div>
        </div>

        <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-[0_24px_80px_rgba(15,23,42,0.08)] dark:border-white/10 dark:bg-zinc-900/80 sm:p-8">
            <div class="flex flex-col gap-6 md:flex-row md:items-start md:justify-between">
                <div class="space-y-3">
                    <div class="inline-flex items-center gap-2 rounded-full border border-sky-200 bg-sky-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.25em] text-sky-700 dark:border-sky-500/30 dark:bg-sky-500/10 dark:text-sky-300">
                        Receipt
                    </div>
                    <h1 class="text-3xl font-semibold tracking-tight text-slate-950 dark:text-white">{{ $general->sitename }}</h1>
                    <p class="text-sm text-slate-500 dark:text-zinc-400">{{ $typeLabel }}</p>
                </div>

                <div class="rounded-3xl border border-slate-200 p-4 text-sm text-slate-600 dark:border-white/10 dark:text-zinc-300">
                    <p class="font-semibold text-slate-900 dark:text-white">{{ $user->firstname }} {{ $user->lastname }}</p>
                    <p class="mt-1">{{ $user->email }}</p>
                    <p>{{ $user->mobile }}</p>
                </div>
            </div>

            <div class="mt-8 grid gap-6 lg:grid-cols-[1.1fr_0.9fr]">
                <div class="space-y-4">
                    <div class="rounded-3xl border border-slate-200 p-5 dark:border-white/10">
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-zinc-400">Product</p>
                                <p class="mt-2 text-sm font-medium text-slate-950 dark:text-white">
                                    @if($bill->type == 1)
                                        VTU Topup
                                    @elseif($bill->type == 2)
                                        {{ $bill->bundle }} Data
                                    @elseif($bill->type == 3)
                                        {{ $bill->bundle }}
                                    @elseif($bill->type == 4)
                                        {{ $bill->network }}
                                    @else
                                        {{ $bill->bundle ?? $bill->accountname ?? 'Receipt' }}
                                    @endif
                                </p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-zinc-400">Transaction ID</p>
                                <p class="mt-2 text-sm font-medium text-slate-950 dark:text-white">{{ $bill->trx }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-zinc-400">Beneficiary</p>
                                <p class="mt-2 text-sm font-medium text-slate-950 dark:text-white">{{ $bill->phone ?? $bill->accountnumber ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-zinc-400">Status</p>
                                <p class="mt-2 inline-flex rounded-full px-3 py-1 text-xs font-semibold {{ (int) $bill->status === 1 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-500/10 dark:text-emerald-300' : ((int) $bill->status === 0 ? 'bg-amber-100 text-amber-700 dark:bg-amber-500/10 dark:text-amber-300' : 'bg-rose-100 text-rose-700 dark:bg-rose-500/10 dark:text-rose-300') }}">
                                    {{ (int) $bill->status === 1 ? 'Success' : ((int) $bill->status === 0 ? 'Pending' : 'Declined') }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-slate-200 p-5 dark:border-white/10">
                        <dl class="space-y-4">
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-sm text-slate-500 dark:text-zinc-400">Purchase date</dt>
                                <dd class="text-sm font-medium text-slate-950 dark:text-white">{{ $bill->created_at }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-sm text-slate-500 dark:text-zinc-400">Amount</dt>
                                <dd class="text-sm font-medium text-slate-950 dark:text-white">{{ $general->cur_sym }}{{ number_format((float) $bill->amount, 2) }}</dd>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <dt class="text-sm text-slate-500 dark:text-zinc-400">Reference</dt>
                                <dd class="text-sm font-medium text-slate-950 dark:text-white">{{ $bill->token ?? $bill->trx }}</dd>
                            </div>
                        </dl>
                    </div>
                </div>

                <aside class="space-y-4">
                    <div class="rounded-3xl border border-slate-200 p-5 dark:border-white/10">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-500 dark:text-zinc-400">Details</p>
                        <div class="mt-4 space-y-3 text-sm">
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-slate-500 dark:text-zinc-400">Network</span>
                                <span class="font-medium text-slate-950 dark:text-white">{{ $bill->network ?? 'N/A' }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-slate-500 dark:text-zinc-400">Bundle</span>
                                <span class="font-medium text-slate-950 dark:text-white">{{ $bill->bundle ?? $name ?? 'N/A' }}</span>
                            </div>
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-slate-500 dark:text-zinc-400">New balance</span>
                                <span class="font-medium text-slate-950 dark:text-white">{{ $general->cur_sym }}{{ number_format((float) $bill->newbalance, 2) }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-3xl border border-slate-200 bg-slate-50 p-5 text-sm text-slate-600 dark:border-white/10 dark:bg-white/5 dark:text-zinc-300">
                        <p class="font-semibold text-slate-900 dark:text-white">Receipt note</p>
                        <p class="mt-2 leading-6">This is a system-generated receipt. Keep it for your records if you need support or reconciliation later.</p>
                    </div>
                </aside>
            </div>
        </section>
    </div>
@endsection
