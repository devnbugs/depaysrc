@extends('admin.layouts.app')

@section('panel')
    <div class="container-fluid">
        @if(request()->routeIs('admin.withdraw.log') || request()->routeIs('admin.withdraw.method') || request()->routeIs('admin.users.withdrawals') || request()->routeIs('admin.users.withdrawals.method'))
            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-lg-4 col-md-6 col-12 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted small">@lang('Approved Withdrawals')</p>
                                    <h4 class="text-success mt-2">{{ __($general->cur_sym) }}{{ $withdrawals->where('status',1)->sum('amount') }}</h4>
                                </div>
                                <span class="badge bg-success">
                                    <i class="la la-check-circle la-2x"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-12 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted small">@lang('Pending Withdrawal')</p>
                                    <h4 class="text-warning mt-2">{{ __($general->cur_sym) }}{{ $withdrawals->where('status',2)->sum('amount') }}</h4>
                                </div>
                                <span class="badge bg-warning">
                                    <i class="la la-clock la-2x"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-lg-4 col-md-6 col-12 mb-3">
                    <div class="card">
                        <div class="card-body">
                            <div class="d-flex justify-content-between align-items-start">
                                <div>
                                    <p class="text-muted small">@lang('Rejected Withdrawals')</p>
                                    <h4 class="text-danger mt-2">{{ __($general->cur_sym) }}{{ $withdrawals->where('status',3)->sum('amount') }}</h4>
                                </div>
                                <span class="badge bg-danger">
                                    <i class="la la-times-circle la-2x"></i>
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        <!-- Withdrawals Table -->
        <div class="row mb-4">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">@lang('Withdrawals')</h5>
                    </div>
                    <div class="card-body p-0">
                        <div class="table-responsive">
                            <table class="table table-hover mb-0">
                                <thead class="table-light">
                                    <tr>
                                        <th>@lang('Gateway | Trx')</th>
                                        <th class="d-none d-md-table-cell">@lang('Initiated')</th>
                                        <th>@lang('User')</th>
                                        <th class="d-none d-lg-table-cell">@lang('Amount')</th>
                                        <th class="d-none d-xl-table-cell">@lang('Conversion')</th>
                                        <th class="text-center">@lang('Status')</th>
                                        <th class="text-center">@lang('Action')</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @forelse($withdrawals as $withdraw)
                                        @php
                                            $details = ($withdraw->withdraw_information != null) ? json_encode($withdraw->withdraw_information) : null;
                                        @endphp
                                        <tr>
                                            <td>
                                                <div>
                                                    <a href="{{ route('admin.withdraw.method',[$withdraw->method->id,'all']) }}" class="text-primary fw-bold">{{ __(@$withdraw->method->name) }}</a>
                                                    <p class="mt-1 mb-0 small text-muted">{{ $withdraw->trx }}</p>
                                                </div>
                                            </td>
                                            <td class="d-none d-md-table-cell">
                                                <small>{{ showDateTime($withdraw->created_at) }}</small>
                                                <br>
                                                <small class="text-muted">{{ diffForHumans($withdraw->created_at) }}</small>
                                            </td>
                                            <td>
                                                <div>
                                                    <strong>{{ $withdraw->user?->fullname ?? 'N/A' }}</strong>
                                                    @if($withdraw->user)
                                                        <br>
                                                        <a href="{{ route('admin.users.detail', $withdraw->user_id) }}" class="small text-primary">@{{ $withdraw->user->username }}</a>
                                                    @else
                                                        <br>
                                                        <small class="text-muted">User deleted</small>
                                                    @endif
                                                </div>
                                            </td>
                                            <td class="d-none d-lg-table-cell">
                                                <div>
                                                    <strong class="text-success">{{ __($general->cur_sym) }}{{ showAmount($withdraw->amount) }}</strong>
                                                    <br>
                                                    <small class="text-danger">- {{ showAmount($withdraw->charge) }} charge</small>
                                                    <br>
                                                    <small><strong>{{ showAmount($withdraw->amount-$withdraw->charge) }} {{ __($general->cur_text) }}</strong></small>
                                                </div>
                                            </td>
                                            <td class="d-none d-xl-table-cell">
                                                <small>1 {{ __($general->cur_text) }} = {{ showAmount($withdraw->rate) }} {{ __($withdraw->currency) }}</small>
                                                <br>
                                                <strong>{{ showAmount($withdraw->final_amount) }} {{ __($withdraw->currency) }}</strong>
                                            </td>
                                            <td class="text-center">
                                                @if($withdraw->status == 2)
                                                    <span class="badge bg-warning">@lang('Pending')</span>
                                                @elseif($withdraw->status == 1)
                                                    <span class="badge bg-success">@lang('Approved')</span>
                                                    <br>
                                                    <small class="text-muted">{{ diffForHumans($withdraw->updated_at) }}</small>
                                                @elseif($withdraw->status == 3)
                                                    <span class="badge bg-danger">@lang('Rejected')</span>
                                                    <br>
                                                    <small class="text-muted">{{ diffForHumans($withdraw->updated_at) }}</small>
                                                @endif
                                            </td>
                                            <td class="text-center">
                                                <a href="{{ route('admin.withdraw.details', $withdraw->id) }}" class="btn btn-sm btn-info">@lang('View')</a>
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td class="text-center text-muted py-4" colspan="7">{{ __($emptyMessage) }}</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="card-footer">
                        {{ paginateLinks($withdrawals) }}
                    </div>
                </div>
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
