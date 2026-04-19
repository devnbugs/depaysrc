@extends($activeTemplate.'layouts.dashboard')

@section('content')
    <div class="min-h-screen space-y-6">
        <!-- Header -->
        <section class="rounded-[2rem] border border-slate-200 bg-gradient-to-br from-white to-slate-50 p-6 shadow-sm dark:border-white/10 dark:from-white/5 dark:to-white/3">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-start lg:justify-between">
                <div class="max-w-2xl">
                    <p class="text-xs font-semibold uppercase tracking-[0.26em] text-blue-600 dark:text-blue-400">✓ Review & Confirm</p>
                    <h2 class="mt-2 text-3xl font-semibold tracking-tight text-slate-950 dark:text-white">Transfer Preview</h2>
                    <p class="mt-3 text-sm leading-6 text-slate-600 dark:text-zinc-400">
                        Please review the details below before confirming this transfer.
                    </p>
                </div>
            </div>
        </section>

        <!-- Preview Details -->
        <div class="grid gap-6 lg:grid-cols-2">
            <!-- Transfer Details -->
            <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-white/5">
                <h3 class="mb-6 text-lg font-semibold text-slate-900 dark:text-white">Transfer Details</h3>
                
                @php
                    $resolved = session('dpay_transfer.resolved', []);
                @endphp

                <div class="space-y-4">
                    <!-- Recipient Name -->
                    <div class="rounded-lg bg-slate-50 p-4 dark:bg-zinc-950">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-600 dark:text-zinc-400">Recipient Name</p>
                        <p class="mt-2 text-lg font-semibold text-slate-900 dark:text-white">{{ $resolved['account_name'] ?? 'N/A' }}</p>
                    </div>

                    <!-- Recipient Contact -->
                    <div class="rounded-lg bg-slate-50 p-4 dark:bg-zinc-950">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-600 dark:text-zinc-400">Recipient Contact</p>
                        <p class="mt-2 text-sm font-medium text-slate-900 dark:text-white font-mono">{{ session('dpay_transfer.recipient') }}</p>
                    </div>

                    <!-- Bank -->
                    <div class="rounded-lg bg-slate-50 p-4 dark:bg-zinc-950">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-600 dark:text-zinc-400">Bank</p>
                        <p class="mt-2 text-sm font-medium text-slate-900 dark:text-white">{{ $resolved['bank_name'] ?? 'Internal Transfer' }}</p>
                    </div>
                </div>
            </section>

            <!-- Amount & Description -->
            <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-white/5">
                <h3 class="mb-6 text-lg font-semibold text-slate-900 dark:text-white">Amount & Description</h3>
                
                <div class="space-y-4">
                    <!-- Transfer Amount -->
                    <div class="rounded-lg border-2 border-blue-300 bg-blue-50 p-4 dark:border-blue-500/30 dark:bg-blue-500/10">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-blue-600 dark:text-blue-400">Transfer Amount</p>
                        <p class="mt-2 text-3xl font-bold text-blue-700 dark:text-blue-300">
                            {{ $general->cur_sym }}{{ showAmount(session('dpay_transfer.amount')) }}
                        </p>
                    </div>

                    <!-- Narration -->
                    <div class="rounded-lg bg-slate-50 p-4 dark:bg-zinc-950">
                        <p class="text-xs font-semibold uppercase tracking-[0.2em] text-slate-600 dark:text-zinc-400">Description</p>
                        <p class="mt-2 text-sm text-slate-900 dark:text-white">
                            {{ session('dpay_transfer.narration') ?: '(No description provided)' }}
                        </p>
                    </div>

                    @if(session('dpay_transfer.narration'))
                        <div class="rounded-lg border-l-4 border-sky-300 bg-sky-50 px-4 py-3 dark:border-sky-500/30 dark:bg-sky-500/10">
                            <p class="text-sm text-sky-700 dark:text-sky-300">
                                ℹ️ Your transfer includes a description for the recipient.
                            </p>
                        </div>
                    @endif
                </div>
            </section>
        </div>

        <!-- Action Buttons -->
        <section class="rounded-[2rem] border border-slate-200 bg-white p-6 shadow-sm dark:border-white/10 dark:bg-white/5">
            <form action="{{ route('user.dpay.confirm') }}" method="POST" id="confirm-form" data-confirm-form="1" data-confirm-title="Confirm Interbank Transfer" data-confirm-message="This action cannot be undone. Please confirm you want to proceed." data-confirm-accept-text="Yes, Confirm Transfer">
                @csrf

                <div class="flex flex-col gap-3 sm:flex-row">
                    <button type="submit" class="flex-1 rounded-xl bg-gradient-to-r from-emerald-600 to-emerald-700 px-6 py-3 text-center font-semibold text-white transition hover:from-emerald-700 hover:to-emerald-800 focus:outline-none focus:ring-2 focus:ring-emerald-500/20 dark:from-emerald-600 dark:to-emerald-700 dark:hover:from-emerald-700 dark:hover:to-emerald-800">
                        ✓ Confirm Transfer
                    </button>
                    <a href="{{ route('user.dpay.index') }}" class="flex-1 rounded-xl border-2 border-slate-300 bg-white px-6 py-3 text-center font-semibold text-slate-900 transition hover:border-slate-400 hover:bg-slate-50 focus:outline-none focus:ring-2 focus:ring-slate-500/20 dark:border-white/10 dark:bg-zinc-950 dark:text-white dark:hover:border-white/20 dark:hover:bg-zinc-900">
                        ← Go Back
                    </a>
                </div>
            </form>
        </section>
    </div>
@endsection
