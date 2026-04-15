@extends('admin.layouts.app')

@section('panel')
<div class="space-y-6">
    <!-- Transactions Table -->
    <div class="panel-card rounded-2xl border border-slate-200 dark:border-white/10 overflow-hidden">
        <div class="border-b border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 px-6 py-4">
            <h2 class="text-lg font-semibold text-slate-950 dark:text-white">@lang('Transaction Report')</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5">
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-zinc-400">@lang('User')</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-zinc-400">@lang('Trx')</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-zinc-400">@lang('Transacted')</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-zinc-400">@lang('Amount')</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-zinc-400">@lang('Post Balance')</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-zinc-400">@lang('Detail')</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                    @forelse($transactions as $trx)
                        <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-semibold text-slate-950 dark:text-white">{{ $trx->user->fullname }}</p>
                                    <a href="{{ route('admin.users.detail', $trx->user_id) }}" class="mt-1 text-sm text-sky-600 dark:text-sky-400 hover:text-sky-700">@{{ $trx->user->username }}</a>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-semibold text-slate-950 dark:text-white">{{ $trx->trx }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <p class="font-medium text-slate-950 dark:text-white">{{ showDateTime($trx->created_at) }}</p>
                                    <p class="mt-1 text-slate-500 dark:text-zinc-400">{{ diffForHumans($trx->created_at) }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-semibold @if($trx->trx_type == '+')text-emerald-600 dark:text-emerald-400 @else text-red-600 dark:text-red-400 @endif">
                                    {{ $trx->trx_type }} {{showAmount($trx->amount)}} {{ $general->cur_text }}
                                </p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="font-medium text-slate-950 dark:text-white">{{ showAmount($trx->post_balance) }} {{ __($general->cur_text) }}</p>
                            </td>
                            <td class="px-6 py-4">
                                <p class="text-sm text-slate-600 dark:text-zinc-400">{{ __($trx->details) }}</p>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-8 text-center text-slate-500 dark:text-zinc-400" colspan="6">{{ __($emptyMessage) }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 dark:border-white/10 px-6 py-4">
            {{ paginateLinks($transactions) }}
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
    @if(request()->routeIs('admin.users.transactions'))
        <form action="" method="GET" class="flex gap-2">
            <div class="flex-1">
                <input type="text" name="search" class="w-full rounded-full border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder-slate-500 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-zinc-400" placeholder="@lang('TRX / Username')" value="{{ $search ?? '' }}">
            </div>
            <button type="submit" class="inline-flex items-center rounded-full bg-sky-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-sky-700 dark:bg-sky-700 dark:hover:bg-sky-800">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.35-4.35"></path></svg>
            </button>
        </form>
    @else
        <form action="{{ route('admin.report.transaction.search') }}" method="GET" class="flex gap-2">
            <div class="flex-1">
                <input type="text" name="search" class="w-full rounded-full border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder-slate-500 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-zinc-400" placeholder="@lang('TRX / Username')" value="{{ $search ?? '' }}">
            </div>
            <button type="submit" class="inline-flex items-center rounded-full bg-sky-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-sky-700 dark:bg-sky-700 dark:hover:bg-sky-800">
                <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.35-4.35"></path></svg>
            </button>
        </form>
    @endif
@endpush