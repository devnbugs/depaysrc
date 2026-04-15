@extends($activeTemplate.'layouts.dashboard')

@section('content')
<section class="space-y-6">
    <div class="hero-surface p-6 sm:p-8">
        <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
            <div class="space-y-2">
                <p class="section-kicker">Support Desk</p>
                <h1 class="text-3xl font-semibold tracking-tight text-slate-950 dark:text-white sm:text-4xl">
                    Track your support tickets
                </h1>
                <p class="max-w-2xl text-sm leading-6 text-slate-600 dark:text-zinc-300">
                    Review open issues, check the latest replies, and open a new request whenever you need help with funding, payments, or account activity.
                </p>
            </div>

            <a href="{{ route('user.ticket.open') }}" class="inline-flex items-center justify-center rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                New ticket
            </a>
        </div>
    </div>

    <div class="grid gap-6 xl:grid-cols-[1.25fr_0.75fr]">
        <div class="panel-card overflow-hidden">
            <div class="border-b border-slate-200/80 px-6 py-5 dark:border-white/10">
                <div class="flex flex-wrap items-center justify-between gap-3">
                    <div>
                        <p class="section-kicker">Inbox</p>
                        <h2 class="mt-2 section-title text-xl">Your tickets</h2>
                    </div>
                    <span class="inline-flex items-center rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-xs font-semibold uppercase tracking-[0.22em] text-slate-600 dark:border-white/10 dark:bg-white/5 dark:text-zinc-300">
                        {{ $supports->total() }} total
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200 dark:divide-white/10">
                    <thead class="bg-slate-50/80 dark:bg-white/5">
                        <tr>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500 dark:text-zinc-400">Subject</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500 dark:text-zinc-400">Status</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500 dark:text-zinc-400">Priority</th>
                            <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-[0.18em] text-slate-500 dark:text-zinc-400">Last reply</th>
                            <th class="px-6 py-4 text-right text-xs font-semibold uppercase tracking-[0.18em] text-slate-500 dark:text-zinc-400">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                        @forelse($supports as $support)
                            @php
                                $statusLabel = 'Closed';
                                $statusClass = 'border-slate-200 bg-slate-100 text-slate-700 dark:border-white/10 dark:bg-white/5 dark:text-zinc-300';

                                if ($support->status == 0) {
                                    $statusLabel = 'Open';
                                    $statusClass = 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-300';
                                } elseif ($support->status == 1) {
                                    $statusLabel = 'Answered';
                                    $statusClass = 'border-sky-200 bg-sky-50 text-sky-700 dark:border-sky-500/20 dark:bg-sky-500/10 dark:text-sky-300';
                                } elseif ($support->status == 2) {
                                    $statusLabel = 'Customer Reply';
                                    $statusClass = 'border-amber-200 bg-amber-50 text-amber-700 dark:border-amber-500/20 dark:bg-amber-500/10 dark:text-amber-300';
                                }

                                $priorityClass = match ($support->priority) {
                                    'High' => 'border-rose-200 bg-rose-50 text-rose-700 dark:border-rose-500/20 dark:bg-rose-500/10 dark:text-rose-300',
                                    'Medium' => 'border-emerald-200 bg-emerald-50 text-emerald-700 dark:border-emerald-500/20 dark:bg-emerald-500/10 dark:text-emerald-300',
                                    default => 'border-slate-200 bg-slate-100 text-slate-700 dark:border-white/10 dark:bg-white/5 dark:text-zinc-300',
                                };
                            @endphp

                            <tr class="bg-white/60 transition hover:bg-slate-50/90 dark:bg-transparent dark:hover:bg-white/5">
                                <td class="px-6 py-4">
                                    <a href="{{ route('user.ticket.view', $support->ticket) }}" class="block space-y-1">
                                        <p class="font-semibold text-slate-900 dark:text-white">[Ticket #{{ $support->ticket }}] {{ __($support->subject) }}</p>
                                        <p class="text-xs uppercase tracking-[0.2em] text-slate-400 dark:text-zinc-500">Support thread</p>
                                    </a>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $statusClass }}">
                                        {{ $statusLabel }}
                                    </span>
                                </td>
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center rounded-full border px-3 py-1 text-xs font-semibold {{ $priorityClass }}">
                                        {{ $support->priority }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-sm text-slate-600 dark:text-zinc-300">
                                    {{ \Carbon\Carbon::parse($support->last_reply)->diffForHumans() }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <a href="{{ route('user.ticket.view', $support->ticket) }}" class="inline-flex items-center justify-center rounded-full border border-slate-200 bg-white px-4 py-2 text-sm font-semibold text-slate-700 transition hover:border-sky-200 hover:text-sky-700 dark:border-white/10 dark:bg-zinc-900 dark:text-zinc-200 dark:hover:border-sky-500/30 dark:hover:text-sky-300">
                                        View
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="mx-auto max-w-md space-y-3">
                                        <p class="text-base font-semibold text-slate-900 dark:text-white">No support tickets yet</p>
                                        <p class="text-sm text-slate-500 dark:text-zinc-400">
                                            When you open a ticket, the status and latest reply will show up here.
                                        </p>
                                        <a href="{{ route('user.ticket.open') }}" class="inline-flex items-center justify-center rounded-full bg-slate-950 px-5 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 dark:bg-white dark:text-slate-950 dark:hover:bg-slate-200">
                                            Open your first ticket
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="border-t border-slate-200/80 px-6 py-4 dark:border-white/10">
                {{ $supports->links() }}
            </div>
        </div>

        <div class="space-y-4">
            <div class="panel-card p-6">
                <p class="section-kicker">Help flow</p>
                <div class="mt-4 space-y-3 text-sm leading-6 text-slate-600 dark:text-zinc-300">
                    <p>Open a ticket with the clearest subject possible so the issue can be routed faster.</p>
                    <p>Use the priority thoughtfully. High priority should be reserved for urgent wallet or payment issues.</p>
                    <p>Replies stay in the same thread, so you can track the full conversation in one place.</p>
                </div>
            </div>

            <div class="panel-card p-6">
                <p class="section-kicker">Quick action</p>
                <a href="{{ route('user.ticket.open') }}" class="mt-4 inline-flex w-full items-center justify-center rounded-full border border-slate-200 bg-slate-50 px-5 py-3 text-sm font-semibold text-slate-700 transition hover:bg-slate-100 dark:border-white/10 dark:bg-white/5 dark:text-zinc-200 dark:hover:bg-white/10">
                    Start a new support request
                </a>
            </div>
        </div>
    </div>
</section>
@endsection
