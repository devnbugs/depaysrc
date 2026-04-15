@extends('admin.layouts.app')

@section('panel')
<div class="space-y-6">
    <!-- Support Tickets Table -->
    <div class="panel-card rounded-2xl border border-slate-200 dark:border-white/10 overflow-hidden">
        <div class="border-b border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 px-6 py-4">
            <h2 class="text-lg font-semibold text-slate-950 dark:text-white">{{ __($pageTitle) }}</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5">
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-zinc-400">@lang('Subject')</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-zinc-400">@lang('Status')</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-zinc-400">@lang('Priority')</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-zinc-400">@lang('Last Reply')</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-zinc-400">@lang('Action')</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                    @foreach($supports as $key => $support)
                        <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.user.ticket.view', $support->ticket) }}" class="font-semibold text-sky-600 dark:text-sky-400 hover:text-sky-700">
                                    [#{{ $support->ticket }}] {{ __($support->subject) }}
                                </a>
                            </td>
                            <td class="px-6 py-4">
                                @if($support->status == 0)
                                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">@lang('Open')</span>
                                @elseif($support->status == 1)
                                    <span class="inline-flex items-center rounded-full bg-sky-100 px-2.5 py-1 text-xs font-semibold text-sky-700 dark:bg-sky-900/30 dark:text-sky-400">@lang('Answered')</span>
                                @elseif($support->status == 2)
                                    <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">@lang('Customer Reply')</span>
                                @elseif($support->status == 3)
                                    <span class="inline-flex items-center rounded-full bg-slate-200 px-2.5 py-1 text-xs font-semibold text-slate-700 dark:bg-slate-700 dark:text-slate-300">@lang('Closed')</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($support->priority == 'Low')
                                    <span class="inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-semibold text-slate-700 dark:bg-slate-900/30 dark:text-slate-400">@lang('Low')</span>
                                @elseif($support->priority == 'Medium')
                                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">@lang('Medium')</span>
                                @elseif($support->priority == 'High')
                                    <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">@lang('High')</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-slate-600 dark:text-zinc-400">{{ \Carbon\Carbon::parse($support->last_reply)->diffForHumans() }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.user.ticket.view', $support->ticket) }}" class="inline-flex items-center gap-2 rounded-full bg-sky-100 px-3 py-1.5 text-xs font-semibold text-sky-700 transition hover:bg-sky-200 dark:bg-sky-900/30 dark:text-sky-400 dark:hover:bg-sky-900/50">
                                    View
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 dark:border-white/10 px-6 py-4">
            {{ $supports->links() }}
        </div>
    </div>
</div>

@endsection
