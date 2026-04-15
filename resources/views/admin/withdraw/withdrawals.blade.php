@extends('admin.layouts.app')

@section('panel')
<div class="space-y-6">
    @if(request()->routeIs('admin.withdraw.log') || request()->routeIs('admin.withdraw.method') || request()->routeIs('admin.users.withdrawals') || request()->routeIs('admin.users.withdrawals.method'))
        <!-- Stats Cards -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="panel-card p-6 rounded-2xl border border-slate-200 dark:border-white/10">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600 dark:text-zinc-400">@lang('Approved Withdrawals')</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-950 dark:text-white">{{ __($general->cur_sym) }}{{ $withdrawals->where('status',1)->sum('amount') }}</p>
                    </div>
                    <div class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600 dark:bg-emerald-900/30 dark:text-emerald-400">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 1v22"></path><path d="M17 5H9.5a3.5 3.5 0 0 0 0 7H14a3 3 0 0 1 0 6H6"></path></svg>
                    </div>
                </div>
            </div>
            <div class="panel-card p-6 rounded-2xl border border-slate-200 dark:border-white/10">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600 dark:text-zinc-400">@lang('Pending Withdrawal')</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-950 dark:text-white">{{ __($general->cur_sym) }}{{ $withdrawals->where('status',2)->sum('amount') }}</p>
                    </div>
                    <div class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 text-amber-600 dark:bg-amber-900/30 dark:text-amber-400">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><polyline points="12 6 12 12 16 14"></polyline></svg>
                    </div>
                </div>
            </div>
            <div class="panel-card p-6 rounded-2xl border border-slate-200 dark:border-white/10">
                <div class="flex items-start justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-600 dark:text-zinc-400">@lang('Rejected Withdrawals')</p>
                        <p class="mt-2 text-2xl font-semibold text-slate-950 dark:text-white">{{ __($general->cur_sym) }}{{ $withdrawals->where('status',3)->sum('amount') }}</p>
                    </div>
                    <div class="inline-flex h-12 w-12 items-center justify-center rounded-xl bg-red-100 text-red-600 dark:bg-red-900/30 dark:text-red-400">
                        <svg class="h-6 w-6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"></circle><line x1="15" y1="9" x2="9" y2="15"></line><line x1="9" y1="9" x2="15" y2="15"></line></svg>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <!-- Withdrawals Table -->
    <div class="panel-card rounded-2xl border border-slate-200 dark:border-white/10 overflow-hidden">
        <div class="border-b border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5 px-6 py-4">
            <h2 class="text-lg font-semibold text-slate-950 dark:text-white">@lang('Withdrawals')</h2>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full">
                <thead>
                    <tr class="border-b border-slate-200 dark:border-white/10 bg-slate-50 dark:bg-white/5">
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-zinc-400">@lang('Gateway | Trx')</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-zinc-400">@lang('Initiated')</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-zinc-400">@lang('User')</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-zinc-400">@lang('Amount')</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-zinc-400">@lang('Conversion')</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-zinc-400">@lang('Status')</th>
                        <th class="px-6 py-4 text-left text-xs font-semibold uppercase tracking-wider text-slate-600 dark:text-zinc-400">@lang('Action')</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200 dark:divide-white/10">
                    @forelse($withdrawals as $withdraw)
                        @php
                            $details = ($withdraw->withdraw_information != null) ? json_encode($withdraw->withdraw_information) : null;
                        @endphp
                        <tr class="hover:bg-slate-50 dark:hover:bg-white/5 transition-colors">
                            <td class="px-6 py-4">
                                <div>
                                    <a href="{{ route('admin.withdraw.method',[$withdraw->method->id,'all']) }}" class="font-semibold text-sky-600 dark:text-sky-400 hover:text-sky-700 dark:hover:text-sky-300">{{ __(@$withdraw->method->name) }}</a>
                                    <p class="mt-1 text-sm text-slate-500 dark:text-zinc-400">{{ $withdraw->trx }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <p class="font-medium text-slate-950 dark:text-white">{{ showDateTime($withdraw->created_at) }}</p>
                                    <p class="mt-1 text-slate-500 dark:text-zinc-400">{{ diffForHumans($withdraw->created_at) }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div>
                                    <p class="font-semibold text-slate-950 dark:text-white">{{ $withdraw->user?->fullname ?? 'N/A' }}</p>
                                    @if($withdraw->user)
                                        <a href="{{ route('admin.users.detail', $withdraw->user_id) }}" class="mt-1 text-sm text-sky-600 dark:text-sky-400 hover:text-sky-700">@{{ $withdraw->user->username }}</a>
                                    @else
                                        <p class="mt-1 text-sm text-slate-500 dark:text-zinc-400">User deleted</p>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm">
                                    <p class="font-medium text-slate-950 dark:text-white">{{ __($general->cur_sym) }}{{ showAmount($withdraw->amount) }}</p>
                                    <p class="mt-1 text-slate-500 dark:text-zinc-400">- <span class="text-red-600 dark:text-red-400">{{ showAmount($withdraw->charge) }}</span> charge</p>
                                    <p class="mt-1 font-semibold text-slate-950 dark:text-white">{{ showAmount($withdraw->amount-$withdraw->charge) }} {{ __($general->cur_text) }}</p>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm text-slate-600 dark:text-zinc-400">
                                <p>1 {{ __($general->cur_text) }} = {{ showAmount($withdraw->rate) }} {{ __($withdraw->currency) }}</p>
                                <p class="mt-1 font-medium text-slate-950 dark:text-white">{{ showAmount($withdraw->final_amount) }} {{ __($withdraw->currency) }}</p>
                            </td>
                            <td class="px-6 py-4">
                                @if($withdraw->status == 2)
                                    <span class="inline-flex items-center rounded-full bg-amber-100 px-2.5 py-1 text-xs font-semibold text-amber-700 dark:bg-amber-900/30 dark:text-amber-400">@lang('Pending')</span>
                                @elseif($withdraw->status == 1)
                                    <span class="inline-flex items-center rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-semibold text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-400">@lang('Approved')</span>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-zinc-400">{{ diffForHumans($withdraw->updated_at) }}</p>
                                @elseif($withdraw->status == 3)
                                    <span class="inline-flex items-center rounded-full bg-red-100 px-2.5 py-1 text-xs font-semibold text-red-700 dark:bg-red-900/30 dark:text-red-400">@lang('Rejected')</span>
                                    <p class="mt-1 text-xs text-slate-500 dark:text-zinc-400">{{ diffForHumans($withdraw->updated_at) }}</p>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.withdraw.details', $withdraw->id) }}" class="inline-flex items-center gap-2 rounded-full bg-sky-100 px-3 py-1.5 text-xs font-semibold text-sky-700 transition hover:bg-sky-200 dark:bg-sky-900/30 dark:text-sky-400 dark:hover:bg-sky-900/50">
                                    View
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td class="px-6 py-8 text-center text-slate-500 dark:text-zinc-400" colspan="7">{{ __($emptyMessage) }}</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="border-t border-slate-200 dark:border-white/10 px-6 py-4">
            {{ paginateLinks($withdrawals) }}
        </div>
    </div>
</div>
@endsection

@push('breadcrumb-plugins')
    @if(!request()->routeIs('admin.users.withdrawals') && !request()->routeIs('admin.users.withdrawals.method'))
        <div class="flex flex-col gap-3 sm:flex-row">
            <form action="{{ route('admin.withdraw.search', $scope ?? str_replace('admin.withdraw.', '', request()->route()->getName())) }}" method="GET" class="flex gap-2">
                <div class="flex-1">
                    <input type="text" name="search" class="w-full rounded-full border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder-slate-500 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-zinc-400" placeholder="@lang('Trx number/Username')" value="{{ $search ?? '' }}">
                </div>
                <button type="submit" class="inline-flex items-center rounded-full bg-sky-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-sky-700 dark:bg-sky-700 dark:hover:bg-sky-800">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.35-4.35"></path></svg>
                </button>
            </form>

            <form action="{{route('admin.withdraw.dateSearch',$scope ?? str_replace('admin.withdraw.', '', request()->route()->getName()))}}" method="GET" class="flex gap-2">
                <div class="flex-1">
                    <input name="date" type="text" data-range="true" data-multiple-dates-separator=" - " data-language="en" class="datepicker-here w-full rounded-full border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 placeholder-slate-500 transition focus:border-sky-500 focus:outline-none dark:border-white/10 dark:bg-white/5 dark:text-white dark:placeholder-zinc-400" data-position='bottom right' placeholder="@lang('Min Date - Max date')" autocomplete="off" value="{{ @$dateSearch }}">
                    <input type="hidden" name="method" value="{{ @$method->id }}">
                </div>
                <button type="submit" class="inline-flex items-center rounded-full bg-sky-600 px-4 py-2.5 text-sm font-medium text-white transition hover:bg-sky-700 dark:bg-sky-700 dark:hover:bg-sky-800">
                    <svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"></circle><path d="m21 21-4.35-4.35"></path></svg>
                </button>
            </form>
        </div>
    @endif
@endpush

@push('script-lib')
    <script src="{{ asset('assets/admin/js/vendor/datepicker.min.js') }}"></script>
    <script src="{{ asset('assets/admin/js/vendor/datepicker.en.js') }}"></script>
@endpush

@push('script')
    <script>
        (function($){
            'use strict';
            if(!$('.datepicker-here').val()){
                $('.datepicker-here').datepicker();
            }
        })(jQuery)
    </script>
@endpush
